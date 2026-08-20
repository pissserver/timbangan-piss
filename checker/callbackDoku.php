<?php
    include "../connection.php";

    /* =========================
    CONFIG DOKU
    Masukkan Client ID & Secret Key dari DOKU Dashboard > Integrations > API Keys
    Harus sama dengan configDoku.php
    ========================= */

    $doku_client_id  = 'BRN-0260-1787041432710';
    $doku_secret_key = 'SK-h2tLzpQD6qB9Ie3cnitg';

    /* =========================
    AMBIL JSON CALLBACK
    ========================= */

    $json_result = file_get_contents('php://input');
    $result = json_decode($json_result, true);

    /* =========================
    LOG CALLBACK (DEBUG LENGKAP)
    ========================= */

    $all_headers = array();
    if (function_exists('getallheaders')) {
        $all_headers = getallheaders();
    }

    file_put_contents(
        'doku_callback_log.txt',
        date('Y-m-d H:i:s')."\n".
        'METHOD: '.$_SERVER['REQUEST_METHOD']."\n".
        'URI: '.$_SERVER['REQUEST_URI']."\n".
        'HEADERS: '.json_encode($all_headers)."\n".
        'BODY: '.$json_result."\n\n",
        FILE_APPEND
    );

    file_put_contents(
        'callback_log.txt',
        date('Y-m-d H:i:s')."\n".
        $json_result."\n\n",
        FILE_APPEND
    );

    /* =========================
    VALIDASI JSON
    ========================= */

    if (!$result) {
        http_response_code(400);
        exit('Invalid JSON');
    }

    /* =========================
    AMBIL HEADER
    ========================= */

    function doku_get_header($name) {
        $headers = array();

        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        }

        if (!$headers) {
            foreach ($_SERVER as $key => $value) {
                if (strpos($key, 'HTTP_') === 0) {
                    $header_key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                    $headers[$header_key] = $value;
                }
            }
        }

        foreach ($headers as $key => $value) {
            if (strtolower($key) === strtolower($name)) {
                return $value;
            }
        }

        return '';
    }

    $header_client_id  = doku_get_header('Client-Id');
    $header_request_id = doku_get_header('Request-Id');
    $header_timestamp  = doku_get_header('Request-Timestamp');
    $header_signature  = doku_get_header('Signature');

    $request_target = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $digest         = base64_encode(hash('sha256', $json_result, true));

    $component_signature =
        'Client-Id:'.$header_client_id."\n".
        'Request-Id:'.$header_request_id."\n".
        'Request-Timestamp:'.$header_timestamp."\n".
        'Request-Target:'.$request_target."\n".
        'Digest:'.$digest;

    $expected_signature = 'HMACSHA256='.base64_encode(
        hash_hmac('sha256', $component_signature, $doku_secret_key, true)
    );

    /* =========================
    VALIDASI SIGNATURE
    ========================= */

    if ($header_client_id !== $doku_client_id || $header_signature !== $expected_signature) {
        http_response_code(403);
        exit('Invalid Signature');
    }

    /* =========================
    AMBIL DATA
    ========================= */

    $order_id           = isset($result['order']['invoice_number']) ? $result['order']['invoice_number'] : '';
    $transaction_status = isset($result['transaction']['status']) ? strtoupper($result['transaction']['status']) : '';
    $payment_type       = isset($result['channel']['id']) ? $result['channel']['id'] : '';
    $gross_amount       = isset($result['order']['amount']) ? $result['order']['amount'] : 0;

    if ($order_id === '') {
        http_response_code(400);
        exit('Invoice number not found');
    }

    /* =========================
    AMBIL weighing_id

    FORMAT:
    TONASE-12345-171111
    ========================= */

    $explode     = explode('-', $order_id);
    $weighing_id = isset($explode[1]) ? $explode[1] : '';
    $payment     = strtolower($explode[0]);

    $weighing_id = mysql_real_escape_string($weighing_id);
    $order_id    = mysql_real_escape_string($order_id);
    $payment_type = mysql_real_escape_string($payment_type);
    $gross_amount = mysql_real_escape_string($gross_amount);

    /* =========================
    STATUS SUCCESS
    ========================= */

    if ($transaction_status == 'SUCCESS') {
        if ($payment == 'tonase') {
            mysql_query("
                UPDATE weighing
                SET
                    weighing_sts3 = 'Pending'
                WHERE weighing_id = '$weighing_id'
            ");
        } else if ($payment == 'mitra') {
            mysql_query("
                UPDATE weighing_mitra
                SET
                    weighing_sts3 = 'Pending'
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
            '$order_id',
            '$weighing_id',
            'paid',
            '$payment_type',
            '$gross_amount',
            NOW()
        )");
    }

    /* =========================
    RESPON WAJIB
    ========================= */

    http_response_code(200);
    echo 'OK';
