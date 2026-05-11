<?php
	include "connection.php";
?>
<style>
/* ================= WRAPPER ================= */
#payTableWrapperMitra{
	display:flex;
	flex-direction:column;
	gap:14px;
}

/* ================= HEADER ================= */
.pay-header-mitra{
	display:flex;
	justify-content:space-between;
	align-items:center;
	margin-bottom:10px;
}
.pay-header-mitra h4{
	margin:0;
	font-weight:700;
	color:#1f2937;
}
.pay-search-mitra{
	padding:8px 14px;
	border-radius:12px;
	border:1px solid #d1d5db;
	width:220px;
	font-size:13px;
}

/* ================= CARD ================= */
.pay-card-mitra{
	display:grid;
	grid-template-columns: 1.2fr 1fr 1fr 140px;
	align-items:center;
	background:#fff;
	border-radius:18px;
	padding:16px 20px;
	box-shadow:0 14px 32px rgba(0,0,0,.08);
	transition:.25s ease;
}
.pay-card-mitra:hover{
	transform:translateY(-2px);
	box-shadow:0 20px 40px rgba(0,0,0,.12);
}

/* LEFT */
.pay-left-mitra h5{
	margin:0;
	font-weight:800;
	color:#16a34a;
	font-size:14px;
}
.pay-left-mitra span{
	font-size:12px;
	color:#6b7280;
}

/* MID */
.pay-mid-mitra{
	font-size:12px;
	color:#374151;
}
.pay-mid-mitra b{
	display:block;
	font-size:13px;
	color:#111827;
}

/* RIGHT */
.pay-right-mitra{
	text-align:right;
}
.pay-netto-mitra{
	font-weight:700;
	font-size:13px;
}
.pay-total-mitra{
	font-weight:800;
	color:#15803d;
	font-size:14px;
}

/* ACTION */
.pay-action-mitra button{
	border-radius:10px;
	font-weight:600;
}

/* ================= EMPTY ================= */
.pay-empty-mitra{
	display:none;
	text-align:center;
	padding:40px 0;
	color:#6b7280;
}
.pay-empty-mitra img{
	width:200px;
	opacity:.9;
}
.pay-empty-mitra h4{
	margin-top:14px;
	font-weight:700;
	color:#374151;
}

/* ================= PAGINATION ================= */
.pay-pagination-mitra{
	display:flex;
	justify-content:center;
	flex-wrap:wrap;
	gap:6px;
	margin-top:16px;
}

.pay-pagination-mitra button{
	border:1px solid #d1d5db;
	background:#fff;
	padding:6px 12px;
	border-radius:8px;
	font-size:12px;
	cursor:pointer;
}

.pay-pagination-mitra button.active{
	background:#16A34A;
	color:#fff;
	border-color:#16A34A;
}

.pay-pagination-mitra .dots{
	padding:6px 8px;
	color:#9ca3af;
	font-size:12px;
}

/* ================= HEADER NEO ================= */
.pay-header-neo-mitra{
	display:flex;
	justify-content:space-between;
	align-items:center;
	padding:16px 20px;
	background: #dfe4e8;
	border-radius:18px;
	box-shadow:0 12px 28px rgba(0,0,0,.08);
	margin-bottom:16px;
}

.pay-header-left-mitra{
	display:flex;
	align-items:center;
	gap:14px;
}

.pay-icon-mitra{
	width:46px;
	height:46px;
	border-radius:14px;
	background:linear-gradient(135deg,#16a34a,#16a34a);
	display:flex;
	align-items:center;
	justify-content:center;
	color:#fff;
	font-size:20px;
}

.pay-header-left-mitra h4{
	margin:0;
	font-weight:800;
	color:#111827;
	font-size:16px;
}

.pay-header-left-mitra span{
	font-size:12px;
	color:#6b7280;
}

.pay-header-right-mitra{
	display:flex;
	align-items:center;
}

.pay-search-wrap-mitra{
	position:relative;
}

.pay-search-wrap-mitra i{
	position:absolute;
	top:50%;
	left:14px;
	transform:translateY(-50%);
	color:#9ca3af;
	font-size:13px;
}

.pay-search-mitra{
	padding:8px 14px 8px 36px;
	border-radius:14px;
	border:1px solid #d1d5db;
	width:240px;
	font-size:13px;
	transition:.2s ease;
}

.pay-search-mitra:focus{
	outline:none;
	border-color: #66e546;
	box-shadow:0 0 0 3px rgba(79,70,229,.15);
}
</style>

<!-- ================= HEADER ================= -->
<div class="pay-header-neo-mitra">
	<div class="pay-header-left-mitra">
		<div class="pay-icon-mitra">
			<i class="fa fa-users"></i>
		</div>
		<div>
			<h4>Data Tagihan Mitra</h4>
			<span>Daftar penagihan yang siap diproses pembayaran</span>
		</div>
	</div>

	<div class="pay-header-right-mitra">
		<div class="pay-search-wrap-mitra">
			<i class="fa fa-search"></i>
			<input type="text" id="paySearchMitra" class="pay-search-mitra" placeholder="Cari ID, pedagang, nopol...">
		</div>
	</div>
</div>

<div id="payTableWrapperMitra">

<?php
$query2 = mysql_query("
	select 	w.weighing_id, 
			w.merchant_id, 
			w.netto,
			w.nopol, 
			w.cost_bagi_mitra,
			w.cost_simpan_mitra,
			w.cost_bagi_mandor,
			w.cost_simpan_mandor,
			w.cost_bayar,
			w.cost, 
			w.weighing_sts, 
			w.weighing_sts3, 
			w.date_out,
			(w.cost_bagi_mitra + w.cost_simpan_mitra) AS pendapatan_mitra,
			w.date_check,
			m.merchant_type 
	from 	weighing_mitra w, merchant m	
	where 	w.merchant_id = m.merchant_id and 
			m.merchant_status = 'Aktif' and
			w.weighing_sts = 'Keluar' and 
			w.weighing_sts3 is null and
			(w.date_in between '2016-09-01' and '".$date_now."') and 
			w.date_check is not null
	order by weighing_id asc
");

while($data2 = mysql_fetch_array($query2)){
	$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$data2['merchant_id']."'");
?>
	<div class="pay-card-mitra pay-item-mitra" id="row-<?php echo $data2['weighing_id']; ?>-mitra">

		<div class="pay-left-mitra">
			<h5><?php echo $data2['weighing_id']; ?></h5>
			<span><?php echo date("d M Y", strtotime($data2['date_check'])); ?> • <?php echo $data2['nopol']; ?></span><br>

		<?php
			$dateCheck = new DateTime($data2['date_check']);
			$today     = new DateTime();
			$diff      = $today->diff($dateCheck)->days;

			// opsional: warna beda sesuai umur
			$color = '#16a34a'; // hijau default

			if($diff >= 3 && $diff < 7){
				$color = '#f59e0b'; // kuning
			}elseif($diff >= 7){
				$color = '#dc2626'; // merah
			}
		?>

		<span style="color: <?php echo $color; ?>; font-size:11px;">
			Umur tagihan <?php echo $diff; ?> hari
		</span>
		</div>

		<div class="pay-mid-mitra">
			<b><?php echo $merchant; ?></b>
			<?php echo $data2['merchant_type']; ?><br>
			<small><?php echo $data2['merchant_id']; ?></small>
		</div>

		<div class="pay-right-mitra">
			<div class="pay-netto-mitra"><?php echo $data2['netto']; ?> Kg</div>
			<div class="pay-total-mitra"><?php echo format_rupiah($data2['cost_bayar']); ?></div>
		</div>

		<div class="pay-action-mitra text-right">
		<?php 
			if($data2['weighing_sts']!='Lunas'||$data2['weighing_sts3']!='Pending'){
				echo '
				<a href="pay_action.php?task=pay_mitra&type=ko&id='.$data2['weighing_id'].'" style="text-decoration:none;">
					<button class="btn btn-outline btn-success btn-sm">
						<i class="fa fa-money"></i> Bayar
					</button>
				</a>';
			}
		?>
		</div>

		<span class="pay-search-text-mitra" style="display:none;">
			<?php
				echo strtolower(
					$data2['weighing_id']." ".
					$merchant." ".
					$data2['merchant_id']." ".
					$data2['nopol']
				);
			?>
		</span>
	</div>
<?php } ?>

</div>

<!-- EMPTY -->
<div class="pay-empty-mitra" id="payEmptyMitra">
	<img src="img/analyze.png">
	<h4>Data tidak ditemukan</h4>
</div>

<div class="pay-pagination-mitra" id="payPaginationMitra"></div>


<script>
	const perPageMitra = 5;
	const maxPageShownMitra = 5;
	let pageMitra = 1;

	function renderPaginationMitra(total){
		const pages = Math.ceil(total / perPageMitra);
		if(pages <= 1){
			payPaginationMitra.innerHTML = '';
			return;
		}

		let html = '';

		/* PREV */
		if(pageMitra > 1){
			html += `<button onclick="goPageMitra(${pageMitra-1})">‹ Prev</button>`;
		}

		/* RANGE */
		let start = Math.max(1, pageMitra - Math.floor(maxPageShownMitra / 2));
		let end   = start + maxPageShownMitra - 1;

		if(end > pages){
			end = pages;
			start = Math.max(1, end - maxPageShownMitra + 1);
		}

		if(start > 1){
			html += `<button onclick="goPageMitra(1)">1</button>`;
			if(start > 2) html += `<span class="dots">...</span>`;
		}

		for(let i=start;i<=end;i++){
			html += `<button class="${i===pageMitra?'active':''}" onclick="goPageMitra(${i})">${i}</button>`;
		}

		if(end < pages){
			if(end < pages - 1) html += `<span class="dots">...</span>`;
			html += `<button onclick="goPageMitra(${pages})">${pages}</button>`;
		}

		/* NEXT */
		if(pageMitra < pages){
			html += `<button onclick="goPageMitra(${pageMitra+1})">Next ›</button>`;
		}

		payPaginationMitra.innerHTML = html;
	}

	function goPageMitra(p){
		pageMitra = p;
		filterMitra();
	}

	function filterMitra(){
		const key = paySearchMitra.value.toLowerCase();
		const items = document.querySelectorAll('.pay-item-mitra');
		let visible = [];

		items.forEach(item=>{
			const text = item.querySelector('.pay-search-text-mitra').innerText;
			if(text.includes(key)){
				item.style.display = 'grid';
				visible.push(item);
			}else{
				item.style.display = 'none';
			}
		});

		visible.forEach((item,i)=>{
			item.style.display =
				(i >= (pageMitra-1)*perPageMitra && i < pageMitra*perPageMitra)
				? 'grid' : 'none';
		});

		document.getElementById('payEmptyMitra').style.display =
			visible.length ? 'none' : 'block';

		renderPaginationMitra(visible.length);
	}

	paySearchMitra.addEventListener('keyup',()=>{
		pageMitra = 1;
		filterMitra();
	});

	filterMitra();
</script>

