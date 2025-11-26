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

	$countRevision = executeScalar("SELECT COUNT(revision_id) FROM revision WHERE status1 != 'Ditolak' AND status2 IS NULL");
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

	/* BADGE STYLE */
	.achievement-badge-head {
		position: absolute;
		top: -5px;
		right: -70px;
		z-index: 5;
	}

	.level-badge-nav {
		display: inline-flex;
		align-items: center;
		padding: 4px 10px;
		border-radius: 30px;
		font-weight: 600;
		font-size: 9px;
		gap: 8px;
		color: white;
		/* box-shadow: 0 4px 8px rgba(0,0,0,0.25); */
		animation: popIn .4s ease;
	}

	/* ANIMASI */
	@keyframes popIn {
		from { transform: scale(.7); opacity: 0; }
		to   { transform: scale(1); opacity: 1; }
	}

	/* Color Badge */
	.level-badge-nav.color-developer-nav {
		background: linear-gradient(45deg, #524949ff, #000000);
		color: #fff;
		/* box-shadow: 0 0 15px #f7c40eff; */
	}
	
	.level-badge-nav.color-manager-nav {
		background: linear-gradient(45deg, #ef4646ff, #9a0404ff);
		color: #fff;
		/* box-shadow: 0 0 15px #f7c40eff; */
	}
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
			<a class="dropdown-toggle nav-user-wrapper" data-toggle="dropdown" href="#" 
			style="color: #ffffff !important; background-color: transparent !important;"
			onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'" 
			onmouseout="this.style.backgroundColor='transparent'">
				<!-- <i class="fa fa-user fa-fw" style="color: #ffffff;"></i> -->
				<span style="color: #ffffff;"><?php echo $greetingMessage; ?></span>
				<?php 
					$privileges = executeScalar("SELECT privileges FROM admin WHERE username = '".$_SESSION['username']."'");
					if ($privileges == 'manager') { ?>
						<div class="level-badge-nav color-manager-nav">
							<i class="fa fa-diamond fa-lg"></i>MANAGER
						</div>
				<?php } elseif ($privileges == 'developer') { ?>
					<div class="level-badge-nav color-developer-nav">
						<i class="fa fa-code fa-lg"></i>DEVELOPER
					</div>
				<?php } ?>
				<b style="text-transform: capitalize; color: #ffffff;"><?php echo ' - ' . $name; ?>&nbsp;</b>
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
								<div class="alert alert-info" onclick="clearNotif()">
									<h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3>
								</div>
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
						
						<li class="<?= isParentActive(['index.php', 'index2.php', 'index3.php', 'index4.php', 'check.php', 'check2.php', 'check_edit.php', 'index5.php', 'listing.php']); ?>"> 
							<a href="#"><i class="fa fa-balance-scale fa-fw"></i> Timbangan<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['index.php', 'index2.php', 'index3.php']); ?>" href="index.php"><i class="fa fa-truck fa-fw"></i> Masuk / Keluar / Kir </a></li>
								<li><a class="<?= isActive(['index4.php']); ?>" href="index4.php"><i class="fa fa-truck fa-fw"></i> Retail </a></li>
								<li><a class="<?= isActive(['check2.php']); ?>" href="check2.php"><i class="fa fa-square-o fa-fw"></i> Data Belum Dicek </a></li>
								<li><a class="<?= isActive(['check.php', 'check_edit.php']); ?>" href="check.php"><i class="fa fa-check-square-o fa-fw"></i> Data Sudah Dicek </a></li>
								<li><a class="<?= isActive(['index5.php', 'listing.php']); ?>" href="index5.php"><i class="fa fa-search fa-fw"></i> Listing Nota </a></li>
							</ul>
						</li>

						<li class="<?= isParentActive(['cancel1.php', 'cancel2.php', 'cancel3.php']); ?>"> 
							<a href="#"><i class="fa fa-close fa-fw"></i> Pembatalan<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['cancel1.php']); ?>" href="cancel1.php"><i class="fa fa-truck fa-fw"></i> Kendaraan Masuk </a></li>
								<li><a class="<?= isActive(['cancel2.php']); ?>" href="cancel2.php"><i class="fa fa-truck fa-fw"></i> Kendaraan Keluar </a></li>
								<li><a class="<?= isActive(['cancel3.php']); ?>" href="cancel3.php"><i class="fa fa-money fa-fw"></i> Pembayaran </a></li>
							</ul>
						</li>

						<li class="<?= isParentActive(['edit1.php', 'edit2.php', 'edit3.php', 'edit4.php', 'edit5.php']); ?>"> 
							<a href="#"><i class="fa fa-edit fa-fw"></i> Pengeditan<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['edit1.php']); ?>" href="edit1.php"><i class="fa fa-arrow-circle-down fa-fw"></i> Berat Masuk </a></li>
								<li><a class="<?= isActive(['edit2.php']); ?>" href="edit2.php"><i class="fa fa-arrow-circle-up fa-fw"></i> Berat Keluar </a></li>
								<li><a class="<?= isActive(['edit3.php']); ?>" href="edit3.php"><i class="fa fa-pie-chart fa-fw"></i> Berat Bersih </a></li>
								<li><a class="<?= isActive(['edit5.php']); ?>" href="edit5.php"><i class="fa fa-clock-o fa-fw"></i> Jam & Tanggal </a></li>
								<li><a class="<?= isActive(['edit4.php']); ?>" href="edit4.php"><i class="fa fa-file fa-fw"></i> Status </a></li>
							</ul>
						</li>

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
										<li><a class="<?= isActive(['report_outstanding.php']); ?>" href="report_outstanding.php">
											<table>
												<tr>
													<td><i class="fa fa-clock-o fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Outstanding Timbang</td>
												</tr>
											</table>
										</a></li>
										<li><a class="<?= isActive(['report_cancel.php']); ?>" href="report_cancel.php"><i class="fa fa-close fa-fw"></i> Timbang Batal </a></li>
									</ul>
								</li>

								<li class="<?= isParentActive(['report_revenue.php', 'report_revenue_merchant.php', 'report_revenue_unpaid.php', 'report_revenue_unpaid_merchant.php']); ?>"><a href="#"><i class="fa fa-money fa-fw"></i> Tagihan Tonase<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_revenue.php']); ?>" href="report_revenue.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar </a></li> 
										<li><a class="<?= isActive(['report_revenue_merchant.php']); ?>" href="report_revenue_merchant.php">
											<table>
												<tr>
													<td><i class="fa fa-check-square fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Terbayar Per Pedagang </td>
												</tr>
											</table>
										</a></li>
										<li><a class="<?= isActive(['report_revenue_unpaid.php']); ?>" href="report_revenue_unpaid.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar </a></li>
										<li><a class="<?= isActive(['report_revenue_unpaid_merchant.php']); ?>" href="report_revenue_unpaid_merchant.php">
										<table>
												<tr>
													<td><i class="fa fa-clock-o fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Belum Terbayar Per Pedagang </td>
												</tr>
											</table>
										</a></li>
									</ul>
								</li>

								<li class="<?= isParentActive(['report_revenue_mitra.php', 'report_revenue_merchant_mitra.php', 'report_revenue_unpaid_mitra.php', 'report_revenue_unpaid_merchant_mitra.php']); ?>"><a href="#"><i class="fa fa-money fa-fw"></i> Tagihan Mitra<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_revenue_mitra.php']); ?>" href="report_revenue_mitra.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar </a></li> 
										<li><a class="<?= isActive(['report_revenue_merchant_mitra.php']); ?>" href="report_revenue_merchant_mitra.php">
											<table>
												<tr>
													<td><i class="fa fa-check-square fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Terbayar Per Pedagang </td>
												</tr>
											</table>
										</a></li>
										<li><a class="<?= isActive(['report_revenue_unpaid_mitra.php']); ?>" href="report_revenue_unpaid_mitra.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar </a></li>
										<li><a class="<?= isActive(['report_revenue_unpaid_merchant_mitra.php']); ?>" href="report_revenue_unpaid_merchant_mitra.php">
											<table>
												<tr>
													<td><i class="fa fa-clock-o fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Belum Terbayar Per Pedagang </td>
												</tr>
											</table>
										</a></li>
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
										<li><a class="<?= isActive(['report_outstanding_ko.php']); ?>" href="report_outstanding_ko.php">
											<table>
												<tr>
													<td><i class="fa fa-clock-o fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Outstanding KO & KM Tonase </td>
												</tr>
											</table>
										</a></li>
										<li><a class="<?= isActive(['report_outstanding_ko_mitra.php']); ?>" href="report_outstanding_ko_mitra.php">
											<table>
												<tr>
													<td><i class="fa fa-clock-o fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Outstanding KO & KM Mitra </td>
												</tr>
											</table>
										</a></li>
									</ul>
								</li>
							</ul>
						</li>

						<li class="<?= isParentActive(['admin.php', 'checker.php', 'laborer.php', 'transportation.php', 'goods.php', 'seasonalcommodity.php', 'noncommodity.php', 'payment.php']); ?>">
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
						</li>

						<li class="<?= isParentActive(['log_spv.php', 'log_print_spv.php', 'log_pp.php', 'log_cashier.php', 'log_print.php']); ?>">
							<a href="#"><i class="fa fa-history fa-fw"></i> Log History<span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">								
								<li class="<?= isParentActive(['log_spv.php', 'log_print_spv.php']); ?>">
									<a href="#"><i class="fa fa-user fa-fw"></i> SPV <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['log_spv.php']); ?>" href="log_spv.php"><i class="fa fa-hourglass-2 fa-fw"></i> Activity </a></li>
										<li><a class="<?= isActive(['log_print_spv.php']); ?>" href="log_print_spv.php"><i class="fa fa-print fa-fw"></i> Print </a></li>
									</ul>
								</li>

								<li class="<?= isParentActive(['log_pp.php']); ?>">
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

							<li><a class="<?= isActive(['achievement.php', 'achievement_detail.php']); ?>" href="achievement.php"><i class="fa fa-trophy fa-fw"></i> Pencapaian </a></li>
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
						</li>
						<li><a class="<?= isActive(['changelog.php']); ?>" href="changelog.php"><i class="fa fa-history fa-fw"></i> Update Log </a></li>
                    </ul>
                </div>
            </div>
        </nav>

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