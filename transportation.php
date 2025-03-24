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
	
	$checker 	= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
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
    <title>KIR Kendaraan | PISS</title>

    <?php echo headScript(); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="js/jquery.js"></script>
	
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
                            <i class="fa fa-truck fa-fw"></i> Tambah Data Kir Kendaraan
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" id="formTransportation" name = "formTransportation" method="POST" action="transportation_action.php?task=insert">
								<fieldset>
									<div class="form-group" style="padding:10px; margin-top:-10px; margin-bottom:0px;">
										<label>Nopol Kendaraan</label>
										<input type="text" class="form-control" id="trans" name="trans" placeholder="Masukkan nopol kendaraan" autofocus="autofocus" required>                  
									</div>
									<div class="form-group" style="padding:10px; margin-bottom:0px;">
										<label>Berat Kendaraan</label><sup> (Kg)</sup>
										<input class="form-control" id="weight" name="weight" type="text" placeholder="Masukkan berat kendaraan" required/>
										<script>
											$("input[name='weight']").TouchSpin({
											  verticalbuttons: true,
											  verticalupclass: 'glyphicon glyphicon-plus',
											  verticaldownclass: 'glyphicon glyphicon-minus'
											});
										</script>
									</div>
									<div class="form-group" style="padding:10px; margin-bottom:0px;">
										<center><button type="submit" class="btn btn-block btn-primary"><i class="fa fa-floppy-o fa-fw"></i>&nbsp;Simpan</button></center>
									</div>
								</fieldset>
							</form> 
                        </div>
                    </div>
					<!-- <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-truck fa-fw"></i> Print Data Kir Kendaraan
                        </div>
                        <div class="panel-body">
                            <form action="htmltopdf/reportKir.php" target="_blank" class="form-horizontal" id="formTransportation" name = "formTransportation" method="POST">
								<div class="form-group" style="padding:10px; margin-bottom:-10px;">
									<center><button type="submit" class="btn btn-block btn-primary">Print</button></center>
								</div>
							</form> 
                        </div>
                    </div> -->
                </div>
            </div>

			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
                            <i class="fa fa-truck fa-fw"></i> Data Kir Kendaraan
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
    </div>

	<!-- Modal Edit -->
	<div id="modalEditTransportation" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Meload modal "transportation_modal.php" file -->
            </div>
        </div>
    </div>

		<!-- Modal Delete -->
		<div id="modalDeleteTransportation" class="modal fade">
			<div class="modal-dialog">
				<div class="modal-content">
					<!-- Meload modal "transportation_delete_modal.php" file -->
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
			$('#Area1').load("transportation_table.php",function(){clearNotif();});
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