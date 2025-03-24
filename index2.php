<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
	
	$checker 	= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	$id		= $_GET['id'];
	$edit	= mysql_query("select * from weighing where weighing_id = '".$id."'");
	while ($r = mysql_fetch_array($edit)){
		$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
		$nick	= executeScalar("select merchant_nick from merchant where merchant_id = '".$r['merchant_id']."'");
		$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
		where r.lapak_id = l.lapak_id and r.merchant_id = '".$r['merchant_id']."'
		and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
		$goods 	= executeScalar("select goods_name from goods where goods_id='".$r['goods_id']."'");
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
    <title>Timbangan Keluar | PISS</title>
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
						<div class="row">
								<div class="col-md-6" style="padding-top: 8px;">
									<i class="fa fa-truck fa-fw"></i> Kendaraan Keluar
								</div>
								<div class="col-md-6 text-right">
									<a style="text-decoration: none;" class="modalWeightOut2" data-toggle="modal" data-target="#modalWeightOut2" data-id="<?php echo $r['weighing_id']; ?>">
										<button style="border: 1px solid #fff;" class="btn btn-primary"><i class="fa fa-arrow-circle-up"></i>&nbsp;Keluar Manual</button>
									</a>
								</div>
							</div>
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form action="weighing_action.php?task=keluar&id=<?php echo $id; ?>" class="form-horizontal" id="formTransOut" name = "formTransOut" method="POST" >
									<div class="row">
										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px;">
												<label>Nopol</label>
												<input type="text" class="form-control" id="nopol" name="nopol" value="<?php echo $r['nopol']; ?>" required readonly>                  
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px; ">
												<label>Pedagang</label>
												<select class="form-control" name="merchant" id="merchant" readonly>
													<option value="<?php echo $r['merchant_id']; ?>" required><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.$lpk; ?></option>
													<?php 
														$sql = mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
														while($d1 = mysql_fetch_array($sql)){
															$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
																					where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
																					and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
															$nick2	= executeScalar("select merchant_nick from merchant where merchant_id = '".$d1['merchant_id']."'");
															if($nick2!=''){
																$nick2 = $nick2.' /  ';
															}else{
																$nick2 = '';
															} ?>
														<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
													<?php } ?>
												</select>
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-right:10px;">
												<label>Barang</label>
												<select class="form-control" name="goods" id="goods" readonly required>
													<option value="<?php echo $r['goods_id']; ?>" required><?php echo $goods; ?></option>
														<?php
															$sql 		= mysql_query("select goods_id, goods_name from goods order by goods_name");
															while($data = mysql_fetch_array($sql))
														{?>
															<option value="<?php echo $data['goods_id'];?>"><?php echo $data['goods_name'];?></option>
														<?php } ?>
												</select>
											</div>
										</div>

										<!-- <div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-right:10px; margin-top:15px;">
												<button onclick="ubah()" type="button" class="btn btn-block btn-outline btn-primary">
													<i class="fa fa-edit fa-lg"></i> Ubah 
												</button>
											</div>
										</div> -->
									</div>

									<div class="row">
										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left: 10px;">
												<label>Berat Kendaraan Masuk</label>
												<input id="weight_in" name="weight_in" value="<?php echo $r['weight_in']; ?> Kg" type="text" style="text-align: left;" class="form-control"  disabled>
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Jam & Tanggal Masuk</label>
												<input id="weight_in" name="weight_in" value="<?php echo date("H:i", strtotime($r['time_in'])). ' | ' .date("d M Y", strtotime($r['date_in'])); ?>" type="text" style="text-align: left;" class="form-control"  disabled>
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-right:10px; margin-top:15px;">
												<button type="submit" class="btn btn-block  btn-primary">
													Keluar <i class="fa fa-angle-double-right fa-lg"></i>
												</button>
											</div>
										</div>
									</div>
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
        </div>
    </div>
	
	<!-- Modal Weight Out -->
	<div class="modal fade" id="modalWeightOut2" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			    <!-- Modal Content modal_weight_out2.php -->
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
			// Auto Refresh Page
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("weighing_table.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',100); 
			
			// Auto Refresh Page
			/*$.ajaxSetup({ cache: false }); // This part addresses an IE bug.  without it, IE will only load the first number and will never refresh
			setInterval(function() {
				$('#weight_out').load('random.php');
			}, 1000);*/
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
		});
		function clearNotif(){$("#notifArea").html("");}
		//Memanggil modal nonkomoditi
		function pecahNota(){
			$('#modalPecahNota').modal('show');
		}
		//Memanggil fungsi gabung nota
		function merge(){
			window.location=('weighing_action.php?task=merge&id=<?php echo $r['merchant_id']; ?>');
		}
		function breakdown(){
			window.location=('weighing_action.php?task=breakdown&id=<?php echo $id; ?>');
		}
		function clearNotif(){$("#notifArea").html("");}
    </script>
	<script>
		const selectMerchant = document.getElementById('merchant');
		const selectGoods = document.getElementById('goods');

		selectMerchant.addEventListener('mousedown', function(event){
			event.preventDefault();
		});

		selectGoods.addEventListener('mousedown', function(event){
			event.preventDefault();
		});
	</script>
	<script>
		$(document).ready(function() {
			$('.modalWeightOut2').on('click', function() {
				var weighingId = $(this).data('id'); 

				$.ajax({
					url: 'modal_weight_out2.php',
					type: 'GET',
					data: { id: weighingId }, 
					success: function(response) {
						$('#modalWeightOut2 .modal-content').html(response);
						$('#modalWeightOut2').modal('show');
					}
				});
			});
		});
	</script>
    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
	<script src="js/plugins/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
    	$(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
    </script>
</body>
</html>
<?php } ?>