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
    <title>Laporan KO & KM Tonase | PISS</title>
	<?php echo headScript(); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="../js/jquery.min.js"></script>
	<script>
		/* hide all elements & show preloader*/
		document.documentElement.className += 'js';

		/* display variabel */
		var alertLoadProcess = "<span class='alert alert-danger' onclick='clearNotif()'><i class='fa fa-spinner fa-spin'></i> Loading...</span>";
    </script>
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
                <div class="col-lg-6">
					<div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Rincian Tagihan Tonase Terbayar Per ID KO 
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportRevenue" name = "reportRevenue" method="POST">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Kasir Operasional (KO)</label>
											<select class="form-control chzn-select" name="ko" id="ko" required>
												<option value="" selected disabled>- Pilih ID KO -</option>
												<?php 
													$sql2 = mysql_query("select ko_id from ko order by ko_id desc ");
													while($d2 = mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['ko_id'];?>">KO<?php echo $d2['ko_id'];?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
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
					<div class="panel panel-success">
                        <div class="panel-heading" style="background: green; color: #fff;">
                            <i class="fa fa-file-text-o fa-fw"></i> Rekap Tagihan Tonase Terbayar Per ID KO 
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportKO3" name="reportKO3" method="POST">
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
								</div>

								<div class="row">
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
				</div>
				<div class="col-lg-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Rincian Tagihan Tonase Terbayar Per ID KM
                        </div>
                        <div class="panel-body">
							<form class="form-horizontal" id="reportKM" name = "reportKM" method="POST">
								<div class="row">
									<div class="col-md-12">
										<div class="form-group" style="padding:10px 15px; margin-top:-10px;">
											<label>Kasir Management (KM)</label>
											<select class="form-control chzn-select" name="km" id="km" required>
												<option value="" selected disabled>- Pilih ID KM -</option>
												<?php 
													$sql2 = mysql_query("select km_id from km order by km_id desc ");
													while($d2 = mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['km_id'];?>">KM<?php echo $d2['km_id'];?></option>
												<?php } ?>
											</select>
										</div>
									</div>
								</div>

								<div class="row">
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
			$('#Area1').load("../line_revenue.php",function(){clearNotif();});
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
			var form1 = document.getElementById("reportRevenue");
			var form2 = document.getElementById("reportKO3");
			var form3 = document.getElementById("reportKM");
			if (type === 'pdf') {
				form1.action = "../htmltopdf/reportKO.php";
				form2.action = "../htmltopdf/reportKO3.php";
				form3.action = "../htmltopdf/reportKM.php";
				form1.target = "_blank";
				form2.target = "_blank";
				form3.target = "_blank";
			} else if (type === 'excel') {
				form1.action = "../exportExcel/exportReportKO.php";
				form2.action = "../exportExcel/exportReportKO3.php";
				form3.action = "../exportExcel/exportReportKM.php";
				form1.target = "_self";
				form2.target = "_self";
				form3.target = "_self";
			}
		}
	</script>
</body>
</html>