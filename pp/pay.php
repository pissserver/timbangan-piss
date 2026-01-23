<?php
	session_start();
	include "../connection.php";
	include "components/role_session.php";

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
	
	$check 	= executeScalar("select count(ko_id) from ko where ko_status = 'DiTerima'");
	//Generate KM ID
	$km_id 	= 0;
	$year	= date('Y');
	$km_id 	= executeScalar("select count(km_id) from km where km_year = '".$year."'");
	$km_id 	= $km_id+1;
	$km_id 	= sprintf("%06d", $km_id);
	$km_id  = $year.$km_id;
	$cost 	= executeScalar("select sum(ko_total) from ko where ko_status = 'Diterima'");
?>

<style>
/* ===============================
   DASHBOARD CARD
================================ */
.dashboard-card {
    background: #ffffff;
    border-radius: 18px;
    box-shadow: 0 20px 40px rgba(16,24,40,.08);
    margin-top: 40px;
    overflow: hidden;
}

/* HEADER */
.dashboard-card-header {
    padding: 20px 24px;
    background: #f5f7fb;
    border-bottom: 1px solid #eef2f7;
}

.dashboard-card-header h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 700;
    color: #4f46e5;
}

.dashboard-card-header span {
    font-size: 12px;
    color: #64748b;
}

/* HEADER GREEN */
.dashboard-card-header.green h4 {
    color: #16a34a;
}

/* BODY */
.dashboard-card-body {
    padding: 24px;
}

/* LOADING */
.dashboard-loading {
    background: #f1f5f9;
    padding: 20px;
    border-radius: 14px;
    text-align: center;
    font-weight: 600;
    color: #64748b;
}

/* INFO */
.dashboard-info {
    margin-top: 14px;
    font-size: 12px;
    color: #475569;
    display: flex;
    gap: 8px;
    align-items: center;
}

/* EMPTY */
.dashboard-empty {
    text-align: center;
    padding: 40px 0;
}

.dashboard-empty h4 {
    margin-top: 15px;
    font-weight: 700;
    color: #334155;
}

/* ===== SECTION WRAPPER ===== */
.acc-section{
	background:#fff;
	border-radius:18px;
	box-shadow:0 14px 32px rgba(0,0,0,.08);
	padding:16px 18px;
}

/* HEADER */
.acc-section-header{
	display:flex;
	align-items:center;
	margin-bottom:14px;
	border-bottom:1px solid #e5e7eb;
	padding-bottom:10px;
}
.acc-title{
	display:flex;
	align-items:center;
	gap:8px;
	font-weight:800;
	color:#1f2937;
	font-size:15px;
}
.acc-title i{
	color:#4f46e5;
}

/* VARIAN GREEN */
.acc-section-blue{
	border-top:4px solid #4f46e5;
}

/* VARIAN GREEN */
.acc-section-green{
	border-top:4px solid #16a34a;
}

.acc-title.green i{
	color:#16a34a;
}

/* BODY */
.acc-section-body{
	padding-top:6px;
}

/* LOADING */
.acc-loading{
	display:flex;
	flex-direction:column;
	align-items:center;
	justify-content:center;
	padding:40px 0;
	color:#6b7280;
	cursor:pointer;
}
.acc-loading i{
	font-size:28px;
	margin-bottom:8px;
	color:#4f46e5;
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
	<link rel="icon" href="../img/icon.png">
    <link rel="shortcut icon" href="../img/icon.png" />
    <title>Acc Pembayaran KM Tonase | PISS</title>
	<?php echo headScript(); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="../js/jquery.min.js"></script>
	<script>
		/* hide all elements & show preloader*/
		document.documentElement.className += 'js';

		/* display variabel */
		var alertLoadProcess = "<span class='alert alert-danger' onclick='clearNotif()'><i class='fa fa-spinner fa-spin'></i> Loading...</span>";
    </script>
</head>
<body>
	<div id="wrapper">
	<?php include 'components/sidebar.php'; ?>

        <div id="page-wrapper">
			<br><br>
            <div class="row">
                <div class="col-lg-6">
					<div class="dashboard-card">
						<div class="dashboard-card-header">
							<div>
								<h4>KM Payment</h4>
								<span>Daftar Setoran KM Tonase</span>
							</div>
						</div>

						<div class="dashboard-card-body">
							<?php 
								$query = mysql_query("select * from km where km_status = 'Diajukan' order by km_id DESC");
								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area1">
										<div class="dashboard-loading">
											<i class="fa fa-spinner fa-spin"></i> Memuat data...
										</div>
									</div>
							<?php } else { ?>
								<div class="dashboard-empty">
									<img src="../img/analyze.png" width="220">
									<h4>Belum Ada Pengajuan KM Tonase</h4>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="dashboard-card dashboard-card-green">
						<div class="dashboard-card-header">
							<div>
								<h4 style="color: #16a34a;">KMM Payment</h4>
								<span>Daftar Setoran KMM Mitra</span>
							</div>
						</div>

						<div class="dashboard-card-body">
							<?php 
								$query = mysql_query("select * from km_mitra where km_status = 'Diajukan' order by km_id DESC");

								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area3">
										<div class="dashboard-loading">
											<i class="fa fa-spinner fa-spin"></i> Memuat data...
										</div>
									</div>
							<?php } else { ?>
								<div class="dashboard-empty">
									<img src="../img/analyze.png" width="220">
									<h4>Belum Ada Pengajuan KMM Mitra</h4>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
            </div><br><br>

            <div class="row">
				<div class="col-lg-6">
					<div class="acc-section acc-section-blue">
						<!-- HEADER -->
						<div class="acc-section-header">
							<div class="acc-title">
								<i class="fa fa-file-text-o"></i>
								<span>Data ACC KM Tonase</span>
							</div>
						</div>

						<!-- BODY -->
						<div class="acc-section-body">
							<?php 
								$query = mysql_query("select * from km where km_status='Acc' order by km_id DESC limit 20");

								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area2">
										<div class="acc-loading" onclick="clearNotif()">
											<i class="fa fa-spinner fa-spin"></i>
											<span>Loading data...</span>
										</div>
									</div>
								<?php } else { ?>
									<div class="payment-notfound text-center">
										<img src="../img/analyze.png" width="220">
										<h3><b>Belum Ada ACC KM Tonase</b></h3>
									</div>
								<?php } ?>
						</div>

					</div>
				</div>

				<div class="col-lg-6">
					<div class="acc-section acc-section-green">
						<!-- HEADER -->
						<div class="acc-section-header">
							<div class="acc-title green">
								<i class="fa fa-file-text-o"></i>
								<span>Data ACC KMM Mitra</span>
							</div>
						</div>

						<!-- BODY -->
						<div class="acc-section-body">
							<?php 
								$query = mysql_query("select * from km_mitra where km_status='Acc' order by km_id DESC limit 20");

								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area4">
										<div class="acc-loading" onclick="clearNotif()">
											<i class="fa fa-spinner fa-spin"></i>
											<span>Loading data...</span>
										</div>
									</div>
								<?php } else { ?>
									<div class="payment-notfound text-center">
										<img src="../img/analyze.png" width="220">
										<h3><b>Belum Ada ACC KMM Mitra</b></h3>
									</div>
								<?php } ?>
						</div>

					</div>
				</div>
            </div><br><br>
        </div>
    </div>
	
	<script>
		$(document).ready(function(){
			// Auto Refresh Page
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("pay_table.php",function(){clearNotif();});
			// $('#Area2').load("pay_acc_table.php",function(){clearNotif();});
			$('#Area2').load("pay_table_tonase_acc.php",function(){clearNotif();});
			$('#Area3').load("pay_table_km_mitra.php",function(){clearNotif();});
			$('#Area4').load("pay_table_mitra_acc.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',1000); 
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
		});
		function clearNotif(){$("#notifArea").html("");}
    </script>
    <!-- jQuery -->
    <script src="../js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/sb-admin-2.js"></script>
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