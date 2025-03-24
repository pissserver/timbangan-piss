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
	
	$goodsID = $_GET['id'];
	$name = executeScalar("select goods_name from goods where goods_id = '".$goodsID."'");
	$type = executeScalar("select goods_type from goods where goods_id = '".$goodsID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-edit fa-fw"></i>&nbsp;Edit Data Komoditi</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formGoods" name = "formGoods" method="POST" action="goods_action.php?task=update&id=<?php echo $goodsID; ?>">
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px;">
			<label>Nama Komoditi</label>
			<input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" autofocus="autofocus" required>  
		</div>
		<div class="form-group" style="padding:10px; margin-left:10px; margin-right: 10px; margin-top:-10px;">
			<label>Kategori Komoditi</label>
				<select class="form-control" id="type" name="type" required>
					<option value="Buah" <?php echo $type == 'Buah' ? 'selected' : '';?>>Buah</option>
					<option value="Sayur" <?php echo $type == 'Sayur' ? 'selected' : '';?>>Sayur</option>
					<option value="Pelengkap" <?php echo $type == 'Pelengkap' ? 'selected' : '';?>>Pelengkap</option>
				</select>
		</div>
		<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right: 10px;">
			<button type="submit" class="btn btn-outline btn-primary btn-block btn-sm" style="font-size: 14px;"><i class="fa fa-floppy-o fa-fw" style="font-size: 16px;"></i>&nbsp;Simpan</button>                   
		</div>
	</form>
	<input type="hidden" id="value_id" />
</div>