<?php
	session_start();
	include "../connection.php";
	include "components/role_session.php";

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
?>

<style>
	.card-cashier {
		border-radius: 10px;
		background: #ffffff;
		padding: 20px;
		margin-bottom: 20px;
		box-shadow: 0px 3px 12px rgba(0,0,0,0.2);
		transition: 0.2s;
	}
	
	.card-cashier:hover {
		transform: translateY(-3px);
		box-shadow: 0px 5px 18px rgba(0,0,0,0.15);
	}

	.card-title {
		font-size: 18px;
		font-weight: bold;
		color: #333;
	}

	.level-badge {
		display: inline-block;
		padding: 8px 14px;
		border-radius: 20px;
		color: #fff;
		font-size: 13px;
		font-weight: bold;
	}

	.level-basic     { background: linear-gradient(45deg, #b87333, #cd7f32); }
	.level-silver    { background: linear-gradient(45deg, #c0c0c0, #e0e0e0); color:#333; }
	.level-gold      { background: linear-gradient(45deg, #ffd700, #ffbf00); color:#333; }
	.level-platinum  { background: linear-gradient(45deg, #3d3d3d, #666666); }

	.card-footer-btn {
		margin-top: 15px;
	}
</style>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<link rel="icon" href="../img/logo.png">
		<link rel="shortcut icon" href="../img/logo.png" />
		<title>Pencapaian | PISS</title>

		<?php echo headScript(); ?>
	</head>
	<body>
		<div id="wrapper">
		<?php include "components/sidebar.php" ?>

			<div id="page-wrapper">
				<br><br><br><br>
				<div class="row">
					<div class="panel-primary">
						<div class="panel-heading" style="border-radius: 10px; padding: 15px 20px; font-size: 18px;">
							<h4 class="panel-title">
								<i class="fa fa-trophy"></i>&nbsp; Pencapaian Tim Kasir
							</h4>
						</div>
						<div class="panel-body">
							<div class="row" style="margin-top:30px;">
								<?php
									$sql = "
										SELECT 
											c.checker_id,
											c.checker_name, 
											COUNT(w.weighing_id) AS total_tonase
											FROM checker c
											JOIN weighing w ON w.checker_id = c.checker_id
											WHERE status_aktif = '1' 
											AND checker_role = 'Cashier' 
											AND weighing_sts = 'Lunas'
											GROUP BY c.checker_id, c.checker_name
											ORDER BY total_tonase DESC
											";

									$query = mysql_query($sql);
									$no = 1;

									while($data = mysql_fetch_array($query)) {

									// LEVEL CHECK
									if ($data['total_tonase'] > 25000) {
										$level = "Platinum";  $badgeClass = "level-platinum";
									} elseif ($data['total_tonase'] > 15000) {
										$level = "Gold";      $badgeClass = "level-gold";
									} elseif ($data['total_tonase'] > 10000) {
										$level = "Silver";    $badgeClass = "level-silver";
									} else {
										$level = "Basic";     $badgeClass = "level-basic";
									} ?>

									<div class="col-md-4 col-sm-6">
										<div class="card-cashier">					
											<div class="card-title">
												<?php echo $no . ". " . $data['checker_name']; ?>
											</div>

											<div style="margin-top:10px;">
												<span class="level-badge <?php echo $badgeClass; ?>">
													<i class="fa fa-shield"></i> 
													<?php echo $level; ?>
												</span>
											</div>

											<div style="margin-top:10px; color:#666;">
												Total Tonase Terbayar:
												<b><?php echo number_format($data['total_tonase']); ?></b>
												Nota Lunas
											</div>

											<div style="margin-top:10px; color:#666;">
												Dengan Revisi:
												<b>
													<?php 
														$revision = executeScalar("SELECT COUNT(revision_id) FROM revision WHERE applicant = '".$data['checker_id']."'");
														echo number_format($revision); 
													?>
												</b>
												Kali
											</div>

											<div class="card-footer-btn">
												<a href="achievement_detail.php?id=<?php echo $data['checker_id']; ?>" 
													class="btn btn-primary btn-sm btn-block">
													<i class="fa fa-eye"></i>&nbsp; Lihat Detail
												</a>
											</div>
										</div>
									</div>
									<?php 
									$no++; 
									} ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End Main Content -->
		</div>

		<!-- jQuery -->
		<script src="../js/jquery.js"></script>
		<!-- Bootstrap Core JavaScript -->
		<script src="../js/bootstrap.min.js"></script>
		<!-- Metis Menu Plugin JavaScript -->
		<script src="../js/plugins/metisMenu/metisMenu.min.js"></script>
		<!-- Custom Theme JavaScript -->
		<script src="../js/sb-admin-2.js"></script>
		<script>
			$(document).ready(function(){
				//Modal Password
				$('#modalPassword').on('shown.bs.modal', function (e) {
					$("#notifArea").html(alertLoadProcess);
					$("#data_password").load("password.php",function(){clearNotif();});
				})
			});
		</script>
	</body>
</html>