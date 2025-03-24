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
    <title>Mitra / Mandor | PISS</title>

	<?php echo headScript(); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="../js/jquery.min.js"></script>
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
                            <i class="fa fa-user-plus fa-fw"></i> Tambah Data Mitra / Mandor
                        </div>
                        <div class="panel-body">
                            <form class="form-horizontal" id="formLaborer" name = "formLaborer" method="POST" action="laborer_action.php?task=insert">
								<fieldset>
									<div class="form-group" style="padding:10px; margin-top:-10px">
										<label>Kode Mitra / Mandor</label>
										<input type="text" class="form-control" id="code" name="code" placeholder="Maksukkan kode mitra / mandor" autofocus="autofocus" required>                  
									</div>
									<div class="form-group" style="padding:10px; margin-top:-10px">
										<label>Nama Mitra / Mandor</label>
										<input class="form-control" id="name" name="name" type="text" placeholder="Masukkan nama mitra / mandor" required/>
									</div>
									<div class="form-group" style="padding:10px; margin-top:-10px">
											<label>Posisi</label>
											<select class="form-control" name="role" id="role" required>
												<option value="" disabled selected>- Pilih Posisi -</option>
												<option value="Mitra Buah">Mitra Buah</option>
												<option value="MItra Sayur">Mitra Sayur</option>
												<option value="Mandor">Mandor</option>
											</select>
										</div>
									<div class="form-group" style="padding:10px; margin-bottom:-5px">
										<center><button type="submit" class="btn btn-block btn-primary"><i class="fa fa-floppy-o fa-fw"></i>&nbsp;Simpan</button></center>
									</div>
								</fieldset>
							</form> 
                        </div>
                    </div>
                </div>
            </div>

			<div class="row">
				<div class="col-lg-12">
					<div class="panel panel-primary">
						<div class="panel-heading">
                            <i class="fa fa-user fa-fw"></i> Data Mandor
                        </div>
						<div class="panel-body">
							<div id="Area2">
								<div class="alert alert-info" onclick="clearNotif()">
									<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
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
                            <i class="fa fa-user fa-fw"></i> Data Mitra Buah
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
				<div class="col-lg-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
                            <i class="fa fa-user fa-fw"></i> Data Mitra Sayur
                        </div>
						<div class="panel-body">
							<div id="Area3">
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
	<div id="modalEditLaborer" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Meload modal "checker_modal.php" file -->
            </div>
        </div>
    </div>

	<!-- Modal Delete -->
	<div id="modalDeleteLaborer" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Meload modal "checker_delete_modal.php" file -->
            </div>
        </div>
    </div>

	<script>
    $(document).ready(function(){
			// Auto Refresh Page
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("laborer_table.php",function(){clearNotif();});
			$('#Area2').load("mandor_table.php",function(){clearNotif();});
			$('#Area3').load("laborer2_table.php",function(){clearNotif();});
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
