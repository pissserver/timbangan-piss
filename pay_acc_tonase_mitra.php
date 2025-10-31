<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
	
	$cashier = executeScalar("select checker_id from checker where checker_code = '".$_SESSION['checker']."'");
	$checker = executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	$check 	= executeScalar("select count(ko_id) from ko where cashier_id = '".$cashier."' and ko_status = 'Diterima'");
	$check_mitra = executeScalar("select count(ko_id) from ko_mitra where cashier_id = '".$cashier."' and ko_status = 'Diterima'");

	//Generate KM ID Tonase
	$km_id 	= 0;
	$year	= date('Y');
	$km_id 	= executeScalar("select count(km_id) from km where km_year = '".$year."'");
	$km_id 	= $km_id + 1;
	$km_id 	= sprintf("%06d", $km_id);
	$km_id  = $year.$km_id;
	$cost 	= executeScalar("select sum(ko_total) from ko where cashier_id = '".$cashier."' and ko_status = 'Diterima'");

	// Genarate KMM ID Mitra
	$km_id_mitra 	= 0;
	$km_id_mitra 	= executeScalar("select count(km_id) from km_mitra where km_year = '".$year."'");
	$km_id_mitra 	= $km_id_mitra + 1;
	$km_id_mitra 	= sprintf("%06d", $km_id_mitra);
	$km_id_mitra  = $year.$km_id_mitra;
	$cost_mitra 	= executeScalar("select sum(ko_total) from ko_mitra where cashier_id = '".$cashier."' and ko_status = 'Diterima'");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="icon" href="img/icon.png">
    <link rel="shortcut icon" href="img/icon.png" />
    <title>Setoran KO | PISS</title>

    <?php echo headScript(); ?>
	
	<!-- jquery -->
	<script src="js/jquery.js"></script>
	<!-- input number -->
	<link href="touchpin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" media="all">
	<script src="touchpin/jquery.bootstrap-touchspin.js"></script>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script language="javascript">
		/* hide all elements & show preloader*/
		document.documentElement.className += 'js';
		/* display variabel */
		var alertLoadProcess = "<span class='alert alert-info' onclick='clearNotif()'><i class='fa fa-spinner fa-spin'></i> Loading...</span>";
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
                            <i class="	fa fa-file-text-o fa-fw"></i> Data Kasir Operasional (KO) Tonase
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div id="Area1">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
					<br><br><br><br>
                    <div class="panel panel-success">
                        <div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Data Kasir Operasional (KOM) Mitra
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div id="AreaMitra1">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>

			<div class="row">
				<div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="	fa fa-file-text-o fa-fw"></i> Data Penampungan Kasir Operasional (KO) Tonase
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div id="Area2">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
								</div>
							</div>
							<form class="form-horizontal" action="pay_action2.php?task=cancelAll&type=km" id="cancelko" name="cancelko" method="POST">
								<div class="form-group" style="padding:10px; margin-left:0px; margin-right:0px;">
									<?php 
										if($check>=1){
											echo'<center><button type="submit" class="btn btn-outline btn-primary btn-block"><i class="fa fa-times"></i>&nbsp;Batalkan Semua</button></center>';
										}
									?>
								</div>
							</form>
                        </div>
                    </div>
				</div>

				<div class="col-lg-6">
					<div class="panel panel-success">
						<div class="panel-heading" style="background: green; color: #fff;">
							<i class="fa fa-file-text-o fa-fw"></i> Data Penampungan Kasir Operasional (KOM) Mitra
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<div id="AreaMitra2">
										<div class="alert alert-info" onclick="clearNotif()">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>
									</div>
								</div>
								<form class="form-horizontal" action="pay_action2.php?task=cancelAll_mitra&type=km" id="cancelko" name="cancelko" method="POST">
									<div class="form-group" style="padding:10px; margin-left:0px; margin-right:0px;">
										<?php 
											if($check_mitra>=1){
												echo'<center><button type="submit" class="btn btn-outline btn-success btn-block"><i class="fa fa-times"></i>&nbsp;Batalkan Semua </button></center>';
											}
										?>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- <div class="col-lg-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-close fa-fw"></i> Pembatalan Data Penampungan Kasir Operasional (KO) Tonase
						</div>
						<div class="panel-body">
							<div class="row">
								<form class="form-horizontal" action="pay_action2.php?task=cancelAll&type=km" id="cancelkm" name="cancelkm" method="POST">
									<div class="col-md-6" style="margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<label>ID KM: <?php echo $km_id; ?></label>
										</div>
									</div>
									<div class="col-md-6" style="margin-top:0px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<label>Total Rupiah: <?php echo format_rupiah($cost); ?></label>
										</div>
									</div>
									<div class="form-group" style="padding:10px; margin-left:0px; margin-right:0px; margin-bottom:-10px;">
										<?php 
											if($check>=1){
												echo'<center><button type="submit" class="btn btn-outline btn-primary btn-block">Batalkan Semua <i class="fa fa-angle-double-right fa-lg"></i></button></center>';
											}
										?>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div> -->
			</div>

			<div class="row">
				<div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-check-square fa-fw"></i> Acc Data KO Tonase Menjadi KM
                        </div>
                        <div class="panel-body">
						<?php if($check>=1) { ?>
							<div class="row">
								<form class="form-horizontal" action="pay_action2.php?task=acc&type=km" id="acckm" name="acckm" method="POST" target="_blank">
									<div class="col-md-6" style="margin-top:0px; margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>ID KM: <?php echo $km_id; ?></label>
											</div>
										</div>
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-15px;">
											<input type="number" class="form-control text-center" id="cash" name="cash" placeholder="Uang Yg Diterima" required>                  
										</div>
									</div>
									<div class="col-md-6" style="margin-top:0px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>Total Rupiah: <?php echo format_rupiah($cost); ?></label>
											</div>
										</div>
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-15px;">
											<?php 
												if($check>=1){
													echo'<center><button type="button" id="btn-acc" class="btn btn-outline btn-primary btn-block" style="display: none;" data-toggle="modal" data-target="#confirmationModal"><i class="fa fa-check"></i>&nbsp;ACC</button></center>';
												}
											?>

											<!-- Modal -->
											<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
													<div class="modal-dialog text-left" role="document">
														<div class="modal-content">
														<div class="modal-header bg-primary" >
															<h5 class="modal-title" id="confirmationModalLabel"><b>MOHON CEK KEMBALI !</b></h5>
															<button type="button" style="margin-top: -20px;" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<table>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Total setor KO harus sama dengan total rupiah KM</td>
																</tr>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Setelah laporan KOM Tonase terbuat cek total setoran sebelum melakukan print laporan</td>
																</tr>
															</table>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
															<button type="button" class="btn btn-primary" id="confirmSubmit" onclick="postAndReload()">Yakin dan lanjutkan</button>
														</div>
														</div>
													</div>
												</div>
										</div>
									</div>
								</form>
							</div>
							<?php } elseif($check<1) { ?>
									<div class="payment-notfound text-center">
										<img src="img/payment-tonase-notfound.png" alt="Image Payment Not Found" width="200px">
										<h3><b>Belum Ada Persetujuan KO</b></h3>
									</div>
								<?php } ?>
                        </div>
                    </div>
				</div>
				<div class="col-lg-6">
					<div class="panel panel-success">
                        <div class="panel-heading"  style="background: green; color: #fff;">
                            <i class="fa fa-check-square fa-fw"></i> Acc Data KOM Mitra Menjadi KMM
                        </div>
                        <div class="panel-body">
						<?php if($check_mitra>=1) { ?>
							<div class="row">
								<form class="form-horizontal" action="pay_action2.php?task=acc_mitra&type=km" id="acckm_mitra" name="acckm" method="POST" target="_blank">
									<div class="col-md-6" style="margin-top:0px; margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>ID KM: <?php echo $km_id_mitra; ?></label>
											</div>
										</div>
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-15px;">
											<input type="number" class="form-control text-center" id="cash-mitra" name="cash" placeholder="Uang Yg Diterima" required>                  
										</div>
									</div>
									<div class="col-md-6" style="margin-top:0px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>Total Rupiah: <?php echo format_rupiah($cost_mitra); ?></label>
											</div>
										</div>
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-15px;">
											<?php 
												if($check_mitra>=1){
													echo'<center><button type="button" id="btn-acc-mitra" class="btn btn-outline btn-success btn-block" style="display: none;" data-toggle="modal" data-target="#confirmationModalMitra"><i class="fa fa-check"></i>&nbsp;ACC</button></center>';
												}
											?>

											<!-- Modal -->
											<div class="modal fade" id="confirmationModalMitra" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
													<div class="modal-dialog text-left" role="document">
														<div class="modal-content">
														<div class="modal-header" style="background: green; color: #fff;">
															<h5 class="modal-title" id="confirmationModalLabel"><b>MOHON CEK KEMBALI !</b></h5>
															<button type="button" style="margin-top: -20px;" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<table>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Total setor KOM harus sama dengan total rupiah KMM</td>
																</tr>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Setelah laporan KKM Mitra terbuat cek total setoran sebelum melakukan print laporan</td>
																</tr>
															</table>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
															<button type="button" class="btn btn-success" id="confirmSubmitMitra" onclick="postAndReload()">Yakin dan lanjutkan</button>
														</div>
														</div>
													</div>
												</div>
										</div>
									</div>
								</form>
							</div>
							<?php } elseif($check_mitra<1) { ?>
									<div class="payment-notfound text-center">
										<img src="img/payment-mitra-notfound.png" alt="Image Payment Not Found" width="200px">
										<h3><b>Belum Ada Persetujuan KOM</b></h3>
									</div>
								<?php } ?>
                        </div>
                    </div>
				</div>
			</div>
        </div>
    </div>
	<!-- Modal Password -->
	<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src='../assets/image/Delete.png' width='25' height='25'/></button>
					<h4 id="header_password">Ganti Password</h4>
				</div>
				<div class="modal-body">
					<div id="data_password" style="margin-bottom: 0px;">
						<div class="alert alert-info" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
					</div>
					<input type="hidden" id="value_id" />
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>
	<script>
		$(document).ready(function(){
			// Auto Refresh Page
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("km_table.php",function(){clearNotif();});
			$('#Area2').load("km_acc_table.php",function(){clearNotif();});
			$('#AreaMitra1').load("km_table_mitra.php",function(){clearNotif();});
			$('#AreaMitra2').load("km_acc_table_mitra.php",function(){clearNotif();});
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
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>

	<!-- Reload Window Tonase-->
	<script>
			// Fungsi untuk menampilkan tombol ACC Tonase
			document.getElementById('cash').addEventListener('input', function() {
				const receiverValue = this.value;
				const btnAcc = document.getElementById('btn-acc');

				// Jika ada input, tampilkan tombol ACC, jika kosong sembunyikan
				if (receiverValue !== "") {
					btnAcc.style.display = "block";
				} else {
					btnAcc.style.display = "none";
				}
			});

			// Tangkap event submit untuk form
			document.getElementById('acckm').addEventListener('submit', function(event) {
				event.preventDefault(); // Cegah default form submit
				const btnAcc = document.getElementById('btn-acc');

				// Jika tombol ACC terlihat, trigger klik tombol ACC
				if (btnAcc && btnAcc.style.display === "block") {
					btnAcc.click();
				}
			});

			// Modal konfirmasi tombol acc (tonase)
			document.getElementById('confirmSubmit').addEventListener('click', function () {
				// Submit form setelah konfirmasi
				document.getElementById('acckm').submit();
			});

			function postAndReload() {
				setTimeout(function() {
					location.reload();
				}, 100);
			}
	</script>

	<!-- Reload Window Mitra-->
	<script>
			// Fungsi untuk menampilkan tombol ACC untuk Mitra
			document.getElementById('cash-mitra').addEventListener('input', function() {
				const receiverValueMitra = this.value;
				const btnAccMitra = document.getElementById('btn-acc-mitra');

				// Jika ada input, tampilkan tombol ACC Mitra, jika kosong sembunyikan
				if (receiverValueMitra !== "") {
					btnAccMitra.style.display = "block";
				} else {
					btnAccMitra.style.display = "none"; 
				}
			});

			// Tangkap event submit untuk form
			document.getElementById('acckm_mitra').addEventListener('submit', function(event) {
				event.preventDefault(); // Cegah default form submit
				const btnAcc = document.getElementById('btn-acc-mitra');

				// Jika tombol ACC terlihat, trigger klik tombol ACC
				if (btnAcc && btnAcc.style.display === "block") {
					btnAcc.click();
				}
			});

			// Modal konfirmasi tombol acc (mitra)
			document.getElementById('confirmSubmitMitra').addEventListener('click', function () {
				// Submit form setelah konfirmasi
				document.getElementById('acckm_mitra').submit();
			});

			function postAndReload() {
				setTimeout(function() {
					location.reload();
				}, 100);
			}
	</script>
	

	<!-- Sweet Alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
		<?php
			// Menampilkan SweetAlert jika session success ada dan bernilai true
			if (isset($_SESSION['success'])) {
				$title = $_SESSION['title'];
				$message = $_SESSION['message'];
				$icon = $_SESSION['icon'];
				echo "<script>
					Swal.fire({
						title: '$title',
						text: '$message',
						icon: '$icon'
					});
				</script>";
				unset($_SESSION['success']);
				unset($_SESSION['icon']);
				unset($_SESSION['title']);
				unset($_SESSION['message']);
			} elseif (isset($_SESSION['success'])) {
				$title = $_SESSION['title'];
				$message = $_SESSION['message'];
				$icon = $_SESSION['icon'];
				echo "<script>
					Swal.fire({
						title: '$title',
						text: '$message',
						icon: '$icon'
					});
				</script>";
				unset($_SESSION['success']); 
				unset($_SESSION['icon']);
				unset($_SESSION['title']);
				unset($_SESSION['message']);
			}
		?>
</body>
</html>