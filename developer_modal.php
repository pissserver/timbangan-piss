<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}

	$revisionID = $_GET['id'];
	$type = executeScalar("select type from revision where revision_id = '".$revisionID."'");
	$nota = executeScalar("select nota from revision where revision_id = '".$revisionID."'");
	$description = executeScalar("select description from revision where revision_id = '".$revisionID."'");
	$reason = executeScalar("select reason from revision where revision_id = '".$revisionID."'");
?>

<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
	<h4 style="margin-left: 20px;"><i class="fa fa-edit fa-fw"></i>&nbsp;Edit Data Revisi</h4>
	<button style="margin-top: -35px; margin-right: 20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
		<span aria-hidden="true">&times;</span>
	</button>
</div>
<div class="modal-body">
	<form class="form-horizontal" id="formRevision" name = "formRevision" method="POST" action="developer_action.php?task=update&id=<?php echo $revisionID; ?>">
		<fieldset>
										<div class="row" style="padding: 0 20px;">
											<div class="col-md-6">
												<div class="form-group" style="padding:10px; margin-top:-10px;">
													<label>Tipe Nota</label>
													<select class="form-control" name="type" id="type" required>
														<option value="TB" <?php if($type == 'TB') echo 'selected'; ?>>TB</option>
														<option value="TG" <?php if($type == 'TG') echo 'selected'; ?>>TG</option>
													</select>           
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" style="padding:10px; margin-top:-10px;">
													<label>No Nota</label>
													<input type="number" class="form-control" id="nota" name="nota" value="<?php echo $nota; ?>" required>
												</div>
											</div>
										</div>

										<div class="row" style="padding: 0 20px;">
											<div class="col-md-6">
												<div class="form-group" style="padding:10px; margin-top:-10px;">
													<label>Uraian Revisi</label>
													<textarea name="description" id="description" class="form-control" rows="4" cols="50" required><?php echo $description; ?></textarea>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group" style="padding:10px; margin-top:-10px;">
													<label>Alasan Revisi</label>
													<textarea name="reason" id="reason" class="form-control" rows="4" cols="50" required><?php echo $reason; ?></textarea>
												</div>
											</div>
										</div>
										<div class="col-md-12">
											<div class="form-group" style="padding:10px 15px; margin-bottom:-10px;">
												<center><button type="submit" class="btn btn-primary btn-block"><i class="fa fa-edit "></i>&nbsp;&nbsp;Edit</button></center>
											</div>
										</div>
									</fieldset>
	</form>
	<input type="hidden" id="value_id" />
</div>