<?php
	include "../connection.php";
?>

<style>
	.kmc-wrapper{
		display:flex;
		flex-direction:column;
		gap:14px;
	}

	/* CARD */
	.kmc-card{
		display:flex;
		align-items:center;
		justify-content:space-between;
		background:#ffffff;
		border-radius:18px;
		padding:16px 22px;
		box-shadow:0 14px 32px rgba(0,0,0,.08);
		transition:.25s ease;
	}
	.kmc-card:hover{
		transform:translateY(-2px);
		box-shadow:0 20px 40px rgba(0,0,0,.12);
	}

	/* LEFT */
	.kmc-left{
		min-width:170px;
	}
	.kmc-id{
		margin:0;
		font-weight:800;
		color:#4f46e5;
	}
	.kmc-date{
		font-size:12px;
		color:#64748b;
	}

	/* CENTER */
	.kmc-center{
		display:grid;
		grid-template-columns: 150px auto;
		align-items:center;
		gap:30px;
	}


	.kmc-user{
		display:flex;
		align-items:center;
		gap:10px;
	}
	.kmc-user i{
		background:#eef2ff;
		color:#4f46e5;
		padding:10px;
		border-radius:50%;
	}
	.kmc-user span{
		font-size:12px;
		color:#64748b;
	}

	/* STATUS */
	.kmc-status{
		padding:6px 14px;
		border-radius:999px;
		font-size:12px;
		font-weight:600;
		display:flex;
		align-items:center;
		gap:6px;
	}
	.kmc-status-wait{
		background:#fff7ed;
		color:#c2410c;
	}

	/* RIGHT */
	.kmc-right{
		text-align:right;
		min-width:150px;
	}
	.kmc-amount{
		font-weight:800;
		font-size:16px;
		color:#15803d;
		margin-bottom:8px;
	}

	/* BUTTON ACC */
	.kmc-btn-acc{
		display:inline-flex;
		align-items:center;
		gap:6px;
		padding:7px 18px;
		font-size:12px;
		font-weight:700;
		border-radius:999px;
		background:linear-gradient(135deg,#4f46e5,#6366f1);
		color:#fff;
		text-decoration:none;
		box-shadow:0 8px 18px rgba(79,70,229,.35);
		transition:.25s ease;
	}
	.kmc-btn-acc:hover{
		transform:translateY(-1px);
		box-shadow:0 8px 18px rgba(79,70,229,.45);
		color:#fff;
	}
</style>


<div class="kmc-wrapper">

<?php
	$no = 0;
	$query = mysql_query("select * from km where km_status = 'Diajukan' order by km_id DESC");

	while($data = mysql_fetch_array($query)) {

		$cashier_id = executeScalar("select cashier_id from ko where km_id = '".$data['km_id']."'");
		$cashier    = executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");
		$no++;
?>

	<div class="kmc-card">

		<!-- LEFT -->
		<div class="kmc-left">
			<h4 class="kmc-id">KMM-<?php echo $data['km_id']; ?></h4>
			<span class="kmc-date">
				<i class="fa fa-calendar"></i>
				<?php echo date("d M Y", strtotime($data['km_date'])); ?>
			</span>
		</div>

		<!-- CENTER -->
		<div class="kmc-center">
			<div class="kmc-user">
				<i class="fa fa-user"></i>
				<div>
					<strong><?php echo $cashier; ?></strong><br>
					<span>Kasir</span>
				</div>
			</div>

			<div class="kmc-status kmc-status-wait">
				<i class="fa fa-spinner fa-spin"></i> Menunggu ACC
			</div>
		</div>

		<!-- RIGHT -->
		<div class="kmc-right">
			<div class="kmc-amount">
				<?php echo format_rupiah($data['km_total']); ?>
			</div>

			<a href="pay_action.php?task=acc&id=<?php echo $data['km_id']; ?>" class="kmc-btn-acc" style="text-decoration: none;">
				<i class="fa fa-check"></i> ACC
			</a>
		</div>

	</div>

<?php } ?>

</div>
