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

	date_default_timezone_set('Asia/Jakarta');
	$hour = (int) date('H');

	if ($hour >= 7 && $hour < 15) {
		$shiftText  = 'Shift 1';
		$shiftClass = 'shift-pagi';
		$shiftTime  = '07:00 – 15:00';
	} elseif ($hour >= 15 && $hour < 23) {
		$shiftText  = 'Shift 2';
		$shiftClass = 'shift-sore';
		$shiftTime  = '15:00 – 23:00';
	} else {
		$shiftText  = 'Shift 3';
		$shiftClass = 'shift-malam';
		$shiftTime  = '15:00 – 23:00';
	}


	$greetingMessage = greeting();

	$checkerName	= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	$photoUrl	= executeScalar("select photo from checker where checker_code = '".$_SESSION['checker']."'");
	$checkerCode = mysql_real_escape_string($_SESSION['checker']);
	$allUser = mysql_query("SELECT * FROM checker WHERE checker_role = 'cashier' AND status_aktif = 1 AND checker_code != '$checkerCode' ORDER BY checker_name ASC");

	$countRevision = executeScalar("SELECT COUNT(revision_id) FROM revision WHERE status1 IS NOT NULL AND status2 IS NULL");


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


	/* Collapse Sidebar */
	/* Sidebar tetap bisa scroll */
	.sidebar-nav {
		overflow-y: auto;
		scrollbar-width: none;        /* Firefox */
		-ms-overflow-style: none;     /* IE & Edge lama */
	}

	/* Chrome, Safari, Edge */
	.sidebar-nav::-webkit-scrollbar {
		width: 0;
		height: 0;
	}
	.sidebar {
		width: 230px;
		transition: all 0.3s ease;
	}

	/* Content ikut geser */
	#page-wrapper {
		margin-left: 240px;
		transition: all 0.3s ease;
	}

	/* Mode sidebar kecil */
	body.sidebar-collapse .sidebar {
		width: 60px;
	}

	/* Content ikut mengecil */
	body.sidebar-collapse #page-wrapper {
		margin-left: 60px;
	}

	/* Sembunyikan teks menu */
	body.sidebar-collapse .sidebar .nav > li > a span,
	body.sidebar-collapse .sidebar .nav-second-level {
		display: none;
	}

	/* Tengah ikon */
	body.sidebar-collapse .sidebar .nav > li > a {
		text-align: center;
		padding-left: 0;
		padding-right: 0;
	}

	/* ==============================
	PROFILE CARD (NORMAL)
	================================ */
	.profile-card-sidebar {
		display: flex;
		align-items: center;
		gap: 12px;
		padding: 12px;
		margin: 15px;
		background:
			linear-gradient(135deg, #334155, #020617),
			radial-gradient(circle at top left, rgba(59,130,246,.18), transparent 55%);
		border-radius: 14px;
		box-shadow: 0 6px 12px rgba(0,0,0,.35);
		transition: all .3s ease;
		position: relative;
		overflow: hidden;
		z-index: 1;
	}

	/* Ring cahaya */
	.profile-card-sidebar::before {
		content: "";
		position: absolute;
		inset: -2px;
		background: conic-gradient(
			from 0deg,
			transparent 0deg,
			#facc15 60deg,
			#fde68a 120deg,
			#f59e0b 180deg,
			#fde68a 240deg,
			#facc15 300deg,
			transparent 360deg
		);
		animation: rotateGlow 6s linear infinite;
		filter: blur(6px);
		opacity: .9;
		z-index: -1;
	}

	/* Mask agar glow hanya di pinggir */
	.profile-card-sidebar::after {
		content: "";
		position: absolute;
		inset: 2px;
		background:
			linear-gradient(135deg, #334155, #020617),
			radial-gradient(circle at top left, rgba(59,130,246,.18), transparent 55%);
		border-radius: 14px;
		z-index: -1;
	}

	/* Animasi putar */
	@keyframes rotateGlow {
		from {
			transform: rotate(0deg);
		}
		to {
			transform: rotate(360deg);
		}
	}

	.profile-card-sidebar:hover {
		transform: translateY(-2px);
		box-shadow: 0 10px 18px rgba(0,0,0,.45);
	}

	/* ==============================
	AVATAR
	================================ */
	.profile-avatar {
		position: relative;
		flex-shrink: 0;
	}

	.profile-avatar img {
		width: 48px;
		height: 48px;
		border-radius: 50%;
		object-fit: cover;
		border: 2px solid #3b82f6;
		cursor: pointer;
	}

	/* STATUS ONLINE */
	.status-online {
		position: absolute;
		bottom: 2px;
		right: 2px;
		width: 10px;
		height: 10px;
		background: #22c55e;
		border: 2px solid #0f172a;
		border-radius: 50%;
	}

	/* ==============================
	PROFILE INFO
	================================ */
	.profile-info b {
		display: block;
		font-size: 14px;
		color: #ffffff;
	}

	.profile-info span {
		font-size: 11px;
		color: #94a3b8;
	}

	/* ROLE BADGE */
	.profile-card-sidebar .role-badge {
		display: inline-block;
		margin-top: 2px;
		padding: 3px 8px;
		font-size: 10px;
		border-radius: 20px;
		background: #2563eb;
		color: #ffffff !important;
		line-height: 1.3;
	}

	.profile-meta {
		margin-top: 4px;
		display: flex;
		align-items: center;
		gap: 6px;
		font-size: 10px;
	}

	.meta-separator {
		color: rgba(255,255,255,0.5);
	}

	.shift-badge {
		padding: 0px 8px;
		line-height: 18px;
		border-radius: 10px;
		font-weight: 500;
		position: relative;
    	cursor: pointer;
	}

	/* Warna shift */
	.shift-pagi {
		background: #dcfce7;
		color: #166534 !important;
	}

	.shift-sore {
		background: #ffedd5;
		color: #9a3412 !important;
	}

	.shift-malam {
		background: #e0e7ff;
		color: #1e3a8a !important;
	}

	.shift-badge::after {
		content: attr(data-tooltip);
		position: absolute;
		bottom: 130%;
		left: 50%;
		transform: translateX(-50%);
		background: rgba(65, 75, 97, 0.95);
		color: #fff;
		padding: 4px 8px;
		font-size: 11px;
		border-radius: 6px;
		white-space: nowrap;
		opacity: 0;
		pointer-events: none;
		transition: opacity .2s ease;
		z-index: 9999;
	}

	.shift-badge::before {
		content: '';
		position: absolute;
		bottom: 120%;
		left: 50%;
		transform: translateX(-50%);
		border: 5px solid transparent;
		border-top-color: rgba(15,23,42,.95);
		opacity: 0;
		transition: opacity .2s ease;
	}

	.shift-badge:hover::after,
	.shift-badge:hover::before {
		opacity: 1;
	}

	/* ==============================
	PROFILE CARD SAAT COLLAPSE
	================================ */
	body.sidebar-collapse .profile-card-sidebar {
		justify-content: center;
		align-items: center;
		padding: 12px 0;
		margin: 15px auto;
		width: 60px;
		height: 60px;
		border-radius: 50%;
		background:
			linear-gradient(135deg, #334155, #020617),
			radial-gradient(circle at top, rgba(59,130,246,.25), transparent 60%);
	}

	/* SEMBUNYIKAN TEKS */
	body.sidebar-collapse .profile-info {
		display: none;
	}

	/* AVATAR SAAT COLLAPSE */
	body.sidebar-collapse .profile-avatar img {
		width: 42px;
		height: 42px;
		border-width: 2px;
	}

	/* STATUS ONLINE SAAT COLLAPSE */
	body.sidebar-collapse .status-online {
		bottom: 3px;
		right: 3px;
	}

	/* HOVER SAAT COLLAPSE */
	body.sidebar-collapse .profile-card-sidebar:hover {
		transform: scale(1.05);
		box-shadow: 0 10px 22px rgba(0,0,0,.6);
	}

	.sidebar-toggle-btn {
		background: transparent;
		border: none;
		color: #fff;
		font-size: 18px;
		width: 40px;
		height: 40px;
		margin-left: 5px;
		margin-top: 5px;
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.sidebar-toggle-btn:hover {
		background: rgba(255,255,255,0.2);
		border-radius: 4px;
	}

	/* ===== SIDEBAR DARK MODE ===== */
	.sidebar {
		width: 240px;
		background-color: #1f2937;
		border-right: 1px solid #2d3748;
		transition: all 0.3s ease;
	}

	/* Menu utama */
	.sidebar .nav > li > a {
		color: #e5e7eb;
		padding: 12px 18px;
		font-size: 14px;
		transition: all 0.2s ease;
	}

	/* Icon */
	.sidebar .nav > li > a i {
		color: #e5e7eb;
		width: 20px;
		text-align: center;
		margin-right: 8px;
	}

	/* Hover */
	.sidebar .nav > li > a:hover {
		background-color: #374151;
		color: #ffffff;
	}

	/* Active menu */
	.sidebar .nav > li.active > a,
	.sidebar .nav > li > a.active {
		background-color: #111827;
		color: #ffffff;
	}

	/* Submenu */
	.sidebar .nav-second-level {
		background-color: #111827;
	}

	.sidebar .nav-second-level li a {
		color: #cbd5f5;
		padding-left: 40px;
	}

	.sidebar .nav-second-level li a:hover {
		background-color: #1f2937;
		color: #ffffff;
	}

	.sidebar .nav > li {
    	border-bottom: 1px solid rgba(255,255,255,0.06);
	}

	/* Text */
	.text-profile {
		display: flex;
		flex-direction: column;
		line-height: 1.2;
	}

	.text-profile b {
		color: #ffffff;
		font-size: 16px;
		margin-bottom: 5px;
	}

	.text-profile span {
		color: #9ca3af;     /* abu lembut */
		font-size: 14px;
	}

	/* ======================================================
	BASE SIDEBAR CARD (ALL LEVEL)
	====================================================== */
	.sidebar-nav .nav li > a {
		display: flex;
		align-items: center;
		gap: 12px;
		position: relative;

		padding: 12px 14px;
		margin: 6px 10px;
		border-radius: 14px;

		background: rgba(255,255,255,0.04);
		backdrop-filter: blur(4px);

		color: #cbd5e1;
		transition: all .3s ease;
	}

	/* Text fleksibel agar arrow ke kanan */
	.sidebar-nav .nav li > a span {
		flex: 1;
	}

	/* Hover card */
	.sidebar-nav .nav li > a:hover {
		background: rgba(250, 204, 21, 0.10);
		transform: translateX(4px);
		color: #fff;
	}

	/* ======================================================
	ICON MINI CARD
	====================================================== */
	.sidebar-nav .nav li > a i:not(.arrow) {
		width: 34px;
		height: 34px;
		min-width: 34px;

		display: flex;
		align-items: center;
		justify-content: center;

		border-radius: 10px;
		background: rgba(255,255,255,0.08);

		color: #facc15;
		font-size: 14px;

		transition: all .3s ease;
	}

	/* Hover icon */
	.sidebar-nav .nav li > a:hover i:not(.arrow) {
		background: rgba(250, 204, 21, 0.25);
		color: #fff;
	}

	/* ======================================================
	ARROW (SELALU DI KANAN)
	====================================================== */
	.sidebar-nav .nav li > a .arrow {
		margin-left: auto;
		font-size: 13px;
		opacity: 0.7;
		transition: transform .3s ease;
	}

	/* Arrow rotate saat submenu open */
	.sidebar-nav .nav li.open > a .arrow {
		transform: rotate(180deg);
	}

	/* ======================================================
	ACTIVE STATE (ALL LEVEL)
	====================================================== */
	.sidebar-nav .nav li > a.active {
		background: linear-gradient(135deg, #facc15, #f59e0b);
		color: #1f2937;
		font-weight: 600;

		box-shadow:
			0 0 12px rgba(250, 204, 21, 0.9),
			0 0 28px rgba(250, 204, 21, 0.6);

		animation: glowPulse 1.8s infinite alternate;
	}

	/* Icon aktif */
	.sidebar-nav .nav li > a.active i:not(.arrow) {
		background: rgba(31, 41, 55, 0.25);
		color: #1f2937;
		box-shadow: inset 0 0 8px rgba(0,0,0,0.25);
	}

	/* Garis samping emas */
	.sidebar-nav .nav li > a.active::before {
		content: '';
		position: absolute;
		left: -6px;
		top: 15%;
		width: 4px;
		height: 70%;
		background: linear-gradient(to bottom, #fde68a, #f59e0b);
		border-radius: 5px;
	}

	/* ======================================================
	INDENT LEVEL (HIRARKI MENU)
	====================================================== */

	/* SECOND LEVEL */
	.sidebar-nav .nav-second-level > li > a {
		margin-left: 24px;
		padding-left: 16px;
		background: rgba(255,255,255,0.03);
	}

	/* THIRD LEVEL */
	.sidebar-nav .nav-third-level > li > a {
		margin-left: 44px;
		padding-left: 16px;
		background: rgba(255,255,255,0.02);
	}

	/* Active second & third tetap glow */
	.sidebar-nav .nav-second-level li > a.active,
	.sidebar-nav .nav-third-level li > a.active {
		background: linear-gradient(135deg, #facc15, #f59e0b);
		color: #1f2937;
	}

	/* ======================================================
	GLOW ANIMATION
	====================================================== */
	@keyframes glowPulse {
		from {
			box-shadow:
				0 0 8px rgba(250, 204, 21, 0.6),
				0 0 20px rgba(250, 204, 21, 0.4);
		}
		to {
			box-shadow:
				0 0 16px rgba(250, 204, 21, 1),
				0 0 36px rgba(250, 204, 21, 0.8);
		}
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
        <a class="navbar-brand" href="dashboard.php" style="color: #ffffff; background-color: #111827; width: 240px;"><i class="fa fa-balance-scale fa-fw silhouette"></i> <b class="silhouette">TIMBANGAN PISS</b></a>
		<button type="button" class="sidebar-toggle-btn"
			onclick="toggleSidebar()">
			<i class="fa fa-bars"></i>
		</button>
    </div>
	
	<ul class="nav navbar-top-links navbar-right">
		<?php include 'components/clock.php'; ?>
		<?php include 'components/cut_retail.php'; ?>
		<?php include 'components/calculator.php'; ?>
		<?php include 'components/offcanvas_note.php'; ?>
		<li class="dropdown">
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" 
			style="color: #ffffff !important; background-color: transparent !important;"
			onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'" 
			onmouseout="this.style.backgroundColor='transparent'">

				<i class="fa fa-cog" style="color: #ffffff;"></i>&nbsp;
				<b style="text-transform: capitalize; color: #ffffff;">Pengaturan</b>&nbsp;
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
					<a data-toggle="modal" data-target="#modalAccount" href="#" 
					style="color: #ffffff; font-size: 15px; display: block; padding: 10px; text-decoration: none;"
					onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'; this.style.color='#ffffff';"
					onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ffffff';">
						<div class="name" style="display: inline-block; padding-right: 45px; padding-left: 10px;">
							Beralih Akun 
						</div>
						<div class="icon" style="display: inline-block;">
							<i class="fa fa-exchange fa-fw" style="color: #ffffff; font-size: 15px;"></i>
						</div>
					</a>
				</li>
				<li class="divider"></li>
				<li>
					<a data-toggle="modal" data-target="#modalLogout" href="#" 
					style="color: #ffffff; font-size: 15px; display: block; padding: 10px; text-decoration: none;"
					onmouseover="this.style.backgroundColor='rgb(103, 165, 218)'; this.style.color='#ffffff';"
					onmouseout="this.style.backgroundColor='transparent'; this.style.color='#ffffff';">
						<div class="name" style="display: inline-block; padding-right: 90px; padding-left: 10px;">
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
				<div class="modal-dialog">
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

			<!-- Modal Account -->
			<div class="modal fade" id="modalAccount" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-primary text-white" style="border-top-left-radius: 5px; border-top-right-radius: 5px;">
							<h4 id="header_password"><i class="fa fa-users fa-fw"></i>&nbsp;&nbsp;Pilih Akun</h4>
							<button style="margin-top: -35px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<div id="data_account" style="margin-bottom: 0px;">
							<?php while ($row = mysql_fetch_array($allUser)) { ?>
								<div class="panel panel-default">
									<div class="panel-body">
										<div class="row" style="align-items: center;">
											<div class="col-xs-1 text-center">
												<!-- <i class="fa fa-user fa-3x text-primary"></i> -->
												<img id="previewPhoto" src="<?php echo $row['photo'] ? '../uploads/' . $row['photo'] : '../uploads/profile-default.png'; ?>" style="width: 50px; border-radius: 100%;">
											</div>
											<div class="col-xs-8" style="padding-left: 30px;">
												<h4 class="media-heading" style="margin-top: 5px;"><?php echo $row['checker_name'] ?></h4>
												<p style="margin-bottom: 0;">Kode Kasir | <?php echo $row['checker_code'] ?></p>
											</div>
											<div class="col-xs-3 text-right">
												<a href="javascript:void(0)" 
													class="btn btn-primary btn-sm pilih-akun" 
													style="padding: 10px 20px; font-size: 14px; margin-top:5px;"
													data-id="<?php echo $row['checker_id'] ?>"
													data-nama="<?php echo htmlspecialchars($row['checker_name']) ?>"
													data-photo="<?php echo htmlspecialchars($row['photo']) ?>"
													data-kode="<?php echo htmlspecialchars($row['checker_code']) ?>">
													Pilih
												</a>
											</div>
										</div>
									</div>
								</div>
							<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Modal account confirmation -->
			<div class="modal fade" id="modalLoginConfirm" tabindex="-1" role="dialog" aria-labelledby="modalLoginConfirmLabel">
				<div class="modal-dialog">
					<div class="modal-content">
					<div class="modal-header bg-primary text-white">
						<h4 class="modal-title" id="modalLoginConfirmLabel"><i class="fa fa-lock"></i>&nbsp; Konfirmasi Akun</h4>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -30px;">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="panel panel-default">
							<div class="panel-body">
								<div class="row" style="align-items: center;">
									<div class="col-xs-1 text-center">
										<!-- <i class="fa fa-user fa-3x text-primary"></i> -->
										<img id="previewPhotoSwitch" style="width:50px; border-radius:100%;">
									</div>
									<input type="hidden" id="modal_id_user" value="">
									<div class="col-xs-5" style="padding-left: 30px;">
										<h4 class="media-heading" style="margin-top: 5px;"> <span id="modal_nama"></span></h4>
										<p style="margin-bottom: 0;">Kode Kasir |  <span id="modal_kode"></span></p>
									</div>
									<div class="col-xs-6 text-right" style="margin-top: 10px;">
										<input type="password" class="form-control" id="modal_password" placeholder="Password" style="padding-right: 40px;" autofocus>
										<i class="fa fa-eye" id="togglePassword" style="position: absolute; top: 50%; right: 25px; transform: translateY(-50%); cursor: pointer;"></i>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
						<button type="button" class="btn btn-primary" onclick="submitPassword()">Submit</button>
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
							<a href="logout.php" class="btn btn-primary" id="logoutButton">Keluar</a>
						</div>
					</div>
				</div>
			</div>

            <div class="navbar-default sidebar" role="navigation">
				<div class="sidebar-nav navbar-collapse" style="min-height: 100vh;  background-color: #1f2937;">
					<ul class="nav" id="side-menu" style="margin-bottom: 70px;">
						<!-- Profile Card -->
						<div class="profile-card-sidebar" id="photoContainer" style="margin-bottom: 30px;">
							<div class="profile-avatar">
								<img id="previewPhoto"
									src="<?php echo $photoUrl ? '../uploads/' . $photoUrl : '../uploads/profile-default.png'; ?>">
								<span class="status-online"></span>
							</div>

							<div class="profile-info">
								<b><?php echo $checkerName; ?></b>
								
								<div class="profile-meta">
									<span class="role-badge">Kasir</span>
									<span class="meta-separator">|</span>
									<span class="shift-badge <?php echo $shiftClass; ?>" data-tooltip="<?php echo $shiftTime; ?>">
										<?php echo $shiftText; ?>
									</span>
								</div>
							</div>
						</div>


						
						<!-- Navigation -->
						<li><a class="<?= isActive(['dashboard.php']); ?>" href="dashboard.php"><i class="fa fa-dashboard fa-fw"></i> <span>Dashboard</span> </a></li>

						<li class="<?= isParentActive(['index.php', 'index2.php', 'index3.php', 'index4.php', 'check2.php', 'check.php', 'check_edit.php', 'index5.php', 'listing.php']); ?>">
							<a href="#"><i class="fa fa-balance-scale fa-fw"></i> <span>Timbangan</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<li><a class="<?= isActive(['index.php', 'index2.php', 'index3.php']); ?>" href="index.php"><i class="fa fa-truck fa-fw"></i> Masuk / Keluar / Kir </a></li>
								<li><a class="<?= isActive(['index4.php']); ?>" href="index4.php"><i class="fa fa-users fa-fw"></i> Pedagang Retail </a></li>
								<li><a class="<?= isActive(['check2.php']); ?>" href="check2.php"><i class="fa fa-square-o fa-fw"></i> Data Belum Dicek </a></li>
								<li><a class="<?= isActive(['check.php', 'check_edit.php']); ?>" href="check.php"><i class="fa fa-check-square-o fa-fw"></i> Data Sudah Dicek </a></li>
								<li><a class="<?= isActive(['index5.php', 'listing.php']); ?>" href="index5.php"><i class="fa fa-search fa-fw"></i> Listing Nota</a></li>
							</ul>
						</li>

						<li class="<?= isParentActive(['pay.php', 'pay_mitra.php', 'pay_mitra_salary.php', 'pay_mitra_salary2.php', 'pay_acc.php', 'pay_acc_mitra.php', 'pay_tonase_mitra.php', 'pay_acc_tonase_mitra.php']); ?>">
							<a href="#"><i class="fa fa-credit-card fa-fw"></i> <span>Pembayaran & Setoran</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<!-- <li class="<?= isParentActive(['pay.php', 'pay_acc.php']); ?>"><a href="#"><i class="fa fa-truck fa-fw"></i> Tonase<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
									<li><a class="<?= isActive(['pay.php']); ?>" href="pay.php"><i class="fa fa-money fa-fw"></i> Pembayaran Tonase</a></li>
									<li><a class="<?= isActive(['pay_acc.php']); ?>" href="pay_acc.php"><i class="fa fa fa-book fa-fw"></i> Setoran KO Tonase</a></li>
									</ul>
								</li>

								<li class="<?= isParentActive(['pay_mitra.php', 'pay_acc_mitra.php', 'pay_mitra_salary.php']); ?>"><a href="#"><i class="fa fa-group fa-fw"></i> Mitra<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['pay_mitra.php']); ?>" href="pay_mitra.php"><i class="fa fa-money fa-fw"></i> Pembayaran Mitra</a></li>
										<li><a class="<?= isActive(['pay_acc_mitra.php']); ?>" href="pay_acc_mitra.php"><i class="fa fa fa-book fa-fw"></i> Setoran KO Mitra</a></li>
										<li><a class="<?= isActive(['pay_mitra_salary.php']); ?>" href="pay_mitra_salary.php"><i class="fa fa-mail-forward fa-fw"></i> Pembulatan Mitra</a></li>
									</ul>
								</li> -->

								<li><a class="<?= isActive(['pay_tonase_mitra.php']); ?>" href="pay_tonase_mitra.php"><i class="fa fa-money fa-fw"></i> Pembayaran</a></li>
								<li><a class="<?= isActive(['pay_acc_tonase_mitra.php']); ?>" href="pay_acc_tonase_mitra.php"><i class="fa fa fa-book fa-fw"></i> Setoran KO</a></li>
								<li><a class="<?= isActive(['pay_mitra_salary.php', 'pay_mitra_salary2.php']); ?>" href="pay_mitra_salary.php" style="display:flex; align-items:center;"><i class="fa fa-mail-forward fa-fw"></i> Pembulatan Upah Mitra </a></li>
							</ul>
						</li>

						<li class="<?= isParentActive(['report_transaction.php', 'report_outstanding.php', 'report_cancel.php', 'report_revenue.php', 'report_revenue_merchant.php', 'report_revenue_unpaid.php', 'report_revenue_unpaid_merchant.php', 'report_revenue_mitra.php', 'report_revenue_merchant_mitra.php', 'report_revenue_unpaid_mitra.php', 'report_revenue_unpaid_merchant_mitra.php', 'report_mitra.php', 'report_kokm.php', 'report_kokm_mitra.php', 'report_outstanding_ko.php', 'report_outstanding_ko_mitra.php']); ?>">
							<a href="#"><i class="fa fa-clipboard fa-fw"></i> <span>Laporan</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<!-- <li><a href="#"><i class="fa fa-line-chart fa-fw"></i> Transaksi Timbang<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a href="report_transaction.php"><i class="fa fa-line-chart fa-fw"></i> Transaksi Timbang</a></li>
										<li><a href="report_outstanding.php"><i class="fa fa-clock-o fa-fw"></i> Transaksi Outstanding</a></li>
										<li><a href="report_cancel.php"><i class="fa fa-close fa-fw"></i> Transaksi Batal</a></li>
									</ul>
								</li> -->

								<li class="<?= isParentActive(['report_transaction.php', 'report_outstanding.php', 'report_cancel.php']); ?>"><a href="#"><i class="fa fa-balance-scale fa-fw"></i> Transaksi Timbang <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_transaction.php']); ?>" href="report_transaction.php"><i class="fa fa-adjust fa-fw"></i> Timbang Harian</a></li>
										<li>
											<a class="<?= isActive(['report_outstanding.php']); ?>" href="report_outstanding.php" style="display:flex; align-items:center;">
												<i class="fa fa-clock-o fa-fw"></i>
												<span style="margin-left:8px;">Outstanding Timbang</span>
											</a>
										</li>
										<li><a class="<?= isActive(['report_cancel.php']); ?>" href="report_cancel.php"><i class="fa fa-close fa-fw"></i> Timbang Batal</a></li>
									</ul>
								</li>
								
								<li class="<?= isParentActive(['report_revenue.php', 'report_revenue_merchant.php', 'report_revenue_unpaid.php', 'report_revenue_unpaid_merchant.php']); ?>"><a href="#"><i class="fa fa-money fa-fw"></i> Tagihan Tonase<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<!-- <li><a class="<?= isActive(['report_revenue.php']); ?>" href="report_revenue.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar</a></li>  -->
										<!-- <li><a class="<?= isActive(['report_revenue_merchant.php']); ?>" href="report_revenue_merchant.php">
											<table>
												<tr>
													<td><i class="fa fa-check-square fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Terbayar Per Pedagang</td>
												</tr>
											</table>
										</a></li> -->
										<li><a class="<?= isActive(['report_revenue_unpaid.php']); ?>" href="report_revenue_unpaid.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar</a></li>
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

								<!-- <li><a href="#"><i class="fa fa-line-chart fa-fw"></i> Tonase<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a href="report_tonase.php"><i class="fa fa-line-chart fa-fw"></i> Tonase Harian</a></li>
										<li><a href="report_tonase_merchant.php"><i class="fa fa-group fa-fw"></i> Tonase Pedagang</a></li>
										<li><a href="report_tonase_block.php"><i class="fa fa-line-chart fa-fw"></i> Tonase Blok</a></li>
										<li><a href="report_tonase_comodity.php"><i class="fa fa-archive fa-fw"></i> Tonase Komoditi</a></li>
									</ul>
								</li> -->
								<li class="<?= isParentActive(['report_revenue_mitra.php', 'report_revenue_merchant_mitra.php', 'report_revenue_unpaid_mitra.php', 'report_revenue_unpaid_merchant_mitra.php']); ?>"><a href="#"><i class="fa fa-money fa-fw"></i> Tagihan Mitra<span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<!-- <li><a class="<?= isActive(['report_revenue_mitra.php']); ?>" href="report_revenue_mitra.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar</a></li>
										<li><a class="<?= isActive(['report_revenue_merchant_mitra.php']); ?>" href="report_revenue_merchant_mitra.php">
											<table>
												<tr>
													<td><i class="fa fa-check-square fa-fw"></i></td>
													<td>&nbsp;</td>
													<td>Terbayar Per Pedagang</td>
												</tr>
											</table>
										</a></li> -->
										<li><a class="<?= isActive(['report_revenue_unpaid_mitra.php']); ?>" href="report_revenue_unpaid_mitra.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar</a></li>
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

								<!-- <li class="<?= isParentActive(['.php', '.php', '.php', '.php']); ?>"><a href="#"><i class="fa fa-line-chart fa-fw"></i>Tagihan Tonase + Mitra &nbsp;<span style="background-color: red; color: white; padding: 4px 10px; border-radius: 10px; font-size: 9px;">Segera</span><span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['.php']); ?>" href="coming_soon.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar</a></li> 
										<li><a class="<?= isActive(['.php']); ?>" href="coming_soon.php"><i class="fa fa fa-check-square fa-fw"></i> Terbayar Per Pedagang</a></li>
										<li><a class="<?= isActive(['.php']); ?>" href="coming_soon.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar</a></li>
										<li><a class="<?= isActive(['.php']); ?>" href="coming_soon.php"><i class="fa fa-clock-o fa-fw"></i> Belum Terbayar Per Pedagang</a></li>
									</ul>
								</li> -->

								<li class="<?= isParentActive(['report_mitra.php']); ?>"><a href="#"><i class="fa fa-line-chart fa-fw"></i> Pendapatan Mitra <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_mitra.php']); ?>" href="report_mitra.php"><i class="fa fa-adjust fa-fw"></i> Mitra Harian</a></li>
										<!-- <li><a href="report_mitra_year.php"><i class="fa fa-line-chart fa-fw"></i> Mitra Tahunan</a></li> -->
									</ul>
								</li>

								<li class="<?= isParentActive(['report_kokm.php', 'report_kokm_mitra.php', 'report_outstanding_ko.php', 'report_outstanding_ko_mitra.php']); ?>"><a href="#"><i class="fa fa-line-chart fa-fw"></i> KO & KM <span class="fa arrow"></span></a>
									<ul class="nav nav-third-level">
										<li><a class="<?= isActive(['report_kokm.php']); ?>" href="report_kokm.php"><i class="fa fa-sticky-note-o fa-fw"></i> KO & KM Tonase</a></li>
										<li><a class="<?= isActive(['report_kokm_mitra.php']); ?>" href="report_kokm_mitra.php"><i class="fa fa-sticky-note-o fa-fw"></i> KO & KM Mitra</a></li>
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

						<li class="<?= isParentActive(['laborer.php', 'transportation.php', 'transportation_edit.php']); ?>">
							<a href="#"><i class="fa fa-database fa-fw"></i> <span>Master</span><span class="fa arrow"></span></a>
							<ul class="nav nav-second-level">
								<!-- <li><a class="<?= isActive('laborer.php'); ?>" href="laborer.php"><i class="fa fa-group fa-fw"></i> Mitra</a></li> -->
								<li><a class="<?= isActive(['transportation.php', 'transportation_edit.php']); ?>" href="transportation.php"><i class="fa fa-truck fa-fw"></i> Kendaraan </a></li>
							</ul>
						</li>

						<li><a class="<?= isActive(['achievement.php', 'achievement_detail.php', 'achievement_detail2.php']); ?>" href="achievement.php"><i class="fa fa-trophy fa-fw"></i> <span>Pencapaian Kasir</span></a></li>
							<li>
								<a class="<?= isActive(['developer.php']); ?>" href="developer.php">
									<?php 
										if ($countRevision != 0) {
											echo '<span class="notification-revision">' . $countRevision . '</span>';
										} else {
											echo '<span class="notification-revision-hidden"></span>';
										}
									?>
									<i class="fa fa-pencil fa-fw"></i> <span>Revisi</span> <span style="margin-left: 100px;"></span>
								</a>
							</li>
							<li><a class="<?= isActive(['changelog.php']); ?>" href="changelog.php"><i class="fa fa-history fa-fw"></i><span> Update Log</span> </a></li>
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

	$(document).on('click', '.pilih-akun', function () {
		var id = $(this).data('id');
		var nama = $(this).data('nama');
		var kode = $(this).data('kode');
		var photo = $(this).data('photo');

		$('#modal_nama').text(nama);
		$('#modal_kode').text(kode);
		$('#modal_id_user').val(id);
		$('#modal_password').val('');
		$('#modal_photo').val(photo);
		
		// 🔥 UPDATE FOTO DI MODAL
		var photoUrl = photo ? '../uploads/' + photo : 'img/profile-default.png';
		$('#previewPhotoSwitch').attr('src', photoUrl);

		$('#modalLoginConfirm').modal('show');
	});

	$(document).on('click', '#togglePassword', function () {
		const input = $('#modal_password');
		const type = input.attr('type') === 'password' ? 'text' : 'password';
		input.attr('type', type);

		// Toggle class ikon
		$(this).toggleClass('fa-eye fa-eye-slash');
	});

	function submitPassword() {
		var id = $('#modal_id_user').val();
		var password = $('#modal_password').val();

		if (password === '') {
			alert('Password tidak boleh kosong.');
			return;
		}

		$.post('switch_account_action.php', { id: id, password: password }, function (res) {
			console.log('Response:', res);

			if (res == 'ok') {
				localStorage.removeItem('notificationHidden');
				$.get('components/welcome_animation.php', function (animationHtml) {
					$('body').html(animationHtml);

					setTimeout(function() {
						window.location.href = 'dashboard.php';
					}, 3250);
				});
			} else if (res == 'wrong-password') {
				alert('Password salah.');
			}
		});
	}
</script>

<!-- Viewer JS untuk zoom foto profil -->
<script>
	document.addEventListener("DOMContentLoaded", function () {

		const container = document.getElementById('photoContainer');

		const viewer = new Viewer(container, {
			navbar: false,
			toolbar: true,
			title: false,
			zoomRatio: 0.5,
		});

	});
</script>