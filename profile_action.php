<?php
	session_start();
	include "connection.php";

	$name     = $_POST['name'];
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Cek apakah ada file foto
	$photoName = "";
	$oldPhoto = executescalar("SELECT photo FROM checker WHERE checker_code = '".$username."'");

	if ($photoName == "") {
		$photoName = $oldPhoto;
	}

	if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {

		// Folder tujuan
		$targetDir = "uploads/";

		// Jika folder belum ada → buat
		if (!file_exists($targetDir)) {
			mkdir($targetDir, 0777, true);
		}

		// Hapus foto lama (jika ada dan file-nya masih ada)
		if (!empty($oldPhoto) && file_exists($targetDir . $oldPhoto)) {
			unlink($targetDir . $oldPhoto);
		}

		// Nama file unik
		$photoName = uniqid() . "_" . basename($_FILES['photo']['name']);
		$targetFile = $targetDir . $photoName;

		// Pindahkan file ke folder upload
		move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile);
	}

	// Update
	$update = mysql_query("
		UPDATE checker SET
			checker_name = '".$name."',
			checker_code = '".$username."',
			checker_pass = '".$password."',
			photo = '".$photoName."'
		WHERE checker_code = '".$username."'
	") or die(mysql_error());

	if($update){
		// Alert Information
		$_SESSION['success'] = true;
		$_SESSION['title'] = 'Berhasil';
		$_SESSION['message'] = "Update Profile";
		echo "<script>window.location=('profile.php')</script>";
	}else{
		// Alert Information
		$_SESSION['success'] = false;
		$_SESSION['title'] = 'Gagal';
		$_SESSION['message'] = "Update Profile";
		echo "<script>window.location=('profile.php')</script>";
	}
?>
