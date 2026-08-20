<?php
	include "../connection.php";

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
		background: linear-gradient(180deg,#5148E5,#2563eb);
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
		color: #5148E5;
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

	/* Search & Pagination */
	.payment-toolbar{
		display:flex;
		justify-content:space-between;
		align-items:center;
		gap:14px;
		margin-bottom:18px;
		padding:12px 14px;
		background:linear-gradient(135deg, #f8f8ff, #efeeff);
		border-radius:16px;
		box-shadow:0 10px 28px rgba(79,70,229,.12);
	}

	/* SEARCH */
	.search-box{
		position:relative;
		max-width:340px;
		width:100%;
	}

	.search-box i{
		position:absolute;
		top:50%;
		left:14px;
		transform:translateY(-50%);
		color:#000;
		font-size:14px;
	}

	.search-box input{
		width:100%;
		padding:12px 16px 12px 42px;
		border-radius:14px;
		border:1px solid #dbe0ff;
		font-size:14px;
		background:#fff;
		transition:.25s ease;
		box-shadow:inset 0 0 0 transparent;
	}

	.search-box input:focus{
		outline:none;
		border-color:#4f46e5;
		box-shadow:0 0 0 3px rgba(79,70,229,.18);
	}

	/* LIMIT */
	.limit-box select{
		padding:10px 14px;
		border-radius:14px;
		border:1px solid #dbe0ff;
		background:#fff;
		font-weight:600;
		color:#000;
		cursor:pointer;
		transition:.25s ease;
	}

	.limit-box select:hover{
		background:#eef2ff;
	}

	.pagination-wrapper{
		display:flex;
		justify-content:center;
		align-items:center;
		gap:8px;
		margin:26px 0 10px;
		padding:10px 14px;
		background:#f8f9ff;
		border-radius:16px;
		box-shadow:0 10px 30px rgba(0,0,0,.08);
	}

	/* PAGE BUTTON */
	.page-link{
		min-width:42px;
		height:42px;
		display:flex;
		align-items:center;
		justify-content:center;
		border-radius:12px;
		border:1px solid #dbe0ff;
		background:#fff;
		color:#5148E5;
		font-weight:600;
		font-size:14px;
		text-decoration:none;
		transition:.25s ease;
		box-shadow:0 6px 14px rgba(79,70,229,.15);
	}

	.page-link:hover{
		background:#5148E5;
		color:#fff;
		transform:translateY(-2px);
		box-shadow:0 10px 22px rgba(79,70,229,.35);
	}

	/* ACTIVE */
	.page-link.active{
		background:#5148E5;
		color:#fff;
		border-color:#5148E5;
		box-shadow:0 12px 26px rgba(79,70,229,.45);
		transform:scale(1.05);
	}

	.loading{
		text-align:center;
		padding:40px 0;
		color:#4f46e5;
		font-weight:600;
	}

	.loading:after{
		content:'';
		display:inline-block;
		width:26px;
		height:26px;
		border:3px solid #c7d2fe;
		border-top-color:#4f46e5;
		border-radius:50%;
		animation:spin 1s linear infinite;
		margin-left:10px;
	}

	@keyframes spin{
		to{transform:rotate(360deg);}
	}

	/* Empty Search */
	.empty-state{
		text-align:center;
		padding:50px 20px;
		background:linear-gradient(135deg,#f8f9ff,#eef2ff);
		border-radius:20px;
		box-shadow:0 14px 34px rgba(79,70,229,.15);
		color:#4f46e5;
	}

	.empty-icon{
		width:70px;
		height:70px;
		margin:0 auto 16px;
		border-radius:50%;
		background:#e0e7ff;
		display:flex;
		align-items:center;
		justify-content:center;
		font-size:26px;
	}

	.empty-state h4{
		margin:0 0 8px;
		font-weight:700;
		color:#1e1b4b;
	}

	.empty-state p{
		margin:0;
		font-size:14px;
		color:#555;
	}

	/* ================= HEADER PENAMPUNGAN ================= */
	.payment-header{
		display:flex;
		justify-content:space-between;
		align-items:center;
		background: #dfe4e8;
		padding:18px 22px;
		border-radius:18px;
		box-shadow:0 14px 32px rgba(0,0,0,.08);
		margin-bottom:14px;
	}

	.payment-header-left{
		display:flex;
		align-items:center;
		gap:16px;
	}

	.payment-header-icon{
		width:52px;
		height:52px;
		border-radius:16px;
		background:#5148E5;
		display:flex;
		align-items:center;
		justify-content:center;
		color:#fff;
		font-size:22px;
	}

	.payment-header-left h3{
		margin:0;
		font-size:17px;
		font-weight:800;
		color:#111827;
	}

	.payment-header-left span{
		font-size:12px;
		color:#6b7280;
	}

	.payment-header-right{
		display:flex;
		align-items:center;
	}

	.payment-badge{
		display:inline-flex;
		align-items:center;
		gap:6px;
		background:#fff;
		color:#5148E5;
		font-size:12px;
		font-weight:600;
		padding:8px 14px;
		border-radius:999px;
	}

	/* Info Bar */
	.payment-info-bar{
		display:flex;
		justify-content:flex-start;
		margin:4px 0 10px;
	}

	/* DARK CARD */
	#data-info{
		display:none;
		padding:8px 16px;
		font-size:12.5px;
		color:#e5e7eb;
		border-radius:12px;

		background:linear-gradient(
			145deg,
			#0f172a,
			#020617
		);

		border:1px solid rgba(255,255,255,.08);
		box-shadow:
			0 8px 24px rgba(0,0,0,.6),
			inset 0 1px 0 rgba(255,255,255,.05);

		letter-spacing:.25px;
	}

	/* ANGKA */
	#data-info b{
		color:#ffffff;
		font-weight:600;
	}

	/* ================= MOBILE ================= */
	@media (max-width: 767px){
		.payment-frame{
			padding:14px;
		}
		.payment-header{
			flex-direction:column;
			align-items:flex-start;
			gap:12px;
			padding:14px;
		}
		.payment-header-left{
			gap:12px;
		}
		.payment-header-right{
			width:100%;
		}
		.payment-toolbar{
			flex-direction:column;
			align-items:stretch;
			gap:10px;
		}
		.search-box{
			max-width:none;
		}
		.payment-card-tonase{
			flex-direction:column;
			align-items:stretch;
			gap:10px;
			padding:16px 16px 16px 20px;
		}
		.payment-left,
		.payment-mid,
		.payment-right{
			width:100%;
			text-align:left;
		}
		.pagination-wrapper{
			flex-wrap:wrap;
			gap:6px;
		}
		.summary-tile strong{
			font-size:22px;
		}
	}
</style>


<div class="payment-frame">
	<!-- ================= HEADER PENAMPUNGAN ================= -->
	<div class="payment-header">
		<div class="payment-header-left">
			<div class="payment-header-icon">
				<i class="fa fa-database"></i>
			</div>
			<div>
				<h3>Penampungan Pembayaran Tonase</h3>
				<span>Daftar transaksi tonase yang menunggu proses pembayaran</span>
			</div>
		</div>

		<div class="payment-header-right">
			<span class="payment-badge">
				<i class="fa fa-clock-o"></i> Menunggu Dibayarkan
			</span>
		</div>
	</div>

	<div class="payment-toolbar">
		<div class="limit-box">
			<select id="limit">
				<option value="5" selected>5</option>
				<option value="10">10</option>
				<option value="20">20</option>
				<option value="50">50</option>
			</select>
		</div>

		<div class="search-box">
			<i class="fa fa-search"></i>
			<input type="text" id="searchACC" placeholder="Cari Nota / Nopol / Pedagang">
		</div>
	</div><br>

	<!-- INFO BAR -->
	<div class="payment-info-bar">
		<span id="data-info">Memuat data...</span>
	</div><br>

	<!-- ================= LIST PENAMPUNGAN ================= -->
	<div id="frame-tonase">
		<div class="payment-list" id="paymentData">
			<!-- Load By Ajax -->
		</div>
		<div id="pagination">
			<!-- Load By Ajax -->
		</div>
	</div>


	<!-- ================= SUMMARY ================= -->
	<?php if($check >= 1){ ?>
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
		<!-- <form action="pay_action.php?task=acc&type=ko"
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
		</form> -->
		<!-- ===== END FORM ACC ===== -->

		<!-- ===== FORM BATALKAN SEMUA (TERPISAH) ===== -->
		<!-- <div class="row action-row">
			<div class="col-md-12">
				<form action="pay_action.php?task=cancelAll&type=ko"
					method="POST"
					>

					<button type="submit" class="btn btn-danger btn-lg btn-block">
						<i class="fa fa-times-circle"></i> BATALKAN SEMUA
					</button>
				</form>
			</div>
		</div> -->
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

<!-- AJAX PAY SEARCH & PAGINATION -->
<script>
	let page = 1;

	function loadData(){
		let search = $('#searchACC').val();
		let limit  = $('#limit').val();

		$('#data-info').html('Memuat data...');
		$('#paymentData').html('<div class="loading">Memuat data</div>');

		$.ajax({
			url: 'pay_acc_table_ajax.php',
			type: 'POST',
			data: {
				search: search,
				page: page,
				limit: limit
			},
			success:function(res){
				let data = JSON.parse(res);
				$('#paymentData').html(data.html);
				$('#pagination').html(data.pagination);

				if(data.info && data.info.trim() !== ''){
					$('#data-info').html(data.info).fadeIn(150);
				}else{
					$('#data-info').fadeOut(150);
				}
			}
		});
	}

	$(document).ready(function(){
		loadData();

		$('#searchACC').on('keyup', function(){
			page = 1;
			loadData();
		});

		$('#limit').on('change', function(){
			page = 1;
			loadData();
		});

		$('#frame-tonase').on('click','.page-link',function(e){
			e.preventDefault();
			page = $(this).data('page');
			loadData();
		})
	});
</script>


