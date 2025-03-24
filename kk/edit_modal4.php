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
	$id 	= $_GET['id'];
	$sts1 	= executeScalar("select weighing_sts from weighing where weighing_id = '".$id."'");
	$sts2 	= executeScalar("select weighing_sts2 from weighing where weighing_id = '".$id."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-edit fa-fw"></i>&nbsp;Edit Status</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form action="weighing_action.php?task=edit4&id=<?php echo $id; ?>"  id="form_edit3" name="form_edit3" method="POST" class="form-horizontal">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
			<label>No Nota</label>
			<input id="weighing_id" name="weighing_id" value="<?php echo $id; ?>" type="number" class="form-control" disabled>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top: -15px;">
			<label>Status 1</label>
			<select class="form-control chzn-select" name="status1" id="status1" required>
				<option value="<?php echo $sts1; ?>"><?php echo $sts1; ?></option>
				<option value="Masuk">Masuk</option>
				<option value="Keluar">Keluar</option>
				<option value="Lunas">Lunas</option>
			</select>
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top: -15px;">
			<label>Status 2</label>
			<select class="form-control chzn-select" name="status2" id="status2" required>
				<option value="<?php echo $sts2; ?>"><?php echo $sts2; ?></option>
				<option value="Normal">Normal</option>
				<option value="Kir">Kir</option>
				<option value="Dipecah">Dipecah</option>
				<option value="Digabung">Digabung</option>
				<option value="Gabungan">Gabungan</option>
				<option value="Pecahan">Pecahan</option>
				<option value="Tanpa Timbang">Tanpa Timbang</option>
			</select>
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top: -15px;">
			<label>Alasan Pengeditan</label>
			<input id="reason" name="reason" placeholder="Alasan Mengapa Diedit" type="text" class="form-control" required>                  
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-primary btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-floppy-o fa-fw" style="font-size: 16px;"></i>&nbsp;Simpan</button>               
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>
