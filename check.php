<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	$checker 		= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
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
	<link rel="icon" href="img/icon.png">
    <link rel="shortcut icon" href="img/icon.png" />
    <title>Data Sudah Dicek | PISS</title>
	<?php echo headScript(); ?>
</head>
<body >
    <div id="wrapper">
        <?php include 'components/sidebar.php'; ?>
		
		<!--<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
					<br />
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="fa fa-truck fa-fw"></i> Pengecekan Data Timbangan
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
                </div>
            </div>
        </div>-->

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
					<br /><br><br><br>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-check-square-o fa-fw"></i> Data Sudah Dicek
                        </div>
                        <div class="panel-body">
							<div class="row" style=" overflow-x: auto;">
								<div class="col-md-12">
									<div id="Area">
										<!--<div class="alert alert-info">
											<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
										</div>-->
										<table id="transactionView" class="table table-striped table-bordered table-hover" style="font-size:12px;" width="100%">
											<thead>
												<tr>
													<th style="width:5%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NO</th>
													<th style="width:5%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NO NOTA</th>
													<th style="width:7%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NOPOL</th>
													<th style="width:15%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">PEDAGANG</th>
													<th style="width:9%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">MASUK</th>
													<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">KELUAR</th>
													<th style="width:7%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NON KOMODITI</th>
													<th style="width:7%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">RAFAKSI</th>
													<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NETTO</th>
													<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">STATUS</th>
													<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">STATUS MITRA</th>
													<th style="width:10%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">JAM & TGL MASUK</th>
													<th style="width:6%; font-size:12px; text-align:center; vertical-align: middle;" colspan="3">STATUS PRINT</th>
													<th style="width:5%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">AKSI</th>
												</tr>
												<tr>
													<th style="font-size:11px;" class="font-size:12px; text-center; vertical-align: middle;">TIMBANG</th>
													<th style="font-size:11px;" class="font-size:12px; text-center; vertical-align: middle;">CEK</th>
													<th style="font-size:11px;" class="font-size:12px; text-center; vertical-align: middle;">TAGIHAN</th>
												</tr>
											</thead>
											<tbody>	
												
											</tbody>
										</table>
									</div>
								</div>
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
						<div class="alert alert-info" onclick="clearNotif()">
							<h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3>
						</div>
					</div>
					<input type="hidden" id="value_id" />
				</div>
				<div class="modal-footer"></div>
			</div>
		</div>
	</div>
	<!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
    <script src="dataTables/jquery.dataTables.js"></script>
	<script src="dataTables/dataTables.tableTools.js"></script>
	<script src="dataTables/dataTables.bootstrap.js"></script>
	<script>

		$(document).ready(function()
		{
			// Auto Refresh Page
			//$("#notifArea").html(alertLoadProcess);
			/*$('#Area1').load("check_table.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',100);*/ 
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				//$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			});
			dataTable();
		});
		function dataTable()
  		{
			var tabel = $('#transactionView').DataTable({
				//"deferRender": true,
				//"destroy" : true,
				"processing": true,
				"serverSide" : true,
				"ajax": "improve_check.php",
				"createdRow": function ( row, data, index )
				{
					$('td', row).eq(0).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(1).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(2).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(3).addClass('text-left').css('vertical-align', 'middle'),
					$('td', row).eq(4).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(5).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(6).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(7).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(8).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(9).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(10).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(11).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(12).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(13).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(14).addClass('text-center').css('vertical-align', 'middle'),
					$('td', row).eq(15).addClass('text-center').css('vertical-align', 'middle')
				},
				"order": [[ 0, "desc"]],
				});
		}
		function clearNotif(){$("#notifArea").html("");}

		function edit($d)
		{
			window.location.href = 'check_edit.php?id='+$d;
		}
    </script>
	<!-- Sweet Alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
	<?php
		// Menampilkan SweetAlert jika session success ada dan bernilai true
		if (isset($_SESSION['success'])) {
			$message = $_SESSION['message'];
			$icon = $_SESSION['success'] ? 'success' : 'error'; // Menentukan ikon berdasarkan status
			echo "<script>
				Swal.fire({
					title: 'Berhasil Pecah Data',
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