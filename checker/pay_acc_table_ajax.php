<?php
include "../connection.php";

$check 			= executeScalar("select count(weighing_id) from weighing where weighing_sts3 = 'Pending'");
$search = mysql_real_escape_string($_POST['search']);
$page   = (int)$_POST['page'];
$limit  = (int)$_POST['limit'];

if($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* ===== TOTAL DATA ===== */
$totalQ = mysql_query("
	select count(*) as total
	from weighing w
	INNER JOIN merchant m on w.merchant_id = m.merchant_id
	where 
	m.merchant_status='Aktif'
	and w.weighing_sts='Keluar'
	and w.weighing_sts3='Pending'
	and (
		w.weighing_id like '%$search%' OR
		w.nopol like '%$search%' OR
		m.merchant_type like '%$search%' OR
		m.merchant_name like '%$search%'
	)
");

$total   = mysql_fetch_assoc($totalQ)['total'];
$totalPage = ceil($total / $limit);

/* ===== DATA ===== */
$q = mysql_query("
	select w.*, m.merchant_type, m.merchant_name
	from weighing w
	join merchant m on w.merchant_id = m.merchant_id
	where m.merchant_status='Aktif'
	and w.weighing_sts='Keluar'
	and w.weighing_sts3='Pending'
	and (
		w.weighing_id like '%$search%' OR
		w.nopol like '%$search%' OR
		m.merchant_type like '%$search%' OR
		m.merchant_name like '%$search%'
	)
	order by w.weighing_id desc
	limit $offset,$limit
");

$html = '';

while($d = mysql_fetch_array($q)){

	$html .= '
	<div class="payment-card-tonase">
		<div class="payment-left">
			<div class="nota-id-tonase">#'.$d['weighing_id'].'</div>
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
			<div class="amount">'.format_rupiah($d['cost_tonase']).'</div>
		</div>
	</div>';
}

if(mysql_num_rows($q) < 1 && $check > 0){
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
} elseif ($check < 1){
    $html .= '
    		<div class="payment-notfound text-center">
			<img src="../img/payment-tonase-notfound.png" alt="Image Payment Not Found" width="200px">
			<h3><b>Belum Ada Pembayaran</b></h3>
		</div>
    ';
}

/* ===== PAGINATION ===== */
$pagination = '';

if($totalPage > 1){
	for($i=1;$i<=$totalPage;$i++){
		$active = ($i==$page)?'active':'';
		$pagination .= '<a href="#" class="page-link '.$active.'" data-page="'.$i.'">'.$i.'</a>';
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
