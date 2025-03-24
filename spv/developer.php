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
    <title>Developer | PISS</title>
	<?php echo headScript(); ?>
</head>
<body>
    <div id="wrapper">
		<?php include "components/sidebar.php"; ?>

        <div id="page-wrapper">
			<div class="row">
				<br /><br><br><br>
				<div style="margin: 0px 15px;" class="alert alert-danger" role="alert">
					<i class="fa fa-warning" style="display: inline-block;"></i>&nbsp;
					<p style="font-size: 14px; display: inline-block">
						Halaman ini hanya dipergunakan untuk melakukan acc revisi data timbangan salah. Tolong jangan disalah gunakan, Terima kasih!
					</p>
				</div>
			</div>
			<br>
			
			<div class="row">
                <div class="col-lg-12">
                    <ul class="nav nav-tabs nav-justified">
                        <li class="active">
                            <a data-toggle="tab" href="#accept"
                                style="background-color: #428bca; color: #ffffff;">Revisi Disetujui</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#done"
                                style="background-color: transparent; color: #428bca;">Revisi Selesai</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div id="accept" class="tab-pane fade in active">
                            <br />
                            <div class="row">
								<div class="col-lg-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<i class="fa fa-check-square fa-fw"></i> Data Revisi Disetujui
										</div>
										<div class="panel-body">
											<div class="row">
												<div class="col-md-12">
													<?php 
														$query 	= mysql_query("SELECT * FROM revision WHERE status1 = 'Disetujui' AND status2 IS NULL ORDER BY revision_id DESC");

														if (mysql_num_rows($query) != 0) { ?>
															<div id="Area1">
																<div class="alert alert-info" onclick="clearNotif()">
																	<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
																</div>
															</div>
														<?php } else { ?>
															<div class="revision-notfound text-center">
																<img src="../img/analyze.png" alt="Image Revision Not Found" width="250px">
																<h3><b>Belum Ada Pengajuan Revisi Disetujui</b></h3>
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

                        <div id="done" class="tab-pane fade">
                            <br />
                            <div class="row">
								<div class="col-lg-12">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<i class="fa fa-check-square fa-fw"></i> Data Revisi Selesai
										</div>
										<div class="panel-body">
											<div class="row">
												<div class="col-md-12">
													<?php 
														$query 	= mysql_query("SELECT * FROM revision WHERE status2 = 'Selesai' ORDER BY revision_id DESC");

														if (mysql_num_rows($query) != 0) { ?>
															<div id="Area2">
																<div class="alert alert-info" onclick="clearNotif()">
																	<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
																</div>
															</div>
														<?php } else { ?>
															<div class="revision-notfound text-center">
																<img src="../img/analyze.png" alt="Image Revision Not Found" width="250px">
																<h3><b>Belum Ada Pengajuan Revisi Selesai</b></h3>
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

	<!-- Modal Password -->
	<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src='assets/image/Delete.png' width='25' height='25'/></button>
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
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("developer_table_acc.php",function(){clearNotif();});
			$('#Area2').load("developer_table_done.php",function(){clearNotif();});
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
	<script src="../js/plugins/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
    	$(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
    </script>
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
	<script>
        $(document).ready(function() {
            $('.nav-tabs li a').on('click', function() {
                $('.nav-tabs li a').css({'background-color': 'transparent', 'color': '#428bca'}); // Reset semua tab
                $(this).css({'background-color': '#428bca', 'color': '#ffffff'}); // Tab yang aktif
            });
        });
    </script>
</body>
</html>