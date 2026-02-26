<?php
	include "../connection.php";

	$limit=5;
	$page=isset($_GET['page'])?(int)$_GET['page']:1;
	if($page<1)$page=1;
	$offset=($page-1)*$limit;
	$self=$_SERVER['PHP_SELF'];

	$totalQuery=mysql_query("select count(*) total from km_mitra where km_status='Diajukan'");
	$totalData=mysql_fetch_array($totalQuery);
	$totalPage=ceil($totalData['total']/$limit);

	$query=mysql_query("select * from km_mitra where km_status='Diajukan' order by km_id DESC limit $offset,$limit");
?>

<style>
	.payment-card-wrapper-mitra{
		display:flex;
		flex-direction:column;
		gap:14px;
	}
	.payment-card-mitra{
		display:flex;
		align-items:center;
		justify-content:space-between;
		background:#ffffff;
		border-radius:18px;
		padding:16px 22px;
		box-shadow:0 14px 32px rgba(0,0,0,.08);
		transition:.25s ease;
	}
	.payment-card-mitra:hover{
		transform:translateY(-2px);
		box-shadow:0 20px 40px rgba(0,0,0,.12);
	}
	.payment-card-left-mitra{
		min-width:170px;
	}
	.payment-id-mitra{
		margin:0;
		font-weight:800;
		color:#16a34a;
	}
	.payment-date-mitra{
		font-size:12px;
		color:#64748b;
	}
	.payment-card-center-mitra{
		display:grid;
		grid-template-columns:150px auto;
		align-items:center;
		gap:30px;
	}
	.payment-user-mitra{
		display:flex;
		align-items:center;
		gap:10px;
	}
	.payment-user-mitra i{
		background:#dcfce7;
		color:#16a34a;
		padding:10px;
		border-radius:50%;
	}
	.payment-user-mitra span{
		font-size:12px;
		color:#64748b;
	}
	.payment-status-mitra{
		padding:6px 14px;
		border-radius:999px;
		font-size:12px;
		font-weight:600;
		display:flex;
		align-items:center;
		gap:6px;
	}
	.payment-status-mitra.waiting{
		background:#fff7ed;
		color:#c2410c;
	}
	.payment-card-right-mitra{
		text-align:right;
		min-width:150px;
	}
	.payment-amount-mitra{
		font-weight:800;
		font-size:16px;
		color:#15803d;
		margin-bottom:8px;
	}
	.btn-acc-mitra{
		display:inline-flex;
		align-items:center;
		gap:6px;
		padding:7px 18px;
		font-size:12px;
		font-weight:700;
		border-radius:999px;
		background:linear-gradient(135deg,#22c55e,#16a34a);
		color:#fff;
		text-decoration:none;
		box-shadow:0 6px 16px rgba(34,197,94,.35);
		transition:.25s ease;
	}
	.btn-acc-mitra:hover{
		transform:translateY(-1px);
		box-shadow:0 10px 22px rgba(34,197,94,.45);
		color:#fff;
	}
	.payment-pagination-mitra{
		display:flex;
		justify-content:center;
		gap:6px;
		margin-top:20px;
	}
	.payment-pagination-mitra button{
		padding:6px 12px;
		border:none;
		background:#dcfce7;
		color:#16a34a;
		cursor:pointer;
		border-radius:6px;
		font-weight:600;
	}
	.payment-pagination-mitra button.active{
		background:#16a34a;
		color:#fff;
	}
	.payment-pagination-mitra button.nav{
		background:#16a34a;
		color:#fff;
	}
	.payment-pagination-mitra span{
		padding:6px 10px;
	}
</style>

<div id="payment-area-mitra">
<div class="payment-card-wrapper-mitra">
<?php
	while($data=mysql_fetch_array($query)){
		$cashier_id=executeScalar("select cashier_id from ko_mitra where km_id='".$data['km_id']."'");
		$cashier=executeScalar("select checker_name from checker where checker_id='".$cashier_id."'");
?>
	<div class="payment-card-mitra">
		<div class="payment-card-left-mitra">
			<h4 class="payment-id-mitra">KMM-<?php echo $data['km_id']; ?></h4>
			<span class="payment-date-mitra">
				<i class="fa fa-calendar"></i>
				<?php echo date("d M Y",strtotime($data['km_date'])); ?>
			</span>
		</div>
		<div class="payment-card-center-mitra">
			<div class="payment-user-mitra">
				<i class="fa fa-user"></i>
				<div>
					<strong><?php echo $cashier; ?></strong><br>
					<span>Kasir</span>
				</div>
			</div>
			<div class="payment-status-mitra waiting">
				<i class="fa fa-spinner fa-spin"></i>Menunggu ACC
			</div>
		</div>
		<div class="payment-card-right-mitra">
			<div class="payment-amount-mitra">
				<?php echo format_rupiah($data['km_total']); ?>
			</div>
			<a href="pay_action.php?task=acc_mitra&id=<?php echo $data['km_id']; ?>" class="btn-acc-mitra" style="text-decoration:none;">
				<i class="fa fa-check"></i>ACC
			</a>
		</div>
	</div>
<?php } ?>
</div>

<div class="payment-pagination-mitra">
<?php if($page>1){ ?>
<button class="nav" onclick="loadPageMitra(<?php echo $page-1; ?>)">Previous</button>
<?php } ?>

<?php
	$start=max(1,$page-1);
	$end=min($totalPage,$page+1);

	if($start>1){
		echo "<button onclick='loadPageMitra(1)'>1</button>";
		if($start>2)echo "<span>...</span>";
	}

	for($i=$start;$i<=$end;$i++){
		$class=($i==$page)?"active":"";
		echo "<button onclick='loadPageMitra($i)' class='$class'>$i</button>";
	}

	if($end<$totalPage){
		if($end<$totalPage-1)echo "<span>...</span>";
		echo "<button onclick='loadPageMitra($totalPage)'>$totalPage</button>";
	}
?>

<?php if($page<$totalPage){ ?>
<button class="nav" onclick="loadPageMitra(<?php echo $page+1; ?>)">Next</button>
<?php } ?>
</div>
</div>

<script>
function loadPageMitra(page){
	var xhr=new XMLHttpRequest();
	xhr.open("GET","<?php echo $self; ?>?page="+page,true);
	xhr.onreadystatechange=function(){
		if(xhr.readyState==4&&xhr.status==200){
			var parser=new DOMParser();
			var doc=parser.parseFromString(xhr.responseText,"text/html");
			document.getElementById("payment-area-mitra").innerHTML=
			doc.getElementById("payment-area-mitra").innerHTML;
		}
	};
	xhr.send();
}
</script>