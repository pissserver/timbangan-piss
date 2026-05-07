<?php

require_once dirname(__FILE__) . '/vendor/midtrans/midtrans-php/Midtrans.php';

include "connection.php";

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

/* =========================
ORDER ID UNIK
========================= */

if ($payment == 'tonase') {
	$order_id = 'TONASE-'.$weighing_id.'-'.date('Ymd');
} else if ($payment == 'mitra') {
	$order_id = 'MITRA-'.$weighing_id.'-'.date('Ymd');
} else {
	http_response_code(400);
	exit('Invalid Payment Type');
}

/* =========================
SIMPAN KE DATABASE (OPSIONAL)
========================= */

/*
mysql_query("
INSERT INTO payment_pending(
	order_id,
	weighing_id,
	total,
	status
) VALUES(
	'$order_id',
	'$weighing_id',
	'$total',
	'pending'
)
");
*/

/* =========================
PARAMETER MIDTRANS
========================= */

$params = array(

	'transaction_details' => array(
		'order_id'      => $order_id,
		'gross_amount'  => $total,
	),

	'customer_details' => array(
		'first_name' => $merchant,
	),

    'item_details' => array(
        array(
            'id' => $weighing_id,
            'price' => $total,
            'quantity' => 1,
            'name' => ($payment == 'tonase') ? "Tonase - ".$weighing_id." - ".$netto." kg" : "Mitra - ".$weighing_id." - ".$netto." kg"
        )
    ),

    'enabled_payments' => array('gopay', 'bank_transfer', 'qris')
);

/* =========================
GENERATE SNAP TOKEN
========================= */

$snapToken = \Midtrans\Snap::getSnapToken($params);

echo $snapToken;