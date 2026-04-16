<?php
	session_start();
	include "connection.php";
	$id 	= $_GET['id'];
	$ada 	= executeScalar("select count(weighing_id) from laborer_fee where weighing_id = '".$id."' ");
	$merchantType = executeScalar("select m.merchant_type from merchant m join weighing w on m.merchant_id = w.merchant_id where weighing_id = '".$id."' ");
	$type   = executeScalar("select m.merchant_type from merchant m join weighing w on m.merchant_id = w.merchant_id where weighing_id = '".$id."' ");
	$statusCheck = executeScalar("select date_check from weighing where weighing_id = '".$id."'");
	$checkStatusMitra = executeScalar("select laborer_sts from weighing where weighing_id = '".$id."'");
	$showGate = ($checkStatusMitra == 'Ya') ? false : true;

	// Cek apakah ada mitra buah atau sayur yang sudah dichecklist
	$mitraBuahChecked = executeScalar("
		SELECT COUNT(*) 
		FROM laborer_fee 
		WHERE weighing_id = '".$id."' 
		AND laborer_fee_sts != 'Batal'
		AND (B001 > 0 OR B002 > 0 OR B003 > 0 OR B004 > 0 OR B005 > 0 OR B006 > 0)
	");

	$mitraSayurChecked = executeScalar("
		SELECT COUNT(*) 
		FROM laborer_fee 
		WHERE weighing_id = '".$id."' 
		AND laborer_fee_sts != 'Batal'
		AND (S001 > 0 OR S002 > 0 OR S003 > 0 OR S004 > 0 OR S005 > 0 OR S006 > 0 OR S007 > 0 OR S008 > 0 OR S009 > 0 OR S010 > 0 OR S011 > 0 OR S012 > 0)
	");

	// Override type jika ditemukan mitra yang sudah dichecklist
	if ($mitraBuahChecked > 0) {
		$type = 'Buah';
	} elseif ($mitraSayurChecked > 0) {
		$type = 'Sayur';
	}
?>

<style>
	/* ===============================
	PANEL PREMIUM
	=============================== */
	.mitra-gate-premium {
		background: #fff;
		border-radius: 16px;
		padding: 25px;
		box-shadow: 0 15px 40px rgba(0,0,0,0.08);
		text-align: center;
		margin-bottom: 20px;
		position: relative;
		z-index: 10;
		animation: scaleIn 0.5s cubic-bezier(0.22, 1, 0.36, 1);
	}

	/* ===============================
	WARNING BOX (UPGRADE)
	=============================== */
	.warning-box {
		display: flex;
		align-items: flex-start;
		gap: 15px;
		background: linear-gradient(135deg, #fff8e6, #ffffff);
		border-left: 4px solid #f6a609;
		border-radius: 12px;
		padding: 15px;
		margin-bottom: 20px;
		transition: 0.3s;
	}

	.warning-box:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 25px rgba(0,0,0,0.06);
	}

	.warning-box .icon {
		font-size: 22px;
		color: #f6a609;
		margin-top: 5px;
		animation: pulseIcon 2s infinite;
	}

	@keyframes pulseIcon {
		0% { transform: scale(1); opacity: 1; }
		50% { transform: scale(1.15); opacity: 0.8; }
		100% { transform: scale(1); opacity: 1; }
	}

	/* ===============================
	TOGGLE AREA
	=============================== */
	.toggle-wrapper {
		display: flex;
		align-items: center;
		justify-content: center;
		gap: 15px;
		margin-top: 15px;
	}

	.label-left, .label-right {
		font-size: 13px;
		font-weight: 600;
		color: #999;
	}

	/* ===============================
	SWITCH PREMIUM
	=============================== */
	.switch-premium {
		position: relative;
		width: 90px;
		height: 36px;
	}

	.switch-premium input {
		display: none;
	}

	.slider-premium {
		position: absolute;
		cursor: pointer;
		background: #e0e0e0;
		border-radius: 50px;
		inset: 0;
		transition: 0.4s;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 0 12px;
		font-size: 11px;
		font-weight: 600;
		color: white;
		overflow: hidden;
	}

	/* GLOW EFFECT */
	.slider-premium::after {
		content: "";
		position: absolute;
		width: 120%;
		height: 120%;
		background: radial-gradient(circle, rgba(255,255,255,0.4), transparent);
		top: -10%;
		left: -10%;
		opacity: 0;
		transition: 0.4s;
	}

	/* BULATAN */
	.slider-premium:before {
		content: "";
		position: absolute;
		height: 28px;
		width: 28px;
		left: 4px;
		bottom: 4px;
		background: white;
		border-radius: 50%;
		transition: 0.4s cubic-bezier(0.22, 1, 0.36, 1);
		box-shadow: 0 3px 8px rgba(0,0,0,0.2);
	}

	/* TEXT */
	.text-off { opacity: 1; transition: 0.3s; }
	.text-on { opacity: 0; transition: 0.3s; }

	/* ACTIVE */
	.switch-premium input:checked + .slider-premium {
		background: linear-gradient(135deg, #4e73df, #224abe);
	}

	.switch-premium input:checked + .slider-premium:before {
		transform: translateX(50px) scale(1.05);
	}

	.switch-premium input:checked + .slider-premium::after {
		opacity: 1;
	}

	.switch-premium input:checked + .slider-premium .text-off {
		opacity: 0;
	}

	.switch-premium input:checked + .slider-premium .text-on {
		opacity: 1;
	}

	/* HOVER EFFECT */
	.slider-premium:hover {
		transform: scale(1.03);
	}

	/* ===============================
	HELPER TEXT
	=============================== */
	.helper-text {
		font-size: 12px;
		color: #777;
		margin-top: 12px;
		opacity: 0.9;
	}

	/* ===============================
	BLUR OVERLAY (UPGRADE)
	=============================== */
	.blur-overlay {
		position: absolute;
		inset: 0;
		backdrop-filter: blur(8px);
		background: rgba(255,255,255,0.5);
		z-index: 5;
		animation: fadeIn 0.4s ease;
		transition: all 0.4s ease;
	}

	/* ===============================
	CONTENT ANIMATION (FIX UTAMA)
	=============================== */
	.mitra-content {
		opacity: 0;
		transform: translateY(10px);
		pointer-events: none;
		transition: all 0.4s ease;
		display: none;
	}

	.mitra-content.show {
		display: block;
		opacity: 1;
		transform: translateY(0);
		pointer-events: auto;
	}

	/* GLOW SAAT MUNCUL */
	@keyframes unlockFlash {
		0% { box-shadow: 0 0 0 rgba(78,115,223,0); }
		50% { box-shadow: 0 0 30px rgba(78,115,223,0.2); }
		100% { box-shadow: 0 0 0 rgba(78,115,223,0); }
	}

	/* ===============================
	ANIMATIONS
	=============================== */
	@keyframes fadeUp {
		from { opacity: 0; transform: translateY(15px); }
		to { opacity: 1; transform: translateY(0); }
	}

	@keyframes scaleIn {
		from { opacity: 0; transform: scale(0.95); }
		to { opacity: 1; transform: scale(1); }
	}
</style>

<div class="panel panel-primary">
	<div class="panel-heading">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i></button>
		<i class="fa fa-group fa-fw"></i> Data Mitra
	</div>
	<div class="panel-body">
		<div id="blurOverlay" class="blur-overlay" style="<?php echo $showGate ? '' : 'display:none;'; ?>"></div>

		<div id="gateMitra" class="mitra-gate-premium" style="<?php echo $showGate ? '' : 'display:none;'; ?>">
			<div class="warning-box">
				<div class="icon">
					<i class="fa fa-shield"></i>
				</div>
				<div class="text">
					<h4>Perhatian Sebelum Input Mitra</h4>
					<p>
						Sebelum melanjutkan, pastikan data <b>NonKomoditi</b> telah diinput dengan benar.  
						Jika tidak terdapat data NonKomoditi, silakan lanjutkan pengisian Mitra dengan teliti sesuai nota timbang. Dan setelah melakukan simpan mitra, pastikan simpan data dihari yang sama juga!
					</p>
				</div>
			</div>

			<div class="toggle-wrapper">
				<span class="label-left">Tidak</span>

				<label class="switch-premium">
					<input type="checkbox" id="toggleMitra">
					<span class="slider-premium">
					</span>
				</label>

				<span class="label-right">Ya</span>
			</div>

			<p class="helper-text">
				Aktifkan untuk melanjutkan ke pengisian data Mitra
			</p>

		</div>


		<div id="mitraContent"
		class="mitra-content <?php echo !$showGate ? 'show' : ''; ?>"
		style="<?php echo $showGate ? 'display:none;' : 'display:block;'; ?>">
			<div class="row">
				<div style="margin: 0px 15px;" class="alert 
					<?php if ($mitraBuahChecked > 0 && $mitraSayurChecked > 0 && $statusCheck == NULL) {
						echo 'alert-warning';
					}  elseif ($mitraBuahChecked > 0 && $mitraSayurChecked > 0 && $statusCheck != NULL) {
						echo 'alert-danger';
					} elseif ($mitraBuahChecked == 0 && $mitraSayurChecked == 0 && $statusCheck == NULL) {
						echo 'alert-info';
					} elseif ($merchantType == 'Buah' && $type == 'Buah' && $statusCheck == NULL) {
						echo 'alert-info';
					} elseif ($merchantType == 'Sayur' && $type == 'Sayur' && $statusCheck == NULL) {
						echo 'alert-info';
					} elseif ($merchantType == 'Buah' && $type == 'Sayur' && $statusCheck == NULL) {
						echo 'alert-warning';
					} elseif ($merchantType == 'Sayur' && $type == 'Buah' && $statusCheck == NULL)  {
						echo 'alert-warning';
					} elseif (($merchantType == 'Sayur' && $type == 'Buah') || ($merchantType == 'Buah' && $type == 'Sayur') && $statusCheck != NULL)  {
						echo 'alert-danger';
					} elseif (($merchantType == 'Sayur' && $type == 'Sayur') || ($merchantType == 'Buah' && $type == 'Buah') && $statusCheck != NULL)  {
						echo 'alert-success';
					} 
					?>
					" role="alert">
					<table>
						<tr>
							<td style="font-size:20px; padding-right: 10px;">
								<?php echo $statusCheck != NULL ? '<i class="fa fa-check"></i>' : '<i class="fa fa-exclamation-triangle"></i>'; ?>
							</td>
							<td>
								<p style="font-size: 14px; padding-top:10px;">
									<?php if ($mitraBuahChecked > 0 && $mitraSayurChecked > 0 && $statusCheck == NULL) {
										echo 'Anda telah memilih mitra <b>Buah</b> dan <b>Sayur</b> (tidak boleh keduanya). Mohon cek kembali!';
									} elseif ($mitraBuahChecked > 0 && $mitraSayurChecked > 0 && $statusCheck != NULL) {
										echo 'Anda telah memilih mitra <b>Buah</b> dan <b>Sayur</b> (tidak boleh keduanya). Mohon lakukan revisi!';
									} elseif ($mitraBuahChecked == 0 && $mitraSayurChecked == 0) {
										echo 'Jenis pedagang adalah <b>' . $merchantType . '</b>. Tolong pilih mitra sesuai jenis pedagang jika data memakai mitra!';
									} elseif ($merchantType == 'Buah' && $type == 'Buah' && $statusCheck == NULL) {
										echo 'Jenis Pedagang <b>Buah</b> dan anda telah memilih mitra <b>Buah</b>. Sudah sesuai!';
									} elseif ($merchantType == 'Sayur' && $type == 'Sayur' && $statusCheck == NULL) {
										echo 'Jenis Pedagang <b>Sayur</b> dan anda telah memilih mitra <b>Sayur</b>. Sudah sesuai!';
									} elseif ($merchantType == 'Buah' && $type == 'Sayur' && $statusCheck == NULL) {
										echo 'Jenis Pedagang <b>Buah</b> tetapi anda telah memilih mitra <b>Sayur</b>. Mohon cek kembali!';
									} elseif ($merchantType == 'Sayur' && $type == 'Buah' && $statusCheck == NULL)  {
										echo 'Jenis Pedagang <b>Sayur</b> tetapi anda telah memilih mitra <b>Buah</b>. Mohon cek kembali!';
									} elseif (($merchantType == 'Sayur' && $type == 'Buah') || ($merchantType == 'Buah' && $type == 'Sayur') && $statusCheck != NULL)  {
										echo 'Jenis pedagang dan mitra yang anda pilih tidak sesuai. Mohon lakukan revisi!';
									} elseif (($merchantType == 'Sayur' && $type == 'Sayur') || ($merchantType == 'Buah' && $type == 'Buah') && $statusCheck != NULL)  {
										echo 'Jenis pedagang dan mitra yang anda pilih sudah sesuai!';
									}
									?>
								</p>
							</td>
						</tr>
					</table>
				</div>
				<div class="col-md-12" style="padding: 15px; margin-bottom: 10px;">
					<select class="form-control" name="type" id="type" <?php echo ($statusCheck !='' || $statusCheck != null) ? 'disabled' : '';?>>
						<option value="B" <?php echo $type == 'Buah' ? 'selected' : ''?>>Buah</option>
						<option value="S" <?php echo $type == 'Sayur' ? 'selected' : ''?>>Sayur</option>
					</select>
				</div>
			</div>

			<div class="row" style="font-size: 13px;">
				<form method="POST" action="weighing_action.php?task=Mitra&id=<?php echo $id; ?>" class="form-horizontal" id="formMitra" name="formMitra">
				<fieldset>
					<div class="col-md-2"></div>
					<div class="col-md-4" id="mitraBuah">
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
					<div class="col-md-4" id="mitraSayur">
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
					<div class="col-md-6" id="mandor">
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
								echo '<button type="submit" class="btn btn-primary btn-block"><i class="fa fa-floppy-o fa-fw"></i>&nbsp;Simpan</button>';
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
</div>


<script>
	$(document).ready(function() {

		function toggleMitra() {
			var val = $('#type').val();
			if (val === 'B') {
				$('#mitraBuah').show();
				$('#mitraSayur').hide();
				$('#mandor').show();
			} else if (val === 'S') {
				$('#mitraBuah').hide();
				$('#mitraSayur').show();
				$('#mandor').show();
			}
		}

		// Jalankan pertama kali saat modal dibuka
		toggleMitra();

		// Jalankan setiap kali user ganti type
		$('#type').on('change', function() {
			toggleMitra();
		});

	});
</script>

<script>
document.getElementById("toggleMitra").addEventListener("change", function() {
    if(this.checked){

        let gate = document.getElementById("gateMitra");
        let overlay = document.getElementById("blurOverlay");
        let content = document.getElementById("mitraContent");

        // fade out gate
        gate.style.transition = "0.3s ease";
        gate.style.opacity = "0";
        gate.style.transform = "scale(0.95)";

        if(overlay){
            overlay.style.transition = "0.3s ease";
            overlay.style.opacity = "0";
        }

        setTimeout(() => {
            gate.style.display = "none";
            if(overlay) overlay.style.display = "none";

            // tampilkan content dengan animasi
            content.style.display = "block";

            setTimeout(() => {
                content.classList.add("show");
            }, 50);

        }, 300);
    }
});
</script>
