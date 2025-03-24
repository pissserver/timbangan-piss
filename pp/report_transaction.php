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
    <title>Laporan Timbang | PISS</title>
	<?php echo headScript(); ?>
	<script src="../dataTables/dataTables.tableTools.css"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
</head>
<body>
    <div id="wrapper">
        <?php include 'components/sidebar.php'; ?>

        <div id="page-wrapper">
			<br /><br><br><br>
            <div class="row">
                <div class="col-lg-12">
                    <div id="Area1">
						<div class="alert alert-info" onclick="clearNotif()">
							<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
						</div>
					</div>
					<input type="hidden" id="value_day1" />
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Laporan Transaksi Timbang Harian
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportOmzetBlock" name = "reportOmzetBlock" method="POST">
							<fieldset>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Tanggal Awal</label>
											<input name="date1" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Tanggal Akhir</label>
											<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="margin-top:-10px; margin-left:0px; margin-right:0px;">
											<button type="submit" class="btn btn-primary btn-block"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Print PDF</button>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="margin-top:-10px; margin-left:0px; margin-right:0px;">
											<button type="submit" class="btn btn-success btn-block" style="background-color: #008000;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Export Excel</button>
										</div>
									</div>
								</div>
							</fieldset>
							</form>
                        </div>
                    </div>
				</div>
            </div>
        </div>
    </div>

    
	<!-- jQuery -->
    <script src="../js/jquery.js"></script>
	<script src="../dataTables/jquery.dataTables.js"></script>
	<script src="../dataTables/dataTables.tableTools.js"></script>
	<script src="../dataTables/dataTables.bootstrap.js"></script>
	<!-- highcharts -->
	<script src="../highcharts/js/highcharts.js"></script>
	<script src="../highcharts/js/highcharts-more.js"></script>
	
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/sb-admin-2.js"></script>
	<script>
		$(document).ready(function(){
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("../line_transaction.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',1000);
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
			//data table Tonase
			$('#tableTonase').dataTable();
		});
		//Memanggil Modal Tonase
		function callTonase(getDay){
			$("#value_day2").val(getDay);
			$("#body_tonase").load("../detail_tonase.php?day="+$("#value_day2").val(),function(){clearNotif();});
			$('#modalTonase').modal('show');
		}
		function clearNotif(){$("#notifArea").html("");}
    </script>
	<script>
		function setAction(type) {
			var form = document.getElementById("reportOmzetBlock");
			if (type === 'pdf') {
				form.action = "../htmltopdf/reportOmzetDaily.php";
				form.target = "_blank";
			} else if (type === 'excel') {
				form.action = "../exportExcel/exportReportOmzetDaily.php";
				form.target = "_self";
			}
		}
	</script>
</body>
</html>