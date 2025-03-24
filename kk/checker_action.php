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
		$code		= $_POST['code'];
		$name		= ucwords(strtolower(trim($_POST['name'])));
		$pass 		= $_POST['pass'];
		$position 	= ucwords(strtolower(trim($_POST['position'])));
		$exist 		= executeScalar("select count(checker_code) from checker where checker_code='".$_POST['code']."'");
		if($exist>0){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Kode Sudah Ada, Ganti Dengan yg Lain";
			echo "<script>window.location=('checker.php')</script>";
		}
		else{
			$insert = mysql_query("INSERT INTO `checker` (`checker_id`, `checker_code`, `checker_name`, `checker_pass`, `checker_role`) 
			VALUES ('', '".$code."', '".$name."', '".$pass."', '".$position."')");
			
			if($insert){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Menambahkan Cashier / Checker Berhasil";
				echo "<script>window.location=('checker.php')</script>";
			}
			else{
				echo "<script>window.alert('Data Gagal');
				window.history.back()</script>";
			}
		}
	}

	// Update
	elseif ($task == 'update'){
		$id			= $_GET['id'];
		$kodetetap 	= executeScalar("select checker_code from checker where checker_code='".$_POST['code']."' and checker_id='".$id."'");
		//jika kode tetap
		if($kodetetap){
			$update = mysql_query("update checker set checker_code	= '".$_POST['code']."',
													checker_name 	= '".$_POST['name']."',
													checker_pass 	= '".$_POST['pass']."',
													checker_role 	= '".$_POST['position']."'
												where checker_id 	= '".$id."'");
			if($update){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Mengedit Cashier / Checker Berhasil";
				echo "<script>window.location=('checker.php')</script>";
			}
			else{
				echo "<script>window.alert('Data Gagal Diperbarui');
				window.history.back()</script>";
			}
		}
		else{
			$exist 	= executeScalar("select count(checker_code) from checker where checker_code='".$_POST['code']."'");
			if($exist>0){
				// Alert Information
				$_SESSION['success'] = false;
				$_SESSION['title'] = 'Gagal';
				$_SESSION['message'] = "Kode Sudah Ada, Ganti Dengan yg Lain";
				echo "<script>window.location=('checker.php')</script>";
			}
			else{
				$update = mysql_query("update checker set checker_code	= '".$_POST['code']."',
														checker_name 	= '".$_POST['name']."'
													where checker_id 	= '".$id."'");
				if($update){
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Mengedit Cashier / Checker Berhasil";
					echo "<script>window.location=('checker.php')</script>";
				}
				else{
					echo "<script>window.alert('Data Gagal Diperbarui');
					window.history.back()</script>";
				}
			}
		}
	}
	
	elseif ($task == 'delete'){
		$id		= $_GET['id'];
		$delete = mysql_query("delete from checker where checker_id = '".$id."'");
		if($delete){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menghapus Cashier / Checker Berhasil";
			echo "<script>window.location=('checker.php')</script>";
		}
		else{
			echo "<script>window.alert('Delete Failled');
			window.location=('checker.php')</script>";
		}
	}
?>