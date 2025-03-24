<?php
	session_start();
	include "connection.php";
	
	date_default_timezone_set('Asia/Jakarta');
	$date_now	= date("Y-m-d H:i:s");
	$time_now	= date("H:i:s");
	
	if($_SESSION['checker']==''){
		echo "<script>window.alert('Anda Tidak Mempunyai Hak Akses');
		window.history.back()</script>";
		exit();
	}
	$task	= $_GET['task'];

	//Mengambil Id Checker
	$checker= executeScalar("select checker_id from checker where checker_code='".$_SESSION['checker']."'");
	
	if ($task == 'revision') {
		$type = $_POST['type'];
		$nota = $_POST['nota'];
		$description = $_POST['description'];
		$reason = $_POST['reason'];

		$insert = mysql_query("INSERT INTO `revision` (`revision_id`,  `applicant`, `type`, `nota`, `description`, `reason`, `date`, `time`)
		VALUES ('', '".$checker."', '".$type."', '".$nota."', '".$description."', '".$reason."', '".$date_now."', '".$time_now."')");

		if($insert){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Melakukan Pengajuan Revisi";
			echo 'Berat Masuk '.$x;
			HEADER("location: developer.php");
			exit();
		}
	}
?>