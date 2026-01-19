<?php
	include "connection.php";

	// Generate KO ID Mitra
	$year			= date('Y');
	$ko_id_mitra 	= 0;
	$ko_id_mitra 	= executeScalar("select count(ko_id) from ko_mitra where ko_year = '".$year."'");
	$ko_id_mitra 	= $ko_id_mitra+1;
	$ko_id_mitra 	= sprintf("%06d", $ko_id_mitra);
	$ko_id_mitra  = $year.$ko_id_mitra;
	$check_mitra 	= executeScalar("select count(weighing_id) from weighing_mitra where weighing_sts3 = 'Pending'");
	$cost_mitra 	= executeScalar("select sum(cost_bayar) from weighing_mitra where weighing_sts3 = 'Pending'");
?>

<style>
	/* =========================
	FINANCE EXECUTIVE UI
	========================= */

	.payment-frame {
		background: linear-gradient(180deg,#ffffff,#f7faff);
		border-radius: 18px;
		padding: 26px;
		box-shadow:
			0 20px 40px rgba(15,23,42,.08),
			inset 0 1px 0 rgba(255,255,255,.6);
	}

	/* LIST */
	.payment-list {
		display: flex;
		flex-direction: column;
		gap: 14px;
	}

	/* CARD */
	.payment-card-mitra {
		position: relative;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 18px 22px;
		border-radius: 14px;
		background:
			linear-gradient(180deg,#ffffff,#f8fbff);
		border: 1px solid #e6edf5;
		transition: all .35s ease;
		overflow: hidden;
	}

	/* Accent bar kiri */
	.payment-card-mitra:before {
		content: "";
		position: absolute;
		left: 0;
		top: 0;
		width: 4px;
		height: 100%;
		background: linear-gradient(180deg, #4ade80, #22c55e);
		opacity: .9;
	}

	.payment-card-mitra:hover {
		transform: translateY(-3px);
		box-shadow:
			0 15px 35px rgba(15,23,42,.12);
	}

	/* LEFT */
	.payment-left {
		width: 24%;
	}

	.payment-left .nota-id-mitra {
		font-weight: 800;
		font-size: 15px;
		color: #16a34a;
	}

	.payment-left .nota-date {
		font-size: 12px;
		color: #64748b;
	}

	.payment-left .nota-truck {
		font-size: 13px;
		font-weight: 600;
		color: #334155;
	}

	/* MID */
	.payment-mid {
		width: 36%;
	}

	.payment-mid .merchant {
		font-weight: 800;
		font-size: 15px;
		color: #0f172a;
	}

	.payment-mid .commodity {
		font-size: 13px;
		color: #64748b;
	}

	.payment-mid .code {
		font-size: 12px;
		color: #94a3b8;
	}

	/* RIGHT */
	.payment-right {
		width: 26%;
		text-align: right;
	}

	.payment-right .netto {
		font-weight: 700;
		font-size: 14px;
		color: #0f172a;
	}

	.payment-right .amount {
		font-weight: 800;
		font-size: 15px;
		color: #16a34a;
	}

	/* CANCEL BUTTON */
	.btn-cancel {
		margin-top: 8px;
		border: none;
		background: linear-gradient(180deg,#fee2e2,#fecaca);
		color: #b91c1c;
		border-radius: 50%;
		width: 34px;
		height: 34px;
		box-shadow: 0 4px 10px rgba(220,38,38,.25);
		transition: all .3s ease;
	}

	.btn-cancel:hover {
		background: linear-gradient(180deg,#dc2626,#b91c1c);
		color: #fff;
		transform: rotate(90deg) scale(1.05);
	}

	/* CONTROL PANEL */
	.payment-control-panel {
		margin-top: 35px;
		padding: 30px;
		border-radius: 20px;
		background:
			linear-gradient(180deg,#ffffff,#f7faff);
		box-shadow:
			0 20px 40px rgba(15,23,42,.1);
	}

	/* SUMMARY */
	.summary-row {
		margin-bottom: 28px;
	}

	.summary-tile {
		padding: 22px;
		border-radius: 16px;
		text-align: center;
		position: relative;
		overflow: hidden;
	}

	/* Glow layer */
	.summary-tile:after {
		content: "";
		position: absolute;
		inset: 0;
		background: radial-gradient(circle at top,
			rgba(255,255,255,.5),
			transparent 70%);
	}

	.summary-tile span {
		font-size: 12px;
		letter-spacing: 1.2px;
		text-transform: uppercase;
		color: #64748b;
	}

	.summary-tile strong {
		display: block;
		margin-top: 6px;
		font-size: 28px;
		font-weight: 900;
	}

	/* KO */
	.summary-tile.ko {
		background: linear-gradient(135deg,#e0ecff,#f5f9ff);
		color: #2563eb;
		box-shadow: 0 10px 25px rgba(37,99,235,.2);
	}

	/* TOTAL */
	.summary-tile.total {
		background: linear-gradient(135deg,#dcfce7,#f0fdf4);
		color: #16a34a;
		box-shadow: 0 10px 25px rgba(22,163,74,.2);
	}

	/* PAYER */
	.payer-group label {
		font-weight: 800;
		color: #0f172a;
	}

	/* BUTTON AREA */
	.action-row {
		margin-top: 15px;
	}

	/* ================= MODAL FINANCE STYLE ================= */

	.modal-finance {
		border-radius: 14px;
		overflow: hidden;
		border: none;
		box-shadow: 0 15px 40px rgba(0,0,0,.25);
		animation: scaleIn .25s ease;
	}

	@keyframes scaleIn {
		from { transform: scale(.9); opacity: 0; }
		to   { transform: scale(1); opacity: 1; }
	}

	/* Header */
	.finance-header {
		display: flex;
		position: relative;
		align-items: center;
		gap: 15px;
		background: linear-gradient(135deg, #2563eb, #1e40af);
		color: #fff;
		padding: 18px 20px;
		border-bottom: none;
	}

	.finance-header h4 {
		margin: 0;
		font-weight: 700;
		letter-spacing: .5px;
	}

	.finance-header small {
		opacity: .85;
		font-size: 12px;
	}

	.finance-icon {
		width: 45px;
		height: 45px;
		background: rgba(255,255,255,.15);
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 20px;
	}

	.finance-close {
		position: absolute;
		top: 14px;
		right: 20px;   /* INI KUNCI: beri nafas dari tepi */

		width: 36px;
		height: 36px;
		border-radius: 10px;

		border: none;
		background: rgba(255,255,255,.15);
		color: #fff;

		display: flex;
		align-items: center;
		justify-content: center;

		font-size: 15px;
		cursor: pointer;

		transition: all .25s ease;
	}

	.finance-close:hover {
		background: rgba(255,255,255,.35);
		transform: scale(1.1);
	}

	/* Body */
	.finance-body {
		padding: 25px;
	}

	.finance-alert {
		background: #fef3c7;
		color: #92400e;
		padding: 12px 15px;
		border-radius: 10px;
		margin-bottom: 20px;
		font-weight: 600;
		display: flex;
		align-items: center;
		gap: 10px;
	}

	.finance-checklist {
		list-style: none;
		padding: 0;
		margin: 0;
	}

	.finance-checklist li {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px 0;
		border-bottom: 1px solid #eee;
		font-size: 15px;
	}

	.finance-checklist li i {
		color: #16a34a;
		font-size: 18px;
	}

	/* Footer */
	.finance-footer {
		padding: 18px 20px;
		background: #f8fafc;
		border-top: none;
	}

	.finance-footer .btn {
		min-width: 160px;
		border-radius: 30px;
		font-weight: 600;
	}

	.btn-confirm {
		box-shadow: 0 6px 15px rgba(37,99,235,.4);
	}

	/* ============================
	ENTERPRISE ACTION BUTTON
	============================ */

	.btn-success.btn-lg.btn-block,
	.btn-danger.btn-lg.btn-block {
		position: relative;
		border: none;
		border-radius: 14px;
		font-weight: 800;
		letter-spacing: .5px;
		padding: 14px 20px;
		font-size: 15px;
		overflow: hidden;
		transition: all .35s ease;
	}

	/* ================= GREEN ACC ================= */

	.btn-success.btn-lg.btn-block {
		background: linear-gradient(135deg,#22c55e,#16a34a);
		box-shadow:
			0 10px 25px rgba(34,197,94,.45),
			inset 0 1px 0 rgba(255,255,255,.4);
	}

	.btn-success.btn-lg.btn-block:before {
		content: "";
		position: absolute;
		inset: 0;
		background: linear-gradient(120deg,
			transparent 30%,
			rgba(255,255,255,.4),
			transparent 70%);
		transform: translateX(-100%);
		transition: .6s;
	}

	.btn-success.btn-lg.btn-block:hover:before {
		transform: translateX(100%);
	}

	.btn-success.btn-lg.btn-block:hover {
		transform: translateY(-3px);
		box-shadow:
			0 18px 40px rgba(34,197,94,.55);
	}

	/* ================= RED CANCEL ================= */

	.btn-danger.btn-lg.btn-block {
		background: linear-gradient(135deg,#ef4444,#dc2626);
		box-shadow:
			0 10px 25px rgba(239,68,68,.45),
			inset 0 1px 0 rgba(255,255,255,.4);
	}

	.btn-danger.btn-lg.btn-block:before {
		content: "";
		position: absolute;
		inset: 0;
		background: linear-gradient(120deg,
			transparent 30%,
			rgba(255,255,255,.35),
			transparent 70%);
		transform: translateX(-100%);
		transition: .6s;
	}

	.btn-danger.btn-lg.btn-block:hover:before {
		transform: translateX(100%);
	}

	.btn-danger.btn-lg.btn-block:hover {
		transform: translateY(-3px);
		box-shadow:
			0 18px 40px rgba(239,68,68,.55);
	}

	/* ================= ICON EFFECT ================= */

	.btn-success i,
	.btn-danger i {
		margin-right: 6px;
		transition: .35s;
	}

	.btn-success:hover i {
		transform: rotate(-5deg) scale(1.2);
	}

	.btn-danger:hover i {
		transform: rotate(90deg) scale(1.2);
	}

</style>


<!-- jquery -->
<script src="dataTables/jquery.dataTables.js"></script>
<script src="dataTables/dataTables.tableTools.js"></script>
<script src="dataTables/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
	//data table
	$('#pay_accViewMitra').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>


<div class="payment-frame">

	<!-- ================= LIST PENAMPUNGAN MITRA ================= -->
	<div class="payment-list">
	<?php
	$query = mysql_query("
		SELECT 
			w.weighing_id, 
			w.merchant_id, 
			w.netto,
			w.nopol, 
			w.cost_bagi_mitra,
			w.cost_simpan_mitra,
			w.cost_bagi_mandor,
			w.cost_simpan_mandor,
			w.cost_bayar,
			w.cost, 
			w.weighing_sts, 
			w.weighing_sts3, 
			w.date_out,
			(w.cost_bagi_mitra + w.cost_simpan_mitra) AS pendapatan_mitra,
			w.date_check,
			m.merchant_type 
		FROM weighing_mitra w
		INNER JOIN merchant m ON w.merchant_id = m.merchant_id
		WHERE 
			m.merchant_status = 'Aktif' AND
			w.weighing_sts = 'Keluar' AND 
			w.weighing_sts3 = 'Pending' AND
			w.date_in BETWEEN '2016-09-01' AND '".$date_now."'
		ORDER BY w.weighing_id DESC
	");

	while($data = mysql_fetch_array($query)) {
		$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
	?>
		<div class="payment-card-mitra mitra">

			<div class="payment-left">
				<div class="nota-id-mitra">#<?php echo $data['weighing_id']; ?></div>
				<div class="nota-date"><?php echo date("d M Y", strtotime($data['date_check'])); ?></div>
				<div class="nota-truck"><i class="fa fa-truck"></i> <?php echo $data['nopol']; ?></div>
			</div>

			<div class="payment-mid">
				<div class="merchant"><?php echo $merchant; ?></div>
				<div class="commodity"><?php echo $data['merchant_type']; ?></div>
				<div class="code">Kode: <?php echo $data['merchant_id']; ?></div>
			</div>

			<div class="payment-right">
				<div class="netto"><?php echo number_format($data['netto']); ?> Kg</div>
				<div class="amount"><?php echo format_rupiah($data['cost_bayar']); ?></div>

				<a href="pay_action.php?task=cancel_mitra&type=ko&id=<?php echo $data['weighing_id']; ?>">
					<button class="btn-cancel"><i class="fa fa-times"></i></button>
				</a>
			</div>

		</div>
	<?php } ?>
	</div>


	<!-- ================= SUMMARY ================= -->
	<?php if($check_mitra >= 1){ ?>

	<div class="payment-control-panel">

		<div class="row summary-row">
			<div class="col-md-6">
				<div class="summary-tile ko">
					<span>ID KO Mitra</span>
					<strong><?php echo $ko_id_mitra; ?></strong>
				</div>
			</div>
			<div class="col-md-6">
				<div class="summary-tile total">
					<span>Total Pembayaran Mitra</span>
					<strong><?php echo format_rupiah($cost_mitra); ?></strong>
				</div>
			</div>
		</div>

		<!-- ===== FORM ACC MITRA ===== -->
		<form action="pay_action.php?task=acc_mitra&type=ko"
			  method="POST"
			  id="accko_mitra"
			  target="_blank">

			<div class="form-group payer-group">
				<label><i class="fa fa-user"></i> Penyetor Uang</label>
				<select class="form-control input-lg" name="receiver" id="receiver-mitra" required>
					<option value="">- Pilih Penyetor -</option>
					<?php
					$sql = mysql_query("select checker_id, checker_name from checker
										where checker_sts='Aktif'
										and checker_role='Checker'
										and status_aktif='1'
										order by checker_name");
					while($c = mysql_fetch_array($sql)){
					?>
						<option value="<?php echo $c['checker_id']; ?>">
							<?php echo $c['checker_name']; ?>
						</option>
					<?php } ?>
				</select>
			</div>

			<div class="row action-row">
				<div class="col-md-12">
					<button type="button"
							class="btn btn-success btn-lg btn-block"
							id="btn-acc-mitra"
							data-toggle="modal"
							data-target="#confirmationModalMitra"
							style="display:none;">
						<i class="fa fa-check-circle"></i> ACC PEMBAYARAN MITRA
					</button>
				</div>
			</div>
		</form>

		<!-- ===== FORM BATALKAN SEMUA (TERPISAH) ===== -->
		<div class="row action-row">
			<div class="col-md-12">
				<form action="pay_action.php?task=cancelAll_mitra&type=ko"
					method="POST"
					>

					<button type="submit" class="btn btn-danger btn-lg btn-block">
						<i class="fa fa-times-circle"></i> BATALKAN SEMUA
					</button>
				</form>
			</div>
		</div>
	</div>

	<?php } else { ?>
		<div class="payment-notfound text-center">
			<img src="img/payment-mitra-notfound.png" width="200px">
			<h3><b>Belum Ada Pembayaran Mitra</b></h3>
		</div>
	<?php } ?>

</div>



<!-- ================= MODAL KONFIRMASI MITRA ================= -->
<div class="modal fade" id="confirmationModalMitra" tabindex="-1">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content modal-finance">

      <div class="modal-header finance-header">
        <div class="finance-icon">
          <i class="fa fa-shield"></i>
        </div>
        <div>
          <h4 class="modal-title">Verifikasi Akhir Pembayaran Mitra</h4>
          <small>Sistem Keuangan Terproteksi</small>
        </div>
        <button type="button" class="finance-close" data-dismiss="modal">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <div class="modal-body finance-body">

        <div class="finance-alert">
          <i class="fa fa-exclamation-triangle"></i>
          Pastikan seluruh data berikut telah diverifikasi dengan benar
        </div>

        <ul class="finance-checklist">
          <li><i class="fa fa-check-circle"></i> Nominal pembayaran sesuai nota mitra</li>
          <li><i class="fa fa-check-circle"></i> Total setoran sesuai uang fisik</li>
          <li><i class="fa fa-check-circle"></i> Data siap dicetak laporan KO Mitra</li>
        </ul>

      </div>

      <div class="modal-footer finance-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">
          <i class="fa fa-times"></i> Batal
        </button>

        <button type="button"
                class="btn btn-primary btn-confirm"
                id="confirmSubmitMitra">
          <i class="fa fa-check-circle"></i> Ya, Lanjutkan Pembayaran
        </button>
      </div>

    </div>
  </div>
</div>


<!-- ================= JS FLOW MITRA ================= -->
<script>
	const receiverMitra = document.getElementById('receiver-mitra');
	const btnAccMitra = document.getElementById('btn-acc-mitra');

	if(receiverMitra && btnAccMitra){
		receiverMitra.addEventListener('change', function(){
			btnAccMitra.style.display = this.value ? 'block' : 'none';
		});
	}

	$(document).on('click', '#confirmSubmitMitra', function () {

		const accForm = document.getElementById('accko_mitra');

		if(!accForm){
			alert('Form ACC Mitra tidak ditemukan!');
			return;
		}

		$('#confirmationModalMitra').modal('hide');

		setTimeout(function(){

			accForm.submit();

			setTimeout(function(){
				window.location.reload();
			}, 800);

		}, 300);

	});
</script>
