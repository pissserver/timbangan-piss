<?php
	session_start();
	include "connection.php";
	//Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	$checker 	= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
?>

<style>
	.card-box {
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
	<!-- CSS Search dropdown -->
	<link href="css/plugins/chosen/chosen.css" rel="stylesheet" />
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
							<div class="row">
								<form action="weighing_action.php?task=createMerchantRetail" class="form-horizontal" id="formWeighingIn" name = "formWeighingIn" method="POST">
								<fieldset>
									<div class="col-md-6">
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
									<!-- <div class="col-md-4">
										<div class="form-group" style="padding:10px; margin-top:-10px;">
											<label>Barang</label>
											<select class="form-control chzn-select" name="goods" id="goods" required>
												<option value="">- Pilih Barang -</option>
												<?php 
													$sql2 		= mysql_query("select goods_id, goods_name from goods order by goods_name");
													while($d2 	= mysql_fetch_array($sql2))
												{?>
													<option value="<?php echo $d2['goods_id'];?>"><?php echo $d2['goods_name'];?></option>
												<?php } ?>
											</select>
										</div>
									</div> -->
									<div class="col-md-6">
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
							<form action="htmltopdf/nota_tagihan_retail.php" method="POST" target="_blank">
								<div class="row" style="padding:10px;">

								<?php 
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

								
								while($d = mysql_fetch_array($query)) {

									$lpk 	= executeScalar("select 	l.lapak_name 
																					from 	rent r, 
																							lapak l 
																					where 	r.lapak_id = l.lapak_id and 
																							r.merchant_id = '".$d['merchant_id']."' 
																							and (r.rent_sts != 'Batal' and r.rent_sts2 not in ('Batal', 'Pindah')) limit 1");
														$nick	= $d['merchant_nick'];
														if($nick!=''){
															$nick = $nick.' / ';
														}else{
															$nick = '';
														}
								?>

								<div class="col-md-4">
									<div class="card-box">

										<input type="hidden" name="merchant[]" value="<?php echo $d['merchant_id']; ?>">
										<input type="text" class="form-control"
											value="<?php echo $d['merchant_name'] . ' (' . $lpk . ')'; ?>" 
											readonly>

										<input type="hidden" name="goods[]" value="79">
										<input style="margin-top:10px; margin-bottom: 10px;" type="text" class="form-control" value="Centeng" readonly>

										<div class="toggle-wrapper">
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

								<div class="form-group" style="padding:10px; margin-bottom: 0px;">
									<center><button type="submit" id="btnCetakAll" class="btn btn-primary btn-block" onclick="postAndReload()">Buat Data Tagihan Retail Keseluruhan &nbsp;<i class="fa fa-print"></i></button></center>
								</div>
							</form>
						</div>
                    </div>
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
					<button type="button" class="close text-white" data-dismiss="modal">
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

	<!-- Modal Ubah -->
	<div class="modal fade" id="modalUbah" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			    <!-- Modal Content modal_edit_weightin.php -->
			</div>
		</div>
	</div>

	<!-- Modal Password -->
	<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><img src='assets/image/Delete.png' width='25' height='25'/></button>
					<h4 id="header_password">Ganti Password</h4>
				</div>
				<div class="modal-body">
					<div id="data_password" style="margin-bottom: 0px;">
						<div class="alert alert-info" onclick="clearNotif()"><h3><i class="fa fa-spinner fa-spin"></i> Loading...</h3></div>
					</div>
					<input type="hidden" id="value_id" />
				</div>
				<div class="modal-footer">
				</div>
			</div>
		</div>
	</div>


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
		function postAndReload() {
			setTimeout( function() {
				location.reload();
			}, 100);
		}
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