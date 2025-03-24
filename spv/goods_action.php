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
	if ($task == 'insert'){
		$exist 	= executeScalar("select count(goods_name) from goods where goods_name = '".$_POST['name']."'");
		if($exist>0){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Komoditi Sudah Ada, Beri Tambahan Kata";
			echo "<script>window.location=('goods.php')</script>";
		}
		else{
			$insert = mysql_query("INSERT INTO `goods` (`goods_id`, `goods_name`, `goods_type`) 
			VALUES ('', '".$_POST['name']."', '".$_POST['type']."')");
			
			if($insert){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menambahkan Komoditi Berhasil";
			echo "<script>window.location=('goods.php')</script>";
			}
			else{
				echo "<script>window.alert('Data Gagal');
				window.history.back()</script>";
			}
		}
	}

	// Update
	elseif ($task == 'update'){
		$id		= $_GET['id'];
		$exist 	= executeScalar("select count(goods_name) from goods where goods_name='".$_POST['name']."'");
		if($exist>1){
			echo "<script>window.alert('Nama Barang Sudah Ada, Beri Tambahan Kata');
			window.location=('goods.php')</script>";
		}
		else{
			$update = mysql_query("update goods set goods_name	= '".$_POST['name']."',
													goods_type 	= '".$_POST['type']."'
												where goods_id 	= '".$id."'");
			
			if($update){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Mengedit Komoditi Berhasil";
				echo "<script>window.location=('goods.php')</script>";
			}
			else{
				echo "<script>window.alert('Data Gagal');
				window.history.back()</script>";
			}
		}
	}
	
	elseif ($task == 'delete'){
		$id		= $_GET['id'];
		$delete = mysql_query("delete from goods where goods_id = '".$id."'");
		if($delete){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menghapus Komoditi Berhasil";
			echo "<script>window.location=('goods.php')</script>";
		}
		else{
			echo "<script>window.alert('Gagal Dihapus');
			window.location=('goods.php')</script>";
		}
	}
?>