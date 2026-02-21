<?php

	/* ================= SUMMARY ================= */

	$summary=mysql_fetch_array(mysql_query("
			SELECT
				SUM(CASE WHEN date_pay IS NULL THEN 1 ELSE 0 END) AS belum,
				SUM(CASE WHEN date_pay IS NOT NULL THEN 1 ELSE 0 END) AS sudah
			FROM weighing
			WHERE weighing_sts2='Tanpa Timbang'
                AND weighing_sts != 'Batal'
				AND date_check BETWEEN '$periodStartDate' AND '$periodEndDate'
			"));

					?>

    <div class="card-summary-centeng">
        <div class="icon-retail">
            <i class="fa fa-calendar"></i>
        </div>

        <div class="content">
            <div class="periode">
                <?= date('d M Y',strtotime($periodStartDate)); ?> - <?= date('d M
                Y',strtotime($periodEndDate)); ?>
            </div>

            <div class="row-total">
                <div class="belum">
                    Belum Bayar :
                    <span
                        ><?= number_format($summary['belum']); ?>
                        Pedagang</span
                    >
                </div>

                <div class="sudah">
                    Sudah Bayar :
                    <span
                        ><?= number_format($summary['sudah']); ?>
                        Pedagang</span
                    >
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php 
        $query=mysql_query("
            SELECT 
                m.merchant_id,
                m.merchant_name,

                w.weighing_id,
                w.cost_tonase AS total_tagihan,
                MAX(w.date_pay) AS date_pay,

                GROUP_CONCAT(
                    DISTINCT l.lapak_name 
                    ORDER BY l.lapak_name 
                    SEPARATOR ', '
                ) AS lapak

            FROM merchant m

            JOIN merchant_retail mr 
                ON mr.merchant_id=m.merchant_id

            LEFT JOIN weighing w 
                ON w.merchant_id=m.merchant_id
                AND w.weighing_sts2='Tanpa Timbang'
                AND w.date_check BETWEEN '$periodStartDate' AND '$periodEndDate'

            LEFT JOIN rent r 
                ON r.merchant_id=m.merchant_id
                AND r.rent_sts!='Batal'
                AND r.rent_sts2 NOT IN('Batal','Pindah')

            LEFT JOIN lapak l 
                ON l.lapak_id=r.lapak_id

            GROUP BY m.merchant_id

            ORDER BY m.merchant_name
            ");
        while($d=mysql_fetch_array($query)){ ?>
        <div class="col-md-6">
            <div class="card-centeng">
                <div class="icon-retail">
                    <i class="fa fa-user"></i>
                </div>

                <div class="content">
                    <div class="nama"><?= $d['merchant_name']; ?></div>

                    <div class="lapak">
                        <?= $d['lapak']; ?> |
                        (TG<?= $d['weighing_id']; ?>)
                    </div>
                </div>

                <div class="right">
                    <div class="tagihan">
                        Rp <?= number_format($d['total_tagihan'],0,',','.'); ?>
                    </div>

                    <div
                        class="tanggal"
                        style="color:<?= $d['date_pay']?'#28a745':'#dc3545'; ?>"
                    >
                        <i
                            class="fa <?= $d['date_pay']?'fa-check-circle':'fa-times-circle'; ?>"
                        ></i>

                        <?= $d['date_pay'] ?date('d M Y',strtotime($d['date_pay']))
                        :'Belum Terbayar'; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Empty State -->
	<div id="emptySearch" class="empty-centeng" style="display:none;">
		<div class="icon">
			<i class="fa fa-search"></i>
		</div>

		<div class="title">
			Data tidak ditemukan
		</div>

		<div class="desc">
			Coba gunakan kata kunci lain
		</div>
	</div>
