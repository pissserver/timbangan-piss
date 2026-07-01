<?php

require_once dirname(__FILE__) . '/../vendor/midtrans/midtrans-php/Midtrans.php';
include "../connection.php";

/* =========================
CONFIG MIDTRANS
========================= */

\Midtrans\Config::$serverKey = 'Mid-server-KKefOwR1i3NTPI7EheVlXOrr';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

/* =========================
AMBIL DATA DARI AJAX
========================= */

$weighing_id = $_POST['weighing_id'];
$total       = (int)$_POST['total'];
$merchant    = $_POST['merchant'];
$type        = $_POST['type'];
$merchant_id = $_POST['merchant_id'];
$netto       = $_POST['netto'];
$payment     = $_POST['payment'];
$method      = $_POST['method'];

/* =========================
HITUNG FEE
========================= */

$fee = 0;
$enabled_payments = [];

switch($method){

    case 'qris':
        $fee = ceil($total * 0.007);
        $enabled_payments = ['gopay'];
    break;

    case 'va':
        $fee = 4440;
        $enabled_payments = ['bank_transfer'];
    break;

    default:

        http_response_code(400);
        exit('Metode pembayaran tidak valid');

}

/* =========================
TOTAL AKHIR
========================= */

$grand_total = $total + $fee;

/* =========================
ORDER ID
========================= */

if ($payment == 'tonase') {

    $order_id = 'TONASE-'.$weighing_id.'-'.time();

} else if ($payment == 'mitra') {

    $order_id = 'MITRA-'.$weighing_id.'-'.time();

} else {

    http_response_code(400);
    exit('Invalid Payment Type');

}

/* =========================
PARAMETER MIDTRANS
========================= */

$params = array(

    'transaction_details' => array(

        'order_id'     => $order_id,
        'gross_amount' => $grand_total

    ),

    'customer_details' => array(

        'first_name' => $merchant

    ),

    'item_details' => array(

        array(

            'id'       => $weighing_id,
            'price'    => $total,
            'quantity' => 1,
            'name'     => ($payment == 'tonase')
                ? 'Tonase '.$weighing_id.' ('.$netto.') Kg'
                : 'Mitra '.$weighing_id.' '.$netto.' Kg'

        ),

        array(

            'id'       => 'FEE',
            'price'    => $fee,
            'quantity' => 1,
            'name'     => 'Biaya Administrasi'

        )

    ),

    'enabled_payments' => $enabled_payments

);

/* =========================
GENERATE SNAP TOKEN
========================= */

try{

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    echo $snapToken;

}catch(Exception $e){

    http_response_code(500);
    echo $e->getMessage();

}