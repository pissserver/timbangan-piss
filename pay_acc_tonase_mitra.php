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

	$km_id 	= 0;
	$year	= date('Y');
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

            <div id="mode-tonase">
				<div class="row">
					<div class="col-lg-12">
						<div id="Area2">
						</div>
					</div>
				</div>
            </div>

			<div id="mode-mitra" style="display: none;">
				<div class="row">
					<div class="col-lg-12">
						<div id="AreaMitra2">
						</div>
					</div>
				</div>
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
			$('#Area2').load("km_acc_table.php",function(){clearNotif();});
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