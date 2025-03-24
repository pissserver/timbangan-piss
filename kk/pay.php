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
            <div class="row">
                <div class="col-lg-6">
					<br><br><br><br>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Data Pengajuan KM Tonase
                        </div>
                        <div class="panel-body">
							<?php 
								$query 		= mysql_query("select * from km where km_status = 'Diajukan' order by km_id DESC");

								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area1">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
									<div class="alert alert-warning" role="alert">
											<table>
												<tr>
													<td style="font-size:20px; padding-right: 10px;">
														<i class="fa fa-info"></i> 
													</td>
													<td>
														<p style="font-size: 12px; padding-top:10px;"> 
															Acc data KM Tonase untuk validasi data
														</p>
													</td>
												</tr>
											</table>
										</div>
								<?php } else { ?>
									<div class="payment-notfound text-center">
										<img src="../img/analyze.png" alt="Image Payment Not Found" width="250px">
										<h3><b>Belum Ada Pengajuan KM Tonase</b></h3>
									</div>
								<?php }
							?>
						</div>
                    </div>
                </div>

				<div class="col-lg-6">
					<br><br><br><br>
					<div class="panel panel-primary">
						<div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Data Pengajuan KMM Mitra
                        </div>
                        <div class="panel-body">
							<?php 
								$query 		= mysql_query("select * from km_mitra where km_status = 'Diajukan' order by km_id DESC");

								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area3">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
									<div class="alert alert-warning" role="alert">
											<table>
												<tr>
													<td style="font-size:20px; padding-right: 10px;">
														<i class="fa fa-info"></i> 
													</td>
													<td>
														<p style="font-size: 12px; padding-top:10px;"> 
															Acc KMM Mitra untuk validasi data
														</p>
													</td>
												</tr>
											</table>
										</div>
								<?php } else { ?>
									<div class="payment-notfound text-center">
										<img src="../img/analyze.png" alt="Image Payment Not Found" width="250px">
										<h3><b>Belum Ada Pengajuan KM Tonase</b></h3>
									</div>
								<?php }
							?>
						</div>
					</div>
				</div>
            </div>

            <div class="row">
				<div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Data ACC KM Tonase
                        </div>
                        <div class="panel-body">
							<?php 
								$query 		= mysql_query("select * from km where km_status = 'Acc' order by km_id DESC limit 20");

								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area2">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
								<?php } else { ?>
									<div class="payment-notfound text-center">
										<img src="../img/analyze.png" alt="Image Payment Not Found" width="250px">
										<h3><b>Belum Ada ACC KM Tonase</b></h3>
									</div>
								<?php }
							?>
						</div>
                    </div>
				</div>

				<div class="col-lg-6">
					<div class="panel panel-primary">
						<div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Data ACC KMM Mitra
                        </div>
                        <div class="panel-body">
							<?php 
								$query 		= mysql_query("select * from km_mitra where km_status = 'Acc' order by km_id DESC limit 20");

								if (mysql_num_rows($query) != 0) { ?>
									<div id="Area4">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
								<?php } else { ?>
									<div class="payment-notfound text-center">
										<img src="../img/analyze.png" alt="Image Payment Not Found" width="250px">
										<h3><b>Belum Ada ACC KMM Mitra</b></h3>
									</div>
								<?php }
							?>
						</div>
                    </div>
				</div>
            </div>
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