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
	if($_SESSION['role']!='admin'||$_SESSION['role']!='super_admin'){
		echo "<script>window.alert('Anda Tidak Memiliki Hak Akses Untuk Mengubah Data');
		window.location=('transportation.php')</script>";
	}
	
	// Input 
	if ($task == 'insert'){
		$username		= $_POST['username'];
		$name		= ucwords(strtolower(trim($_POST['name'])));
		$pass 		= $_POST['pass'];
		$position 	= ucwords(strtolower(trim($_POST['position'])));
		$exist 		= executeScalar("select count(username) from admin where username = '".$_POST['username']."'");
		if($exist>0){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Username Sudah Ada, Ganti Dengan yg Lain";
			echo "<script>window.location=('admin.php')</script>";
		}
		else{
			$insert = mysql_query("INSERT INTO `admin` (`id_admin`, `username`, `name`, `password`, `role`) 
			VALUES ('', '".$username."', '".$name."', '".$pass."', '".$position."')");
			
			if($insert){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Menambahkan SPV / PP / Keuangan Berhasil";
				echo "<script>window.location=('admin.php')</script>";
			}
			else{
				// Alert Information
				$_SESSION['success'] = false;
				$_SESSION['title'] = 'Gagal';
				$_SESSION['message'] = "Menambahkan SPV / PP / Keuangan Gagal";
				echo "<script>window.history.back()</script>";
			}
		}
	}

	// Update
	elseif ($task == 'update'){
		$id			= $_GET['id'];

		$update = mysql_query("update admin set username	= '".$_POST['username']."',
												name 		= '".$_POST['name']."',
												password 	= '".$_POST['pass']."',
												role 		= '".$_POST['position']."'
											where id_admin 	= '".$id."'");
		if($update){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Mengedit SPV / PP / Keuangan Berhasil";
			echo "<script>window.location=('admin.php')</script>";
		}
		else{
			echo "<script>window.alert('Data Gagal Diperbarui');
			window.history.back()</script>";
		}
	}
	
	elseif ($task == 'delete'){
		$id		= $_GET['id'];
		$delete = mysql_query("delete from admin where id_admin = '".$id."'");
		if($delete){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menghapus SPV / PP / Keuangan Berhasil";
			echo "<script>window.location=('admin.php')</script>";
		}
		else{
			echo "<script>window.alert('Delete Failled');
			window.location=('admin.php')</script>";
		}
	}
?>