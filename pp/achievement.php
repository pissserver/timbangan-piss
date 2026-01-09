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
	/* ===============================
	CASHIER CARD – PREMIUM UPGRADE
	=============================== */

	.card-cashier {
		background: linear-gradient(180deg, #ffffff, #f9fafb);
		border-radius: 16px;
		padding: 22px 20px 20px;
		margin-bottom: 24px;
		box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
		transition: all .3s ease;
		position: relative;
		overflow: hidden;
	}

	.card-cashier::after {
		content: "";
		position: absolute;
		inset: 0;
		border-radius: 16px;
		box-shadow: inset 0 0 0 1px rgba(0,0,0,.05);
		pointer-events: none;
	}

	.card-cashier:hover {
		transform: translateY(-5px);
		box-shadow: 0 18px 45px rgba(0, 0, 0, 0.15);
	}

	/* ===============================
	TITLE
	=============================== */

	.card-title {
		font-size: 17px;
		font-weight: 600;
		color: #1f2937;
		padding-right: 90px; /* ruang foto */
	}

	/* ===============================
	PROFILE PHOTO (ABSOLUTE)
	=============================== */

	.card-title .img-profile {
		position: absolute;
		top: 14px;
		right: 16px;
	}

	.card-title .img-profile img {
		width: 86px !important;
		height: 86px !important;
		border-radius: 50%;
		object-fit: cover;
		border: 4px solid #ffffff;
		background: #fff;
		box-shadow: 0 12px 25px rgba(0,0,0,.25);
		cursor: zoom-in;
		transition: all .25s ease;
	}

	.card-title .img-profile img:hover {
		transform: scale(1.08);
	}

	/* ===============================
	LEVEL BADGE
	=============================== */

	.level-badge {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		padding: 7px 16px;
		border-radius: 999px;
		font-size: 12px;
		font-weight: 600;
		box-shadow: 0 6px 15px rgba(0,0,0,.15);
	}

	/* LEVEL COLORS */
	.level-basic {
		background: linear-gradient(135deg, #b87333, #cd7f32);
	}

	.level-silver {
		background: linear-gradient(135deg, #e5e7eb, #cbd5e1);
		color: #374151;
	}

	.level-gold {
		background: linear-gradient(135deg, #fde68a, #facc15);
		color: #92400e;
	}

	.level-platinum {
		background: linear-gradient(135deg, #0f172a, #475569);
	}

	/* ===============================
	INFO TEXT
	=============================== */

	.card-cashier > div:nth-of-type(3) {
		font-size: 13px;
		color: #6b7280;
		margin-top: 10px;
	}

	.card-cashier b {
		color: #111827;
	}

	/* ===============================
	FOOTER BUTTON
	=============================== */

	.card-footer-btn {
		margin-top: 18px;
	}

	.card-footer-btn .btn {
		border-radius: 12px;
		padding: 11px 14px;
		font-weight: 600;
		letter-spacing: .3px;
		transition: all .25s ease;
	}

	.card-footer-btn.blue .btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(0, 94, 128, 0.35);
	}

	.card-footer-btn.green .btn:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(0, 128, 0, 0.35);
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
											c.photo,
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
												<div class="img-profile photo-viewer" id="photoContainerProfile">
													<img id="previewPhoto"
													src="<?php echo $data['photo'] ? '../../uploads/' . $data['photo'] : '../../uploads/profile-default.png'; ?>"
													style="width:120px; height:120px; border-radius:50%;">
												</div>
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

											<div class="card-footer-btn blue">
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

				<div class="row" style="margin-top: 30px;">
					<div class="panel-primary">
						<div class="panel-heading" style="border-radius: 10px; padding: 15px 20px; font-size: 18px; background-color: #008000;">
							<h4 class="panel-title">
								<i class="fa fa-trophy"></i>&nbsp; Pencapaian Tim Checker
							</h4>
						</div>
						<div class="panel-body">
							<div class="row" style="margin-top:30px;">
								<?php
									$sql = "
										SELECT 
											c.checker_id,
											c.checker_name, 
											c.photo,
											COUNT(k.ko_id) AS total_ko
											FROM checker c
											JOIN ko k ON k.receiver_id = c.checker_id
											WHERE status_aktif = '1' 
											AND checker_role = 'Checker'
											GROUP BY c.checker_id, c.checker_name
											ORDER BY total_ko DESC
											";

									$query = mysql_query($sql);
									$no = 1;

									while($data = mysql_fetch_array($query)) {

									// LEVEL CHECK
									if ($data['total_ko'] > 1000) {
										$level = "Platinum";  $badgeClass = "level-platinum";
									} elseif ($data['total_ko'] > 500) {
										$level = "Gold";      $badgeClass = "level-gold";
									} elseif ($data['total_ko'] > 250) {
										$level = "Silver";    $badgeClass = "level-silver";
									} else {
										$level = "Basic";     $badgeClass = "level-basic";
									} ?>

									<div class="col-md-4 col-sm-6">
										<div class="card-cashier">					
											<div class="card-title">
												<?php echo $no . ". " . $data['checker_name']; ?>
												<div class="img-profile photo-viewer" id="photoContainerProfile2">
													<img id="previewPhoto2"
													src="<?php echo $data['photo'] ? '../../uploads/' . $data['photo'] : '../../uploads/profile-default.png'; ?>"
													style="width:120px; height:120px; border-radius:50%;">
												</div>
											</div>

											<div style="margin-top:10px;">
												<span class="level-badge <?php echo $badgeClass; ?>">
													<i class="fa fa-shield"></i> 
													<?php echo $level; ?>
												</span>
											</div>

											<div style="margin-top:10px; color:#666;">
												Total Setoran KO:
												<b><?php echo number_format($data['total_ko']); ?></b>
												Setoran
											</div>

											<div class="card-footer-btn green">
												<a href="achievement_detail2.php?id=<?php echo $data['checker_id']; ?>" 
													class="btn btn-sm btn-block" style="background-color: #008000; color:#fff;">
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


		<!-- Viewer JS untuk zoom foto profil -->
		<script>
			document.addEventListener("DOMContentLoaded", function () {

				const containers = document.querySelectorAll('.photo-viewer');

				containers.forEach(container => {
					new Viewer(container, {
						navbar: false,
						toolbar: true,
						title: false,
						zoomRatio: 0.5,
					});
				});

			});
		</script>

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