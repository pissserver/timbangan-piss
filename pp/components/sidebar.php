<?php
	function isActive($pages) {
		// return basename($_SERVER['PHP_SELF']) == $pages ? 'active' : '';
		return in_array(basename($_SERVER['PHP_SELF']), $pages) ? 'active' : '';
	}

	function isParentActive($pages) {
		return in_array(basename($_SERVER['PHP_SELF']), $pages) ? 'active' : '';
	}

	function greeting() {
		$hour = date('H');
	
		if ($hour >= 5 && $hour < 12) {
			return "Selamat pagi, ";
		} elseif ($hour >= 12 && $hour < 18) {
			return "Selamat siang, ";
		} elseif ($hour >= 18 && $hour < 21) {
			return "Selamat sore, ";
		} else {
			return "Selamat malam, ";
		}
	}

	$greetingMessage = greeting();
	$photoUrl	= executeScalar("select photo from admin where username = '".$_SESSION['username']."'");

	$countRevision = executeScalar("SELECT COUNT(revision_id) FROM revision WHERE status1 IS NULL");

	// Trophy Query
	$monthNames = [
		'01' => 'Januari',
		'02' => 'Februari',
		'03' => 'Maret',
		'04' => 'April',
		'05' => 'Mei',
		'06' => 'Juni',
		'07' => 'Juli',
		'08' => 'Agustus',
		'09' => 'September',
		'10' => 'Oktober',
		'11' => 'November',
		'12' => 'Desember'
	];

	$month = date('m', strtotime('-1 month'));
	$year = date('Y', strtotime('-1 month'));
	$lastMonthName = $monthNames[$month] . " " . $year;
	$sql = "
		SELECT 
			c.checker_name, 
			c.photo, 
			COUNT(w.weighing_id) AS total_transactions
		FROM checker c
		JOIN weighing w 
			ON w.checker_id = c.checker_id
		WHERE 
			c.status_aktif = '1'
			AND c.checker_role = 'Cashier'
			AND w.weighing_sts = 'Lunas'
			AND DATE_FORMAT(w.date_pay, '%m') = '".$month."'
			AND DATE_FORMAT(w.date_pay, '%Y') = '".$year."'
		GROUP BY c.checker_id
		ORDER BY total_transactions DESC
		LIMIT 1
	";


	$result = mysql_query($sql);
	$data = mysql_fetch_assoc($result);

	$sql2 = "SELECT 
				c.checker_id,
				c.checker_name, 
				c.photo,
				COUNT(k.ko_id) AS total_ko
			FROM checker c
			JOIN ko k ON k.receiver_id = c.checker_id
			WHERE status_aktif = '1' 
			AND checker_role = 'Checker'
			AND DATE_FORMAT(k.ko_date, '%m') = '".$month."'
			AND DATE_FORMAT(k.ko_date, '%Y') = '".$year."'
			GROUP BY c.checker_id, c.checker_name
			ORDER BY total_ko DESC
			LIMIT 1";
	$result2 = mysql_query($sql2);
	$data2 = mysql_fetch_assoc($result2);
?>

<style>
	    .silhouette {
        font-weight: bold;
        font-size: 20px;
        background: linear-gradient(120deg, #ffffff 30%, #ec9841 50%, #ffffff 70%);
        background-size: 200% auto;
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
        animation: silhouetteMove 5s linear infinite;
    }

    @keyframes silhouetteMove {
        0% { background-position: 200% center; }
        100% { background-position: -200% center; }
    }

	.notification-wrapper {
		position: relative;
	}
	
	.notification-revision {
        display: block;
        position: absolute;
        right: 10px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 2px 5px;
        font-size: 10px;
    }

	.notification-revision-hidden {
		display: none;
	}

	/* Image Profile */
	.nav-profile-img {
		width: 20px;
		height: 20px;
		border-radius: 50%;
		object-fit: cover;
		object-position: center;
		border: 2px solid #fff;
		box-shadow: 0 0 5px rgba(255,255,255,0.7);
		transition: transform .2s ease-in-out;
		vertical-align: middle;
	}

	/* Floating Trophy */
	.floating-trophy {
		position: fixed;
		bottom: 25px;
		right: 25px;
		width: 65px;
		height: 65px;
		background: #ffcc00;
		color: white;
		border-radius: 50%;
		font-size: 30px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		box-shadow: 0 5px 20px rgba(0,0,0,0.3);
		z-index: 9999;
		transition: 0.25s ease;
	}

	/* Hover effect */
	.floating-trophy:hover {
		transform: scale(1.15);
	}

	/* Small floating animation (optional) */
	.floating-trophy {
		animation: floatAnim 2.5s ease-in-out infinite;
	}

	@keyframes floatAnim {
		0%, 100% { transform: translateY(0); }
		50% { transform: translateY(-8px); }
	}

	/* Kotak modal elegan */
	.modal-congrats-box {
		border-radius: 18px !important;
		padding: 25px;
		text-align: center;
		background: linear-gradient(to bottom right, #ffffff, #d8e4f2ff);
		border: 2px solid #e0e7ff;
		box-shadow: 0 0 25px rgba(0,0,0,0.15);
		position: relative;
		overflow: hidden;
	}

	/* Foto */
	.modal-congrats-photo {
		width: 130px;
		height: 130px;
		border-radius: 50%;
		border: 4px solid #ffd369;
		box-shadow: 0 0 12px rgba(255, 193, 7, 0.5);
		margin-top: 15px;
	}

	/* Judul */
	.modal-congrats-title {
		font-weight: bold;
		font-size: 24px;
		color: #333;
		margin-top: 20px;
	}

	/* Subjudul */
	.modal-congrats-sub {
		font-size: 16px;
		color: #555;
		margin-top: 10px;
	}

	/* Detail */
	.modal-congrats-detail {
		font-size: 16px;
		color: #444;
		margin-top: 10px;
	}

	/* Dekorasi atas */
	.modal-decor-top {
		width: 100%;
		height: 8px;
		background: linear-gradient(to right, #ffb200, #ffe38f, #ffb200);
		border-radius: 0 0 10px 10px;
		margin-top: -25px;
	}

	/* Dekorasi bawah */
	.modal-decor-bottom {
		width: 100%;
		height: 8px;
		background: linear-gradient(to right, #6ca8ff, #d5e6ff, #6ca8ff);
		border-radius: 10px 10px 0 0;
		margin-bottom: -25px;
	}

	
	/* Carousel Modal */
	.slide {
		display: none;
		opacity: 0;
		transition: opacity 0.5s ease;
	}

	.slide.active {
		display: block;
		opacity: 1;
	}

	/* Tombol Next Prev */
	.slide-btn {
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		background: rgba(0,0,0,0.4);
		border: none;
		color: #fff;
		width: 35px;
		height: 35px;
		border-radius: 50%;
		cursor: pointer;
		font-size: 18px;
		line-height: 35px;
		text-align: center;
	}

	.prev-btn { left: 15px; }
	.next-btn { right: 15px; }

</style>

<nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0; background-color: #428bca;">
    <div class="navbar-header bg-primary">
    	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="dashboard.php" style="color: #ffffff;"><i class="fa fa-balance-scale fa-fw silhouette"></i> <b class="silhouette">TIMBANGAN PISS</b></a>
    </div>

	<ul class="nav navbar-top-links navbar-right">
		<?php include 'components/clock.php'; ?>&nbsp;&nbsp;
		<?php include 'components/offcanvas_note.php'; ?>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" 
			style="color: #ffffff !important; background-color: transparent !important;"
			onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'" 
			onmouseout="this.style.backgroundColor='transparent'">
				<!-- <i class="fa fa-user fa-fw" style="color: #ffffff;"></i> -->
				<span style="color: #ffffff;"><?php echo $greetingMessage; ?></span>
				<b style="text-transform: capitalize; color: #ffffff;"><?php echo 'PP - ' . $name; ?>&nbsp;</b>
				<img id="previewPhoto" class="nav-profile-img" src="<?php echo $photoUrl ? '../../uploads/' . $photoUrl : '../../uploads/profile-default.png'; ?>">&nbsp; 
				<i id="dropdownIcon" class="fa fa-angle-down" style="color: #ffffff;"></i>
			</a>
			<ul class="dropdown-menu dropdown-user" style="width: 200px; margin-right: 10px; margin-top: 10px; border: 3px solid #ffffff; background-color: #428bca; border-radius: 10px;">
				<li>
					<a href="profile.php" 
					style="color: #ffffff; font-size: 15px; display: block; padding: 10px; text-decoration: none;"
					onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'; this.style.color='#ffffff';"
					onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ffffff';">
						<div class="name" style="display: inline-block; padding-right: 100px; padding-left: 10px;">
							Profil
						</div>
						<div class="icon" style="display: inline-block;">
							<i class="fa fa-user fa-fw" style="color: #ffffff; font-size: 15px;"></i>
						</div>
					</a>
				</li>
			<!-- <li>
					<a data-toggle="modal" data-target="#modalPassword" href="#" 
					style="color: #ffffff; font-size: 15px; display: block; padding: 10px; text-decoration: none;"
					onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'; this.style.color='#ffffff';"
					onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ffffff';">
						<div class="name" style="display: inline-block; padding-right: 30px; padding-left: 10px;">
							Ganti Password 
						</div>
						<div class="icon" style="display: inline-block;">
							<i class="fa fa-lock fa-fw" style="color: #ffffff; font-size: 15px;"></i>
						</div>
					</a>
				</li> -->
				<li class="divider"></li>
				<li>
					<a data-toggle="modal" data-target="#modalLogout" href="#" 
					style="color: #ffffff; font-size: 15px; display: block; padding: 10px; text-decoration: none;"
					onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'; this.style.color='#ffffff';"
					onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ffffff';">
						<div class="name" style="display: inline-block; padding-right: 95px; padding-left: 10px;">
							Keluar
						</div>
						<div class="icon" style="display: inline-block;">
							<i class="fa fa-sign-out fa-fw" style="color: #ffffff; font-size: 15px;"></i>
						</div>
					</a>
				</li>
			</ul>
		</li>
	</ul>

			<!-- Modal Password -->
			<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
				<div class="modal-dialog" >
					<div class="modal-content">
						<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
							<h4 id="header_password"><i class="fa fa-lock fa-fw"></i>&nbsp;Ganti Password</h4>
							<button style="margin-top: -35px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div id="data_password" style="margin-bottom: 0px;">
								<div class="alert alert-info" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
							</div>
							<input type="hidden" id="value_id" />
						</div>
					</div>
				</div>
			</div>

			<!-- Modal Logout -->
			<div class="modal fade" id="modalLogout" tabindex="-1" role="dialog" aria-labelledby="modalLogoutLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
							<h5 class="modal-title" id="modalLogoutLabel"><b>KONFIRMASI KELUAR</b></h5>
							<button style="margin-top: -20px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<p>Apakah Anda yakin ingin keluar?</p>
						</div>
						<div class="modal-footer d-flex justify-content-between">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
							<a href="../logout.php" class="btn btn-primary">Keluar</a>
						</div>
					</div>
				</div>
			</div>
        
            <div class="navbar-default sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse" style="overflow-y: auto; max-height: 100vh;">
                    <ul class="nav" id="side-menu" style="margin-bottom: 70px;">
						<li><a class="<?= isActive(['dashboard.php']); ?>" href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard </a></li>
						
						<!-- <li class="<?= isParentActive(['index.php', 'index2.php', 'index3.php', 'index4.php', 'check.php', 'check2.php', 'check_edit.php', 'index5.php', 'listing.php']); ?>"> 
							<a href="#"><i class="fa fa-balance-scale fa-fw"></i> Timbangan<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['index.php', 'index2.php', 'index3.php']); ?>" href="index.php"><i class="fa fa-truck fa-fw"></i> Masuk / Keluar / Kir </a></li>
								<li><a class="<?= isActive(['index4.php']); ?>" href="index4.php"><i class="fa fa-truck fa-fw"></i> Retail </a></li>
								<li><a class="<?= isActive(['check2.php']); ?>" href="check2.php"><i class="fa fa-square-o fa-fw"></i> Data Belum Dicek </a></li>
								<li><a class="<?= isActive(['check.php', 'check_edit.php']); ?>" href="check.php"><i class="fa fa-check-square-o fa-fw"></i> Data Sudah Dicek </a></li>
								<li><a class="<?= isActive(['index5.php', 'listing.php']); ?>" href="index5.php"><i class="fa fa-search fa-fw"></i> Listing Nota </a></li>
							</ul>
						</li> -->

						<li class="<?= isParentActive(['cancel1.php', 'cancel2.php', 'cancel3.php']); ?>"> 
							<a href="#"><i class="fa fa-close fa-fw"></i> Pembatalan<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['cancel1.php']); ?>" href="cancel1.php"><i class="fa fa-truck fa-fw"></i> Kendaraan Masuk </a></li>
								<li><a class="<?= isActive(['cancel2.php']); ?>" href="cancel2.php"><i class="fa fa-truck fa-fw"></i> Kendaraan Keluar </a></li>
								<li><a class="<?= isActive(['cancel3.php']); ?>" href="cancel3.php"><i class="fa fa-money fa-fw"></i> Pembayaran </a></li>
							</ul>
						</li>

						<!-- <li class="<?= isParentActive(['edit1.php', 'edit2.php', 'edit3.php', 'edit4.php', 'edit5.php']); ?>"> 
							<a href="#"><i class="fa fa-edit fa-fw"></i> Pengeditan<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['edit1.php']); ?>" href="edit1.php"><i class="fa fa-arrow-circle-down fa-fw"></i> Berat Masuk </a></li>
								<li><a class="<?= isActive(['edit2.php']); ?>" href="edit2.php"><i class="fa fa-arrow-circle-up fa-fw"></i> Berat Keluar </a></li>
								<li><a class="<?= isActive(['edit3.php']); ?>" href="edit3.php"><i class="fa fa-pie-chart fa-fw"></i> Berat Bersih </a></li>
								<li><a class="<?= isActive(['edit5.php']); ?>" href="edit5.php"><i class="fa fa-clock-o fa-fw"></i> Jam & Tanggal </a></li>
								<li><a class="<?= isActive(['edit4.php']); ?>" href="edit4.php"><i class="fa fa-file fa-fw"></i> Status </a></li>
							</ul>
						</li> -->

						<li class="<?= isParentActive(['pay_tonase_mitra.php', 'pay_acc_tonase_mitra.php', 'pay.php', 'pay_km_mitra.php', 'pay_mitra.php', 'pay_mitra2.php']); ?>">
							<a href="#"><i class="fa fa-credit-card fa-fw"></i> ACC & Pembulatan<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['pay.php']); ?>" href="pay.php"><i class="fa fa-check-square fa-fw"></i> ACC KM </a></li>
								<!-- <li><a class="<?= isActive(['pay_km_mitra.php']); ?>" href="pay_km_mitra.php"><i class="fa fa-users fa-fw"></i> ACC Setoran KM Mitra </a></li> -->
								<li><a class="<?= isActive(['pay_mitra.php', 'pay_mitra2.php']); ?>" href="pay_mitra.php"><i class="fa fa-mail-forward fa-fw"></i></i> Pembulatan Upah Mitra </a></li>
							</ul>
						</li>

						<li class="<?= isParentActive(['report_transaction.php', 'report_outstanding.php', 'report_cancel.php', 'report_tonase.php', 'report_tonase_merchant.php', 'report_tonase_block.php', 'report_tonase_comodity.php', 'report_merchant_comodity.php', 'report_revenue.php', 'report_revenue_merchant.php', 'report_revenue_unpaid.php', 'report_revenue_unpaid_merchant.php', 'report_revenue_mitra.php', 'report_revenue_merchant_mitra.php', 'report_revenue_unpaid_mitra.php', 'report_revenue_unpaid_merchant_mitra.php', 'report_revenue_tonase_mitra.php', 'report_revenue_merchant_tonase_mitra.php', 'report_revenue_unpaid_tonase_mitra.php', 'report_revenue_unpaid_merchant_tonase_mitra.php', 'report_kokm.php', 'report_kokm_mitra.php', 'report_outstanding_ko.php', 'report_outstanding_ko_mitra.php', 'report_mitra.php', 'report_mitra_year.php']);?>">
							<a href="#"><i class="fa fa-clipboard fa-fw"></i> 
							Laporan
							<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">

								<li class="<?= isParentActive(['report_transaction.php', 'report_outstanding.php', 'report_cancel.php']); ?>"><a href="#"><i class="fa fa-balance-scale fa-fw"></i> Transaksi Timbang<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_transaction.php']); ?>" href="report_transaction.php"><i class="fa fa-adjust fa-fw"></i> Timbang Harian </a></li>
										<li>
											<a class="<?= isActive(['report_outstanding.php']); ?>" href="report_outstanding.php" style="display:flex; align-items:center;">
												<i class="fa fa-clock-o fa-fw"></i>
												<span style="margin-left:8px;">Outstanding Timbang</span>
											</a>
										</li>
										<li><a class="<?= isActive(['report_cancel.php']); ?>" href="report_cancel.php"><i class="fa fa-close fa-fw"></i> Timbang Batal </a></li>
									</ul>
								</li>

								<li class="<?= isParentActive(['report_revenue.php', 'report_revenue_merchant.php', 'report_revenue_unpaid.php', 'report_revenue_unpaid_merchant.php']); ?>"><a href="#"><i class="fa fa-money fa-fw"></i> Tagihan Tonase<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_revenue.php']); ?>" href="report_revenue.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar </a></li> 
										<li>
											<a class="<?= isActive(['report_revenue_merchant.php']); ?>" 
											href="report_revenue_merchant.php"
											style="display:flex; align-items:center;">
											
												<i class="fa fa-check-square fa-fw"></i>
												<span style="margin-left:8px;">Terbayar Per Pedagang</span>

											</a>
										</li>
										<li><a class="<?= isActive(['report_revenue_unpaid.php']); ?>" href="report_revenue_unpaid.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar </a></li>
										<li>
											<a class="<?= isActive(['report_revenue_unpaid_merchant.php']); ?>" 
											href="report_revenue_unpaid_merchant.php"
											style="display:flex; align-items:center;">

												<i class="fa fa-clock-o fa-fw"></i>
												<span style="margin-left:8px;">Belum Terbayar Per Pedagang</span>

											</a>
										</li>
									</ul>
								</li>

								<li class="<?= isParentActive(['report_revenue_mitra.php', 'report_revenue_merchant_mitra.php', 'report_revenue_unpaid_mitra.php', 'report_revenue_unpaid_merchant_mitra.php']); ?>"><a href="#"><i class="fa fa-money fa-fw"></i> Tagihan Mitra<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_revenue_mitra.php']); ?>" href="report_revenue_mitra.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar </a></li> 
										<li>
											<a class="<?= isActive(['report_revenue_merchant_mitra.php']); ?>"
											href="report_revenue_merchant_mitra.php"
											style="display:flex; align-items:center;">

												<i class="fa fa-check-square fa-fw"></i>
												<span style="margin-left:8px;">Terbayar Per Pedagang</span>

											</a>
										</li>
										<li><a class="<?= isActive(['report_revenue_unpaid_mitra.php']); ?>" href="report_revenue_unpaid_mitra.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar </a></li>
										<li>
											<a class="<?= isActive(['report_revenue_unpaid_merchant_mitra.php']); ?>"
											href="report_revenue_unpaid_merchant_mitra.php"
											style="display:flex; align-items:center;">

												<i class="fa fa-clock-o fa-fw"></i>
												<span style="margin-left:8px;">Belum Terbayar Per Pedagang</span>

											</a>
										</li>
									</ul>
								</li>

								<!-- <li class="<?= isParentActive(['report_revenue_tonase_mitra.php', 'report_revenue_merchant_tonase_mitra.php', 'report_revenue_unpaid_tonase_mitra.php', 'report_revenue_unpaid_merchant_tonase_mitra.php']); ?>"><a href="#"><i class="fa fa-money fa-fw"></i> Tagihan Tonase & Mitra <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_revenue_tonase_mitra.php']); ?>" href="report_revenue_tonase_mitra.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar </a></li> 
										<li><a class="<?= isActive(['report_revenue_merchant_tonase_mitra.php']); ?>" href="report_revenue_merchant_tonase_mitra.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar Per Pedagang </a></li>
										<li><a class="<?= isActive(['report_revenue_unpaid_tonase_mitra.php']); ?>" href="report_revenue_unpaid_tonase_mitra.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar </a></li>
										<li><a class="<?= isActive(['report_revenue_unpaid_merchant_tonase_mitra.php']); ?>" href="report_revenue_unpaid_merchant_tonase_mitra.php"> 
											<table>
												<tr>
													<td><i class="fa fa-clock-o fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Belum Terbayar Per Pedagang </td>
												</tr>
											</table>
										</a></li>
									</ul>
								</li> -->

								
								<li class="<?= isParentActive(['report_tonase.php', 'report_tonase_merchant.php', 'report_tonase_block.php', 'report_tonase_comodity.php', 'report_merchant_comodity.php']); ?>"><a href="#"><i class="fa fa-line-chart fa-fw"></i> Pendapatan Tonase<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_tonase.php']); ?>" href="report_tonase.php"><i class="fa fa-adjust fa-fw"></i> Tonase Harian </a></li>
										<li><a class="<?= isActive(['report_tonase_merchant.php']); ?>" href="report_tonase_merchant.php"><i class="fa fa-group fa-fw"></i> Tonase Pedagang </a></li>
										<li><a class="<?= isActive(['report_tonase_block.php']); ?>" href="report_tonase_block.php"><i class="fa fa-line-chart fa-fw"></i> Tonase Blok </a></li>
										<li><a class="<?= isActive(['report_tonase_comodity.php']); ?>" href="report_tonase_comodity.php"><i class="fa fa-archive fa-fw"></i> Tonase Komoditi </a></li>
										<li><a class="<?= isActive(['report_merchant_comodity.php']); ?>" href="report_merchant_comodity.php"><i class="fa fa-truck fa-fw"></i> Pedagang & Komoditi </a></li>
									</ul>
								</li>
								
								<li class="<?= isParentActive(['report_mitra.php', 'report_mitra_year.php']); ?>"><a href="#"><i class="fa fa-line-chart fa-fw"></i> Pendapatan Mitra <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_mitra.php']); ?>" href="report_mitra.php"><i class="fa fa-adjust fa-fw"></i> Mitra Harian </a></li>
										<!-- <li><a class="<?= isActive(['report_mitra_year.php']); ?>" href="report_mitra_year.php"><i class="fa fa-line-chart fa-fw"></i> Mitra Tahunan</a></li> -->
									</ul>
								</li>

								<li class="<?= isParentActive(['report_kokm.php', 'report_kokm_mitra.php', 'report_outstanding_ko.php', 'report_outstanding_ko_mitra.php']); ?>"><a href="#"><i class="fa fa-line-chart fa-fw"></i> KO & KM <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_kokm.php']); ?>" href="report_kokm.php"><i class="fa fa-sticky-note-o fa-fw"></i> KO & KM Tonase </a></li>
										<li><a class="<?= isActive(['report_kokm_mitra.php']); ?>" href="report_kokm_mitra.php"><i class="fa fa-sticky-note-o fa-fw"></i> KO & KM Mitra </a></li>
										<li>
											<a class="<?= isActive(['report_outstanding_ko.php']); ?>"
											href="report_outstanding_ko.php"
											style="display:flex; align-items:center;">

												<i class="fa fa-clock-o fa-fw"></i>
												<span style="margin-left:8px;">Outstanding KO & KM Tonase</span>

											</a>
										</li>

										<li>
											<a class="<?= isActive(['report_outstanding_ko_mitra.php']); ?>"
											href="report_outstanding_ko_mitra.php"
											style="display:flex; align-items:center;">

												<i class="fa fa-clock-o fa-fw"></i>
												<span style="margin-left:8px;">Outstanding KO & KM Mitra</span>

											</a>
										</li>
									</ul>
								</li>
							</ul>
						</li>

						<!-- <li class="<?= isParentActive(['admin.php', 'checker.php', 'laborer.php', 'transportation.php', 'goods.php', 'seasonalcommodity.php', 'noncommodity.php', 'payment.php']); ?>">
							<a href="#"><i class="fa fa-database fa-fw"></i> Master<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['admin.php']); ?>" href="admin.php"><i class="fa fa-group fa-fw"></i> SPV / PP / Keuangan </a></li>
								<li><a class="<?= isActive(['checker.php']); ?>" href="checker.php"><i class="fa fa-group fa-fw"></i> Cashier / Checker </a></li>
								<li><a class="<?= isActive(['laborer.php']); ?>" href="laborer.php"><i class="fa fa-group fa-fw"></i> Mitra / Mandor </a></li>
								<li><a class="<?= isActive(['transportation.php']); ?>" href="transportation.php"><i class="fa fa-truck fa-fw"></i> Kendaraan </a></li>
								<li><a class="<?= isActive(['goods.php']); ?>" href="goods.php"><i class="fa fa-archive fa-fw"></i> Komoditi </a></li>
								<li><a class="<?= isActive(['seasonalcommodity.php']); ?>" href="seasonalcommodity.php"><i class="fa fa-archive fa-fw"></i> Komoditi Musiman </a></li>
								<li><a class="<?= isActive(['noncommodity.php']); ?>" href="noncommodity.php"><i class="fa fa-archive fa-fw"></i> NonKomoditi </a></li>
								<li><a class="<?= isActive(['payment.php']); ?>" href="payment.php"><i class="fa fa-money fa-fw"></i> Tarif </a></li>
							</ul>
						</li> -->

						<li class="<?= isParentActive(['log_pp.php', 'log_print_spv.php', 'log_cashier.php', 'log_print.php']); ?>">
							<a href="#"><i class="fa fa-history fa-fw"></i> Log History<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">								
								<li class="<?= isParentActive(['log_pp.php', 'log_print_spv.php']); ?>">
									<a href="#"><i class="fa fa-user fa-fw"></i> PP <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['log_pp.php']); ?>" href="log_pp.php"><i class="fa fa-hourglass-2 fa-fw"></i> Activity </a></li>
										<!-- <li><a class="<?= isActive(['log_print_spv.php']); ?>" href="log_print_spv.php"><i class="fa fa-print fa-fw"></i> Print </a></li> -->
									</ul>
								</li>

								<li class="<?= isParentActive(['log_cashier.php', 'log_print.php']); ?>">
									<a href="#"><i class="fa fa-user fa-fw"></i> Cashier <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['log_cashier.php']); ?>" href="log_cashier.php"><i class="fa fa-hourglass-2 fa-fw"></i> Activity </a></li>
										<li><a class="<?= isActive(['log_print.php']); ?>" href="log_print.php"><i class="fa fa-print fa-fw"></i> Print </a></li>
									</ul>
								</li>
							</ul>

							<li><a class="<?= isActive(['achievement.php', 'achievement_detail.php', 'achievement_detail2.php']); ?>" href="achievement.php"><i class="fa fa-trophy fa-fw"></i> Pencapaian Cashier</a></li>
							<li>
								<a class="<?= isActive(['developer.php']); ?> notification-wrapper" href="developer.php">
									<?php 
										if ($countRevision != 0) {
											echo '<span class="notification-revision">' . $countRevision . '</span>';
										} else {
											echo '<span class="notification-revision-hidden"></span>';
										}
									?>
									<i class="fa fa-pencil fa-fw"></i> Revisi <span style="margin-left: 100px;"></span>
								</a>
							</li>

							<li><a class="<?= isActive(['changelog.php']); ?>" href="changelog.php"><i class="fa fa-history fa-fw"></i> Update Log </a></li>
						</li>
                    </ul>
                </div>
            </div>
</nav>

<!-- Floating Trophy -->
<div class="floating-trophy" onclick="openCongrats()">
    <i class="fa fa-trophy"></i>
</div>


<!-- Modal Congrats -->
<div class="modal fade" id="modalCongrats" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-congrats-box">

            <div class="modal-decor-top"></div>

            <!-- 2 SLIDE -->
            <div class="slide slide1 active">
                <img id="previewPhoto" 
                    src="<?php echo $data['photo'] ? '../../uploads/' . $data['photo'] : '../../uploads/profile-default.png'; ?>"
                    class="modal-congrats-photo">

                <h3 class="modal-congrats-title">
                    Selamat, <?php echo $data['checker_name']; ?> 🎉
                </h3>

                <p class="modal-congrats-sub">
                    Menjadi <b>Cashier of The Month</b><br>
                    untuk bulan <?php echo $lastMonthName ?>!
                </p>

                <p class="modal-congrats-detail">
                    Dengan pembayaran tonase sebanyak:
                    <br><b><?php echo number_format($data['total_transactions'], 0, ',', '.'); ?></b> Nota Lunas!
                </p>
            </div>

            <div class="slide slide2">
                <img id="previewPhoto" 
                    src="<?php echo $data2['photo'] ? '../../uploads/' . $data2['photo'] : '../../uploads/profile-default.png'; ?>"
                    class="modal-congrats-photo">

                <h3 class="modal-congrats-title">
                    Selamat, <?php echo $data2['checker_name']; ?> 🎉
                </h3>

                <p class="modal-congrats-sub">
                    Menjadi <b>Checker of The Month</b><br>
                    untuk bulan <?php echo $lastMonthName ?>!
                </p>

                <p class="modal-congrats-detail">
                    Dengan setoran KO sebanyak:
                    <br><b><?php echo number_format($data2['total_ko'], 0, ',', '.'); ?></b> Setoran!
                </p>
            </div>

            <!-- BUTTON CONTROL -->
            <button class="slide-btn prev-btn">❮</button>
            <button class="slide-btn next-btn">❯</button>

            <div class="modal-decor-bottom"></div>

        </div>
    </div>
</div>


<!-- Canvas Confetti -->
<canvas id="confetti-canvas" style="position: fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; z-index:9998;"></canvas>


<!-- Confetti -->
<script>
	function openCongrats() {
		// buka modal manual tanpa bootstrap
		document.getElementById("modalCongrats").style.display = "block";
		document.getElementById("modalCongrats").classList.add("in");

		startConfetti();
	}

	window.onclick = function(e){
		if(e.target.id === "modalCongrats"){
			document.getElementById("modalCongrats").style.display = "none";
			document.getElementById("modalCongrats").classList.remove("in");
		}
	}
</script>

<script>
	const confettiCanvas = document.getElementById('confetti-canvas');
	const ctx = confettiCanvas.getContext('2d');

	confettiCanvas.width = window.innerWidth;
	confettiCanvas.height = window.innerHeight;

	let particles = [];

	function random(min, max) {
		return Math.random() * (max - min) + min;
	}

	function createConfetti() {
		for (let i = 0; i < 150; i++) {
			particles.push({
				x: random(0, confettiCanvas.width),
				y: random(-200, -10),
				size: random(3, 6),
				speed: random(1, 4),
				angle: random(0, Math.PI * 2),
				color: "hsl(" + random(0, 360) + ", 100%, 50%)"
			});
		}
	}

	function draw() {
		ctx.clearRect(0, 0, confettiCanvas.width, confettiCanvas.height);

		particles.forEach((p, index) => {
			ctx.beginPath();
			ctx.fillStyle = p.color;
			ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
			ctx.fill();

			p.y += p.speed;
			p.x += Math.sin(p.angle);

			if (p.y > confettiCanvas.height) {
				particles.splice(index, 1);
			}
		});

		requestAnimationFrame(draw);
	}

	draw();

	function startConfetti() {
		particles = []; // reset biar tidak numpuk
		createConfetti();
	}
</script>

<!-- Carousel Modal -->
<script>
	document.addEventListener("DOMContentLoaded", function() {

		var slides = document.querySelectorAll(".slide");
		var current = 0;
		var autoSlide;

		function showSlide(index) {
			slides.forEach(function(s, i) {
				s.classList.remove("active");
			});
			slides[index].classList.add("active");
		}

		function nextSlide() {
			current = (current + 1) % slides.length;
			showSlide(current);
		}

		function prevSlide() {
			current = (current - 1 + slides.length) % slides.length;
			showSlide(current);
		}

		document.querySelector(".next-btn").onclick = function() {
			nextSlide();
			resetAutoSlide();
		};

		document.querySelector(".prev-btn").onclick = function() {
			prevSlide();
			resetAutoSlide();
		};

		function startAutoSlide() {
			autoSlide = setInterval(nextSlide, 5000);
		}

		function resetAutoSlide() {
			clearInterval(autoSlide);
			startAutoSlide();
		}

		// Saat modal dibuka → mulai auto-slide
		$('#modalCongrats').on('shown.bs.modal', function () {
			current = 0;
			showSlide(current);
			startAutoSlide();
		});

		// Saat modal ditutup → stop auto-slide
		$('#modalCongrats').on('hidden.bs.modal', function () {
			clearInterval(autoSlide);
		});

	});
</script>

<script>
	$(document).ready(function() {
		$(".dropdown").on("shown.bs.dropdown", function() {
			$("#dropdownIcon").removeClass("fa-angle-down").addClass("fa-angle-up");
		});

		$(".dropdown").on("hidden.bs.dropdown", function() {
			$("#dropdownIcon").removeClass("fa-angle-up").addClass("fa-angle-down");
		});
	});
</script>