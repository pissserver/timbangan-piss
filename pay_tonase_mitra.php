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

	$ko_id 			= 0;
	$year			= date('Y');

	// Check Tonase
	$check 			= executeScalar("select count(weighing_id) from weighing where weighing_sts3 = 'Pending'");
	$cost_tonase 	= executeScalar("select sum(cost_tonase) from weighing where weighing_sts3 = 'Pending'");

	// Check Mitra
	$check_mitra 	= executeScalar("select count(weighing_id) from weighing_mitra where weighing_sts3 = 'Pending'");
	$cost_mitra 	= executeScalar("select sum(cost_bayar) from weighing_mitra where weighing_sts3 = 'Pending'");
	
?>

<style>
	/* ===== PAYMENT MODE SWITCH (FINTECH STYLE) ===== */
	.payment-mode-switch {
		display: flex;
		justify-content: center;
		margin: 15px 0 5px 0;
	}

	.switch-container {
		position: relative;
		display: flex;
		background: #f1f4f8;
		border-radius: 50px;
		padding: 5px;
		width: 360px;
		box-shadow: inset 0 2px 6px rgba(0,0,0,.08);
	}

	.switch-btn {
		position: relative;
		flex: 1;
		border: none;
		background: transparent;
		padding: 10px 0;
		border-radius: 50px;
		font-weight: 700;
		font-size: 13px;
		color: #7a8a9a;
		cursor: pointer;
		z-index: 2;
		transition: all .3s ease;
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 6px;
	}

	.switch-btn i {
		font-size: 14px;
	}

	.switch-btn.active {
		color: #fff;
	}

	/* Sliding pill */
	.switch-indicator {
		position: absolute;
		top: 5px;
		left: 5px;
		width: 50%;
		height: calc(100% - 10px);
		background: linear-gradient(135deg, #3b82f6, #428BCA);
		border-radius: 50px;
		box-shadow: 0 6px 15px rgba(37,99,235,.35);
		transition: all .35s cubic-bezier(.4,0,.2,1);
	}

	/* When Mitra Active */
	.switch-container.mitra-active .switch-indicator {
		left: calc(50% - 5px);
		background: linear-gradient(135deg, #22c55e, #16a34a);
		box-shadow: 0 6px 15px rgba(22,163,74,.35);
	}

	/* ===== ANIMASI PINDAH KE KERANJANG ===== */
	.payment-card-tonase {
		transition: all .45s ease;
	}

	.payment-card-mitra {
		transition: all .45s ease;
	}

	.card-slide-out {
		transform: translateX(120px) scale(.95);
		opacity: 0;
	}

	/* ===== ANIMASI MASUK KE PANEL BAWAH ===== */
	.card-slide-in {
		animation: slideInRight .45s ease;
	}

	@keyframes slideInRight {
		from {
			transform: translateX(-80px) scale(.95);
			opacity: 0;
		}
		to {
			transform: translateX(0) scale(1);
			opacity: 1;
		}
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

			<div id="page-wrapper"><br><br><br>
				<!-- Switch Toggle -->
				<div class="payment-mode-switch">
					<div class="switch-container">
						<div class="switch-indicator"></div>

						<button id="btn-tonase" class="switch-btn active">
							<i class="fa fa-truck"></i>
							<span>TONASE</span>
						</button>

						<button id="btn-mitra" class="switch-btn">
							<i class="fa fa-users"></i>
							<span>MITRA</span>
						</button>
					</div>
				</div>
				<hr>

				<!-- Tonase -->
				<div id="mode-tonase">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-xs-12">
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
					</div>
					<br><br>

					<div class="row">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<div class="panel panel-primary" style="margin-bottom:0px;">
								<div class="panel-heading">
									<i class="fa fa-file-text-o fa-fw"></i> Data Penampungan Pembayaran Tonase
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
									</div>
								</div>
							</div>
						</div>
					</div>
					<br><br>
				</div>

				<!-- Mitra -->
				<div id="mode-mitra" style="display: none;">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-xs-12">
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
					</div><br><br>

					<div class="row">
						<div class="col-lg-12 col-md-12 col-xs-12">
							<div class="panel panel-success" style="margin-bottom:0px;">
								<div class="panel-heading" style="background: green; color: #fff;">
									<i class="	fa fa-file-text-o fa-fw"></i> Data Penampungan Pembayaran Mitra
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
									</div>
								</div>
							</div>
						</div>
					</div><br><br>
				</div>
			</div>
		</div>


		<!-- Switch Toggle -->
		<script>
			$('#btn-tonase').on('click', function(){

				$('#btn-mitra').removeClass('active');
				$(this).addClass('active');

				$('.switch-container').removeClass('mitra-active');

				$('#mode-mitra').hide();
				$('#mode-tonase').fadeIn(200);
			});

			$('#btn-mitra').on('click', function(){

				$('#btn-tonase').removeClass('active');
				$(this).addClass('active');

				$('.switch-container').addClass('mitra-active');

				$('#mode-tonase').hide();
				$('#mode-mitra').fadeIn(200);
			});
		</script>
		
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

		// * TONASE
		<!-- Ajax Tonase -->
		<script>
			function reloadPaymentArea(){
				$("#Area1").load("pay_table.php");
				$("#Area2").load("pay_acc_table.php");
			}

			/* ================= BAYAR TONASE (ANIMATED) ================= */
			$(document).on('click', 'a[href*="pay_action.php?task=pay&type=ko"]', function(e){
				e.preventDefault();

				const url = $(this).attr('href');
				const card = $(this).closest('.payment-card-tonase');

				// Animasi geser keluar
				card.addClass('card-slide-out');

				setTimeout(function(){

					$.get(url, function(){

						// Reload kedua area
						$('#Area1').load('pay_table.php');
						$('#Area2').load('pay_acc_table.php', function(){

							// Animasi masuk untuk kartu baru di panel bawah
							$('#Area2 .payment-card-tonase').first().addClass('card-slide-in');

						});

					}).fail(function(){
						alert('Gagal memproses pembayaran');
						card.removeClass('card-slide-out');
					});

				}, 350);
			});

			/* ================= CANCEL TONASE (ANIMATED) ================= */
			$(document).on('click', 'a[href*="pay_action.php?task=cancel&type=ko"]', function(e){
				e.preventDefault();

				const url = $(this).attr('href');
				const card = $(this).closest('.payment-card-tonase');

				card.addClass('card-slide-out');

				setTimeout(function(){

					$.get(url, function(){

						$('#Area1').load('pay_table.php', function(){
							$('#Area1 .payment-card-tonase').first().addClass('card-slide-in');
						});

						$('#Area2').load('pay_acc_table.php');

					}).fail(function(){
						alert('Gagal membatalkan pembayaran');
						card.removeClass('card-slide-out');
					});

				}, 350);
			});

			/* ================= CANCEL ALL TONASE ================= */
			$(document).on('submit', 'form[action*="pay_action.php?task=cancelAll&type=ko"]', function(e){
				e.preventDefault();

				const form = $(this);

				$.post(form.attr('action'), form.serialize(), function(){
					reloadPaymentArea();
				}).fail(function(){
					alert('Gagal membatalkan semua pembayaran');
				});
			});
		</script>


		// * MITRA
		<!-- Ajax Mitra -->
		<script>
			function reloadMitraArea(){
				$("#AreaMitra1").load("pay_mitra_table.php");
				$("#AreaMitra2").load("pay_mitra_acc_table.php");
			}

			/* ================= BAYAR MITRA (ANIMATED) ================= */
			$(document).on('click', 'a[href*="pay_action.php?task=pay_mitra&type=ko"]', function(e){

				e.preventDefault();

				// hanya jika mode mitra aktif
				if(!$('#mode-mitra').is(':visible')) return;

				const url  = $(this).attr('href');
				const card = $(this).closest('.payment-card-mitra');

				/* Animasi geser keluar */
				card.addClass('card-slide-out');

				setTimeout(function(){

					$.get(url, function(){

						/* Reload kedua area */
						$('#AreaMitra1').load('pay_mitra_table.php');

						$('#AreaMitra2').load('pay_mitra_acc_table.php', function(){

							/* Animasi masuk kartu baru di panel bawah */
							$('#AreaMitra2 .payment-card-mitra')
								.first()
								.addClass('card-slide-in');
						});

					}).fail(function(){
						alert('Gagal memproses pembayaran mitra');
						card.removeClass('card-slide-out');
					});

				}, 350);
			});

			/* ================= CANCEL MITRA (ANIMATED) ================= */
			$(document).on('click', 'a[href*="pay_action.php?task=cancel_mitra&type=ko"]', function(e){

				e.preventDefault();

				if(!$('#mode-mitra').is(':visible')) return;

				const url  = $(this).attr('href');
				const card = $(this).closest('.payment-card-mitra');

				card.addClass('card-slide-out');

				setTimeout(function(){

					$.get(url, function(){

						$('#AreaMitra1').load('pay_mitra_table.php', function(){

							$('#AreaMitra1 .payment-card-mitra')
								.first()
								.addClass('card-slide-in');
						});

						$('#AreaMitra2').load('pay_mitra_acc_table.php');

					}).fail(function(){
						alert('Gagal membatalkan pembayaran mitra');
						card.removeClass('card-slide-out');
					});

				}, 350);
			});

			/* ================= CANCEL ALL MITRA ================= */
			$(document).on('submit', 'form[action*="pay_action.php?task=cancelAll_mitra&type=ko"]', function(e){

				e.preventDefault();

				if(!$('#mode-mitra').is(':visible')) return;

				const form = $(this);

				$.post(form.attr('action'), form.serialize(), function(){
					reloadMitraArea();
				}).fail(function(){
					alert('Gagal membatalkan semua pembayaran mitra');
				});
			});
		</script>

		<!-- jQuery -->
		<script src="js/jquery.js"></script>
		<!-- Bootstrap Core JavaScript -->
		<script src="js/bootstrap.min.js"></script>
		<!-- Metis Menu Plugin JavaScript -->
		<script src="js/plugins/metisMenu/metisMenu.min.js"></script>
		<!-- Custom Theme JavaScript -->
		<script src="js/sb-admin-2.js"></script>
		
		
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

