<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	//$date_now	= date("Y-m-d");
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
    <title>Outstanding KO & KM Mitra | PISS</title>
	<?php echo headScript(); ?>
	<script src="dataTables/dataTables.tableTools.css"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
</head>
<body>
    <div id="wrapper">
		<?php include 'components/sidebar.php'; ?>

		<div id="page-wrapper">
			<br /><br><br><br>
            <div class="row">
				<div class="col-lg-6">
					<input type="hidden" id="value_day2" />
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa fa-file-text-o fa-fw"></i> Rincian Outstanding KOM Mitra
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form class="form-horizontal" id="reportOutstandingKO_mitra" name="reportOutstandingKO_mitra" method="POST">
									<div class="row">
										<div class="col-md-6">
											<div class="form-group" style="padding:10px 25px; margin-top:-10px;">
												<label>Nama Kasir</label>
												<select class="form-control" name="cashier" id="cashier" required>
													<option value="">- Pilih Kasir -</option>
													<option value="Semua">Semua</option>
													<?php 
														$sql2 = mysql_query("select * from checker where checker_role = 'cashier' and status_aktif = '1' order by checker_name");
														while($d2 = mysql_fetch_array($sql2))
													{?>
														<option value="<?php echo $d2['checker_id'];?>"><?php echo $d2['checker_name'];?></option>
													<?php } ?>
												</select>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="padding:10px 25px; margin-top:-10px;">
												<label>Tanggal Akhir</label>
												<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
												data-fv-notempty-message="Tanggal Belum Diisi"/>
											</div>
										</div>
									</div>
									
									<div class="row">
										<div class="col-md-6">
											<div class="form-group" style="margin-top:5px; padding: 0 10px; margin-right:0px; margin-left:0px">
												<button type="submit" class="btn btn-primary btn-block"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Print PDF</button>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:5px; padding: 0 10px; margin-left:0px; margin-right: 0px">
												<button type="submit" class="btn btn-success btn-block" style="background-color: #008000;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Export Excel</button>
											</div>
										</div>
									</div>
								</form>
							</div>
                        </div>
                    </div>
				</div>
				<div class="col-lg-6">
					<input type="hidden" id="value_day2" />
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa fa-file-text-o fa-fw"></i> Rincian Outstanding KMM Mitra
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form class="form-horizontal" id="reportOutstandingKM_mitra" name="reportOutstandingKM_mitra" method="POST">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group" style="padding:10px 25px; margin-top:-10px;">
												<label>Nama Kasir</label>
												<select class="form-control" name="cashier" id="cashier" required>
													<option value="">- Pilih Kasir -</option>
													<option value="Semua">Semua</option>
													<?php 
														$sql2 = mysql_query("select * from checker where checker_role = 'cashier' and status_aktif = '1' order by checker_name");
														while($d2 = mysql_fetch_array($sql2))
													{?>
														<option value="<?php echo $d2['checker_id'];?>"><?php echo $d2['checker_name'];?></option>
													<?php } ?>
												</select>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="row">
											<div class="col-md-6">
												<div class="form-group" style="margin-top:5px; padding: 0 15px 0 25px; margin-right:0px; margin-left:0px">
													<button type="submit" class="btn btn-primary btn-block"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Print PDF</button>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" style="margin-top:5px; padding: 0 25px 0 15px; margin-left:0px; margin-right: 0px">
													<button type="submit" class="btn btn-success btn-block" style="background-color: #008000;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Export Excel</button>
												</div>
											</div>
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
	<!-- Modal Outstanding -->
	<div class="modal fade bs-example-modal-lg" id="modalOutstanding" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src='img/Delete.png' width='25' height='25'/></button>
					<h4 id="header_outstanding">Tonase Outstanding PISS </h4>
				</div>
				<div class="modal-body">
					<div id="body_outstanding" style="margin-bottom: 0px;">
						<div class="alert alert-info" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
					</div>
				</div>
				<div class="modal-footer">
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
    
	<!-- jQuery -->
    <script src="js/jquery.js"></script>
	<script src="dataTables/jquery.dataTables.js"></script>
	<script src="dataTables/dataTables.tableTools.js"></script>
	<script src="dataTables/dataTables.bootstrap.js"></script>
	<!-- highcharts -->
	<script src="highcharts/js/highcharts.js"></script>
	<script src="highcharts/js/highcharts-more.js"></script>
	
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
	<script>
		$(document).ready(function(){
			$("#notifArea").html(alertLoadProcess);
			setTimeout('$("html").removeClass("js")',1000); 
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
			//data table Tonase
			$('#tableOutstanding').dataTable();
		});
		//Memanggil Modal Outstanding
		function callOutstanding(getDay){
			$("#value_day1").val(getDay);
			$("#body_outstanding").load("detail_outstanding.php?day="+$("#value_day1").val(),function(){clearNotif();});
			$('#modalOutstanding').modal('show');
		}
		function clearNotif(){$("#notifArea").html("");}
    </script>
	<script>
		function setAction(type) {
			var form1 = document.getElementById("reportOutstandingKO_mitra");
			var form2 = document.getElementById("reportOutstandingKM_mitra");
			if (type === 'pdf') {
				form1.action = "htmltopdf/reportOutstandingKO_mitra.php";
				form2.action = "htmltopdf/reportOutstandingKM_mitra.php";
				form1.target = "_blank";
				form2.target = "_blank";
			} else if (type === 'excel') {
				form1.action = "exportExcel/exportReportOutstandingKOMitra.php";
				form2.action = "exportExcel/exportReportOutstandingKMMitra.php";
				form1.target = "_self";
				form2.target = "_self";
			}
		}
	</script>
</body>
</html>