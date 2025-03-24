<?php
	session_start();
	include "connection.php";
	$id 	= $_GET['id'];
	$ada 	= executeScalar("select count(weighing_id) from laborer_fee where weighing_id = '".$id."' ");
?>
<div class="panel panel-primary">
	<div class="panel-heading">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
		<i class="fa fa-group fa-fw"></i> Data Mitra
	</div>
	<div class="panel-body">
		<div class="row" style="font-size: 13px;">
			<form method="POST" action="weighing_action.php?task=Mitra&id=<?php echo $id; ?>" class="form-horizontal" id="formMitra" name="formMitra">
			<fieldset>
				<div class="col-md-4">
					<div class="form-group" style="padding:10px; margin-top:-10px;">
						<input type="checkbox" id="mitra[]" name="mitra[]" value="B001"  <?php if($ada>=1){
							$x 	= executeScalar("select sum(B001) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Nanang Budiono (B001)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="B002" <?php if($ada>=1){
							$x 	= executeScalar("select sum(B002) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Putut Sugiyarto (B002)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="B003" <?php if($ada>=1){
							$x 	= executeScalar("select sum(B003) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Abd. Muid (B003)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="B004" <?php if($ada>=1){
							$x 	= executeScalar("select sum(B004) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Moh. Muid Faruq (B004)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="B005" <?php if($ada>=1){
							$x 	= executeScalar("select sum(B005) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Mat Rizal (B005)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="B006" <?php if($ada>=1){
							$x 	= executeScalar("select sum(B006) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Sulaiman (B006)</label><br>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group" style="padding:10px; margin-top:-10px;">
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S001" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S001) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Markum (S001)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S002" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S002) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Veri (S002)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S003" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S003) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Arifin (S003)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S004" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S004) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Jastro (S004)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S005" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S005) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Ahmad Nurkholis (S005)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S006" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S006) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Nawari (S006)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S007" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S007) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Muis Hariyanto (S007)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S008" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S008) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Imam (S008)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S009" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S009) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Muhammad (S009)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S010" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S010) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Feru (S010)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S011" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S011) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Durasid (S011)</label><br>
						<input type="checkbox" id="mitra[]" name="mitra[]" value="S012" <?php if($ada>=1){
							$x 	= executeScalar("select sum(S012) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Sumiran (S012)</label><br>
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group" style="padding:10px; margin-top:-10px;">
						<input type="checkbox" id="mandor[]" name="mandor[]" value="M01"  <?php if($ada>=1){
							$x 	= executeScalar("select sum(M01) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Darwanto (M01)</label><br>
						<input type="checkbox" id="mandor[]" name="mandor[]" value="M02"  <?php if($ada>=1){
							$x 	= executeScalar("select sum(M02) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Tri Wibowo (M02)</label><br>
						<input type="checkbox" id="mandor[]" name="mandor[]" value="M03"  <?php if($ada>=1){
							$x 	= executeScalar("select sum(M03) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Abdul Roud (M03)</label><br>
						<input type="checkbox" id="mandor[]" name="mandor[]" value="M04"  <?php if($ada>=1){
							$x 	= executeScalar("select sum(M04) from laborer_fee where laborer_fee_sts != 'Batal' and weighing_id = '".$id."' ");
							if($x>0){echo 'checked';}} ?>><label class="light">Mohammad Rois (M04)</label><br>
					</div>
				</div>
				<div class="form-group" style="padding:10px; margin-top:30px; margin-bottom:-10px; margin-left:0px; margin-right:0px;">
					<?php
						$c 	= executeScalar("select date_check from weighing where weighing_id = '".$id."'");
						$p2 = executeScalar("select p2 from weighing where weighing_id = '".$id."'");
						if($c!=''||$c!=null||$p2!=null){
							// echo '<button type="submit" class="btn btn-primary btn-block">Simpan <i class="fa fa-angle-double-right fa-lg"></i></button>';
						}else{
							echo '<button type="submit" class="btn btn-primary btn-block">Simpan <i class="fa fa-angle-double-right fa-lg"></i></button>';
						}
					?>
				</div>
				<!-- Fitur Check Box All--> 
				<!--<div>
				<input type="checkbox" id="checkAll"> Check All
				</div>-->
			</fieldset>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	/*$('#checkAll').click(function ()
	{    
    	$('input:checkbox').prop('checked', this.checked);*/
 	}); // UPDATE DISINI
</script>