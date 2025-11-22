<?php
	session_start();
	include "connection.php";

	$name     = $_POST['name'];
	$username = $_POST['username'];
	$old_username = executeScalar("SELECT checker_code FROM checker WHERE checker_code = '".$_SESSION['checker']."'");    // username lama
	$password      = $_POST['password'];		// password baru
	$old_password  = $_POST['old_password'];    // password lama
	$id = executeScalar("SELECT checker_id FROM checker WHERE checker_code = '".$old_username."'");

	// Cek apakah ada file foto
	$photoName = "";
	$oldPhoto = executescalar("SELECT photo FROM checker WHERE checker_code = '".$old_username."'");

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

	$shouldLogout = false;

	if ($username != $old_username) {
		$shouldLogout = true;
	}

	if ($password != $old_password) {
		$shouldLogout = true;
	}

	if ($shouldLogout) {
		// UPDATE
		$update = mysql_query("UPDATE checker SET
				checker_name = '$name',
				checker_code = '$username',
				checker_pass = '$password',
				photo = '$photoName'
			WHERE checker_id = '$id'") or die(mysql_error());

		// LOGOUT
		echo '<script>alert("Login kembali untuk masuk sistem")</script>';
		session_destroy();
		echo '<script>
		localStorage.removeItem("notificationHidden");
		window.location.href = "login.html";
		</script>';
	} else {
		// UPDATE TANPA LOGOUT
		$update = mysql_query("UPDATE checker SET
				checker_name = '$name',
				checker_code = '$username',
				checker_pass = '$password',
				photo = '$photoName'
			WHERE checker_id = '$id'") or die(mysql_error());

		// Feedback
		if($update){
			$_SESSION['success'] = true;
			$_SESSION['title'] = 'Berhasil';
			$_SESSION['message'] = "Update Profile";
			echo "<script>window.location=('profile.php')</script>";
		}
	}
?>
