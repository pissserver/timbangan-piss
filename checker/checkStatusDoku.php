<?php
    include "../connection.php";

    header('Content-Type: application/json');

    /* =========================
    CONFIG DOKU
    Harus sama dengan configDoku.php
    ========================= */

    $doku_client_id     = 'BRN-0260-1787041432710';
    $doku_secret_key    = 'SK-h2tLzpQD6qB9Ie3cnitg';
    $doku_is_production = false;

    $doku_base_url = $doku_is_production
        ? 'https://api.doku.com'
        : 'https://api-sandbox.doku.com';

    $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : (isset($_POST['order_id']) ? $_POST['order_id'] : '');

    if ($order_id === '') {
        echo json_encode(array('status' => 'invalid'));
        exit;
    }

    function doku_method_label($channel_id) {
        $map = array(
            'VIRTUAL_ACCOUNT_BCA'                   => 'Virtual Account BCA',
            'VIRTUAL_ACCOUNT_BANK_MANDIRI'          => 'Virtual Account Mandiri',
            'VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI'  => 'Virtual Account BSI',
            'VIRTUAL_ACCOUNT_DOKU'                  => 'Virtual Account DOKU',
            'VIRTUAL_ACCOUNT_BRI'                   => 'Virtual Account BRI',
            'VIRTUAL_ACCOUNT_BNI'                   => 'Virtual Account BNI',
            'VIRTUAL_ACCOUNT_BANK_PERMATA'          => 'Virtual Account Permata',
            'VIRTUAL_ACCOUNT_BANK_CIMB'             => 'Virtual Account CIMB',
            'VIRTUAL_ACCOUNT_BANK_DANAMON'          => 'Virtual Account Danamon',
            'VIRTUAL_ACCOUNT_BNC'                   => 'Virtual Account BNC',
            'VIRTUAL_ACCOUNT_BTN'                   => 'Virtual Account BTN',
            'QRIS'                                  => 'QRIS'
        );
        if (isset($map[$channel_id])) {
            return $map[$channel_id];
        }
        return $channel_id !== '' ? $channel_id : 'Virtual Account / QRIS';
    }

    /* =========================
    1. CEK DB DULU (kalau callback sudah update)
    ========================= */

    $order_id_esc = mysql_real_escape_string($order_id);

    $rs = mysql_query("
        SELECT status, total, payment_type
        FROM payment_log
        WHERE order_id = '$order_id_esc'
        ORDER BY created_at DESC
        LIMIT 1
    ");

    if ($rs && mysql_num_rows($rs) > 0) {
        $row = mysql_fetch_assoc($rs);
        echo json_encode(array(
            'status'  => 'paid',
            'source'  => 'db',
            'amount'  => $row['total'],
            'method'  => doku_method_label($row['payment_type'])
        ));
        exit;
    }

    /* =========================
    2. CEK LANGSUNG KE DOKU (Check Status API)
    ========================= */

    $request_target = '/orders/v1/status/'.$order_id;

    $request_id        = uniqid('doku-status-', true);
    $request_timestamp = gmdate('Y-m-d\TH:i:s\Z');

    $component_signature =
        'Client-Id:'.$doku_client_id."\n".
        'Request-Id:'.$request_id."\n".
        'Request-Timestamp:'.$request_timestamp."\n".
        'Request-Target:'.$request_target;

    $signature = 'HMACSHA256='.base64_encode(hash_hmac('sha256', $component_signature, $doku_secret_key, true));

    $ch = curl_init($doku_base_url.$request_target);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Client-Id: '.$doku_client_id,
        'Request-Id: '.$request_id,
        'Request-Timestamp: '.$request_timestamp,
        'Signature: '.$signature
    ));

    $response_body = curl_exec($ch);
    $http_code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents(
        'doku_status_log.txt',
        date('Y-m-d H:i:s')."\n".
        'ORDER: '.$order_id."\n".
        'HTTP: '.$http_code."\n".
        'RESPONSE: '.$response_body."\n\n",
        FILE_APPEND
    );

    $result = json_decode($response_body, true);

    $transaction_status = isset($result['transaction']['status']) ? strtoupper($result['transaction']['status']) : '';

    if ($transaction_status !== 'SUCCESS') {
        echo json_encode(array('status' => 'pending', 'doku_status' => $transaction_status));
        exit;
    }

    /* =========================
    3. SUCCESS - UPDATE DATABASE
    ========================= */

    $payment_type = isset($result['channel']['id']) ? $result['channel']['id'] : '';
    $gross_amount = isset($result['order']['amount']) ? $result['order']['amount'] : 0;

    $explode     = explode('-', $order_id);
    $weighing_id = isset($explode[1]) ? $explode[1] : '';
    $payment     = strtolower($explode[0]);

    $weighing_id  = mysql_real_escape_string($weighing_id);
    $payment_type = mysql_real_escape_string($payment_type);
    $gross_amount = mysql_real_escape_string($gross_amount);

    if ($payment == 'tonase') {
        mysql_query("
            UPDATE weighing
            SET weighing_sts3 = 'Pending'
            WHERE weighing_id = '$weighing_id'
        ");
    } else if ($payment == 'mitra') {
        mysql_query("
            UPDATE weighing_mitra
            SET weighing_sts3 = 'Pending'
            WHERE weighing_id = '$weighing_id'
        ");
    }

    mysql_query("
    INSERT INTO payment_log(
        order_id,
        weighing_id,
        status,
        payment_type,
        total,
        created_at
    ) VALUES(
        '$order_id_esc',
        '$weighing_id',
        'paid',
        '$payment_type',
        '$gross_amount',
        NOW()
    )");

    echo json_encode(array(
        'status'  => 'paid',
        'source'  => 'doku',
        'amount'  => $gross_amount,
        'method'  => doku_method_label($payment_type)
    ));
