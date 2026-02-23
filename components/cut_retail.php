<style>
	/* ================= BUTTON SCISSOR ================= */

	.btn.btn-cut{
		margin-left:8px;
		background:#ec9841;
		border-radius:50%;
		padding:8px 9px;
		cursor:pointer;
		box-shadow:0 4px 15px rgba(0,0,0,0.2);
		transition:all 0.3s;
		position:relative;
		overflow:hidden;
	}

	.btn.btn-cut i{
		color:#fff;
		font-size:18px;
	}

	.btn.btn-cut:hover{
		transform:scale(1.1);
		box-shadow:0 6px 25px rgba(0,0,0,0.3);
	}


	/* ================= BLINK ================= */

	.btn.btn-cut.blink{
		animation:blinkBg 1s infinite;
	}

	@keyframes blinkBg{

		0%{
			background:#ec9841;
		}

		50%{
			background:rgba(255,0,0,0.9);
			box-shadow:
				0 0 0 3px rgba(255,0,0,0.3),
				0 0 20px rgba(255,0,0,0.9),
				0 4px 15px rgba(0,0,0,0.3);
		}

		100%{
			background:#ec9841;
		}

	}


	/* ================= MODAL ================= */

	.centeng-modal .modal-dialog{
		width:900px;
		margin-top:40px;
	}

	.centeng-modal .modal-content{
		border-radius:20px;
		border:none;
		box-shadow:0 15px 40px rgba(0,0,0,0.2);
		overflow:hidden;
	}

	.modal-header{
		background:linear-gradient(135deg,#4e73df,#224abe);
		color:#fff;
		border:none;
	}

	.modal-header .close{
		color:#fff;
		opacity:1;
	}


	/* ================= HEADER ================= */

	.centeng-header{
		padding:15px;
		background:#f8f9fc;
		border-bottom:1px solid #eee;
	}

	.centeng-header-row{
		display:flex;
		align-items:center;
	}


	/* SEARCH */

	.centeng-search-wrapper{
		position:relative;
	}

	.centeng-search-icon{
		position:absolute;
		left:15px;
		top:50%;
		transform:translateY(-50%);
		color:#aaa;
	}

	.centeng-search-input{
		padding-left:40px;
		padding-right:35px;
		border-radius:30px;
		height:38px;
	}

	.centeng-search-clear{
		position:absolute;
		right:12px;
		top:50%;
		transform:translateY(-50%);
		color:#999;
		cursor:pointer;
	}

	.centeng-search-clear:hover{
		color:red;
	}


	/* COUNTER */

	.centeng-counter-box{
		display:inline-flex;
		align-items:center;
		background:linear-gradient(135deg,#ff9800,#ff5722);
		padding:8px 18px;
		border-radius:30px;
		color:#fff;
		font-size:13px;
		box-shadow:0 4px 15px rgba(0,0,0,0.2);
	}

	.counter-item{
		display:flex;
		align-items:center;
		gap:6px;
	}

	.counter-number{
		font-size:16px;
		font-weight:bold;
	}

	.counter-divider{
		width:1px;
		height:20px;
		background:rgba(255,255,255,0.4);
		margin:0 12px;
	}


	/* ================= CONTAINER ================= */

	.centeng-container{
		max-height:450px;
		overflow:auto;
		padding:15px;
		background:#f4f6fb;
	}


	/* ================= CARD ================= */

	.centeng-card{
		background:#fff;
		border-radius:18px;
		padding:15px;
		margin-bottom:12px;
		box-shadow:0 5px 20px rgba(0,0,0,0.08);
		border-left:5px solid #ff9800;
        transform 0.25s ease,
		box-shadow 0.25s ease,
		border-color 0.25s ease,
		background 0.25s ease;
	}

    /* Hover utama */
    .centeng-card:hover{

        transform:translateY(-6px) scale(1.01);

        box-shadow:
            0 15px 35px rgba(0,0,0,0.15),
            0 5px 15px rgba(0,0,0,0.08);

        border-left:5px solid #ff5722;

        background:#ffffff;

    }


    /* Glow kiri */

    .centeng-card::before{

        content:'';

        position:absolute;

        left:0;
        top:0;
        bottom:0;

        width:0;

        background:linear-gradient(
            to bottom,
            #ff9800,
            #ff5722
        );

        border-radius:18px 0 0 18px;

        transition:width 0.25s ease;

    }


    .centeng-card:hover::before{

        width:6px;

    }


    /* Avatar ikut animasi */

    .centeng-card:hover .centeng-avatar{

        transform:scale(1.08);

        box-shadow:
            0 8px 20px rgba(0,0,0,0.25);

    }


    /* Text lebih tajam */

    .centeng-card:hover .centeng-name{

        color:#ff5722;

    }

	.centeng-avatar{
		width:50px;
		height:50px;
		border-radius:50%;
		background:linear-gradient(135deg,#667eea,#764ba2);
		display:flex;
		align-items:center;
		justify-content:center;
		color:#fff;
		font-size:20px;
	}

	.centeng-name{
		font-weight:bold;
		font-size:15px;
	}

	.centeng-lapak{
		font-size:12px;
		color:#888;
	}

	.centeng-info{
		font-size:12px;
		color:#555;
	}


	/* EMPTY */

	.centeng-empty{
		text-align:center;
		padding:40px;
		color:#999;
	}

	.centeng-empty i{
		font-size:50px;
		color:#ddd;
	}

</style>


<?php
/* ================= PERIODE ================= */

$year=date('Y');
$month=date('m');
$day=date('d');

if($day<=7){$periodStart=1;$periodEnd=7;}
elseif($day<=14){$periodStart=8;$periodEnd=14;}
elseif($day<=21){$periodStart=15;$periodEnd=21;}
elseif($day<=28){$periodStart=22;$periodEnd=28;}
else{$periodStart=29;$periodEnd=date('t');}

$periodStartDate="$year-$month-".str_pad($periodStart,2,'0',STR_PAD_LEFT)." 00:00:00";
$periodEndDate="$year-$month-".str_pad($periodEnd,2,'0',STR_PAD_LEFT)." 23:59:59";


/* ================= CEK BLINK ================= */

$blinkQuery=mysql_query("

	SELECT COUNT(*) as total

	FROM weighing w

	JOIN merchant_retail mr ON mr.merchant_id=w.merchant_id

	WHERE
		w.weighing_sts NOT IN ('Batal','Lunas')
		AND w.weighing_sts2!='Tanpa Timbang'
		AND COALESCE(w.weighing_sts5,'') = ''
        AND w.date_check
		BETWEEN '$periodStartDate' AND '$periodEndDate'
");

$blinkData=mysql_fetch_array($blinkQuery);

$blinkClass=($blinkData['total']>0)?"blink":"";
?>


<!-- BUTTON -->

<div class="btn btn-cut <?=$blinkClass?>" data-toggle="modal" data-target="#centengModal">

	<i class="fa fa-scissors"></i>

</div>



<!-- MODAL -->

<div class="modal fade centeng-modal" id="centengModal">

	<div class="modal-dialog">

		<div class="modal-content">


			<div class="modal-header">

				<button class="close" data-dismiss="modal" style="margin-top:5px;">&times;</button>

				<h4>

					<i class="fa fa-scissors"></i>&nbsp;

					Belum Terpotong Centeng

				</h4>

			</div>



			<!-- HEADER -->

			<div class="centeng-header">

				<div class="row centeng-header-row">


					<div class="col-md-8">

						<div class="centeng-search-wrapper">

							<i class="fa fa-search centeng-search-icon"></i>

							<input
								type="text"
								id="search"
								class="form-control centeng-search-input"
								placeholder="Search pedagang atau nota..."
							>

							<i class="fa fa-times centeng-search-clear" id="clearSearchData" style="display:none"></i>

						</div>

					</div>



					<div class="col-md-4 text-right">

						<div class="centeng-counter-box" id="counter">

							<?php

							$q=mysql_query("

								SELECT

									COUNT(DISTINCT w.merchant_id) total_pedagang,
									COUNT(DISTINCT w.weighing_id) total_nota

								FROM weighing w

								JOIN merchant_retail mr ON mr.merchant_id=w.merchant_id

								WHERE

									w.weighing_sts NOT IN ('Batal','Lunas')
									AND w.weighing_sts2!='Tanpa Timbang'
									AND COALESCE(w.weighing_sts5,'') = ''
									AND w.date_check
									BETWEEN '$periodStartDate' AND '$periodEndDate'

							");

							$row=mysql_fetch_array($q);

							?>

							<span class="counter-item">

								<i class="fa fa-user"></i>

								<span class="counter-number">

									<?=$row['total_pedagang']?>

								</span>

								Pedagang

							</span>


							<span class="counter-divider"></span>


							<span class="counter-item">

								<i class="fa fa-file-text"></i>

								<span class="counter-number">

									<?=$row['total_nota']?>

								</span>

								Nota

							</span>


						</div>

					</div>


				</div>

			</div>



			<!-- LIST -->

			<div class="centeng-container" id="list">

				<?php

				$query=mysql_query("

					SELECT

						m.merchant_id,
						m.merchant_name,

						GROUP_CONCAT(
							DISTINCT l.lapak_name
							ORDER BY l.lapak_name
							SEPARATOR ', '
						) AS lapak,

						GROUP_CONCAT(

							DISTINCT CONCAT(
                                'TG',
								w.weighing_id,
								' (',
								DATE_FORMAT(w.date_check,'%d %b %Y'),
								')'
							)

							ORDER BY w.date_check
							SEPARATOR ', '

						) AS weighing_id

					FROM merchant m

					JOIN merchant_retail mr
					ON mr.merchant_id=m.merchant_id


					LEFT JOIN weighing w
					ON w.merchant_id=m.merchant_id
					AND w.weighing_sts NOT IN ('Batal','Lunas')
					AND w.weighing_sts2!='Tanpa Timbang'
                    AND COALESCE(w.weighing_sts5,'') = ''
					AND w.date_check
					BETWEEN '$periodStartDate' AND '$periodEndDate'


					LEFT JOIN rent r
					ON r.merchant_id=m.merchant_id
					AND r.rent_sts!='Batal'
					AND r.rent_sts2 NOT IN ('Batal','Pindah')


					LEFT JOIN lapak l
					ON l.lapak_id=r.lapak_id


					GROUP BY m.merchant_id

					HAVING weighing_id IS NOT NULL

					ORDER BY m.merchant_name

				");

                $totalData = mysql_num_rows($query);

                if($totalData==0){
                ?>

                    <div class="centeng-empty" id="emptyData">

                        <i class="fa fa-inbox"></i>

                        <div class="centeng-empty-title">
                            Tidak ada data
                        </div>

                        <div class="centeng-empty-sub">
                            Semua nota sudah terpotong
                        </div>

                    </div>

                <?php
                }


				while($d=mysql_fetch_array($query)){

				?>



				<div class="centeng-card" id="card<?=$d['merchant_id']?>">

					<div class="row">


						<div class="col-xs-1">

							<div class="centeng-avatar">

								<i class="fa fa-user"></i>

							</div>

						</div>



						<div class="col-xs-11">

							<div class="centeng-name">

								<?=$d['merchant_name']?>

							</div>



							<div class="centeng-lapak">

								<?=$d['lapak']?>

							</div>



							<div class="centeng-info">

								Nota : <?=$d['weighing_id']?>

							</div>


						</div>


					</div>

				</div>



				<?php } ?>


                <div class="centeng-empty" id="emptySearchData" style="display:none">

                    <i class="fa fa-search"></i>

                    <div class="centeng-empty-title">
                        Data tidak ditemukan
                    </div>

                    <div class="centeng-empty-sub">
                        Coba kata kunci lain
                    </div>

                </div>
			</div>


		</div>

	</div>

</div>



<script>

	$("#search").keyup(function(){

		let value = $(this).val().toLowerCase();

		let totalCard = $(".centeng-card").length;
		let totalFound = 0;


		/* ================= FILTER CARD ================= */

		$(".centeng-card").each(function(){

			let found = $(this).text().toLowerCase().indexOf(value) > -1;

			$(this).toggle(found);

			if(found) totalFound++;

		});


		/* ================= CLEAR BUTTON ================= */

		if(value!='')
			$("#clearSearchData").show();
		else
			$("#clearSearchData").hide();


		/* ================= EMPTY STATE CONTROL ================= */

		if(totalCard == 0){

			/* DATA MEMANG KOSONG */

			$("#emptyData").show();
			$("#emptySearchData").hide();

		}
		else if(totalFound == 0){

			/* DATA ADA TAPI SEARCH TIDAK DITEMUKAN */

			$("#emptyData").hide();
			$("#emptySearchData").show();

		}
		else{

			/* DATA ADA DAN DITEMUKAN */

			$("#emptyData").hide();
			$("#emptySearchData").hide();

		}

	});



	/* ================= CLEAR SEARCH ================= */

	$("#clearSearchData").click(function(){

		// kosongkan input
		$("#search").val('');

		// sembunyikan tombol clear
		$(this).hide();

		// tampilkan semua card kembali
		$(".centeng-card").show();

		// sembunyikan empty state
		$("#emptySearchData").hide();
		$("#emptyData").hide();

		// fokus kembali ke input
		$("#search").focus();

	});


</script>