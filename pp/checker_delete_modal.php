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
	
	$checkerID = $_GET['id'];
	$code = executeScalar("select checker_code from checker where checker_id = '".$checkerID."'");
	$name = executeScalar("select checker_name from checker where checker_id = '".$checkerID."'");
	$pass = executeScalar("select checker_pass from checker where checker_id = '".$checkerID."'");
	$position = executeScalar("select checker_role from checker where checker_id = '".$checkerID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-trash fa-fw"></i>&nbsp;Hapus Data Cashier / Checker</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formChecker" name = "formChecker" method="POST" action="checker_action.php?task=delete&id=<?php echo $checkerID; ?>">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
			<label>Kode Cashier / Checker</label>
			<input type="text" class="form-control" id="code" name="code" value="<?php echo $code; ?>" autofocus="autofocus" required disabled>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Nama Cashier / Checker </label>
			<input class="form-control" id="name" name="name" type="text" value="<?php echo $name; ?>" required/ disabled>                
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Password </label>
			<input class="form-control" id="pass" name="pass" type="text" value="<?php echo $pass; ?>" required/ disabled>                
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Posisi</label>
			<select class="form-control" name="position" id="position" required disabled>
				<option value="" disabled selected>- Pilih Posisi -</option>
				<option value="Cashier" <?php echo $position == 'Cashier' ? 'selected' : '';?>>Cashier</option>
				<option value="Checker" <?php echo $position == 'Checker' ? 'selected' : '';?>>Checker</option>
			</select>
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-danger btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-trash fa-fw" style="font-size: 16px;"></i>&nbsp;Hapus</button>                   
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>