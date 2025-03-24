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
	
	$laborerID = $_GET['id'];
	$code = executeScalar("select laborer_code from laborer where laborer_id = '".$laborerID."'");
	$name = executeScalar("select laborer_name from laborer where laborer_id = '".$laborerID."'");
	$position = executeScalar("select laborer_role from laborer where laborer_id = '".$laborerID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-edit fa-fw"></i>&nbsp;Edit Data Mitra / Mandor</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formLaborer" name = "formLaborer" method="POST" action="laborer_action.php?task=update&id=<?php echo $laborerID; ?>">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
			<label>Kode Mitra / Mandor</label>
			<input type="text" class="form-control" id="code" name="code" value="<?php echo $code; ?>" autofocus="autofocus" required>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Nama Mitra / Mandor </label>
			<input class="form-control" id="name" name="name" type="text" value="<?php echo $name; ?>" required/>                
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Posisi</label>
			<select class="form-control" name="role" id="role" required>
				<option value="Mitra Buah" <?php echo $position == 'Mitra Buah' ? 'selected' : '';?>>Mitra Buah</option>
				<option value="Mitra Sayur" <?php echo $position == 'Mitra Sayur' ? 'selected' : '';?>>Mitra Sayur</option>
				<option value="Mandor" <?php echo $position == 'Mandor' ? 'selected' : '';?>>Mandor</option>
			</select>
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-primary btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-floppy-o fa-fw" style="font-size: 16px;"></i>&nbsp;Simpan</button>                   
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>