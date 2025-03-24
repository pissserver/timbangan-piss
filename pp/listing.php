<?php
	session_start();
	include "../connection.php";
	include "components/role_session.php";
	
	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}

	$id			= $_GET['id_nota'];
	$edit		= mysql_query("select * from weighing where weighing_id = '".$id."'");
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
								<div class="col-md-9"  style="padding-top: 8px; margin-left: -10px;">
									<i class="fa fa-file-text-o fa-fw"></i> Detail Listing Data
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
										<img src="../img/empty-listing.png" alt="Empty" width="450px" class="img-fluid">
										<h2 style="color: #333333;"><b>Maaf, Data Tidak Ada</b></h2>
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
				$lid 		= executeScalar("select lapak_id from rent where merchant_id = '".$r['merchant_id']."'");
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
												echo 'TG'.$wid;
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
													<option value="<?php echo $r['merchant_id']; ?>"><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.$lpk; ?></option>
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
											<div class="form-group" style="margin-bottom:20px;">
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
													echo date("d-m-Y", strtotime($r['date_pay'])).' | '.date("H:s", strtotime($r['time_pay']));
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
										<div class="form-group" style="padding:10px; margin-left:5px; margin-right:5px; margin-top:-10px;">
											<a href="javascript:history.back()"><button type="button"  class="btn btn-block btn-outline btn-primary">
												Kembali
											</button></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			
			<?php } 
		}?>
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
    <!-- jQuery -->
    <script src="../js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/sb-admin-2.js"></script>
	<script type="text/javascript">
    	function nonkomoditi()
    	{
			$('#modalNonkomoditi').modal('show');
		}
    </script>
</body>
</html>