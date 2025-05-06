<?php
	session_start();
	include "connection.php";
	
	$id = mysql_real_escape_string($_POST['id']);
	$password = $_POST['password'];
	
	$query = "SELECT * FROM checker WHERE checker_id = '$id'";
	$result = mysql_query($query);
	$user = mysql_fetch_array($result);
	
	if ($user) {
		if (strtoupper($password) === strtoupper($user['checker_pass'])) {
			session_destroy();
			session_start();

			$_SESSION['checker'] = $user['checker_code'];
			$_SESSION['role'] = $user['checker_role'];
			echo 'ok';
			exit();
		} else {
			echo 'wrong-password';	
		}
	} else {
		echo 'user-not-found';
	}

?>
