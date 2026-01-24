<?php
	include "../connection.php";
?>

<style>
	.payment-card-wrapper{
		display:flex;
		flex-direction:column;
		gap:14px;
	}

	/* CARD */
	.payment-card{
		display:flex;
		align-items:center;
		justify-content:space-between;
		background:#ffffff;
		border-radius:18px;
		padding:16px 22px;
		box-shadow:0 14px 32px rgba(0,0,0,.08);
		transition:.25s ease;
	}
	.payment-card:hover{
		transform:translateY(-2px);
		box-shadow:0 20px 40px rgba(0,0,0,.12);
	}

	/* LEFT */
	.payment-card-left{
		min-width:170px;
	}
	.payment-id{
		margin:0;
		font-weight:800;
		color:#16a34a;
	}
	.payment-date{
		font-size:12px;
		color:#64748b;
	}

	/* CENTER */
	.payment-card-center{
		display:grid;
		grid-template-columns: 150px auto;
		align-items:center;
		gap:30px;
	}

	.payment-user{
		display:flex;
		align-items:center;
		gap:10px;
	}
	.payment-user i{
		background:#dcfce7;
		color:#16a34a;
		padding:10px;
		border-radius:50%;
	}
	.payment-user span{
		font-size:12px;
		color:#64748b;
	}

	/* STATUS */
	.payment-status{
		padding:6px 14px;
		border-radius:999px;
		font-size:12px;
		font-weight:600;
		display:flex;
		align-items:center;
		gap:6px;
	}
	.payment-status.waiting{
		background:#fff7ed;
		color:#c2410c;
	}

	/* RIGHT */
	.payment-card-right{
		text-align:right;
		min-width:150px;
	}
	.payment-amount{
		font-weight:800;
		font-size:16px;
		color:#15803d;
		margin-bottom:8px;
	}

	/* BUTTON ACC */
	.btn-acc{
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
	.btn-acc:hover{
		transform:translateY(-1px);
		box-shadow:0 10px 22px rgba(34,197,94,.45);
		color:#fff;
	}
</style>


<div class="payment-card-wrapper">

<?php
	$no = 0;
	$query = mysql_query("select * from km_mitra where km_status = 'Diajukan' order by km_id DESC");

	while($data = mysql_fetch_array($query)) {

		$cashier_id = executeScalar("select cashier_id from ko_mitra where km_id = '".$data['km_id']."'");
		$cashier    = executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");
		$no++;
?>

	<div class="payment-card payment-card-mitra">

		<!-- LEFT -->
		<div class="payment-card-left">
			<h4 class="payment-id">KMM-<?php echo $data['km_id']; ?></h4>
			<span class="payment-date">
				<i class="fa fa-calendar"></i>
				<?php echo date("d M Y", strtotime($data['km_date'])); ?>
			</span>
		</div>

		<!-- CENTER -->
		<div class="payment-card-center">
			<div class="payment-user">
				<i class="fa fa-user"></i>
				<div>
					<strong><?php echo $cashier; ?></strong><br>
					<span>Kasir</span>
				</div>
			</div>

			<div class="payment-status waiting">
				<i class="fa fa-spinner fa-spin"></i> Menunggu ACC
			</div>
		</div>

		<!-- RIGHT -->
		<div class="payment-card-right">
			<div class="payment-amount">
				<?php echo format_rupiah($data['km_total']); ?>
			</div>

			<a href="pay_action.php?task=acc_mitra&id=<?php echo $data['km_id']; ?>" class="btn-acc" style="text-decoration: none;">
				<i class="fa fa-check"></i> ACC
			</a>
		</div>

	</div>

<?php } ?>

</div>
