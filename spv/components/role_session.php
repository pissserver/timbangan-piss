<?php 

    //Validasi role
	if($_SESSION['role']=='spv'){
		if($_SESSION['username']!=''){
			$name = executescalar("select name from admin where username = '".$_SESSION['username']."'");
		}else{
			header("Location:../login.html");
		}
	}else{
		header("Location:../login.html");
	}

?>