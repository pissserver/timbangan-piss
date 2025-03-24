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
	$id = $_GET['id'];
	$date_in = executeScalar("select date_in from weighing where weighing_id = '".$id."'");
	$date_out = executeScalar("select date_out from weighing where weighing_id = '".$id."'");
?>
<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-edit fa-fw"></i>&nbsp;Edit Tanggal Masuk / Keluar Kendaraan</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
		<form action="weighing_action.php?task=edit5_date&id=<?php echo $id; ?>"  id="form_edit1" name="form_edit1" method="POST" class="form-horizontal">
			<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
				<label>No Nota</label>
				<input id="weighing_id" name="weighing_id" value="<?php echo $id; ?>" type="number" class="form-control" disabled>  
			</div>
			<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top: -15px;">
				<label>Tanggal Masuk Kendaraan</label>
				<input id="date_in" name="date_in" value="<?php echo $date_in ? date("Y-m-d", strtotime($date_in)) : ''; ?>" type="date" class="form-control">
			</div>
			<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top: -15px;">
				<label>Tanggal Keluar Kendaraan</label>
				<input id="date_out" name="date_out" value="<?php echo $date_out ? date("Y-m-d", strtotime($date_out)) : ''; ?>" type="date" class="form-control">
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
