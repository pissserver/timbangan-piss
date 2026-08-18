<?php

include "../connection.php";

/* =========================
CONFIG DOKU
Masukkan Client ID & Secret Key dari DOKU Dashboard > Integrations > API Keys
========================= */

$doku_client_id      = 'BRN-0260-1787041432710';
$doku_secret_key     = 'SK-h2tLzpQD6qB9Ie3cnitg';
$doku_is_production  = false;

$doku_base_url = $doku_is_production
    ? 'https://api.doku.com'
    : 'https://api-sandbox.doku.com';

$doku_request_target = '/checkout/v1/payment';

/* =========================
AMBIL DATA DARI AJAX
========================= */

$weighing_id = isset($_POST['weighing_id']) ? $_POST['weighing_id'] : '';
$total       = (int)$_POST['total'];
$merchant    = isset($_POST['merchant']) ? $_POST['merchant'] : '';
$type        = isset($_POST['type']) ? $_POST['type'] : '';
$merchant_id = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : '';
$netto       = isset($_POST['netto']) ? $_POST['netto'] : '';
$payment     = isset($_POST['payment']) ? $_POST['payment'] : '';
$return_url  = isset($_POST['return_url']) ? $_POST['return_url'] : '';

header('Content-Type: application/json');

if ($doku_client_id === 'MASUKKAN_CLIENT_ID_DOKU' || $doku_secret_key === 'MASUKKAN_SECRET_KEY_DOKU') {
    http_response_code(500);
    echo json_encode(array(
        'message' => 'Client ID dan Secret Key DOKU belum diisi di configDoku.php'
    ));
    exit;
}

$grand_total = $total;

/* =========================
ORDER ID / INVOICE NUMBER
========================= */

if ($payment == 'tonase') {

    $order_id = 'TONASE-'.$weighing_id.'-'.time();
    $item_name = 'Tonase '.$weighing_id.' ('.$netto.') Kg';

} else if ($payment == 'mitra') {

    $order_id = 'MITRA-'.$weighing_id.'-'.time();
    $item_name = 'Mitra '.$weighing_id.' '.$netto.' Kg';

} else {

    http_response_code(400);
    echo json_encode(array('message' => 'Invalid Payment Type'));
    exit;

}

$is_https =
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

$protocol = $is_https ? 'https' : 'http';
$host     = $_SERVER['HTTP_HOST'];
$base_url = $protocol.'://'.$host.rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

if ($return_url === '') {
    $return_url = $base_url.'/'.($payment == 'mitra' ? 'pay_mitra.php' : 'pay_tonase.php');
}

$notification_url = $base_url.'/callbackDoku.php';

/* =========================
PARAMETER DOKU CHECKOUT
========================= */

$request_body = array(
    'order' => array(
        'amount'              => $grand_total,
        'invoice_number'      => $order_id,
        'currency'            => 'IDR',
        'callback_url'        => $return_url,
        'callback_url_result' => $return_url,
        'auto_redirect'       => false,
        'line_items'          => array(
            array(
                'id'       => (string)$weighing_id,
                'name'     => $item_name,
                'quantity' => 1,
                'price'    => $total
            )
        )
    ),
    'payment' => array(
        'payment_due_date'     => 60,
        'payment_method_types' => array(
            'VIRTUAL_ACCOUNT_BCA',
            'VIRTUAL_ACCOUNT_BANK_MANDIRI',
            'VIRTUAL_ACCOUNT_BANK_SYARIAH_MANDIRI',
            'VIRTUAL_ACCOUNT_DOKU',
            'VIRTUAL_ACCOUNT_BRI',
            'VIRTUAL_ACCOUNT_BNI',
            'VIRTUAL_ACCOUNT_BANK_PERMATA',
            'VIRTUAL_ACCOUNT_BANK_CIMB',
            'VIRTUAL_ACCOUNT_BANK_DANAMON',
            'VIRTUAL_ACCOUNT_BNC',
            'VIRTUAL_ACCOUNT_BTN',
            'QRIS'
        )
    ),
    'customer' => array(
        'name' => $merchant
    ),
    'additional_info' => array(
        'override_notification_url' => $notification_url
    )
);

$json_body = json_encode($request_body);

$request_id        = uniqid('doku-', true);
$request_timestamp = gmdate('Y-m-d\TH:i:s\Z');
$digest            = base64_encode(hash('sha256', $json_body, true));

$component_signature =
    'Client-Id:'.$doku_client_id."\n".
    'Request-Id:'.$request_id."\n".
    'Request-Timestamp:'.$request_timestamp."\n".
    'Request-Target:'.$doku_request_target."\n".
    'Digest:'.$digest;

$signature = 'HMACSHA256='.base64_encode(hash_hmac('sha256', $component_signature, $doku_secret_key, true));

/* =========================
HIT DOKU CHECKOUT API
========================= */

$ch = curl_init($doku_base_url.$doku_request_target);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Id: '.$doku_client_id,
    'Request-Id: '.$request_id,
    'Request-Timestamp: '.$request_timestamp,
    'Signature: '.$signature
));

$response_body = curl_exec($ch);
$http_code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error    = curl_error($ch);
curl_close($ch);

if ($response_body === false) {
    http_response_code(500);
    echo json_encode(array('message' => 'Gagal terhubung ke DOKU: '.$curl_error));
    exit;
}

$result = json_decode($response_body, true);

file_put_contents(
    'doku_debug_log.txt',
    date('Y-m-d H:i:s')."\n".
    'HTTP: '.$http_code."\n".
    'REQUEST: '.$json_body."\n".
    'RESPONSE: '.$response_body."\n\n",
    FILE_APPEND
);

if ($http_code != 200 || empty($result['response']['payment']['url'])) {
    http_response_code(500);

    $message = 'Gagal membuat pembayaran DOKU';

    if (!empty($result['error_messages']) && is_array($result['error_messages'])) {
        $message = implode(', ', $result['error_messages']);
    } else if (!empty($result['message']) && is_array($result['message'])) {
        $message = implode(', ', $result['message']);
    } else if (!empty($result['message']) && is_string($result['message'])) {
        $message = $result['message'];
    }

    echo json_encode(array(
        'message'  => $message,
        'response' => $result
    ));
    exit;
}

echo json_encode(array(
    'payment_url'     => $result['response']['payment']['url'],
    'invoice_number'  => $order_id,
    'token_id'        => $result['response']['payment']['token_id']
));
