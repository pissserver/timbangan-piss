<?php
	session_start();
	include "../connection.php";
	include "components/role_session.php";

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
?>

<style>
	.card-box {
		position: relative;
		background: #fff;
		border-radius: 10px;
		padding: 18px;
		box-shadow: 0 0 10px rgba(0,0,0,0.12);
		margin-bottom: 20px;
		border-left: 5px solid #007bff;
		transition: 0.2s;
	}
	.card-box:hover {
		transform: translateY(-3px);
		box-shadow: 0 6px 18px rgba(0,0,0,0.18);
	}

	.toggle-row {
		display: flex;
		align-items: center;
		margin-top: 10px;
	}

	.toggle-col {
		width: 60px; /* lebih besar agar tidak turun */
		display: flex;
		justify-content: center;
	}

	.btn-col {
		flex: 1;
		margin-left: 10px;
	}

	.toggle-wrapper {
		display: flex;
		align-items: center;
		margin: 6px 0;
	}

	/* Hide checkbox */
	.toggle-switch {
		display: none;
	}

	/* Toggle background */
	.toggle-label {
		width: 50px;
		height: 26px;
		background: #ccc;
		border-radius: 50px;
		position: relative;
		cursor: pointer;
		transition: background 0.25s ease-in-out;
		transform: scale(1.2);
	}

	/* Bulatan + ikon */
	.toggle-icon {
		width: 22px;
		height: 22px;
		background: white;
		border-radius: 50%;
		position: absolute;
		top: 2px;
		left: 2px;
		font-size: 14px;
		line-height: 22px;
		text-align: center;
		color: #dc3545; /* merah default */
		transition: transform 0.25s, background 0.25s, color 0.25s;
	}

	/* ON → IYA */
	.toggle-switch:checked + .toggle-label {
		background: #28a745;
	}

	/* Bulatan bergerak + ikon centang */
	.toggle-switch:checked + .toggle-label .toggle-icon {
		transform: translateX(24px);
		color: #28a745;   /* warna ikon sama dengan background */
		background: white;
	}

	/* Ikon default: silang */
	.toggle-label .toggle-icon::before {
		content: "✖";
	}

	/* Jika aktif → ganti ke centang */
	.toggle-switch:checked + .toggle-label .toggle-icon::before {
		content: "✔";
	}

	/* Disabled selected input */
	.no-open[data-readonly="true"] {
		pointer-events: none;
		background-color: #e9ecef;
	}

	/* SEARCH */
	.search-wrapper {
		position: relative;
	}

	.search-wrapper i {
		position: absolute;
		top: 50%;
		left: 15px;
		transform: translateY(-50%);
		color: #aaa;
	}

	.search-wrapper input {
		padding-left: 40px;
		border-radius: 30px;
		height: 42px;
	}

	/* EMPTY STATE */
	.empty-box {
		text-align: center;
		background: #f9f9f9;
		padding: 40px;
		border-radius: 12px;
		color: #777;
	}

	.empty-box i {
		font-size: 40px;
		margin-bottom: 10px;
		color: #ccc;
	}

	.empty-box h4 {
		margin-bottom: 5px;
		font-weight: bold;
	}

	.btn-centeng-modern {
		width: 100%;
		height: 45px;
		border: none;
		border-radius: 30px;
		background: linear-gradient(135deg,#667eea,#764ba2);
		color: white;
		font-weight: 600;
		box-shadow: 0 4px 15px rgba(0,0,0,0.2);
		transition: 0.3s;
		position: relative;
		overflow: hidden;
	}

	.btn-centeng-modern i {
		margin-right: 8px;
	}

	.btn-centeng-modern:hover {
		transform: translateY(-3px);
		box-shadow: 0 8px 25px rgba(0,0,0,0.3);
	}

	.btn-centeng-modern:before {
		content: "";
		position: absolute;
		left: -100%;
		top: 0;
		width: 100%;
		height: 100%;
		background: linear-gradient(90deg,transparent,rgba(255,255,255,0.4),transparent);
		transition: 0.5s;
	}

	.btn-centeng-modern:hover:before {
		left: 100%;
	}

	.modal-centeng-modern{
		border:none;
		border-radius:20px;
		overflow:hidden;
		background:linear-gradient(135deg,#eef2ff,#ffffff);
	}

	.header-centeng{
		background:linear-gradient(135deg,#667eea,#764ba2);
		color:white;
		border:none;
		padding:18px;
	}

	.header-centeng .close{
		color:white;
		opacity:1;
		font-size:22px;
	}

	.body-centeng{
		max-height:75vh;
		overflow-y:auto;
		padding: 20px;
	}

	/* scrollbar modern */
	.body-centeng::-webkit-scrollbar{
		width:8px;
	}

	.body-centeng::-webkit-scrollbar-track{
		background:#f1f1f1;
		border-radius:10px;
	}

	.body-centeng::-webkit-scrollbar-thumb{
		background:linear-gradient(180deg,#667eea,#764ba2);
		border-radius:10px;
	}

	.body-centeng::-webkit-scrollbar-thumb:hover{
		background:linear-gradient(180deg,#5a6fd8,#6a4190);
	}

	.card-summary-centeng{
		display:flex;
		align-items:center;
		background:linear-gradient(135deg,#667eea,#764ba2);
		color:#fff;
		padding:18px;
		border-radius:18px;
		margin-bottom:20px;
		box-shadow:0 6px 20px rgba(0,0,0,.2);
	}
	.card-summary-centeng .icon{
		font-size:28px;
		margin-right:15px;
	}
	.card-summary-centeng .periode{
		font-size:16px;
		font-weight:bold;
		margin-bottom:4px;
	}
	.card-summary-centeng .row-total{
		display:flex;
		gap:20px;
	}
	.card-summary-centeng .belum span{
		color:#ffb3b3;
		font-weight:bold;
	}
	.card-summary-centeng .sudah span{
		color:#8cffc1;
		font-weight:bold;
	}

	.card-centeng{
		display:flex;
		align-items:center;
		background:white;
		border-radius:15px;
		padding:15px;
		margin-bottom:15px;
		box-shadow:0 8px 25px rgba(0,0,0,0.08);
		transition:0.3s;
		position:relative;
		overflow:hidden;
	}

	.card-centeng:hover{
		transform:translateY(-5px);
		box-shadow:0 15px 35px rgba(0,0,0,0.15);
	}

	.card-centeng:before{
		content:"";
		position:absolute;
		left:-100%;
		top:0;
		width:100%;
		height:100%;
		background:linear-gradient(90deg,transparent,rgba(102,126,234,0.2),transparent);
		transition:0.5s;
	}

	.card-centeng:hover:before{
		left:100%;
	}

	.icon-retail{
		width:45px;
		height:45px;
		border-radius:12px;
		background:linear-gradient(135deg,#36d1dc,#5b86e5);
		display:flex;
		align-items:center;
		justify-content:center;
		color:white;
		font-size:18px;
		margin-right:12px;
	}

	.content{
		flex:1;
	}

	.nama{
		font-weight:bold;
		font-size:13px;
		color:#333;
	}

	.lapak{
		font-size:11px;
		color:#888;
		margin-top:3px;
	}

	.right{
		text-align:right;
	}

	.tagihan{
		font-weight:bold;
		color:#667eea;
		font-size:13px;
	}

	.tanggal{
		font-size:11px;
		color:#999;
		margin-top:3px;
	}

	/* ================= LOADING CENTENG ENTERPRISE ================= */
	.loading-centeng{
		padding:30px 10px 40px;
		animation:fadeInCenteng 0.3s ease;
	}

	@keyframes fadeInCenteng{
		from{
			opacity:0;
			transform:translateY(10px);
		}
		to{
			opacity:1;
			transform:translateY(0);
		}
	}

	/* spinner */
	.spinner-centeng{
		width:55px;
		height:55px;
		margin:10px auto 25px;
		border-radius:50%;
		background:conic-gradient(
			#4e73df,
			#36b9cc,
			#1cc88a,
			#4e73df
		);
		animation:spinCenteng 0.8s linear infinite;
		position:relative;
	}

	.spinner-centeng:before{
		content:'';
		position:absolute;
		top:6px;
		left:6px;
		right:6px;
		bottom:6px;
		background:#fff;
		border-radius:50%;
	}

	@keyframes spinCenteng{
		100%{
			transform:rotate(360deg);
		}
	}

	/* skeleton */
	.skeleton-centeng{
		height:80px;
		border-radius:14px;
		margin-bottom:15px;
		background:linear-gradient(
			90deg,
			#f1f3f5,
			#e2e6ea,
			#f1f3f5
		);
		background-size:200% 100%;
		animation:skeletonCenteng 1.2s infinite;
	}

	@keyframes skeletonCenteng{
		0%{
			background-position:200% 0;
		}

		100%{
			background-position:-200% 0;
		}
	}

	/* text */
	.loading-text-centeng{
		text-align:center;
		color:#6c757d;
		font-weight:500;
		margin-bottom:15px;
		font-size:14px;
	}

	.search-centeng-wrap{
		position:relative;
		display:inline-block;
		margin-right:10px;
		margin-top:5px;
	}

	.search-centeng{
		border:none;
		background:#ffffff;
		padding:7px 35px 7px 35px;
		border-radius:20px;
		outline:none;
		width:200px;
		transition:0.3s;
		font-size:13px;
		color:#333 !important;
		box-shadow:0 2px 6px rgba(0,0,0,0.08);
	}

	.search-centeng::placeholder{
		color:#999 !important;
	}

	.search-centeng:focus{
		width:260px;
		background:#fff;
		color:#333 !important;
		box-shadow:0 0 0 3px rgba(78,115,223,0.15);
	}


	.icon-search{
		position:absolute;
		left:12px;
		top:8px;
		color:#999;
		font-size:13px;
	}

	.icon-clear{
		position:absolute;
		right:12px;
		top:8px;
		color:#bbb;
		cursor:pointer;
		display:none;
	}

	.icon-clear:hover{
		color:#dc3545;
	}

	.empty-centeng{
		text-align:center;
		padding:60px 20px;
		color:#999;
	}

	.empty-centeng .icon{
		font-size:50px;
		color:#ddd;
		margin-bottom:10px;
	}

	.empty-centeng .title{
		font-size:18px;
		font-weight:600;
		color:#666;
	}

	.empty-centeng .desc{
		font-size:13px;
		color:#aaa;
	}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="icon" href="../img/icon.png">
    <link rel="shortcut icon" href="../img/icon.png" />
	<!-- CSS Search dropdown -->
	<link href="../css/plugins/chosen/chosen.css" rel="stylesheet" />
    <title>Retail | PISS</title>
	<?php echo headScript(); ?>
</head>
<body>
    <div id="wrapper">
        <?php include 'components/sidebar.php'; ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
					<br /><br><br><br>
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-plus fa-fw"></i> Tambah Pedagang Retail
                        </div>
                        <div class="panel-body">
							<div class="row" style="margin-bottom: -20px;">
								<form action="weighing_action.php?task=createMerchantRetail" class="form-horizontal" id="formWeighingIn" name = "formWeighingIn" method="POST">
								<fieldset>
									<div class="col-md-4">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Pedagang</label>
											<select class="form-control chzn-select" name="merchant" id="merchant" required>
												<option value="">- Pilih Pedagang -</option>
												<?php 
													$sql 	= mysql_query("	select 	merchant_id, 
																					merchant_name, 
																					merchant_nick
																			from 	merchant 
																			where 	merchant_status = 'Aktif' and 
																					merchant_type != 'Buah' 
																			order by 	merchant_nick, 
																						merchant_name");
													while($d1 = mysql_fetch_array($sql)){
														$lpk 	= executeScalar("	select 	l.lapak_name 
																					from 	rent r, 
																							lapak l 
																					where 	r.lapak_id = l.lapak_id and 
																							r.merchant_id = '".$d1['merchant_id']."' 
																							and (r.rent_sts != 'Batal' and r.rent_sts2 not in ('Batal', 'Pindah')) limit 1");
														$nick	= $d1['merchant_nick'];
														if($nick!=''){
															$nick = $nick.' / ';
														}else{
															$nick = '';
														}
												?>
													<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.($lpk ? $lpk : 'Tidak Ada Lapak');?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Barang</label>
											<select class="form-control chzn-select" name="goods_id" id="goods" required>
												<option value="">- Pilih Barang -</option>
												<?php 
													$sql2 		= mysql_query("select goods_id, goods_name from goods where goods_id in('79', '41') order by goods_name");
													while($d2 	= mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['goods_id'];?>"><?php echo $d2['goods_name'];?></option>
												<?php } ?>
											</select>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group" style="padding:10px; margin-top:15px;">
											<center><button type="submit" class="btn btn-primary btn-block">Tambahkan &nbsp;<i class="fa fa-floppy-o"></i></button></center>
										</div>
									</div>
								</fieldset>
								</form>
							</div>
                        </div>
                    </div>
                </div>
            </div>
			
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <i class="fa fa-users fa-fw"></i> Data Pedagang Retail
                        </div>
                        <div class="panel-body">
							<div class="row" style="padding:10px;">
								<div class="col-md-10">
									<div class="search-wrapper">
										<i class="fa fa-search"></i>
										<input type="text"
											id="searchMerchant"
											class="form-control"
											placeholder="Cari nama pedagang, atau lapak...">
									</div>
								</div>

								<div class="col-md-2 text-right">
									<?php 
										$totalRetail = executeScalar("SELECT COUNT(*) FROM merchant_retail");
									?>
									<button class="btn-centeng-modern"
											data-toggle="modal"
											data-target="#modalCenteng">
										<i class="fa fa-users"></i>&nbsp; <?php echo $totalRetail; ?>&nbsp; Retail
									</button>
								</div>
							</div>

							<div id="emptyState" style="display:none; padding:30px;">
								<div class="empty-box">
									<i class="fa fa-search-minus"></i>
									<h4>Data tidak ditemukan</h4>
									<p>Coba gunakan kata kunci lain</p>
								</div>
							</div>

							<form action="../htmltopdf/nota_tagihan_retail.php?task=printAll" method="POST" target="_blank">
								<div class="row" style="padding:10px;">

								<?php 
								$no = 0;
								$query = mysql_query("
									SELECT r.retail_id,
										r.print_status,
										r.print_date,
										m.merchant_name,
										m.merchant_id,
										m.merchant_nick
									FROM merchant_retail r
									JOIN merchant m ON r.merchant_id = m.merchant_id
									ORDER BY r.retail_id ASC
								");

								$alreadyAll = 0;

								while($d = mysql_fetch_array($query)) {
									$no++;

									// query lapak
									$string	= "";
									$counter= 0;
									$sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
											where r.lapak_id = l.lapak_id and r.merchant_id = '".$d['merchant_id']."' and
											(r.rent_sts != 'Batal' and r.rent_sts2 not in ('Batal', 'Pindah')) order by l.lapak_name");
									while($row=mysql_fetch_array($sql2)){
										if($counter!=0){
											$string = ", ".$string;
										}
										$string = $row['lapak_name'].$string;
										$counter++;
									}
									$lpk 	= "".$string;
									
									$nick	= $d['merchant_nick'];
									if($nick!=''){
										$nick = $nick.' / ';
									}else{
										$nick = '';
									}

									// current date
									$year  = date('Y');
									$month = date('m');
									$day   = (int) date('d');

									// jumlah hari dalam bulan
									$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

									// tentukan periode
									if ($day <= 7) {
										$periodStart = 1;
										$periodEnd   = 7;
									} elseif ($day <= 14) {
										$periodStart = 8;
										$periodEnd   = 14;
									} elseif ($day <= 21) {
										$periodStart = 15;
										$periodEnd   = 21;
									} elseif ($day <= 28) {
										$periodStart = 22;
										$periodEnd   = 28;
									} else {
										$periodStart = 29;
										$periodEnd   = $daysInMonth;
									}

									// tanggal range
									$periodStartDate = $year.'-'.$month.'-'.str_pad($periodStart, 2, '0', STR_PAD_LEFT);
									$periodEndDate   = $year.'-'.$month.'-'.str_pad($periodEnd, 2, '0', STR_PAD_LEFT);

									$already = executeScalar("
										SELECT COUNT(*) 
										FROM merchant_retail 
										WHERE merchant_id = '".$d['merchant_id']."'
										AND print_status = 'iya'
										AND DATE(print_date) BETWEEN '".$periodStartDate."' AND '".$periodEndDate."'
									");

									if ($already > 0) {
										$alreadyAll = 1;
									}
								?>

								<div class="col-md-3 merchant-card" data-search="<?= strtolower($d['merchant_name'].' '.$d['merchant_nick'].' '.$lpk); ?>">
									<div class="card-box">
										<div class="label-body" style="position:absolute; top:-15px; right:-20px;">
											<span style="
												display:flex;
												align-items:center;
												justify-content:center;
												width:45px;
												height:45px;
												background:#f0ad4e;
												color:#fff;
												border-radius:50%;
												font-size:16px;
												font-weight:bold;
												box-shadow:0 2px 6px rgba(0,0,0,0.3);
											">
												<?php echo $no; ?>
											</span>
										</div>

										<input type="hidden" name="merchant[]" value="<?php echo $d['merchant_id']; ?>">
										<input type="text" class="form-control"
											value="<?php echo $d['merchant_name']; ?>" 
											style="font-weight:bold; font-size:14px; text-align:center;"
											readonly>

										<input type="text" class="form-control"
											value="<?php echo $lpk; ?>" 
											style="font-weight: bold; font-size:12px; text-align:center; margin-top:10px; margin-bottom: 10px;"
											readonly>
										
										<div class="form-group" style="margin-top:10px; margin-bottom: 10px;">
											<select class="form-control no-open"
												name="goods[]"
												id="goods_<?= $d['merchant_id']; ?>"
												style="text-align:center; display: none;"
												required
												<?php echo ($already > 0) ? 'data-readonly="true"' : ''; ?>>

											`	<?php
												// goods yang pernah dipilih merchant
												$selectedGoods = executeScalar("
													SELECT goods_id 
													FROM merchant_retail 
													WHERE merchant_id = '".$d['merchant_id']."' 
													LIMIT 1
												");

												// daftar goods
												$sql2 = mysql_query("
													SELECT goods_id, goods_name 
													FROM goods 
													WHERE goods_id IN ('79','41') 
													ORDER BY goods_name
												");

												while ($d2 = mysql_fetch_array($sql2)) {
													$selected = ($d2['goods_id'] == $selectedGoods) ? 'selected' : '';
												?>
													<option value="<?= $d2['goods_id']; ?>" <?= $selected; ?>>
														<?= $d2['goods_name']; ?>
													</option>
												<?php } ?>`
											</select>
										</div>

										<div class="toggle-row">
											<div class="toggle-col">
												<div class="toggle-wrapper" style="margin-top: 10px;">
													<input type="checkbox"
														class="toggle-switch"
														id="cetak_<?= $d['merchant_id'] ?>"
														name="cetak[<?= $d['merchant_id'] ?>]"
														value="iya"
														<?= ($d['print_status'] == 'iya') ? 'checked' : '' ?>>

													<label for="cetak_<?= $d['merchant_id'] ?>" class="toggle-label">
														<span class="toggle-icon"></span>
													</label>
												</div>
											</div>

											<div class="btn-col">
												<?php if ($already > 0) { ?>
													<button type="button"
														class="btn btn-success btn-block btn-sm"
														style="opacity:0.9; cursor:not-allowed; border-radius:20px;"
														disabled>
														<?= date('d', strtotime($periodStartDate)) . ' - ' . date('d M', strtotime($periodEndDate)); ?> Terbuat&nbsp;&nbsp;<i class="fa fa-check-circle"></i>
													</button>
												<?php } else { ?>
													<a  href="javascript:void(0)"
														target="_blank"
														class="btn btn-primary btn-block btn-sm"
														style="border-radius:20px;"
														onclick="printNota('<?= $d['merchant_id']; ?>'); return false;">
														Buat Tagihan&nbsp;&nbsp;<i class="fa fa-print"></i>
													</a>
												<?php } ?>
											</div>
										</div>

										<button style="margin-top: 10px;" type="button" 
											class="btn btn-danger btn-block"
											data-toggle="modal" 
											data-target="#deleteModal"
											data-nama="<?php echo $d['merchant_name']; ?>" 
											data-lapak="<?php echo $lpk; ?>"
											data-id="<?php echo $d['retail_id']; ?>">
											Hapus Dari Retail &nbsp;<i class="fa fa-trash"></i>
										</button>
									</div>
								</div>

								<?php } ?>
								</div>

								<div class="form-group" style="padding:10px; margin-top:-10px;">
									<label>Kasir</label>
									<select class="form-control" name="checker" id="checker" required <?php echo $already > 0 ? 'disabled' : ''?>>
										<option value="<?php echo $already > 0 ? 'sudah terbuat' : ''?>" <?php echo $already > 0 ? 'selected' : ''?> ><?php echo $already > 0 ? 'Semua Data Sudah Terbuat' : '- Pilih Kasir -' ?></option>
										<?php 
											$sql 		= mysql_query("select checker_id, checker_name from checker 
											where checker_role = 'Cashier' and checker_sts = 'Aktif' and status_aktif = 1 order by checker_name");
											while($data = mysql_fetch_array($sql))
										{?>
											<option value="<?php echo $data['checker_id'];?>"><?php echo $data['checker_name'];?></option>
										<?php } ?>
									</select>
								</div>

								<?php if ($alreadyAll > 0) { ?>
									<div class="form-group" style="padding:10px; margin-bottom: 0px;">
										<center><div id="btnCetakAll" class="btn btn-success btn-block" name="print_all">Periode <?= date('d', strtotime($periodStartDate)) . ' - ' . date('d M', strtotime($periodEndDate)); ?> Terbuat &nbsp;<i class="fa fa-check-circle"></i></div></center>
									</div>
								<?php } else { ?>
									<div class="form-group" style="padding:10px; margin-bottom: 0px;">
										<center><button type="submit" id="btnCetakAll" class="btn btn-primary btn-block" name="print_all" onclick="return postAndReload('all')">Buat Tagihan Retail Keseluruhan &nbsp;<i class="fa fa-print"></i></button></center>
									</div>
								<?php } ?>
							</form>
						</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<!-- Modal List -->
	<div class="modal fade" id="modalCenteng">
		<div class="modal-dialog modal-lg">
			<div class="modal-content modal-modern">
				<div class="modal-header modal-header-modern">
					<button type="button" class="close" data-dismiss="modal" style="margin-top: 10px;">&times;</button>
					<h4 style="display:inline-block;">
						<i class="fa fa-users"></i>&nbsp; Daftar Pedagang Centeng
					</h4>

					<button id="btnFilterTanggal"
						class="btn btn-xs btn-warning pull-right"
						style="margin-top: 5px; margin-right:30px;border-radius:20px;padding:6px 15px;">
						<i class="fa fa-calendar"></i>&nbsp; Filter
						<!-- Input Range -->
						<input type="text" id="filterTanggal" style="position:absolute;opacity:0;width:1px;height:1px;border:0;top:40px;right:90px;">
					</button>

					<div class="search-centeng-wrap pull-right">

						<i class="fa fa-search icon-search"></i>

						<input type="text"
							id="searchCenteng"
							class="search-centeng"
							placeholder="Cari pedagang...">

						<i class="fa fa-times icon-clear" id="clearSearch"></i>

					</div>
				</div>

				<div class="modal-body body-centeng">
					<!-- File data_modal_retail.php -->
				</div>
			</div>
		</div>
	</div>

	<!-- Modal konfirmasi hapus -->
	<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				
				<div class="modal-header bg-primary text-white">
					<h5 class="modal-title"><b>KONFIRMASI HAPUS</b></h5>
					<button type="button" class="close text-white" data-dismiss="modal" style="margin-top: -20px;">
						<span>&times;</span>
					</button>
				</div>

				<div class="modal-body">

					<p>Anda yakin ingin menghapus pedagang berikut dari Retail?</p>

					<!-- BOX NAMA PEDAGANG -->
					<input id="namaPedagangBox"
						type="text"
						class="form-control p-3 mb-3"
						style="border:2px solid #dc3545; border-radius:8px; background:#fff5f5; 
							font-size:16px; font-weight:bold; text-align:center;"
						readonly
						value="">

				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
					
					<a id="confirmDeleteBtn" href="#" class="btn btn-danger">
						Hapus
					</a>
				</div>

			</div>
		</div>
	</div>



	<!-- Ajax data modal -->
	<script>
		$(document).ready(function(){

			let start='<?= $periodStartDate ?>';
			let end='<?= $periodEndDate ?>';

			$('#filterTanggal').daterangepicker({
				startDate:moment(start),
				endDate:moment(end),

				parentEl: '#modalCenteng',
				autoApply: true,
				opens: 'center',
				drops: 'down',

				locale:{
					format:'YYYY-MM-DD'
				}

			},function(s,e){
				start=s.format('YYYY-MM-DD');
				end=e.format('YYYY-MM-DD');

				loadData(start,end);
			});

			$('#btnFilterTanggal').click(function(){
				$('#filterTanggal').data('daterangepicker').show();
			});

			/* LOAD SAAT MODAL DIBUKA */
			$('#modalCenteng').on('shown.bs.modal', function(){
				loadData(start,end);
			});

			function loadData(start,end){
				$('.body-centeng').html(

					'<div class="loading-centeng">'+
						'<div class="spinner-centeng"></div>'+

						'<div class="loading-text-centeng">'+
							'Memuat data pedagang...'+
						'</div>'+

						'<div class="row">'+
							'<div class="col-md-6">'+
								'<div class="skeleton-centeng"></div>'+
							'</div>'+

							'<div class="col-md-6">'+
								'<div class="skeleton-centeng"></div>'+
							'</div>'+

							'<div class="col-md-6">'+
								'<div class="skeleton-centeng"></div>'+
							'</div>'+

							'<div class="col-md-6">'+
								'<div class="skeleton-centeng"></div>'+
							'</div>'+
						'</div>'+
					'</div>'
				);

				$.ajax({
					url:'ajax_modal_retail.php',
					type:'POST',
					data:{
						start:start,
						end:end
					},

					success:function(res){
						$('.body-centeng').html(res);
					}
				});
			}
		});
	</script>

	<!-- Search Modal -->
	<script>
		$('#searchCenteng').on('keyup',function(){
			let value=$(this).val().toLowerCase();
			let total=0;

			// tombol clear
			if(value!='')
				$('#clearSearch').show();
			else
				$('#clearSearch').hide();

			// filter data
			$('.body-centeng .col-md-6').each(function(){
				let found=$(this).text().toLowerCase().indexOf(value)>-1;
				$(this).toggle(found);
				if(found) total++;
			});

			// empty state
			if(total==0){
				$('#emptySearch').fadeIn(200);
			}else{
				$('#emptySearch').hide();
			}
		});

		$('#clearSearch').click(function(){
			$('#searchCenteng').val('').keyup().focus();
		});
	</script>

    <!-- Single Print -->
	<script>
		function printNota(merchantId) {

			const select = document.getElementById('goods_' + merchantId);

			if (!select) {
				alert('Goods tidak ditemukan');
				return false;
			}

			const goods = select.value;

			if (!goods) {
				alert('Pilih goods terlebih dahulu');
				return false;
			}

			const url = `../htmltopdf/nota_tagihan_retail.php?task=singlePrint&merchant_id=${merchantId}&goods=${goods}`;
			window.open(url, '_blank');

			postAndReload('single'); // reload setelah print
		}
	</script>
	
	<!-- Radio Button -->
	<script>
		document.querySelectorAll('.toggle-switch').forEach(sw => {
			sw.addEventListener('change', function() {
				let text = this.closest('.toggle-wrapper').querySelector('.toggle-text');
				text.textContent = this.checked ? 'Iya' : 'Tidak';
			});
		});
	</script>
	
	<!-- Display none btnCetalAll -->
	<script>
		function checkAllToggles() {
			let toggles = document.querySelectorAll('.toggle-switch');
			let btn = document.getElementById('btnCetakAll');

			let hasIya = false;

			toggles.forEach(t => {
				if (t.checked) {
					hasIya = true;
				}
			});

			btn.style.display = hasIya ? 'block' : 'none';
		}

		// Saat page load
		document.addEventListener("DOMContentLoaded", checkAllToggles);

		// Saat toggle berubah
		document.addEventListener("change", function(e) {
			if (e.target.classList.contains("toggle-switch")) {
				checkAllToggles();
			}
		});
	</script>


    <!-- Script delete merchant retail -->
	<script>
		$(document).ready(function (){
			$('#deleteModal').on('show.bs.modal', function (event) {
				var button   = $(event.relatedTarget); 
				var nama     = button.data('nama'); 
				var retailId = button.data('id');
				var lapak    = button.data('lapak');

				$('#namaPedagangBox').val(nama + ' (' + lapak + ')');
				$('#confirmDeleteBtn').attr("href", 
					"weighing_action.php?task=deleteMerchantRetail&retail_id=" + retailId
				);
			});
		});
	</script>

	<!-- Post & reload -->
	<script>
		function postAndReload(type) {

			// hanya cek kalau cetak ALL
			if (type === 'all') {
				var checker = document.getElementById('checker').value;

				if (checker === '') {
					alert('Silakan pilih kasir terlebih dahulu!');
					return false; // ⛔ STOP
				}
			}

			// reload tetap jalan
			setTimeout(function () {
				location.reload();
			}, 100);

			return true;
		}
	</script>

	<!-- Search -->
	<script>
		document.getElementById('searchMerchant').addEventListener('keyup', function () {
			let keyword = this.value.toLowerCase();
			let cards = document.querySelectorAll('.merchant-card');
			let found = 0;

			cards.forEach(function(card) {
				let text = card.getAttribute('data-search');

				if (text.includes(keyword)) {
					card.style.display = '';
					found++;
				} else {
					card.style.display = 'none';
				}
			});

			// EMPTY STATE
			document.getElementById('emptyState').style.display = (found === 0) ? 'block' : 'none';
		});
	</script>

	<script>
		$(document).ready(function(){
			$("#notifArea").html(alertLoadProcess);
			$('#Area1').load("weighing_table.php",function(){clearNotif();});
			setTimeout('$("html").removeClass("js")',100); 
			
			// Auto Refresh Page
			/*$.ajaxSetup({ cache: false }); // This part addresses an IE bug.  without it, IE will only load the first number and will never refresh
			setInterval(function() {
				$('#weight_in').load('random.php');
			}, 1000);*/
			
			//Modal Password
			$('#modalPassword').on('shown.bs.modal', function (e) {
				$("#notifArea").html(alertLoadProcess);
				$("#data_password").load("password.php",function(){clearNotif();});
			})
		});
		function clearNotif(){$("#notifArea").html("");}
    </script>
    <!-- jQuery -->
    <script src="../js/jquery.js"></script>
	<!-- Date Range Picker -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
	<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="../js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="../js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="../js/sb-admin-2.js"></script>
	<script src="../js/plugins/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
    	$(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
    </script>
	<!-- Sweet Alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js"></script>
	<?php
		// Menampilkan SweetAlert jika session success ada dan bernilai true
		if (isset($_SESSION['success'])) {
			$title = $_SESSION['title'];
			$message = $_SESSION['message'];
			$icon = $_SESSION['success'] ? 'success' : 'error';
			echo "<script>
				Swal.fire({
					title: '$title',
					text: '$message',
					icon: '$icon'
				});
			</script>";
			unset($_SESSION['success']);
			unset($_SESSION['message']);
		}
	?>
</body>
</html>