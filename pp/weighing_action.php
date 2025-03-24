<?php
	session_start();
	include "../connection.php";
	include "components/role_session.php";
	//Set Datetime setting area
	date_default_timezone_set('Asia/Jakarta');
	$dt_now	= date("Y-m-d H:i:s");
	
	//Cek session role user yg masuk
	if($_SESSION['role']==''){//jika role kosong
		echo "<script>window.alert('ANda Tidak Mempunyai Hak Akses');
		window.history.back()</script>";
		exit();
	}
	$task		= $_GET['task'];
	// $checker	= -1;
	$id			= $_GET['id'];
	$checker    = executeScalar("select checker_id from weighing where weighing_id = '".$id."'");

	//Spv Masuk
	if($task=='in'){
		$reason = $_POST['reason'];
		//Jika nama pedagang, barang atau tanggal masuk kendaraan kosong
		if($_POST['merchant']==''||$_POST['goods']==''||$_POST['date_in']==''){
			echo "<script>window.alert('Data pedagang, jenis barang & Tanggal Masuk belum diisi')</script>;
			<script>window.history.back()</script>";
			exit();
		}//Jika berat masuk kosong / timbangan belum stabil
		if($_POST['weight_in']==0){
			echo "<script>window.alert('Berat Masuk 0 Atau Belum Stabil, Tolong Diinputkan lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
		$last_id	= 0;
		
		//cara mendapatkan data 1 menit yg lalu
		$currentDate= strtotime($time_now);
		$pastdate 	= $currentDate-(60*1);
		$past 		= date("H:i:s", $pastdate);
		
		//Menghindari kemasukan data double
		$double 	= mysql_query("select nopol from weighing where nopol = '".$_POST['nopol']."' and date_in = '".$date_now."' and (time_in between '".$past."' and '".$time_now."')");
		$triple 	= mysql_query("select nopol from weighing where nopol = '".$_POST['nopol']."' and weighing_sts = 'Masuk'");
		$ketemu		= mysql_num_rows($double);
		$ketemu2	= mysql_num_rows($triple);
		if($ketemu>=1||$ketemu2>=1){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Data double atau kendaraan masih di dalam area";
			echo "<script>window.location=('index.php')</script>";
			exit();
		}
		else{
			//Cek Apakah Pedagang Mempunyai Tagihan yg Belum Terbayar
			//Cek berdasarkan tgl keluar
			$date1		= '2016-10-01';
			$date2 		= date('Y-m-d', strtotime($_POST['date_in'] . "-3 days")); 
			//tahun 2018
			$year 	= date("y");
			$year2 	= date("Y");
			
			$count_id 	= executeScalar("select count(weighing_id) from weighing where YEAR(date_in) = '".$year2."'");
			$total_id	= $count_id+1;
			$idx 		= sprintf("%07d", $total_id);
			$id			= $year.''.$idx+4;//+3 Krn ada nota 2018 dipecah ke 2019
			
			//Simpan data ke tabel timbangan
			$insert2 	= mysql_query("	INSERT INTO `weighing` (	`weighing_id`, 
																`nopol`, 
																`weight_in`, 
																`checker_id`, 
																`merchant_id`, 
																`goods_id`, 
																`date_in`, 
																`time_in`, 
																`laborer_sts`, 
																`weighing_sts`, 
																`weighing_sts2`) 
										VALUES ('".$id."', 
												'".$_POST['nopol']."', 
												'".$_POST['weight_in']."', 
												'".$_POST['checker']."', 
												'".$_POST['merchant']."', 
												'".$_POST['goods']."', 
												'".$_POST['date_in']."', 
												'".$_POST['time_in']."', 
												'Tidak', 
												'Masuk', 
												'Normal')");

			//Ambil Id terakhir data yg diinput mysql_query
			$last_id	= mysql_insert_id();
			if($insert2){//Jika simpan data berhasil masuk
				$bm 			= $_POST['weight_in'];
				$spv_merchant 	= $_POST['merchant'];
				$spv_nopol		= $_POST['nopol'];
				// Insert SPV LOG
				$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
											spv_user, 
											spv_action, 
											spv_target, 
											spv_nopol, 
											spv_merchant_id, 
											spv_reason, 
											action_time) 
										value (	'', 
											'".$name."', 
											'Memasukkan Kendaraan Dengan Berat Masuk -> ".$bm." Kg', 
											'".$last_id."', 
											'".$_POST['nopol']."', 
											'".$_POST['merchant']."', 
											'".$reason."', 
											'".$dt_now."')");
												
				if($insert_spv){//jika insert ke log spv berhasil
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Data kendaraan masuk berhasil";
					echo "<script>window.location=('index.php')</script>";
					exit();
				}else{
					// Alert Information
					$_SESSION['success'] = false;
					$_SESSION['title'] = 'Gagal';
					$_SESSION['message'] = "Data log spv gagal Masuk, Silahkan dicoba lagi";
					echo "<script>window.history.back()</script>";
					exit();
				}
			}
			else{//jika data gagal masuk ke database
				// Alert Information
				$_SESSION['success'] = false;
				$_SESSION['title'] = 'Gagal';
				$_SESSION['message'] = "Data gagal masuk";
				HEADER("location: index.php");
				exit();
			}
		}
	}
	
	//Spv Masuk & Keluar
	if($task=='out'){
		$reason = $_POST['reason'];
		//jika pedagang & barang kosong Blm dibikin handlernya
		if($_POST['merchant']==''||$_POST['goods']==''||$_POST['date_in']==''){
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Data pedagang, jenis barang & Tanggal Masuk belum diisi";
			echo "<script>window.history.back()</scrip>";
			exit();
		}//Jika berat masuk kosong / timbangan belum stabil
		if($_POST['weight_in']==0||$_POST['weight_out']==0){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Berat Masuk / Keluar Salah";
			echo "<script>window.history.back()</script>";
			exit();
		}
		
		//Menghindari kemasukan data double (cek jika ada nopol yg sama masuk dalam jangka waktu yg dekat)
		$double = mysql_query("	select 	nopol 
								from 	weighing 
								where 	nopol 			= '".$_POST['nopol']."' and 
										weight_in 		= '".$_POST['weight_in']."' and 
										weight_out 		= '".$_POST['weight_out']."' and 
										date_in 		= '".$date_now."' and 
										time_in 		= '".$_POST['time_in']."' and 
										weighing_sts 	= 'Keluar'");
		//Jika Data nopol, tgl, jam, sts sama / double
		$ketemu	= mysql_num_rows($double);
		if($ketemu>=1){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Data double atau kendaraan masih di dalam area";
			echo "<script>window.location=('index.php')</script>";
			exit();
		}else{
			//tahun 2018
			$year 		= date("y");
			$year2 		= date("Y");
			$count_id 	= executeScalar("select count(weighing_id) from weighing where YEAR(date_in) = '".$year2."'");
			$total_id	= $count_id+1;
			$idx 		= sprintf("%07d", $total_id);
			$id			= $year.''.$idx+4;//+3 Krn ada nota 2018 dipecah ke 2019
			
			$last_id	= 0;
			$in 		= $_POST['weight_in'];
			$out 		= $_POST['weight_out'];
			$do 		= $_POST['date_out'];
			//Jika brt Keluar !=0 / ada berat keluar
			if($out!=0||$out!='0'||$out!=''||$out!=null||$_POST['date_out']!='0000-00-00'){
				$netto		= 0;
				$total1		= 0;
				$total2		= 0;
				$tonase1 	= 0;
				$tonase3 	= 0;
				
				//Jika Jenis pedangang sayur / buar harga berbeda
				$jenis	= mb_substr($_POST['merchant'], 0, 1, 'utf-8');
				if($jenis=='B'){
					$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
					// if($_POST['merchant']=='B0072'&&$_POST['goods']==2){
					// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
					// }
				}
				elseif($jenis=='S'){
					$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
				}
				elseif($jenis=='L'){
					$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
				}//ambil tarif miniman timbangan
				$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
				
				//jika berat masuk & keluar tidak valid bm <= 0 
				if($in==0||$in=='0'||$out==0||$out=='0'){
					echo "<script>window.alert('Anda Salah Memasukkan Berat Masuk / Keluar $do')</script>;
					<script>window.history.back()</script>";
					exit();
				}
				
				//berat bersih pertama
				$netto = $in-$out;
				
				//jika mitra status tidak
				$total1	= $netto*$tonase1;
				$total2	= $netto*$tonase1;
				
				//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
				if($total1>$tarifmin){
					$total1 = $total1;
				}
				else{
					$total1 = $tarifmin;
				}
				
				//jika keluar=0 / kluar > masuk
				if($out==0||$out>$in){
					$_SESSION['success'] = false;
					$_SESSION['title'] = 'Gagal';
					$_SESSION['message'] = "Berat Keluar Salah";
					echo "<script>window.history.back()</script>";
						exit();
				}

				$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$_POST['merchant']."'");
				$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$_POST['merchant']."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");
				// Pengecekan apabila pedagang merupakan pedagang trial di lapak
				if ($kat_merchant == 'Trial Lapak') { 
					$total1 = executeScalar("select pay_total from payment where pay_id = '15'");
					if ($jml_kehadiran_hari_ini > 1) {
						$total1 = 0;
					}
				}

				// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
				if ($kat_merchant == 'Trial Lapak Jalan') { 
					$total1 = executeScalar("select pay_total from payment where pay_id = '16'");
					if ($jml_kehadiran_hari_ini > 1) {
						$total1 = 0;
					}
				}

				//update status = batal
				elseif($in==$out){
					
					$batal = mysql_query("	INSERT INTO `weighing` (`weighing_id`, 
																	`nopol`, 
																	`weight_in`, 
																	`weight_out`,  
																	`netto`, 
																	`checker_id`, 
																	`merchant_id`, 
																	`goods_id`, 
																	`date_in`, 
																	`date_out`, 
																	`time_in`, 
																	`time_out`, 
																	`laborer_sts`, 
																	`cost_tonase`,
																	`cost_real_tonase`,
																	`cost_bongkar`,
																	`cost`, 
																	`cost2`, 
																	`weighing_sts`, 
																	`weighing_sts2`) 
											VALUES ('".$id."', 
													'".$_POST['nopol']."', 
													'".$in."', 
													'".$out."', 
													'0', 
													'".$_POST['checker']."', 
													'".$_POST['merchant']."', 
													'".$_POST['goods']."', 
													'".$_POST['date_in']."', 
													'".$_POST['date_out']."', 
													'".$_POST['time_in']."', 
													'".$time_now."', 
													'Tidak', 
													'0',
													'0',
													'0',
													'0', 
													'0', 
													'Batal', 
													'Normal')");

												
					if($batal){
						HEADER("location: index.php");
						exit();
					}
					else{
						trigger_error("Data gagal Masuk, Silahkan dicoba lagi", E_USER_ERROR);
						HEADER("location: index.php");
						exit();
					}
				}
				elseif($in>$out){//Jika berat masuk lebih besar drpd berat keluar
					// $sql = "
					// 	INSERT INTO `weighing` (`weighing_id`, 
					// 												`nopol`, 
					// 												`weight_in`, 
					// 												`weight_out`, 
					// 												`netto`, 
					// 												`checker_id`, 
					// 												`merchant_id`, 
					// 												`goods_id`, 
					// 												`date_in`, 
					// 												`date_out`, 
					// 												`time_in`, 
					// 												`time_out`, 
					// 												`laborer_sts`, 
					// 												`cost_tonase`,
					// 												`cost_real_tonase`,
					// 												`cost`, 
					// 												`cost2`, 
					// 												`weighing_sts`, 
					// 												`weighing_sts2`) 
					// 						VALUES ('".$id."', 
					// 								'".$_POST['nopol']."', 
					// 								'".$in."', 
					// 								'".$out."', 
					// 								'".$netto."', 
					// 								'".$_POST['checker']."', 
					// 								'".$_POST['merchant']."', 
					// 								'".$_POST['goods']."', 
					// 								'".$_POST['date_in']."', 
					// 								'".$_POST['date_out']."', 
					// 								'".$_POST['time_in']."', 
					// 								'".$time_now."', 
					// 								'Tidak', 
					// 								'".$total1."', 
					// 								'".$total2."',
					// 								'".$total1."', 
					// 								'".$total2."', 
					// 								'Keluar', 
					// 								'Normal')
					// ";
					
					$insert1 = mysql_query("INSERT INTO `weighing` (`weighing_id`, 
																	`nopol`, 
																	`weight_in`, 
																	`weight_out`, 
																	`netto`, 
																	`checker_id`, 
																	`merchant_id`, 
																	`goods_id`, 
																	`date_in`, 
																	`date_out`, 
																	`time_in`, 
																	`time_out`, 
																	`laborer_sts`, 
																	`cost_tonase`,
																	`cost_real_tonase`,
																	`cost`, 
																	`cost2`, 
																	`weighing_sts`, 
																	`weighing_sts2`) 
											VALUES ('".$id."', 
													'".$_POST['nopol']."', 
													'".$in."', 
													'".$out."', 
													'".$netto."', 
													'".$_POST['checker']."', 
													'".$_POST['merchant']."', 
													'".$_POST['goods']."', 
													'".$_POST['date_in']."', 
													'".$_POST['date_out']."', 
													'".$_POST['time_in']."', 
													'".$time_now."', 
													'Tidak', 
													'".$total1."', 
													'".$total2."',
													'".$total1."', 
													'".$total2."', 
													'Keluar', 
													'Normal')");
					
													
					$last_id	= mysql_insert_id();
					if($insert1){//jika input data timbangan berhasil
						// Insert SPV LOG
						$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
													spv_user, 
													spv_action, 
													spv_target, 
													spv_nopol, 
													spv_merchant_id, 
													spv_reason, 
													action_time) 
												value (	'', 
													'".$name."', 
													'Memasukkan & Mengeluarkan Kendaraan Dengan Berat Masuk -> ".$in." Kg & Berat Keluar -> ".$out." Kg', 
													'".$last_id."', 
													'".$_POST['nopol']."', 
													'".$_POST['merchant']."', 
													'".$reason."', 
													'".$dt_now."')");
						if($insert_spv){//jika input data ke log spv
							// Alert Information
							$_SESSION['success'] = true;
							$_SESSION['title'] = 'Berhasil';
							$_SESSION['message'] = "Data kendaraan masuk & keluar berhasil";
							echo "<script>window.location=('index.php')</script>";
							exit();
						}else{
							// Alert Information
							$_SESSION['success'] = false;
							$_SESSION['title'] = 'Gagal';
							$_SESSION['message'] = "Data log spv gagal Masuk, Silahkan dicoba lagi";
							echo "<script>window.history.back()</script>";
							exit();
						}
					}
					else{
						// Alert Information
						$_SESSION['success'] = false;
						$_SESSION['title'] = 'Gagal';
						$_SESSION['message'] = "Data gagal masuk";
						echo "<script>window.history.back()</script>";
						exit();
					}
				}
			}
			//jika tidak ada berat keluar (Status Masuk)
			else{
				//Simpan data ke tabel timbangan
				$insert2 = mysql_query("INSERT INTO `weighing` (`weighing_id`, 
																`nopol`, 
																`weight_in`, 
																`checker_id`, 
																`merchant_id`, 
																`goods_id`, 
																`date_in`, 
																`time_in`, 
																`laborer_sts`, 
																`weighing_sts`, 
																`weighing_sts2`) 
										VALUES ('".$id."', 
												'".$_POST['nopol']."', 
												'".$in."', 
												'".$checker."', 
												'".$_POST['merchant']."', 
												'".$_POST['goods']."', 
												'".$_POST['date_in']."', 
												'".$_POST['time_in']."', 
												'Tidak', 
												'Masuk', 
												'Normal')");
				if($insert2){//jika simpan data berhasil
					echo 'Data Telah Masuk';
					HEADER("location: index.php");
					exit();
				}
				else{//jika simpan data gagal
					echo 'Data Gagal Masuk';
					HEADER("location: index.php");
					exit();
				}
			}
		}
	}

	//Kendaraan Keluar
	elseif($task=='keluar'){
		$id		= $_GET['id'];
		$reason = $_POST['reason'];
		$netto	= 0;
		$total1	= 0;
		$total2	= 0;
		//memanggil tarif tonase
		$tonase3 	= 0;
		$tonase1 	= 0;
		$noncommodity = executeScalar("select noncommodity from weighing where weighing_id = '".$id."'");
		
		//Jika Jenis pedangang sayur / buar harga berbeda
		$jenis	= mb_substr($_POST['merchant'], 0, 1, 'utf-8');
		if($jenis=='B'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '9'");
			// if($_POST['merchant']=='B0072'&&$commodity==2){
			// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		elseif($jenis=='L'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		
		$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
		$tonase3 	= $tonase1+$tonase2;
		$weight_in 	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$weightout 	= $_POST['weight_out'];
		
		//Jika berat keluar = 0
		if($weightout==0||$weightout=='0'){
			echo "<script>window.alert('Berat Keluar 0 Atau Belum Stabil, Tolong Diinputkan lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
		
		//berat bersih pertama
		$netto 	= $weight_in-$weightout;
		
		//jika mitra status tidak
		$total1	= $netto*$tonase1;
		$total2	= $netto*$tonase1;
		
		//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
		if($total1>$tarifmin){
			$total1 = $total1;
		}else{
			$total1 = $tarifmin;
		}
		
		//ambil berat masuk kendaraan
		$cekweight	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		
		//jika berat masuk < drpd berat keluar
		if($cekweight<$weightout){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Berat Keluar Salah, Tolong Diinputkan lagi";
			echo "<script>window.history.back()</script>";
			exit();
		}//jika berat masuk = berat keluar & selisih sekitar 10-20kg
		elseif($cekweight==$weightout||$netto==10||$netto==-10){
			//update status = batal
			$batal = mysql_query("	update weighing 
									set weight_out 		= '".$weightout."', 
										weighing_sts 	= 'Batal' 
									where weighing_id 	= '".$id."'");
			if($batal){//jika berhasil update status batal
				$update = mysql_query("	update weighing 
										set nopol 			= '".$_POST['nopol']."',
											weight_out		= '".$weightout."',
											netto			= '0',
											merchant_id		= '".$_POST['merchant']."',
											goods_id		= '".$_POST['goods']."',
											date_out		= '".$date_now."',
											time_out		= '".$time_now."',
											laborer_sts		= 'Tidak',
											cost			= '0',
											cost2			= '0',
											weighing_sts	= 'Batal'
										where weighing_id 	= '".$id."'");
				if($update){//jika update berhasil
					$insert2 = mysql_query("insert into spv_log (	spv_log_id, 
																	spv_user, 
																	spv_action, 
																	spv_target, 
																	spv_reason, 
																	action_time) 
											value (	'', 
													'".$_SESSION['username']."', 
													'Mengeluarkan Kendaraan Dengan Berat Keluar -> ".$weightout." Kg', 
													'".$last_id."', 
													'".$reason."', 
													'".$dt_now."')");
					if($insert2){//jika insert data berhasil
						// Alert Information
						$_SESSION['success'] = true;
						$_SESSION['title'] = 'Berhasil';
						$_SESSION['message'] = "Kendaraan keluar berhasil";
						echo "<script>window.location=('index.php')</script>";
						exit();
					}else{//jika insert data gagal
						// Alert Information
						$_SESSION['success'] = false;
						$_SESSION['title'] = 'Gagal';
						$_SESSION['message'] = "Data log spv gagal Masuk, Silahkan dicoba lagi";
						echo "<script><script>window.history.back()</script>";
						exit();
					}
				}
				else{
					echo "<script>window.alert('Data gagal Masuk, Silahkan dicoba lagi')</script>;
					<script>window.history.back()</script>";
					exit();
				}
			}
		}

		$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$_POST['merchant']."'");
		$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$_POST['merchant']."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");
		// Pengecekan apabila pedagang merupakan pedagang trial di lapak
		if ($kat_merchant == 'Trial Lapak') { 
			$total1 = executeScalar("select pay_total from payment where pay_id = '15'");
			if ($jml_kehadiran_hari_ini > 1) {
				$total1 = 0;
			}
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
		if ($kat_merchant == 'Trial Lapak Jalan') { 
			$total1 = executeScalar("select pay_total from payment where pay_id = '16'");
			if ($jml_kehadiran_hari_ini > 1) {
				$total1 = 0;
			}
		}

		//update weighing
		$update1 = mysql_query("update weighing 
								set nopol 				= '".$_POST['nopol']."',
									weight_out			= '".$weightout."',
									netto				= '".$netto."',
									merchant_id			= '".$_POST['merchant']."',
									goods_id			= '".$_POST['goods']."',
									date_out			= '".$date_now."',
									time_out			= '".$time_now."',
									laborer_sts			= 'Tidak',
									cost_tonase			= '".$total1."',
									cost_real_tonase	= '".$total2."',
									cost				= '".$total1."',
									cost2				= '".$total2."',
									weighing_sts		= 'Keluar'
								where weighing_id 		= '".$id."'");

		// Insert SPV LOG
		$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
									spv_user, 
									spv_action, 
									spv_target, 
									spv_nopol, 
									spv_merchant_id, 
									spv_reason, 
									action_time) 
								value (	'', 
									'".$name."', 
									'Mengeluarkan Kendaraan Dengan Berat Keluar -> ".$weightout." Kg', 
									'".$id."', 
									'".$_POST['nopol']."', 
									'".$_POST['merchant']."', 
									'".$reason."', 
									'".$dt_now."')");
		if($update1 && $insert_spv){//jika update data berhasil
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Kendaraan keluar berhasil";
			HEADER("location: index.php");
			exit();
		}
		else{
			echo "<script>window.alert('Data gagal Masuk, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Kendaraan Keluar dengan kir
	elseif($task=='kir'){
		$id				= $_GET['id'];
		$netto			= 0;
		$total1			= 0;
		$total2			= 0;
		$noncommodity 	= executeScalar("select noncommodity from weighing where weighing_id = '".$id."'");
		
		//memanggil tarif tonase
		$tonase3 	= 0;
		$tonase1 	= 0;
		//Jika Jenis pedangang sayur / buar harga berbeda
		$jenis		= mb_substr($_POST['merchant'], 0, 1, 'utf-8');
		if($jenis=='B'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
			// if($_POST['merchant']=='B0072'&&$commodity==2){
			// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
		}
		elseif($jenis=='L'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
		}
		$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
		$tonase3 	= $tonase1+$tonase2;
		
		if($noncommodity!=0||$noncommodity!=''){
			$noncommodity = $noncommodity;
		}
		else{
			$noncommodity = 0;
		}
		$weight_in 	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$nopol 		= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$kir 		= executeScalar("select transportation_weight from transportation where transportation_nopol = '".$nopol."'");
		
		//berat bersih pertama
		$netto = $weight_in-$kir-$noncommodity;
		
		//jika mitra status tidak pake mitra
		$total1	= $netto*$tonase1;
		$total2	= $netto*$tonase1;
	
		//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
		//set total bayar
		if($total1>$tarifmin){
			$total1 = $total1;
		}
		else{
			$total1 = $tarifmin;
		}
		//Cek Berat Keluar
		$cekweight	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		if($cekweight<$kir){
			echo "<script>window.alert('Anda Salah Memasukkan Berat Keluar');
			window.history.back()</script>";
			exit();
		}
		elseif($cekweight==$kir){
			//update status = batal
			$batal = mysql_query("update weighing set weight_out = '".$kir."', weighing_sts = 'Batal' 
			where weighing_id = '".$id."'");
			if($batal){
				echo "<script>window.alert('Kendaraan Ini Batal Bongkar');
				window.location=('index.php')</script>";
				exit();
			}
			else{
				echo "<script>window.alert('Data Gagal Diperbarui');
				window.history.back()</script>";
				exit();
			}
		}
		$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$_POST['merchant']."'");
		$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$_POST['merchant']."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");
		// Pengecekan apabila pedagang merupakan pedagang trial di lapak
		if ($kat_merchant == 'Trial Lapak') { 
			$total1 = executeScalar("select pay_total from payment where pay_id = '15'");
			if ($jml_kehadiran_hari_ini > 1) {
				$total1 = 0;
			}
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
		if ($kat_merchant == 'Trial Lapak Jalan') { 
			$total1 = executeScalar("select pay_total from payment where pay_id = '16'");
			if ($jml_kehadiran_hari_ini > 1) {
				$total1 = 0;
			}
		}
		$update1 = mysql_query("update weighing set nopol 		= '".$_POST['nopol']."',
											weight_out			= '".$kir."',
											netto				= '".$netto."',
											checker_id			= '".$checker."',
											merchant_id			= '".$_POST['merchant']."',
											goods_id			= '".$_POST['goods']."',
											date_out			= '".$date_now."',
											time_out			= '".$time_now."',
											laborer_sts			= 'Tidak',
											cost_tonase			= '".$total1."',
											cost_real_tonase	= '".$total2."',
											cost				= '".$total1."',
											cost2				= '".$total2."',
											weighing_sts		= 'Keluar',
											weighing_sts2		= 'Kir'
										where weighing_id 		= '".$id."'");
		
		// Insert SPV LOG
		$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
									spv_user, 
									spv_action, 
									spv_target, 
									spv_nopol, 
									spv_merchant_id, 
									spv_reason, 
									action_time) 
								value (	'', 
									'".$name."', 
									'Kir Kendaraan Dengan Berat Keluar -> ".$kir." Kg', 
									'".$id."', 
									'".$_POST['nopol']."', 
									'".$_POST['merchant']."', 
									'".$_POST['reason']."', 
									'".$dt_now."')");
		if($update1 && $insert_spv){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Kir kendaraan berhasil";
			echo "<script>window.location=('index.php')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Data Tidak Tersimpan');
			window.history.back()</script>";
			exit();
		}
	}

	// Retail Masuk
	if($task=='retail'){
		if($_POST['merchant']==''||$_POST['goods']==''){
			trigger_error("Data pedagang & jenis barang belum diisi", E_USER_ERROR);
			exit();
		}
		//cara mendapatkan data 1 menit yg lalu
		$currentDate = strtotime($time_now);
		$pastdate 	= $currentDate-(60*1);
		$past 		= date("H:i:s", $pastdate);
			
		//Menghindari kemasukan data double
		// $double 	= mysql_query("select nopol from weighing where nopol = '".$_POST['nopol']."'
		// and date_in = '".$date_now."' and merchant_id = '".$_POST['merchant']."' and
		// (time_in between '".$past."' and '".$time_now."')");
			
		// $ketemu		= mysql_num_rows($double);
		// if($ketemu>=1){
		// 	// Alert Information
		// 	$_SESSION['success'] = false;
		// 	$_SESSION['title'] = 'Gagal';
		// 	$_SESSION['message'] = "Data double atau kendaraan masih di dalam area";
		// 	echo "<script>window.location=('index.php')</script>";
		// 	exit();
		// }else{
			$nopol 	= 'CENTENG';
			//tahun 2018
			$year 	= date("y");
			$year2 	= date("Y");
				
			$count_id 	= executeScalar("select count(weighing_id) from weighing where YEAR(date_in) = '".$year2."'");
			$total_id	= $count_id+1;
			$idx 	= sprintf("%07d", $total_id);
			$id		= $year.''.$idx+4;//+3 Krn nota 2018 dipecah ke 2019
				
			//ambil tarif retail dari db
			$tarifretail 	= executeScalar("select pay_total from payment where pay_id = '1'");
				
			//Simpan data ke tabel timbangan
			$insert = mysql_query("INSERT INTO `weighing` (	`weighing_id`, 
																`nopol`, 
																`weight_in`, 
																`weight_out`,  
																`noncommodity`, 
																`netto`, 
																`checker_id`, 
																`merchant_id`, 
																`goods_id`, 
																`date_in`, 
																`date_out`, 
																`time_in`, 
																`time_out`, 
																`laborer_sts`, 
																`cost_tonase`, 
																`cost_real_tonase`,
																`cost`, 
																`cost2`, 
																`weighing_sts`, 
																`weighing_sts2`) 
										VALUES ('".$id."', 
												'".$nopol."', 
												'0', 
												'0', 
												'0', 
												'0', 
												'".$checker."', 
												'".$_POST['merchant']."', 
												'".$_POST['goods']."', 
												'".$date_now."', 
												'".$date_now."', 
												'".$time_now."', 
												'".$time_now."', 
												'Tidak',
												'".$tarifretail."', 
												'".$tarifretail."', 
												'".$tarifretail."', 
												'".$tarifretail."', 
												'Keluar', 
												'Tanpa Timbang')");

			// Insert SPV LOG
			$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
										spv_user, 
										spv_action, 
										spv_target, 
										spv_nopol, 
										spv_merchant_id, 
										spv_reason, 
										action_time) 
										value (	'', 
										'".$name."', 
										'Membuat data baru CENTENG dengan status Tanpa Timbang', 
										'".$id."', 
										'".$nopol."', 
										'".$_POST['merchant']."', 
										'".$_POST['reason']."', 
										'".$dt_now."')");
			if($insert && $insert_spv){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Pembuatan Data Retail Berhasil";
				HEADER("location: index4.php");
				exit();
			}
			else{
				//echo 'Data Gagal Masuk';
				trigger_error("Data gagal Masuk, Silahkan dicoba lagi", E_USER_ERROR);
				HEADER("location: index.php");
				exit();
			}
		// }
	}
		

	// Edit Kendaraan Masuk
	elseif($task=='edit'){
		$id		= $_GET['id'];
		$nopol	= $_POST['nopol'];
		$merchant = $_POST['merchant'];
		$reason = $_POST['reason'];
		$update1 = mysql_query("update weighing set nopol = '".$_POST['nopol']."',
										merchant_id		= '".$_POST['merchant']."',
										goods_id		= '".$_POST['goods']."'
									where weighing_id 	= '".$id."'");

		$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
									spv_user, 
									spv_action, 
									spv_target, 
									spv_nopol, 
									spv_merchant_id, 
									spv_reason, 
									action_time) 
								values (	'', 
									'".$name."', 
									'Mengedit Kendaraan Masuk',
									'".$id."', 
									'".$nopol."', 
									'".$merchant."', 
									'".$reason."', 
									'".$dt_now."')");

		if($update1){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Edit data kendaraan masuk berhasil";
			echo "<script>
                window.location.href = document.referrer;
            </script>";
			exit();
		}
		else{
			echo "<script>window.alert('Data Gagal Diperbarui');
			window.history.back()</script>";
			exit();
		}
	}

	//Edit Berat Kendaraan Masuk
	if($task=='edit1'){
		$id			= $_GET['id'];
		$bm			= $_POST['weight_in'];
		$reason 	= $_POST['reason'];
		$nopol		= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$in 		= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$sts 		= executeScalar("select weighing_sts from weighing where weighing_id = '".$id."'");
		$merchant 	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$laborer 	= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
		
		//cari cara agar ketika update jika sts = kluar maka lsg hitung netto
		if($sts=='Masuk'){
			//update weight in
			$update1 = mysql_query("update weighing 
									set weight_in 		= '".$bm."' 
									where weighing_id 	= '".$id."'");
			if($update1){
				$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
											spv_user, 
											spv_action, 
											spv_target, 
											spv_nopol, 
											spv_merchant_id, 
											spv_reason, 
											action_time) 
										values (	'', 
											'".$name."', 
											'Mengedit berat kendaraan masuk dari -> ".$in." Kg menjadi -> ".$bm." Kg',
											'".$id."', 
											'".$nopol."', 
											'".$merchant."', 
											'".$reason."', 
											'".$dt_now."')");

				if($insert_spv){
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Edit berat masuk kendaraan";
					echo "<script><script>window.location=('edit1.php')</script>";
					exit();
				}else{
					echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
					<script>window.history.back()</script>";
					exit();
				}
			}else{
				echo "<script>window.alert('Proses Perubahan gagal, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}
		elseif($sts=='Keluar' || $sts=='Batal'){
			//memanggil tarif tonase
			$tonase3 	= 0;
			$tonase1 	= 0;
			$netto		= 0;
			$total1		= 0;
			$total2		= 0;
			
			$bk			= executeScalar("select weight_out from weighing where weighing_id = '".$id."'");
			//Jika Jenis pedangang sayur / buar harga berbeda 
			$jenis			= mb_substr($merchant, 0, 1, 'utf-8');
			if($jenis=='B'){
				$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
				$tonase2 	= executeScalar("select pay_total from payment where pay_id = '9'");
				// if($_POST['merchant']=='B0072'&&$commodity==2){
				// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
				// }
			}
			elseif($jenis=='S'){
				$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
				$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
			}
			elseif($jenis=='L'){
				$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
				$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
			}
			
			$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
			$tonase3 	= $tonase1+$tonase2;
			//Jika Diberi Rafaksi / nonkomoditi
			$rafa		= executeScalar("select rafaksi from weighing where weighing_id = '".$id."'");
			$nc 		= executeScalar("select noncommodity from weighing where weighing_id = '".$id."'");
			
			if($nc==0||$nc==''){
				//cek sts timbangan kedua
				$sts2	= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
				//Jika Nota Pecahan & Ada Rafaksi
				if($sts2=='Pecahan'){
					$netto 	= executeScalar("select netto from weighing where weighing_id = '".$id."'");
				}else{
					$netto 	= $bm-$bk-$rafa;
				}
			}
			//Perhitungan NC dengan Peti, Krat dan Kardus (persentase)
			elseif($nc!=0||$nc!=''){
				//Jika Nota Pecahan & Ada Rafaksi
				if($sts2=='Pecahan'){
					$netto 	= executeScalar("select netto from weighing where weighing_id = '".$id."'");
				}else{
					$netto 	= $bm-$bk-$nc-$rafa;
				}
			}
			
			//jika mitra status tidak
			if($laborer=='Tidak'){
				$total1	= $netto*$tonase1;
				$total2	= $total1;
			}else{
				$total1	= $netto*$tonase3;
				$total2	= $total1;
			}
			//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
			if($total1>$tarifmin){
				$total1 = $total1;
			}else{
				$total1 = $tarifmin;
			}
			
			$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$merchant."'");
			$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$merchant."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");
			// Pengecekan apabila pedagang merupakan pedagang trial di lapak
			if ($kat_merchant == 'Trial Lapak') { 
				$total1 = executeScalar("select pay_total from payment where pay_id = '15'");
				if ($jml_kehadiran_hari_ini > 1) {
					$total1 = 0;
				}
			}

			// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
			if ($kat_merchant == 'Trial Lapak Jalan') { 
				$total1 = executeScalar("select pay_total from payment where pay_id = '16'");
				if ($jml_kehadiran_hari_ini > 1) {
					$total1 = 0;
				}
			}

			$update2 = mysql_query("update weighing 
									set rafaksi				= '".$rafa."',
										weight_in			= '".$bm."',
										weight_out			= '".$bk."',
										netto				= '".$netto."',
										cost_tonase			= '".$total1."',
										cost_real_tonase	= '".$total2."',
										cost				= '".$total1."',
										cost2				= '".$total2."'
									where weighing_id 		= '".$id."'");
			if($update2){
				$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
											spv_user, 
											spv_action, 
											spv_target, 
											spv_nopol, 
											spv_merchant_id, 
											spv_reason, 
											action_time) 
										values (	'', 
											'".$name."', 
											'Mengedit berat kendaraan masuk dari -> ".$in." Kg menjadi -> ".$bm." Kg',
											'".$id."', 
											'".$nopol."', 
											'".$merchant."', 
											'".$reason."', 
											'".$dt_now."')");
				if($insert_spv){
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Edit berat masuk kendaraan";
					echo "<script>window.location=('edit1.php')</script>";
					exit();
				}else{
					echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
					<script>window.history.back()</script>";
					exit();
				}
			}else{
				echo "<script>window.alert('Proses Perubahan gagal, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}
	}
	
	//Edit Berat Kendaraan Keluar
	if($task=='edit2'){
		$id			= $_GET['id'];
		$bk			= $_POST['weight_out'];
		$reason 	= $_POST['reason'];
		$nopol		= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$bm			= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$out 		= executeScalar("select weight_out from weighing where weighing_id = '".$id."'");
		$merchant 	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$laborer 	= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
		
		//memanggil tarif tonase
		$tonase3 			= 0;
		$tonase1 			= 0;
		$netto				= 0;
		$total1				= 0;
		$total2				= 0;
		$cost_tonase		= 0;
		$cost_real_tonase	= 0;
		$cost_bongkar	= 0;
		
		//Jika Jenis pedangang sayur / buar harga berbeda 
		$jenis	= mb_substr($merchant, 0, 1, 'utf-8');
		if($jenis=='B'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '9'");
			// if($merchant=='B0072'&&$commodity==2){
			// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		elseif($jenis=='L'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		
		$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
		$tonase3 	= $tonase1+$tonase2;
		//Jika Diberi Rafaksi / nonkomoditi
		$rafa		= executeScalar("select rafaksi from weighing where weighing_id = '".$id."'");
		$nc 		= executeScalar("select noncommodity from weighing where weighing_id = '".$id."'");
		
		if($nc==0||$nc==''){
			//cek sts timbangan kedua
			$sts2	= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
			//Jika Nota Pecahan & Ada Rafaksi
			if($sts2=='Pecahan'){
				$netto 	= executeScalar("select netto from weighing where weighing_id = '".$id."'");
			}else{
				$netto 	= $bm-$bk-$rafa;
			}
		}
		//Perhitungan NC dengan Peti, Krat dan Kardus (persentase)
		elseif($nc!=0||$nc!=''){
			//Jika Nota Pecahan & Ada Rafaksi
			if($sts2=='Pecahan'){
				$netto 	= executeScalar("select netto from weighing where weighing_id = '".$id."'");
			}else{
				$netto 	= $bm-$bk-$nc-$rafa;
			}
		}
		
		//jika mitra status tidak
		if($laborer=='Tidak'){
			$total1				= $netto*$tonase1;
			$total2				= $total1;
			$cost_tonase 		= $total1;
			$cost_real_tonase	= $total2;
			$cost_bongkar		= 0;
		}else{
			$total1				= $netto*$tonase3;
			$total2				= $total1;
			$cost_tonase 		= $netto*$tonase1;
			$cost_real_tonase	= $cost_tonase;
			$cost_bongkar		= $netto*$tonase2;
		}
		//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
		if($total1>$tarifmin){
			$total1 = $total1;
		}else{
			$total1 = $tarifmin;
		}

		if($cost_tonase>$tarifmin){
			$cost_tonase = $cost_tonase;
		}else{
			$cost_tonase = $tarifmin;
		}

		$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$merchant."'");
		$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$merchant."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");
		// Pengecekan apabila pedagang merupakan pedagang trial di lapak
		if ($kat_merchant == 'Trial Lapak') { 
			$total1 		= executeScalar("select pay_total from payment where pay_id = '15'");
			$cost_tonase 	= executeScalar("select pay_total from payment where pay_id = '15'");
			if ($jml_kehadiran_hari_ini > 1) {
				$total1 		= 0;
				$cost_tonase 	= 0;
			}
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
		if ($kat_merchant == 'Trial Lapak Jalan') { 
			$total1 		= executeScalar("select pay_total from payment where pay_id = '16'");
			$cost_tonase 	= executeScalar("select pay_total from payment where pay_id = '16'");

			if ($jml_kehadiran_hari_ini > 1) {
				$total1 		= 0;
				$cost_tonase 	= 0;
			}
		}

		if($netto==0){
			$update = mysql_query("	update 	weighing 
									set 	rafaksi				= '".$rafa."',
											weight_out			= '".$bk."',
											netto				= '".$netto."',
											cost_tonase			= '".$cost_tonase."',
											cost_real_tonase	= '".$cost_real_tonase."',
											cost_bongkar		= '".$cost_bongkar."',
											cost				= '".$total1."',
											cost2				= '".$total2."',
											weighing_sts		= 'Batal'
									where 	weighing_id 		= '".$id."'");
		}else{
			$update = mysql_query("	update 	weighing 
									set 	rafaksi				= '".$rafa."',
											weight_out			= '".$bk."',
											netto				= '".$netto."',
											cost_tonase			= '".$cost_tonase."',
											cost_real_tonase	= '".$cost_real_tonase."',
											cost_bongkar		= '".$cost_bongkar."',
											cost				= '".$total1."',
											cost2				= '".$total2."'
									where 	weighing_id 		= '".$id."'");
		}
		
		if($update){
			$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
										spv_user, 
										spv_action, 
										spv_target, 
										spv_nopol, 
										spv_merchant_id, 
										spv_reason, 
										action_time) 
									values (	'', 
										'".$name."', 
										'Mengedit berat kendaraan keluar dari -> ".$out." Kg menjadi -> ".$bk." Kg',
										'".$id."', 
										'".$nopol."', 
										'".$merchant."', 
										'".$reason."', 
										'".$dt_now."')");
			if($insert_spv){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Edit berat keluar kendaraan";
				echo "<script>window.location=('edit2.php')</script>";
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Proses Perubahan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Edit Berat Kendaraan Netto
	if($task=='edit3'){
		$id			= $_GET['id'];
		$bb			= $_POST['netto'];
		$reason 	= $_POST['reason'];
		$nopol		= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$net 		= executeScalar("select netto from weighing where weighing_id = '".$id."'");
		$merchant 	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$laborer 	= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
		
		//memanggil tarif tonase
		$tonase3 			= 0;
		$tonase1 			= 0;
		$netto				= 0;
		$total1				= 0;
		$total2				= 0;
		$cost_tonase		= 0;
		$cost_real_tonase	= 0;
		$cost_bongkar		= 0;
		
		//Jika Jenis pedangang sayur / buar harga berbeda 
		$jenis			= mb_substr($merchant, 0, 1, 'utf-8');
		if($jenis=='B'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '9'");
			// if($merchant=='B0072'&&$commodity==2){
			// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		elseif($jenis=='L'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		
		$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
		$tonase3 	= $tonase1+$tonase2;
		$netto 		= $bb;
		
		//jika mitra status tidak
		if($laborer=='Tidak'){
			$total1				= $netto * $tonase1;
			$total2				= $total1;
			$cost_tonase 		= $total1;
			$cost_real_tonase 	= $total2;
			$cost_bongkar 		= 0;
		}else{
			$total1				= $netto*$tonase3;
			$total2				= $total1;
			$cost_tonase 		= $netto*$tonase1;
			$cost_real_tonase 	= $cost_tonase;
			$cost_bongkar 		= $netto*$tonase2;
		}
		//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
		if($total1>$tarifmin){
			$total1 = $total1;
		}else{
			$total1 = $tarifmin;
		}

		if($cost_tonase>$tarifmin){
			$cost_tonase = $cost_tonase;
		}else{
			$cost_tonase = $tarifmin;
		}
		
		$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$merchant."'");
		$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$merchant."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");
		// Pengecekan apabila pedagang merupakan pedagang trial di lapak
		if ($kat_merchant == 'Trial Lapak') { 
			$total1 		= executeScalar("select pay_total from payment where pay_id = '15'");
			$cost_tonase 	= executeScalar("select pay_total from payment where pay_id = '15'");
			if ($jml_kehadiran_hari_ini > 1) {
				$total1 		= 0;
				$cost_tonase 	= 0;
			}
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
		if ($kat_merchant == 'Trial Lapak Jalan') { 
			$total1 		= executeScalar("select pay_total from payment where pay_id = '16'");
			$cost_tonase 	= executeScalar("select pay_total from payment where pay_id = '16'");

			if ($jml_kehadiran_hari_ini > 1) {
				$total1 		= 0;
				$cost_tonase 	= 0;
			}
		}

		$update = mysql_query("	update 	weighing 
								set 	netto				= '".$netto."',
										cost_tonase			= '".$cost_tonase."',
										cost_real_tonase	= '".$cost_real_tonase."',
										cost_bongkar		= '".$cost_bongkar."',
										cost				= '".$total1."',
										cost2				= '".$total2."'
								where weighing_id 			= '".$id."'");
		if($update){
			$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
										spv_user, 
										spv_action, 
										spv_target, 
										spv_nopol, 
										spv_merchant_id, 
										spv_reason, 
										action_time) 
									values (	'', 
										'".$name."', 
										'Mengedit berat bersih kendaraan dari -> ".$net." Kg menjadi -> ".$netto." Kg',
										'".$id."', 
										'".$nopol."', 
										'".$merchant."', 
										'".$reason."', 
										'".$dt_now."')");
			if($insert_spv){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Edit berat bersih kendaraan";
				echo "<script>window.location=('edit3.php')</script>";
				exit();
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Proses Perubahan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Edit Status Nota
	if($task=='edit4'){
		$id			= $_GET['id'];
		$sts1		= $_POST['status1'];
		$sts2		= $_POST['status2'];
		$reason 	= $_POST['reason'];
		$nopol		= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$status1 	= executeScalar("select weighing_sts from weighing where weighing_id = '".$id."'");
		$status2 	= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
		$merchant 	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		
		if($sts1=='Lunas'){
			$update = mysql_query("update weighing set weighing_sts	= '".$sts1."',
													weighing_sts2	= '".$sts2."', 
													date_pay		= '".$date_now."',
													time_pay		= '".$time_now."'
												where weighing_id 	= '".$id."'");
		}else{
			$update = mysql_query("update weighing set weighing_sts	= '".$sts1."',
													weighing_sts2	= '".$sts2."'
												where weighing_id 	= '".$id."'");
		}
		if($update){
			$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
										spv_user, 
										spv_action, 
										spv_target, 
										spv_nopol, 
										spv_merchant_id, 
										spv_reason, 
										action_time) 
									values (	'', 
										'".$name."', 
										'Mengedit status nota dari -> (".$status1.") menjadi -> (".$sts1.") & (".$status2.") menjadi -> (".$sts2.")',
										'".$id."', 
										'".$nopol."', 
										'".$merchant."', 
										'".$reason."', 
										'".$dt_now."')");
			if($insert_spv){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Edit status nota";
				echo "<script>window.location=('edit4.php')</script>";
				exit();
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Proses Perubahan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}

	//Edit Jam Kendaraan Masuk / Keluar
	if($task=='edit5_time'){
		$id				= $_GET['id'];
		$time_in		= $_POST['time_in'];
		$time_out		= $_POST['time_out'];
		$reason 		= $_POST['reason'];
		$nopol 			= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant 		= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$time_in_old = executeScalar("SELECT DATE_FORMAT(time_in, '%H:%i') FROM weighing WHERE weighing_id = '".$id."'");
		$time_out_old = executeScalar("SELECT DATE_FORMAT(time_out, '%H:%i') FROM weighing WHERE weighing_id = '".$id."'");
		$merchant 		= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		
		$update = mysql_query("update weighing set time_in	= '".$time_in."', time_out = '".$time_out."' where weighing_id = '".$id."'");

		if($update){
			$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
										spv_user, 
										spv_action, 
										spv_target, 
										spv_nopol, 
										spv_merchant_id, 
										spv_reason, 
										action_time) 
									values (	'', 
										'".$name."', 
										'Mengedit jam kendaraan masuk dari -> ".$time_in_old." WIB menjadi -> ".$time_in." WIB & ".$time_out_old." WIB menjadi -> ".$time_out." WIB',
										'".$id."', 
										'".$nopol."', 
										'".$merchant."', 
										'".$reason."', 
										'".$dt_now."')");
			if($insert_spv){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Edit jam masuk / Keluar";
				echo "<script>window.location=('edit5.php')</script>";
				exit();
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Proses Perubahan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}

	//Edit Jam Kendaraan Masuk / Keluar
	if($task=='edit5_date'){
		$id				= $_GET['id'];
		$date_in		= $_POST['date_in'];
		$formatted_date_in = date("d M Y", strtotime($date_in));
		$date_out		= $_POST['date_out'];
		$formatted_date_out = date("d M Y", strtotime($date_out));
		$reason 		= $_POST['reason'];
		$nopol 			= executeScalar("select nopol from weighing where weighing_id = '".$id."'");	
		$merchant 		= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$date_in_old = executeScalar("SELECT DATE_FORMAT(date_in, '%d %b %Y') FROM weighing WHERE weighing_id = '".$id."'");
		$date_out_old = executeScalar("SELECT DATE_FORMAT(date_out, '%d %b %Y') FROM weighing WHERE weighing_id = '".$id."'");
		$merchant 		= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		
		$update = mysql_query("update weighing set date_in	= '".$date_in."', date_out = '".$date_out."' where weighing_id = '".$id."'");

		if($update){
			$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
										spv_user, 
										spv_action, 
										spv_target, 
										spv_nopol, 
										spv_merchant_id, 
										spv_reason, 
										action_time) 
									values (	'', 
										'".$name."', 
										'Mengedit tanggal kendaraan masuk dari -> ".$date_in_old." menjadi -> ".$formatted_date_in." & ".$date_out_old." menjadi -> ".$formatted_date_out."',
										'".$id."', 
										'".$nopol."', 
										'".$merchant."', 
										'".$reason."', 
										'".$dt_now."')");
			if($insert_spv){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Edit tanggal masuk / Keluar";
				echo "<script>window.location=('edit5.php')</script>";
				exit();
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Proses Perubahan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Kendaraan Batal Masuk
	if($task=='Batal1'){
		$id		= $_GET['id'];
		$reason = $_POST['reason'];
		$out 	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$nopol 	= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant = executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$batal 	= mysql_query("	update 	weighing 
								set 	weight_out			= '".$out."',
										netto				= '0',
										date_out			= '".$date_now."',
										cost_tonase			= '0',
										cost_real_tonase	= '0',
										cost_bongkar		= '0',
										cost				= '0',
										cost2				= '0',
										weighing_sts		= 'Batal'
								where 	weighing_id 		= '".$id."'");
		if($batal){
			$insert_pp = mysql_query("insert into pp_log (pp_log_id, 
											pp_user, 
											pp_action, 
											pp_target, 
											pp_nopol, 
											pp_merchant_id, 
											pp_reason, 
											action_time) 
										values (	'', 
											'".$name."', 
											'Membatalkan kendaraan masuk',
											'".$id."', 
											'".$nopol."', 
											'".$merchant."', 
											'".$reason."', 
											'".$dt_now."')");
			if($insert_pp){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Batal kendaraan masuk";
				echo "<script>window.location=('cancel1.php')</script>";
				exit();
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}
		else{
			echo "<script>window.alert('Proses Pembatalan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Kendaraan Batal Keluar Menjadi Masuk
	if($task=='Batal2'){
		$id		= $_GET['id'];
		$reason = $_POST['reason'];
		$nopol 	= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant = executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$batal 	= mysql_query("	update 	weighing 
								set 	date_out			= null,
										time_out			= null,
										weight_out			= null,
										netto				= null,
										noncommodity		= null,
										rafaksi				= null,
										cost_tonase			= '0',
										cost_real_tonase	= '0',
										cost_bongkar		= '0',
										cost				= '0',
										cost2				= '0',
										weighing_sts		= 'Masuk'
								where 	weighing_id 		= '".$id."'");
		if($batal){
			$insert_pp = mysql_query("insert into pp_log (pp_log_id, 
											pp_user, 
											pp_action, 
											pp_target, 
											pp_nopol, 
											pp_merchant_id, 
											pp_reason, 
											action_time) 
										values (	'', 
											'".$name."', 
											'Membatalkan kendaraan keluar menjadi masuk',
											'".$id."', 
											'".$nopol."', 
											'".$merchant."', 
											'".$reason."', 
											'".$dt_now."')");
			if($insert_pp){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Memasukkan kendaraan kembali";
				echo "<script>window.location=('cancel2.php')</script>";
				exit();
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}
		else{
			echo "<script>window.alert('Proses Pembatalan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Kendaraan Batal Keluar
	if($task=='Batal3'){
		$id		= $_GET['id'];
		$reason = $_POST['reason'];
		$out 	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$nopol 	= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant = executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		// $batal 	= mysql_query("	update 	weighing 
		// 						set 	weight_out			= '".$out."',
		// 								netto				= '0',
		// 								cost_tonase			= '0',
		// 								cost_real_tonase	= '0',
		// 								cost_bongkar		= '0',
		// 								cost				= '0',
		// 								cost2				= '0',
		// 								weighing_sts		= 'Batal'
		// 						where 	weighing_id 		= '".$id."'");
		$batal 	= mysql_query("	update 	weighing 
								set 	weighing_sts		= 'Batal'
								where 	weighing_id 		= '".$id."'");
		
		if($batal){
			$insert_pp = mysql_query("insert into pp_log (pp_log_id, 
											pp_user, 
											pp_action, 
											pp_target, 
											pp_nopol, 
											pp_merchant_id, 
											pp_reason, 
											action_time) 
										values (	'', 
											'".$name."', 
											'Membatalkan kendaraan keluar',
											'".$id."', 
											'".$nopol."', 
											'".$merchant."', 
											'".$reason."', 
											'".$dt_now."')");
			if($insert_pp){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Batal kendaraan keluar";
				echo "<script>window.location=('cancel2.php')</script>";
				exit();
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Proses Pembatalan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Kendaraan Batal Bayar
	if($task=='Batal4'){
		$id		= $_GET['id'];
		$reason = $_POST['reason'];
		$batal 	= mysql_query("update weighing set weighing_sts = 'Keluar', weighing_sts3 = null where weighing_id = '".$id."'");
		$nopol 	= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$merchant = executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		
		if($batal){
			$cost 	= executeScalar("select cost_tonase from weighing where weighing_id = '".$id."'");
			//cari cara agar mengupdate ko & km nya jg
			$ko_id 	= executeScalar("select ko_id from weighing where weighing_id = '".$id."'");
			if($ko_id==null){
				
			}else{//update ko
				$ko_total 	= executeScalar("select ko_total from ko where ko_id = '".$ko_id."'");
				$total_new	= $ko_total-$cost;
				mysql_query("update ko set ko_total = '".$total_new."' where ko_id = '".$ko_id."'");
				mysql_query("update weighing set ko_id is null where weighing_id = '".$id."'");
			}
			$km_id 	= executeScalar("select km_id from weighing where weighing_id = '".$id."'");
			if(empty($km_id)){
				
			}else{//update km
				$km_total 	= executeScalar("select km_total from km where km_id = '".$km_id."'");
				$km_cash 	= executeScalar("select km_cash from km where km_id = '".$km_id."'");
				$total_new	= $km_total-$cost;
				$cash_new	= $km_cash-$cost;
				mysql_query("update km set km_total = '".$total_new."', km_cash = '".$cash_new."' where km_id = '".$km_id."'");
			}
			$update = mysql_query("	update 	weighing 
									set 	ko_id 		= '".$ko_id."', 
											km_id 		= '".$km_id."'
									where 	weighing_id	= '".$id."'");
			if($update){
				$insert_pp = mysql_query("insert into pp_log (pp_log_id, 
											pp_user, 
											pp_action, 
											pp_target, 
											pp_nopol, 
											pp_merchant_id, 
											pp_reason, 
											action_time) 
										values (	'', 
											'".$name."', 
											'Membatalkan pembayaran tagihan',
											'".$id."', 
											'".$nopol."', 
											'".$merchant."', 
											'".$reason."', 
											'".$dt_now."')");
				if($insert_pp){
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Batal Pembayaran Tagihan";
					echo "<script>window.location=('cancel3.php')</script>";
					exit();
				}else{
					echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
					<script>window.history.back()</script>";
					exit();
				}
			}else{
				echo "<script>window.alert('Data log spv gagal Masuk, Silahkan dicoba lagi')</script>;
				<script>window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Proses Pembatalan gagal, Silahkan dicoba lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
	}
	
	//Update Data Timbangan, rafaksi, dll
	elseif($task=='update'){
		$id					= $_GET['id'];
		$netto				= 0;
		$total1				= 0;
		$total2				= 0;
		$tonase1 			= 0;
		$tonase3 			= 0;
		$cost_tonase 		= 0;
		$cost_real_tonase 	= 0;
		$cost_bongkar 		= 0;
		
		//memanggil tarif tonase
		//Jika Jenis pedangang sayur / buar harga berbeda 
		$jenis		= mb_substr($_POST['merchant'], 0, 1, 'utf-8');
		if($jenis=='B'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '9'");
			// if($_POST['merchant']=='B0072'&&$_POST['goods']==2){
			// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		elseif($jenis=='L'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		
		$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
		$tonase3 	= $tonase1+$tonase2;
		
		$weight_in 	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$weight_out = executeScalar("select weight_out from weighing where weighing_id = '".$id."'");
		$nc 		= executeScalar("select noncommodity from weighing where weighing_id = '".$id."'");
		
		//Jika Diberi Rafaksi
		$rafa		= $_POST['rafa'];
		$disc		= 0;
		
		//Perhitungan noncommodity
		if($nc==0||$nc==''){
			$sts	= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
			//Jika Nota Pecahan & Ada Rafaksi
			if($sts=='Pecahan'){
				$netto 	= executeScalar("select netto from weighing where weighing_id = '".$id."'");
			}
			else{
				$netto 	= $weight_in-$weight_out;
			}
			
			if($rafa=='Tidak'){
				//update rafaksi = 0
				$update = mysql_query("update weighing set rafaksi == NULL where weighing_id = '".$id."'");
			}
			
			elseif($rafa=='Ya'||$rafa!='Tidak'){
				if($netto>=1000&&$netto<=1999){
					$disc 	= intval($netto*1/100);
					$netto 	= intval($netto-$disc);
					$rafa	= $disc;
				}
				elseif($netto>=2000&&$netto<=5999){
					$disc 	= intval($netto*2/100);
					$netto 	= intval($netto-$disc);
					$rafa	= $disc;
				}
				elseif($netto>=6000){
					$disc 	= intval($netto*3/100);
					$netto 	= intval($netto-$disc);
					$rafa	= $disc;
				}
			}
		}
		
		//Perhitungan NC dengan Peti, Krat dan Kardus (persentase)
		elseif($nc!=0||$nc!=''){//||$_POST['nc']!=4
			$sts	= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
			//Jika Nota Pecahan & Ada Rafaksi
			if($sts=='Pecahan'){
				$netto 	= executeScalar("select netto from weighing where weighing_id = '".$id."'");
			}
			else{
				$netto 	= $weight_in-$weight_out-$nc;
			}
			
			if($rafa=='Tidak'){
				//update rafaksi = 0
				$update = mysql_query("update weighing set rafaksi == NULL where weighing_id = '".$id."'");
			}
			elseif($rafa=='Ya'||$rafa!='Tidak'){
				if($netto>=1000&&$netto<=1999){
					$disc 	= intval($netto*1/100);
					$netto 	= intval($netto-$disc);
					$rafa	= $disc;
				}
				elseif($netto>=2000&&$netto<=5999){
					$disc 	= intval($netto*2/100);
					$netto 	= intval($netto-$disc);
					$rafa	= $disc;
				}
				elseif($netto>=6000){
					$disc 	= intval($netto*3/100);
					$netto 	= intval($netto-$disc);
					$rafa	= $disc;
				}
			}
		}
		else{
			echo "<script>window.alert('Data Gagal Diperbarui');
			window.history.back()</script>";
			exit();
		}
		
		//cek jika status mitra ya/tidak
		$laborer 	= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
		if($laborer=='Tidak'){
			$total1				= $netto*$tonase1;
			$total2				= $total1;
			$cost_tonase		= $total1;
			$cost_real_tonase	= $cost_tonase;
			$cost_bongkar		= 0;
		}
		else{
			$total1				= $netto*$tonase3;
			$total2				= $total1;
			$cost_tonase		= $netto*$tonase1;
			$cost_real_tonase	= $cost_tonase;
			$cost_bongkar		= $netto*$tonase2;
		}
		//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
		if($jenis != 'B'){
			if($total1>$tarifmin){
				$total1 = $total1;
			}
			else{
				$total1 = $tarifmin;
			}
	
			if($cost_tonase>$tarifmin){
				$cost_tonase = $cost_tonase;
			}
			else{
				$cost_tonase = $tarifmin;
			}
		}
		
		//jika status pembayaran berubah
		$ceksts 	= executeScalar("select weighing_sts from weighing where weighing_id = '".$id."'");
		$cekweight	= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		
		$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$_POST['merchant']."'");
		$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$_POST['merchant']."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");
		// Pengecekan apabila pedagang merupakan pedagang trial di lapak
		if ($kat_merchant == 'Trial Lapak') { 
			$total1 		= executeScalar("select pay_total from payment where pay_id = '15'");
			$cost_tonase 	= executeScalar("select pay_total from payment where pay_id = '15'");
			if ($jml_kehadiran_hari_ini > 1) {
				$total1 		= 0;
				$cost_tonase 	= 0;
			}
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
		if ($kat_merchant == 'Trial Lapak Jalan') { 
			$total1 		= executeScalar("select pay_total from payment where pay_id = '16'");
			$cost_tonase 	= executeScalar("select pay_total from payment where pay_id = '16'");

			if ($jml_kehadiran_hari_ini > 1) {
				$total1 		= 0;
				$cost_tonase 	= 0;
			}
		}

		$update1 	= mysql_query("	update 	weighing 
									set 	nopol 				= '".$_POST['nopol']."',
											noncommodity		= '".$nc."',
											rafaksi				= '".$rafa."',
											netto				= '".$netto."',
											merchant_id			= '".$_POST['merchant']."',
											goods_id			= '".$_POST['goods']."',
											date_check 			= '".$dt_now."',
											cost_tonase			= '".$cost_tonase."',
											cost_real_tonase	= '".$cost_real_tonase."',
											cost_bongkar		= '".$cost_bongkar."',
											cost				= '".$total1."',
											cost2				= '".$total2."'
									where 	weighing_id 		= '".$id."'");

		$update2 = mysql_query("update 	weighing_mitra 
								set 	date_check 	= '".$dt_now."',
										merchant_id	= '".$_POST['merchant']."',
										goods_id	= '".$_POST['goods']."' 
								where 	weighing_id = '".$id."'");
		
		$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
								spv_user, 
								spv_action, 
								spv_target, 
								spv_nopol, 
								spv_merchant_id, 
								spv_reason, 
								action_time) 
							values (	'', 
								'".$name."', 
								'Menyimpan data pada menu (Data Belum Dicek/Data Sudah Dicek)',
								'".$id."', 
								'".$_POST['nopol']."', 
								'".$_POST['merchant']."', 
								'".$_POST['reason']."', 
								'".$dt_now."')");

		if($update1 && $update2 && $insert_spv){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Data berhasil disimpan";
			echo "<script>window.location=('check_edit.php?id=$id')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Data Gagal Diperbarui');
			window.history.back()</script>";
			exit();
		}
	}
	
	//Input Detail Nonkomodity
	elseif($task=='detail'){
		$id		= $_GET['id'];
		//Ambil Harga Tonase
		$tonase1 			= 0;
		$tonase2 			= 0;
		$tonase3 			= 0;
		$cost_tonase 		= 0;
		$cost_real_tonase 	= 0;
		$cost_bongkar 		= 0;
		
		//mencari berat barang nonkomoditi berdasarkan id nonkomoditi
		$nc1	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight1']."'");
		$nc2	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight2']."'");
		$nc3	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight3']."'");
		$nc4	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight4']."'");
		$nc5	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight5']."'");
		$nc6	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight6']."'");
		$nc7	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight7']."'");
		$nc8	= executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$_POST['weight8']."'");
		
		//Jika Jenis pedangang sayur / buar harga berbeda
		$merchant	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$commodity 	= executeScalar("select goods_id from weighing where weighing_id = '".$id."'");
		$jenis		= mb_substr($merchant, 0, 1, 'utf-8');
		if($jenis=='B'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
			// if($merchant=='B0072'&&$commodity==2){
			// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		elseif($jenis=='L'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		
		
		$tarifmin 		= executeScalar("select pay_total from payment where pay_id = '1'");
		$tonase3 		= $tonase1+$tonase2;

		$weight_in		= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
		$weight_out		= executeScalar("select weight_out from weighing where weighing_id = '".$id."'");
		$rafaksi		= executeScalar("select rafaksi from weighing where weighing_id = '".$id."'");
		$laborer		= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
		
		//update table weighing
		$noncommodity 	= 0;
		$netto			= 0;
		$price			= 0;
		
		//Hitung total nonkomoditi
		$noncommodity 	= ($nc1*$_POST['count1'])+($nc2*$_POST['count2'])+($nc3*$_POST['count3'])+($nc4*$_POST['count4'])
		+($nc5*$_POST['count5'])+($nc6*$_POST['count6'])+($nc7*$_POST['count7'])+($nc8*$_POST['count8']);
		$noncommodity 	= intval($noncommodity);
		
		if($_POST['weight1']==8||$_POST['weight2']==8||$_POST['weight3']==8||$_POST['weight4']==8||$_POST['weight5']==8||$_POST['weight6']==8||$_POST['weight7']==8||$_POST['weight8']==8){
			
		}
		
		//cek jika berat nonkomodity >= berat netto
		$netto		= executeScalar("select netto from weighing where weighing_id = '".$id."'");
		if($noncommodity>=$netto){
			echo "<script>window.alert('Berat Nonkomoditi lebih besar dari netto, Tolong Dicek lagi')</script>;
			<script>window.history.back()</script>";
			exit();
		}
		
		//cari tau apakah id nonkomoditi sudah ada d database
		$ada	= executeScalar("select count(weighing_id) from weighing_detail where weighing_id='".$id."'");
		//Jika data nonkomoditi sudah Ada d database (update data)
		if($ada>0){
			$update = mysql_query("update weighing_detail set noncommodity1 = '".$_POST['weight1']."',
															count1			= '".$_POST['count1']."',
															noncommodity2	= '".$_POST['weight2']."',
															count2			= '".$_POST['count2']."',
															noncommodity3	= '".$_POST['weight3']."',
															count3			= '".$_POST['count3']."',
															noncommodity4	= '".$_POST['weight4']."',
															count4			= '".$_POST['count4']."',
															noncommodity5	= '".$_POST['weight5']."',
															count5			= '".$_POST['count5']."',
															noncommodity6	= '".$_POST['weight6']."',
															count6			= '".$_POST['count6']."',
															noncommodity7	= '".$_POST['weight7']."',
															count7			= '".$_POST['count7']."',
															noncommodity8	= '".$_POST['weight8']."',
															count8			= '".$_POST['count8']."'
														where weighing_id 	= '".$id."'");
			if($update){
				//Jika nota telah dipecah
				$sts = executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
				if($sts=='Pecahan'||$sts=='Gabungan'){
					$netto		= executeScalar("select netto from weighing where weighing_id = '".$id."'");
					//Jika ada rafaksi atau tidak
					if($rafaksi!=0||$rafaksi!=''){
						$netto 	= $netto-$noncommodity-$rafaksi;
					}
					else{
						$netto 	= $netto-$noncommodity;
					}
				}
				else{
					//Jika ada rafaksi atau tidak
					if($rafaksi!=0||$rafaksi!=''){
						$netto 	= $weight_in-$weight_out-$noncommodity-$rafaksi;
					}
					else{
						$netto 	= $weight_in-$weight_out-$noncommodity;
					}
				}
				
				//jika mitra status tidak
				if($laborer=='Tidak'){
					$total1				= $netto*$tonase1;
					$total2				= $netto*$tonase1;
					$cost_tonase		= $total1;
					$cost_real_tonase	= $cost_tonase;
					$cost_tonase		= 0;
				}
				else{
					$total1				= $netto*$tonase3;
					$total2				= $netto*$tonase3;
					$cost_tonase		= $netto*$tonase1;
					$cost_real_tonase	= $cost_tonase;
					$cost_tonase		= $netto*$tonase2;
				}
				
				//menghitung netto & total bayar
				//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
				if($total1>$tarifmin){
					$total1 = $total1;
				}
				else{
					$total1 = $tarifmin;
				}

				if($cost_tonase>$tarifmin){
					$cost_tonase = $cost_tonase;
				}
				else{
					$cost_tonase = $tarifmin;
				}

				$update2 = mysql_query("update 	weighing 
										set 	noncommodity 		= '".$noncommodity."',
												netto				= '".$netto."',
												cost_tonase			= '".$cost_tonase."',
												cost_real_tonase	= '".$cost_real_tonase."',
												cost_bongkar		= '".$cost_bongkar."',
												cost				= '".$total1."',
												cost2				= '".$total2."'
										where 	weighing_id 		= '".$id."'");

				//Insert Log SPV
				$nopol 		 = executeScalar("select nopol from weighing where weighing_id = '".$id."'");
				$merchant_id = executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
				$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
														spv_user, 
														spv_action, 
														spv_target, 
														spv_nopol, 
														spv_merchant_id, 
														spv_reason, 
														action_time) 
													values (	'', 
														'".$name."', 
														'Menginputkan Berat NonKomoditi Sebesar -> ".$noncommodity." Kg',
														'".$id."', 
														'".$nopol."', 
														'".$merchant_id."', 
														'Ada kesalahan pada penginputan data berat nonkomoditi', 
														'".$dt_now."')");
				if($update2 && $insert_spv){
					echo "<script>window.location=('check_edit.php?id=$id')</script>";
					exit();
				}
				else{
					echo "<script>window.alert('Data Gagal 1');
					window.history.back()</script>";
					exit();
				}
			}
			else{
				echo "<script>window.alert('Data Gagal 2');
				window.history.back()</script>";
				exit();
			}
		}
		//Jika data nonkomoditi belum Ada d database (insert data)
		else{
			//simpan detail nonkomodity
			$insert = mysql_query("INSERT INTO `weighing_detail` (`weighing_id`, `noncommodity1`, 
			`count1`, `noncommodity2`, `count2`, `noncommodity3`, `count3`, `noncommodity4`, `count4`, 
			`noncommodity5`, `count5`, `noncommodity6`, `count6`, `noncommodity7`, `count7`, 
			`noncommodity8`, `count8`) 
			VALUES ('".$id."', '".$_POST['weight1']."', '".$_POST['count1']."', '".$_POST['weight2']."', 
			'".$_POST['count2']."', '".$_POST['weight3']."', '".$_POST['count3']."', '".$_POST['weight4']."', 
			'".$_POST['count4']."', '".$_POST['weight5']."', '".$_POST['count5']."', '".$_POST['weight6']."', 
			'".$_POST['count6']."', '".$_POST['weight7']."', '".$_POST['count7']."', '".$_POST['weight8']."', 
			'".$_POST['count8']."')");
			
			if($insert){
				//Jika nota telah dipecah
				$sts	= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
				if($sts=='Pecahan'||$sts=='Gabungan'){
					$netto		= executeScalar("select netto from weighing where weighing_id = '".$id."'");
					//Jika ada rafaksi atau tidak
					if($rafaksi!=0||$rafaksi!=''){
						$netto 	= $netto-$noncommodity-$rafaksi;
					}
					else{
						$netto 	= $netto-$noncommodity;
					}
				}
				else{
					//Jika ada rafaksi atau tidak
					if($rafaksi!=0||$rafaksi!=''){
						$netto 	= $weight_in-$weight_out-$noncommodity-$rafaksi;
					}
					else{
						$netto 	= $weight_in-$weight_out-$noncommodity;
					}
				}
				
				
				$laborer 	= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
				//jika mitra status tidak
				if($laborer=='Tidak'){
					$total1				= $netto*$tonase1;
					$total2				= $netto*$tonase1;
					$cost_tonase		= $total1;
					$cost_real_tonase	= $cost_tonase;
					$cost_bongkar		= 0;
				}else{
					$total1				= $netto*$tonase3;
					$total2				= $netto*$tonase3;
					$cost_tonase		= $netto*$tonase1;
					$cost_real_tonase	= $cost_tonase;
					$cost_bongkar		= $netto*$tonase2;
				}
				
				//menghitung netto & total bayar
				//Jika total bayar dibawah 10.000, maka akan dikenakan tarin min (Rp 10.000)
				if($total1>$tarifmin){
					$total1 = $total1;
				}
				else{
					$total1 = $tarifmin;
				}

				if($cost_tonase>$tarifmin){
					$cost_tonase = $cost_tonase;
				}
				else{
					$cost_tonase = $tarifmin;
				}
				
				//Update Tabel detail timbangan
				$update = mysql_query("	update weighing 
										set noncommodity 		= '".$noncommodity."',
											netto				= '".$netto."',
											cost_tonase			= '".$cost_tonase."',
											cost_real_tonase	= '".$cost_real_tonase."',
											cost_bongkar		= '".$cost_bongkar."',
											cost				= '".$total1."',
											cost2				= '".$total2."'
										where weighing_id 		= '".$id."'");

				//Insert Log SPV
				$nopol 		 = executeScalar("select nopol from weighing where weighing_id = '".$id."'");
				$merchant_id = executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
				$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
										spv_user, 
										spv_action, 
										spv_target, 
										spv_nopol, 
										spv_merchant_id, 
										spv_reason, 
										action_time) 
									values (	'', 
										'".$name."', 
										'Menginputkan Berat NonKomoditi Sebesar -> ".$noncommodity." Kg',
										'".$id."', 
										'".$nopol."', 
										'".$merchant_id."', 
										'Ada kesalahan pada penginputan data berat nonkomoditi', 
										'".$dt_now."')");
				if($update && $insert_spv){
					echo "<script>window.location=('check_edit.php?id=$id')</script>";
					exit();
				}
				else{
					echo "<script>window.alert('Data Gagal 3');
					window.history.back()</script>";
					exit();
				}
			}
			else{
				echo "<script>window.alert('Data Gagal 4');
				window.history.back()</script>";
				exit();
			}
		}
	}

	//Gabung Nota
	elseif($task=='merge'){
		$id			= $_GET['wid'];
		$merchant	= $_GET['mid'];
		$tonase1 	= 0;
		$tonase2 	= 0;
		$tonase3 	= 0;
		$cost1 		= 0;
		$cost2 		= 0;
		
		//memanggil semua data nota pedagang (yg dipilih) pada hari ini
		$checker    			= executeScalar("select checker_id from weighing where weighing_id = '".$id."'");
		$nopol 					= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$date_check 			= executeScalar("select date_check from weighing where weighing_id = '".$id."'");
		// $s 						= $date_check;
		$dt						= new DateTime($date_check);
		$daten 					= $dt->format('Y-m-d');
		$date_in 				= executeScalar("select date_in from weighing where weighing_id = '".$id."'");
		$date_out 				= executeScalar("select date_out from weighing where weighing_id = '".$id."'");
		$time_in 				= executeScalar("select time_in from weighing where weighing_id = '".$id."'");
		$time_out 				= executeScalar("select time_out from weighing where weighing_id = '".$id."'");
		$goods 					= executeScalar("select goods_id from weighing where merchant_id = '".$merchant."' and weighing_id = '".$id."'");
		// $laborer 				= executeScalar("select laborer_sts from weighing where merchant_id = '".$merchant."' and weighing_id = '".$id."'");
		$laborer 				= mysql_query("select laborer_sts from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$name_merchant	 				= executeScalar("select merchant_name from merchant where merchant_id = '".$merchant."'");
		$weight_in				= executeScalar("select sum(weight_in) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$weight_out 			= executeScalar("select sum(weight_out) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$noncommodity 			= executeScalar("select sum(noncommodity) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$netto					= executeScalar("select sum(netto) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$total 					= executeScalar("select sum(cost2) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$cost_bongkar 			= executeScalar("select sum(cost_bongkar) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$cekseasonalcommodity 	= executeScalar("select count(id_sc) from seasonalcommodity where goods_id = '".$goods."'");
		$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$merchant."'");
		$totRealTonase			= executeScalar("select sum(cost_real_tonase) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$totBongkar				= executeScalar("select sum(cost_bongkar) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		$totTonase				= executeScalar("select sum(cost_bongkar) from weighing where merchant_id = '".$merchant."' and weighing_sts = 'Keluar' and DATE(date_check) = '".$daten."'");
		
		//Jika Jenis pedangang sayur / buar harga berbeda
		$jenis		= mb_substr($merchant, 0, 1, 'utf-8');
		if($jenis=='B'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '9'");
			// if($merchant=='B0072'&&$goods==2){
			// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}
		elseif($jenis=='L'){
			$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
			$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak
		if ($kat_merchant == 'Trial Lapak') { 
			$tonase1 = executeScalar("select pay_total from payment where pay_id = '15'");
			// if ($jml_kehadiran_hari_ini > 1) {
			// 	$tonase1 = 0;
			// }
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
		if ($kat_merchant == 'Trial Lapak Jalan') { 
			$tonase1 = executeScalar("select pay_total from payment where pay_id = '16'");
			// if ($jml_kehadiran_hari_ini > 1) {
			// 	$tonase1 = 0;
			// }
		}

		if ($cekseasonalcommodity > 0) { 
			$tonase1 = executeScalar("select pay_total from payment where pay_id = '14'");
		}
		
		$tarifmin 			= executeScalar("select pay_total from payment where pay_id = '1'");
		$tonase3 			= $tonase1 + $tonase2;
		$cost_tonase 		= $netto * $tonase1;
		$cost_real_tonase 	= $netto * $tonase1;
		$totTagihan			= $totRealTonase + $totBongkar;

		if($jenis != 'B'){
			//Jika Total Dibawah Tarif Min
			if($total < $tarifmin){
				$cost1 				= $tarifmin;
				$cost2 				= $total;
			}else{
				$cost1 				= $total;
				$cost2 				= $total;
			}

			// Jika total cost tonase + biaya bongkar terkena minimum
			// if($totTagihan < $tarifmin){
			// 	$cost_tonase = $tarifmin;
			// }else{
			// 	$cost_tonase = $cost_tonase;
			// }

			// Jika total cost tonase saja terkena minimum
			// Keputusan Bu Tri 23/11 acuan terkena minimum tetap pada cost tonase yg lain mengikuti.
			if($cost_tonase < $tarifmin){
				$cost_tonase = $tarifmin;
			}else{
				$cost_tonase = $cost_tonase;
			}
		}
		
		//ambil id nota yg digabung
		$reffer	= executeScalar("select weighing_id from weighing where weighing_id = '".$id."'");

		// Kondisi laborer sts
		$has_laborer = false;
		while ($row = mysql_fetch_array($laborer)) {
			if ($row['laborer_sts'] === 'Ya') {
				$has_laborer = true;
				break;
			}
		}
		$laborer_sts = $has_laborer ? 'Ya' : 'Tidak';
		
		//Membuat data transaksi timbangan baru
		$insert = mysql_query("INSERT INTO `weighing` (	`weighing_id`, 
		`nopol`, 
		`weight_in`, 
		`weight_out`, 
		`noncommodity`, 
		`netto`, 
		`merchant_id`, 
		`goods_id`, 
		`date_in`, 
		`date_out`, 
		`date_check`, 
		`time_in`, 
		`time_out`, 
		`checker_id`, 
		`laborer_sts`, 
		`cost_tonase`,
		`cost_real_tonase`,
		`cost_bongkar`,
		`cost`, 
		`cost2`, 
		`weighing_sts`, 
		`weighing_sts2`, 
		`reffer`) 
		VALUES ('', 
		'".$nopol."', 
		'".$weight_in."', 
		'".$weight_out."', 
		'".$noncommodity."', 
		'".$netto."', 
		'".$merchant."', 
		'".$goods."', 
		'".$date_in."', 
		'".$date_out."', 
		'".$date_check."', 
		'".$time_in."', 
		'".$time_out."', 
		'".$checker."', 
		'".$laborer_sts."',
		'".$cost_tonase."',
		'".$totRealTonase."', 
		'".$totBongkar."',
		'".$cost1."', 
		'".$cost2."', 
		'Keluar', 
		'Gabungan', 
		'".$reffer."')");


		if($insert){
			//Ambil weighing_id dr query yg dilakukan sebelumnya
			$wid	= mysql_insert_id();
			//update status transaksi yg telah digabung
			$update = mysql_query("	update 	weighing 
									set 	weighing_sts 		= 'Batal',
											weighing_sts2 		= 'Digabung',
											reffer				= '".$wid."'
									where 	merchant_id			= '".$merchant."' and
											weighing_sts 		= 'Keluar' and
											DATE(date_check)	= '".$daten."' and
											weighing_id			!= '".$wid."'");
									
			$new_reffer	= executeScalar("select weighing_id from weighing where reffer = '".$id."'");
			$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
											spv_user, 
											spv_action, 
											spv_target, 
											spv_nopol, 
											spv_merchant_id, 
											spv_reason, 
											action_time) 
										values (	'', 
											'".$name."', 
											'Menggabungkan data menjadi data baru dengan ID -> ".$new_reffer."',
											'".$id."', 
											'".$nopol."', 
											'".$merchant."', 
											'Ada kesalahan pada penggabungan data', 
											'".$dt_now."')");
			//update laborer_fee_weighing_id nota yg digabung menjadi id nota yg gabungan/terbaru
			// $update2 = mysql_query("update laborer_fee l, weighing w set l.weighing_id 	= '".$wid."'
			// 														where l.weighing_id = w.weighing_id and
			// 															w.merchant_id	= '".$merchant."' and
			// 															weighing_sts2 	= 'Digabung' and
			// 															DATE(date_check)= '".$daten."'");
			if($update && $insert_spv){//jika update sts transaksi & mitra berhasil
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Data berhasil digabung";
				echo "<script>window.location=('check_edit.php?id=$wid')</script>";
				exit();
			}
			else{//Jika update gagal
				echo "<script>window.alert('Data Gagal'); 
				window.history.back()</script>";
				exit();
			}
		}
		else{
			echo "<script>window.alert('Data Gagal');
			window.history.back()</script>";
			exit();
		}
	}
	
	//Pecah Nota
	elseif($task=='breakdown'){
		$id			= $_GET['id'];
		$sts		= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
		if($sts != 'Dipecah'){
			$mc1		= $_POST['mc1'];
			$mc2		= $_POST['mc2'];
			$mc3		= $_POST['mc3'];
			$mc4		= $_POST['mc4'];
			$mc5		= $_POST['mc5'];
			$mc6		= $_POST['mc6'];
			$mc7		= $_POST['mc7'];
			$mc8		= $_POST['mc8'];
			$mc9		= $_POST['mc9'];
			$mc10		= $_POST['mc10'];
			$n1			= $_POST['n1'];
			$n2			= $_POST['n2'];
			$n3			= $_POST['n3'];
			$n4			= $_POST['n4'];
			$n5			= $_POST['n5'];
			$n6			= $_POST['n6'];
			$n7			= $_POST['n7'];
			$n8			= $_POST['n8'];
			$n9			= $_POST['n9'];
			$n10		= $_POST['n10'];
			$merchant	= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
			
			//Cek pecah nota harus minimal 2 pedagang
			if (
				($n2 == 0 && $mc2 == '') &&
				($n3 == 0 && $mc3 == '') &&
				($n4 == 0 && $mc4 == '') &&
				($n5 == 0 && $mc5 == '') &&
				($n6 == 0 && $mc6 == '') &&
				($n7 == 0 && $mc7 == '') &&
				($n8 == 0 && $mc8 == '') &&
				($n9 == 0 && $mc9 == '') &&
				($n10 == 0 && $mc10 == '')
			) {
				echo "<script>window.alert('Pecah data minimal ada 2 pedagang, dan netto tidak boleh kosong');
				window.history.back()</script>";
				exit();
			} elseif (
				($n1 != 0 && $mc1 == '') ||
				($n2 != 0 && $mc2 == '') ||
				($n3 != 0 && $mc3 == '') ||
				($n4 != 0 && $mc4 == '') ||
				($n5 != 0 && $mc5 == '') ||
				($n6 != 0 && $mc6 == '') ||
				($n7 != 0 && $mc7 == '') ||
				($n8 != 0 && $mc8 == '') ||
				($n9 != 0 && $mc9 == '') ||
				($n10 != 0 && $mc10 == '')
			) {
				echo "<script>window.alert('Netto terisi tetapi nama pedagang tidak ada, cek kembali!');
				window.history.back()</script>";
				exit();
			} elseif (
				($n1 == 0 && $mc1 != '') ||
				($n2 == 0 && $mc2 != '') ||
				($n3 == 0 && $mc3 != '') ||
				($n4 == 0 && $mc4 != '') ||
				($n5 == 0 && $mc5 != '') ||
				($n6 == 0 && $mc6 != '') ||
				($n7 == 0 && $mc7 != '') ||
				($n8 == 0 && $mc8 != '') ||
				($n9 == 0 && $mc9 != '') ||
				($n10 == 0 && $mc10 != '')
			) {
				echo "<script>window.alert('Nama pedagang terisi tetapi netto tidak ada, cek kembali!');	
				window.history.back()</script>";
				exit();
			}
			
			//Cek jika pedagang awal tidak ada namanya di dalam pecah nota
			if($mc1==$merchant||$mc2==$merchant||$mc3==$merchant||$mc4==$merchant||$mc5==$merchant||
			$mc6==$merchant||$mc7==$merchant||$mc8==$merchant||$mc9==$merchant||$mc10==$merchant){
				//Netto toleransi 0,5% dr netto awal
				$nettotal 	= 0;
				$netto2 	= 0;
				$toleran 	= 0;
				$netto		= executeScalar("select netto from weighing where weighing_id = '".$id."'");
				$toleran 	= intval($netto*0.5/100);
				$netto2		= $netto-$toleran;
				$nettotal 	= $n1+$n2+$n3+$n4+$n5+$n6+$n7+$n8+$n9+$n10;
				
				//Jika total berat yg dipecah sesuai dgn batas toleransi
				if($nettotal>=$netto2&&$nettotal<=$netto){
					//update status transaksi yg lama
					$update = mysql_query("	update weighing 
											set weighing_sts = 'Batal',
												weighing_sts2 	= 'Dipecah'
											where weighing_id	= '".$id."'");

					//update status mitra nota yg dipecah menjadi batal
					$update2 = mysql_query("update laborer_fee set laborer_fee_sts = 'Batal' where weighing_id = '".$id."'");
					
					// Update pembatalan weighing mitra karena nota dipecah
					$update3 = mysql_query("	update weighing_mitra 
											set weighing_sts 	= 'Batal',
												weighing_sts2 	= 'Dipecah'
											where weighing_id	= '".$id."'");
					
					//memanggil tarif tonase
					$cost 				= 0;
					$cost1 				= 0;
					$cost2 				= 0;
					$tonase1 			= 0;
					$tonase2 			= 0;
					$tonase3 			= 0;
					$cost_tonase 		= 0;
					$cost_real_tonase 	= 0;
					$cost_bongkar 		= 0;
					$tonase2 			= 0;
					$tonase3 			= 0;
					$commodity 			= executeScalar("select goods_id from weighing where weighing_id = '".$id."'");
					$nopol				= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
					$weight_in			= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
					$weight_out			= executeScalar("select weight_out from weighing where weighing_id = '".$id."'");
					$nid 				= executeScalar("select noncommodity_id from weighing where weighing_id = '".$id."'");
					$noncommodity 		= executeScalar("select noncommodity from weighing where weighing_id = '".$id."'");
					$goods				= executeScalar("select goods_id from weighing where weighing_id = '".$id."'");
					$date_in			= executeScalar("select date_in from weighing where weighing_id = '".$id."'");
					$time_out			= executeScalar("select time_out from weighing where weighing_id = '".$id."'");
					$date_out			= executeScalar("select date_out from weighing where weighing_id = '".$id."'");
					$time_in			= executeScalar("select time_in from weighing where weighing_id = '".$id."'");
					$laborer 			= executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
					$rafa	 			= executeScalar("select rafaksi from weighing where weighing_id = '".$id."'");
					
					//Jika Jenis pedangang sayur / buar harga berbeda
					$jenis			= mb_substr($merchant, 0, 1, 'utf-8');
					if($jenis=='B'){
						$tonase1 	= executeScalar("select pay_total from payment where pay_id = '8'");
						$tonase2 	= executeScalar("select pay_total from payment where pay_id = '9'");
						// if($merchant=='B0072'&&$commodity==2){
						// 	$tonase1 = executeScalar("select pay_total from payment where pay_id = '2'");
						// }
					}
					elseif($jenis=='S'){
						$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
						$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
					}
					elseif($jenis=='L'){
						$tonase1 	= executeScalar("select pay_total from payment where pay_id = '2'");
						$tonase2 	= executeScalar("select pay_total from payment where pay_id = '3'");
					}
					
					$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
					$tonase3 	= $tonase1 + $tonase2;
					
					// PISAH NOTA KE 1 ======================================================================================================================
					if($mc1!=''&&$n1!=''){
						if($laborer == "Ya"){
							$cost 				= $n1 * $tonase3;
							$cost_tonase		= $n1 * $tonase1;
							$cost_real_tonase	= $n1 * $tonase1;
							$cost_bongkar		= $n1 * $tonase2;
						}
						else{
							$cost 				= $n1 * $tonase1;
							$cost_tonase 		= $n1 * $tonase1;
							$cost_real_tonase 	= $n1 * $tonase1;
							$cost_bongkar 		= $n1 * 0;
						}
						
						//jika total harga krg dari tarif min
						if($cost < $tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}

						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}
						
						$insert1 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`, 
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n1."', '".$checker."', '".$mc1."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."', 
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 2 ======================================================================================================================
					if($mc2!=''&&$n2!=''){
						if($laborer == "Ya"){
							$cost 				= $n2 * $tonase3;
							$cost_tonase		= $n2 * $tonase1;
							$cost_real_tonase	= $n2 * $tonase1;
							$cost_bongkar		= $n2 * $tonase2;
						}
						else{
							$cost 				= $n2 * $tonase1;
							$cost_tonase 		= $n2 * $tonase1;
							$cost_real_tonase 	= $n2 * $tonase1;
							$cost_bongkar 		= $n2 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert2 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n2."', '".$checker."', '".$mc2."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 3 ======================================================================================================================
					if($mc3!=''&&$n3!=''){
						if($laborer == "Ya"){
							$cost 				= $n3 * $tonase3;
							$cost_tonase		= $n3 * $tonase1;
							$cost_real_tonase	= $n3 * $tonase1;
							$cost_bongkar		= $n3 * $tonase2;
						}
						else{
							$cost 				= $n3 * $tonase1;
							$cost_tonase 		= $n3 * $tonase1;
							$cost_real_tonase 	= $n3 * $tonase1;
							$cost_bongkar 		= $n3 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert3 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n3."', '".$checker."', '".$mc3."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 4 ======================================================================================================================
					if($mc4!=''&&$n4!=''){
						if($laborer == "Ya"){
							$cost 				= $n4 * $tonase3;
							$cost_tonase		= $n4 * $tonase1;
							$cost_real_tonase	= $n4 * $tonase1;
							$cost_bongkar		= $n4 * $tonase2;
						}
						else{
							$cost 				= $n4 * $tonase1;
							$cost_tonase 		= $n4 * $tonase1;
							$cost_real_tonase 	= $n4 * $tonase1;
							$cost_bongkar 		= $n4 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert4 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n4."', '".$checker."', '".$mc4."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}
					// PISAH NOTA KE 5 ======================================================================================================================
					if($mc5!=''&&$n5!=''){
						if($laborer == "Ya"){
							$cost 				= $n5 * $tonase3;
							$cost_tonase		= $n5 * $tonase1;
							$cost_real_tonase	= $n5 * $tonase1;
							$cost_bongkar		= $n5 * $tonase2;
						}
						else{
							$cost 				= $n5 * $tonase1;
							$cost_tonase 		= $n5 * $tonase1;
							$cost_real_tonase 	= $n5 * $tonase1;
							$cost_bongkar 		= $n5 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert5 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n5."', '".$checker."', '".$mc5."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 6 ======================================================================================================================
					if($mc6!=''&&$n6!=''){
						if($laborer == "Ya"){
							$cost 				= $n6 * $tonase3;
							$cost_tonase		= $n6 * $tonase1;
							$cost_real_tonase	= $n6 * $tonase1;
							$cost_bongkar		= $n6 * $tonase2;
						}
						else{
							$cost 				= $n6 * $tonase1;
							$cost_tonase 		= $n6 * $tonase1;
							$cost_real_tonase 	= $n6 * $tonase1;
							$cost_bongkar 		= $n6 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert6 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n6."', '".$checker."', '".$mc6."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 7 ======================================================================================================================
					if($mc7!=''&&$n7!=''){
						if($laborer == "Ya"){
							$cost 				= $n7 * $tonase3;
							$cost_tonase		= $n7 * $tonase1;
							$cost_real_tonase	= $n7 * $tonase1;
							$cost_bongkar		= $n7 * $tonase2;
						}
						else{
							$cost 				= $n7 * $tonase1;
							$cost_tonase 		= $n7 * $tonase1;
							$cost_real_tonase 	= $n7 * $tonase1;
							$cost_bongkar 		= $n7 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert7 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n7."', '".$checker."', '".$mc7."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 8 ======================================================================================================================
					if($mc8!=''&&$n8!=''){
						if($laborer == "Ya"){
							$cost 				= $n8 * $tonase3;
							$cost_tonase		= $n8 * $tonase1;
							$cost_real_tonase	= $n8 * $tonase1;
							$cost_bongkar		= $n8 * $tonase2;
						}
						else{
							$cost 				= $n8 * $tonase1;
							$cost_tonase 		= $n8 * $tonase1;
							$cost_real_tonase 	= $n8 * $tonase1;
							$cost_bongkar 		= $n8 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert8 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n8."', '".$checker."', '".$mc8."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 9 ======================================================================================================================
					if($mc9!=''&&$n9!=''){
						if($laborer == "Ya"){
							$cost 				= $n9 * $tonase3;
							$cost_tonase		= $n9 * $tonase1;
							$cost_real_tonase	= $n9 * $tonase1;
							$cost_bongkar		= $n9 * $tonase2;
						}
						else{
							$cost 				= $n9 * $tonase1;
							$cost_tonase 		= $n9 * $tonase1;
							$cost_real_tonase 	= $n9 * $tonase1;
							$cost_bongkar 		= $n9 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}

						$insert9 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n9."', '".$checker."', '".$mc9."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					// PISAH NOTA KE 10 ======================================================================================================================
					if($mc10!=''&&$n10!=''){
						if($laborer == "Ya"){
							$cost 				= $n10 * $tonase3;
							$cost_tonase		= $n10 * $tonase1;
							$cost_real_tonase	= $n10 * $tonase1;
							$cost_bongkar		= $n10 * $tonase2;
						}
						else{
							$cost 				= $n10 * $tonase1;
							$cost_tonase 		= $n10 * $tonase1;
							$cost_real_tonase 	= $n10 * $tonase1;
							$cost_bongkar 		= $n10 * 0;
						}
						
						if($cost<$tarifmin){
							$cost1 = $tarifmin;
							$cost2 = $cost;
						}
						else{
							$cost1 = $cost;
							$cost2 = $cost;
						}
						
						//jika total harga tonase krg dari tarif min
						if($cost_tonase < $tarifmin){
							$cost_tonase = $tarifmin;
						}
						else{
							$cost_tonase = $cost_tonase;
						}
						$insert10 = mysql_query("INSERT INTO `weighing` (`weighing_id`, `nopol`, 
						`weight_in`, `weight_out`, `rafaksi`, `netto`, 
						`checker_id`, `merchant_id`, `goods_id`, `date_in`, `date_out`, `time_in`, `time_out`, `laborer_sts`, 
						`cost_tonase`, `cost_real_tonase`, `cost_bongkar`, `cost`, `cost2`,  
						`weighing_sts`, `weighing_sts2`, `reffer`) 
						VALUES ('', '".$nopol."', '".$weight_in."', '".$weight_out."', '".$rafa."', '".$n10."', '".$checker."', '".$mc."', '".$goods."', 
						'".$date_in."', '".$date_out."', '".$time_in."', '".$time_out."', '".$laborer."', 
						'".$cost_tonase."', '".$cost_real_tonase."', '".$cost_bongkar."',  '".$cost1."', '".$cost2."',
						'Keluar', 'Pecahan', '".$id."')");
					}

					if($insert1||$insert2||$insert3||$insert4||$insert5||$insert6||$insert7||$insert8||$insert9||$insert10){
						$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
													spv_user, 
													spv_action, 
													spv_target, 
													spv_nopol, 
													spv_merchant_id, 
													spv_reason, 
													action_time) 
												values (	'', 
													'".$name."', 
													'Memecah data',
													'".$id."', 
													'".$nopol."', 
													'".$merchant."', 
													'Ada kesalahan pada saat pecah data', 
													'".$dt_now."')");
						if($update && $insert_spv){
							// Alert Information
							$_SESSION['success'] = true;
							$_SESSION['title'] = "Berhasil";
							$_SESSION['message'] = "Pecah data berhasil";
							echo "<script>window.location=('check.php')</script>";
							exit();
						}else{
							echo "<script>window.alert('Data Gagal');
							window.history.back()</script>";
							exit();
						}
					}else{
						echo "<script>window.alert('Pecah Nota Tidak berhasil ".$insert1." ');
						window.history.back()</script>";
						exit();
					}
				}else{
					echo "<script>window.alert('Jumlah Netto yang dimasukkan tidak cocok, Toleransi selisih hanya 0,5%');
					window.history.back()</script>";
					exit();
				}
			}else{
				echo "<script>window.alert('Nama pedagang tidak ada di dalam pecah nota, mohon di cek lagi');
				window.history.back()</script>";
				exit();
			}
		}else{
			echo "<script>window.alert('Nota telah dipecah, tidak bisa dipecah lagi');
			window.history.back()</script>";
			exit();
		}
	}
	
	//Pembagian Upah Mitra
	elseif($task=='Mitra'){
		$id					= $_GET['id'];
		$total1				= 0;
		$total2				= 0;
		$cost_tonase		= 0;
		$cost_real_tonase	= 0;
		$cost_bongkar		= 0;
		$cost_bagi_mitra	= 0;
		$cost_simpan_mitra	= 0;
		$cost_bagi_mandor	= 0;
		$cost_simpan_mandor	= 0;
		$tarif1 			= 0;
		$laborer 			= '';
		$N					= 0;	
		$N2					= 0;
		
		//Set tarif harga jika Jenis pedagang berbeda 
		$merchant 				= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$jenis					= mb_substr($merchant, 0, 1, 'utf-8');
		$commodity 				= executeScalar("select goods_id from weighing where weighing_id = '".$id."'");
		$stsnota 				= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
		$cekseasonalcommodity 	= executeScalar("select count(id_sc) from seasonalcommodity where goods_id = '".$commodity."'");
		
		//Tarif 2 adlh tarif mitra sdgkan tarif1 adalah tarif normal tanpa mitra
		if($jenis =='B'){
			$tarif1 = executeScalar("select pay_total from payment where pay_id = '8'"); // Tarif tonase buah
			$tarif2 = executeScalar("select pay_total from payment where pay_id = '9'"); // Tarif mitra & mandor buah
			// if($merchant=='B0072'&&$commodity==2){
			// 	$tarif1 = executeScalar("select pay_total from payment where pay_id = '2'");
			// }
		}
		elseif($jenis=='S'){
			$tarif1 = executeScalar("select pay_total from payment where pay_id = '2'"); // Tarif tonase sayur
			$tarif2 = executeScalar("select pay_total from payment where pay_id = '3'"); // Tarif mitra & mandor sayur
		}
		elseif($jenis=='L'){
			$tarif1 = executeScalar("select pay_total from payment where pay_id = '2'"); // Tarif tonase lainnya
			$tarif2 = executeScalar("select pay_total from payment where pay_id = '3'"); // Tarif mitra & mandor lainnya
		}

		if ($cekseasonalcommodity > 0) { 
			$tarif1 = executeScalar("select pay_total from payment where pay_id = '14'");
		}

		//tarifmin adlh tarif minimal timbangan (Saat Ini 15.000)
		$tarifmin 	= executeScalar("select pay_total from payment where pay_id = '1'");
		
		//tarif3 adlh tarif normal + mitra
		$tarif3 	= $tarif1 + $tarif2;
		$netto 		= executeScalar("select netto from weighing where weighing_id = '".$id."'");
		
		//Jika total bayar dibawah 15.000, maka akan dikenakan tarin min (Rp 15.000)
		if(empty($_POST['mitra']) && empty($_POST['mandor'])){
			$laborer 			= 'Tidak';
			$total1				= $netto * $tarif1; //set lsg dgn tarik tanpa mitra
			$total2				= $total1;
			$cost_tonase	 	= $netto * $tarif1;
			$cost_real_tonase 	= $netto * $tarif1;
			$cost_bongkar 		= '0';

			if($jenis != 'B'){
				if($total1<$tarifmin){
					$total1 = $tarifmin;
				}
	
				if($cost_tonase < $tarifmin){
					$cost_tonase = $tarifmin;
				}
			}

			// Segment pengecekan tarif untuk pedagang trial ===========================================================================
			$id_merchant 			= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
			$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$id_merchant."'");
			$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$id_merchant."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");

			// Pengecekan apabila pedagang merupakan pedagang trial di lapak
			if ($kat_merchant == 'Trial Lapak') { 
				$cost_tonase = executeScalar("select pay_total from payment where pay_id = '15'");
				if ($jml_kehadiran_hari_ini > 1) {
					$cost_tonase = 0;
				}
			}

			// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
			if ($kat_merchant == 'Trial Lapak Jalan') { 
				$cost_tonase = executeScalar("select pay_total from payment where pay_id = '16'");
				if ($jml_kehadiran_hari_ini > 1) {
					$cost_tonase = 0;
				}
			}
			// ==========================================================================================================================

			$update1 = mysql_query("update laborer_fee set laborer_fee_sts = 'Batal' where weighing_id = '".$id."'");
			//Update Status Mitra = Tidak & Total Biaya tanpa mitra
			$update2 = mysql_query("update weighing set laborer_sts 		= 'Tidak',
														cost_tonase			= '".$cost_tonase."',
														cost_real_tonase	= '".$cost_real_tonase."',
														cost_bongkar		= '0',
														cost				= '".$total1."',
														cost2				= '".$total2."' 
												where weighing_id 			= '".$id."'");
											
			$update3 = mysql_query("update 	weighing_mitra 
									set 	date_check 	= '".$dt_now."'
									where 	weighing_id = '".$id."'");

			if ($update1 && $update2 && $update3) {
				echo "<script>window.location = 'check_edit.php?id={$id}';</script>";
				exit();
			}
			else{
				echo "<script>window.alert('Mitra Error');
				window.location = 'check_edit.php?id={$id}';</script>";
				exit();
			}
		}
		else{
			$laborer = 'Ya';
			// Pengecekan pemilihan tarif berdasarkan jenis merchant
			if($jenis == 'B'){
				$mitrabagi 	= executeScalar("select pay_total from payment where pay_id = '10'"); //Tarif Mitra Dibagi Buah
				$mitrasmpn 	= executeScalar("select pay_total from payment where pay_id = '11'"); //Tarif Mitra Disimpan Buah
				$mandorbagi = executeScalar("select pay_total from payment where pay_id = '12'"); //Tarif Mandor DiBagi Buah
				$mandorsmpn	= executeScalar("select pay_total from payment where pay_id = '13'"); //Tarif Mandor Disimpan Buah
				$total1		= $netto * $tarif3;
			}
			elseif($jenis=='S'){
				$mitrabagi 	= executeScalar("select pay_total from payment where pay_id = '4'"); //Tarif Mitra Dibagi Sayur
				$mitrasmpn 	= executeScalar("select pay_total from payment where pay_id = '5'"); //Tarif Mitra Disimpan Sayur
				$mandorbagi = executeScalar("select pay_total from payment where pay_id = '6'"); //Tarif Mandor DiBagi Sayur
				$mandorsmpn	= executeScalar("select pay_total from payment where pay_id = '7'"); //Tarif Mandor Disimpan Sayur
				$total1		= $netto*$tarif3;
			}
			elseif($jenis=='L'){
				$mitrabagi 	= executeScalar("select pay_total from payment where pay_id = '4'"); //Tarif Mitra Dibagi Sayur
				$mitrasmpn 	= executeScalar("select pay_total from payment where pay_id = '5'"); //Tarif Mitra Disimpan Sayur
				$mandorbagi = executeScalar("select pay_total from payment where pay_id = '6'"); //Tarif Mandor DiBagi Sayur
				$mandorsmpn	= executeScalar("select pay_total from payment where pay_id = '7'"); //Tarif Mandor Disimpan Sayur
				$total1		= $netto*$tarif3;
			}
						
			$total2				= $total1;
			$cost_tonase	 	= $netto * $tarif1;
			$cost_real_tonase 	= $netto * $tarif1;
			$cost_bongkar 		= $netto * $tarif2;
			
			//Ambil tarif mitra
			$total4 	= $netto * $tarif2; //Total4 (Biaya Angkut) = Brt Bersih (Netto) x Tarif Mitra
			$kuli 		= 0;
			$k_dibagi 	= 0;
			$k_disimpan = 0;
			$mandor 	= 0;
			$m_dibagi 	= 0;
			$m_disimpan = 0;
			$pios 		= 0;
			
			//Tgl & Waktu Pembayaran Mitra = tgl keluar
			$date_out 	= executeScalar("select date_out from weighing where weighing_id = '".$id."'");
			$time_out 	= executeScalar("select time_out from weighing where weighing_id = '".$id."'");
			
			//hitung Tarif permitra
			if(empty($_POST['mandor'])){//jika tidak ada mandor
				$N 			= count($_POST['mitra']); //N = Jml Mitra Yg Kerja
				$N2			= 0;
				$kuli 		= $netto/$N; 
				$k_dibagi 	= $kuli*$mitrabagi;
				$k_disimpan = $kuli*$mitrasmpn;
				$pios		= $netto*($mandorbagi+$mandorsmpn);
			}
			elseif(empty($_POST['mitra'])){//jika tidak ada Kuli
				$N2 		= count($_POST['mandor']); //N2 = Jml Mandor Yg Kerja
				$N			= 0;
				$mandor 	= $netto/$N2;
				$m_dibagi 	= $mandor*$mandorbagi;
				$m_disimpan = $mandor*$mandorsmpn;
				$pios		= $netto*($mitrabagi+$mitrasmpn);
				
				for($i=0; $i < $N2; $i++){
					$mandor	= $_POST['mandor'];
					if($i==0){
						$ada	= executeScalar("select count(weighing_id) from laborer_fee where weighing_id = '".$id."'");
						//$ketemu	= mysql_num_rows($ada);
						if($ada>0){
							mysql_query("delete from laborer_fee where weighing_id = '".$id."'");
						}
						mysql_query("insert into laborer_fee (	weighing_id, 
																$mandor[$i], 
																$mandor[$i]_save, 
																pios_fee, 
																laborer_fee_sts, 
																laborer_fee_date) 
											value (	'".$id."', 
													'".$m_dibagi."', 
													'".$m_disimpan."', 
													'".$pios."', 
													'Baru', 
													'".$date_out.' 
													'.$time_out."' )");
					}elseif($i>0){
						mysql_query("update laborer_fee set $mandor[$i] 		= '".$m_dibagi."',
															$mandor[$i]_save	= '".$m_disimpan."'
													where weighing_id 			= '".$id."'");
					}
				}
			}
			else{//jika ada mitra & mandor
				$N 			= count($_POST['mitra']); //N = Jml Mitra Yg Kerja
				$N2 		= count($_POST['mandor']); //N2 = Jml Mandor Yg Kerja
				$kuli 		= $netto/$N; //Biaya Kuli = Tarif mitra(Rp. 12) / Jml Kuli yg Kerja 
				$k_dibagi 	= $kuli*$mitrabagi;
				$k_disimpan = $kuli*$mitrasmpn;
				$mandor 	= $netto/$N2;
				$m_dibagi 	= $mandor*$mandorbagi;
				$m_disimpan = $mandor*$mandorsmpn;
			}
			
			//Buat proses pengecekan apakah id sdh atau atau blm == NULL
			for($i=0; $i < $N; $i++){	
				$mitra	= $_POST['mitra'];
				if($i==0){//query db untuk data yg pertama
					$ada		= executeScalar("select count(weighing_id) from laborer_fee where weighing_id = '".$id."'");
					//$ketemu	= mysql_num_rows($ada);
					//jika data sudah ada
					if($ada>0){
						mysql_query("delete from laborer_fee where weighing_id = '".$id."'");
					}
					//jika tidak ada mandor
					if(empty($_POST['mandor'])){
						mysql_query("insert into laborer_fee (weighing_id, $mitra[$i], $mitra[$i]_save, pios_fee, laborer_fee_sts, laborer_fee_date) 
						value ('".$id."', '".$k_dibagi."', '".$k_disimpan."', '".$pios."', 'Baru', '".$date_out.' '.$time_out."' )");
					}else{//jika ada mandor
						mysql_query("insert into laborer_fee (weighing_id, $mitra[$i], $mitra[$i]_save, laborer_fee_sts, laborer_fee_date) 
						value ('".$id."', '".$k_dibagi."', '".$k_disimpan."', 'Baru', '".$date_out.' '.$time_out."' )");
					}
				}elseif($i>0){//query db untuk data yg kedua dst
					mysql_query("update laborer_fee set $mitra[$i] 	= '".$k_dibagi."',
													$mitra[$i]_save	= '".$k_disimpan."'
												where weighing_id 	= '".$id."'");
				}
			}

			// Input data mandor ke laborer_fee
			// Cek apakah mandor ada atau tidak
			if ($N2 > 0) {
				for($i = 0; $i < $N2; $i++){	
					$mandor	= $_POST['mandor'];
					mysql_query("update laborer_fee set $mandor[$i] 	= '".$m_dibagi."',
													$mandor[$i]_save 	= '".$m_disimpan."'
													where weighing_id 	= '".$id."'");
				}
			}
			// else{
			// 	echo "<script>window.alert('Mandor Tidak Ditemukan');
			// 		window.location=('check_edit.php?id=$id')</script>";
			// 		exit();
			// }

			// Input data ke weighing mitra untuk data pelunasan mitra
			$merchant_id 		= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
			$goods_id 			= executeScalar("select goods_id from weighing where weighing_id = '".$id."'");
			$nopol 				= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
			$weight_in 			= executeScalar("select weight_in from weighing where weighing_id = '".$id."'");
			$weight_out 		= executeScalar("select weight_out from weighing where weighing_id = '".$id."'");
			$noncommodity_id 	= executeScalar("select noncommodity_id from weighing where weighing_id = '".$id."'");
			$noncommodity 		= executeScalar("select noncommodity from weighing where weighing_id = '".$id."'");
			$netto2 			= intval($weight_in) - intval($weight_out) - intval($noncommodity);
			if ($stsnota == 'Pecahan') {
				// Ambil netto dari nota tonase karena nota dipecah tidak memiliki weight in dan out
				$netto2 		= executeScalar("select netto from weighing where weighing_id = '".$id."'");
			}
			$date_in 			= executeScalar("select date_in from weighing where weighing_id = '".$id."'");
			$time_in 			= executeScalar("select time_in from weighing where weighing_id = '".$id."'");
			$date_out 			= executeScalar("select date_out from weighing where weighing_id = '".$id."'");
			$time_out 			= executeScalar("select time_out from weighing where weighing_id = '".$id."'");
			$weighing_sts 		= executeScalar("select weighing_sts from weighing where weighing_id = '".$id."'");
			$weighing_sts2 		= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
			$reffer 			= executeScalar("select reffer from weighing where weighing_id = '".$id."'");
			$p1 				= executeScalar("select p1 from weighing where weighing_id = '".$id."'");
			$p2 				= executeScalar("select p2 from weighing where weighing_id = '".$id."'");

			if($jenis == 'B'){
				$tarifbongkar 	= executeScalar("select pay_total from payment where pay_id = '9'");
				$tbb 			= executeScalar("select pay_total from payment where pay_id = '10'"); // Total bagi buruh
				$tsb 			= executeScalar("select pay_total from payment where pay_id = '11'"); // Total simpan buruh
				$tbm 			= executeScalar("select pay_total from payment where pay_id = '12'"); // Total bagi mandor
				$tsm 			= executeScalar("select pay_total from payment where pay_id = '13'"); // Total simpan mandor
			}elseif($jenis == 'S'){
				$tarifbongkar = executeScalar("select pay_total from payment where pay_id = '3'");
				$tbb 			= executeScalar("select pay_total from payment where pay_id = '4'"); // Total bagi buruh
				$tsb 			= executeScalar("select pay_total from payment where pay_id = '5'"); // Total simpan buruh
				$tbm 			= executeScalar("select pay_total from payment where pay_id = '6'"); // Total bagi mandor
				$tsm 			= executeScalar("select pay_total from payment where pay_id = '7'"); // Total simpan mandor
			}elseif($jenis == 'L'){
				$tarifbongkar = executeScalar("select pay_total from payment where pay_id = '3'");
				$tbb 			= executeScalar("select pay_total from payment where pay_id = '4'"); // Total bagi buruh
				$tsb 			= executeScalar("select pay_total from payment where pay_id = '5'"); // Total simpan buruh
				$tbm 			= executeScalar("select pay_total from payment where pay_id = '6'"); // Total bagi mandor
				$tsm 			= executeScalar("select pay_total from payment where pay_id = '7'"); // Total simpan mandor
			}

			if ($cekseasonalcommodity > 0) { 
				$tarifbongkar 	= executeScalar("select pay_total from payment where pay_id = '14'");
			}
			
			$cost 				= $netto * $tarifbongkar; // Hitung cost jasa bongkar
			$cost_bagi_mitra 	= $netto * $tbb; // Hitung cost bagi mitra
			$cost_simpan_mitra 	= $netto * $tsb; // Hitung cost simpan mitra
			$cost_bagi_mandor 	= $netto * $tbm; // Hitung cost bagi mandor
			$cost_simpan_mandor = $netto * $tsm; // Hitung cost simpan mandor
			// $cost_bayar 		= intval($cost_simpan_mitra) + intval($cost_bagi_mandor) + intval($cost_simpan_mandor); // Hitung cost simpan mandor
			// $cost_bayar 		= intval($cost_bagi_mandor) + intval($cost_simpan_mandor); // Hitung cost simpan mandor

			if($jenis == 'B'){
				$cost_bayar 		= intval($cost_bagi_mandor) + intval($cost_simpan_mandor); // Hitung cost simpan mandor
			} else {
				$cost_bayar 		= intval($cost_bagi_mitra) + intval($cost_simpan_mitra) + intval($cost_bagi_mandor) + intval($cost_simpan_mandor); // Hitung cost simpan mandor
			}

			
			$cek = executeScalar("select count(weighing_id) from laborer_fee where weighing_id = '".$id."'");
			//$ketemu	= mysql_num_rows($ada);
			if($cek > 0){
				mysql_query("delete from weighing_mitra where weighing_id = '".$id."'");
			}

			$insert = mysql_query("INSERT INTO `weighing_mitra` ( 	`weighing_id`,
																	`merchant_id`,
																	`checker_id`,
																	`goods_id`,
																	`nopol`, 
																	`weight_in`, 
																	`weight_out`,  
																	`noncommodity_id`,
																	`noncommodity`,
																	`netto`, 
																	`date_in`, 
																	`time_in`,
																	`date_out`, 
																	`time_out`,
																	`laborer_sts`,
																	`cost_permitra_bagi`,
																	`cost_permitra_simpan`,
																	`cost_permandor_bagi`,
																	`cost_permandor_simpan`,
																	`cost_bagi_mitra`,
																	`cost_simpan_mitra`,
																	`cost_bagi_mandor`,
																	`cost_simpan_mandor`,
																	`cost_bayar`,
																	`cost`,
																	`weighing_sts`, 
																	`weighing_sts2`,
																	`reffer`,
																	`p1`,
																	`p2`) 
									VALUES ('".$id."', 
											'".$merchant_id."', 
											'".$checker."',
											'".$goods_id."',
											'".$nopol."',
											'".$weight_in."',
											'".$weight_out."', 
											'".$noncommodity_id."',
											'".$noncommodity."', 
											'".$netto2."', 
											'".$date_in."', 
											'".$time_in."', 
											'".$date_out."',
											'".$time_out."',
											'".$laborer."',
											'".$k_dibagi."',
											'".$k_disimpan."',
											'".$m_dibagi."',
											'".$m_disimpan."',
											'".$cost_bagi_mitra."', 
											'".$cost_simpan_mitra."', 
											'".$cost_bagi_mandor."', 
											'".$cost_simpan_mandor."', 
											'".$cost_bayar."',
											'".$cost."', 
											'".$weighing_sts."', 
											'".$weighing_sts2."',
											'".$reffer."',
											'".$p1."',
											'".$p2."')");
					
			if(!$insert){
				//echo 'Data Gagal Masuk';
				trigger_error("Data gagal Masuk, Silahkan dicoba lagi ".$id, E_USER_ERROR);
				HEADER("location: index.php");
				exit();
			}
		}
		
		if($jenis != 'B'){
			// Jika total bayar dibawah 30.000, maka akan dikenakan tarin min (Rp 30.000)
			if($total1 < $tarifmin){
				$total1 = $tarifmin;
			}

			// Jika total cost tonase dibawah 30.000, maka akan dikenakan tarin min (Rp 30.000)
			if($cost_tonase < $tarifmin){
				$cost_tonase = $tarifmin;
			}	
		}

		// Segment pengecekan tarif untuk pedagang trial ===========================================================================
		$id_merchant 			= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$kat_merchant 			= executeScalar("select kat_merchant from merchant where merchant_id = '".$id_merchant."'");
		$jml_kehadiran_hari_ini = executeScalar("select count(weighing_id) from weighing where merchant_id = '".$id_merchant."' AND weighing_sts != 'Batal' AND date_in = '".date("Y-m-d")."' ");

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak
		if ($kat_merchant == 'Trial Lapak') { 
			$cost_tonase = executeScalar("select pay_total from payment where pay_id = '15'");
			if ($jml_kehadiran_hari_ini > 1) {
				$cost_tonase = 0;
			}
		}

		// Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
		if ($kat_merchant == 'Trial Lapak Jalan') { 
			$cost_tonase = executeScalar("select pay_total from payment where pay_id = '16'");
			if ($jml_kehadiran_hari_ini > 1) {
				$cost_tonase = 0;
			}
		}
		// ==========================================================================================================================
		
		//update status dan nilai rupiah pada nota
		$update = mysql_query("	update weighing 
								set laborer_sts			= '".$laborer."',
									cost_tonase			= '".$cost_tonase."',
									cost_real_tonase	= '".$cost_real_tonase."',
									cost_bongkar		= '".$cost_bongkar."',
									cost				= '".$total1."',
									cost2				= '".$total2."'
								where weighing_id 		= '".$id."'");
							
		$update2 = mysql_query("update 	weighing_mitra 
								set 	date_check 	= '".$dt_now."'
								where 	weighing_id = '".$id."'");

		// Insert SPV LOG
		$merchant_id 		= executeScalar("select merchant_id from weighing where weighing_id = '".$id."'");
		$nopol 				= executeScalar("select nopol from weighing where weighing_id = '".$id."'");
		$insert_spv = mysql_query("insert into spv_log (spv_log_id, 
									spv_user, 
									spv_action, 
									spv_target, 
									spv_nopol, 
									spv_merchant_id, 
									spv_reason, 
									action_time) 
								values (	'', 
									'".$name."', 
									'Menginputkan Mitra & Mandor',
									'".$id."', 
									'".$nopol."', 
									'".$merchant_id."', 
									'Ada kesalahan pada penginputan data mitra & mandor', 
									'".$dt_now."')");

		if($update && $update2 && $insert_spv){
			echo "<script>window.location=('check_edit.php?id=$id')</script>";
			exit();
		} else{
			echo "<script>window.alert('Update mitra gagal');
			window.location=('check_edit.php?id=$id')</script>";
			exit();
		}
	}

?>
