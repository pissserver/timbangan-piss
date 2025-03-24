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
		$exist 	= executeScalar("select count(laborer_code) from laborer where laborer_code='".$_POST['code']."'");
		if($exist>0){
			// Alert Information
			$_SESSION['success'] = false;
			$_SESSION['title'] = 'Gagal';
			$_SESSION['message'] = "Kode Sudah Ada, Ganti Dengan yg Lain";
			echo "<script>window.location=('laborer.php')</script>";
		}
		else{
			$insert = mysql_query("INSERT INTO `laborer` (`laborer_code`, `laborer_name`, `laborer_role`) 
			VALUES ('".$_POST['code']."', '".$_POST['name']."', '".$_POST['role']."')");
			
			if($insert){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Menambahkan Mitra / Mandor Berhasil";
				echo "<script>window.location=('laborer.php')</script>";
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
		$kodetetap 	= executeScalar("select laborer_code from laborer where laborer_code='".$_POST['code']."' and laborer_id='".$id."'");
		//jika kode tetap
		if($kodetetap){
			$update1 = mysql_query("update laborer set laborer_code= '".$_POST['code']."',
													laborer_name 	= '".$_POST['name']."',
													laborer_role 	= '".$_POST['role']."'
												where laborer_id 	= '".$id."'");
			if($update1){
				// Alert Information
				$_SESSION['success'] = true;
				$_SESSION['title'] = 'Berhasil';
				$_SESSION['message'] = "Mengedit Mitra / Mandor Berhasil";
				echo "<script>window.location=('laborer.php')</script>";
			}
			else{
				echo "<script>window.alert('Data Gagal Diperbarui');
				window.history.back()</script>";
			}
		}
		else{
			//jika kode berubah
			$exist 	= executeScalar("select count(laborer_code) from laborer where laborer_code='".$_POST['code']."'");
			if($exist>0){
							// Alert Information
				$_SESSION['success'] = false;
				$_SESSION['title'] = 'Gagal';
				$_SESSION['message'] = "Kode Sudah Ada, Ganti Dengan yg Lain";
				echo "<script>window.location=('laborer.php')</script>";
			}
			else{
				$update2 = mysql_query("update laborer set laborer_code= '".$_POST['code']."',
														laborer_name 	= '".$_POST['name']."',
														laborer_role 	= '".$_POST['role']."'
													where laborer_id 	= '".$id."'");
				if($update2){
					// Alert Information
					$_SESSION['success'] = true;
					$_SESSION['title'] = 'Berhasil';
					$_SESSION['message'] = "Mengedit Mitra / Mandor Berhasil";
					echo "<script>window.location=('laborer.php')</script>";
				}
				else{
					echo "<script>window.alert('Data Gagal Diperbarui');
					window.history.back()</script>";
				}
			}
		}
	}
	
	elseif ($task == 'delete'){
		$id			= $_GET['id'];
		$delete = mysql_query("delete from laborer where laborer_id = '".$id."'");
		if($delete){
			// Alert Information
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Menghapus Mitra / Mandor Berhasil";
			echo "<script>window.location=('laborer.php')</script>";
		}
		else{
			echo "<script>window.alert('Delete Failled');
			window.history.back()</script>";
		}
	}
?>