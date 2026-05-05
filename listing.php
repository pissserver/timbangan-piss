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

	$id			= $_GET['id_nota'];
	$edit		= mysql_query("select * from weighing where weighing_id = '".$id."'");
?>

<style>
	/* Reference Cards */
		/* CARD BASE */
		.ref-card-active {
			width: 100%;
			background: linear-gradient(135deg, #eef2ff, #f8fafc);
			border: 1px solid #e0e7ff;
			border-radius: 14px;
			padding: 5px 16px;

			display: flex;
			justify-content: space-between;
			align-items: center;

			transition: 0.2s;
		}

		.ref-card-active:hover {
			transform: translateY(-2px);
		}

		/* EMPTY */
		.ref-card-empty {
			width: 100%;
			background: #f8fafc;
			border: 1px dashed #e2e8f0;
			border-radius: 14px;
			padding: 5px 16px;

			display: flex;
			align-items: center;
			gap: 12px;
		}

		/* CONTENT */
		.ref-content {
			display: flex;
			align-items: center;
			gap: 12px;
		}

		/* ICON */
		.ref-icon {
			width: 30px;
			height: 30px;
			background: #4f46e5;
			color: white;
			border-radius: 10px;

			display: flex;
			align-items: center;
			justify-content: center;
		}

		.ref-icon-empty {
			width: 30px;
			height: 30px;
			background: #e2e8f0;
			color: #64748b;
			border-radius: 10px;

			display: flex;
			align-items: center;
			justify-content: center;
		}

		/* TEXT */
		.ref-title {
			font-weight: 600;
			font-size: 14px;
		}

		/* BUTTON */
		.btn-ref {
			background: #4f46e5;
			color: #fff;
			border: none;
			border-radius: 20px !important;
		}

		.btn-ref:hover {
			background: #4338ca;
		}

		/* Modal Reference */
		.modal-modern-ref {
			border-radius: 16px;
			overflow: hidden;
			border: none;
		}

		/* HEADER */
		.modal-ref-header {
			background: linear-gradient(135deg, #1e293b, #0f172a);
			color: #fff;
			padding: 18px 20px;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.header-left {
			display: flex;
			align-items: center;
			gap: 12px;
		}

		.text {
			display: flex;
			flex-direction: column;
			line-height: 1.2;
		}

		.modal-ref-header .title {
			font-weight: 600;
			font-size: 16px;
		}

		/* ICON */
		.icon-box {
			width: 38px;
			height: 38px;
			background: rgba(255,255,255,0.12);
			border-radius: 10px;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.btn-close-custom {
			width: 32px;
			height: 32px;
			border-radius: 8px;
			background: rgba(255,255,255,0.15);
			border: none;
			color: #fff;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: 0.2s;
		}

		.btn-close-custom:hover {
			background: rgba(255,255,255,0.25);
		}

		/* BODY */
		#modalBody {
			padding: 22px;
		}

		/* SECTION CARD */
		.ref-card {
			background: #f8fafc;
			border-radius: 12px;
			padding: 15px;
			margin-bottom: 15px;
		}

		/* TITLE */
		.ref-title {
			font-weight: 600;
			font-size: 14px;
			margin-bottom: 10px;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		/* BADGE MODERN */
		.ref-badge {
			display: inline-block;
			padding: 6px 12px;
			border-radius: 999px;
			font-size: 13px;
			margin: 5px;
			cursor: pointer;
			transition: 0.2s;
			font-weight: 500;
		}

		/* COLORS */
		.badge-parent {
			background: #dbeafe;
			color: #1d4ed8;
		}

		.badge-child {
			background: #dcfce7;
			color: #15803d;
		}

		.badge-chain {
			background: #fef3c7;
			color: #b45309;
		}

		/* HOVER */
		.ref-badge:hover {
			transform: scale(1.05);
		}

		/* BUTTON */
		.btn-soft {
			background: linear-gradient(135deg, #1e293b, #0f172a) !important;
			color: #fff;
			border-radius: 10px;
		}
</style>

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
    <title>Detail Listing | PISS</title>
	<?php echo headScript(); ?>
</head>
<body>
    <div id="wrapper">
        <?php include 'components/sidebar.php'; ?>

		<?php if (mysql_num_rows($edit) == 0) { ?>
			<div id="page-wrapper">
				<div class="row">
					<div class="col-lg-12">
						<br /><br><br><br>
						<div class="panel panel-primary">
							<div class="panel-heading">
								<div class="col-md-8"  style="padding-top: 8px; margin-left: -10px;">
									<i class="fa fa-file-text-o fa-fw"></i> Detail Listing Nota
								</div>
								<form method="GET" action="listing.php">
									<div class="col-md-2" style="margin-left: -10px;">
										<input type="search" class="form-control" id="search" name="id_nota" placeholder="Cari No Nota" value="" required>
									</div>
									<button type="submit" class="btn btn-primary" style="border: 1px solid #fff">Cari</button>
									<a href="javascript:history.back()">
										<button type="button" style="border: 1px solid #fff; margin-left: 10px;" class="btn btn-primary">
											Kembali
										</button>
									</a>
								</form>
							</div>
							<div class="panel-body">
								<div class="row d-flex justify-content-center align-items-center">
									<div class="text-center">
										<img src="img/empty-listing.png" alt="Empty" width="450px" class="img-fluid">
										<h2 style="color: #333333;"><b>Maaf, Nota Tidak Ada</b></h2>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
       		</div>
		<?php } else {
			while ($r 	= mysql_fetch_array($edit)){ 
				$kasir 		= executeScalar("select checker_name from checker where checker_id = '".$r['checker_id']."'");
				$merchant 	= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
				$nick	 	= executeScalar("select merchant_nick from merchant where merchant_id = '".$r['merchant_id']."'");
				$checker_id = executeScalar("select receiver_id from ko where ko_id = '".$r['ko_id']."'");
				$checker_name	= executeScalar("select checker_name from checker where checker_id = '".$checker_id."'");
				$cashier_id = executeScalar("select cashier_id from ko where ko_id = '".$r['ko_id']."'");
				$cashier	= executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");
				$lid 		= executeScalar("select lapak_id from rent where merchant_id = '".$r['merchant_id']."' and (rent_sts != 'Batal' and rent_sts2 not in ('Batal', 'Pindah'))");
				$lpk 		= executeScalar("select lapak_name from lapak where lapak_id = '".$lid."'");
				$goods 		= executeScalar("select goods_name from goods where goods_id='".$r['goods_id']."'");

				$reffer_query   = mysql_query("select weighing_id, laborer_sts from weighing where reffer = '".$id."'");
				$reffer_array   = [];
				
				// Check reffer query
				while ($row = mysql_fetch_array($reffer_query)) {
					if ($row['laborer_sts'] == "Ya") {
						$reffer_array[] = 'TG' . $row['weighing_id'];
					} elseif ($row['laborer_sts'] == "Tidak") {
						$reffer_array[] = 'TG' . $row['weighing_id'];
					}
				}
			?>
				<div id="page-wrapper">
					<div class="row">
						<div class="col-lg-12">
							<br /><br><br><br>
							<div class="panel panel-primary">
								<div class="panel-heading">
									<div class="row">
										<div class="col-md-10"  style="padding-top: 8px;">
											<i class="fa fa-file-text-o fa-fw"></i> Detail Listing Data
											(<?php 
												$wid = sprintf("%07d", $id);
												
												// if($r['weighing_sts']=='Keluar'||$r['weighing_sts']=='Lunas'){
												// 	$year = executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
												// 	echo 'TG'.$wid;
												// }else{
													// 	$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
													// 	echo 'TB'.$wid;
													// } 
												if ($r['weighing_sts']=='Masuk' || $r['weighing_sts']=='Batal') {
													echo 'TB'.$wid;
												} else {
													echo 'TG'.$wid;
												}
											?>)
										</div>
										<form method="GET" action="listing.php">
											<div class="col-md-2" style="margin-left: -75px;">
												<input type="search" class="form-control" id="search" name="id_nota" placeholder="Cari No Nota" value="" required>
											</div>
											<button type="submit" class="btn btn-primary" style="border: 1px solid #fff">Cari</button>
										</form>
									</div>
								</div>
								<div class="panel-body">
									<div class="row">
										<div class="col-md-3">
											<div class="form-group" style="margin-bottom:20px;">
												<label>Nopol</label>
												<input type="text" class="form-control" id="nopol" name="nopol" value="<?php echo $r['nopol']; ?>" disabled>                  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Pedagang</label>
												<select class="form-control" name="merchant" id="merchant" disabled>
													<option value="<?php echo $r['merchant_id']; ?>"><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.($lpk ? $lpk : '-'); ?></option>
												</select>
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Barang</label>
												<select class="form-control" name="goods" id="goods" disabled>
													<option value="<?php echo $r['goods_id']; ?>" required><?php echo $goods; ?></option>
												</select>
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Tanggal & Jam Masuk</label>
												<input type="text" class="form-control" id="date_in" name="date_in" value="<?php echo date("d M Y", strtotime($r['date_in'])).' | '.date("H:s", strtotime($r['time_in'])); ?>" disabled>                  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Tanggal & Jam Keluar</label>
												<input type="text" class="form-control" id="date_out" name="date_out" value="<?php echo date("d M Y", strtotime($r['date_out'])).' | '.date("H:s", strtotime($r['time_out'])); ?>" disabled>                  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Tanggal Pembuatan Nota</label>
												<input type="text" class="form-control" id="date_check" name="date_check" value="<?php 
												if($r['date_check']!=null){
													echo date("d M Y", strtotime($r['date_check'])).' | '.date("H:s", strtotime($r['date_check']));
												} else {
													echo "Belum Terbuat";
												}?>" disabled >                  
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group" style="margin-bottom:20px;">
												<label>Berat Kendaraan Masuk</label>
												<input type="text" class="form-control" id="weight_in" name="weight_in" value="<?php echo number_format($r['weight_in']).' Kg'; ?>" disabled>
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Berat Kendaraan Keluar</label>
												<input type="text" class="form-control" id="weight_out" name="weight_out" value="<?php echo number_format($r['weight_out']).' Kg'; ?>" disabled >
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Berat NonKomoditi</label>
												<input type="text" class="form-control" id="noncommodity" name="noncommodity" value="<?php echo number_format($r['noncommodity']).' Kg'; ?>" disabled />
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Rafaksi</label>
												<input type="text" class="form-control" id="rafaksi" name="rafaksi" value="<?php echo number_format($r['rafaksi']).' Kg'; ?>" disabled >                  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Berat Bersih (Netto)</label>
												<input type="text" class="form-control" id="netto" name="netto" value="<?php echo number_format($r['netto']); ?> Kg" disabled />
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Total Biaya</label>
												<input type="text" class="form-control" id="trans_total" name="trans_total" value="<?php echo format_rupiah($r['cost']); ?>" disabled>       
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group" style="margin-bottom:20px;">
												<label>Status Mitra</label>
												<select class="form-control" name="laborer" id="laborer" disabled>
													<option value="<?php echo $r['laborer_sts']; ?>">
														<?php echo $r['laborer_sts']; ?>
													</option>
												</select>
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Status Kendaraan</label>
												<input type="text" class="form-control" value="<?php echo $r['weighing_sts']; ?>" disabled>  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Status Nota</label>
												<input type="text" class="form-control" value="<?php echo $r['weighing_sts2']; ?>" disabled>  
												</select>
											</div>

											<!-- Old reffer ui -->
											<!-- <div class="form-group" style="margin-bottom:20px;">
												<label>Nota Refrensi</label>
												<input type="text" class="form-control" id="reff" name="reff" value="<?php 
												if($r['reffer']!=NULL && ($r['weighing_sts2'] == 'Gabungan' || $r['weighing_sts2'] == 'Dipecah')){
													// $wid = sprintf("%07d", $r['reffer']);
													// $year = executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
													// echo 'TG'.$year.$wid;
													$finalReffer = implode(", ", $reffer_array);
													echo $finalReffer;
												} elseif ($r['reffer']!=NULL && ($r['weighing_sts2'] == 'Digabung' || $r['weighing_sts2'] == 'Pecahan')) {
													echo 'TG' . $r['reffer'];
												} else{
													echo 'Tidak Ada';
												} ?>" disabled>
											</div> -->

											<!-- New reffer UI -->
											<div class="form-group mt-2">

												<label class="mb-2">Nota Referensi</label>

												<?php if ($r['reffer'] != NULL) { ?>
															
													<div class="ref-card-active">
														<div class="ref-content">
																	
															<div class="ref-icon">
																<i class="fa fa-link"></i>
															</div>

															<div class="ref-text">
																<small>Klik untuk melihat detail relasi nota</small>
															</div>

														</div>

														<button 
															type="button"
															class="btn-sm btn-ref"
															data-toggle="modal" 
															data-target="#modalReffer"
															onclick="loadRefferDetail('<?php echo $r['weighing_id']; ?>')"
														>
															<i class="fa fa-search me-1"></i> Lihat
														</button>
													</div>

												<?php } else { ?>

													<div class="ref-card-empty">
														<div class="ref-icon-empty">
															<i class="fa fa-unlink"></i>
														</div>

														<div>
															<small class="text-muted">Nota ini tidak memiliki relasi</small>
														</div>
													</div>

												<?php } ?>

											</div>

											<!-- Modal reffer -->
												<div class="modal fade" id="modalReffer" tabindex="-1">
													<div class="modal-dialog modal-lg modal-dialog-centered">
														<div class="modal-content modal-modern-ref">

															<!-- HEADER -->
															<div class="modal-ref-header">

																<!-- KIRI (ICON + TITLE HARUS DALAM 1 FLEX) -->
																<div class="header-left">
																	<div class="icon-box">
																		<i class="fa fa-link"></i>
																	</div>

																	<div class="text">
																		<div class="title">Detail Relasi Nota</div>
																		<small id="modalTitle"></small>
																	</div>
																</div>

																<!-- KANAN (CLOSE) -->
																<button type="button" class="btn-close-custom" data-dismiss="modal">
																	<i class="fa fa-times"></i>
																</button>

															</div>

															<!-- BODY -->
															<div class="modal-body" id="modalBody">
																<div class="text-center text-muted">Loading...</div>
															</div>

															<!-- FOOTER -->
															<div class="modal-footer border-0">
																<button class="btn btn-soft" data-dismiss="modal">Tutup</button>
															</div>

														</div>
													</div>
												</div>

											<div class="form-group" style="margin-bottom:20px;">
												<label>Status Print & Pengecekan Nota</label>
												<input type="text" class="form-control" value="<?php 
													if ($r['p1'] != NULL && $r['p2'] != NULL && $r['date_check'] != NULL) {
														echo "Selesai";
													}
													elseif ($r['p1'] == NULL && $r['p2'] != NULL && $r['date_check'] != NULL) {
														echo "Tagihan, Cek";
													}
													elseif ($r['p1'] != NULL && $r['p2'] == NULL && $r['date_check'] != NULL) {
														echo "Timbang, Cek";
													}
													elseif ($r['p1'] != NULL && $r['p2'] != NULL && $r['date_check'] == NULL) {
														echo "Timbang, Tagihan";
													}
													elseif ($r['p1'] != NULL && $r['p2'] == NULL && $r['date_check'] == NULL) {
														echo "Timbang";
													}
													elseif ($r['p1'] == NULL && $r['p2'] != NULL && $r['date_check'] == NULL) {
														echo "Tagihan";
													}
													elseif ($r['p1'] == NULL && $r['p2'] == NULL && $r['date_check'] != NULL) {
														echo "Cek";
													}
													elseif ($r['p1'] == NULL && $r['p2'] == NULL && $r['date_check'] == NULL) {
														echo "Belum";
													}
												?>" disabled>
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Tanggal Bayar</label>
												<input type="text" class="form-control" id="date_out" name="date_out" value="<?php 
												if($r['date_pay']!=null){
													echo date("d M Y", strtotime($r['date_pay'])).' | '.date("H:s", strtotime($r['time_pay']));
												} else {
													echo "Belum Terbayar";
												}
												?>" disabled >
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group" style="margin-bottom:20px;">
												<label>Kasir</label>
												<input type="text" class="form-control" value="<?php echo $kasir; ?>" disabled>  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Penyetor Uang</label>
												<input type="text" class="form-control" value="<?php 
													if($checker_name != null){
														echo $checker_name;
													} else {
														echo "Belum Ada";
													}
												?>" disabled>  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>Penerima Uang</label>
												<input type="text" class="form-control" value="<?php 
													if($cashier != null){
														echo $cashier;
													} else {
														echo "Belum Ada";
													}
												?>" disabled>  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>KO ID</label>
												<input type="text" class="form-control" value="<?php 
													if($r['ko_id']!=null){
														echo $r['km_id'];
													} else {
														echo "Belum Setor";
													}
												?>" disabled>  
											</div>
											<div class="form-group" style="margin-bottom:20px;">
												<label>KM ID</label>
												<input type="text" class="form-control" value="<?php 
													if($r['km_id']!=null){
														echo $r['km_id'];
													} else {
														echo "Belum Setor";
													}
												?>" disabled>  
											</div>
											<div>
												<label>Detail NonKomoditi </label>
												<button onclick="nonkomoditi()" type="button" class="btn btn-block btn-outline btn-info">
													Detail NonKomoditi
												</button>
											</div>
										</div>
										<?php
											$task = isset($_GET['task']) ? $_GET['task'] : '';
										?>
										<?php if ($task != 'preview') { ?>
											<div class="form-group" style="padding:10px; margin-left:5px; margin-right:5px; margin-top:-10px;">
												<a href="javascript:history.back()">
													<button type="button" class="btn btn-block btn-primary">
														<i class="fa fa-arrow-left"></i> &nbsp;Kembali
													</button>
												</a>
											</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			
			<?php } 
		}?>
    </div>

	<!-- Modal nonkomoditi -->
	<div class="modal fade" id="modalNonkomoditi" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
					<h4 id="header_nonkomoditi"><i class="fa fa-archive fa-fw"></i>&nbsp;Data Barang NonKomoditi</h4>
				</div>
				<div class="modal-body">
					<div id="data_nonkomoditi" style="margin-bottom: 0px;">
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
			$('#Area1').load("check_table.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',100); 
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			});
			
			$('#modalNonkomoditi').on('shown.bs.modal', function (e)
			{
				$("#notifArea").html(alertLoadProcess);
				$("#data_nonkomoditi").load("modal_nonkomoditi_detail.php?id=<?php echo $id; ?>",function(){clearNotif();});
			});
		});
		function clearNotif(){$("#notifArea").html("");}
		
		//Memanggil modal nonkomoditi
		function nonkomoditi(){
			$('#modalNonkomoditi').modal('show');
		}
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

	<!-- Ajax reffer -->
	<script>
		function loadRefferDetail(id) {
			$('#modalBody').html('<div class="text-center text-muted">Loading...</div>');

			$.ajax({
				url: 'get_reffer_detail_listing.php',
				method: 'POST',
				data: { id: id },
				success: function(res) {
					$('#modalBody').html(res);
				}
			});
		}
	</script>

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
</body>
</html>