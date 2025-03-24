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
	
	$SCID = $_GET['id'];
	$name = executeScalar("select g.goods_name from seasonalcommodity sc join goods g on g.goods_id = sc.goods_id where id_sc = '".$SCID."'");
	$type = executeScalar("select g.goods_type from seasonalcommodity sc join goods g on g.goods_id = sc.goods_id where id_sc = '".$SCID."'");
	$status = executeScalar("select sc.status_aktif from seasonalcommodity sc join goods g on g.goods_id = sc.goods_id where id_sc = '".$SCID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-trash fa-fw"></i>&nbsp;Hapus Data Komoditi Musiman</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formSeasonalCommodity" name = "formSeasonalCommodity" method="POST" action="seasonalcommodity_action.php?task=delete&id=<?php echo $SCID; ?>">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Nama Komoditi Musiman </label>
			<input class="form-control" id="name" name="name" type="text" value="<?php echo $name; ?>" required disabled/>                
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Kategori Komoditi Musiman</label>
			<input type="text" class="form-control" id="type" name="type" value="<?php echo $type; ?>" autofocus="autofocus" required disabled>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Status</label>
			<input type="text" class="form-control" id="status" name="status" value="<?php echo $status == 1 ? 'Aktif' : 'Tidak Aktif'; ?>" autofocus="autofocus" required disabled>  
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-danger btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-trash fa-fw" style="font-size: 16px;"></i>&nbsp;Hapus</button>                   
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>