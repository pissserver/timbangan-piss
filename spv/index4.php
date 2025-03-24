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
	<!-- CSS Search dropdown -->
	<link href="../css/plugins/chosen/chosen.css" rel="stylesheet" />
    <title>Retail | PISS</title>
	<?php echo headScript(); ?>
</head>
<body>
    <div id="wrapper">
        <?php include 'components/sidebar.php'; ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
					<br /><br><br><br>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-truck fa-fw"></i> Retail Kendaraan Masuk
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form action="weighing_action.php?task=retail" class="form-horizontal" id="formWeighingIn" name = "formWeighingIn" method="POST">
								<fieldset>
									<div class="col-md-4">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Pedagang</label>
											<select class="form-control chzn-select" name="merchant" id="merchant" required>
												<option value="">- Pilih Pedagang -</option>
												<?php 
													$sql 	= mysql_query("	select 	merchant_id, 
																					merchant_name, 
																					merchant_nick
																			from 	merchant 
																			where 	merchant_status = 'Aktif' and 
																					merchant_type != 'Buah' 
																			order by 	merchant_nick, 
																						merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("	select 	l.lapak_name 
																					from 	rent r, 
																							lapak l 
																					where 	r.lapak_id = l.lapak_id and 
																							r.merchant_id = '".$d1['merchant_id']."' and 
																							r.rent_sts != 'Batal' and 
																							r.rent_sts2 != 'Pindah' limit 1");
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
									</div>
									<div class="col-md-4">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Barang</label>
											<select class="form-control chzn-select" name="goods" id="goods" required>
												<option value="">- Pilih Barang -</option>
												<?php 
													$sql2 		= mysql_query("select goods_id, goods_name from goods order by goods_name");
													while($d2 	= mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['goods_id'];?>"><?php echo $d2['goods_name'];?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Keterangan</label>
											<input id="reason" name="reason" placeholder="Keterangan kendaraan masuk" type="text" class="form-control" required>                  
										</div>
									</div>
									<div class="form-group" style="padding:10px; margin-top:15px; margin-left:0px; margin-right:0px;">
											<center><button type="submit" class="btn btn-primary btn-block">Masuk <i class="fa fa-angle-double-right fa-lg"></i></button></center>
										</div>
								</fieldset>
								</form>
							</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-truck fa-fw"></i> Data Kendaraan Masuk
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<?php 
										$query 	= mysql_query("select * from weighing 
										where (date_in  between '2016-03-15' and '$date_now') and 
										weighing_sts = 'Masuk' order by weighing_id desc");

										if (mysql_num_rows($query) != 0) { ?>
											<div id="Area1">
												<div class="alert alert-info" onclick="clearNotif()">
													<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
												</div>
											</div>
										<?php } else { ?>
											<div class="payment-notfound text-center">
												<img src="../img/logistic.png" alt="Image Payment Not Found" width="250px">
												<h3><b>Belum Ada Kendaraan Masuk</b></h3>
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
			$('#Area1').load("weighing_table.php",function(){clearNotif();});
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
</body>
</html>