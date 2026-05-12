<?php
	session_start();
	include "../connection.php";
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
	.data-card{
	background:#fff;
	border-radius:16px;
	box-shadow:0 12px 28px rgba(0,0,0,.08);
	display:flex;
	flex-direction:column;
	margin-bottom:20px;
}

.data-card-header{
	display:flex;
	justify-content:space-between;
	align-items:center;
	padding:14px 18px;
	font-weight:700;
	border-radius:16px 16px 0 0;
}

.data-card-header.primary{
	background:#4f46e5;
	color:#fff;
}

.data-card-header.success{
	background:#16a34a;
	color:#fff;
}

.search-input{
	border:none;
	border-radius:20px;
	padding:6px 14px;
	font-size:13px;
	width:180px;
	color:#333;
}

.search-input:focus{
	outline:none;
	box-shadow:0 0 0 2px rgba(255,255,255,.4);
}

.data-card-body{
	padding:14px;
	min-height:260px;
}

.data-item{
	display:grid;
	grid-template-columns: 1.2fr 1fr 110px;
	align-items:center;
	padding:12px 14px;
	border-radius:12px;
	margin-bottom:10px;
	background:#f9fafb;
	transition:.2s;
}

.data-item:hover{
	background:#eef2ff;
}

.data-left{
	font-weight:600;
}

.data-date{
	text-align:center;
	font-size:12px;
	color:#64748b;
}

.data-acc{
	text-align:right;
}

.btn-acc{
	background:#22c55e;
	color:#fff;
	border:none;
	padding:6px 14px;
	border-radius:20px;
	font-size:12px;
	font-weight:600;
	cursor:pointer;
}

.btn-acc:hover{
	background:#16a34a;
}

.data-card-footer{
	padding:10px 14px;
	border-top:1px solid #eee;
	text-align:center;
}

.pagination-btn{
	display:inline-block;
	padding:6px 12px;
	margin:0 3px;
	border-radius:8px;
	background:#e5e7eb;
	cursor:pointer;
	font-size:12px;
}

.pagination-btn.active{
	background:#4f46e5;
	color:#fff;
}

.loading-ui,
.empty-ui{
	text-align:center;
	padding:40px 0;
	color:#64748b;
	font-size:14px;
}


/* Status Payment */
.payment-status{
    display:inline-flex;
    align-items:center;
    gap:6px;

    margin-top:8px;
    padding:5px 10px;

    border-radius:999px;

    background:rgba(34,197,94,0.10);
    border:1px solid rgba(34,197,94,0.18);

    width:auto;
}

.payment-status .status-icon{
    width:18px;
    height:18px;

    border-radius:50%;

    display:flex;
    align-items:center;
    justify-content:center;

    background:linear-gradient(135deg,#22c55e,#16a34a);

    color:#fff;
    font-size:9px;

    box-shadow:0 2px 6px rgba(34,197,94,0.25);
}

.payment-status .status-info{
    display:flex;
    align-items:center;
}

.payment-status .status-label{
    display:none;
}

.payment-status .status-method{
    font-size:11px;
    font-weight:600;
    color:#15803d;
    letter-spacing:.2px;
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
		<title>Pembayaran Tonase & Mitra | PISS</title>

		<?php echo headScript(); ?>
		
		<!-- jquery -->
		<script src="../js/jquery.js"></script>
		<!-- input number -->
		<link href="../touchpin/jquery.bootstrap-touchspin.css" rel="stylesheet" type="text/css" media="all">

		<script src="../touchpin/jquery.bootstrap-touchspin.js"></script>
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

			<div id="page-wrapper"><br><br><br><br>
				<div class="row">
					<!-- Alert Lock -->
					<div style="margin: 0px 15px;" class="alert alert-warning" role="alert">
						<table>
							<tr>
								<td style="font-size:20px; padding-right: 10px;">
									<i class="fa fa-exclamation-triangle"></i>
								</td>
								<td>
									<p style="font-size: 14px; padding-top:10px;">
										Jika pada pembayaran ada ikon <b><i class="fa fa-lock"></i></b>, artinya masih ada nota dengan status <b>Centeng</b> yang belum terlunaskan atas nama pedagang bersangkutan.
									</p>
								</td>
							</tr>
						</table>
					</div><br>

					<!-- MITRA -->
					<div class="col-lg-12 col-md-12 col-xs-12">
						<div class="data-card">
							<div class="data-card-body">
								<div id="AreaMitra1">
									<div class="loading-ui">
										<i class="fa fa-spinner fa-spin"></i> Loading data...
									</div>
								</div>
							</div>

							<div class="data-card-footer">
								<div id="pagination-mitra"></div>
							</div>
						</div>
					</div>
				</div>
				<br><br>

				<div class="row">
					<div class="col-lg-12 col-md-12 col-xs-12">
						<div id="AreaMitra2">
							<div class="alert alert-info" onclick="clearNotif()">
								<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
							</div>
						</div>
					</div>
				</div>
				<br><br>
			</div>
		</div>

		
		<script>
			$(document).ready(function(){
				// Auto Refresh Page
				$("#notifArea").html(alertLoadProcess);
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

		<!-- Ajax Mitra -->
		<script>
			function reloadMitraArea(){
				$("#AreaMitra1").load("pay_mitra_table.php");
				$("#AreaMitra2").load("pay_mitra_acc_table.php");
			}

			/* ================= BAYAR MITRA (ANIMATED) ================= */
			$(document).on('click', 'a[href*="pay_action.php?task=pay_mitra&type=ko"]', function(e){

				e.preventDefault();

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

				const form = $(this);

				$.post(form.attr('action'), form.serialize(), function(){
					reloadMitraArea();
				}).fail(function(){
					alert('Gagal membatalkan semua pembayaran mitra');
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

