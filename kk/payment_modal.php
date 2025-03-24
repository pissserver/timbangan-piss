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
	
	$paymentID = $_GET['id'];
	$name = executeScalar("select pay_name from payment where pay_id = '".$paymentID."'");
	$pay = executeScalar("select pay_total from payment where pay_id = '".$paymentID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-edit fa-fw"></i>&nbsp;Edit Data Tarif</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formPayment" name = "formPayment" method="POST" action="payment_action.php?task=update&id=<?php echo $paymentID; ?>">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
			<label>Jenis Tarif</label>
			<input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" autofocus="autofocus" required>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Nominal Tarif </label><sup> (Rp)</sup>
			<input class="form-control" id="pay" name="pay" type="number" value="<?php echo $pay; ?>" required/>                
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-primary btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-floppy-o fa-fw" style="font-size: 16px;"></i>&nbsp;Simpan</button>                   
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>