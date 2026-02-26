<?php
	session_start();
	include "connection.php";

	$cashier = executeScalar("select checker_id from checker where checker_code = '".$_SESSION['checker']."'");

	//Generate KM ID Tonase
	$km_id 	= 0;
	$year	= date('Y');
	$km_id 	= executeScalar("select count(km_id) from km where km_year = '".$year."'");
	$km_id 	= $km_id + 1;
	$km_id 	= sprintf("%06d", $km_id);
	$km_id  = $year.$km_id;
	$cost 	= executeScalar("select sum(ko_total) from ko where cashier_id = '".$cashier."' and ko_status = 'Diterima'");
	$check 	= executeScalar("select count(ko_id) from ko where cashier_id = '".$cashier."' and ko_status = 'Diterima'");
?>

<style>
	/* PAGE */
	.payment-page {
		width: 100%;
		min-height: calc(100vh - 40px);
		padding: 20px 30px;
		box-sizing: border-box;

		/* HILANGKAN CENTER */
		display: block;
	}

	/* WRAPPER */
	.payment-wrapper {
		width: 100%;
		max-width: 100%;       /* FULL */
		margin: 0;             /* NO CENTER */
		background: #fff;
		border-radius: 22px;
		display: flex;
		overflow: hidden;
		box-shadow: 0 25px 60px rgba(0,0,0,.12);
	}

	/* LEFT PANEL */
	.payment-left-panel {
		width: 62%;
		background: #f4f7ff;
		padding: 30px;
		height: calc(100vh - 120px);
		overflow-y: auto;
	}
	.payment-left-panel::-webkit-scrollbar { display: none; }

	/* RIGHT PANEL */
	.payment-right-panel {
		width: 38%;
		padding: 30px;
		background: #fff;
	}

	.payment-header {
		margin-bottom: 10px;
	}

	.payment-header .logo {
		font-size: 22px;
		font-weight: bold;
		color: #5f4dee;
	}

	.payment-header small {
		color: #777;
	}

	/* ITEM */
	.payment-item {
		background: #fff;
		border-radius: 16px;
		padding: 18px;
		margin-top: 18px;
		display: grid;
		grid-template-columns: 1.2fr 1fr 1fr auto;
		gap: 15px;
		align-items: center;
		box-shadow: 0 10px 30px rgba(0,0,0,.06);
		transition: .4s;
		backdrop-filter: blur(6px);
	}

	.payment-item:hover {
		transform: translateY(-4px);
		box-shadow: 0 18px 40px rgba(95,77,238,.25);
	}

	.item-id {
		font-weight: bold;
		color: #5f4dee;
	}
	.item-id span {
		display: block;
		font-size: 11px;
		color: #777;
	}

	.item-info strong {
		font-size: 14px;
	}
	.item-info small {
		font-size: 12px;
		color: #777;
	}

	.item-amount {
		padding-top: 16px;
		font-weight: bold;
		color: #198754;
		font-size: 16px;
	}

	/* ================= BADGE SIAP SETOR ================= */
	.payment-item {
		position: relative;
	}

	/* BADGE */
	.payment-badge-ready {
		position: absolute;
		top: -10px;
		right: -10px;

		background: linear-gradient(135deg,#2ecc71,#27ae60);
		color: #fff;
		font-size: 11px;
		font-weight: 600;
		padding: 6px 12px;
		border-radius: 999px;

		display: flex;
		align-items: center;
		gap: 6px;

		box-shadow: 0 8px 20px rgba(46,204,113,.45);
		animation: badgePop .6s ease, badgePulse 2.5s infinite;
	}

	/* ICON */
	.payment-badge-ready i {
		font-size: 13px;
	}

	/* HOVER EFFECT */
	.payment-item:hover .payment-badge-ready {
		transform: scale(1.05);
		box-shadow: 0 12px 30px rgba(46,204,113,.6);
	}

	/* RIGHT PANEL */
	.payment-right-panel {
		width: 45%;
		padding: 40px;
	}

	/* SUMMARY */
	.summary-card {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 15px;
		margin-bottom: 30px;
	}

	.summary-box {
		padding: 20px;
		border-radius: 18px;
		background: linear-gradient(135deg,#667eea,#764ba2);
		color: #fff;
	}
	.summary-box.total {
		background: linear-gradient(135deg,#43e97b,#38f9d7);
	}

	.summary-box span {
		font-size: 13px;
	}
	.summary-box strong {
		font-size: 22px;
		display: block;
		margin-top: 6px;
	}

	/* FORM */
	.form-group label {
		font-size: 13px;
		color: #666;
	}

	.form-group input {
		width: 100%;
		margin-top: 8px;
		padding: 14px;
		border-radius: 14px;
		border: none;
		background: #f3f5fb;
		font-size: 15px;
	}

	/* REMOVE NUMBER ARROW */
	input[type=number]::-webkit-inner-spin-button,
	input[type=number]::-webkit-outer-spin-button {
		-webkit-appearance: none;
	}
	input[type=number] {
		-moz-appearance: textfield;
	}

	/* BUTTON */
	.btn-pay {
		width: 100%;
		margin-top: 25px;
		padding: 16px;
		border-radius: 16px;
		border: none;
		background: linear-gradient(135deg,#5f4dee,#7a6bff);
		color: #fff;
		font-size: 16px;
		cursor: pointer;
		transition: .4s;
	}

	.btn-pay:hover {
		transform: translateY(-3px);
		box-shadow: 0 15px 35px rgba(95,77,238,.5);
	}

	.btn-cancel {
		margin-top: 15px;
		width: 100%;
		padding: 14px;
		border-radius: 16px;
		border: none;
		background: linear-gradient(135deg,#ff416c,#ff4b2b);
		color: #fff;
		cursor: pointer;
	}

	/* ================= EMPTY STATE ================= */

	.payment-empty {
		width: 100%;
		min-height: 60vh;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 40px;
	}

	.empty-card {
		background: #fff;
		padding: 45px 50px;
		border-radius: 18px;
		text-align: center;
		max-width: 480px;
		width: 100%;

		box-shadow: 0 20px 45px rgba(0,0,0,.08);
		animation: fadeUp .5s ease;
	}

	@keyframes fadeUp {
		from {
			transform: translateY(20px);
			opacity: 0;
		}
		to {
			transform: translateY(0);
			opacity: 1;
		}
	}

	.empty-card img {
		max-width: 260px;
		margin-bottom: 25px;
		opacity: .95;
	}

	.empty-card h3 {
		color: #0f172a;
		font-weight: 800;
		margin-bottom: 12px;
		letter-spacing: .4px;
	}

	.empty-card p {
		color: #64748b;
		font-size: 14px;
		line-height: 1.6;
		margin-bottom: 18px;
	}

	.empty-hint {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 10px 16px;
		border-radius: 30px;

		background: #eff6ff;
		color: #2563eb;
		font-size: 13px;
		font-weight: 600;
	}

	.empty-hint i {
		font-size: 15px;
	}

	/* ALERT NOMINAL */
	.amount-alert {
		display: none;
		margin-top: 14px;
		padding: 14px 16px;
		border-radius: 14px;
		background: linear-gradient(135deg,#ff416c,#ff4b2b);
		color: #fff;
		font-size: 14px;
		line-height: 1.4;
		box-shadow: 0 10px 30px rgba(255,65,108,.35);
		animation: blinkSoft 1.3s infinite;
	}

	.amount-alert i {
		margin-right: 6px;
	}

	.amount-alert small {
		display: block;
		font-size: 12px;
		opacity: .9;
	}

	/* BLINK HALUS (BUKAN KASAR) */
	@keyframes blinkSoft {
		0%   { opacity: 1; }
		50%  { opacity: .45; }
		100% { opacity: 1; }
	}


	/* ANIM */
	@keyframes fadeUp {
		from { opacity:0; transform: translateY(30px); }
		to { opacity:1; transform: translateY(0); }
	}

	@keyframes badgePop {
		from {
			opacity: 0;
			transform: scale(.7) translateY(-10px);
		}
		to {
			opacity: 1;
			transform: scale(1) translateY(0);
		}
	}

	@keyframes badgePulse {
		0%   { box-shadow: 0 0 0 0 rgba(46,204,113,.4); }
		70%  { box-shadow: 0 0 0 10px rgba(46,204,113,0); }
		100% { box-shadow: 0 0 0 0 rgba(46,204,113,0); }
	}

	/* ALERT NOMINAL */
	.amount-alert {
		display: none;
		margin-top: 14px;
		padding: 14px 16px;
		border-radius: 14px;
		background: linear-gradient(135deg,#ff416c,#ff4b2b);
		color: #fff;
		font-size: 14px;
		line-height: 1.4;
		box-shadow: 0 10px 30px rgba(255,65,108,.35);
		animation: blinkSoft 1.3s infinite;
	}

	.amount-alert i {
		margin-right: 6px;
	}

	.amount-alert small {
		display: block;
		font-size: 12px;
		opacity: .9;
	}

	/* BLINK HALUS (BUKAN KASAR) */
	@keyframes blinkSoft {
		0%   { opacity: 1; }
		50%  { opacity: .45; }
		100% { opacity: 1; }
	}


	/* ANIM */
	@keyframes fadeUp {
		from { opacity:0; transform: translateY(30px); }
		to { opacity:1; transform: translateY(0); }
	}

	@keyframes badgePop {
		from {
			opacity: 0;
			transform: scale(.7) translateY(-10px);
		}
		to {
			opacity: 1;
			transform: scale(1) translateY(0);
		}
	}

	@keyframes badgePulse {
		0%   { box-shadow: 0 0 0 0 rgba(46,204,113,.4); }
		70%  { box-shadow: 0 0 0 10px rgba(46,204,113,0); }
		100% { box-shadow: 0 0 0 0 rgba(46,204,113,0); }
	}

	/* ================= MODAL FINANCE STYLE ================= */

	.modal-secure {
		position: relative;
		border-radius: 16px;
		overflow: hidden;
		border: none;
		box-shadow: 0 18px 50px rgba(0,0,0,.4);
		animation: scaleIn .3s ease forwards;
		background: #fff;
		z-index: 1000;
	}

	@keyframes scaleIn {
	from { transform: scale(.9); opacity: 0; }
	to   { transform: scale(1); opacity: 1; }
	}

	/* Background Lock Icon */
	.modal-bg-lock {
		position: absolute;
		top: 50%; left: 50%;
		transform: translate(-50%, -50%);
		font-size: 120px;
		color: rgba(0,0,0,0.03);
		z-index: 0;
		animation: rotateLock 8s linear infinite;
	}

	@keyframes rotateLock {
	from { transform: translate(-50%, -50%) rotate(0deg); }
	to   { transform: translate(-50%, -50%) rotate(360deg); }
	}

	/* Header */
	.secure-header {
		display: flex;
		align-items: center;
		gap: 15px;
		position: relative;
		padding: 20px;
		color: #fff;
		background: linear-gradient(135deg,#0d3b66,#1e90ff);
		z-index: 1;
	}

	.secure-header h4 {
		margin: 0;
		font-weight: 700;
		letter-spacing: .5px;
		text-transform: uppercase;
	}

	.secure-header small {
		opacity: .85;
		font-size: 12px;
	}

	.secure-icon {
		width: 50px;
		height: 50px;
		background: rgba(255,255,255,.15);
		border-radius: 12px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 24px;
		box-shadow: 0 0 20px rgba(0,255,255,0.5);
		color: #0ff;
		animation: neonGlow 1.5s ease-in-out infinite alternate;
	}

	@keyframes neonGlow {
		from { box-shadow: 0 0 8px #0ff; }
		to   { box-shadow: 0 0 20px #0ff; }
	}

	.secure-close {
		position: absolute;
		top: 15px;
		right: 20px;
		width: 38px;
		height: 38px;
		border-radius: 12px;
		border: none;
		background: rgba(255,255,255,.15);
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		transition: all .25s ease;
	}
	.secure-close:hover { background: rgba(255,255,255,.35); transform: scale(1.1); }

	/* Body */
	.secure-body { padding: 25px; position: relative; z-index: 1; }

	.secure-alert {
		background: #D1FAE5;
		color: #065f46;
		padding: 12px 15px;
		border-radius: 12px;
		display: flex;
		align-items: center;
		gap: 10px;
		font-weight: 600;
		margin-bottom: 20px;
	}

	/* Checklist Animasi Masuk */
	.secure-checklist {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	.secure-checklist li {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px 16px;
		border-radius: 12px;
		border: 1px solid #eee;
		font-size: 15px;
		margin-bottom: 8px;
		opacity: 0;
		transform: translateX(-20px);
		animation: slideIn 0.5s forwards;
	}
	.secure-checklist li i { color: #16a34a; font-size: 18px; text-shadow: 0 0 6px #16a34a; }

	@keyframes slideIn {
		to { opacity: 1; transform: translateX(0); }
	}

	/* Verification progress */
	.verification-progress {
		height: 6px;
		width: 100%;
		background: #eee;
		border-radius: 6px;
		margin-top: 20px;
		overflow: hidden;
	}
	.verification-progress .progress-bar {
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg,#16a34a,#22c55e);
		animation: progressGlow 1s infinite alternate;
	}
	@keyframes progressGlow {
		from { box-shadow: 0 0 8px #16a34a; }
		to   { box-shadow: 0 0 20px #22c55e; }
	}

	/* Footer */
	.secure-footer { padding: 18px 20px; background: #f8fafc; border-top: none; z-index: 1; }
	.secure-footer .btn { min-width: 160px; border-radius: 30px; font-weight: 600; transition: all .25s; }
	.btn-confirm { box-shadow: 0 8px 20px rgba(37,99,235,.4); animation: neonButton 1.5s ease-in-out infinite alternate; }
	.btn-confirm:hover { transform: scale(1.05); box-shadow: 0 12px 30px rgba(37,99,235,.6); }

	@keyframes neonButton {
		from { box-shadow: 0 8px 20px rgba(37,99,235,.4); }
		to   { box-shadow: 0 8px 30px rgba(0,255,255,.7); }
	}

	/* ================= VERIFY LOADING ================= */

	.verify-loading {
		position: fixed;
		inset: 0;
		background: rgba(15,23,42,.75);
		z-index: 2000;

		display: none;
		align-items: center;
		justify-content: center;
	}

	.verify-box {
		background: #fff;
		border-radius: 16px;
		padding: 30px 35px;
		min-width: 260px;
		text-align: center;

		box-shadow: 0 25px 60px rgba(0,0,0,.35);
		animation: popIn .3s ease;
	}

	@keyframes popIn {
		from { transform: scale(.85); opacity: 0; }
		to   { transform: scale(1); opacity: 1; }
	}

	/* Spinner */
	.verify-spinner {
		width: 55px;
		height: 55px;
		border: 5px solid #e5e7eb;
		border-top-color: #2563eb;
		border-radius: 50%;
		margin: 0 auto 15px;

		animation: spin 1s linear infinite;
	}

	@keyframes spin {
		to { transform: rotate(360deg); }
	}

	.verify-text strong {
		display: block;
		font-size: 16px;
		color: #0f172a;
		margin-bottom: 5px;
	}

	.verify-text small {
		color: #64748b;
		font-size: 13px;
	}
</style>

<!-- jquery -->
<script src="dataTables/jquery.dataTables.js"></script>
<script src="dataTables/dataTables.tableTools.js"></script>
<script src="dataTables/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
	//data table
	$('#kmAccView').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>

<?php if($check>=1){ ?>
<div class="payment-page">

    <div class="payment-wrapper">

        <!-- ================= LEFT : LIST ================= -->
        <div class="payment-left-panel">

            <div class="payment-header">
                <div class="logo">KM Payment</div>
                <small>Daftar Setoran KO Tonase</small>
            </div>

            <div class="payment-list">
            <?php
                $cashier = executeScalar("select checker_id from checker where checker_code = '".$_SESSION['checker']."'");
                $query   = mysql_query("select * from ko where cashier_id = '".$cashier."' and ko_status = 'Diterima' order by ko_id desc");
				$countQuery = mysql_num_rows(
					mysql_query("
						SELECT ko_id
						FROM ko
						WHERE cashier_id = '".$cashier."'
						AND ko_status = 'Diterima'
					")
				);
				$countKO = $countQuery;
                while($data = mysql_fetch_array($query)) {
                    $cashier_name = executeScalar("select checker_name from checker where checker_id = '".$data['cashier_id']."'");
                    $checker_name = executeScalar("select checker_name from checker where checker_id = '".$data['receiver_id']."'");
            ?>
                <div class="payment-item">

                    <div class="item-id">
                        KO-<?php echo $data['ko_id']; ?>
                        <span><?php echo date("d M Y", strtotime($data['ko_date'])); ?></span>
                    </div>

                    <div class="item-info">
                        <i class="fa fa-user"></i>&nbsp;<strong><?php echo $cashier_name; ?></strong><br>
                        <small>Kasir Penerima</small>
                    </div>

                    <div class="item-info">
                        <i class="fa fa-user"></i>&nbsp;<strong><?php echo $checker_name; ?></strong><br>
                        <small>Checker Penyetor</small>
                    </div>

                    <div class="item-amount">
                        <?php echo format_rupiah($data['ko_total']); ?>
                    </div>

					<div class="payment-badge-ready">
						<i class="fa fa-check-circle"></i> Siap untuk Disetor
					</div>

                </div>
            <?php } ?>
            </div>

        </div>

        <!-- ================= RIGHT : SUMMARY ================= -->
        <div class="payment-right-panel">

            <div class="summary-card">
                <div class="summary-box">
                    <span>ID KM</span>
                    <strong><?php echo $km_id; ?></strong>
                </div>

                <div class="summary-box total">
                    <span>Total Setoran</span>
                    <strong><?php echo format_rupiah($cost); ?></strong>
                </div>
            </div>

            <form action="pay_action2.php?task=acc&type=km"
                  method="POST"
                  id="acckm"
                  target="_blank">

                <div class="form-group">
                    <label>Uang Fisik Yang Diterima</label>
                    <input type="number"
                           name="cash"
                           id="cash"
                           placeholder="Masukkan nominal"
                           required>
                </div>

				<div class="amount-alert" id="amountAlert">
					<i class="fa fa-exclamation-circle"></i>
					Nominal fisik tidak sesuai dengan sistem.<br>
					<small>Selisih maksimal ± Rp 500</small>
				</div>

                <button type="button"
                        id="btn-acc"
                        class="btn-pay"
                        style="display:none;">
                    <i class="fa fa-shield"></i>&nbsp;&nbsp;Verifikasi
                </button>

            </form>

            <!-- <form action="pay_action2.php?task=cancelAll&type=km"
                  method="POST">

                <button type="submit" class="btn-cancel">
                    BATALKAN SEMUA
                </button>

            </form> -->

        </div>
    </div>
</div>
<?php } else { ?>
	<div class="payment-empty">
		<div class="empty-card">
			<img src="img/payment-tonase-notfound.png" alt="Tidak ada setoran">

			<h3>Belum Ada Setoran Tonase</h3>
			<p>
				Saat ini belum ada data setoran tonase yang diterima.<br>
				Silakan cek kembali setelah melakukan pembayaran tonase.
			</p>

			<div class="empty-hint">
				<i class="fa fa-info-circle"></i>
				Data akan muncul otomatis ketika setoran tersedia
			</div>
		</div>
	</div>
<?php } ?>

<!-- ================= LOADING VERIFIKASI ================= -->
<div class="verify-loading" id="verifyLoading">
	<div class="verify-box">
		<div class="verify-spinner"></div>
		<div class="verify-text">
			<strong>Memverifikasi Data</strong>
			<small>Mohon tunggu sebentar...</small>
		</div>
	</div>
</div>

<!-- ================= MODAL KONFIRMASI ACC ================= -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-md modal-dialog-centered" role="document">
		<div class="modal-content modal-secure">

		<!-- Animated Background Lock -->
		<div class="modal-bg-lock">
			<i class="fa fa-lock"></i>
		</div>

		<!-- Header -->
		<div class="modal-header secure-header">
			<div class="secure-icon">
			<i class="fa fa-shield"></i>
			</div>
			<div>
			<h4 class="modal-title">Verifikasi Akhir Setoran</h4>
			<small>Sistem Keuangan Terproteksi</small>
			</div>
			<button type="button" class="secure-close" data-dismiss="modal">
				<i class="fa fa-times"></i>
			</button>
		</div>

		<!-- Body -->
		<div class="modal-body secure-body">

			<div class="secure-alert">
			<i class="fa fa-check-circle"></i>
			Data telah memenuhi syarat untuk penyetoran.
			</div>

			<ul class="secure-checklist">
			<li style="animation-delay:0.2s">
				<i class="fa fa-check-circle"></i>
				Jumlah setoran KO: <strong><?php echo $countKO ?></strong> laporan
			</li>
			<li style="animation-delay:0.4s">
				<i class="fa fa-check-circle"></i>
				Nominal total setoran: <strong id="sysAmount"><?php echo format_rupiah($cost); ?></strong>
			</li>
			<li style="animation-delay:0.6s">
				<i class="fa fa-check-circle"></i>
				Nominal fisik: <strong id="cashAmount">-</strong>
			</li>
			<li style="animation-delay:0.8s">
				<i class="fa fa-check-circle"></i>
				Selisih: <strong id="diffAmount">-</strong>
			</li>
			</ul>

			<div class="verification-progress">
			<div class="progress-bar"></div>
			</div>

		</div>

		<!-- Footer -->
		<div class="modal-footer secure-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">
			<i class="fa fa-times"></i> Batal
			</button>
			<button type="button" class="btn btn-primary btn-confirm" id="confirmSubmit">
			<i class="fa fa-check-circle"></i>&nbsp; Setorkan KM
			</button>
		</div>

		</div>
	</div>
</div>


<!-- Reload Window Tonase-->
<script>
	const SYSTEM_TOTAL = <?php echo (int)$cost; ?>;
	const TOLERANCE    = 500;

	const cashInput = document.getElementById('cash');
	const btnAcc    = document.getElementById('btn-acc');
	const alertBox  = document.getElementById('amountAlert');

	// === INPUT HANDLER (MODIFIKASI TANPA MERUSAK LOGIC) ===
	cashInput.addEventListener('input', function() {

		// jika kosong → sembunyikan semua
		if (this.value.trim() === '') {

			btnAcc.style.display   = "none";
			alertBox.style.display = "none";
			return;

		}

		const receiverValue = Number(this.value);
		const diff = Math.abs(receiverValue - SYSTEM_TOTAL);

		if (diff <= TOLERANCE) {

			btnAcc.style.display   = "block";
			alertBox.style.display = "none";

		} else {

			btnAcc.style.display   = "none";
			alertBox.style.display = "block";

		}

	});

	// === ALERT DIKLIK → RELOAD ===
	alertBox.addEventListener('click', function () {
		postAndReload();
	});

	// === SUBMIT FORM (KODE ASLI KAMU) ===
	document.getElementById('acckm').addEventListener('submit', function(event) {
		event.preventDefault();
		const btnAcc = document.getElementById('btn-acc');

		if (btnAcc && btnAcc.style.display === "block") {
			btnAcc.click();
		}
	});

	// === MODAL KONFIRMASI ===
	document.getElementById('confirmSubmit').addEventListener('click', function () {
		document.getElementById('acckm').submit();
		postAndReload();
	});

	// === POST & RELOAD (TETAP DIPAKAI) ===
	function postAndReload() {
		setTimeout(function() {
			location.reload();
		}, 100);
	}
</script>

<script>
	const systemAmount = <?php echo (int)$cost; ?>;

	$('#confirmationModal').on('show.bs.modal', function () {
		const cash = parseInt(document.getElementById('cash').value || 0);
		const diff = cash - systemAmount;

		document.getElementById('cashAmount').innerText =
			'Rp ' + cash.toLocaleString('id-ID');

		document.getElementById('diffAmount').innerText =
			(diff >= 0 ? '+ ' : '- ') + 'Rp ' + Math.abs(diff).toLocaleString('id-ID');
	});
</script>

<script>
	// TOMBOL ACC → TAMPILKAN LOADING → BARU MODAL
	document.getElementById('btn-acc').addEventListener('click', function () {
		const loading = document.getElementById('verifyLoading');

		// tampilkan loading
		loading.style.display = 'flex';

		// simulasi verifikasi (1.5 detik)
		setTimeout(function () {
			loading.style.display = 'none';

			// buka modal setelah loading
			$('#confirmationModal').modal('show');

		}, 2000); // ubah durasi di sini
	});
</script>

<!-- JS untuk stagger checklist -->
<script>
	$('#confirmationModal').on('shown.bs.modal', function () {
		$('.secure-checklist li').each(function(index) {
			$(this).css('animation-delay', (0.2 * index) + 's');
		});
	});
</script>
