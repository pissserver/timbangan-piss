<?php
	session_start();
	include "../connection.php";
	include "components/role_session.php";

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
?>	
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
		<title>Update Log | PISS</title>

		<?php echo headScript(); ?>

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
		<script src="../js/jquery.min.js"></script>
		<!-- input number -->
		<link href="../js/touchpin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" media="all">
		<script src="../js/touchpin/jquery.bootstrap-touchspin.js"></script>
		<script>
			/* hide all elements & show preloader*/
			document.documentElement.className += 'js';

			/* display variabel */
			var alertLoadProcess = "<span class='alert alert-info' onclick='clearNotif()'><i class='fa fa-spinner fa-spin'></i> Loading...</span>";
			//fungsi input hanya angka
			function isNumber(event) {
			if (event) {
				var charCode = (event.which) ? event.which : event.keyCode;
				if (charCode != 190 && charCode > 31 && 
				(charCode < 48 || charCode > 57) && 
				(charCode < 96 || charCode > 105) && 
				(charCode < 37 || charCode > 40) && 
					charCode != 110 && charCode != 8 && charCode != 46 )
				return false;
			}
			return true;
			}
		</script>
	</head>
	<body>
		<div id="wrapper">
		<?php include "components/sidebar.php" ?>

			<div id="page-wrapper">
				<br><br><br><br>
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h4 class="panel-title">
									<i class="fa fa-history"></i> Update Log / Changelog
								</h4>
							</div>
							<div class="panel-body">
								<div class="panel-group" id="accordionChangelog">

									<!-- Versi 1.2.1 -->
									<div class="panel panel-default">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordionChangelog" href="#ver121">
													<i class="fa fa-plus"></i> Version 1.2.1 - 04 November 2025
												</a>
											</h4>
										</div>
										<div id="ver121" class="panel-collapse collapse in">
											<div class="panel-body">
												<h5># Bug Fixing</h5>
												<ul>
													<li>🐞 Pembetulan bug saat data telah dipecah, sekarang pedagang utama tidak bisa diganti</li>
													<li>⚙️ Pengoptimalan input mitra, sekarang input mitra menjadi lebih aman dari kesalahan. Karena mitra yang ditampilkan sesuai dengan jenis pedagang yang bersangkutan </li>
													<li>🛠️ Penambahaan alert informasi saat pertama login</li>
												</ul>
											</div>
										</div>
									</div>
									
									<!-- Versi 1.2.0 -->
									<div class="panel panel-info">
										<div class="panel-heading">
											<h4 class="panel-title">
												<a data-toggle="collapse" data-parent="#accordionChangelog" href="#ver120">
													<i class="fa fa-plus"></i> Version 1.2.0 - 31 Oktober 2025
												</a>
											</h4>
										</div>
										<div id="ver120" class="panel-collapse collapse">
											<div class="panel-body">
												<h5># Penambahan & Optimalisasi Fitur</h5>
												<ul>
													<li>✨ Sistem versioning timbangan 1.2.0</li>
													<li>🐞 Sedikit perubahan tampilan pada nota timbang, tagihan, mitra, dan hasil timbang</li>
													<li>⚙️ Optimalisasi pada fitur 'Data Belum Dicek' & 'Data Sudah Dicek'</li>
													<li>🦴 Fixing bug pada fitur 'Listing Data'</li>
													<li>🎊 Sedikit perubahan button (Tombol Aksi) pada fitur 'Pembayaran & Setoran'</li>
													<li>🎈 Penambahan fitur log update (changelog)</li>
													<li>⏰ Penambahan fitur jam disamping nama akun</li>
													<li>🙌 Fixing bug notification pada icon "bell" disamping nama akun pada saat ada pembaruan sistem</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
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