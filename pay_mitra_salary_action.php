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
	
	$task	= $_GET['task'];
	//Pembayaran
	if ($task=='pay'){
		//Ambil tgl pembayaran Upah Mitra
		$date	= $_GET['date'];
		
		//Ambil Total upah mitra yg akan dibagikan
		$sql = "

		SELECT

		sum(B001) B001,
		sum(B002) B002,
		sum(B003) B003,
		sum(B004) B004,
		sum(B005) B005,
		sum(B006) B006,
		sum(B015) B015,

		sum(S001) S001,
		sum(S002) S002,
		sum(S003) S003,
		sum(S004) S004,
		sum(S005) S005,
		sum(S006) S006,
		sum(S007) S007,
		sum(S008) S008,
		sum(S009) S009,
		sum(S010) S010,
		sum(S011) S011,
		sum(S012) S012,

		sum(M01) M01,
		sum(M02) M02,
		sum(M03) M03,
		sum(M04) M04,


		sum(B001_save) SB001,
		sum(B002_save) SB002,
		sum(B003_save) SB003,
		sum(B004_save) SB004,
		sum(B005_save) SB005,
		sum(B006_save) SB006,

		sum(S001_save) SS001,
		sum(S002_save) SS002,
		sum(S003_save) SS003,
		sum(S004_save) SS004,
		sum(S005_save) SS005,
		sum(S006_save) SS006,
		sum(S007_save) SS007,
		sum(S008_save) SS008,
		sum(S009_save) SS009,
		sum(S010_save) SS010,
		sum(S011_save) SS011,
		sum(S012_save) SS012,

		sum(M01_save) SM01,
		sum(M02_save) SM02,
		sum(M03_save) SM03,
		sum(M04_save) SM04


		FROM laborer_fee

		JOIN weighing_mitra
		ON weighing_mitra.weighing_id = laborer_fee.weighing_id

		WHERE

		date(date_check) = '".$date."'
		AND laborer_fee_sts = 'Baru'

		";


		$q = mysql_query($sql);

		$data = mysql_fetch_assoc($q);

		$B001 = $data['B001'];
		$B002 = $data['B002'];
		$B003 = $data['B003'];
		$B004 = $data['B004'];
		$B005 = $data['B005'];
		$B006 = $data['B006'];
		$B015 = $data['B015'];

		$S001 = $data['S001'];
		$S002 = $data['S002'];
		$S003 = $data['S003'];
		$S004 = $data['S004'];
		$S005 = $data['S005'];
		$S006 = $data['S006'];
		$S007 = $data['S007'];
		$S008 = $data['S008'];
		$S009 = $data['S009'];
		$S010 = $data['S010'];
		$S011 = $data['S011'];
		$S012 = $data['S012'];

		$M01 = $data['M01'];
		$M02 = $data['M02'];
		$M03 = $data['M03'];
		$M04 = $data['M04'];



		$SB001 = $data['SB001'];
		$SB002 = $data['SB002'];
		$SB003 = $data['SB003'];
		$SB004 = $data['SB004'];
		$SB005 = $data['SB005'];
		$SB006 = $data['SB006'];

		$SS001 = $data['SS001'];
		$SS002 = $data['SS002'];
		$SS003 = $data['SS003'];
		$SS004 = $data['SS004'];
		$SS005 = $data['SS005'];
		$SS006 = $data['SS006'];
		$SS007 = $data['SS007'];
		$SS008 = $data['SS008'];
		$SS009 = $data['SS009'];
		$SS010 = $data['SS010'];
		$SS011 = $data['SS011'];
		$SS012 = $data['SS012'];

		$SM01 = $data['SM01'];
		$SM02 = $data['SM02'];
		$SM03 = $data['SM03'];
		$SM04 = $data['SM04'];

			//Cek apakah tgl pembayaran sdh ada di DB
			$ada	= executeScalar("SELECT count(fee_id) FROM laborer_fee2 where fee_date = '".$date."'");
			$ada2	= executeScalar("SELECT count(fee_id) FROM laborer_fee2_before where fee_date = '".$date."'");
			if($ada>0 && $ada2>0){
				//update pembayaran mitra
				$update1 = mysql_query("update laborer_fee2 
										set `fee_user` = '".$checker."',
											`fee_B001` = '".$_POST['B001']."',
											`fee_B002` = '".$_POST['B002']."',
											`fee_B003` = '".$_POST['B003']."',
											`fee_B004` = '".$_POST['B004']."',
											`fee_B005` = '".$_POST['B005']."',
											`fee_B006` = '".$_POST['B006']."',
											`fee_B007` = '0',
											`fee_B008` = '0',
											`fee_B009` = '0',
											`fee_B010` = '0',
											`fee_B011` = '0',
											`fee_B012` = '0',
											`fee_B013` = '0',
											`fee_B014` = '0',
											`fee_B015` = '0',
											`fee_S001` = '".$_POST['S001']."',
											`fee_S002` = '".$_POST['S002']."',
											`fee_S003` = '".$_POST['S003']."',
											`fee_S004` = '".$_POST['S004']."',
											`fee_S005` = '".$_POST['S005']."',
											`fee_S006` = '".$_POST['S006']."',
											`fee_S007` = '".$_POST['S007']."',
											`fee_S008` = '".$_POST['S008']."',
											`fee_S009` = '".$_POST['S009']."',
											`fee_S010` = '".$_POST['S010']."',
											`fee_S011` = '".$_POST['S011']."',
											`fee_S012` = '".$_POST['S012']."',
											`fee_S013` = '0',
											`fee_S014` = '0',
											`fee_S015` = '0',
											`fee_M01` = '".$_POST['M01']."',
											`fee_M02` = '".$_POST['M02']."',
											`fee_M03` = '".$_POST['M03']."',
											`fee_M04` = '".$_POST['M04']."',
											`save_B001` = '0',
											`save_B002` = '0',
											`save_B003` = '0',
											`save_B004` = '0',
											`save_B005` = '0',
											`save_B006` = '0',
											`save_B007` = '0',
											`save_B008` = '0',
											`save_B009` = '0',
											`save_B010` = '0',
											`save_B011` = '0',
											`save_B012` = '0',
											`save_B013` = '0',
											`save_B014` = '0',
											`save_B015` = '0',
											`save_S001` = '0',
											`save_S002` = '0',
											`save_S003` = '0',
											`save_S004` = '0',
											`save_S005` = '0',
											`save_S006` = '0',
											`save_S007` = '0',
											`save_S008` = '0',
											`save_S009` = '0',
											`save_S010` = '0',
											`save_S011` = '0',
											`save_S012` = '0',
											`save_S013` = '0',
											`save_S014` = '0',
											`save_S015` = '0',
											`save_M01` = '0',
											`save_M02` = '0',
											`save_M03` = '0',
											`save_M04` = '0'
										where `fee_date` = '".$date."' ");

				$update2 = mysql_query("update laborer_fee2_before
										set `fee_user` = '".$checker."',
											`fee_B001` = '".$B001."',
											`fee_B002` = '".$B002."',
											`fee_B003` = '".$B003."',
											`fee_B004` = '".$B004."',
											`fee_B005` = '".$B005."',
											`fee_B006` = '".$B006."',
											`fee_B007` = '0',
											`fee_B008` = '0',
											`fee_B009` = '0',
											`fee_B010` = '0',
											`fee_B011` = '0',
											`fee_B012` = '0',
											`fee_B013` = '0',
											`fee_B014` = '0',
											`fee_B015` = '0',
											`fee_S001` = '".$S001."',
											`fee_S002` = '".$S002."',
											`fee_S003` = '".$S003."',
											`fee_S004` = '".$S004."',
											`fee_S005` = '".$S005."',
											`fee_S006` = '".$S006."',
											`fee_S007` = '".$S007."',
											`fee_S008` = '".$S008."',
											`fee_S009` = '".$S009."',
											`fee_S010` = '".$S010."',
											`fee_S011` = '".$S011."',
											`fee_S012` = '".$S012."',
											`fee_S013` = '0',
											`fee_S014` = '0',
											`fee_S015` = '0',
											`fee_M01` = '".$M01."',
											`fee_M02` = '".$M02."',
											`fee_M03` = '".$M03."',
											`fee_M04` = '".$M04."',
											`save_B001` = '0',
											`save_B002` = '0',
											`save_B003` = '0',
											`save_B004` = '0',
											`save_B005` = '0',
											`save_B006` = '0',
											`save_B007` = '0',
											`save_B008` = '0',
											`save_B009` = '0',
											`save_B010` = '0',
											`save_B011` = '0',
											`save_B012` = '0',
											`save_B013` = '0',
											`save_B014` = '0',
											`save_B015` = '0',
											`save_S001` = '0',
											`save_S002` = '0',
											`save_S003` = '0',
											`save_S004` = '0',
											`save_S005` = '0',
											`save_S006` = '0',
											`save_S007` = '0',
											`save_S008` = '0',
											`save_S009` = '0',
											`save_S010` = '0',
											`save_S011` = '0',
											`save_S012` = '0',
											`save_S013` = '0',
											`save_S014` = '0',
											`save_S015` = '0',
											`save_M01` = '0',
											`save_M02` = '0',
											`save_M03` = '0',
											`save_M04` = '0'
										where `fee_date` = '".$date."' ");

				if($update1 && $update2){
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Data Pembulatan Upah Mitra Berhasil Diupdate";
					echo "<script>window.location=('pay_mitra_salary.php')</script>";
					exit();
				}
				else{
					echo "<script>window.alert('Update Pembayaran Gagal');
					window.history.back()</script>";
					exit();
				}
			}else{
				//Insert pembayaran mitra
				$insert1 = mysql_query("	insert into laborer_fee2(`fee_date`, 
																	`fee_user`, 
																	`fee_B001`, 
																	`fee_B002`, 
																	`fee_B003`, 
																	`fee_B004`, 
																	`fee_B005`, 
																	`fee_B006`, 
																	`fee_B007`, 
																	`fee_B008`, 
																	`fee_B009`, 
																	`fee_B010`, 
																	`fee_B011`, 
																	`fee_B012`, 
																	`fee_B013`, 
																	`fee_B014`, 
																	`fee_B015`, 
																	`fee_S001`, 
																	`fee_S002`, 
																	`fee_S003`, 
																	`fee_S004`, 
																	`fee_S005`, 
																	`fee_S006`, 
																	`fee_S007`, 
																	`fee_S008`, 
																	`fee_S009`, 
																	`fee_S010`, 
																	`fee_S011`, 
																	`fee_S012`, 
																	`fee_S013`, 
																	`fee_S014`, 
																	`fee_S015`, 
																	`fee_M01`, 
																	`fee_M02`, 
																	`fee_M03`, 
																	`fee_M04`, 
																	`save_B001`, 
																	`save_B002`, 
																	`save_B003`, 
																	`save_B004`, 
																	`save_B005`, 
																	`save_B006`, 
																	`save_B007`, 
																	`save_B008`, 
																	`save_B009`, 
																	`save_B010`, 
																	`save_B011`, 
																	`save_B012`, 
																	`save_B013`, 
																	`save_B014`, 
																	`save_B015`, 
																	`save_S001`, 
																	`save_S002`, 
																	`save_S003`, 
																	`save_S004`, 
																	`save_S005`, 
																	`save_S006`, 
																	`save_S007`, 
																	`save_S008`, 
																	`save_S009`, 
																	`save_S010`, 
																	`save_S011`, 
																	`save_S012`, 
																	`save_S013`, 
																	`save_S014`, 
																	`save_S015`, 
																	`save_M01`, 
																	`save_M02`, 
																	`save_M03`, 
																	`save_M04`) 
										values(	'".$date."', 
												'".$checker."', 
												'".$_POST['B001']."',
												'".$_POST['B002']."',
												'".$_POST['B003']."',
												'".$_POST['B004']."',
												'".$_POST['B005']."',
												'".$_POST['B006']."',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'".$_POST['S001']."',
												'".$_POST['S002']."',
												'".$_POST['S003']."',
												'".$_POST['S004']."',
												'".$_POST['S005']."',
												'".$_POST['S006']."',
												'".$_POST['S007']."',
												'".$_POST['S008']."',
												'".$_POST['S009']."',
												'".$_POST['S010']."',
												'".$_POST['S011']."',
												'".$_POST['S012']."',
												'0',
												'0',
												'0',
												'".$_POST['M01']."',
												'".$_POST['M02']."',
												'".$_POST['M03']."',
												'".$_POST['M04']."',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0')");

				$insert2 = mysql_query("insert into laborer_fee2_before(`fee_date`, 
																	`fee_user`, 
																	`fee_B001`, 
																	`fee_B002`, 
																	`fee_B003`, 
																	`fee_B004`, 
																	`fee_B005`, 
																	`fee_B006`, 
																	`fee_B007`, 
																	`fee_B008`, 
																	`fee_B009`, 
																	`fee_B010`, 
																	`fee_B011`, 
																	`fee_B012`, 
																	`fee_B013`, 
																	`fee_B014`, 
																	`fee_B015`, 
																	`fee_S001`, 
																	`fee_S002`, 
																	`fee_S003`, 
																	`fee_S004`, 
																	`fee_S005`, 
																	`fee_S006`, 
																	`fee_S007`, 
																	`fee_S008`, 
																	`fee_S009`, 
																	`fee_S010`, 
																	`fee_S011`, 
																	`fee_S012`, 
																	`fee_S013`, 
																	`fee_S014`, 
																	`fee_S015`, 
																	`fee_M01`, 
																	`fee_M02`, 
																	`fee_M03`, 
																	`fee_M04`, 
																	`save_B001`, 
																	`save_B002`, 
																	`save_B003`, 
																	`save_B004`, 
																	`save_B005`, 
																	`save_B006`, 
																	`save_B007`, 
																	`save_B008`, 
																	`save_B009`, 
																	`save_B010`, 
																	`save_B011`, 
																	`save_B012`, 
																	`save_B013`, 
																	`save_B014`, 
																	`save_B015`, 
																	`save_S001`, 
																	`save_S002`, 
																	`save_S003`, 
																	`save_S004`, 
																	`save_S005`, 
																	`save_S006`, 
																	`save_S007`, 
																	`save_S008`, 
																	`save_S009`, 
																	`save_S010`, 
																	`save_S011`, 
																	`save_S012`, 
																	`save_S013`, 
																	`save_S014`, 
																	`save_S015`, 
																	`save_M01`, 
																	`save_M02`, 
																	`save_M03`, 
																	`save_M04`) 
										values(	'".$date."', 
												'".$checker."', 
												'".$B001."',
												'".$B002."',
												'".$B003."',
												'".$B004."',
												'".$B005."',
												'".$B006."',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'".$S001."',
												'".$S002."',
												'".$S003."',
												'".$S004."',
												'".$S005."',
												'".$S006."',
												'".$S007."',
												'".$S008."',
												'".$S009."',
												'".$S010."',
												'".$S011."',
												'".$S012."',
												'0',
												'0',
												'0',
												'".$M01."',
												'".$M02."',
												'".$M03."',
												'".$M04."',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0',
												'0')");

				if($insert1 && $insert2){
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Data Pembulatan Upah Mitra Berhasil Diinput";
					echo "<script>window.location=('pay_mitra_salary.php')</script>";
					exit();
				}
				else{
					echo "<script>window.alert('Insert Pembayaran Gagal');
					window.history.back()</script>";
					exit();
				}
			}
	}
?>