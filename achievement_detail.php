
<?php
	session_start();
	include "connection.php";

	if($_SESSION['checker']==''){
		header("Location:login.html");
	}

	$checker	= executeScalar("select checker_name from checker where checker_id = '".$_GET['id']."'");
	$role		= executeScalar("select checker_role from checker where checker_id = '".$_GET['id']."'");
	$username	= executeScalar("select checker_code from checker where checker_id = '".$_GET['id']."'");
	$password	= executeScalar("select checker_pass from checker where checker_id = '".$_GET['id']."'");
	$photoUrlChecker	= executeScalar("select photo from checker where checker_id = '".$_GET['id']."'");
	$firstWeighing = executeScalar("SELECT MIN(date_in) FROM weighing WHERE checker_id = '".$_GET['id']."'");

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
?>

<style>
	.profile-card {
		width: 100%;
		background-size: cover;
		background-position: center;
		background-repeat: no-repeat;
		border-radius: 10px;
		padding: 20px 15px;
		box-shadow: 0 3px 7px rgba(0,0,0,0.15);
		text-align: center;
		border: 1px solid #ddd;

		/* Default background */
		background-image: url('img/bg-profile.png');

		/* Smooth transition */
		transition: background-image 1s ease-in-out, opacity 1s ease-in-out;
	}

	.bg2 {
		background-image: url('img/bg-profile2.png') !important;
	}

	.profile-card {
		position: relative;
		overflow: hidden;
	}

	/* Overlay hitam */
	.profile-card::before {
		content: "";
		position: absolute;
		inset: 0;
		background: rgba(0,0,0,0.40); /* 0.35 = gelap halus */
		z-index: 0;
		border-radius: inherit;
	}

	/* Agar isi tetap muncul di atas */
	.profile-card * {
		position: relative;
		z-index: 1;
	}

	.profile-card .img-profile {
		position: relative;
		display: inline-block;
	}

	.profile-card img {
		width: 120px;
		height: 120px;
		border-radius: 50%;
		border: 5px solid #fff;
		object-fit: cover;
		margin-bottom: 15px;
		background: #f0f0f0;
		cursor: pointer;
	}

	.profile-card h3 {
		margin: 0;
		font-weight: bold;
		color: #fff;
	}

	.profile-card p {
		margin: 5px 0;
		color: #fff;
	}

	.profile-card .role-label {
		font-size: 15px;
		background: #3498db;
		color: white;
		padding: 5px 10px;
		border-radius: 20px;
		display: inline-block;
		margin-top: 10px;
	}
	
	/* Badge */
	.progress-container {
		margin-top: 15px;
	}

	.progress {
		height: 18px;
		border-radius: 10px;
	}

	.level-line {
		display: flex;
		justify-content: space-between;
		font-size: 12px;
		color: #555;
		margin-top: 5px;
	}

	/* BOX */
	.achievement-box {
		padding: 25px;
		border-radius: 12px;
		background: #f7f9fc;
		border: 1px solid #d5e3f0;
		box-shadow: 0 4px 12px rgba(0,0,0,0.08);
		margin-bottom: 30px;
		text-align: center;
	}

	/* TITLE */
	.achievement-title {
		font-size: 16px;
		font-weight: 700;
		color: #333;
		margin-bottom: 15px;
		letter-spacing: .5px;
	}

	/* BADGE WRAPPER */
	.achievement-badge-head-wrapper {
		position: absolute;
		top: -5px;
		right: 0px;
		z-index: 5;
	}

	.achievement-badge {
		margin-bottom: 18px;
	}

	/* BADGE STYLE */
	.level-badge {
		display: inline-flex;
		align-items: center;
		padding: 10px 18px;
		border-radius: 30px;
		font-weight: 600;
		font-size: 15px;
		gap: 8px;
		color: white;
		box-shadow: 0 4px 8px rgba(0,0,0,0.25);
		animation: popIn .4s ease;
	}

	/* BASIC */
	.level-badge.basic {
		background: linear-gradient(45deg, #b87333, #cd7f32);
		box-shadow: 0 0 15px #cd7f32aa;
	}

	/* SILVER */
	.level-badge.silver {
		background: linear-gradient(45deg, #c0c0c0, #e0e0e0);
		color: #333;
		box-shadow: 0 0 15px #c0c0c0cc;
	}

	/* GOLD */
	.level-badge.gold {
		background: linear-gradient(45deg, #ffd700, #ffbf00);
		color: #222;
		box-shadow: 0 0 20px #ffcc00aa;
	}

	/* PLATINUM */
	.level-badge.platinum {
		background: linear-gradient(45deg, #6d6d6d, #3d3d3d);
		box-shadow: 0 0 25px #ffffffaa;
	}

	/* TONASE */
	.achievement-tonase {
		margin-top: 5px;
		font-size: 14px;
		color: #444;
	}

	/* ANIMASI */
	@keyframes popIn {
		from { transform: scale(.7); opacity: 0; }
		to   { transform: scale(1); opacity: 1; }
	}

	.level-roadmap {
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 25px 0;
		gap: 10px;
	}

	.road-badge {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
		color: white;
		font-size: 18px;
	}

	.road-badge.basic { background:#cd7f32; }
	.road-badge.silver { background:#c0c0c0; }
	.road-badge.gold { background:#ffcc00; }
	.road-badge.platinum { background:#3d3d3d; }

	.road-line {
		width: 40px;
		height: 4px;
		background:#999;
		border-radius: 10px;
	}

	/* Data Cashier */
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
		<link rel="icon" href="img/logo.png">
		<link rel="shortcut icon" href="img/logo.png" />
		<title>Detail Pencapaian | PISS</title>

		<?php echo headScript(); ?>

		<link rel="stylesheet" href="https://unpkg.com/viewerjs/dist/viewer.css">
		<script src="https://unpkg.com/viewerjs/dist/viewer.js"></script>
	</head>
	<body>
		<div id="wrapper">
		<?php include "components/sidebar.php" ?>

			<div id="page-wrapper">
				<br><br><br><br>
				<div class="row">
					<div class="col-md-12">
						<div class="profile-preview">
							<div class="profile-card">
								<div class="img-profile" id="photoContainer">
									<div class="achievement-badge-head-wrapper">
										<?php
												$idChecker = $_GET['id'];
												$tonase = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE checker_id = '".$idChecker."' AND weighing_sts = 'Lunas'");

												// Tentukan level & batas maksimal
												// LEVEL CHECK
												if ($tonase > 25000) {
													$level = "Platinum";
													$color = "linear-gradient(45deg, #3d3d3d, #666666)";
												} elseif ($tonase > 15000) {
													$level = "Gold";
													$color = "linear-gradient(45deg, #ffd700, #ffbf00)";
												} elseif ($tonase > 10000) {
													$level = "Silver";
													$color = "linear-gradient(45deg, #c0c0c0, #e0e0e0)";
												} else {
													$level = "Basic";
													$color = "linear-gradient(45deg, #b87333, #cd7f32)"; // bronze
												}

												// PROGRESS
												// tonase di bawah 25000 → pakai tonase asli
												// tonase lebih dari 25000 → stop di 25000 (MAX)
												$progressValue = ($tonase > 25000) ? 25000 : $tonase;

												// Biar progress bar terlihat proporsional, ubah ke persen
												$progressPercent = ($progressValue / 25000) * 100;

											?>
										<?php if ($level == "Basic") { ?>
											<div class="level-badge basic">
												<i class="fa fa-shield"></i>
											</div>
										<?php } ?>

										<?php if ($level == "Silver") { ?>
											<div class="level-badge silver">
												<i class="fa fa-shield"></i>
											</div>
										<?php } ?>

										<?php if ($level == "Gold") { ?>
											<div class="level-badge gold">
												<i class="fa fa-shield"></i>
											</div>
										<?php } ?>

										<?php if ($level == "Platinum") { ?>
											<div class="level-badge platinum">
												<i class="fa fa-diamond"></i>
											</div>
										<?php } ?>
									</div>
								
								
									<img id="previewPhoto"
									src="<?php echo $photoUrlChecker ? '../../uploads/' . $photoUrlChecker : '../../uploads/profile-default.png'; ?>"
									style="width:120px; height:120px; border-radius:50%;">
								</div>
								
								<h3 id="previewName"><?php echo $checker; ?></h3>
								<p id="previewUsername">@<?php echo $username; ?></p>
								<span class="role-label" id="previewRole"><?php echo $role; ?></span>
							</div>
						</div>
					</div>
				</div>

				<div class="row" style="margin-top: 20px;">
					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">
									<i class="fa fa-trophy"></i>&nbsp; Pencapaian Keseluruhan
								</h4>
							</div>
							<div class="panel-body">
								<div class="panel-group" id="accordionChangelog" style="padding: 5px 15px 0px;">
											<div class="row">
												<div style="margin: 0px 15px;" class="alert alert-info" role="alert">
													<i class="fa fa-warning" style="display: inline-block;"></i>&nbsp;
													<p style="font-size: 14px; display: inline-block">
														Data dari <?php echo date("d M Y", strtotime($firstWeighing)); ?> - Sekarang
													</p>
												</div><br>
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Timbang Kendaraan
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$weighingCount = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE checker_id = '".$idChecker."'");
															$weighingCount = number_format($weighingCount, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $weighingCount . " Kendaraan"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Tonase Terbayar
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$tonasePayCount = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE checker_id = '".$idChecker."' AND weighing_sts = 'Lunas'");
															$tonasePayCount = number_format($tonasePayCount, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $tonasePayCount . " Nota Lunas"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Mitra Terbayar
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$mitraPayLunas = executeScalar("SELECT COUNT(weighing_id) FROM weighing_mitra WHERE checker_id = '".$idChecker."' AND weighing_sts = 'Lunas'");
															$mitraPayLunas = number_format($mitraPayLunas, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $mitraPayLunas . " Nota Lunas"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Setoran Laporan KO
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$reportKO = executeScalar("SELECT COUNT(ko_id) FROM ko WHERE cashier_id = '".$idChecker."'");
															$reportKO = number_format($reportKO, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $reportKO . " Setoran"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Setoran Laporan KO Mitra
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$reportKOM = executeScalar("SELECT COUNT(ko_id) FROM ko_mitra WHERE cashier_id = '".$idChecker."'");
															$reportKOM = number_format($reportKOM, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $reportKOM . " Setoran"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Revisi
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$revision = executeScalar("SELECT COUNT(revision_id) FROM revision WHERE applicant = '".$idChecker."'");
															$revision = number_format($revision, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $revision . " Pengajuan Revisi"; ?>" disabled>
													</div>
												</div>
											</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">
									<i class="fa fa-trophy"></i>&nbsp; Pencapaian Bulanan 
									<div class="wrap-month" style="display: inline-block; color: #f1ea18ff; font-weight: bold;">
										<?php echo date("F Y"); ?>
									</div>
								</h4>
							</div>
							<div class="panel-body">
								<div class="panel-group" id="accordionChangelog" style="padding: 5px 15px 0px;">
											<div class="row">
												<div style="margin: 0px 15px;" class="alert alert-info" role="alert">
													<i class="fa fa-warning" style="display: inline-block;"></i>&nbsp;
													<p style="font-size: 14px; display: inline-block">
														Data ini reset setiap bulan
													</p>
												</div><br>
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Timbang Kendaraan
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$monthYearNow = date("Y-m");
															$weighingCount = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE date_in LIKE '".$monthYearNow."%' AND checker_id = '".$idChecker."'");
															$weighingCount = number_format($weighingCount, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $weighingCount . " Kendaraan"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Tonase Terbayar
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$monthYearNow = date("Y-m");
															$tonasePayCount = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE date_pay LIKE '".$monthYearNow."%' AND checker_id = '".$idChecker."' AND weighing_sts = 'Lunas'");
															$tonasePayCount = number_format($tonasePayCount, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $tonasePayCount . " Nota Lunas"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Mitra Terbayar
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$monthYearNow = date("Y-m");
															$mitraPayLunas = executeScalar("SELECT COUNT(weighing_id) FROM weighing_mitra WHERE date_pay LIKE '".$monthYearNow."%' AND checker_id = '".$idChecker."' AND weighing_sts = 'Lunas'");
															$mitraPayLunas = number_format($mitraPayLunas, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $mitraPayLunas . " Nota Lunas"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Setoran Laporan KO
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$monthYearNow = date("Y-m");
															$reportKO = executeScalar("SELECT COUNT(ko_id) FROM ko WHERE ko_date LIKE '".$monthYearNow."%' AND cashier_id = '".$idChecker."'");
															$reportKO = number_format($reportKO, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $reportKO . " Setoran"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Setoran Laporan KO Mitra
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$monthYearNow = date("Y-m");
															$reportKOM = executeScalar("SELECT COUNT(ko_id) FROM ko_mitra WHERE ko_date LIKE '".$monthYearNow."%' AND cashier_id = '".$idChecker."'");
															$reportKOM = number_format($reportKOM, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $reportKOM . " Setoran"; ?>" disabled>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label for="name">
															Revisi
														</label>
														<?php 
															$idChecker = $_GET['id'];
															$monthYearNow = date("Y-m");
															$revision = executeScalar("SELECT COUNT(revision_id) FROM revision WHERE date LIKE '".$monthYearNow."%' AND applicant = '".$idChecker."'");
															$revision = number_format($revision, 0, ',', '.');
														?>
														<input type="text" class="form-control" id="name" name="name" value="<?php echo $revision . " Pengajuan Revisi"; ?>" disabled>
													</div>
												</div>
											</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">
									<i class="fa fa-trophy"></i>&nbsp; Info Pencapaian
								</h4>
							</div>
							<div class="panel-body">
								<div class="panel-group" id="accordionChangelog" style="padding: 5px 15px 0px;">
									<div class="achievement-box">
										<?php
											$idChecker = $_GET['id'];
											$tonase = executeScalar("SELECT COUNT(weighing_id) FROM weighing WHERE checker_id = '".$idChecker."' AND weighing_sts = 'Lunas'");

											// Tentukan level & batas maksimal
											// LEVEL CHECK
											if ($tonase > 25000) {
												$level = "Platinum";
												$color = "linear-gradient(45deg, #3d3d3d, #666666)";
											} elseif ($tonase > 15000) {
												$level = "Gold";
												$color = "linear-gradient(45deg, #ffd700, #ffbf00)";
											} elseif ($tonase > 10000) {
												$level = "Silver";
												$color = "linear-gradient(45deg, #c0c0c0, #e0e0e0)";
											} else {
												$level = "Basic";
												$color = "linear-gradient(45deg, #b87333, #cd7f32)"; // bronze
											}

											// PROGRESS
											// tonase di bawah 25000 → pakai tonase asli
											// tonase lebih dari 25000 → stop di 25000 (MAX)
											$progressValue = ($tonase > 25000) ? 25000 : $tonase;

											// Biar progress bar terlihat proporsional, ubah ke persen
											$progressPercent = ($progressValue / 25000) * 100;

										?>

										<!-- BADGE LEVEL SAAT INI -->
										<div class="achievement-box">
											<div class="achievement-title">Level Saat Ini</div>
											<div class="achievement-badge">
												<?php if ($level == "Basic") { ?>
													<div class="level-badge basic">
														<i class="fa fa-shield"></i>
														<span>Basic</span>
													</div>
												<?php } ?>

												<?php if ($level == "Silver") { ?>
													<div class="level-badge silver">
														<i class="fa fa-shield"></i>
														<span>Silver</span>
													</div>
												<?php } ?>

												<?php if ($level == "Gold") { ?>
													<div class="level-badge gold">
														<i class="fa fa-shield"></i>
														<span>Gold</span>
													</div>
												<?php } ?>

												<?php if ($level == "Platinum") { ?>
													<div class="level-badge platinum">
														<i class="fa fa-diamond"></i>
														<span>Platinum</span>
													</div>
												<?php } ?>
											</div>

											<!-- === 4 BADGE ROADMAP === -->
											<div class="level-roadmap">
												<div class="road-badge basic"><i class="fa fa-shield"></i></div>
												<div class="road-line"></div>

												<div class="road-badge silver"><i class="fa fa-shield"></i></div>
												<div class="road-line"></div>

												<div class="road-badge gold"><i class="fa fa-shield"></i></div>
												<div class="road-line"></div>

												<div class="road-badge platinum"><i class="fa fa-diamond"></i></div>
											</div>

											<div class="achievement-tonase">
												Total Tonase Terbayar:  
												<b><?php echo number_format($tonase,0,',','.'); ?></b> Nota Lunas
											</div>

										</div>

										<!-- PROGRESS BAR -->
										<div class="progress-container">
												<div class="progress" 
												style="height:20px; background:#eaeaea; border-radius: 10px;">
												<div class="progress-bar"
													style="width: <?php echo $progressPercent; ?>%; 
															background: <?php echo $color; ?>; 
															border-bottom:none; 
															box-shadow:none;
															transition: width 1s ease;">
													<?php echo number_format($tonase); ?>
												</div>
											</div>
										</div>

											<!-- LABEL BATAS -->
											<div class="level-line">
												<span>Basic (0)</span>
												<span>Silver (10.000)</span>
												<span>Gold (15.000)</span>
												<span>Platinum (25.000+)</span>
											</div>
										</div>
									</div>

									<div class="row" style="margin-top: 20px; padding: 0 15px;">
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
											}
										?>
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
															<i class="fa fa-eye"></i> Lihat Detail
														</a>
													</div>

												</div>
											</div>
										<?php 
											$no++; 
										} 
										?>
									</div>
								</div>
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

				const container = document.getElementById('photoContainer');

				const viewer = new Viewer(container, {
					navbar: false,
					toolbar: true,
					title: false,
					zoomRatio: 0.5,
				});

			});
		</script>


		<!-- Script Background -->
		<script>
			setInterval(() => {
				const card = document.querySelector('.profile-card');
				card.classList.toggle('bg2');  // toggle ke bg2 atau kembali ke bg1
			}, 5000); // 5000ms = 5 detik
		</script>



		<!-- Cropper JS -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
		<!-- jQuery -->
		<script src="js/jquery.js"></script>
		<!-- Bootstrap Core JavaScript -->
		<script src="js/bootstrap.min.js"></script>
		<!-- Metis Menu Plugin JavaScript -->
		<script src="js/plugins/metisMenu/metisMenu.min.js"></script>
		<!-- Custom Theme JavaScript -->
		<script src="js/sb-admin-2.js"></script>
		<script>
			function togglePassword(id, btn) {
				const input = document.getElementById(id);
				const icon = btn.querySelector('i');

				if (input.type === "password") {
					input.type = "text";
					icon.classList.remove("fa-eye-slash");
					icon.classList.add("fa-eye");
				} else {
					input.type = "password";
					icon.classList.remove("fa-eye");
					icon.classList.add("fa-eye-slash");
				}
			}

			document.getElementById('photo').addEventListener('change', function () {
				var fileName = this.files[0].name;
				document.getElementById('fileName').innerText = "File dipilih: " + fileName;
			});
		</script>
		<!-- Sweet Alert -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
		<?php
			// Menampilkan SweetAlert jika session success ada dan bernilai true
			if (isset($_SESSION['success'])) {
				$title = $_SESSION['title'];
				$message = $_SESSION['message'];
				$icon = $_SESSION['success'] ? 'success' : 'error';
				echo "<script>
					Swal.fire({
						title: '$title',
						text: '$message',
						icon: '$icon'
					});
				</script>";
				unset($_SESSION['success']);
				unset($_SESSION['message']);
			}
		?>
	</body>
</html>