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
    <title>Laporan Terbayar | PISS</title>
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
                <div class="col-lg-12">
					<div id="Area1">
						<div class="alert alert-info" onclick="clearNotif()">
							<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
						</div>
					</div>
					<input type="hidden" id="value_day1" />
				</div>
                <div class="col-lg-12">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="	fa fa-file-text-o fa-fw"></i> Rincian Tagihan Tonase Terbayar
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportRevenue" name = "reportRevenue" method="POST" target="_blank">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Tanggal Awal</label>
											<input name="date1" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Tanggal Akhir</label>
											<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Jenis Komoditi</label>
											<select class="form-control" name="commodity" id="commodity" required>
												<option value="">- Pilih Jenis Komoditi -</option>
												<option value="Semua">Semua</option>
												<?php 
													$sql2 = mysql_query("select distinct(merchant_type) from merchant");
													while($d2 = mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['merchant_type'];?>"><?php echo $d2['merchant_type'];?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:25px; margin-right:0px;">
												<button type="submit" class="btn btn-primary btn-block"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Print PDF</button>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:25px; margin-left:0px;">
												<button type="submit" class="btn btn-success btn-block" style="background-color: #008000;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Export Excel</button>
											</div>
										</div>
									</div>
								</div>
							</form>
                        </div>
                    </div>
				</div>
                
				<!-- <div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-line-chart fa-fw"></i> Laporan Setoran Pembayaran Tonase
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" action="../htmltopdf/reportRevenue2.php" id="reportRevenue2" name="reportRevenue2" method="POST" target="_blank">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Nama Penerima Uang</label>
											<select class="form-control" name="receiver" id="receiver" required>
												<option value="">- Pilih Penerima Uang -</option>
												<option value="Semua">Semua</option>
												<?php 
													$sql2 = mysql_query("select * from checker order by checker_name");
													while($d2 = mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['checker_id'];?>"><?php echo $d2['checker_name'];?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Tanggal</label>
											<input name="date" max="<?php echo $date_now;?>" type="date" class="form-control" required/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Jam Awal</label>
											<input name="time1" type="time" class="form-control" required/>
										</div>
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Jam Akhir</label>
											<input name="time2" type="time" class="form-control" required/>
										</div>
									</div>
									<div class="form-group" style="padding:5px; margin-left:5px; margin-right:10px; margin-top:30px;">
										<center><button type="submit" class="btn btn-outline btn-primary btn-block">Lihat <i class="fa fa-angle-double-right fa-lg"></i></button></center>
									</div>
								</div>
							</form>
                        </div>
                    </div>
				</div> -->
            </div>
        </div>
    </div>
	<!-- Modal Tonase -->
	<div class="modal fade bs-example-modal-lg" id="modalTonase" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src='img/Delete.png' width='25' height='25'/></button>
					<h4 id="header_tonase">Tonase Timbangan PIOS </h4>
				</div>
				<div class="modal-body">
					<div id="body_tonase" style="margin-bottom: 0px;">
						<div class="alert alert-info" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
					</div>
				</div>
				<div class="modal-footer">
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
					<h4 id="header_outstanding">Tonase Outstanding PIOS </h4>
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
			$('#Area1').load("line_revenue.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',1000); 
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
			//data table Tonase
			$('#tableTonase').dataTable();
			//data table Outstanding
		});
		//Memanggil Modal Tonase
		function callTonase(getDay){
			$("#value_day2").val(getDay);
			$("#body_tonase").load("detail_tonase.php?day="+$("#value_day2").val(),function(){clearNotif();});
			$('#modalTonase').modal('show');
		}
		
		function clearNotif(){$("#notifArea").html("");}
    </script>
	<script>
		function setAction(type) {
			var form = document.getElementById("reportRevenue");
			if (type === 'pdf') {
				form.action = "htmltopdf/reportRevenue.php";
				form.target = "_blank";
			} else if (type === 'excel') {
				form.action = "exportExcel/exportReportRevenue.php";
				form.target = "_self";
			}
		}
	</script>
</body>
</html>