<?php
	session_start();
	include "connection.php";
	include "components/role_session.php";
	
	date_default_timezone_set('Asia/Jakarta');
	$date_now	= date("Y-m-d H:i:s");
	$time_now	= date("H:i:s");
	
	$task	= $_GET['task'];

	if ($task == 'accept') {
		$revision_id = $_GET['revision_id'];

		$update = mysql_query("UPDATE revision SET approver = '$name', approver_date = '$date_now', approver_time = '$time_now', status1 = 'Disetujui' WHERE revision_id = '$revision_id'");
		if($update){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Melakukan Setujui Revisi";
			HEADER("location: developer.php");
			exit();
		}
	} 
	
	if ($task == 'reject') {
		$revision_id = $_GET['revision_id'];

		$update = mysql_query("UPDATE revision SET approver = '$name', approver_date = '$date_now', approver_time = '$time_now', status1 = 'Ditolak' WHERE revision_id = '$revision_id'");
		if($update){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Melakukan Tolak Revisi";
			HEADER("location: developer.php");
			exit();
		} 
	}
?>