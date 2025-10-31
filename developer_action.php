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

	if ($task == 'update') {
		$revision_id = $_GET['id'];
		$type = $_POST['type'];
		$nota = $_POST['nota'];
		$description = $_POST['description'];
		$reason = $_POST['reason'];

		$update = mysql_query("UPDATE revision SET type = '".$type."', nota = '".$nota."', description = '".$description."', reason = '".$reason."' WHERE revision_id = '".$revision_id."'");

		if($update){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Mengedit Data Revisi";
			HEADER("location: developer.php");
			exit();
		}
	}

	if ($task == 'delete') {
		$revision_id = $_GET['id'];

		$delete = mysql_query("DELETE FROM revision WHERE revision_id = '".$revision_id."'");

		if($delete){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menghapus Data Revisi";
			HEADER("location: developer.php");
			exit();
		}
	}
?>