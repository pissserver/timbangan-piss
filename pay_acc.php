<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	$cashier = executeScalar("select checker_id from checker where checker_code = '".$_SESSION['checker']."'");
	$checker 	= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	$check 	= executeScalar("select count(ko_id) from ko where cashier_id = '".$cashier."' and ko_status = 'Diterima'");
	//Generate KM ID
	$km_id 	= 0;
	$year	= date('Y');
	$km_id 	= executeScalar("select count(km_id) from km where km_year = '".$year."'");
	$km_id 	= $km_id + 1;
	$km_id 	= sprintf("%06d", $km_id);
	$km_id  = $year.$km_id;
	$cost 	= executeScalar("select sum(ko_total) from ko where cashier_id = '".$cashier."' and ko_status = 'Diterima'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="icon" href="../images/icon.png">
    <link rel="shortcut icon" href="../images/icon.png" />
    <title>Setoran KO Tonase | PISS</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- MetisMenu CSS -->
    <link href="css/plugins/metisMenu/metisMenu.min.css" rel="stylesheet">
    <!-- Timeline CSS -->
    <link href="css/plugins/timeline.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/sb-admin-2.css" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="font-awesome-4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
	
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
                <div class="col-lg-12">
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
                        </div>
                    </div>
                </div>
				<div class="col-lg-6">
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
				</div>
				<div class="col-lg-6">
					
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-check-square fa-fw"></i> Acc Data KO Tonase Menjadi KM
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form class="form-horizontal" action="pay_action2.php?task=acc&type=km" id="acckm" name="acckm" method="POST" target="_blank">
									<div class="col-md-6" style="margin-top:0px; margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<label>ID KM: <?php echo $km_id; ?></label>
										</div>
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-15px;">
											<input type="number" class="form-control" id="cash" name="cash" placeholder="Uang Yg Diterima" required>                  
										</div>
									</div>
									<div class="col-md-6" style="margin-top:0px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-10px;">
											<label>Total Rupiah: <?php echo format_rupiah($cost); ?></label>
										</div>
										<div class="form-group" style="padding:10px; text-align:center; margin-top:-15px;">
											<?php 
												if($check>=1){
													echo'<center><button type="submit" class="btn btn-outline btn-primary btn-block">ACC <i class="fa fa-angle-double-right fa-lg"></i></button></center>';
												}
											?>
										</div>
									</div>
								</form>
							</div>
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
</body>
</html>