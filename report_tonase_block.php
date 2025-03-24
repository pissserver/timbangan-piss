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
	<link rel="icon" href="../images/icon.png">
    <link rel="shortcut icon" href="../images/icon.png" />
    <title>Laporan Tonase Blok | PISS</title>
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
				</div>
				<div class="col-lg-6">
					<input type="hidden" id="value_day1" />
					<div class="panel panel-info">
                        <div class="panel-heading">
                            <i class="fa fa-line-chart fa-fw"></i> Laporan Tonase Per Blok
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" action="htmltopdf/reportOmzetBlock.php" id="reportOmzetBlock" name="reportOmzetBlock" method="POST" target="_blank">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Tanggal Awal</label>
											<input name="date1" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Tanggal Akhir</label>
											<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Blok</label>
											<select class="form-control" name="block" id="block" required>
												<option value="">- Pilih Block -</option>
												<?php
													$sql = mysql_query("select block_id from block ");
													while($data = mysql_fetch_array($sql)){?>
													<option value="<?php echo $data['block_id'];?>"><?php echo $data['block_id'];?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Urutkan Berdasarkan</label>
											<select class="form-control" name="sortir" id="sortir" required>
												<option value="tonase">Tonase</option>
												<option value="lapak">Lapak</option>
											</select>
										</div>
									</div>
									<div class="col-md-12" style="margin-top:0px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px;">
											<center><button type="submit" class="btn btn-outline btn-primary btn-block">Lihat <i class="fa fa-angle-double-right fa-lg"></i></button></center>
										</div>
									</div>
								</div>
							</form>
                        </div>
                    </div>
				</div>
				<div class="col-lg-6">
					
				</div>
            </div>
        </div>
    </div>
	<!-- Modal Tonase-->
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
			$('#Area1').load("line_tonase_block.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',1000); 
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
			//data table Tonase
			
			$('#tableOutstanding').dataTable();
			$('#tableTonase').dataTable();
			//data table Outstanding
		});
		//Memanggil Modal Outstanding
		function callOutstanding(getDay){
			$("#value_day1").val(getDay);
			$("#body_outstanding").load("detail_outstanding.php?day="+$("#value_day1").val(),function(){clearNotif();});
			$('#modalOutstanding').modal('show');
		}
		//Memanggil Modal Tonase
		function callTonase(getDay){
			$("#value_day2").val(getDay);
			$("#body_tonase").load("detail_tonase.php?day="+$("#value_day2").val(),function(){clearNotif();});
			$('#modalTonase').modal('show');
		}
		
		function clearNotif(){$("#notifArea").html("");}
    </script>
</body>
</html>