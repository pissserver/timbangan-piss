<?php
	session_start();
	include "connection.php";
	if($_SESSION['checker']==''){
		echo "<script>window.alert('Anda Tidak Mempunyai Hak Akses');
		window.history.back()</script>";
		exit();
	}
	//Mengambil Id Checker
	$checker = executeScalar("select checker_id from checker where checker_code = '".$_SESSION['checker']."'");
	$task		= $_GET['task'];
	$type		= $_GET['type'];

	//Pembayaran Tonase
	if($type=='ko'&&$task=='pay'){
		$id		= $_GET['id'];
		//update status pembayaran
		$pay 	= mysql_query("update weighing set weighing_sts3 = 'Pending' where weighing_id = '".$id."'");
		if($pay){
			// echo "<script>window.location=('pay.php')</script>";
			echo "<script>window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Pembayaran Gagal');
			window.history.back()</script>";
			exit();
		}
		//cek jumlah nota pd ko apa sdh 10?
		/*$jml 	= executeScalar("select count(weighing_id) from weighing where weighing_sts3 = 'Pending'");
		if($jml<10){
		
		}else{
			echo "<script>window.alert('Pembayaran Max 10 Nota Untuk 1 KO');
			window.history.back()</script>";
			exit();
		}*/
	}

	// Pembayaran Jasa Bongkar
	if($type=='ko'&&$task=='pay_mitra'){
		$id		= $_GET['id'];
		//update status pembayaran
		$pay 	= mysql_query("update weighing_mitra set weighing_sts3 = 'Pending' where weighing_id = '".$id."'");
		if($pay){
			// echo "<script>window.location=('pay_mitra.php')</script>";
			echo "<script>window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Pembayaran Gagal');
			window.history.back()</script>";
			exit();
		}
		//cek jumlah nota pd ko apa sdh 10?
		/*$jml 	= executeScalar("select count(weighing_id) from weighing where weighing_sts3 = 'Pending'");
		if($jml<10){
		
		}else{
			echo "<script>window.alert('Pembayaran Max 10 Nota Untuk 1 KO');
			window.history.back()</script>";
			exit();
		}*/
	}

	//ACC Pembayaran Tonase
	if ($type=='ko'&&$task=='acc'){
		//Generate KO ID
		$ko_id 		= 0;
		$year		= date('Y');
		$ko_id 		= executeScalar("select count(ko_id) from ko where ko_year = '".$year."'");
		$ko_id 		= $ko_id+1;
		$ko_id 		= sprintf("%06d", $ko_id);
		$ko_id  	= $year.$ko_id;
		$receiver	= $_POST['receiver'];
		$check 		= executeScalar("select count(weighing_id) from weighing where weighing_sts3 = 'Pending'");
		if($receiver==''){
			echo "<script>window.alert('Penyetor Belum Diisi');
			window.history.back()</script>";
			exit();
		}elseif($check<1){
			// echo "<script>window.alert('Data Pembayaran Masih Kosong');
			// window.location=('pay.php')</script>";
			echo "<script>window.alert('Data Pembayaran Masih Kosong');
			window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		
		//Function untuk menghandel nota yg batal digabung tp sdh dlm status tunggu pembayaran
		$cancel = mysql_query("	update 	weighing 
								set 	weighing_sts3 	= null 
								where 	weighing_sts 	= 'Batal' and 
										weighing_sts3 	= 'Pending'");
		if($cancel){
				//update status pembayaran
				$update = mysql_query("update weighing set date_pay	= '".$date_now."',
													time_pay		= '".$time_now."',
													ko_id			= '".$ko_id."',
													weighing_sts	= 'Lunas',
													weighing_sts3	= 'acc'
												where weighing_sts3	= 'Pending'");
				if($update){
					$total	= executeScalar("select sum(cost_tonase) from weighing where ko_id ='".$ko_id."'");
					$year	= date('Y');

					$insert = mysql_query("	insert into ko (ko_id, 
															ko_year, 
															ko_total, 
															ko_status, 
															ko_date, 
															cashier_id, 
															receiver_id)
											value (	'".$ko_id."', 
													'".$year."', 
													'".$total."', 
													'Diajukan', 
													'".$date_now."', 
													'".$checker."', 
													'".$receiver."')");
					if($insert){
						// Alert Information
						$_SESSION['success'] = true;
						$_SESSION['message'] = "Pembayaran selesai & Laporan KO Tonase berhasil dibuat";
						
						echo "<script>window.location=('htmltopdf/reportKO2.php?ko=$ko_id');
						</script>";
						exit();
					} else{
						echo "<script>window.alert('ACC Gagal');
						window.history.back()</script>";
						exit();
					}
				}
				else{
					echo "<script>window.alert('ACC Gagal');
					window.history.back()</script>";
					exit();
				}
		}
	}

	//ACC Pembayaran Jasa Bongkar
	if ($type=='ko'&&$task=='acc_mitra'){
		//Generate KO ID
		$ko_id 		= 0;
		$year		= date('Y');
		$ko_id 		= executeScalar("select count(ko_id) from ko_mitra where ko_year = '".$year."'");
		$ko_id 		= $ko_id + 1;
		$ko_id 		= sprintf("%06d", $ko_id);
		$ko_id  	= $year.$ko_id;
		$receiver	= $_POST['receiver'];
		$check 		= executeScalar("select count(weighing_id) from weighing_mitra where weighing_sts3 = 'Pending'");
		
		if($receiver==''){
			echo "<script>window.alert('Penyetor Belum Diisi');
			window.history.back()</script>";
			exit();
		}elseif($check<1){
			// echo "<script>window.alert('Data Pembayaran Masih Kosong');
			// window.location=('pay_mitra.php')</script>";
			echo "<script>window.alert('Data Pembayaran Masih Kosong');
			window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		//Function untuk menghandel nota yg batal digabung tp sdh dlm status tunggu pembayaran
		$cancel = mysql_query("	update 	weighing_mitra 
								set 	weighing_sts3 	= null 
								where 	weighing_sts 	= 'Batal' and 
										weighing_sts3 	= 'Pending'");
		if($cancel){
				//update status pembayaran
				$update = mysql_query("	update 	weighing_mitra 
										set 	date_pay		= '".$date_now."',
												time_pay		= '".$time_now."',
												ko_id			= '".$ko_id."',
												weighing_sts	= 'Lunas',
												weighing_sts3	= 'acc'
										where 	weighing_sts3	= 'Pending'");
				if($update){
					$total	= executeScalar("select sum(cost_bayar) from weighing_mitra where ko_id ='".$ko_id."'");
					// $total	= executeScalar("select sum(cost) from weighing_mitra where ko_id ='".$ko_id."'");
					$year	= date('Y');
					$insert = mysql_query("insert into ko_mitra (	ko_id, 
																	ko_year, 
																	ko_total, 
																	ko_status, 
																	ko_date, 
																	cashier_id, 	
																	receiver_id)
											value (	'".$ko_id."', 
													'".$year."', 
													'".$total."', 
													'Diajukan', 
													'".$date_now."', 
													'".$checker."', 
													'".$receiver."')");
					if($insert){
						// Alert Information
						$_SESSION['success'] = true;
						$_SESSION['message'] = "Pembayaran selesai & Laporan KOM Mitra berhasil dibuat";

						echo "<script>window.location=('htmltopdf/reportKO2_mitra.php?ko=$ko_id')</script>";
						exit();
					}
					else{
						echo "<script>window.alert('ACC Gagal');
						window.history.back()</script>";
						exit();
					}
				}
				else{
					echo "<script>window.alert('ACC Gagal');
					window.history.back()</script>";
					exit();
				}
		}
	}
	
	//Hitung Pembayaran Tonase
	if ($type == 'ko' && $task == 'calculate') {
		$money = $_POST['receive'];
		$cost = executeScalar("select sum(cost_tonase) from weighing where weighing_sts3 = 'Pending'");
		$calculator = $cost - $money;
	
		if ($calculator <= 0) {
			$calculator = format_rupiah(abs($calculator));
			// Set session untuk sukses
			$_SESSION['success'] = true;
			$_SESSION['message'] = "Uang Setoran Kembali $calculator";
		} else {
			$calculator = format_rupiah(abs($calculator));
			// Set session untuk gagal
			$_SESSION['success'] = false;
			$_SESSION['message'] = "Uang Setoran Kurang $calculator";
		}
	
		// Redirect setelah memproses
		header("Location: pay_tonase_mitra.php");
		exit();
	}

	//Hitung Pembayaran Mitra
	if ($type == 'ko' && $task == 'calculate_mitra') {
		$money = $_POST['receive'];
		$cost = executeScalar("select sum(cost) from weighing_mitra where weighing_sts3 = 'Pending'");
		$calculator = $cost - $money;
	
		if ($calculator <= 0) {
			$calculator = format_rupiah(abs($calculator));
			// Set session untuk sukses
			$_SESSION['success'] = true;
			$_SESSION['message'] = "Uang Setoran Kembali $calculator";
		} else {
			$calculator = format_rupiah(abs($calculator));
			// Set session untuk gagal
			$_SESSION['success'] = false;
			$_SESSION['message'] = "Uang Setoran Kurang $calculator";
		}
	
		// Redirect setelah memproses
		header("Location: pay_tonase_mitra.php");
		exit();
	}
	
	//Cancel Pembayaran
	if ($type=='ko'&&$task=='cancel'){
		$id	= $_GET['id'];
		//update status pembayaran
		$cancel = mysql_query("	update weighing 
								set weighing_sts3 = null 
								where 	weighing_id = '".$id."' and 
										weighing_sts3 = 'Pending'");
		if($cancel){
			// echo "<script>window.location=('pay.php')</script>";
			echo "<script>window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Pembayaran Gagal');
			window.history.back()</script>";
			exit();
		}
	}

	//Cancel Pembayaran Mitra
	if ($type=='ko'&&$task=='cancel_mitra'){
		$id	= $_GET['id'];
		//update status pembayaran
		$cancel = mysql_query("	update weighing_mitra 
								set weighing_sts3 = null 
								where 	weighing_id = '".$id."' and 
										weighing_sts3 = 'Pending'");
		if($cancel){
			// echo "<script>window.location=('pay_mitra.php')</script>";
			echo "<script>window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Pembayaran Gagal');
			window.history.back()</script>";
			exit();
		}
	}
	
	//Cancel Semua Pembayaran
	// if ($type=='ko'&&$task=='cancelAll'){
	// 	//update status pembayaran
	// 	$cancel = mysql_query("update weighing set weighing_sts3 = null where weighing_sts3 = 'Pending'");
	// 	if($cancel){
	// 		echo "<script>window.location=('pay.php')</script>";
	// 		exit();
	// 	}
	// 	else{
	// 		echo "<script>window.alert('Pembayaran Gagal');
	// 		window.history.back()</script>";
	// 		exit();
	// 	}
	// }
	if ($type == 'ko' && $task=='cancelAll'){
		//update status pembayaran
		$cancel = mysql_query("update weighing set weighing_sts3 = null where weighing_sts3 = 'Pending'");
		if($cancel){
			// echo "<script>window.location=('pay.php')</script>";
			echo "<script>window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Pembayaran Gagal');
			window.history.back()</script>";
			exit();
		}
	}

	//Cancel Semua Pembayaran Mitra
	if ($type == 'ko' && $task=='cancelAll_mitra'){
		//update status pembayaran
		$cancel = mysql_query("update weighing_mitra set weighing_sts3 = null where weighing_sts3 = 'Pending'");
		if($cancel){
			// echo "<script>window.location=('pay_mitra.php')</script>";
			echo "<script>window.location=('pay_tonase_mitra.php')</script>";
			exit();
		}
		else{
			echo "<script>window.alert('Pembayaran Gagal');
			window.history.back()</script>";
			exit();
		}
	}
?>