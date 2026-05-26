<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	$checker 		= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
	
?>

<style>
	.input-icon-wrapper{
		display:flex;
		align-items:center;
		gap:5px;
	}

	.input-icon{
		width:44px;
		height:38px;
		display:flex;
		align-items:center;
		justify-content:center;
		background:#428bca;
		color:#fff;
		border-radius:10px;
		font-size:16px;
		box-shadow:0 4px 10px rgba(0,0,0,0.15);
	}

	.payment-notfound{
		min-height: 420px;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		text-align: center;
		margin-top: -90px;
	}

	.payment-notfound dotlottie-wc{
		width: 420px !important;
		height: 420px !important;
		margin-bottom: -90px;
	}

	/* Smart Tips */
	#smartTips{

		position:relative;

		height:58px;

		border-radius:18px;

		margin-bottom:18px;
		padding:0 22px;

		display:flex;
		align-items:center;

		overflow:hidden;

		background:
			linear-gradient(
				135deg,
				rgba(37,99,235,0.95),
				rgba(29,78,216,0.88),
				rgba(15,23,42,0.92)
			);

		backdrop-filter:blur(14px);

		box-shadow:
			0 10px 30px rgba(37,99,235,0.25),
			inset 0 1px 0 rgba(255,255,255,0.15);

	}

	#smartTips::before{

		content:'';

		position:absolute;

		top:0;
		left:-150%;

		width:60%;
		height:100%;

		background:
			linear-gradient(
				90deg,
				transparent,
				rgba(255,255,255,0.15),
				transparent
			);

		animation:shineMove 5s infinite;
	}

	.smartTips-icon{

		min-width:38px;
		height:38px;

		border-radius:12px;

		background:rgba(255,255,255,0.12);

		display:flex;
		align-items:center;
		justify-content:center;

		margin-right:16px;

		font-size:18px;
		color:#fff;

		box-shadow:
			inset 0 1px 0 rgba(255,255,255,0.2);

	}

	.smartTips-wrapper{

		position:relative;

		height:24px;

		overflow:hidden;

		flex:1;
	}

	.smartTips-slider{

		position:absolute;

		width:100%;

		transition:transform 0.8s cubic-bezier(0.77, 0, 0.175, 1);
	}

	.tip-item{

		height:24px;
		line-height:24px;

		color:#fff;

		font-size:14px;
		font-weight:600;

		letter-spacing:0.3px;

		opacity:0.96;
	}

	@keyframes shineMove{

		0%{
			left:-150%;
		}

		100%{
			left:150%;
		}
	}

	/* LIVE LABEL */
	#smartTips .smart-label{
		position:absolute;
		right:16px;
		top:50%;
		transform:translateY(-50%);
		font-size:11px;
		padding:6px 13px;
		border-radius:40px;
		background:
			linear-gradient(
				135deg,
				#f59e0b,
				#ea580c
			);
		color:#fff;
		letter-spacing:1px;
		font-weight:800;
		box-shadow:
			0 0 0 rgba(245,158,11,0.7);
		animation:pulseLive 1.8s infinite;
		display:flex;
		align-items:center;
	}

	/* RED DOT */
	#smartTips .smart-label::before{
		content:'';
		width:7px;
		height:7px;
		border-radius:50%;
		background:#fff;
		margin-right:7px;
		animation:blinkDot 1s infinite;
	}

	/* PULSE */
	@keyframes pulseLive{

		0%{
			transform:translateY(-50%) scale(1);
			box-shadow:
				0 0 0 0 rgba(245,158,11,0.6);
		}

		70%{
			transform:translateY(-50%) scale(1.05);
			box-shadow:
				0 0 0 12px rgba(245,158,11,0);
		}

		100%{
			transform:translateY(-50%) scale(1);
			box-shadow:
				0 0 0 0 rgba(245,158,11,0);
		}
	}

	/* BLINK */
	@keyframes blinkDot{

		0%,100%{
			opacity:1;
			transform:scale(1);
		}

		50%{
			opacity:0.3;
			transform:scale(0.7);
		}
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
	<link rel="icon" href="img/icon.png">
    <link rel="shortcut icon" href="img/icon.png" />
    <title>Timbangan Masuk | PISS</title>
	<?php echo headScript(); ?>
</head>
<body>
    <div id="wrapper">
		<?php include "components/sidebar.php"; ?>

        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
					<br /><br><br><br>
					<div id="smartTips">
						<div class="smartTips-icon">
							<i class="fa fa-lightbulb-o"></i>
						</div>

						<div class="smartTips-wrapper">
							<div class="smartTips-slider" id="smartTipsSlider">
								<div class="tip-item">
									Klik tombol "Masuk" hanya sekali untuk menyimpan data, agar tidak terjadi duplikasi data kendaraan
								</div>

								<div class="tip-item">
									Ketika pedagang terblokir dan tidak bisa diinput, konsultasikan dengan pendamping pedagang untuk melakukan pembukaan blokir
								</div>

								<div class="tip-item">
									Pastikan mitra sudah dipilih dengan benar sebelum dijadikan nota tagihan
								</div>

								<div class="tip-item">
									Hindari banyak kesalahan saat input data, karena akan mempengaruhi laporan dan analisis data
								</div>

								<div class="tip-item">
									Saat akan cut off perhari di jam 23.45, pastikan semua data yang belum digabungkan sudah digabung semua
								</div>

								<div class="tip-item">
									Untuk nota dengan pedagang berstatus "CENTENG", jangan lupa untuk potong tarif dengan tarif centeng
								</div>

								<div class="tip-item">
									Lakukan pengajuan revisi di menu "Revisi" jika terjadi kesalahan input data, jangan lupa untuk menyertakan bukti yang jelas
								</div>
							</div>

							<div class="smart-label">
								Smart Tips
							</div>
						</div>
					</div>
				
                    <div class="panel panel-primary">
                        <div class="panel-heading">
							<div class="row">
								<div class="col-md-6">
									<i class="fa fa-truck fa-fw"></i> Kendaraan Masuk
								</div>
								<!-- <div class="col-md-6 text-right">
									<a href="modal_weight_in.php" style="text-decoration: none;" class="modalWeightIn" data-toggle="modal" data-target="#modalWeightIn">
										<button style="border: 1px solid #fff;" class="btn btn-primary"><i class="fa fa-arrow-circle-down"></i>&nbsp;Masuk Manual</button>
									</a>
									<a href="modal_weight_out.php" style="text-decoration: none;" class="modalWeightOut" data-toggle="modal" data-target="#modalWeightOut">
										<button style="border: 1px solid #fff;" class="btn btn-primary"><i class="fa fa-arrow-circle-up"></i>&nbsp;Masuk & Keluar Manual</button>
									</a>
								</div> -->
							</div>
                        </div>
                        <div class="panel-body">
							<div class="row" style="margin-bottom: -30px;">
								<form class="form-horizontal" id="formWeighingIn" name = "formWeighingIn" method="POST" action="weighing_action.php?task=masuk">
									<fieldset>
										<div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Nomor Polisi</label>

												<div class="input-icon-wrapper">
													<div class="input-icon">
														<i class="fa fa-car"></i>
													</div>

													<input type="text"
														class="form-control"
														id="nopol"
														name="nopol"
														placeholder="Nopol Kendaraan"
														autofocus
														required>
												</div>

											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Pedagang</label>

												<div class="input-icon-wrapper">
													<div class="input-icon">
														<i class="fa fa-user"></i>
													</div>

													<select class="form-control chzn-select" name="merchant" id="merchant" required>
														<option value="" selected disabled>-- Pilih Pedagang --</option>
														<?php 
														$sql=mysql_query("select merchant_id, merchant_name, merchant_nick from merchant 
														where merchant_status='Aktif' order by merchant_nick, merchant_name");
														while($d1=mysql_fetch_array($sql)){
															$lpk=executeScalar("select l.lapak_name from rent r, lapak l 
															where r.lapak_id=l.lapak_id and r.merchant_id='".$d1['merchant_id']."'
															and (r.rent_sts!='Batal' and r.rent_sts2 not in ('Batal','Pindah')) limit 1");

															$nick=$d1['merchant_nick'];
															if($nick!=''){ $nick=$nick.' / '; } else { $nick=''; }
														?>
														<option value="<?php echo $d1['merchant_id']; ?>">
															<?php echo $nick.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.($lpk?$lpk:'Tidak Ada Lapak'); ?>
														</option>
														<?php } ?>
													</select>
												</div>

											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:-10px;">
												<label>Barang Komoditi</label>

												<div class="input-icon-wrapper">
													<div class="input-icon">
														<i class="fa fa-cubes"></i>
													</div>

													<select class="form-control chzn-select" name="goods" id="goods" required>
														<option selected disabled>-- Pilih Barang --</option>
														<?php 
														$sql2=mysql_query("select goods_id, goods_name from goods 
														where goods_id!='1' and goods_id!='7' 
														and goods_id!='96' and goods_id!='94' and goods_id!='95'
														order by goods_name");
														while($d2=mysql_fetch_array($sql2)){ ?>
															<option value="<?php echo $d2['goods_id'];?>">
																<?php echo $d2['goods_name'];?>
															</option>
														<?php } ?>
													</select>
												</div>

											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group" style="padding:10px; margin-top:15px;">
												<center><button type="submit" class="btn btn-primary btn-block">Masuk <i class="fa fa-angle-double-right fa-lg"></i></button></center>
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
                            <i class="fa fa-truck fa-fw"></i> Data Kendaraan Masuk
                        </div>
                        <div class="panel-body">
							<div class="row">
								<div class="col-md-12">
									<?php 
										$query 	= mysql_query("select * from weighing 
										where (date_in  between '2016-03-15' and '$date_now') and 
										weighing_sts = 'Masuk' order by weighing_id desc");

										if (mysql_num_rows($query) != 0) { ?>
											<div id="Area1">
												<div class="alert alert-info" onclick="clearNotif()">
													<center><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></center>
												</div>
											</div>
										<?php } else { ?>
											<div class="payment-notfound">
												<dotlottie-wc
													src="https://lottie.host/37c6dc2e-7bcc-43ae-a7ce-ef1bcd4b9e8e/DEKZ2wYgU8.lottie"
													autoplay
													loop>
												</dotlottie-wc>

												<h3>
													<b>Belum Ada Kendaraan Masuk</b>
												</h3>
											</div>
										<?php }
									?>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

	<!-- Modal Weight In -->
	<div class="modal fade" id="modalWeightIn" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			    <!-- Modal Content modal_weight_in.php -->
			</div>
		</div>
	</div>

	<!-- Modal Weight Out -->
	<div class="modal fade" id="modalWeightOut" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			    <!-- Modal Content modal_weight_out.php -->
			</div>
		</div>
	</div>

	<!-- Modal Ubah -->
	<div class="modal fade" id="modalUbah" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			    <!-- Modal Content modal_edit_weightin.php -->
			</div>
		</div>
	</div>

	<!-- Smart Tips -->
	<script>
		const slider = document.getElementById('smartTipsSlider');
		const firstItem = slider.children[0].cloneNode(true);
		slider.appendChild(firstItem);
		const totalItems = slider.children.length;
		let currentIndex = 0;
		const itemHeight = 24;

		setInterval(() => {
			currentIndex++;
			slider.style.transition = 'transform 0.8s cubic-bezier(0.77, 0, 0.175, 1)';
			slider.style.transform = `translateY(-${currentIndex * itemHeight}px)`;

			// jika sudah clone terakhir
			if(currentIndex === totalItems - 1){
				setTimeout(() => {
					slider.style.transition = 'none';
					slider.style.transform = 'translateY(0px)';
					currentIndex = 0;
				}, 900);
			}
		}, 7000);
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
    <script src="js/jquery.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Metis Menu Plugin JavaScript -->
    <script src="js/plugins/metisMenu/metisMenu.min.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/sb-admin-2.js"></script>
	<script src="js/plugins/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
    	$(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
    </script>
	<script>
		$(document).ready(function() {
			$('.modalWeightIn').on('click', function() {
				$('#modalWeightIn .modal-content').html(response);
				$('#modalWeightIn').modal('show');
			});

			$('.modalWeightOut').on('click', function() {
				$('#modalWeightOut .modal-content').html(response);
				$('#modalWeightOut').modal('show');
			});
		});
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