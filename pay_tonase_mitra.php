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

	$checker 			= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	//Generate KO ID Tonase
	$ko_id 			= 0;
	$year			= date('Y');
	$ko_id 			= executeScalar("select count(ko_id) from ko where ko_year = '".$year."'");
	$ko_id 			= $ko_id+1;
	$ko_id 			= sprintf("%06d", $ko_id);
	$ko_id  		= $year.$ko_id;
	$check 			= executeScalar("select count(weighing_id) from weighing where weighing_sts3 = 'Pending'");
	$cost_tonase 	= executeScalar("select sum(cost_tonase) from weighing where weighing_sts3 = 'Pending'");

	// Generate KO ID Mitra
	$ko_id_mitra 	= 0;
	$ko_id_mitra 	= executeScalar("select count(ko_id) from ko_mitra where ko_year = '".$year."'");
	$ko_id_mitra 	= $ko_id_mitra+1;
	$ko_id_mitra 	= sprintf("%06d", $ko_id_mitra);
	$ko_id_mitra  = $year.$ko_id_mitra;
	$check_mitra 	= executeScalar("select count(weighing_id) from weighing_mitra where weighing_sts3 = 'Pending'");
	$cost_mitra 	= executeScalar("select sum(cost_bayar) from weighing_mitra where weighing_sts3 = 'Pending'");
	
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
		<title>Pembayaran Tonase & Mitra | PISS</title>

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
	<body style="font-family: Inter, sans-serif;">
		<div id="wrapper">
			<?php include 'components/sidebar.php'; ?>

			<div id="page-wrapper">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-xs-6">
						<br><br><br><br>
						<div class="panel panel-primary" style="margin-bottom:-10px;">
							<div class="panel-heading">
								<i class="	fa fa-file-text-o fa-fw"></i> Data Tagihan Tonase
							</div>
							<div class="panel-body" style="margin-bottom:-10px;">
								<div class="row" style=" overflow-x: auto;">
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

					<div class="col-lg-6 col-md-6 col-xs-6">
						<br/><br><br><br>
						<div class="panel panel-success" style="margin-bottom:-10px;">
							<div class="panel-heading" style="background: green; color: #fff;">
								<i class="fa fa-file-text-o fa-fw"></i> Data Tagihan Mitra
							</div>
							<div class="panel-body" style="margin-bottom:-10px;">
								<div class="row" style=" overflow-x: auto;">
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
				</div><br>
				<div class="row">
					<div class="col-lg-6 col-md-6 col-xs-6">
						<div class="panel panel-primary" style="margin-bottom:0px;">
							<div class="panel-heading">
								<i class="	fa fa-file-text-o fa-fw"></i> Data Penampungan Pembayaran Tonase Sementara
							</div>
							<div class="panel-body" style="margin-bottom:-10px;">
								<div class="row" style=" overflow-x: auto;">
									<div class="col-md-12">
										<div id="Area2">
											<div class="alert alert-info" onclick="clearNotif()">
												<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
											</div>
										</div>
									</div>

									<form class="form-horizontal" action="pay_action.php?task=cancelAll&type=ko" id="cancelko" name="cancelko" method="POST">
										<div class="form-group" style="padding:10px; margin-left:0px; margin-right:0px;">
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
					</div>

					<div class="col-lg-6 col-md-6 col-xs-6">
						<div class="panel panel-success" style="margin-bottom:0px;">
							<div class="panel-heading" style="background: green; color: #fff;">
								<i class="	fa fa-file-text-o fa-fw"></i> Data Penampungan Pembayaran Mitra Sementara
							</div>
							<div class="panel-body" style="margin-bottom:-10px;">
								<div class="row" style=" overflow-x: auto;">
									<div class="col-md-12" >
										<div id="AreaMitra2">
											<div class="alert alert-info" onclick="clearNotif()">
												<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
											</div>
										</div>
									</div>

									<form class="form-horizontal" action="pay_action.php?task=cancelAll_mitra&type=ko" id="cancelko" name="cancelko" method="POST">
										<div class="form-group" style="padding:10px; margin-left:0px; margin-right:0px;">
											<?php 
												if($check_mitra>=1){
													echo'<center><button type="submit" class="btn btn-outline btn-success btn-block">Batalkan Semua <i class="fa fa-angle-double-right fa-lg"></i></button></center>';
												}
											?>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div><br>
				<div class="row">
					<!-- <div class="col-lg-6 col-md-6 col-xs-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<i class="fa fa-close fa-fw"></i> Pembatalan Data Pembayaran
							</div>
							<div class="panel-body">
								<div class="row">
									<form class="form-horizontal" action="pay_action.php?task=cancelAll" id="cancelko" name="cancelko" method="POST">
										<div class="col-md-6" style="margin-bottom:-10px;">
											<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
												<label>ID KO: <?php echo $ko_id; ?></label>
											</div>
										</div>
										<div class="col-md-6" style="margin-top:0px;margin-bottom:-10px;">
											<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
												<label>Total Rupiah: <?php echo format_rupiah($cost_tonase); ?></label>
											</div>
										</div>
										<div class="form-group" style="padding:10px; margin-left:0px; margin-right:0px;">
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
					<div class="col-lg-6 col-md-6 col-xs-6">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<i class="fa fa-check-square fa-fw"></i> Acc Data Pembayaran Tonase
							</div>
							<div class="panel-body">
								<?php if($check>=1) { ?>
								<div class="row">
									<div class="col-md-6" style="margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>ID KO : <?php echo $ko_id; ?></label>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>Total Rupiah: <?php echo format_rupiah($cost_tonase); ?></label>
											</div>
										</div>
									</div>
								</div>
								<!-- <div class="row">
									<div class="col-md-12" style="margin-bottom:-10px;">
										<form class="form-horizontal" action="pay_action.php?task=calculate&type=ko" id="calculate" name="calculate" method="POST">
											<div class="form-group" style="padding:10px; text-align:center;">
												<input type="number" class="form-control text-center" id="received" name="receive" placeholder="Uang Yg Diterima" required>                  
											</div>
											<div class="form-group" style="padding:10px; margin-top: -20px;">
												<?php 
													if($check>=1){
														echo'<center><button type="submit" class="btn btn-outline btn-primary btn-block">Hitung <i class="fa fa-angle-double-right fa-lg"></i></button></center>';
													}
												?>
											</div>
										</form>
									</div>
								</div> -->
								<div class="row">
									<div class="col-md-12">
										<form class="form-horizontal" action="pay_action.php?task=acc&type=ko" id="accko" name="accko" method="POST" target="_blank">
											<div class="form-group" style="padding:10px; text-align:center;">
												<select class="form-control" name="receiver" id="receiver" required>
													<option value="" class="text-center">- Pilih Penyetor Uang -</option>
													<?php 
														$sql 		= mysql_query("select checker_id, checker_name from checker where checker_sts = 'Aktif' AND checker_role = 'Checker' AND status_aktif = '1' order by checker_name");
														while($data = mysql_fetch_array($sql))
													{?>
														<option class="text-center" value="<?php echo $data['checker_id'];?>"><?php echo $data['checker_name'];?></option>
													<?php } ?>
												</select>
											</div>
											<div class="form-group" style="padding:10px; margin-top: -20px;">
												<?php 
													if($check>=1){
														echo'<center>
																<button type="button" class="btn btn-outline btn-primary btn-block" id="btn-acc" data-toggle="modal" data-target="#confirmationModal" style="display: none;" >ACC <i class="fa fa-angle-double-right fa-lg"></i></button>
															</center>';
													}
												?>

												<!-- Modal -->
												<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
														<div class="modal-header" >
															<h5 class="modal-title" id="confirmationModalLabel"><b>MOHON CEK KEMBALI !</b></h5>
															<button type="button" style="margin-top: -20px;" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<table>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Uang fisik tagihan harus sama dengan nota</td>
																</tr>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Total rupiah pembayaran tonase harus sama dengan uang fisik tagihan</td>
																</tr>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Setelah laporan KO Tonase terbuat cek total setoran sebelum melakukan print laporan</td>
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
										</form>
									</div>
								</div>
								<?php } elseif($check<1) { ?>
									<div class="payment-notfound text-center">
										<img src="img/payment-tonase-notfound.png" alt="Image Payment Not Found" width="200px">
										<h3><b>Belum Ada Pembayaran</b></h3>
									</div>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="col-lg-6 col-md-6 col-xs-6">
						<div class="panel panel-success">
							<div class="panel-heading" style="background: green; color: #fff;">
								<i class="fa fa-check-square fa-fw"></i> Acc Data Pembayaran Mitra
							</div>
							<div class="panel-body">
							<?php if($check_mitra>=1) { ?>
								<div class="row">
									<div class="col-md-6" style="margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>ID KOM : <?php echo $ko_id_mitra; ?></label>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<div class="box-ko-id" style="background: #A04747; padding: 10px; color: white; border-radius: 8px;">
												<label>Total Rupiah: <?php echo format_rupiah($cost_mitra); ?></label>
											</div>
										</div>
									</div>
								</div>
								<!-- <div class="row">
									<div class="col-md-12" style="margin-bottom:-10px;">
										<form class="form-horizontal" action="pay_action.php?task=calculate_mitra&type=ko" id="calculate" name="calculate" method="POST">
											<div class="form-group" style="padding:10px; text-align:center;">
												<input type="number" class="form-control text-center" id="received" name="receive" placeholder="Uang Yg Diterima" required>                  
											</div>
											<div class="form-group" style="padding:10px; margin-top: -20px;">
												<?php 
													if($check_mitra>=1){
														echo'<center><button type="submit" class="btn btn-outline btn-primary btn-block">Hitung <i class="fa fa-angle-double-right fa-lg"></i></button></center>';
													}
												?>
											</div>
										</form>
									</div>
								</div> -->
								<div class="row">
									<div class="col-md-12">
										<form class="form-horizontal" action="pay_action.php?task=acc_mitra&type=ko" id="accko_mitra" name="accko" method="POST" target="_blank">
											<div class="form-group" style="padding:10px; text-align:center;">
												<select class="form-control" name="receiver" id="receiver-mitra" required>
													<option value="" class="text-center">- Pilih Penyetor Uang -</option>
													<?php 
														$sql 		= mysql_query("select checker_id, checker_name from checker where checker_sts = 'Aktif' AND checker_role = 'Checker' AND status_aktif = '1' order by checker_name");
														while($data = mysql_fetch_array($sql))
													{?>
														<option class="text-center" value="<?php echo $data['checker_id'];?>"><?php echo $data['checker_name'];?></option>
													<?php } ?>
												</select>
											</div>
											<div class="form-group" style="padding:10px; margin-top: -20px;">
												<?php 
													if($check_mitra>=1){
														echo'<center>
															<button type="button" class="btn btn-outline btn-success btn-block" id="btn-acc-mitra" style="display: none;" data-toggle="modal" data-target="#confirmationModalMitra">
															ACC <i class="fa fa-angle-double-right fa-lg"></i>
															</button>
														</center>';
													}
												?>

												<!-- Modal -->
												<div class="modal fade" id="confirmationModalMitra" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
													<div class="modal-dialog" role="document">
														<div class="modal-content">
														<div class="modal-header" >
															<h5 class="modal-title" id="confirmationModalLabel"><b>MOHON CEK KEMBALI !</b></h5>
															<button type="button" style="margin-top: -20px;" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
															</button>
														</div>
														<div class="modal-body">
															<table>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Uang fisik tagihan harus sama dengan nota</td>
																</tr>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Total rupiah pembayaran mitra harus sama dengan uang fisik tagihan</td>
																</tr>
																<tr>
																	<td style="font-size:28px; padding-right: 10px;"> • </td>
																	<td>Setelah laporan KOM Mitra terbuat cek total setoran sebelum melakukan print laporan</td>
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
										</form>
									</div>
								</div>
								<?php } elseif($check_mitra<1) { ?>
									<div class="payment-notfound text-center">
										<img src="img/payment-mitra-notfound.png" alt="Image Payment Not Found" width="200px">
										<h3><b>Belum Ada Pembayaran</b></h3>
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
				$('#Area1').load("pay_table.php",function(){clearNotif();});
				$('#Area2').load("pay_acc_table.php",function(){clearNotif();});
				$('#AreaMitra1').load("pay_mitra_table.php",function(){clearNotif();});
				$('#AreaMitra2').load("pay_mitra_acc_table.php",function(){clearNotif();});
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
			// Fungsi untuk menampilkan tombol jika penyetor dipilih (Tonase)
			const receiverTonase = document.getElementById('receiver');
			const btnAccTonase = document.getElementById('btn-acc');

			if (receiverTonase && btnAccTonase) {
				receiverTonase.addEventListener('change', function() {
					const receiverValue = this.value; // Ambil nilai dari select box

					if (receiverValue !== "") {
						btnAccTonase.style.display = "block"; // Tampilkan tombol jika ada opsi dipilih
					} else {
						btnAccTonase.style.display = "none"; // Sembunyikan tombol jika tidak ada opsi dipilih
					}
				});
			}

			// Modal konfirmasi tombol acc (tonase)
			document.addEventListener('DOMContentLoaded', function() {
				document.getElementById('confirmSubmit').addEventListener('click', function () {
				// Submit form setelah konfirmasi
				document.getElementById('accko').submit();
				});
			});

			function postAndReload() {
				setTimeout(function() {
					location.reload();
				}, 100);
			}
		</script>

		<!-- Reload Window Mitra-->
		<script>
			// Fungsi untuk menampilkan tombol jika penyetor dipilih (Mitra)
			const receiverMitra = document.getElementById('receiver-mitra');
			const btnAccMitra = document.getElementById('btn-acc-mitra');

			if (receiverMitra && btnAccMitra) {
				receiverMitra.addEventListener('change', function() {
					const receiverValueMitra = this.value; // Ambil nilai dari select box

					if (receiverValueMitra !== "") {
						btnAccMitra.style.display = "block"; // Tampilkan tombol jika ada opsi dipilih
					} else {
						btnAccMitra.style.display = "none"; // Sembunyikan tombol jika tidak ada opsi dipilih
					}
				});
			}

			// Modal konfirmasi tombol acc (mitra)
			document.addEventListener('DOMContentLoaded', function() {
				document.getElementById('confirmSubmitMitra').addEventListener('click', function () {
				// Submit form setelah konfirmasi
				document.getElementById('accko_mitra').submit();
				});
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
				$message = $_SESSION['message'];
				$icon = $_SESSION['success'] ? 'success' : 'error'; // Menentukan ikon berdasarkan status
				echo "<script>
					Swal.fire({
						title: 'Terima Kasih',
						text: '$message',
						icon: '$icon'
					});
				</script>";
				unset($_SESSION['success']); // Menghapus session setelah alert ditampilkan
				unset($_SESSION['message']);
			}
		?>
	</body>
</html>

