<?php
	include "connection.php";
?>
<style>
/* ================= WRAPPER ================= */
#payTableWrapper{
	display:flex;
	flex-direction:column;
	gap:14px;
}

/* ================= HEADER ================= */
.pay-header{
	display:flex;
	justify-content:space-between;
	align-items:center;
	margin-bottom:10px;
}
.pay-header h4{
	margin:0;
	font-weight:700;
	color:#1f2937;
}
.pay-search{
	padding:8px 14px;
	border-radius:12px;
	border:1px solid #d1d5db;
	width:220px;
	font-size:13px;
}

/* ================= CARD ================= */
.pay-card{
	display:grid;
	grid-template-columns: 1.2fr 1fr 1fr 140px;
	align-items:center;
	background:#fff;
	border-radius:18px;
	padding:16px 20px;
	box-shadow:0 14px 32px rgba(0,0,0,.08);
	transition:.25s ease;
}
.pay-card:hover{
	transform:translateY(-2px);
	box-shadow:0 20px 40px rgba(0,0,0,.12);
}

/* LEFT */
.pay-left h5{
	margin:0;
	font-weight:800;
	color: #5148E5;
	font-size:14px;
}
.pay-left span{
	font-size:12px;
	color:#6b7280;
}

/* MID */
.pay-mid{
	font-size:12px;
	color:#374151;
}
.pay-mid b{
	display:block;
	font-size:13px;
	color:#111827;
}

/* RIGHT INFO */
.pay-right{
	text-align:right;
}
.pay-netto{
	font-weight:700;
	font-size:13px;
}
.pay-total{
	font-weight:800;
	color:#15803d;
	font-size:14px;
}

/* ACTION */
.pay-action button{
	border-radius:10px;
	font-weight:600;
}

/* ================= EMPTY ================= */
.pay-empty{
	display:none;
	text-align:center;
	padding:40px 0;
	color:#6b7280;
}
.pay-empty img{
	width:200px;
	opacity:.9;
}
.pay-empty h4{
	margin-top:14px;
	font-weight:700;
	color:#374151;
}

/* ================= PAGINATION ================= */
.pay-pagination{
	display:flex;
	justify-content:center;
	flex-wrap:wrap;
	gap:6px;
	margin-top:16px;
}

.pay-pagination button{
	border:1px solid #d1d5db;
	background:#fff;
	padding:6px 12px;
	border-radius:8px;
	font-size:12px;
	cursor:pointer;
}

.pay-pagination button.active{
	background:#5148E5;
	color:#fff;
	border-color:#5148E5;
}

.pay-pagination .dots{
	padding:6px 8px;
	color:#9ca3af;
	font-size:12px;
}

/* ================= HEADER NEO ================= */
.pay-header-neo{
	display:flex;
	justify-content:space-between;
	align-items:center;
	padding:16px 20px;
	background: #dfe4e8;
	border-radius:18px;
	box-shadow:0 12px 28px rgba(0,0,0,.08);
	margin-bottom:16px;
}

.pay-header-left{
	display:flex;
	align-items:center;
	gap:14px;
}

.pay-icon{
	width:46px;
	height:46px;
	border-radius:14px;
	background:#5148E5;
	display:flex;
	align-items:center;
	justify-content:center;
	color:#fff;
	font-size:20px;
}

.pay-header-left h4{
	margin:0;
	font-weight:800;
	color:#111827;
	font-size:16px;
}

.pay-header-left span{
	font-size:12px;
	color:#6b7280;
}

.pay-header-right{
	display:flex;
	align-items:center;
}

.pay-search-wrap{
	position:relative;
}

.pay-search-wrap i{
	position:absolute;
	top:50%;
	left:14px;
	transform:translateY(-50%);
	color:#9ca3af;
	font-size:13px;
}

.pay-search{
	padding:8px 14px 8px 36px;
	border-radius:14px;
	border:1px solid #d1d5db;
	width:240px;
	font-size:13px;
	transition:.2s ease;
}

.pay-search:focus{
	outline:none;
	border-color:#4f46e5;
	box-shadow:0 0 0 3px rgba(79,70,229,.15);
}
</style>

<!-- ================= HEADER ================= -->
<div class="pay-header-neo">
	<div class="pay-header-left">
		<div class="pay-icon">
			<i class="fa fa-truck"></i>
		</div>
		<div>
			<h4>Data Tagihan Tonase</h4>
			<span>Daftar penagihan yang siap diproses pembayaran</span>
		</div>
	</div>

	<div class="pay-header-right">
		<div class="pay-search-wrap">
			<i class="fa fa-search"></i>
			<input type="text" id="paySearch" class="pay-search" placeholder="Cari ID, pedagang, nopol...">
		</div>
	</div>
</div>



<div id="payTableWrapper" class="slide-normal">


<?php

/* =============================
PRELOAD CENTENG
============================= */

$centengList = [];

$qCenteng = mysql_query("
	SELECT DISTINCT merchant_id
	FROM weighing
	WHERE weighing_sts != 'Batal'
	AND weighing_sts2 = 'Tanpa Timbang'
	AND date_pay IS NULL
");

while($c = mysql_fetch_array($qCenteng)){

$centengList[$c['merchant_id']] = true;

}



/* =============================
QUERY UTAMA (OPTIMIZED)
============================= */


$query = mysql_query("

SELECT

w.weighing_id,
w.merchant_id,
w.nopol,
w.netto,
w.cost_tonase,
w.weighing_sts2,
w.date_check,

m.merchant_name,
m.merchant_type


FROM weighing w

JOIN merchant m ON w.merchant_id = m.merchant_id


WHERE

m.merchant_status = 'Aktif'

AND w.weighing_sts = 'Keluar'

AND w.weighing_sts3 IS NULL

AND w.date_check IS NOT NULL


ORDER BY w.weighing_id ASC


LIMIT 500

");



while($data = mysql_fetch_array($query)){


$merchant = $data['merchant_name'];

$checkCenteng = isset($centengList[$data['merchant_id']]);

$isCentengNota = $data['weighing_sts2'] == 'Tanpa Timbang';

?>


<div class="pay-card pay-item" id="row-<?php echo $data['weighing_id']; ?>">


	<div class="pay-left">

		<h5><?php echo $data['weighing_id']; ?></h5>

		<span>

		<?php echo date("d M Y", strtotime($data['date_check'])); ?>

		• <?php echo $data['nopol']; ?>

		</span>

	</div>



	<div class="pay-mid">

		<b><?php echo $merchant; ?></b>

		<?php echo $data['merchant_type']; ?>

		<br>

		<small><?php echo $data['merchant_id']; ?></small>

	</div>



	<div class="pay-right">

		<div class="pay-netto">

			<?php echo number_format($data['netto']); ?> Kg

		</div>

		<div class="pay-total">

			<?php echo format_rupiah($data['cost_tonase']); ?>

		</div>

	</div>



	<div class="pay-action text-right">


<?php


/* =============================
LOGIKA BAYAR (SAMA)
============================= */


if ($checkCenteng){


	if ($isCentengNota){


	echo '

	<a href="pay_action.php?task=pay&type=ko&id='.$data['weighing_id'].'" style="text-decoration:none;">

	<button class="btn btn-outline btn-primary btn-sm btn-bayar">

	<i class="fa fa-money"></i> Bayar

	</button>

	</a>

	';


	}else{


	echo '

	<div class="btn" style="background-color:#000;color:#fff;" disabled>

	<i class="fa fa-lock"></i>

	</div>

	';


	}


}else{


	echo '

	<a href="pay_action.php?task=pay&type=ko&id='.$data['weighing_id'].'" style="text-decoration:none;">

	<button class="btn btn-outline btn-primary btn-sm btn-bayar">

	<i class="fa fa-money"></i> Bayar

	</button>

	</a>

	';


}

?>


	</div>



<span class="pay-search-text" style="display:none;">


<?php


echo strtolower(

$data['weighing_id']." ".

$merchant." ".

$data['merchant_id']." ".

$data['nopol']

);


?>


</span>



</div>


<?php } ?>



</div>




<!-- EMPTY -->

<div class="pay-empty" id="payEmpty">

	<img src="img/analyze.png">

	<h4>Data tidak ditemukan</h4>

</div>



<div class="pay-pagination" id="payPagination"></div>





<!-- =============================
PAGINATION JS (PERSIS KODE ASLI KAMU)
============================= -->


<script>


const payPerPage = 5;
const maxPageShown = 5;
let payPage = 1;



function renderPayPagination(total){

	const pages = Math.ceil(total / payPerPage);

	if(pages <= 1){

		payPagination.innerHTML = '';

		return;

	}


	let html = '';


	/* PREV */

	if(payPage > 1){

		html += `<button onclick="goPayPage(${payPage-1})">‹ Prev</button>`;

	}



	let start = Math.max(1, payPage - Math.floor(maxPageShown / 2));

	let end   = start + maxPageShown - 1;



	if(end > pages){

		end = pages;

		start = Math.max(1, end - maxPageShown + 1);

	}



	if(start > 1){

		html += `<button onclick="goPayPage(1)">1</button>`;

		if(start > 2) html += `<span class="dots">...</span>`;

	}



	for(let i=start;i<=end;i++){

		html += `<button class="${i===payPage?'active':''}" onclick="goPayPage(${i})">${i}</button>`;

	}



	if(end < pages){

		if(end < pages - 1)

		html += `<span class="dots">...</span>`;

		html += `<button onclick="goPayPage(${pages})">${pages}</button>`;

	}



	if(payPage < pages){

		html += `<button onclick="goPayPage(${payPage+1})">Next ›</button>`;

	}



	payPagination.innerHTML = html;

}




function goPayPage(p){

	payPage = p;

	filterPay();

}




function filterPay(){


	const key = paySearch.value.toLowerCase();

	const items = document.querySelectorAll('.pay-item');

	let visible = [];


	items.forEach(item=>{


		const text = item.querySelector('.pay-search-text').innerText;


		if(text.includes(key)){

			item.style.display='grid';

			visible.push(item);

		}else{

			item.style.display='none';

		}


	});


	visible.forEach((item,i)=>{


		item.style.display =

		(i >= (payPage-1)*payPerPage && i < payPage*payPerPage)

		? 'grid' : 'none';


	});


	document.getElementById('payEmpty').style.display =

	visible.length ? 'none' : 'block';


	renderPayPagination(visible.length);


}



paySearch.addEventListener('keyup',()=>{

	payPage = 1;

	filterPay();

});

filterPay();
</script>
