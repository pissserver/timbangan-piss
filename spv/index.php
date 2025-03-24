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
	<link rel="icon" href="../img/icon.png">
    <link rel="shortcut icon" href="../img/icon.png" />
    <title>Timbangan | PISS</title>
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
				<div class="col-lg-12">
					<br><br><br>
					<br />
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-gear fa-fw"></i> Pilih Aksi
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<select class="form-control" id="mySelect" onchange="switchTime()" autofocus="autofocus">
										<option value="blank">- Pilih Kendaraan Masuk / Keluar -</option>
										<option value="in">Masuk</option>
										<option value="out">Masuk & Keluar</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div id="Area2"></div>
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<i class="fa fa-truck fa-fw"></i> Data Kendaraan Masuk
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-12">
											<?php 
												$query 	= mysql_query("select * from weighing 
												where (date_in  between '2016-03-15' and '$date_now') and 
												weighing_sts = 'Masuk' order by weighing_id desc");

												if (mysql_num_rows($query) != 0) { ?>
													<div id="Area1">
														<div class="alert alert-info" onclick="clearNotif()">
															<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
														</div>
													</div>
												<?php } else { ?>
													<div class="payment-notfound text-center">
														<img src="../img/logistic.png" alt="Image Payment Not Found" width="250px">
														<h3><b>Belum Ada Kendaraan Masuk</b></h3>
													</div>
												<?php }
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>

	<!-- Modal Ubah -->
	<div class="modal fade" id="modalUbah" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			    <!-- Modal Content modal_edit_weightin.php -->
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function(){
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("weighing_table.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',100); 
			
			// Auto Refresh Page
			/*$.ajaxSetup({ cache: false }); // This part addresses an IE bug.  without it, IE will only load the first number and will never refresh
			setInterval(function() {
				$('#weight_in').load('random.php');
			}, 1000);*/
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
		});
		
			function switchTime() {
				var x = document.getElementById("mySelect").value;
				
				if(x=="blank"){
					$("#notifArea").html(alertLoadProcess);
					$("#Area2").load("blank.php",function(){clearNotif();});
				}
				else if(x=="in"){
					$("#notifArea").html(alertLoadProcess);
					$("#Area2").load("in.php",function(){clearNotif();});
				}
				else if(x=="out"){
					$("#notifArea").html(alertLoadProcess);
					$("#Area2").load("out.php",function(){clearNotif();});
				}
			}
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