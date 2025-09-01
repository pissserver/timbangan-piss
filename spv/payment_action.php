<?php
	session_start();
	include "../connection.php";
	//Validasi role
	if($_SESSION['role']='admin'||$_SESSION['role']='super_admin'){
		if($_SESSION['username']!=''){
			$admin = $_SESSION['username'];
		}else{
			header("Location:../login.html");
		}
	}else{
		header("Location:../login.html");
	}
	
	$task	= $_GET['task'];
	// Input 
	if ($task == 'insert'){
		$insert = mysql_query("INSERT INTO `payment` (`pay_id`, `pay_name`, `pay_total`) 
		VALUES ('', '".$_POST['name']."', '".$_POST['pay']."')");
		
		if($insert){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menambahkan Tarif Berhasil";
			echo "<script>window.location=('payment.php')</script>";
		}
		else{
			echo "<script>window.alert('Data Gagal');
			window.history.back()</script>";
		}
	}

	// Update
	elseif ($task == 'update'){
		$id		= $_GET['id'];
		$oldPay = executeScalar("select pay_total from payment where pay_name = '".$_POST['name']."'");
		$date_change = date("Y-m-d");
		
		$update = mysql_query("update payment set pay_name 	= '".$_POST['name']."',
												pay_total	= '".$_POST['pay']."',
												changes     = '".$oldPay." -> ".$_POST['pay']."',
												date_change = '".$date_change."'
											where pay_id 	= '".$id."'");
		if($update){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Megedit Tarif Berhasil";
			echo "<script>window.location=('payment.php')</script>";
		}
		else{
			echo "<script>window.alert('Data Gagal Diperbarui');
			window.history.back()</script>";
		}
	}
	
	elseif ($task == 'delete'){
		$id		= $_GET['id'];
		$delete = mysql_query("delete from payment where pay_id = '".$id."'");
		if($delete){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menghapus Tarif Berhasil";
			echo "<script>window.location=('payment.php')</script>";
		}
		else{
			echo "<script>window.alert('Delete Failled');
			window.location=('payment.php')</script>";
		}
	}
?>