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
									<i class="fa fa-trophy"></i> Pencapaian Tim Kasir
								</h4>
							</div>
							<div class="panel-body">
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
			<!-- End Main Content -->
		</div>

		<script>
		$(document).ready(function(){
				// Auto Refresh Page
				$("#notifArea").html(alertLoadProcess);
				$('#Area1').load("achievement_table.php",function(){clearNotif();});
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