<?php
	include "connection.php";
?>

<style>
	/* ================= WRAPPER ================= */
	.salary-wrapper{
		background:#f4f6fb;
		padding:22px;
		border-radius:16px;
	}

	/* ================= HEADER ================= */
	.luxury-header{
		display:flex;
		justify-content:space-between;
		align-items:center;
		padding:18px 22px;
		border-radius:18px;
		background:linear-gradient(135deg,#ffffff,#f4f6fb);
		box-shadow:0 14px 34px rgba(0,0,0,.08);
		margin-bottom:20px;
	}

	.header-left{
		display:flex;
		align-items:center;
		gap:14px;
	}

	.header-icon{
		width:46px;
		height:46px;
		border-radius:14px;
		background:linear-gradient(135deg,#4f46e5,#3b82f6);
		display:flex;
		align-items:center;
		justify-content:center;
		color:#fff;
		font-size:18px;
		box-shadow:0 8px 20px rgba(79,70,229,.45);
	}

	.header-text h4{
		margin:0;
		font-weight:800;
		color:#1f2937;
	}

	.header-text span{
		font-size:12px;
		color:#6b7280;
	}

	/* ================= SEARCH ================= */
	.header-search{
		position:relative;
		width:260px;
	}

	.header-search input{
		width:100%;
		padding:9px 16px 9px 40px;
		border-radius:30px;
		border:1px solid #e5e7eb;
		font-size:13px;
		background:#fff;
		transition:.25s;
	}

	.header-search i{
		position:absolute;
		left:16px;
		top:50%;
		transform:translateY(-50%);
		color:#9ca3af;
	}

	.header-search input:focus{
		border-color:#4f46e5;
		box-shadow:0 0 0 4px rgba(79,70,229,.14);
		outline:none;
	}

	/* ================= CARD ================= */
	.salary-card{
		display:flex;
		align-items:center;
		gap:18px;
		background:#ffffff;
		border-radius:18px;
		padding:16px 18px;
		margin-bottom:14px;
		box-shadow:0 10px 26px rgba(0,0,0,.06);
		transition:.25s;
	}

	.salary-card:hover{
		transform:translateY(-2px);
		box-shadow:0 18px 40px rgba(0,0,0,.12);
	}

	/* ================= LEFT (NO + DATE) ================= */
	.salary-left{
		min-width:90px;
		text-align:center;
		flex-shrink:0;
	}

	.salary-no{
		font-size:17px;
		font-weight:800;
		color:#4f46e5;
	}

	.salary-date{
		font-size:12px;
		color:#6b7280;
	}

	/* ================= DIVIDER ================= */
	.salary-divider{
		width:1px;
		align-self:stretch;
		background:linear-gradient(
			to bottom,
			transparent,
			#e5e7eb,
			transparent
		);
	}

	/* ================= SUMMARY ================= */
	.salary-summary{
		position:relative;
		flex:1;
		max-width:420px;
		display:flex;
		align-items:center;
		background:linear-gradient(135deg,#ffffff,#f9fafb);
		border-radius:18px;
		padding:14px 18px;
		box-shadow:
			inset 0 0 0 1px #eef0f4,
			0 8px 20px rgba(0,0,0,.04);
		overflow:hidden;
	}

	/* Accent bar */
	.summary-accent{
		position:absolute;
		left:0;
		top:10px;
		bottom:10px;
		width:4px;
		border-radius:6px;
		background:linear-gradient(
			to bottom,
			#6366f1,
			#3b82f6,
			#06b6d4
		);
		box-shadow:0 0 0 4px rgba(99,102,241,.12);
	}

	/* Summary content */
	.summary-content{
		display:flex;
		gap:42px;
		padding-left:12px;
		z-index:2;
	}

	/* ================= BLOCK ================= */
	.salary-block{
		position:relative;
	}

	.label-title{
		font-size:11px;
		letter-spacing:.6px;
		text-transform:uppercase;
		color:#6b7280;
		margin-bottom:6px;
		display:block;
	}

	/* ================= FLOW ================= */
	.salary-flow{
		display:flex;
		align-items:center;
		gap:8px;
	}

	/* ================= BADGE ================= */
	.badge{
		padding:6px 14px;
		border-radius:999px;
		font-size:12px;
		font-weight:700;
		white-space:nowrap;
	}

	.badge-red{
		background:#ef4444;
		color:#fff;
		box-shadow:0 0 0 4px rgba(239,68,68,.12);
	}

	.badge-green{
		background:#22c55e;
		color:#fff;
		box-shadow:0 0 0 4px rgba(34,197,94,.14);
	}

	/* ================= ICON ================= */
	.flow-icon{
		color:#f59e0b;
		font-size:18px;
		opacity:.9;
	}

	/* ================= WATERMARK ================= */
	.summary-icon{
		position:absolute;
		right:18px;
		top:50%;
		transform:translateY(-50%);
		font-size:62px;
		color:#e5e7eb;
		opacity:.35;
		pointer-events:none;
		z-index:1;
	}

	/* ================= ACTION ================= */
	.salary-action{
		min-width:160px;
		flex-shrink:0;
		display:flex;
		flex-direction:column;
		gap:6px;
		margin-left:auto;
		padding-left:14px;
		border-left:1px dashed #e5e7eb;
	}

	/* ================= EMPTY ================= */
	.salary-empty{
		text-align:center;
		padding:50px;
		color:#9ca3af;
	}

	.salary-empty i{
		font-size:42px;
		margin-bottom:12px;
	}

	/* ================= PAGINATION ================= */
	.salary-pagination{
		display:flex;
		justify-content:center;
		align-items:center;
		gap:8px;
		margin-top:25px;
	}

	.page-btn{
		min-width:38px;
		height:38px;
		padding:0 12px;
		border-radius:10px;
		border:1px solid #e5e7eb;
		background:#fff;
		font-weight:600;
		color:#4b5563;
		cursor:pointer;
		transition:.25s ease;
		box-shadow:0 6px 16px rgba(0,0,0,.06);
	}

	.page-btn:hover{
		background:#4f46e5;
		color:#fff;
		border-color:#4f46e5;
	}

	.page-btn.active{
		background:#4f46e5;
		color:#fff;
		border-color:#4f46e5;
		box-shadow:0 10px 25px rgba(79,70,229,.35);
		cursor:default;
	}

	.page-btn.prev,
	.page-btn.next{
		font-weight:700;
	}

	.page-ellipsis{
		padding:0 6px;
		color:#9ca3af;
		font-weight:700;
		user-select:none;
	}

	/* ================= RESPONSIVE ================= */
	@media (max-width: 992px){
		.salary-summary{
			max-width:100%;
		}
	}

	@media (max-width: 768px){
		.salary-card{
			flex-wrap:wrap;
		}

		.salary-action{
			width:100%;
			border-left:none;
			border-top:1px dashed #e5e7eb;
			padding-top:10px;
			margin-left:0;
		}
	}

	/* BOX STICKY */
	.salary-illustration-box{
		position:fixed;
		top:55%;
		transform:translateY(-50%);
		width:32%;
		background:linear-gradient(145deg,#ffffff,#f4f7ff);
		border-radius:22px;
		padding:40px 30px;
		text-align:center;
		box-shadow:
			0 20px 60px rgba(0,0,0,.08),
			inset 0 0 0 1px rgba(255,255,255,.6);
	}

	/* IMAGE */
	.salary-illustration-img{
		width:100%;
		max-width:320px;
		margin-bottom:20px;
		filter:drop-shadow(0 15px 30px rgba(0,0,0,.15));
		animation:float 4s ease-in-out infinite;
	}

	/* FLOAT ANIMATION */
	@keyframes float{
		0%{ transform:translateY(0px); }

		50%{ transform:translateY(-10px); }

		100%{ transform:translateY(0px); }
	}

	/* TITLE */
	.salary-illustration-title{
		font-weight:800;
		font-size:22px;
		color:#1f2937;
		margin-bottom:12px;
	}


	/* DESC */
	.salary-illustration-desc{
		font-size:14px;
		color:#6b7280;
		line-height:1.7;
		margin-bottom:18px;
	}

	/* BADGE */
	.salary-illustration-badge{
		display:inline-block;
		padding:8px 18px;
		border-radius:999px;
		background:linear-gradient(135deg,#4f46e5,#3b82f6);
		color:#fff;
		font-weight:600;
		font-size:13px;
		box-shadow:0 10px 25px rgba(79,70,229,.35);
	}
</style>


<div class="row">
	<div class="col-md-7">
		<div class="alert alert-warning d-flex align-items-center" role="alert" style="margin: 0 0;">
			<i class="fa fa-warning me-2" style="font-size: 18px;"></i>
			<span style="font-size: 14px;">
				Pembagian & pembulatan upah mitra dilakukan setelah <b>pergantian hari</b> agar tidak terjadi kesalahan pembagian!
			</span>
		</div><br>
		<div class="salary-wrapper">
			<div class="salary-header luxury-header">
				<div class="header-left">
					<div class="header-icon">
						<i class="fa fa-database"></i>
					</div>
					<div class="header-text">
						<h4>Data Upah Mitra</h4>
						<span>Ringkasan pembagian & pembulatan upah mitra</span>
					</div>
				</div>

				<div class="header-search">
					<i class="fa fa-search"></i>
					<input type="text" id="salarySearch" placeholder="Cari tanggal / nominal...">
				</div>
			</div>

			<div id="salaryList">

				<?php
					$no		= 0;
					$total	= 0;
					$query 	= mysql_query("	SELECT date(date_check) as dt,
												sum(B001) as SB001, 
												sum(B002) as SB002, 
												sum(B003) as SB003,
												sum(B004) as SB004,
												sum(B005) as SB005, 
												sum(B006) as SB006,
												sum(B007) as SB007, 
												sum(B008) as SB008,
												sum(B009) as SB009,
												sum(B010) as SB010,
												sum(B011) as SB011,
												sum(B012) as SB012,
												sum(B013) as SB013,
												sum(B014) as SB014,
												sum(B015) as SB015,
												sum(S001) as SS001, 
												sum(S002) as SS002, 
												sum(S003) as SS003,
												sum(S004) as SS004,
												sum(S005) as SS005, 
												sum(S006) as SS006,
												sum(S007) as SS007, 
												sum(S008) as SS008,
												sum(S009) as SS009,
												sum(S010) as SS010,
												sum(S011) as SS011,
												sum(S012) as SS012,
												sum(S013) as SS013,
												sum(S014) as SS014,
												sum(S015) as SS015, 
												sum(M01) as SM01,
												sum(M02) as SM02,
												sum(M03) as SM03,
												sum(M04) as SM04,
												sum(B001_save) as SB001s, 
												sum(B002_save) as SB002s, 
												sum(B003_save) as SB003s,
												sum(B004_save) as SB004s,
												sum(B005_save) as SB005s, 
												sum(B006_save) as SB006s,
												sum(B007_save) as SB007s, 
												sum(B008_save) as SB008s,
												sum(B009_save) as SB009s,
												sum(B010_save) as SB010s,
												sum(B011_save) as SB011s,
												sum(B012_save) as SB012s,
												sum(B013_save) as SB013s,
												sum(B014_save) as SB014s,
												sum(B015_save) as SB015s,
												sum(S001_save) as SS001s, 
												sum(S002_save) as SS002s, 
												sum(S003_save) as SS003s,
												sum(S004_save) as SS004s,
												sum(S005_save) as SS005s, 
												sum(S006_save) as SS006s,
												sum(S007_save) as SS007s, 
												sum(S008_save) as SS008s,
												sum(S009_save) as SS009s,
												sum(S010_save) as SS010s,
												sum(S011_save) as SS011s,
												sum(S012_save) as SS012s,
												sum(S013_save) as SS013s,
												sum(S014_save) as SS014s,
												sum(S015_save) as SS015s,
												sum(M01_save) as SM01s,
												sum(M02_save) as SM02s,
												sum(M03_save) as SM03s,
												sum(M04_save) as SM04s
											FROM laborer_fee lf
											JOIN weighing_mitra wm ON wm.weighing_id = lf.weighing_id 
											where wm.weighing_sts != 'Batal' and laborer_fee_sts = 'Baru'
											group by date(date_check) desc limit 30");
					while($r	= mysql_fetch_array($query)) { 
						$no++;
						$share	= executeScalar("	select (sum(fee_B001)+
															sum(fee_B002)+
															sum(fee_B003)+
															sum(fee_B004)+
															sum(fee_B005)+
															sum(fee_B006)+
															sum(fee_B007)+
															sum(fee_B008)+
															sum(fee_B009)+
															sum(fee_B010)+
															sum(fee_B011)+
															sum(fee_B012)+
															sum(fee_B013)+
															sum(fee_B014)+
															sum(fee_B015)+
															sum(fee_S001)+
															sum(fee_S002)+
															sum(fee_S003)+
															sum(fee_S004)+
															sum(fee_S005)+
															sum(fee_S006)+
															sum(fee_S007)+
															sum(fee_S008)+
															sum(fee_S009)+
															sum(fee_S010)+
															sum(fee_S011)+
															sum(fee_S012)+
															sum(fee_S013)+
															sum(fee_S014)+
															sum(fee_S015)+
															sum(fee_M01)+
															sum(fee_M02)+
															sum(fee_M03)+
															sum(fee_M04)) 
													from laborer_fee2 where fee_date = '".$r['dt']."'");
						$save	= executeScalar("	select (sum(save_B001)+
															sum(save_B002)+
															sum(save_B003)+
															sum(save_B004)+
															sum(save_B005)+
															sum(save_B006)+
															sum(save_B007)+
															sum(save_B008)+
															sum(save_B009)+
															sum(save_B010)+
															sum(save_B011)+
															sum(save_B012)+
															sum(save_B013)+
															sum(save_B014)+
															sum(save_B015)+
															sum(save_S001)+
															sum(save_S002)+
															sum(save_S003)+
															sum(save_S004)+
															sum(save_S005)+
															sum(save_S006)+
															sum(save_S007)+
															sum(save_S008)+
															sum(save_S009)+
															sum(save_S010)+
															sum(save_S011)+
															sum(save_S012)+
															sum(save_S013)+
															sum(save_S014)+
															sum(save_S015)+
															sum(save_M01)+
															sum(save_M02)+
															sum(save_M03)+
															sum(save_M04)) 
													from laborer_fee2 where fee_date = '".$r['dt']."'");
						$total1	= 	$r['SB001']+
									$r['SB002']+
									$r['SB003']+
									$r['SB004']+
									$r['SB005']+
									$r['SB006']+
									$r['SB007']+
									$r['SB008']+
									$r['SB009']+
									$r['SB010']+
									$r['SB011']+
									$r['SB012']+
									$r['SB013']+
									$r['SB014']+
									$r['SB015']+
									$r['SS001']+
									$r['SS002']+
									$r['SS003']+
									$r['SS004']+
									$r['SS005']+
									$r['SS006']+
									$r['SS007']+
									$r['SS008']+
									$r['SS009']+
									$r['SS010']+
									$r['SS011']+
									$r['SS012']+
									$r['SS013']+
									$r['SS014']+
									$r['SS015']+
									$r['SM01']+
									$r['SM02']+
									$r['SM03']+
									$r['SM04'];
						$total2	= 	$r['SB001s']+
									$r['SB002s']+
									$r['SB003s']+
									$r['SB004s']+
									$r['SB005s']+
									$r['SB006s']+
									$r['SB007s']+
									$r['SB008s']+
									$r['SB009s']+
									$r['SB010s']+
									$r['SB011s']+
									$r['SB012s']+
									$r['SB013s']+
									$r['SB014s']+
									$r['SB015s']+
									$r['SS001s']+
									$r['SS002s']+
									$r['SS003s']+
									$r['SS004s']+
									$r['SS005s']+
									$r['SS006s']+
									$r['SS007s']+
									$r['SS008s']+
									$r['SS009s']+
									$r['SS010s']+
									$r['SS011s']+
									$r['SS012s']+
									$r['SS013s']+
									$r['SS014s']+
									$r['SS015s']+
									$r['SM01s']+
									$r['SM02s']+
									$r['SM03s']+
									$r['SM04s'];
				?>

				<div class="salary-card salary-item">
					<div class="salary-left">
						<div class="salary-no"><?php echo $no; ?></div>
						<div class="salary-date">
							<?php echo date("d M Y", strtotime($r['dt'])); ?>
						</div>
					</div>

					<div class="salary-divider"></div>

					<div class="salary-summary">
						<div class="summary-accent"></div>
						
						<div class="summary-content">
							<div class="salary-block">
								<span class="label-title">Upah Dibagi</span>
								<div class="salary-flow">
									<span class="badge badge-red"><?php echo format_rupiah($total1); ?></span>
									<i class="fa fa-angle-double-right flow-icon"></i>
									<span class="badge badge-green"><?php echo format_rupiah($share); ?></span>
								</div>
							</div>

							<!-- <div class="salary-block">
								<span class="label-title">Upah Disimpan</span>
								<div class="salary-flow">
									<span class="badge badge-red"><?php echo format_rupiah($total2); ?></span>
									<i class="fa fa-angle-double-right flow-icon"></i>
									<span class="badge badge-green"><?php echo format_rupiah($save); ?></span>
								</div>
							</div> -->
						</div>

						<div class="summary-icon">
							<i class="fa fa-wallet"></i>
						</div>
					</div>

					<div class="salary-action">
						<?php
							if($share==null){
								echo "<a href='pay_mitra_salary2.php?date=".$r['dt']."' class='btn btn-sm btn-outline btn-primary btn-block'>
										<i class='fa fa-money'></i> Bayar
									</a>";
							}else{
								echo "<a href='pay_mitra_salary2.php?date=".$r['dt']."' class='btn btn-sm btn-outline btn-primary btn-block'>
										<i class='fa fa-edit'></i> Revisi
									</a>";
								echo "<a href='htmltopdf/mitra_dibagi.php?date=".$r['dt']."' target='_blank' class='btn btn-sm btn-outline btn-warning btn-block'>
										<i class='fa fa-file-pdf-o'></i> Print PDF
									</a>";
								echo "<a href='exportExcel/exportMitraDibagi.php?date=".$r['dt']."' class='btn btn-sm btn-outline btn-success btn-block'>
										<i class='fa fa-file-excel-o'></i> Export Excel
									</a>";
							}
						?>
					</div>

				</div>
				<?php } ?>

			</div>

			<div id="salaryEmpty" class="salary-empty" style="display:none;">
				<i class="fa fa-search"></i>
				<p>Data tidak ditemukan</p>
			</div>

			<div class="salary-pagination" id="salaryPagination"></div>
		</div>
	</div>

	<div class="col-md-5">
		<div class="salary-illustration-box">
			<img src="img/illustration-salary.png" class="salary-illustration-img">

			<h3 class="salary-illustration-title">
				Pembulatan Upah Mitra
			</h3>

			<p class="salary-illustration-desc">
				Atur dan pantau pembagian upah mitra dengan perhitungan yang akurat dan pencatatan yang terorganisir dalam satu sistem.
			</p>

			<div class="salary-illustration-badge">
				<i class="fa fa-shield"></i> Aman & Akurat
			</div>

		</div>
	</div>
</div>


<script>
const itemsPerPage = 5;
let currentPage = 1;
const maxVisiblePages = 5;

function getFilteredItems(){
	return $('.salary-item').filter(function(){
		return !$(this).hasClass('filtered-out');
	});
}

function renderPagination() {
	let items = getFilteredItems();
	let totalItems = items.length;
	let totalPages = Math.ceil(totalItems / itemsPerPage);

	if (totalItems === 0) {
		$('.salary-item').hide(); 
		$('#salaryEmpty').show();
		$('#salaryPagination').hide();
		return;
	}

	$('#salaryEmpty').hide();
	$('#salaryPagination').show();

	if (currentPage > totalPages) currentPage = totalPages;
	if (currentPage < 1) currentPage = 1;

	// HIDE ALL
	$('.salary-item').hide();

	// SHOW CURRENT PAGE
	items
		.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage)
		.show();

	// ===== BUILD PAGINATION =====
	let html = '';

	// PREV
	if (currentPage > 1) {
		html += `<button class="page-btn prev" data-page="${currentPage - 1}">Prev</button>`;
	}

	// PAGE WINDOW
	let start = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
	let end = start + maxVisiblePages - 1;

	if (end > totalPages) {
		end = totalPages;
		start = Math.max(1, end - maxVisiblePages + 1);
	}

	if (start > 1) {
		html += `<button class="page-btn" data-page="1">1</button>`;
		if (start > 2) html += `<span class="page-ellipsis">…</span>`;
	}

	for (let i = start; i <= end; i++) {
		if (i === currentPage) {
			html += `<button class="page-btn active">${i}</button>`;
		} else {
			html += `<button class="page-btn" data-page="${i}">${i}</button>`;
		}
	}

	if (end < totalPages) {
		if (end < totalPages - 1) html += `<span class="page-ellipsis">…</span>`;
		html += `<button class="page-btn" data-page="${totalPages}">${totalPages}</button>`;
	}

	// NEXT
	if (currentPage < totalPages) {
		html += `<button class="page-btn next" data-page="${currentPage + 1}">Next</button>`;
	}

	$('#salaryPagination').html(html);
}

// CLICK PAGINATION
$(document).on('click', '.page-btn', function(){
	let page = $(this).data('page');
	if (!page) return;
	currentPage = page;
	renderPagination();
});

// SEARCH
$('#salarySearch').on('keyup', function(){
	let val = $(this).val().toLowerCase();
	currentPage = 1;

	$('.salary-item').each(function(){
		let match = $(this).text().toLowerCase().includes(val);
		$(this).toggleClass('filtered-out', !match);
	});

	renderPagination();
});

// INIT
renderPagination();
</script>


