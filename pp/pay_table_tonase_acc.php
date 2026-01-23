<?php
	include "../connection.php";
?>
<style>
/* ================= HEADER ================= */
.acc-header{
	display:flex;
	justify-content:space-between;
	align-items:center;
	margin:10px 0 14px;
}
.acc-header h4{
	margin:0;
	font-weight:700;
	color:#1f2937;
}
.acc-search{
	padding:8px 14px;
	border-radius:12px;
	border:1px solid #d1d5db;
	width:220px;
	font-size:13px;
}

/* ================= LIST ================= */
.acc-list{
	display:flex;
	flex-direction:column;
	gap:12px;
}

/* ===== CARD (GRID BASED) ===== */
.acc-card{
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
.acc-card:hover{
	transform:translateY(-2px);
	box-shadow:0 18px 34px rgba(0,0,0,.12);
}

/* LEFT */
.acc-left h5{
	margin:0;
	font-weight:800;
	color:#4f46e5;
	font-size:14px;
}
.acc-left span{
	font-size:12px;
	color:#6b7280;
}

/* CENTER (PERFECTLY SYMMETRIC) */
.acc-mid{
	display:flex;
	flex-direction:column;
	align-items:center;
	justify-content:center;
	gap:4px;
}
.acc-status{
	background:#4f46e5;
	color:#fff;
	padding:4px 14px;
	border-radius:999px;
	font-weight:700;
	font-size:12px;
}
.acc-date{
	font-size:11px;
	color:#374151;
	line-height:1;
}

/* RIGHT */
.acc-right{
	text-align:right;
}
.acc-total{
	font-weight:800;
	color:#15803d;
	font-size:14px;
}
.acc-approver{
	font-size:12px;
	color:#6b7280;
}

/* ================= EMPTY STATE ================= */
.acc-empty{
	display:none;
	text-align:center;
	padding:40px 0;
	color:#6b7280;
}
.acc-empty i{
	font-size:32px;
	color:#cbd5e1;
	margin-bottom:10px;
}
.acc-empty span{
	display:block;
	font-size:13px;
}

/* ================= PAGINATION ================= */
.acc-pagination{
	display:flex;
	justify-content:center;
	gap:6px;
	margin-top:16px;
}
.acc-pagination button{
	border:1px solid #d1d5db;
	background:#fff;
	padding:6px 12px;
	border-radius:8px;
	font-size:12px;
	cursor:pointer;
}
.acc-pagination button.active{
	background:#4f46e5;
	color:#fff;
	border-color:#4f46e5;
}

</style>

<!-- ================== HEADER ================== -->
<div class="acc-header">
	<input type="text" id="accSearch" class="acc-search" placeholder="Search...">
</div>

<div class="acc-list" id="accList">
<?php
$query = mysql_query("select * from km where km_status='Acc' order by km_id DESC limit 50");
while($data = mysql_fetch_array($query)){
	$cashier_id = executeScalar("select cashier_id from ko where km_id='".$data['km_id']."'");
	$cashier    = executeScalar("select checker_name from checker where checker_id='".$cashier_id."'");
?>
	<div class="acc-card acc-item">
		<div class="acc-left">
			<h5><?php echo $data['km_id']; ?></h5>
			<span><?php echo $cashier; ?> • <?php echo date("d M Y", strtotime($data['km_date'])); ?></span>
		</div>

		<div class="acc-mid">
			<div class="acc-status"><i class="fa fa-check"></i> ACC</div>
			<div class="acc-date"><?php echo date("d M Y", strtotime($data['km_date_acc'])); ?></div>
		</div>

		<div class="acc-right">
			<div class="acc-total"><?php echo format_rupiah($data['km_total']); ?></div>
			<div class="acc-approver">by <?php echo ucfirst($data['km_approver']); ?></div>
		</div>

		<span class="acc-search-text" style="display:none;">
			<?php echo $data['km_id']." ".$cashier." ".$data['km_approver']; ?>
		</span>
	</div>
<?php } ?>
</div>

<!-- EMPTY STATE -->
<div class="acc-empty" id="accEmpty">
	<i class="fa fa-folder-open"></i>
	<span>Data tidak ditemukan</span>
</div>

<div class="acc-pagination" id="accPagination"></div>


<script>
const itemsPerPage = 5;
let currentPage = 1;

function renderPagination(total){
	const pageCount = Math.ceil(total / itemsPerPage);
	let html='';
	for(let i=1;i<=pageCount;i++){
		html+=`<button class="${i===currentPage?'active':''}" onclick="goPage(${i})">${i}</button>`;
	}
	document.getElementById('accPagination').innerHTML = html;
}

function goPage(p){
	currentPage = p;
	filterList();
}

function filterList(){
	const keyword = accSearch.value.toLowerCase();
	const items = document.querySelectorAll('.acc-item');
	let visible = [];

	items.forEach(item=>{
		const text = item.querySelector('.acc-search-text').innerText.toLowerCase();
		if(text.includes(keyword)){
			item.style.display='grid';
			visible.push(item);
		}else{
			item.style.display='none';
		}
	});

	visible.forEach((item,i)=>{
		item.style.display = (i>=(currentPage-1)*itemsPerPage && i<currentPage*itemsPerPage)
			? 'grid' : 'none';
	});

	accEmpty.style.display = visible.length ? 'none' : 'block';
	renderPagination(visible.length);
}

accSearch.addEventListener('keyup',()=>{
	currentPage=1;
	filterList();
});

filterList();
</script>

