<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	
	$transportationID = $_GET['id'];
	$nopol = executeScalar("select transportation_nopol from transportation where transportation_id = '".$transportationID."'");
	$weight = executeScalar("select transportation_weight from transportation where transportation_id = '".$transportationID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-edit fa-fw"></i>&nbsp;Edit Data Kir Kendaraan</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formTransportation" name = "formTransportation" method="POST" action="transportation_action.php?task=update&id=<?php echo $transportationID; ?>">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
			<label>Nopol Kendaraan</label>
			<input type="text" class="form-control" id="trans" name="trans" value="<?php echo $nopol; ?>" autofocus="autofocus" required>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Berat Standar </label><sup> (Kg)</sup>
			<input class="form-control" id="weight" name="weight" type="text" value="<?php echo $weight; ?>" required/>                
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-primary btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-floppy-o fa-fw" style="font-size: 16px;"></i>&nbsp;Simpan</button>                   
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>