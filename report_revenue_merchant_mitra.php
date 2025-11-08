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
	<!-- CHOSEN CSS -->
	<link rel="stylesheet" href="../css/plugins/chosen/chosen.css">
    <title>Laporan Mitra Terbayar Per-Pedagang | PISS</title>
	<?php echo headScript(); ?>
	<script src="dataTables/dataTables.tableTools.css"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
</head>
<body>
    <div id="wrapper">
		<?php include 'components/sidebar.php'; ?>

        <div id="page-wrapper">
			<br />
			<br><br><br>
            <div class="row">
                <div class="col-lg-12">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Rincian Tagihan Mitra Terbayar Per Pedagang
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form class="form-horizontal"  id="reportOmzetMerchant" name="reportOmzetMerchant" method="POST" target="_blank">
								<fieldset>
									<div class="col-md-6" style="margin-top:-10px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px 15px; margin-top:0px;">
											<label>Tanggal Awal</label>
											<input name="date1" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
										<div class="form-group" style="padding:10px 15px; margin-top:-20px;">
											<label>Tanggal Akhir</label>
											<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6" style="margin-top:-10px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px 15px; margin-top:0px;">
											<label>Pedagang</label>
											<select class="form-control chzn-select" name="merchant" id="merchant">
												<option value="" selected disabled>- Pilih Pedagang -</option>
												<?php 
													$sql1 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant order by merchant_name");
													while($d1 = mysql_fetch_array($sql1)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."' and (rent_sts != 'Batal' and rent_sts2 not in ('Batal', 'Pindah')) limit 1");
														$nick	= $d1['merchant_nick'];
														if($nick!=''){
															$nick = $nick.' / ';
														}else{
															$nick = '';
														}
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.($lpk != NULL ? $lpk : 'Tidak Ada Lapak');?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:15px; margin-right:0px;">
												<button type="submit" class="btn btn-primary btn-block"  onclick="setAction('pdf')"><i class="fa fa-file-pdf-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Print PDF</button>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group" style="margin-top:15px; margin-left:0px;">
												<button type="submit" class="btn btn-success btn-block" style="background-color: #008000;" onclick="setAction('excel')"><i class="fa fa-file-excel-o fa-lg" style="font-size: 16px;"></i>&nbsp;&nbsp;Export Excel</button>
											</div>
										</div>
									</div>
								</fieldset>
								</form>
							</div>
                        </div>
                    </div>
					<!-- <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Rekap Pembayaran Mitra Per Pedagang (Terbayar)
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form class="form-horizontal" action="../htmltopdf/reportMerchantPay_mitra.php" id="reportOmzetMerchant" name="reportOmzetMerchant" method="POST" target="_blank">
								<fieldset>
									<div class="col-md-6" style="margin-top:-10px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; margin-top:0px;">
											<label>Tanggal Awal</label>
											<input name="date1" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
										<div class="form-group" style="padding:10px; margin-top:-20px;">
											<label>Tanggal Akhir</label>
											<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6" style="margin-top:-10px;margin-bottom:-10px;">
										<div class="form-group" style="padding:10px; margin-top:0px;">
											<label>Pedagang</label>
											<select class="form-control" name="merchant" id="merchant">
												<option value="">- Pilih Pedagang -</option>
												<?php 
													$sql1 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant order by merchant_name");
													while($d1 = mysql_fetch_array($sql1)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."' limit 1");
														$nick	= $d1['merchant_nick'];
														if($nick!=''){
															$nick = $nick.' / ';
														}else{
															$nick = '';
														}
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
										</div>
										<div class="form-group" style="padding:10px; margin-top:20px;">
											<center><button type="submit" class="btn btn-outline btn-primary btn-block">Lihat <i class="fa fa-angle-double-right fa-lg"></i></button></center>
										</div>
									</div>
								</fieldset>
								</form>
							</div>
                        </div>
                    </div> -->

					<div class="panel panel-success">
                        <div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Rekap Tagihan Mitra Terbayar Seluruh Pedagang
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form class="form-horizontal" id="reportOmzetMerchantAll" name="reportOmzetMerchant" method="POST" target="_blank">
								<fieldset>
									<div class="col-md-6" style="margin-top:-10px;">
										<div class="form-group" style="padding:10px; margin-top:0px;">
											<label>Tanggal Awal</label>
											<input name="date1" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
										<div class="form-group" style="padding:10px 15px; margin-top:-20px;">
											<label>Tanggal Akhir</label>
											<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6" style="margin-top:-10px;">
										<div class="form-group" style="padding:10px 15px; margin-top:0px;">
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
										<div class="form-group" style="padding:10px 15px; margin-top:-20px;">
											<label>Sortir Berdasarkan</label>
											<select class="form-control" name="sortir" id="sortir">
												<option value="name">Nama</option>
												<option value="tot desc">Jml Nota</option>
												<option value="total desc">Rupiah</option>
											</select>
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
								</fieldset>
								</form>
							</div>
                        </div>
                    </div>
				</div>
				<!-- <div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Rekap Pembayaran Mitra Seluruh Pedagang (Terbayar)
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form class="form-horizontal" action="../htmltopdf/reportMerchantPayAll_mitra.php" id="reportOmzetMerchant" name="reportOmzetMerchant" method="POST" target="_blank">
								<fieldset>
									<div class="col-md-6" style="margin-top:-10px;">
										<div class="form-group" style="padding:10px; margin-top:0px;">
											<label>Tanggal Awal</label>
											<input name="date1" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
										<div class="form-group" style="padding:10px; margin-top:-20px;">
											<label>Tanggal Akhir</label>
											<input name="date2" max="<?php echo $date_now;?>" type="date" class="form-control" placeholder="DD/MM/YYYY" required
											data-fv-notempty-message="Tanggal Belum Diisi"/>
										</div>
									</div>
									<div class="col-md-6" style="margin-top:-10px;">
										<div class="form-group" style="padding:10px; margin-top:0px;">
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
										<div class="form-group" style="padding:10px; margin-top:-20px;">
											<label>Sortir Berdasarkan</label>
											<select class="form-control" name="sortir" id="sortir">
												<option value="name">Nama</option>
												<option value="tot desc">Jml Nota</option>
												<option value="total desc">Rupiah</option>
											</select>
										</div>
									</div>
									<div class="form-group" style="padding:10px; margin-top:0px; margin-bottom:0px; margin-left:0px; margin-right:0px;">
										<center><button type="submit" class="btn btn-outline btn-primary btn-block">Lihat <i class="fa fa-angle-double-right fa-lg"></i></button></center>
									</div>
								</fieldset>
								</form>
							</div>
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
			$('#Area2').load("line_revenue2.php",function(){clearNotif();});
			//$('#Area2').load("line_omzet_merchant.php",function(){clearNotif();});
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
	<script src="js/plugins/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
		$(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
	</script>
	<script>
		function setAction(type) {
			var form1 = document.getElementById("reportOmzetMerchant");
			var form2 = document.getElementById("reportOmzetMerchantAll");
			if (type === 'pdf') {
				form1.action = "htmltopdf/reportMerchantPay_mitra.php";
				form2.action = "htmltopdf/reportMerchantPayAll_mitra.php";
				form1.target = "_blank";
				form2.target = "_blank";
			} else if (type === 'excel') {
				form1.action = "exportExcel/exportReportMerchantPayMitra.php";
				form2.action = "exportExcel/exportReportMerchantPayAllMitra.php";
				form1.target = "_self";
				form2.target = "_self";
			}
		}
	</script>
</body>
</html>