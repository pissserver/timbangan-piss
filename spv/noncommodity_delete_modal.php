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
	
	$nonCommodityID = $_GET['id'];
	$name = executeScalar("select noncommodity_name from noncommodity where noncommodity_id = '".$nonCommodityID."'");
	$weight = executeScalar("select noncommodity_weight from noncommodity where noncommodity_id = '".$nonCommodityID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-trash fa-fw"></i>&nbsp;Hapus Data NonKomoditi</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formLaborer" name = "formNonCommodity" method="POST" action="noncommodity_action.php?task=delete&id=<?php echo $nonCommodityID; ?>">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
			<label>Nama NonKomoditi</label>
			<input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" autofocus="autofocus" required disabled>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Berat </label><sup>(Kg)</sup>
			<input class="form-control" id="weight" name="weight" type="text" value="<?php echo $weight; ?>" required disabled/>                
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-danger btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-trash fa-fw" style="font-size: 16px;"></i>&nbsp;Hapus</button>                   
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>