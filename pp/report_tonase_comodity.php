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
	<!-- CHOSEN CSS -->
	<link rel="stylesheet" href="../css/plugins/chosen/chosen.css">
    <title>Laporan Tonase Per-Komoditi | PISS</title>
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
                <div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Rincian Pendapatan Tonase Per Komoditi
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportTonaseCommodity" name = "reportTonaseCommodity" method="POST">
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
											<label>Komoditi</label>
											<select class="form-control chzn-select" name="goods" id="goods" required>
												<option value="" selected disabled>- Pilih Komoditi -</option>
												<?php 
													$sql2 = mysql_query("select goods_id, goods_name from goods order by goods_name");
													while($d2 = mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['goods_id'];?>"><?php echo $d2['goods_name'];?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:25px; margin-right: 1px;">
												<button type="submit" class="btn btn-primary btn-block" style="font-size: 12px;"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 12px;"></i>&nbsp;&nbsp;Print PDF</button>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:25px;">
												<button type="submit" class="btn btn-success btn-block" style="background-color: #008000; font-size: 12px;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 12px;"></i>&nbsp;&nbsp;Export Excel</button>
											</div>
										</div>
									</div>
								</div>
							</form>
                        </div>
                    </div>

					<div class="panel panel-success">
                        <div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Rekap Pendapatan Tonase Bulanan Per Komoditi
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportCommodityMonthly" name = "reportCommodityMonthly" method="POST">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Bulan & Tahun Awal</label>
											<div class="row">
												<div class="col-lg-6">
													<select class="form-control" name="month1" id="month1" required>
														<option value="">- Bulan -</option>
														<option value="1">Januari</option>
														<option value="2">Februari</option>
														<option value="3">Maret</option>
														<option value="4">April</option>
														<option value="5">Mei</option>
														<option value="6">Juni</option>
														<option value="7">Juli</option>
														<option value="8">Agustus</option>
														<option value="9">September</option>
														<option value="10">Oktober</option>
														<option value="11">November</option>
														<option value="12">Desember</option>
													</select>
												</div>
												<div class="col-lg-6">
													<select class="form-control" name="year1" id="year1" required>
														<option value="">- Tahun -</option>
													<?php 
														$sql3 = mysql_query("select distinct(year(date_check)) as year from weighing where date_check is not null order by year");
														while($d3 = mysql_fetch_array($sql3)){
													?>
														<option value="<?php echo $d3['year'];?>"><?php echo $d3['year'];?></option>
													<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Jenis Komoditi</label>
											<select class="form-control" name="type" id="type">
												<option value="">- Pilih Jenis Komoditi -</option>
												<option value="Semua">Semua</option>
												<?php 
													$sql2 = mysql_query("select distinct(goods_type) from goods where goods_type != '' ");
													while($d2 = mysql_fetch_array($sql2)){
												?>
													<option value="<?php echo $d2['goods_type'];?>"><?php echo $d2['goods_type'];?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Bulan & Tahun Akhir</label>
											<div class="row">
												<div class="col-lg-6">
													<select class="form-control" name="month2" id="month2" required>
														<option value="">- Bulan -</option>
														<option value="1">Januari</option>
														<option value="2">Februari</option>
														<option value="3">Maret</option>
														<option value="4">April</option>
														<option value="5">Mei</option>
														<option value="6">Juni</option>
														<option value="7">Juli</option>
														<option value="8">Agustus</option>
														<option value="9">September</option>
														<option value="10">Oktober</option>
														<option value="11">November</option>
														<option value="12">Desember</option>
													</select>
												</div>
												<div class="col-lg-6">
													<select class="form-control" name="year2" id="year2" required>
														<option value="">- Tahun -</option>
													<?php 
														$sql3 = mysql_query("select distinct(year(date_check)) as year from weighing where date_check is not null order by year");
														while($d3 = mysql_fetch_array($sql3)){
													?>
														<option value="<?php echo $d3['year'];?>"><?php echo $d3['year'];?></option>
													<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:25px; margin-right: 1px;">
												<button type="submit" class="btn btn-primary btn-block" style="font-size: 12px;"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 12px;"></i>&nbsp;&nbsp;Print PDF</button>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:25px;">
												<button type="submit" class="btn btn-success btn-block" style="background-color: #008000; font-size: 12px;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 12px;"></i>&nbsp;&nbsp;Export Excel</button>
											</div>
										</div>
									</div>
								</div>
							</form>
                        </div>
                    </div>
				</div>
				<div class="col-lg-6">
					<div class="panel panel-success">
                        <div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Rekap Pendapatan Tonase Semua Komoditi
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportCommodityAll" name = "reportCommodityAll" method="POST">
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
										<div class="form-group" style="margin-top:5px; margin-right:0px; margin-left:0px">
											<button type="submit" class="btn btn-primary btn-block"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Print PDF</button>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="margin-top:5px; margin-left:0px; margin-right: 0px">
											<button type="submit" class="btn btn-success btn-block" style="background-color: #008000;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Export Excel</button>
										</div>
									</div>
								</div>
							</form>
                        </div>
                    </div>
										
					<!-- <div class="panel panel-success">
                        <div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Rekap Pendapatan Tonase Per Komoditi Selisih Pertahun
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" action="../htmltopdf/reportCommodity2.php" id="reportCommodity" name = "reportCommodity" method="POST" target="_blank">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Bulan & Tahun Awal</label>
											<div class="row">
												<div class="col-lg-6">
													<select class="form-control" name="month1" id="month1" required>
														<option value="">- Bulan -</option>
														<option value="1">Januari</option>
														<option value="2">Februari</option>
														<option value="3">Maret</option>
														<option value="4">April</option>
														<option value="5">Mei</option>
														<option value="6">Juni</option>
														<option value="7">Juli</option>
														<option value="8">Agustus</option>
														<option value="9">September</option>
														<option value="10">Oktober</option>
														<option value="11">November</option>
														<option value="12">Desember</option>
													</select>
												</div>
												<div class="col-lg-6">
													<select class="form-control" name="year1" id="year1" required>
														<option value="">- Tahun -</option>
													<?php 
														$sql3 = mysql_query("select distinct(year(date_check)) as year from weighing where date_check is not null order by year");
														while($d3 = mysql_fetch_array($sql3)){
													?>
														<option value="<?php echo $d3['year'];?>"><?php echo $d3['year'];?></option>
													<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Jenis Komoditi</label>
											<select class="form-control" name="type" id="type">
												<option value="">- Pilih Jenis Komoditi -</option>
												<option value="Semua">Semua</option>
												<?php 
													$sql2 = mysql_query("select distinct(goods_type) from goods");
													while($d2 = mysql_fetch_array($sql2)){
												?>
													<option value="<?php echo $d2['goods_type'];?>"><?php echo $d2['goods_type'];?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Bulan & Tahun Akhir</label>
											<div class="row">
												<div class="col-lg-6">
													<select class="form-control" name="month2" id="month2" required>
														<option value="">- Bulan -</option>
														<option value="1">Januari</option>
														<option value="2">Februari</option>
														<option value="3">Maret</option>
														<option value="4">April</option>
														<option value="5">Mei</option>
														<option value="6">Juni</option>
														<option value="7">Juli</option>
														<option value="8">Agustus</option>
														<option value="9">September</option>
														<option value="10">Oktober</option>
														<option value="11">November</option>
														<option value="12">Desember</option>
													</select>
												</div>
												<div class="col-lg-6">
													<select class="form-control" name="year2" id="year2" required>
														<option value="">- Tahun -</option>
													<?php 
														$sql3 = mysql_query("select distinct(year(date_check)) as year from weighing where date_check is not null order by year");
														while($d3 = mysql_fetch_array($sql3)){
													?>
														<option value="<?php echo $d3['year'];?>"><?php echo $d3['year'];?></option>
													<?php } ?>
													</select>
												</div>
											</div>
										</div>
										<div class="form-group" style="padding:10px 15px; margin-top:30px;">
											<center><button type="submit" class="btn btn-outline btn-success btn-block">Lihat <i class="fa fa-angle-double-right fa-lg"></i></button></center>
										</div>
									</div>
								</div>
							</form>
                        </div>
                    </div> -->
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
						<div class="alert alert-primary" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
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
						<div class="alert alert-primary" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
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
						<div class="alert alert-primary" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
					</div>
					<input type="hidden" id="value_id" />
				</div>
				<div class="modal-footer">
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
			$('#Area1').load("../line_tonase.php",function(){clearNotif();});
			$('#Area2').load("../line_tonase_merchant.php",function(){clearNotif();});
			$('#Area3').load("../line_omzet_block.php",function(){clearNotif();});
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
			$("#body_outstanding").load("../detail_outstanding.php?day="+$("#value_day1").val(),function(){clearNotif();});
			$('#modalOutstanding').modal('show');
		}
		//Memanggil Modal Tonase
		function callTonase(getDay){
			$("#value_day2").val(getDay);
			$("#body_tonase").load("../detail_tonase.php?day="+$("#value_day2").val(),function(){clearNotif();});
			$('#modalTonase').modal('show');
		}
		
		function clearNotif(){$("#notifArea").html("");}
    </script>
	<script src="../js/plugins/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
		$(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
	</script>
		<script>
		function setAction(type) {
			var form1 = document.getElementById("reportTonaseCommodity");
			var form2 = document.getElementById("reportCommodityMonthly");
			var form3 = document.getElementById("reportCommodityAll");
			if (type === 'pdf') {
				form1.action = "../htmltopdf/reportTonase2.php";
				form2.action = "../htmltopdf/reportCommodity.php";
				form3.action = "../htmltopdf/reportTonase3.php";
				form1.target = "_blank";
				form2.target = "_blank";
				form3.target = "_blank";
			} else if (type === 'excel') {
				form1.action = "../exportExcel/exportReportTonase2.php";
				form2.action = "../exportExcel/exportReportCommodity.php";
				form3.action = "../exportExcel/exportReportTonase3.php";
				form1.target = "_self";
				form2.target = "_self";
				form3.target = "_self";
			}
		}
	</script>
</body>
</html>