<?php
    include "connection.php";
    require_once dirname(__FILE__) . '/vendor/midtrans/midtrans-php/Midtrans.php';

    /* =========================
    CONFIG MIDTRANS
    ========================= */

    \Midtrans\Config::$serverKey = 'Mid-server-KKefOwR1i3NTPI7EheVlXOrr';
    \Midtrans\Config::$isProduction = false;

    /* =========================
    AMBIL JSON CALLBACK
    ========================= */

    $json_result = file_get_contents("php://input");
    $result = json_decode($json_result, true);

    /* =========================
    LOG CALLBACK (OPTIONAL)
    ========================= */

    file_put_contents(
        'callback_log.txt',
        date('Y-m-d H:i:s')."\n".
        $json_result."\n\n",
        FILE_APPEND
    );

    /* =========================
    VALIDASI JSON
    ========================= */

    if(!$result){
        http_response_code(400);
        exit('Invalid JSON');
    }

    /* =========================
    VALIDASI SIGNATURE
    ========================= */

    $signature_key = hash(
        'sha512',
        $result['order_id'] .
        $result['status_code'] .
        $result['gross_amount'] .
        \Midtrans\Config::$serverKey
    );

    if($signature_key != $result['signature_key']){
        http_response_code(403);
        exit('Invalid Signature');
    }

    /* =========================
    AMBIL DATA
    ========================= */

    $order_id           = $result['order_id'];
    $transaction_status = $result['transaction_status'];
    $payment_type       = $result['payment_type'];
    $gross_amount       = $result['gross_amount'];

    /* =========================
    AMBIL weighing_id

    FORMAT:
    TONASE-12345-171111
    ========================= */

    $explode = explode('-', $order_id);
    $weighing_id = $explode[1];
    $payment = strtolower($explode[0]); // TONASE atau MITRA

    /* =========================
    STATUS SUCCESS
    ========================= */

    if($transaction_status == 'settlement' || $transaction_status == 'capture'){
        if ($payment == 'tonase') {
            // Update tonase status
            mysql_query("
                UPDATE weighing
                SET
                    weighing_sts3 = 'Pending'
                WHERE weighing_id = '$weighing_id'
            ");
        } else if ($payment == 'mitra') {
            // Update mitra status
            mysql_query("
                UPDATE weighing_mitra
                SET
                    weighing_sts3 = 'Pending'
                WHERE weighing_id = '$weighing_id'
            ");
        }

        /* =========================
        SIMPAN HISTORI
        ========================= */

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
    echo "OK";