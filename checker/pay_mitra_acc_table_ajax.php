<?php
include "../connection.php";

$check_mitra 	= executeScalar("select count(weighing_id) from weighing_mitra where weighing_sts3 = 'Pending'");
$search = mysql_real_escape_string($_POST['search']);
$page   = (int)$_POST['page'];
$limit  = (int)$_POST['limit'];

if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* ===== TOTAL DATA ===== */
$totalQ = mysql_query("
	SELECT count(*) as total
	FROM weighing_mitra w
	INNER JOIN merchant m ON w.merchant_id = m.merchant_id
	WHERE 
		m.merchant_status='Aktif' AND
		w.weighing_sts='Keluar' AND
		w.weighing_sts3='Pending' AND
		(
			w.weighing_id LIKE '%$search%' OR
			w.nopol LIKE '%$search%' OR
			m.merchant_type LIKE '%$search%' OR
			m.merchant_name LIKE '%$search%'
		)
");

$total     = mysql_fetch_assoc($totalQ)['total'];
$totalPage = ceil($total / $limit);

/* ===== DATA ===== */
$q = mysql_query("
	SELECT 
		w.*, 
		m.merchant_type,
		m.merchant_name
	FROM weighing_mitra w
	INNER JOIN merchant m ON w.merchant_id = m.merchant_id
	WHERE 
		m.merchant_status='Aktif' AND
		w.weighing_sts='Keluar' AND
		w.weighing_sts3='Pending' AND
		(
			w.weighing_id LIKE '%$search%' OR
			w.nopol LIKE '%$search%' OR
			m.merchant_type LIKE '%$search%' OR
			m.merchant_name LIKE '%$search%'
		)
	ORDER BY w.weighing_id DESC
	LIMIT $offset,$limit
");

$html = '';

while($d = mysql_fetch_array($q)){
	$html .= '
	<div class="payment-card-mitra mitra">
		<div class="payment-left">
			<div class="nota-id-mitra">#'.$d['weighing_id'].'</div>
			<div class="nota-date">'.date("d M Y", strtotime($d['date_check'])).'</div>
			<div class="nota-truck"><i class="fa fa-truck"></i> '.$d['nopol'].'</div>
		</div>

		<div class="payment-mid">
			<div class="merchant"><i class="fa fa-user"></i> '.$d['merchant_name'].'</div>
			<div class="commodity">'.$d['merchant_type'].'</div>
			<div class="code">Kode: '.$d['merchant_id'].'</div>
		</div>

		<div class="payment-right">
			<div class="netto">'.number_format($d['netto']).' Kg</div>
			<div class="amount">'.format_rupiah($d['cost_bayar']).'</div>
		</div>
	</div>';
}

if(mysql_num_rows($q) < 1 && $check_mitra > 0){
	$html = '
	<div class="empty-state">
		<div class="empty-icon">
			<i class="fa fa-search"></i>
		</div>
		<h4>Data Tidak Ditemukan</h4>
		<p>
			Tidak ada data yang cocok dengan<br>
			kata kunci <b>'.htmlspecialchars($search).'</b>
		</p>
	</div>';
} elseif ($check_mitra < 1){
    $html .= '
    		<div class="payment-notfound text-center">
			<img src="../img/payment-mitra-notfound.png" alt="Image Payment Not Found" width="200px">
			<h3><b>Belum Ada Pembayaran</b></h3>
		</div>
    ';
}

/* ===== PAGINATION ===== */
$pagination = '';

if($totalPage > 1){
	for($i=1;$i<=$totalPage;$i++){
		$active = ($i==$page)?'active':'';
		$pagination .= '<a href="#" class="page-link-mitra '.$active.'" data-page="'.$i.'">'.$i.'</a>';
	}
}

$start = ($total > 0) ? $offset + 1 : 0;
$end   = min($offset + $limit, $total);

$info = ($total > 0)
	? "Menampilkan <b>$start</b> – <b>$end</b> dari <b>$total</b> total data"
	: "";

echo json_encode([
	'html' => $html,
	'pagination' => '<div class="pagination-wrapper">'.$pagination.'</div>',
	'info'       => $info
]);
