<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}

	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
	
	$checker = executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");

	$date = $_GET['date'];



	/* ======================================================
	QUERY 1 : laborer_fee
	====================================================== */

	$sql = "

	SELECT

	sum(B001) B001,
	sum(B002) B002,
	sum(B003) B003,
	sum(B004) B004,
	sum(B005) B005,
	sum(B006) B006,
	sum(B015) B015,

	sum(S001) S001,
	sum(S002) S002,
	sum(S003) S003,
	sum(S004) S004,
	sum(S005) S005,
	sum(S006) S006,
	sum(S007) S007,
	sum(S008) S008,
	sum(S009) S009,
	sum(S010) S010,
	sum(S011) S011,
	sum(S012) S012,

	sum(M01) M01,
	sum(M02) M02,
	sum(M03) M03,
	sum(M04) M04,

	sum(B001_save) SB001,
	sum(B002_save) SB002,
	sum(B003_save) SB003,
	sum(B004_save) SB004,
	sum(B005_save) SB005,
	sum(B006_save) SB006,

	sum(S001_save) SS001,
	sum(S002_save) SS002,
	sum(S003_save) SS003,
	sum(S004_save) SS004,
	sum(S005_save) SS005,
	sum(S006_save) SS006,
	sum(S007_save) SS007,
	sum(S008_save) SS008,
	sum(S009_save) SS009,
	sum(S010_save) SS010,
	sum(S011_save) SS011,
	sum(S012_save) SS012,

	sum(M01_save) SM01,
	sum(M02_save) SM02,
	sum(M03_save) SM03,
	sum(M04_save) SM04

	FROM laborer_fee
	JOIN weighing_mitra
	ON weighing_mitra.weighing_id=laborer_fee.weighing_id

	WHERE date(date_check)='$date'
	AND laborer_fee_sts='Baru'

	";

	$q=mysql_query($sql);
	$d=mysql_fetch_assoc($q);



	/* ===============================
	ASSIGN SEMUA VARIABLE
	=============================== */

	$B001=$d['B001'];
	$B002=$d['B002'];
	$B003=$d['B003'];
	$B004=$d['B004'];
	$B005=$d['B005'];
	$B006=$d['B006'];
	$B015=$d['B015'];

	$S001=$d['S001'];
	$S002=$d['S002'];
	$S003=$d['S003'];
	$S004=$d['S004'];
	$S005=$d['S005'];
	$S006=$d['S006'];
	$S007=$d['S007'];
	$S008=$d['S008'];
	$S009=$d['S009'];
	$S010=$d['S010'];
	$S011=$d['S011'];
	$S012=$d['S012'];

	$M01=$d['M01'];
	$M02=$d['M02'];
	$M03=$d['M03'];
	$M04=$d['M04'];

	$SB001=$d['SB001'];
	$SB002=$d['SB002'];
	$SB003=$d['SB003'];
	$SB004=$d['SB004'];
	$SB005=$d['SB005'];
	$SB006=$d['SB006'];

	$SS001=$d['SS001'];
	$SS002=$d['SS002'];
	$SS003=$d['SS003'];
	$SS004=$d['SS004'];
	$SS005=$d['SS005'];
	$SS006=$d['SS006'];
	$SS007=$d['SS007'];
	$SS008=$d['SS008'];
	$SS009=$d['SS009'];
	$SS010=$d['SS010'];
	$SS011=$d['SS011'];
	$SS012=$d['SS012'];

	$SM01=$d['SM01'];
	$SM02=$d['SM02'];
	$SM03=$d['SM03'];
	$SM04=$d['SM04'];



	/* ======================================================
	HITUNG SHARE DAN SAVE
	====================================================== */

	$share=
	$B001+
	$B002+
	$B003+
	$B004+
	$B005+
	$B006+
	$S001+
	$S002+
	$S003+
	$S004+
	$S005+
	$S006+
	$S007+
	$S008+
	$S009+
	$S010+
	$S011+
	$S012+
	$M01+
	$M02+
	$M03+
	$M04;



	$save=
	$SB001+
	$SB002+
	$SB003+
	$SB004+
	$SB005+
	$SB006+
	$SS001+
	$SS002+
	$SS003+
	$SS004+
	$SS005+
	$SS006+
	$SS007+
	$SS008+
	$SS009+
	$SS010+
	$SS011+
	$SS012+
	$SM01+
	$SM02+
	$SM03+
	$SM04;




	/* ======================================================
	QUERY 2 : laborer_fee2
	====================================================== */

	$sql2="

	SELECT

	sum(fee_B001) xB001,
	sum(fee_B002) xB002,
	sum(fee_B003) xB003,
	sum(fee_B004) xB004,
	sum(fee_B005) xB005,
	sum(fee_B006) xB006,

	sum(fee_S001) xS001,
	sum(fee_S002) xS002,
	sum(fee_S003) xS003,
	sum(fee_S004) xS004,
	sum(fee_S005) xS005,
	sum(fee_S006) xS006,
	sum(fee_S007) xS007,
	sum(fee_S008) xS008,
	sum(fee_S009) xS009,
	sum(fee_S010) xS010,
	sum(fee_S011) xS011,
	sum(fee_S012) xS012,

	sum(fee_M01) xM01,
	sum(fee_M02) xM02,
	sum(fee_M03) xM03,
	sum(fee_M04) xM04,

	sum(save_B001) yB001,
	sum(save_B002) yB002,
	sum(save_B003) yB003,
	sum(save_B004) yB004,
	sum(save_B005) yB005,
	sum(save_B006) yB006,

	sum(save_S001) yS001,
	sum(save_S002) yS002,
	sum(save_S003) yS003,
	sum(save_S004) yS004,
	sum(save_S005) yS005,
	sum(save_S006) yS006,
	sum(save_S007) yS007,
	sum(save_S008) yS008,
	sum(save_S009) yS009,
	sum(save_S010) yS010,
	sum(save_S011) yS011,
	sum(save_S012) yS012,

	sum(save_M01) yM01,
	sum(save_M02) yM02,
	sum(save_M03) yM03,
	sum(save_M04) yM04,

	count(fee_id) check_pay

	FROM laborer_fee2
	WHERE fee_date='$date'

	";

	$q2=mysql_query($sql2);
	$d2=mysql_fetch_assoc($q2);



	/* assign */

	$xB001=$d2['xB001'];
	$xB002=$d2['xB002'];
	$xB003=$d2['xB003'];
	$xB004=$d2['xB004'];
	$xB005=$d2['xB005'];
	$xB006=$d2['xB006'];

	$xS001=$d2['xS001'];
	$xS002=$d2['xS002'];
	$xS003=$d2['xS003'];
	$xS004=$d2['xS004'];
	$xS005=$d2['xS005'];
	$xS006=$d2['xS006'];
	$xS007=$d2['xS007'];
	$xS008=$d2['xS008'];
	$xS009=$d2['xS009'];
	$xS010=$d2['xS010'];
	$xS011=$d2['xS011'];
	$xS012=$d2['xS012'];

	$xM01=$d2['xM01'];
	$xM02=$d2['xM02'];
	$xM03=$d2['xM03'];
	$xM04=$d2['xM04'];

	$yB001=$d2['yB001'];
	$yB002=$d2['yB002'];
	$yB003=$d2['yB003'];
	$yB004=$d2['yB004'];
	$yB005=$d2['yB005'];
	$yB006=$d2['yB006'];

	$yS001=$d2['yS001'];
	$yS002=$d2['yS002'];
	$yS003=$d2['yS003'];
	$yS004=$d2['yS004'];
	$yS005=$d2['yS005'];
	$yS006=$d2['yS006'];
	$yS007=$d2['yS007'];
	$yS008=$d2['yS008'];
	$yS009=$d2['yS009'];
	$yS010=$d2['yS010'];
	$yS011=$d2['yS011'];
	$yS012=$d2['yS012'];

	$yM01=$d2['yM01'];
	$yM02=$d2['yM02'];
	$yM03=$d2['yM03'];
	$yM04=$d2['yM04'];

	$check_pay=$d2['check_pay'];



	/* ======================================================
	share2 save2 tetap ada
	====================================================== */

	$share2=
	$xB001+
	$xB002+
	$xB003+
	$xB004+
	$xB005+
	$xB006+
	$xS001+
	$xS002+
	$xS003+
	$xS004+
	$xS005+
	$xS006+
	$xS007+
	$xS008+
	$xS009+
	$xS010+
	$xS011+
	$xS012+
	$xM01+
	$xM02+
	$xM03+
	$xM04;


	$save2=
	$yB001+
	$yB002+
	$yB003+
	$yB004+
	$yB005+
	$yB006+
	$yS001+
	$yS002+
	$yS003+
	$yS004+
	$yS005+
	$yS006+
	$yS007+
	$yS008+
	$yS009+
	$yS010+
	$yS011+
	$yS012+
	$yM01+
	$yM02+
	$yM03+
	$yM04;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
	<link rel="icon" href="img/icon.png">
    <link rel="shortcut icon" href="img/icon.png" />
    <title>Pembayaran Upah Mitra | PISS</title>
	<?php echo headScript(); ?>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	<script src="js/jquery.min.js"></script>
	<script>
		/* hide all elements & show preloader*/
		document.documentElement.className += 'js';

		/* display variabel */
		var alertLoadProcess = "<span class='alert alert-danger' onclick='clearNotif()'><i class='fa fa-spinner fa-spin'></i> Loading...</span>";
    </script>
</head>
<body>
    <div id="wrapper">
        <?php include 'components/sidebar.php'; ?>

        <div id="page-wrapper">
			<div class="row">
				<br /><br><br><br>
                <div class="col-lg-12">
                    <div class="panel panel-primary">
						<div class="panel-heading">
                            <i class="fa fa-file-text-o fa-fw"></i> Detail Data Upah Mitra 
							<div style="display: inline-block; vertical-align: middle; background-color: #EB8317; color: #fff; padding: 5px 10px; border-radius: 20px;">		
								<?php echo date("d M Y", strtotime($date)); ?>
							</div>
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right:10px;">
										<label>Total Upah Mitra</label>
										<input type="text" class="form-control" id="" name="" value="<?php echo format_rupiah($share); ?>" disabled />
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right:10px;">
										<label>Total Dibagi</label>
										<input type="text" class="form-control" id="" name="" value="<?php echo format_rupiah($share2); ?>" disabled />
									</div>
								</div>
								<!-- <div class="col-md-3">
									<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right:10px;">
										<label>Total Simpanan Mitra</label>
										<input type="text" class="form-control" id="" name="" value="<?php echo format_rupiah($save); ?>" disabled />
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group" style="padding:10px; margin-top:-10px; margin-left:10px; margin-right:10px;">
										<label>Total Disimpan</label>
										<input type="text" class="form-control" id="" name="" value="<?php echo format_rupiah($save2); ?>" disabled />
									</div>
								</div> -->
							</div>
							
							<div class="row">
								<form action="pay_mitra_salary_action.php?task=pay&date=<?php echo $date; ?>" class="form-horizontal" id="formTransOut" name = "formTransOut" method="POST" >
									<fieldset>
										<div class="col-md-6">
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Nanang Budiono (B001)</label>
												<input id="" name="" value="<?php echo format_rupiah($B001); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Putut Sugiyarto (B002)</label>
												<input id="" name="" value="<?php echo format_rupiah($B002); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah M. Abd. Muid (B003)</label>
												<input id="" name="" value="<?php echo format_rupiah($B003); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Moh. Muid Faruq (B004)</label>
												<input id="" name="" value="<?php echo format_rupiah($B004); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Mat Rizal (B005)</label>
												<input id="" name="" value="<?php echo format_rupiah($B005); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Sulaiman(B006)</label>
												<input id="" name="" value="<?php echo format_rupiah($B006); ?>" type="text" class="form-control"  disabled />
											</div>
											
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Markum (S001)</label>
												<input id="" name="" value="<?php echo format_rupiah($S001); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Veri (S002)</label>
												<input id="" name="" value="<?php echo format_rupiah($S002); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Arifin (S003)</label>
												<input id="" name="" value="<?php echo format_rupiah($S003); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Jastro (S004)</label>
												<input id="" name="" value="<?php echo format_rupiah($S004); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Ahmad Nurkholis (S005)</label>
												<input id="" name="" value="<?php echo format_rupiah($S005); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Nawari (S006)</label>
												<input id="" name="" value="<?php echo format_rupiah($S006); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Muis Hariyanto (S007)</label>
												<input id="" name="" value="<?php echo format_rupiah($S007); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Imam (S008)</label>
												<input id="" name="" value="<?php echo format_rupiah($S008); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Muhammad (S009)</label>
												<input id="" name="" value="<?php echo format_rupiah($S009); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Feru (S010)</label>
												<input id="" name="" value="<?php echo format_rupiah($S010); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Durasid (S011)</label>
												<input id="" name="" value="<?php echo format_rupiah($S011); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Sumiran (S012)</label>
												<input id="" name="" value="<?php echo format_rupiah($S012); ?>" type="text" class="form-control"  disabled />
											</div>
											
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Darwanto (M01)</label>
												<input id="" name="" value="<?php echo format_rupiah($M01); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Tri W (M02)</label>
												<input id="" name="" value="<?php echo format_rupiah($M02); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah Abdul R (M03)</label>
												<input id="" name="" value="<?php echo format_rupiah($M03); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Upah M. Rois (M04)</label>
												<input id="" name="" value="<?php echo format_rupiah($M04); ?>" type="text" class="form-control"  disabled />
											</div>
										</div>
										
										<div class="col-md-6">
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xB001; ?>" id="B001" name="B001">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xB002; ?>" id="B002" name="B002">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xB003; ?>" id="B003" name="B003">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xB004; ?>" id="B004" name="B004">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xB005; ?>" id="B005" name="B005">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xB006; ?>" id="B006" name="B006">
											</div>
											
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS001; ?>" id="S001" name="S001">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS002; ?>" id="S002" name="S002">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS003; ?>" id="S003" name="S003">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS004; ?>" id="S004" name="S004">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS005; ?>" id="S005" name="S005">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS006; ?>" id="S006" name="S006">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS007; ?>" id="S007" name="S007">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS008; ?>" id="S008" name="S008">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS009; ?>" id="S009" name="S009">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS010; ?>" id="S010" name="S010">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS011; ?>" id="S011" name="S011">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xS012; ?>" id="S012" name="S012">
											</div>
											
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xM01; ?>" id="M01" name="M01">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xM02; ?>" id="M02" name="M02">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xM03; ?>" id="M03" name="M03">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Dibagikan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $xM04; ?>" id="M04" name="M04">
											</div>
										</div>

										<!-- <div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Nanang Budiono (B001)</label>
												<input id="" name="" value="<?php echo format_rupiah($SB001); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Putut Sugiyarto (B002)</label>
												<input id="" name="" value="<?php echo format_rupiah($SB002); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Abd. Muid (B003)</label>
												<input id="" name="" value="<?php echo format_rupiah($SB003); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Moh. Muid Faruq (B004)</label>
												<input id="" name="" value="<?php echo format_rupiah($SB004); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Mat Rizal (B005)</label>
												<input id="" name="" value="<?php echo format_rupiah($SB005); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Sulaiman(B006)</label>
												<input id="" name="" value="<?php echo format_rupiah($SB006); ?>" type="text" class="form-control"  disabled />
											</div>
											
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Markum (S001)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS001); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Veri (S002)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS002); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Arifin (S003)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS003); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Jastro (S004)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS004); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Ahmad Nurkholis (S005)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS005); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Nawari (S006)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS006); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Muis Hariyanto (S007)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS007); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Imam (S008)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS008); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Muhammad (S009)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS009); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Feru (S010)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS010); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Durasid (S011)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS011); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Sumiran (S012)</label>
												<input id="" name="" value="<?php echo format_rupiah($SS012); ?>" type="text" class="form-control"  disabled />
											</div>
											
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Darwanto (M01)</label>
												<input id="" name="" value="<?php echo format_rupiah($SM01); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Tri W (M02)</label>
												<input id="" name="" value="<?php echo format_rupiah($SM02); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan Abdul R (M03)</label>
												<input id="" name="" value="<?php echo format_rupiah($SM03); ?>" type="text" class="form-control"  disabled />
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:10px; margin-right:5px;">
												<label>Simpanan M. Rois (M04)</label>
												<input id="" name="" value="<?php echo format_rupiah($SM04); ?>" type="text" class="form-control"  disabled />
											</div>

										</div>
										
										<div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yB001; ?>" id="SB001" name="SB001">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yB002; ?>" id="SB002" name="SB002">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yB003; ?>" id="SB003" name="SB003">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yB004; ?>" id="SB004" name="SB004">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yB005; ?>" id="SB005" name="SB005">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yB006; ?>" id="SB006" name="SB006">
											</div>

											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS001; ?>" id="SS001" name="SS001">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS002; ?>" id="SS002" name="SS002">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS003; ?>" id="SS003" name="SS003">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS004; ?>" id="SS004" name="SS004">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS005; ?>" id="SS005" name="SS005">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS006; ?>" id="SS006" name="SS006">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS007; ?>" id="SS007" name="SS007">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS008; ?>" id="SS008" name="SS008">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS009; ?>" id="SS009" name="SS009">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS010; ?>" id="SS010" name="SS010">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS011; ?>" id="SS011" name="SS011">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" placeholder="0" value="<?php echo $yS012; ?>" id="SS012" name="SS012">
											</div>
											

											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" value="<?php echo $yM01; ?>" id="SM01" name="SM01">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" value="<?php echo $yM02; ?>" id="SM02" name="SM02">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" value="<?php echo $yM03; ?>" id="SM03" name="SM03">
											</div>
											<div class="form-group" style="padding:10px; margin-top:-15px; margin-left:5px; margin-right:10px;">
												<label>Total Disimpan</label>
												<input type="number" min="0" class="form-control" value="<?php echo $yM04; ?>" id="SM04" name="SM04">
											</div> -->

										</div>
										<div class="form-group" style="padding:10px; margin-left:10px; margin-right:10px; margin-top:-10px; margin-bottom:0px;">
											<button type="submit" class="btn btn-block btn-primary"><i class="fa fa-floppy-o"></i>&nbsp;Simpan</button>
										</div>
									</fieldset>
								</form>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	

    <!-- jQuery -->
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
</body>
</html>