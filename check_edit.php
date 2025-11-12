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
	$checker 		= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	$id				= $_GET['id'];
	$edit			= mysql_query("select * from weighing where weighing_id = '".$id."'");
	$pdg 			= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
	$jenis			= mb_substr($pdg, 0, 1, 'utf-8');
	$kat_merchant 	= executeScalar("select kat_merchant from merchant where merchant_id = '".$pdg."'");

	while ($r 	= mysql_fetch_array($edit)){
		$tarif			= 0;
		$tarif1			= 0;
		$cost_mitra		= 0;
		$jenis			= mb_substr($r['merchant_id'], 0, 1, 'utf-8');
		$merchant 		= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
		$nick	 		= executeScalar("select merchant_nick from merchant where merchant_id = '".$r['merchant_id']."'");
		$lid 			= executeScalar("select lapak_id from rent where merchant_id = '".$r['merchant_id']."' and (rent_sts != 'Batal' and rent_sts2 not in ('Batal', 'Pindah'))");
		$lpk 			= executeScalar("select lapak_name from lapak where lapak_id = '".$lid."'");
		$goods 			= executeScalar("select goods_name from goods where goods_id='".$r['goods_id']."'");
		$status 		= executeScalar("select weighing_sts from weighing where weighing_id = '".$id."'");
		$status2		= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
		$status4		= executeScalar("select weighing_sts4 from weighing where weighing_id = '".$id."'");
		$laborer_sts	= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");

		$reffer_query   = mysql_query("select weighing_id, laborer_sts from weighing where reffer = '".$id."'");
        $reffer_array   = [];

		// Mitra Query
		$netto_mitra		= executeScalar("SELECT SUM(netto) FROM weighing WHERE (weighing_sts2 = 'Digabung' OR weighing_sts2 = 'Pecahan') AND laborer_sts = 'Ya' AND reffer = '".$id."'");
		
		// Check reffer query
		while ($row = mysql_fetch_array($reffer_query)) {
			if ($row['laborer_sts'] == "Ya") {
				$reffer_array[] = 'TG' . $row['weighing_id'];
			} elseif ($row['laborer_sts'] == "Tidak") {
				$reffer_array[] = 'TG' . $row['weighing_id'];
			}
		}

		// Tarif Commodity
		if ($jenis=='B') {
			if ($r['laborer_sts']=='Ya') {
				$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '9'");
				$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '8'");
				$tarif			= intval($tarif1+$tarif2);
				$biayatonase 	= $r['netto'] * $tarif2;
				$biayamitra 	= $r['netto'] * $tarif1;
				$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
			// if ($biayatonase < $tarifminim) {
				// 	$biayatonase = $tarifminim;
				// }
			} else {
				$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '8'");
				$tarif1 		= 0;
				$biayatonase 	= $r['netto'] * $tarif2;
				$biayamitra 	= $r['netto'] * $tarif1;
				$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
				// if ($biayatonase < $tarifminim) {
				// 	$biayatonase = $tarifminim;
				// }
			}
			// if($r['merchant_id']=='B0072'&&$r['goods_id']==2){
			// 	$tarif 	= executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		} elseif ($jenis=='S'&&$r['laborer_sts']=='Ya') {
			$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '3'");
			$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '2'");
			// Pengecekan apabila pedagang merupakan pedagang trial di lapak
			if ($kat_merchant == 'Trial Lapak') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}

			// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
			if ($kat_merchant == 'Trial Lapak Jalan') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}
			$tarif			= intval($tarif1+$tarif2);
			$biayatonase 	= $r['netto'] * $tarif2;
			$biayamitra 	= $r['netto'] * $tarif1;
			$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
			if ($biayatonase < $tarifminim) {
				$biayatonase = $tarifminim;
			}
		} elseif ($jenis=='S'&&$r['laborer_sts']=='Tidak') {
			$tarif2 		= executeScalar("select pay_total from payment where pay_id = '2'");
			// Pengecekan apabila pedagang merupakan pedagang trial di lapak
			if ($kat_merchant == 'Trial Lapak') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}

			// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
			if ($kat_merchant == 'Trial Lapak Jalan') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}
			$tarif1 		= 0;
			$biayatonase 	= $r['netto'] * $tarif2;
			$biayamitra 	= $r['netto'] * $tarif1;
			$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
			if ($biayatonase < $tarifminim) {
				$biayatonase = $tarifminim;
			}
		} elseif ($jenis=='L'&&$r['laborer_sts']=='Ya') {
			$tarif1	 	= executeScalar("select pay_total from payment where pay_id = '3'");
			$tarif2	 	= executeScalar("select pay_total from payment where pay_id = '2'");
			// Pengecekan apabila pedagang merupakan pedagang trial di lapak
			if ($kat_merchant == 'Trial Lapak') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}

			// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
			if ($kat_merchant == 'Trial Lapak Jalan') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}
			$tarif		= intval($tarif1+$tarif2);
			$biayatonase 	= $r['netto'] * $tarif2;
			$biayamitra 	= $r['netto'] * $tarif1;
			$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
			if ($biayatonase < $tarifminim) {
				$biayatonase = $tarifminim;
			}
		} elseif ($jenis=='L'&&$r['laborer_sts']=='Tidak') {
			$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '2'");
			$tarif1 		= 0;
			// Pengecekan apabila pedagang merupakan pedagang trial di lapak
			if ($kat_merchant == 'Trial Lapak') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}

			// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
			if ($kat_merchant == 'Trial Lapak Jalan') {
				$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
				// if ($jml_kehadiran_hari_ini > 1) {
				// 	$tarif2 = 0;
				// }
			}
			$biayatonase 	= $r['netto'] * $tarif2;
			$biayamitra 	= $r['netto'] * $tarif1;
			$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
			if ($biayatonase < $tarifminim) {
				$biayatonase = $tarifminim;
			}
		}

		// Cost Mitra
		if ($jenis == 'B') {
			$cost_mitra = ($r['netto'] + $r['rafaksi']) * intval($tarif1);
		} elseif ($jenis != 'B') {
			if ($r['weighing_sts2'] == 'Gabungan') {
				$cost_mitra = $netto_mitra * intval($tarif1);
			} else {
				$cost_mitra = $r['netto'] * intval($tarif1);
			}
		}
		
		// Periksa apa kah termasuk komoditi khusus
		if($r['noncommodity_id']!=0||$r['noncommodity_id']!=''){
			$nc1	= $r['noncommodity_id'];
			$nc2	= executeScalar("select noncommodity_name from noncommodity where noncommodity_id='".$nc1."'");
		}
		else{
			$nc1	= '';
			$nc2	= '- Pilih Jenis NonKomoditi -';
		}
		//cek tgl check
		$c 	= executeScalar("select date_check from weighing where weighing_id = '".$id."'");
		if($c!=''||$c!=null){ //Jika tgl check Tidak Kosong / Ada
			$c = 'C';
		}else{
			$c = '';
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
    <title>Detail Data Tagihan | PISS</title>
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
								<div class="col-md-9" style="padding-top: 10px;">
									<?php if ($c == NULL) { ?>
										<i class="fa fa-square-o fa-fw"></i>
									<?php } else { ?>
										<i class="fa fa-check-square-o fa-fw"></i>
									<?php } ?>
									Pengecekan Data
								</div>

								<form method="GET" action="check_edit_search.php">
									<div class="col-md-2">
										<input type="search" class="form-control" id="search" name="search_id" placeholder="Cari No Nota" value="" required>
									</div>
									<button type="submit" class="btn btn-primary" style="border: 1px solid #fff">Cari</button>
								</form>
							</div>
                        </div>
                        <div class="panel-body">
							<div class="row">
								<form action="weighing_action.php?task=update&id=<?php echo $id; ?>" class="form-horizontal" id="formTransOut" name = "formTransOut" method="POST" >
								<fieldset>
									<div class="row">
										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px;">
												<i class="fa fa-clock-o fa-fw"></i><label>Masuk</label>
												<input type="text" class="form-control" id="in" name="in" value="<?php echo date("d M Y", strtotime($r['date_in'])).' ('.date("H:i", strtotime($r['time_in'])).') '; ?>" disabled>                  
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<i class="fa fa-clock-o fa-fw"></i><label>Keluar</label>
												<input type="text" class="form-control" id="out" name="out" value="<?php echo date("d M Y", strtotime($r['date_out'])).' ('.date("H:i", strtotime($r['time_out'])).') '; ?>" disabled>                  
											</div>
										</div>
										<div class="col-md-4">
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-right:5px;">
												<i class="fa fa-clock-o fa-fw"></i><label>Nota Terbuat</label>
												<input type="text" class="form-control" id="check" name="check" value="<?php echo $r['date_check'] == NULL ? 'Belum Terbuat' : date('d M Y', strtotime($r['date_check'])) . ' (' . date('H:i', strtotime($r['date_check'])) . ')'; ?>" disabled>                  
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px;">
												<label>No Nota</label>
												<input type="text" class="form-control" id="no_nota" name="no_nota" 
													value="<?php 
														$wid = sprintf('%07d', $id);
														
														// if ($status == 'Keluar' || $status == 'Lunas') {
															$year = executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
															// echo 'TG'.$year.$wid;
															echo 'TG'.$wid;
														// } else {
														// 	$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
														// 	echo 'TB'.$year.$wid;
														// } 
													?>" 
													required disabled>                  
											</div>

											
											<?php
												$sameMerchant = executeScalar("select merchant_id from weighing where reffer = '".$id."'");
											?>
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px;">
												<label>Nopol</label>
												<?php 
													if($sameMerchant === $r['merchant_id'] && $c == NULL) { ?>
														<p style="color: red; font-size: 11px; display: inline-block;">*Nopol utama saat dipecah</p>
													<?php }
												?>
												<input type="text" class="form-control" id="nopol" name="nopol" value="<?php echo $r['nopol']; ?>" required <?php echo ($sameMerchant === $r['merchant_id'] || $c != NULL) ? 'disabled' : ''; ?>>  
												
												<?php if ($sameMerchant === $r['merchant_id'] || $c != NULL): ?>
													<!-- Hidden input agar value tetap terkirim -->
													<input type="hidden" name="nopol" value="<?php echo $r['nopol']; ?>">
												<?php endif; ?>
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px;">
												<label>Pedagang</label>
												<?php 
													if($sameMerchant === $r['merchant_id'] && $c == NULL) { ?>
														<p style="color: red; font-size: 11px; display: inline-block;">*Pedagang utama saat dipecah</p>
													<?php }
												?>
												<select class="form-control <?php echo ($sameMerchant === $r['merchant_id'] || $c != NULL) ? '' : 'chzn-select'; ?> " name="merchant" id="merchant" <?php echo ($sameMerchant === $r['merchant_id'] || $c != NULL) ? 'disabled' : ''; ?>>
													<option value="<?php echo $r['merchant_id']; ?>" required><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.($lpk ? $lpk : 'Tidak Ada Lapak'); ?></option>
													<?php 
													$sql = mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk2 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."' and (r.rent_sts != 'Batal' and r.rent_sts2 not in ('Batal', 'Pindah')) limit 1");
														$nick2	= $d1['merchant_nick'];
														if($nick2!=''){
															$nick2 = $nick2.' / ';
														}else{
															$nick2 = '';
														}
													?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick2.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.($lpk2 ? $lpk2 : 'Tidak Ada Lapak');?></option>
												<?php } ?>
												</select>

												<?php if ($sameMerchant === $r['merchant_id'] || $c != NULL): ?>
													<!-- Hidden input agar value merchant tetap terkirim -->
													<input type="hidden" name="merchant" value="<?php echo $r['merchant_id']; ?>">
												<?php endif; ?>
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px;">
												<label>Status Pedagang</label>
												<input type="text" class="form-control" value="<?php echo $kat_merchant ?>" disabled>  
											</div>	

											<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px;">
												<label>Barang</label>
												<?php 
													if($sameMerchant === $r['merchant_id'] && $c == NULL) { ?>
														<p style="color: red; font-size: 11px; display: inline-block;">*Barang utama saat dipecah</p>
													<?php }
												?>
												<select class="form-control <?php echo ($sameMerchant === $r['merchant_id'] || $c != NULL) ? '' : 'chzn-select'; ?>" name="goods" id="goods" <?php echo ($sameMerchant === $r['merchant_id'] || $c != NULL) ? 'disabled' : ''; ?> required>
													<option value="<?php echo $r['goods_id']; ?>" required><?php echo $goods; ?></option>
													<?php
														$sql 		= mysql_query("select goods_id, goods_name from goods order by goods_name");
														while($data = mysql_fetch_array($sql))
													{?>
														<option value="<?php echo $data['goods_id'];?>"><?php echo $data['goods_name'];?></option>
													<?php } ?>
												</select>

												<?php if ($sameMerchant === $r['merchant_id'] || $c != NULL): ?>
													<!-- Hidden input agar value goods tetap terkirim ke backend -->
													<input type="hidden" name="goods" value="<?php echo $r['goods_id']; ?>">
												<?php endif; ?>
											</div>
										</div>

										<div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Berat Kendaraan Masuk</label>
												<input type="text" class="form-control" id="weight_in" name="weight_in" value="<?php echo $r['weight_in'] != NULL ? $r['weight_in'].' Kg' : '0 Kg'; ?>" disabled>
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Berat Kendaraan Keluar</label>
												<input type="text" class="form-control" id="weight_out" name="weight_out" value="<?php echo $r['weight_out'] != NULL ? $r['weight_out'].' Kg' : '0 Kg'; ?>" disabled >
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Berat Bersih Tonase (Netto)</label>
												<input type="text" class="form-control" id="netto" name="netto" value="<?php echo $r['netto']; ?> Kg" disabled />
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Berat Bersih Bongkar (Netto)</label>
												<input type="text" class="form-control" id="netto" name="netto" 
												value="<?php if ($r['laborer_sts'] == 'Ya' && ($r['weighing_sts2'] == 'Gabungan' || $r['weighing_sts2'] == 'Dipecah')) {
														if ($netto_mitra != 0) {
															echo $netto_mitra . ' Kg';
														} else {
															echo '0 Kg';
														}
													} elseif ($r['laborer_sts'] == 'Ya' && ($r['weighing_sts2'] != 'Gabungan' || $r['weighing_sts2'] != 'Dipecah')) {
														if ($r['netto'] != 0) {
															echo $r['netto'] + $r['rafaksi']. ' Kg';
														} else {
															echo '0 Kg';
														}
													} elseif ($r['laborer_sts'] == 'Tidak') {
														echo '0 Kg';
													}?>
												" disabled />
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Berat NonKomoditi</label>
												<input type="text" class="form-control" id="noncommodity" name="noncommodity" value="<?php echo $r['noncommodity'] != NULL ? $r['noncommodity'] : '0'; ?> Kg" disabled />
											</div>
										</div>

										<div class="col-md-3">											
											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Berat Rafaksi</label>
												<select class="form-control" name="rafa" id="rafa" <?php echo $c != NULL ? 'disabled' : ''; ?> required>
													<?php 
														if($r['rafaksi']==''||$r['rafaksi']==0){
															echo '<option value="Tidak">Tanpa Rafaksi</option>';
															echo '<option value="Ya">Dengan Rafaksi</option>';
														}
														elseif($r['rafaksi']!=''||$r['rafaksi']!=0){
															echo '<option value="'.$r['rafaksi'].'">'.$r['rafaksi'].' Kg </option>';
															echo '<option value="Tidak">Tanpa Rafaksi</option>';
														}
													?>
												</select>
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Status Mitra</label>
												<input id="laborer" name="laborer" value="<?php echo $r['laborer_sts']; ?>" type="text" class="form-control" disabled />
											</div>	

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Status Kendaraan</label>
												<input type="text" class="form-control" value="<?php echo $r['weighing_sts']; ?>" disabled>  
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Status Nota</label>
												<input type="text" class="form-control" value="<?php echo $r['weighing_sts2']; ?>" disabled>  
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Nota Referensi</label>
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
										</div>

										<div class="col-md-3">																					
											<div class="form-group" style="padding:10px; margin-top:-10px; margin-right:5px;">
												<label>Status Print & Pengecekan</label>
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

											<div class="form-group" style="padding:10px; margin-top:-10px; margin-right:5px;">
												<label>Total Tagihan Tonase</label>
												<input type="text" class="form-control" id="trans_total" name="trans_total" value="<?php echo format_rupiah($r['cost_tonase']); ?>" disabled>       
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px; margin-right:5px;">
												<label>Total Tagihan Mitra</label>
												<input type="text" class="form-control" id="trans_total" name="trans_total" value="<?php echo format_rupiah($cost_mitra); ?>" disabled>       
											</div>

											<div class="form-group" style="padding:10px; margin-top:-10px; margin-right:5px;">
												<label>Total Tagihan Keseluruhan</label>
												<input type="text" class="form-control" id="trans_total" name="trans_total" value="<?php echo format_rupiah($r['cost_tonase'] + $cost_mitra); ?>" disabled>       
											</div>

											<div class="form-group" style="padding:10px; margin-right:5px; margin-top:30px;">
												<?php
													if($status=='Lunas'||$status=='Batal'||$status2=='Digabung'||$status2=='Dipecah'||$status=='Masuk'){
														echo '';
													}
													//Jika Belum Diprint Dan Belum Dicheck
													elseif($c==NULL){
														echo '<button type="button" class="btn btn-block btn-primary" data-toggle="modal" data-target="#confirmationModal">
															<i class="fa fa-floppy-o fa-fw"></i>&nbsp;Simpan
														</button> ';
													}
												?> 
											</div>
										</div>
									</div>

									<div style="margin: 0px 15px;" class="alert <?php echo $c != NULL ? 'alert-success' : 'alert-danger'; ?>" role="alert">
										<table>
											<tr>
												<td style="font-size:20px; padding-right: 10px;">
													<?php echo $c != NULL ? '<i class="fa fa-check"></i>' : '<i class="fa fa-exclamation-triangle"></i>'; ?>
												</td>
												<td>
													<p style="font-size: 14px; padding-top:10px;">
														<?php if ($c == NULL) {
															echo 'Sebelum melakukan simpan data, harap dicek kembali agar tidak ada kesalahan data!';
														} elseif ($c != NULL && ($status == 'Keluar'|| $status == 'Batal')) {
															echo 'Data sudah tersimpan. Jika ada kesalahan data mohon revisi sebelum melalukan pelunasan & setoran!';
														} elseif ($c !=NULL && $status == 'Lunas') {
															echo 'Data sudah tersimpan dan status sudah lunas. Cek pada laporan terbayar!';
														} elseif ($c !=NULL && ($status == 'Batal' && $status2 == 'Digabung')) {
															echo 'Data sudah tersimpan dan status batal karena digabung. Lihat di nota referensi!';
														} elseif ($c !=NULL && ($status == 'Batal' && $status2 == 'Dipecah')) {
															echo 'Data sudah tersimpan dan status batal karena dipecah. Lihat di nota referensi!';
														} ?>
													</p>
												</td>
											</tr>
										</table>
									</div>
									</fieldset>
								</form>
							</div>
                        </div>
                    </div>
                </div>
            </div>

			<div class="row">
				<div class="col-lg-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-wrench fa-fw"></i> NonKomoditi & Mitra
						</div>
						<div class="panel-body">
							<div class="form-group">
								<a style="text-decoration: none;" href="mitra.php?id=<?php echo $id; ?>" data-toggle="modal" data-target="#modalMitra">
								<?php if ($c != NULL && ($status2 != 'Gabungan' && $status4 != 'FromGabungan')) : ?>
									<?php if ($r['laborer_sts'] == 'Ya') : ?>
										<button type="button" class="btn btn-block btn-outline btn-success">
											<i class="fa fa-check"></i>&nbsp;Lihat Mitra
										</button>
									<?php elseif ($r['laborer_sts'] == 'Tidak') : ?>
										<div style="margin-top: -15px;"></div>
									<?php endif; ?>
									<?php elseif ($c == NULL) : ?>
										<button type="button" class="btn btn-block btn-outline btn-primary">
											Input Mitra
										</button>
									<?php elseif ($status2 == 'Gabungan' || $status4 == 'FromGabungan') : ?>
										<div style="margin-top: -15px;"></div>
									<?php endif; ?>
								</a>
							</div>

							<div class="form-group">
								<!-- <?php 
									// if ($kat_merchant != 'Tetap') {
										// 	echo '';
									// } else {
									if($status=='Lunas'||$status=='Batal'||$status2=='Digabung'||$status2=='Dipecah' || $status2 == 'Pecahan'){
										echo '';//Input Berat NonKomoditi
									}
									else{
										// zs
									}
													//}
								?> -->
								<?php if ($c != NULL && ($status2 != 'Gabungan' && $status4 != 'FromGabungan')) {
										if ($r['noncommodity'] != 0) { ?>
											<button type="button" class="btn btn-block btn-outline btn-success" onclick="nonkomoditi()"> 
												<i class="fa fa-check"></i>&nbsp;Lihat Berat NonKomoditi
											</button>
										<?php } elseif ($r['noncommodity'] == 0) { ?>
											<div style="margin-top: -15px;"></div>
										<?php } ?>
									<?php } elseif ($c == NULL && $status2 != 'Pecahan') { ?>
										<button type="button" class="btn btn-block btn-outline btn-primary" onclick="nonkomoditi()">
											Input Berat NonKomoditi
										</button>
									<?php } ?>
							</div>

							<?php 
								if (($c != NULL && $r['laborer_sts'] == 'Tidak' && $r['noncommodity'] == 0) || ($c != NULL && $status2 == 'Gabungan') || ($c != NULL && $status2 == 'Dipecah' && $r['laborer_sts'] == 'Tidak' && $r['noncommodity'] == 0) || ($c != NULL && $status2 == 'Digabung' && $status4 == 'FromGabungan')) { ?>
									<div class="alert alert-warning" role="alert" style="margin-top:-15px;">
										<table>
											<tr>
												<td style="font-size:20px; padding-right: 10px;">
													<i class="fa fa-info"></i> 
												</td>
												<td>
													<p style="font-size: 12px; padding-top:10px;"> 
														<?php
															$message = ($status2 == 'Gabungan' || $status4 == 'FromGabungan') 
																? 'Di nota Gabungan/Digabung (yang awalnya Gabungan) jika ada Berat NonKomoditi atau Mitra, lihat di nota referensi' 
																: 'Tidak ada Berat NonKomoditi dan Mitra';

															echo $message;
														?>
													</p>
												</td>
											</tr>
										</table>
									</div>
								<?php }
							?>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-object-ungroup fa-fw"></i> Pecah & Gabung
						</div>
						<div class="panel-body">
								<?php
									// if ($kat_merchant != 'Tetap') {
									// 	echo '';
									// } else {
									//Cetak Pecah Nota
									if($c != NULL && ($status2 == 'Normal' || $status2 == 'Kir') && $laborer_sts != 'Ya'){ ?>
										<div class="form-group">
											<button onclick="pecahNota()" type="button" class="btn btn-block btn-outline btn-primary">Pecah Data</button>
										</div>
									<?php } else { ?>
										<div></div>
									<?php }
													
									$s 		= $r['date_check'];
									$dt		= new DateTime($s);
									$daten 	= $dt->format('Y-m-d');
									//Cetak Gabung Nota
									$exist		= executeScalar("	select count(weighing_id) 
																	from weighing 
																	where 	merchant_id = '".$r['merchant_id']."' and
																			weighing_sts = 'Keluar' and 
																			DATE(date_check) = '".$daten."'");
									if($c == NULL || $status=='Lunas'||$status=='Batal'||$status=='Masuk'){ ?>
										
									<?php } elseif($exist>1 && $c != NULL) {
										if ($jenis != 'B' || $status == 'Keluar') { ?>
											<div class="form-group">
												<button onclick="merge()" type="button" class="btn btn-block btn-outline btn-primary">Gabung Data</button>
											</div>
									<?php }	
									} 
								?>

							<div class="alert alert-warning" role="alert">
								<table>
									<tr>
										<td style="font-size:20px; padding-right: 10px;">
											<i class="fa fa-info"></i> 
										</td>
										<td>
											<p style="font-size: 12px; padding-top:10px;"> 
												<?php 
													if ($status == 'Lunas') {
														echo 'Data sudah tidak bisa dipecah atau digabung, karena status sudah Lunas';
													} elseif ($status2 == 'Dipecah' || $status2 == 'Digabung') {
														echo 'Data sudah tidak bisa dipecah atau digabung, karena status sudah Dipecah/Digabung';
													} elseif ($c != NULL && ($status2 == 'Gabungan' || $status2 == 'Pecahan' || $status2 == 'Normal' || $status2 == 'Kir' || $status2 == 'Tanpa Timbang')) {
														echo 'Pecah atau Gabung hanya dilakukan sekali, mohon dilakukan dengan teliti!. Dan jika status adalah Gabungan/Pecahan/Tanpa Timbang atau status mitra jika "Ya" maka sudah tidak bisa dipecah lagi';
													}  elseif ($c == NULL) {
														echo 'Lakukan simpan data terlebih dahulu untuk pecah atau gabung nota';
													}
												?>
											</p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<i class="fa fa-print fa-fw"></i> Print Nota
						</div>
						<div class="panel-body">
							<div class="form-group">
								<?php if ($r['p1'] == NULL) { ?>
									<a style="text-decoration: none" href="htmltopdf/nota_timbangan.php?id=<?php echo $id;?>" target="_blank">
										<button type="button" class="btn btn-block btn-outline btn-danger" onclick="postAndReload()"> 
											<i class="fa fa-close"></i>&nbsp;Nota Timbang
										</button>
									</a>
								<?php } elseif ($r['p1'] != NULL) { ?>
									<a style="text-decoration: none" href="htmltopdf/nota_timbangan.php?id=<?php echo $id;?>" target="_blank">
										<button type="button" class="btn btn-block btn-outline btn-success" onclick="postAndReload()"> 
											<i class="fa fa-check"></i>&nbsp;Nota Timbang
										</button>
									</a>
								<?php } ?>
							</div>

							<!-- <div class="form-group">
								<?php if ($r['p1'] == NULL) { ?>
									<a style="text-decoration: none" href="htmltopdf/weighing_result.php?id=<?php echo $id;?>" target="_blank">
										<button type="button" class="btn btn-block btn-outline btn-danger" onclick="postAndReload()"> 
											<i class="fa fa-close"></i>&nbsp;Nota Hasil Timbang
										</button>
									</a>
								<?php } elseif ($r['p1'] != NULL) { ?>
									<a style="text-decoration: none" href="htmltopdf/weighing_result.php?id=<?php echo $id;?>" target="_blank">
										<button type="button" class="btn btn-block btn-outline btn-success" onclick="postAndReload()"> 
											<i class="fa fa-check"></i>&nbsp;Nota Hasil Timbang
										</button>
									</a>
								<?php } ?>
							</div> -->
							
							<?php if ($c==NULL) { ?>
								<div></div>
							<?php } elseif ($c!=NULL) { ?>
								<?php if ($r['p2'] == NULL) { ?>
									<div class="form-group">
										<a style="text-decoration:none;" href="htmltopdf/nota_tagihan.php?id=<?php echo $id.'&u='.$checker;?>" target="_blank">
											<button type="button" class="btn btn-block btn-outline btn-danger" onclick="postAndReload()"> 
												<i class="fa fa-close"></i>&nbsp;Nota Tagihan Tonase
											</button>
										</a>
									</div>
								<?php } elseif ($r['p2'] != NULL) { ?>
									<div class="form-group">
										<a style="text-decoration:none;" href="htmltopdf/nota_tagihan.php?id=<?php echo $id.'&u='.$checker;?>" target="_blank">
											<button type="button" class="btn btn-block btn-outline btn-success" onclick="postAndReload()"> 
												<i class="fa fa-check"></i>&nbsp;Nota Tagihan Tonase
											</button>
										</a>
									</div>
								<?php } ?>
							<?php } ?>
							
							<div class="form-group">
								<?php
									if ($laborer_sts == 'Ya' ) {
										if($status=='Lunas'||$status=='Masuk'||$status2=='Digabung'||$status2=='Dipecah'||$status2=='Gabungan'||$c==NULL){
											echo '';//Print Nota Tagihan Mitra
										}
										else{
											echo '<a style="text-decoration:none;" href="htmltopdf/nota_tagihan_mitra.php?id='.$id.'&u='.$checker.'" target="_blank">
												<button type="button" class="btn btn-block btn-outline btn-primary">
													Nota Tagihan Mitra
												</button>
											</a>';
										}
									}else {
										echo '';
									}
								?>
							</div>

							<div class="alert alert-warning" role="alert">
								<table>
									<tr>
										<td style="font-size:20px; padding-right: 10px;">
											<i class="fa fa-info"></i> 
										</td>
										<td>
											<p style="font-size: 12px; padding-top:10px;"> Yang wajib diprint adalah Nota Timbang & Nota Tagihan Tonase. Nota Tagihan Tonase muncul setelah melakukan simpan data</p>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
        </div>
    </div>
	
	<!-- Modal Mitra -->
	<div id="modalMitra" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
					<h4 id="header_password">Input Mitra</h4>
				</div>
				<div class="modal-body">
					<div id="data_mitra" style="margin-bottom: 0px;">
						<div class="alert alert-info" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
					</div>
					<input type="hidden" id="value_id" />
				</div>
				<div class="modal-footer">
				</div>
            </div>
        </div>
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
	
	<!-- Modal Pecah Nota -->
	<div class="modal fade" id="modalPecahNota" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
					<h4 id="header_PecahNota"><i class="fa fa-file-text-o fa-fw"></i>&nbsp;Pecah Data</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<form action="weighing_action.php?task=breakdown&id=<?php echo $id; ?>" method="POST" class="form-horizontal" id="formPecahNota" name="formPecahNota">
							<div class="netto">
								<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px; margin-right:5px;">
									<label>Berat Bersih Tonase (Netto)</label>
									<input type="text" class="form-control" id="netto" name="netto" value="<?php echo $r['netto']; ?> Kg" disabled />									</div>
							</div>
							<fieldset>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-8">
											<label>Pedagang 1</label>
											<select class="form-control" name="mc1" id="mc1" readonly>
												<option value="<?php echo $r['merchant_id']; ?>" required><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.$lpk; ?></option>
												<?php 
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 	= mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_name'].' / '.$d1['merchant_nick'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 2</label>
											<select class="form-control chzn-select" name="mc2" id="mc2">
												<option value="">Pilih Pedagang</option>
												<?php
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 3</label>
											<select class="form-control chzn-select" name="mc3" id="mc3">
												<option value="">Pilih Pedagang</option>
												<?php
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 4</label>
											<select class="form-control chzn-select" name="mc4" id="mc4">
												<option value="">Pilih Pedagang</option>
												<?php
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 5</label>
											<select class="form-control chzn-select" name="mc5" id="mc5">
												<option value="">Pilih Pedagang</option>
												<?php 
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-md-4" style="margin-left:-20px;margin-right:-20px;">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n1" name="n1" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n2" name="n2" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n3" name="n3" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n4" name="n4" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n5" name="n5" class="form-control" placeholder="Netto">
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-8" style="margin-left:-20px;">
											<label>Pedagang 6</label>
											<select class="form-control chzn-select" name="mc6" id="mc6">
												<option value="">Pilih Pedagang</option>
												<?php 
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 7</label>
											<select class="form-control chzn-select" name="mc7" id="mc7">
												<option value="">Pilih Pedagang</option>
												<?php 
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 8</label>
											<select class="form-control chzn-select" name="mc8" id="mc8">
												<option value="">Pilih Pedagang</option>
												<?php 
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 9</label>
											<select class="form-control chzn-select" name="mc9" id="mc9">
												<option value="">Pilih Pedagang</option>
												<?php 
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
											<label>Pedagang 10</label>
											<select class="form-control chzn-select" name="mc10" id="mc10">
												<option value="">Pilih Pedagang</option>
												<?php 
													$sql 	= mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
														where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
														and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $d1['merchant_nick'].' '.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-md-4" style="margin-left:-20px;margin-right:-20px;">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n6" name="n6" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n7" name="n7" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n8" name="n8" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n9" name="n9" class="form-control" placeholder="Netto">
											<label>Netto(Kg)</label>
											<input type="number" value="0" id="n10" name="n10" class="form-control" placeholder="Netto">
										</div>
									</div>
								</div>
							</fieldset>
							<br />
							<div class="col-md-12">
								<button style="" type="submit" class="btn btn-block btn-primary"><i class="fa fa-floppy-o fa-fw"></i>&nbsp;Simpan</button>
							</div>
						</form>
					</div>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Gabung Nota -->
	<div class="modal fade" id="modalGabungNota" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
					<h4 id="header_PecahNota"><i class="fa fa-object-ungroup fa-fw"></i>&nbsp;Gabung Data</h4>
				</div>
				<div class="modal-body" style="height: 400px; overflow-y: auto;">
					<?php 
						$no 	= 0;
						$s 		= $r['date_check'];
						$dt		= new DateTime($s);
						$daten 	= $dt->format('Y-m-d');
						$allDataMerge = mysql_query("select * from weighing where merchant_id = '".$r['merchant_id']."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
						while ($row = mysql_fetch_array($allDataMerge)) {
							$cost_mitra_ref = 0;
							$tarif			= 0;
							$cost_mitra		= 0;
							$jenis			= mb_substr($row['merchant_id'], 0, 1, 'utf-8');
							$reffer_query   = mysql_query("select weighing_id, laborer_sts from weighing where reffer = '".$row['weighing_id']."'");
							$reffer_array   = [];
							$netto_mitra	= executeScalar("SELECT SUM(netto) FROM weighing WHERE (weighing_sts2 = 'Digabung' OR weighing_sts2 = 'Pecahan') AND laborer_sts = 'Ya' AND reffer = '".$row['weighing_id']."'");

							while ($row_query = mysql_fetch_array($reffer_query)) {
								if ($row_query['laborer_sts'] == "Ya") {
									$reffer_array[] = 'TG' . $row_query['weighing_id'];
								} elseif ($row_query['laborer_sts'] == "Tidak") {
									$reffer_array[] = 'TG' . $row_query['weighing_id'];
								}
							}

							// Tarif Commodity
							if ($jenis=='B') {
								if ($row['laborer_sts']=='Ya') {
									$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '9'");
									$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '8'");
									$tarif			= intval($tarif1+$tarif2);
									$biayatonase 	= $row['netto'] * $tarif2;
									$biayamitra 	= $row['netto'] * $tarif1;
									$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
								// if ($biayatonase < $tarifminim) {
									// 	$biayatonase = $tarifminim;
									// }
								} else {
									$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '8'");
									$tarif1 		= 0;
									$biayatonase 	= $row['netto'] * $tarif2;
									$biayamitra 	= $row['netto'] * $tarif1;
									$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
									// if ($biayatonase < $tarifminim) {
									// 	$biayatonase = $tarifminim;
									// }
								}
								// if($r['merchant_id']=='B0072'&&$r['goods_id']==2){
								// 	$tarif 	= executeScalar("select pay_total from payment where pay_id = '2'");
								// }
							} elseif ($jenis=='S'&&$row['laborer_sts']=='Ya') {
								$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '3'");
								$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '2'");
								// Pengecekan apabila pedagang merupakan pedagang trial di lapak
								if ($kat_merchant == 'Trial Lapak') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}

								// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
								if ($kat_merchant == 'Trial Lapak Jalan') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}
								$tarif			= intval($tarif1+$tarif2);
								$biayatonase 	= $row['netto'] * $tarif2;
								$biayamitra 	= $row['netto'] * $tarif1;
								$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
								if ($biayatonase < $tarifminim) {
									$biayatonase = $tarifminim;
								}
							} elseif ($jenis=='S'&&$row['laborer_sts']=='Tidak') {
								$tarif2 		= executeScalar("select pay_total from payment where pay_id = '2'");
								// Pengecekan apabila pedagang merupakan pedagang trial di lapak
								if ($kat_merchant == 'Trial Lapak') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}

								// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
								if ($kat_merchant == 'Trial Lapak Jalan') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}
								$tarif1 		= 0;
								$biayatonase 	= $row['netto'] * $tarif2;
								$biayamitra 	= $row['netto'] * $tarif1;
								$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
								if ($biayatonase < $tarifminim) {
									$biayatonase = $tarifminim;
								}
							} elseif ($jenis=='L'&&$row['laborer_sts']=='Ya') {
								$tarif1	 	= executeScalar("select pay_total from payment where pay_id = '3'");
								$tarif2	 	= executeScalar("select pay_total from payment where pay_id = '2'");
								// Pengecekan apabila pedagang merupakan pedagang trial di lapak
								if ($kat_merchant == 'Trial Lapak') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}

								// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
								if ($kat_merchant == 'Trial Lapak Jalan') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}
								$tarif		= intval($tarif1+$tarif2);
								$biayatonase 	= $row['netto'] * $tarif2;
								$biayamitra 	= $row['netto'] * $tarif1;
								$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
								if ($biayatonase < $tarifminim) {
									$biayatonase = $tarifminim;
								}
							} elseif ($jenis=='L'&&$row['laborer_sts']=='Tidak') {
								$tarif2	 		= executeScalar("select pay_total from payment where pay_id = '2'");
								$tarif1 		= 0;
								// Pengecekan apabila pedagang merupakan pedagang trial di lapak
								if ($kat_merchant == 'Trial Lapak') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}

								// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
								if ($kat_merchant == 'Trial Lapak Jalan') {
									$tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
									// if ($jml_kehadiran_hari_ini > 1) {
									// 	$tarif2 = 0;
									// }
								}
								$biayatonase 	= $row['netto'] * $tarif2;
								$biayamitra 	= $row['netto'] * $tarif1;
								$tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
								if ($biayatonase < $tarifminim) {
									$biayatonase = $tarifminim;
								}
							}

							// Cost Mitra
							if ($jenis == 'B') {
								$cost_mitra_ref = ($row['netto'] + $row['rafaksi']) * intval($tarif1);
							} elseif ($jenis != 'B') {
								if ($row['weighing_sts2'] == 'Gabungan') {
									$cost_mitra_ref = $netto_mitra * intval($tarif1);
								} else {
									$cost_mitra_ref = $row['netto'] * intval($tarif1);
								}
							}
							$no++;
						?>
							<div class="panel panel-success">
								<div class="panel-heading" style="background-color: green; color: #fff;">
									<div class="row">
										<div class="col-md-6" style="padding-top: 5px;">
											<i class="fa fa-file-text-o fa-fw"></i> Data Referensi <?php echo $no; ?>
										</div>
										<div class="col-md-6 text-right">
											<a href="check_edit.php?id=<?php echo $row['weighing_id']; ?>" class="btn" style="background-color: #fff; color: green;">Lihat</a>
										</div>
									</div>
								</div>
								<div class="panel-body" style="font-size: 12px;">
									<div class="row">
										<fieldset>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:5px;">
														<i class="fa fa-clock-o fa-fw"></i><label>Masuk</label>
														<input type="text" class="form-control" id="in" name="in" value="<?php echo date("d M Y", strtotime($row['date_in'])).' ('.date("H:i", strtotime($row['time_in'])).') '; ?>" disabled>                  
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group" style="padding:10px; margin-top:-10px;">
														<i class="fa fa-clock-o fa-fw"></i><label>Keluar</label>
														<input type="text" class="form-control" id="out" name="out" value="<?php echo date("d M Y", strtotime($row['date_out'])).' ('.date("H:i", strtotime($row['time_out'])).') '; ?>" disabled>                  
													</div>
												</div>
												<div class="col-md-4">
													<div class="form-group" style="padding:10px; margin-top:-10px; margin-right:5px;">
														<i class="fa fa-clock-o fa-fw"></i><label>Nota Terbuat</label>
														<input type="text" class="form-control" id="check" name="check" value="<?php echo $row['date_check'] == NULL ? 'Belum Terbuat' : date('d M Y', strtotime($row['date_check'])) . ' (' . date('H:i', strtotime($row['date_check'])) . ')'; ?>" disabled>                  
													</div>
												</div>
											</div>
											<div class="row">
														<div class="col-md-3">
															<div class="form-group" style="padding:10px; margin-top:-20px; margin-left:5px;">
																<label>No Nota</label>
																<input type="text" class="form-control" id="no_nota" name="no_nota" 
																	value="<?php 
																		$wid = sprintf('%07d', $row['weighing_id']);
																		
																		// if ($status == 'Keluar' || $status == 'Lunas') {
																			$year = executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
																			// echo 'TG'.$year.$wid;
																			echo 'TG'.$wid;
																		// } else {
																		// 	$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
																		// 	echo 'TB'.$year.$wid;
																		// } 
																	?>" 
																	required disabled>                  
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px; margin-left:5px;">
																<label>Nopol</label>
																<input type="text" class="form-control" id="nopol" name="nopol" value="<?php echo $row['nopol']; ?>" disabled>                  
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px; margin-left:5px;">
																<label>Pedagang</label>
																<select class="form-control" name="merchant" id="merchant" disabled>
																	<option value="<?php echo $r['merchant_id']; ?>" required><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.$lpk; ?></option>
																	<?php 
																	$sql = mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
																	while($d1 = mysql_fetch_array($sql)){
																		$lpk2 	= executeScalar("select l.lapak_name from rent r, lapak l 
																		where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."' limit 1");
																		$nick2	= $d1['merchant_nick'];
																		if($nick2!=''){
																			$nick2 = $nick2.' / ';
																		}else{
																			$nick2 = '';
																		}
																	?>
																	<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick2.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk2;?></option>
																<?php } ?>
																</select>
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px; margin-left:5px;">
																<label>Status Pedagang</label>
																<input type="text" class="form-control" value="<?php echo $kat_merchant ?>" disabled>  
															</div>	

															<div class="form-group" style="padding:10px; margin-top:-20px; margin-left:5px;">
																<label>Barang</label>
																<select class="form-control <?php echo $c != NULL ? '' : 'chzn-select'; ?>" name="goods" id="goods" <?php echo $c != NULL ? 'disabled' : ''; ?> required>
																	<option value="<?php echo $row['goods_id']; ?>" required><?php echo $goods; ?></option>
																	<?php
																		$sql 		= mysql_query("select goods_id, goods_name from goods order by goods_name");
																		while($data = mysql_fetch_array($sql))
																	{?>
																		<option value="<?php echo $data['goods_id'];?>"><?php echo $data['goods_name'];?></option>
																	<?php } ?>
																</select>
															</div>
														</div>

														<div class="col-md-3">
															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Berat Kendaraan Masuk</label>
																<input type="text" class="form-control" id="weight_in" name="weight_in" value="<?php echo $row['weight_in'] != NULL ? $row['weight_in'].' Kg' : '0 Kg'; ?>" disabled>
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Berat Kendaraan Keluar</label>
																<input type="text" class="form-control" id="weight_out" name="weight_out" value="<?php echo $row['weight_out'] != NULL ? $row['weight_out'].' Kg' : '0 Kg'; ?>" disabled >
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Berat Bersih Tonase (Netto)</label>
																<input type="text" class="form-control" id="netto" name="netto" value="<?php echo $row['netto']; ?> Kg" disabled />
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Berat Bersih Bongkar (Netto)</label>
																<input type="text" class="form-control" id="netto" name="netto" 
																value="<?php if ($row['laborer_sts'] == 'Ya' && ($row['weighing_sts2'] == 'Gabungan' || $row['weighing_sts2'] == 'Dipecah')) {
																		if ($netto_mitra != 0) {
																			echo $netto_mitra . ' Kg';
																		} else {
																			echo '0 Kg';
																		}
																	} elseif ($row['laborer_sts'] == 'Ya' && ($row['weighing_sts2'] != 'Gabungan' || $row['weighing_sts2'] != 'Dipecah')) {
																		if ($row['netto'] != 0) {
																			echo $row['netto'] + $row['rafaksi']. ' Kg';
																		} else {
																			echo '0 Kg';
																		}
																	} elseif ($row['laborer_sts'] == 'Tidak') {
																		echo '0 Kg';
																	}?>
																" disabled />
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Berat NonKomoditi</label>
																<input type="text" class="form-control" id="noncommodity" name="noncommodity" value="<?php echo $row['noncommodity'] != NULL ? $row['noncommodity'] : '0'; ?> Kg" disabled />
															</div>
														</div>

														<div class="col-md-3">											
															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Berat Rafaksi</label>
																<select class="form-control" name="rafa" id="rafa" <?php echo $c != NULL ? 'disabled' : ''; ?> required>
																	<?php 
																		if($row['rafaksi']==''||$row['rafaksi']==0){
																			echo '<option value="Tidak">Tanpa Rafaksi</option>';
																			echo '<option value="Ya">Dengan Rafaksi</option>';
																		}
																		elseif($row['rafaksi']!=''||$row['rafaksi']!=0){
																			echo '<option value="'.$row['rafaksi'].'">'.$row['rafaksi'].' Kg </option>';
																			echo '<option value="Tidak">Tanpa Rafaksi</option>';
																		}
																	?>
																</select>
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Status Mitra</label>
																<input id="laborer" name="laborer" value="<?php echo $row['laborer_sts']; ?>" type="text" class="form-control" disabled />
															</div>	

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Status Kendaraan</label>
																<input type="text" class="form-control" value="<?php echo $row['weighing_sts']; ?>" disabled>  
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Status Nota</label>
																<input type="text" class="form-control" value="<?php echo $row['weighing_sts2']; ?>" disabled>  
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px;">
																<label>Nota Referensi</label>
																<input type="text" class="form-control" id="reff" name="reff" value="<?php 
																if($row['reffer']!=NULL && ($row['weighing_sts2'] == 'Gabungan' || $row['weighing_sts2'] == 'Dipecah')){
																	// $wid = sprintf("%07d", $r['reffer']);
																	// $year = executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
																	// echo 'TG'.$year.$wid;
																	$finalReffer = implode(", ", $reffer_array);
																	echo $finalReffer;
																} elseif ($row['reffer']!=NULL && ($row['weighing_sts2'] == 'Digabung' || $row['weighing_sts2'] == 'Pecahan')) {
																	echo 'TG' . $row['reffer'];
																} else{
																	echo 'Tidak Ada';
																} ?>" disabled>
															</div>
														</div>

														<div class="col-md-3">																					
															<div class="form-group" style="padding:10px; margin-top:-20px; margin-right:5px;">
																<label>Status Print & Pengecekan</label>
																<input type="text" class="form-control" value="<?php 
																	if ($row['p1'] != NULL && $row['p2'] != NULL && $row['date_check'] != NULL) {
																		echo "Selesai";
																	}
																	elseif ($row['p1'] == NULL && $row['p2'] != NULL && $row['date_check'] != NULL) {
																		echo "Tagihan, Cek";
																	}
																	elseif ($row['p1'] != NULL && $row['p2'] == NULL && $row['date_check'] != NULL) {
																		echo "Timbang, Cek";
																	}
																	elseif ($row['p1'] != NULL && $row['p2'] != NULL && $row['date_check'] == NULL) {
																		echo "Timbang, Tagihan";
																	}
																	elseif ($row['p1'] != NULL && $row['p2'] == NULL && $row['date_check'] == NULL) {
																		echo "Timbang";
																	}
																	elseif ($row['p1'] == NULL && $row['p2'] != NULL && $row['date_check'] == NULL) {
																		echo "Tagihan";
																	}
																	elseif ($row['p1'] == NULL && $row['p2'] == NULL && $row['date_check'] != NULL) {
																		echo "Cek";
																	}
																	elseif ($row['p1'] == NULL && $row['p2'] == NULL && $row['date_check'] == NULL) {
																		echo "Belum";
																	}
																?>" disabled>
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px; margin-right:5px;">
																<label>Total Tagihan Tonase</label>
																<input type="text" class="form-control" id="trans_total" name="trans_total" value="<?php echo format_rupiah($row['cost_tonase']); ?>" disabled>       
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px; margin-right:5px;">
																<label>Total Tagihan Mitra</label>
																<input type="text" class="form-control" id="trans_total" name="trans_total" value="<?php echo format_rupiah($cost_mitra_ref); ?>" disabled>       
															</div>

															<div class="form-group" style="padding:10px; margin-top:-20px; margin-right:5px;">
																<label>Total Tagihan Keseluruhan</label>
																<input type="text" class="form-control" id="trans_total" name="trans_total" value="<?php echo format_rupiah($row['cost_tonase'] + $cost_mitra_ref); ?>" disabled>       
															</div>
														</div>
													</div>
										</fieldset>
									</div>
								</div>
							</div>
						<?php }
					?>
					
					<br />
					<button onclick="submitMerge()" class="btn btn-block btn-primary"><i class="fa fa-object-group"></i>&nbsp;Gabungkan</button>
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>

	<!-- Modal konfirmasi simpan -->
	<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header bg-primary" >
					<h5 class="modal-title" id="confirmationModalLabel"><b>KONFIRMASI SIMPAN</b></h5>
					<button type="button" style="margin-top: -20px;" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Apakah anda yakin ingin menyimpan data?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
					<button type="button" class="btn btn-primary" id="confirmSubmit" onclick="postAndReload()">Simpan</button>
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
	
	<script>
		$(document).ready(function(){
			// Auto Refresh Page
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("check_table.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',100); 
			
			//Modal Mitra
			$('#modalMitra').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_mitra").load("mitra.php?id=<?php echo $id; ?>",function(){clearNotif();});
			})
			//Modal Nonkomoditi
			$('#modalNonkomoditi').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_nonkomoditi").load("modal_nonkomoditi.php?id=<?php echo $id; ?>",function(){clearNotif();});
			})
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
		});
		function clearNotif(){$("#notifArea").html("");}
		
		//Memanggil modal nonkomoditi
		function nonkomoditi(){
			$('#modalNonkomoditi').modal('show');
		}
		//Memanggil modal pecah data
		function pecahNota(){
			$('#modalPecahNota').modal('show');
		}
		//Memanggil modal gabung data
		function merge(){
			$('#modalGabungNota').modal('show');
		}
		//Memanggil fungsi gabung nota
		function submitMerge(){
			window.location=('weighing_action.php?task=merge&wid=<?php echo $id; ?>&mid=<?php echo $r['merchant_id']; ?>');
		}
		//Memanggil fungsi pecah nota
		// function breakdown(){
		// 	window.location=('weighing_action.php?task=breakdown&id=<?php echo $id; ?>');
		// }
		function clearNotif(){$("#notifArea").html("");}
    </script>
	<script>
		const selectMC1 = document.getElementById('mc1');

		selectMC1.addEventListener('mousedown', function(event){
			event.preventDefault();
		});
	</script>
	<script>
		function postAndReload() {
			setTimeout(function() {
				location.reload();
			}, 100);
		}
	</script>
	<!-- Reload Window Tonase-->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			document.getElementById('confirmSubmit').addEventListener('click', function () {
			// Submit form setelah konfirmasi
			document.getElementById('formTransOut').submit();
			});
		});

		function postAndReload() {
			setTimeout(function() {
				location.reload();
			}, 100);
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
	<script src="js/plugins/chosen/chosen.jquery.js"></script>
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
			$icon = $_SESSION['success'] ? 'success' : 'error'; // Menentukan ikon berdasarkan status
			echo "<script>
				Swal.fire({
					title: '$title',
					text: '$message',
					icon: '$icon'
				});
			</script>";
			unset($_SESSION['success']); // Menghapus session setelah alert ditampilkan
			unset($_SESSION['message']);
		}
	?>
</body>
</html>
<?php } ?>