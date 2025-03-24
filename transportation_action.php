<?php
	session_start();
	include "connection.php";
	//include "fungsi_thumb.php";
	
	$task	= $_GET['task'];
	//$id	= $_POST['id'];
	
	// Input 
	if ($task == 'insert'){
		//$prcen	= $_POST['weight']*2/100;
		$weight = $_POST['weight'];
		$insert = mysql_query("INSERT INTO `transportation` (`transportation_id`, `transportation_nopol`, `transportation_weight`) 
		VALUES ('', '".$_POST['trans']."', '".$weight."')");
		
		if($insert){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menambahkan Data Kendaraan Berhasil";
			echo "<script>window.location=('transportation.php')</script>";
		}
		else{
			echo "<script>window.alert('Data Gagal');
			window.location=('transportation.php')</script>";
		}
	}

	// Update
	elseif($task=='update'){
		$id		= $_GET['id'];
		$update = mysql_query("update transportation set transportation_nopol 	= '".$_POST['trans']."',
													transportation_weight		= '".$_POST['weight']."'
													where transportation_id 	= '".$id."'");
		if($update){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Mengedit Data Kendaraan Berhasil";
			echo "<script>window.location=('transportation.php')</script>";
		}
		else{
			echo "<script>window.alert('Data Gagal Diperbarui');
			window.history.back()</script>";
		}
	}
	
	elseif($task=='delete'){
		$id		= $_GET['id'];
		$delete = mysql_query("delete from transportation where transportation_id = '".$id."'");
		if($delete){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menghapus Data Kendaraan Berhasil";
			echo "<script>window.location=('transportation.php')</script>";
		}
		else{
			echo "<script>window.alert('Delete Failled');
			window.location=('transportation.php')</script>";
		}
	}
?>