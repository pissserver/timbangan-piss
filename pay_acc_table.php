<?php
	include "connection.php";

	//Generate KO ID Tonase
	$year			= date('Y');
	$ko_id 			= 0;
	$ko_id 			= executeScalar("select count(ko_id) from ko where ko_year = '".$year."'");
	$ko_id 			= $ko_id+1;
	$ko_id 			= sprintf("%06d", $ko_id);
	$ko_id  		= $year.$ko_id;
	$check 			= executeScalar("select count(weighing_id) from weighing where weighing_sts3 = 'Pending'");
	$cost_tonase 	= executeScalar("select sum(cost_tonase) from weighing where weighing_sts3 = 'Pending'");
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
	.payment-card-tonase {
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
	.payment-card-tonase:before {
		content: "";
		position: absolute;
		left: 0;
		top: 0;
		width: 4px;
		height: 100%;
		background: linear-gradient(180deg,#428BCA,#2563eb);
		opacity: .9;
	}

	.payment-card-tonase:hover {
		transform: translateY(-3px);
		box-shadow:
			0 15px 35px rgba(15,23,42,.12);
	}

	/* LEFT */
	.payment-left {
		width: 24%;
	}

	.payment-left .nota-id-tonase {
		font-weight: 800;
		font-size: 15px;
		color: #428BCA;
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

	/* ===============================
	MODERN DASHBOARD SELECT
	================================ */
	.select-dashboard {
		position: relative;
		background: #f6f8fb;
		border-radius: 14px;
		padding: 4px;
		box-shadow:
			0 10px 30px rgba(16,24,40,.06),
			inset 0 0 0 1px rgba(16,24,40,.04);
		transition: all .25s ease;
	}

	/* ICON */
	.select-dashboard i {
		position: absolute;
		left: 18px;
		top: 50%;
		transform: translateY(-50%);
		color: #64748b;
		font-size: 15px;
		pointer-events: none;
	}

	/* SELECT */
	.select-dashboard select {
		width: 100%;
		border: none;
		background: transparent;
		height: 54px;
		padding: 0 18px 0 48px;
		font-size: 14px;
		font-weight: 600;
		color: #0f172a;
		outline: none;
		appearance: none;
		cursor: pointer;
	}

	/* PLACEHOLDER COLOR */
	.select-dashboard select option[value=""] {
		color: #94a3b8;
	}

	/* FOCUS STATE */
	.select-dashboard:focus-within {
		background: #ffffff;
		box-shadow:
			0 0 0 4px rgba(59,130,246,.15),
			0 20px 40px rgba(16,24,40,.08);
	}

	/* ARROW */
	.select-dashboard::after {
		content: '\f107';
		font-family: FontAwesome;
		position: absolute;
		right: 18px;
		top: 50%;
		transform: translateY(-50%);
		color: #94a3b8;
		font-size: 16px;
		pointer-events: none;
	}

	/* BUTTON AREA */
	.action-row {
		margin-top: 15px;
	}

	/* ================= MODAL FINANCE STYLE ================= */

	.modal-reminder {
		border-radius: 16px;
		border: none;
		overflow: hidden;
		box-shadow: 0 20px 50px rgba(0,0,0,.35);
		animation: fadeUp .25s ease;
	}

	@keyframes fadeUp {
		from { transform: translateY(20px); opacity: 0; }
		to   { transform: translateY(0); opacity: 1; }
	}

	/* Header */
	.reminder-header {
		position: relative;
		display: flex;
		align-items: center;
		gap: 15px;
		padding: 20px;
		background: linear-gradient(135deg, #1f2933, #111827);
		color: #fff;
	}

	.reminder-badge {
		width: 48px;
		height: 48px;
		border-radius: 14px;
		background: rgba(251,191,36,.2);
		color: #fbbf24;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 22px;
	}

	.reminder-title h4 {
		margin: 0;
		font-weight: 700;
		letter-spacing: .4px;
	}

	.reminder-title span {
		font-size: 12px;
		opacity: .8;
	}

	.reminder-close {
		position: absolute;
		right: 18px;
		top: 18px;
		width: 34px;
		height: 34px;
		border-radius: 10px;
		border: none;
		background: rgba(255,255,255,.1);
		color: #fff;
		cursor: pointer;
		transition: .2s;
	}

	.reminder-close:hover {
		background: rgba(255,255,255,.25);
		transform: rotate(90deg);
	}

	/* Body */
	.reminder-body {
		padding: 25px;
	}

	.reminder-message {
		display: flex;
		gap: 12px;
		background: #fffbeb;
		color: #92400e;
		padding: 15px;
		border-radius: 12px;
		font-size: 14px;
		margin-bottom: 22px;
	}

	.reminder-message i {
		font-size: 20px;
		margin-top: 2px;
		color: #f59e0b;
	}

	.reminder-points {
		display: grid;
		gap: 14px;
	}

	.reminder-points .point {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px 14px;
		border-radius: 12px;
		background: #f9fafb;
		border: 1px solid #e5e7eb;
		font-size: 14px;
	}

	.reminder-points .point i {
		color: #f59e0b;
		font-size: 18px;
	}

	/* Footer */
	.reminder-footer {
		padding: 18px 20px;
		border-top: none;
		background: #f8fafc;
		display: flex;
		justify-content: space-between;
	}

	.reminder-footer .btn {
		min-width: 150px;
		border-radius: 30px;
		font-weight: 600;
	}

	.btn-reminder-confirm {
		box-shadow: 0 8px 20px rgba(245,158,11,.45);
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
	$('#pay_accView').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>

<div class="payment-frame">
	<!-- ================= LIST PENAMPUNGAN ================= -->
	<div class="payment-list">
	<?php
	$query = mysql_query(" select  w.weighing_id, w.merchant_id, w.nopol,
									w.netto, w.cost_tonase, w.date_out,
									m.merchant_type
							from weighing w, merchant m
							where w.merchant_id = m.merchant_id
							and m.merchant_status='Aktif'
							and w.weighing_sts='Keluar'
							and w.weighing_sts3='Pending'
							and (w.date_in between '2016-09-01' and '".$date_now."')
							order by weighing_id desc");

	while($data = mysql_fetch_array($query)) {
		$merchant = executeScalar("select merchant_name from merchant
								where merchant_id='".$data['merchant_id']."'");
	?>
		<div class="payment-card-tonase">
			<div class="payment-left">
				<div class="nota-id-tonase">#<?php echo $data['weighing_id']; ?></div>
				<div class="nota-date"><?php echo date("d M Y", strtotime($data['date_out'])); ?></div>
				<div class="nota-truck"><i class="fa fa-truck"></i> <?php echo $data['nopol']; ?></div>
			</div>

			<div class="payment-mid">
				<div class="merchant"><i class="fa fa-user"></i>&nbsp;&nbsp;<?php echo $merchant; ?></div>
				<div class="commodity"><?php echo $data['merchant_type']; ?></div>
				<div class="code">Kode: <?php echo $data['merchant_id']; ?></div>
			</div>

			<div class="payment-right">
				<div class="netto"><?php echo number_format($data['netto']); ?> Kg</div>
				<div class="amount"><?php echo format_rupiah($data['cost_tonase']); ?></div>

				<a href="pay_action.php?task=cancel&type=ko&id=<?php echo $data['weighing_id']; ?>">
					<button class="btn-cancel"><i class="fa fa-times"></i></button>
				</a>
			</div>
		</div>
	<?php } ?>
	</div>

	<!-- ================= SUMMARY ================= -->
	<?php if($check>=1){ ?>

	<div class="payment-control-panel">

	<div class="row summary-row">
		<div class="col-md-6">
			<div class="summary-tile ko">
				<span>ID KO</span>
				<strong><?php echo $ko_id; ?></strong>
			</div>
		</div>
		<div class="col-md-6">
			<div class="summary-tile total">
				<span>Total Pembayaran</span>
				<strong><?php echo format_rupiah($cost_tonase); ?></strong>
			</div>
		</div>
	</div>

	<!-- ===== FORM ACC ===== -->
	<form action="pay_action.php?task=acc&type=ko"
		method="POST"
		id="accko"
		target="_blank">

	<div class="form-group payer-group">
		<div class="select-dashboard">
			<i class="fa fa-user"></i>
			<select name="receiver" id="receiver" required>
				<option value="">Pilih Penyetor</option>
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
	</div>

	<div class="row action-row">
		<div class="col-md-12">
			<button type="button"
					class="btn btn-success btn-lg btn-block"
					id="btn-acc"
					data-toggle="modal"
					data-target="#confirmationModal"
					style="display:none;">
				<i class="fa fa-check-circle"></i> BAYAR TAGIHAN
			</button>
		</div>
	</div>
	</form>
	<!-- ===== END FORM ACC ===== -->

	<!-- ===== FORM BATALKAN SEMUA (TERPISAH) ===== -->
	<div class="row action-row">
		<div class="col-md-12">
			<form action="pay_action.php?task=cancelAll&type=ko"
				method="POST"
				>

				<button type="submit" class="btn btn-danger btn-lg btn-block">
					<i class="fa fa-times-circle"></i> BATALKAN SEMUA
				</button>
			</form>
		</div>
	</div>

	</div>
	 <?php } elseif ($check < 1) { ?>
		<div class="payment-notfound text-center">
			<img src="img/payment-tonase-notfound.png" alt="Image Payment Not Found" width="200px">
			<h3><b>Belum Ada Pembayaran</b></h3>
		</div>
	<?php } ?>
</div>


<!-- Modal Konfirmasi ACC Pembayaran -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content modal-reminder">

      <!-- Header -->
      <div class="reminder-header">
        <div class="reminder-badge">
          <i class="fa fa-bell"></i>
        </div>

        <div class="reminder-title">
          <h4>Pengingat Sebelum Pembayaran</h4>
          <span>Tindakan ini bersifat final</span>
        </div>

        <button type="button" class="reminder-close" data-dismiss="modal">
          <i class="fa fa-times"></i>
        </button>
      </div>

      <!-- Body -->
      <div class="modal-body reminder-body">

        <div class="reminder-message">
          <i class="fa fa-info-circle"></i>
          <p>
            Pastikan Anda telah menerima dan menghitung uang fisik dengan benar.
            Setelah pembayaran dikonfirmasi, <b>data akan dikunci</b> dan tidak
            dapat diubah tanpa otorisasi khusus.
          </p>
        </div>

        <div class="reminder-points">
          <div class="point">
            <i class="fa fa-money"></i>
            <span>Jumlah uang fisik sudah sesuai</span>
          </div>

          <div class="point">
            <i class="fa fa-file-text"></i>
            <span>Tidak ada nota yang terlewat</span>
          </div>

          <div class="point">
            <i class="fa fa-lock"></i>
            <span>Pembayaran akan langsung dikunci sistem</span>
          </div>
        </div>

      </div>

      <!-- Footer -->
      <div class="modal-footer reminder-footer">
        <button class="btn btn-outline-secondary" data-dismiss="modal">
          Cek Ulang
        </button>

        <button class="btn btn-warning btn-reminder-confirm" id="confirmSubmit">
          <i class="fa fa-check"></i> Saya Mengerti & Lanjutkan
        </button>
      </div>

    </div>
  </div>
</div>



<!-- Reload Window Tonase-->
<script>
	const receiver = document.getElementById('receiver');
	const btnAcc = document.getElementById('btn-acc');

	if(receiver && btnAcc){
		receiver.addEventListener('change', function(){
			btnAcc.style.display = this.value ? 'block' : 'none';
		});
	}

	// ================= FIX MODAL SUBMIT =================
    $(document).on('click', '#confirmSubmit', function () {

        const accForm = document.getElementById('accko');

        if(!accForm){
            alert('Form ACC tidak ditemukan!');
            return;
        }

        // Tutup modal dulu
        $('#confirmationModal').modal('hide');

        // Delay kecil agar modal benar-benar tertutup
        setTimeout(function(){

            accForm.submit();

            // Reload halaman setelah submit terkirim
            setTimeout(function(){
                window.location.reload();
            }, 800);

        }, 300);

    });
</script>


