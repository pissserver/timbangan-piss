<?php
	include "../connection.php";
?>
<style>
/* ================= HEADER ================= */
.acc-header-mitra{
	display:flex;
	justify-content:space-between;
	align-items:center;
	margin:10px 0 14px;
}
.acc-header-mitra h4{
	margin:0;
	font-weight:700;
	color:#1f2937;
}
.acc-search-mitra{
	padding:8px 14px;
	border-radius:12px;
	border:1px solid #d1d5db;
	width:220px;
	font-size:13px;
}

/* ================= LIST ================= */
.acc-list-mitra{
	display:flex;
	flex-direction:column;
	gap:12px;
}

/* ================= CARD ================= */
.acc-card-mitra{
	display:grid;
	grid-template-columns: 1fr 140px 1fr;
	align-items:center;
	background:#fff;
	border-radius:16px;
	padding:14px 18px;
	box-shadow:0 12px 26px rgba(0,0,0,.08);
	transition:.2s ease;
	min-height:76px;
}
.acc-card-mitra:hover{
	transform:translateY(-2px);
	box-shadow:0 18px 34px rgba(0,0,0,.12);
}

/* LEFT */
.acc-left-mitra h5{
	margin:0;
	font-weight:800;
	color:#16a34a;
	font-size:14px;
}
.acc-left-mitra span{
	font-size:12px;
	color:#6b7280;
}

/* CENTER */
.acc-mid-mitra{
	display:flex;
	flex-direction:column;
	align-items:center;
	justify-content:center;
	gap:4px;
}
.acc-status-mitra{
	background:#16a34a;
	color:#fff;
	padding:4px 14px;
	border-radius:999px;
	font-weight:700;
	font-size:12px;
}
.acc-date-mitra{
	font-size:11px;
	color:#374151;
	line-height:1;
}

/* RIGHT */
.acc-right-mitra{
	text-align:right;
}
.acc-total-mitra{
	font-weight:800;
	color:#15803d;
	font-size:14px;
}
.acc-approver-mitra{
	font-size:12px;
	color:#6b7280;
}

/* EMPTY */
.acc-empty-mitra{
	display:none;
	text-align:center;
	padding:40px 0;
	color:#6b7280;
}
.acc-empty-mitra i{
	font-size:32px;
	color:#cbd5e1;
	margin-bottom:10px;
}
.acc-empty-mitra span{
	display:block;
	font-size:13px;
}

/* PAGINATION */
.acc-pagination-mitra{
	display:flex;
	justify-content:center;
	gap:6px;
	margin-top:16px;
}
.acc-pagination-mitra button{
	border:1px solid #d1d5db;
	background:#fff;
	padding:6px 12px;
	border-radius:8px;
	font-size:12px;
	cursor:pointer;
}
.acc-pagination-mitra button.active{
	background:#4f46e5;
	color:#fff;
	border-color:#4f46e5;
}
</style>

<!-- ================= HEADER ================= -->
<div class="acc-header-mitra">
	<input type="text" id="accSearchMitra" class="acc-search-mitra" placeholder="Search...">
</div>

<!-- ================= LIST ================= -->
<div class="acc-list-mitra" id="accListMitra">
<?php
$query = mysql_query("select * from km_mitra where km_status = 'Acc' order by km_id DESC limit 50");
while($data = mysql_fetch_array($query)){
	$cashier_id = executeScalar("select cashier_id from ko_mitra where km_id='".$data['km_id']."'");
	$cashier    = executeScalar("select checker_name from checker where checker_id='".$cashier_id."'");
?>
	<div class="acc-card-mitra acc-item-mitra">
		<div class="acc-left-mitra">
			<h5><?php echo $data['km_id']; ?></h5>
			<span><?php echo $cashier; ?> • <?php echo date("d M Y", strtotime($data['km_date'])); ?></span>
		</div>

		<div class="acc-mid-mitra">
			<div class="acc-status-mitra">
				<i class="fa fa-check"></i> <?php echo strtoupper($data['km_status']); ?>
			</div>
			<div class="acc-date-mitra">
				<?php echo date("d M Y", strtotime($data['km_date_acc'])); ?>
			</div>
		</div>

		<div class="acc-right-mitra">
			<div class="acc-total-mitra"><?php echo format_rupiah($data['km_total']); ?></div>
			<div class="acc-approver-mitra">by <?php echo ucfirst($data['km_approver']); ?></div>
		</div>

		<span class="acc-search-text-mitra" style="display:none;">
			<?php echo $data['km_id']." ".$cashier." ".$data['km_approver']; ?>
		</span>
	</div>
<?php } ?>
</div>

<!-- EMPTY -->
<div class="acc-empty-mitra" id="accEmptyMitra">
	<i class="fa fa-folder-open"></i>
	<span>Data tidak ditemukan</span>
</div>

<!-- PAGINATION -->
<div class="acc-pagination-mitra" id="accPaginationMitra"></div>

<script>
const itemsPerPageMitra = 5;
let currentPageMitra = 1;

function renderPaginationMitra(total){
	const pageCount = Math.ceil(total / itemsPerPageMitra);
	let html='';
	for(let i=1;i<=pageCount;i++){
		html+=`<button class="${i===currentPageMitra?'active':''}" onclick="goPageMitra(${i})">${i}</button>`;
	}
	document.getElementById('accPaginationMitra').innerHTML = html;
}

function goPageMitra(p){
	currentPageMitra = p;
	filterListMitra();
}

function filterListMitra(){
	const keyword = accSearchMitra.value.toLowerCase();
	const items = document.querySelectorAll('.acc-item-mitra');
	let visible = [];

	items.forEach(item=>{
		const text = item.querySelector('.acc-search-text-mitra').innerText.toLowerCase();
		if(text.includes(keyword)){
			item.style.display='grid';
			visible.push(item);
		}else{
			item.style.display='none';
		}
	});

	visible.forEach((item,i)=>{
		item.style.display = (i>=(currentPageMitra-1)*itemsPerPageMitra && i<currentPageMitra*itemsPerPageMitra)
			? 'grid' : 'none';
	});

	accEmptyMitra.style.display = visible.length ? 'none' : 'block';
	renderPaginationMitra(visible.length);
}

accSearchMitra.addEventListener('keyup',()=>{
	currentPageMitra=1;
	filterListMitra();
});

filterListMitra();
</script>
