<?php
	include "../connection.php";
	$date		= '2016-9-01';
	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	//$commodity	= $_POST['commodity'];
	//$date 		= date("d-m-Y");
	//Inisialisai total
	$tot_other 	= 0;
	$tot_vegie 	= 0;
	$tot_fruit 	= 0;
	$tot_other_mitra 	= 0;
	$tot_vegie_mitra 	= 0;
	$tot_fruit_mitra 	= 0;
	$tot_cost	= 0;
?>
<page backtop="8mm" backbottom="9mm" backleft="2mm" backright="2mm">
	<page_header>
        <table style="width:95%; border:0px; margin-top:9px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left; width: 50%"></td>
                <td style="text-align: center; width: 25%"></td>
                <td style="text-align: right; width: 25%"></td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table style="width: 95%; border: 0px black; margin-top:9px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left;width: 50%">Tgl Cetak: <?php echo $date; ?></td>
				<td style="text-align: center;width: 25%"></td>
                <td style="text-align: right;width: 25%">Halaman [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
    <div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:0px; margin-right:65px;">
		REKAP TAGIHAN TONASE & MITRA BELUM TERBAYAR (PER TANGGAL)
		<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
	</div>
    <br/>
	<table style="margin-bottom: 5px;font-size:9pt; width:300px;" >
		<tr>
			<td style="width: 35%;">Tanggal Awal</td>
			<td style="width: 65%;">: <?php echo date("d-m-Y", strtotime($date1)); ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Tanggal Akhir</td>
			<td style="width: 65%;">: <?php echo date("d-m-Y", strtotime($date2)); ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:9pt; width:900px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 4%; border: solid 1px black;">NO</th>
			<th style="text-align: center;width: 9%; border: solid 1px black;">TGL TAGIHAN</th>
			<th style="text-align: center;width: 9.9%;border: solid 1px black;">TONASE LAINNYA (RP)</th>
			<th style="text-align: center;width: 9.9%;border: solid 1px black;">TONASE SAYUR (RP)</th>
			<th style="text-align: center;width: 9.9%;border: solid 1px black;">TONASE BUAH (RP)</th>
			<th style="text-align: center;width: 9.9%;border: solid 1px black;">MITRA LAINNYA (RP)</th>
			<th style="text-align: center;width: 9.9%;border: solid 1px black;">MITRA SAYUR (RP)</th>
			<th style="text-align: center;width: 9.9%;border: solid 1px black;">MITRA BUAH (RP)</th>
			<th style="text-align: center;width: 9.9%;border: solid 1px black;">TOTAL (RP)</th>
		</tr>
		<?php
			$sql = "SELECT DATE(w.date_check) AS dateonly, 
						DATE(wm.date_check) AS date_check_mitra
					FROM 
						weighing w
					LEFT JOIN 
						weighing_mitra wm ON DATE(wm.date_check) = DATE(w.date_check)
					WHERE 
						w.date_check BETWEEN '".$date1." 00:00:00' AND '".$date2." 23:59:59' 
						AND w.weighing_sts != 'Batal' 
						AND (w.weighing_sts2 != 'Dipecah' AND w.weighing_sts2 != 'Digabung')
					GROUP BY dateonly";

			$query 	= mysql_query($sql);
			$no 	= 1;
			while($r= mysql_fetch_array($query)){
				$date3 		= date("Y-m-d", strtotime($r['dateonly']));
				$date_check_mitra 		= date("Y-m-d", strtotime($r['date_check_mitra']));
				/*$other	= executeScalar("select sum(w.cost)
					from weighing w, merchant m 
					where DATE(w.date_check) = '".$r['dateonly']."' and
					w.merchant_id = m.merchant_id and 
					m.merchant_status = 'Aktif' and
					m.merchant_type = 'Lainnya' and
					(w.date_check between '".$date." 00:00:00' and '".$r['dateonly']." 23:59:59') and
					(w.date_pay > '".$r['dateonly']."' || w.date_pay is NULL) and
					w.weighing_sts != 'Batal' and
					(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				$vegie	= executeScalar("select sum(w.cost)
					from weighing w, merchant m 
					where DATE(w.date_check) = '".$r['dateonly']."' and
					w.merchant_id = m.merchant_id and 
					m.merchant_status = 'Aktif' and
					m.merchant_type = 'Sayur' and
					(w.date_check between '".$date." 00:00:00' and '".$r['dateonly']." 23:59:59') and
					(w.date_pay > '".$r['dateonly']."' || w.date_pay is NULL) and
					w.weighing_sts != 'Batal' and
					(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				$fruit	= executeScalar("select sum(w.cost)
					from weighing w, merchant m 
					where DATE(w.date_check) = '".$r['dateonly']."' and
					w.merchant_id = m.merchant_id and 
					m.merchant_status = 'Aktif' and
					m.merchant_type = 'Buah' and
					(w.date_check between '".$date." 00:00:00' and '".$r['dateonly']." 23:59:59') and
					(w.date_pay > '".$r['dateonly']."' || w.date_pay is NULL) and
					w.weighing_sts != 'Batal' and
					(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				$cost	= executeScalar("select sum(w.cost)
					from weighing w, merchant m 
					where DATE(w.date_check) = '".$r['dateonly']."' and
					w.merchant_id = m.merchant_id and 
					m.merchant_status = 'Aktif' and
					(w.date_check between '".$date." 00:00:00' and '".$r['dateonly']." 23:59:59') and
					(w.date_pay > '".$r['dateonly']."' || w.date_pay is NULL) and
					w.weighing_sts != 'Batal' and
					(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");*/
				$other = executeScalar("select sum(w.cost_tonase) from weighing w, merchant m 
				where w.merchant_id = m.merchant_id and 
				m.merchant_status = 'Aktif' and
				m.merchant_type = 'Lainnya' and 
				(w.date_check between '".$date." 00:00:00' and '".$date3." 23:59:59') and
				(w.date_pay > '".$date3."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");

				$other_mitra = executeScalar("select 	sum(w.cost) 
										from 	weighing_mitra w, merchant m 
										where 	w.merchant_id = m.merchant_id and 
												m.merchant_status = 'Aktif' and
												m.merchant_type = 'Lainnya' and 
												(w.date_check between '".$date." 00:00:00' and '".$date_check_mitra." 23:59:59') and
												(w.date_pay > '".$date_check_mitra."' || w.date_pay is NULL) and
												w.weighing_sts != 'Batal' and
												(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				
				$vegie = executeScalar("select sum(w.cost_tonase) from weighing w, merchant m 
				where w.merchant_id = m.merchant_id and 
				m.merchant_status = 'Aktif' and
				m.merchant_type = 'Sayur' and 
				(w.date_check between '".$date." 00:00:00' and '".$date3." 23:59:59') and
				(w.date_pay > '".$date3."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");

				$vegie_mitra = executeScalar("select 	sum(w.cost) 
										from 	weighing_mitra w, merchant m 
										where 	w.merchant_id = m.merchant_id and 
												m.merchant_status = 'Aktif' and
												m.merchant_type = 'Sayur' and 
												(w.date_check between '".$date." 00:00:00' and '".$date_check_mitra." 23:59:59') and
												(w.date_pay > '".$date_check_mitra."' || w.date_pay is NULL) and
												w.weighing_sts != 'Batal' and
												(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				
				$fruit = executeScalar("select sum(w.cost_tonase) from weighing w, merchant m 
				where w.merchant_id = m.merchant_id and 
				m.merchant_status = 'Aktif' and
				m.merchant_type = 'Buah' and 
				(w.date_check between '".$date." 00:00:00' and '".$date3." 23:59:59') and
				(w.date_pay > '".$date3."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");

				$fruit_mitra = executeScalar("select	sum(w.cost) 
										from 	weighing_mitra w, merchant m 
										where 	w.merchant_id = m.merchant_id and 
												m.merchant_status = 'Aktif' and
												m.merchant_type = 'Buah' and 
												(w.date_check between '".$date." 00:00:00' and '".$date_check_mitra." 23:59:59') and
												(w.date_pay > '".$date_check_mitra."' || w.date_pay is NULL) and
												w.weighing_sts != 'Batal' and
												(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				
				$cost = executeScalar("select sum(w.cost_tonase) from weighing w, merchant m 
				where w.merchant_id = m.merchant_id and 
				(w.date_check between '".$date." 00:00:00' and '".$date3." 23:59:59') and
				m.merchant_status = 'Aktif' and
				(w.date_pay > '".$date3."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");

				$cost_mitra = executeScalar("	select 	sum(w.cost) 
										from 	weighing_mitra w, merchant m 
										where 	w.merchant_id = m.merchant_id and 
												(w.date_check between '".$date." 00:00:00' and '".$date_check_mitra." 23:59:59') and
												m.merchant_status = 'Aktif' and
												(w.date_pay > '".$date_check_mitra."' || w.date_pay is NULL) and
												w.weighing_sts != 'Batal' and
												(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				
				$tot_other 	= $tot_other+$other;
				$tot_vegie 	= $tot_vegie+$vegie;
				$tot_fruit 	= $tot_fruit+$fruit;
				$tot_other_mitra 	= $tot_other_mitra+$other_mitra;
				$tot_vegie_mitra 	= $tot_vegie_mitra+$vegie_mitra;
				$tot_fruit_mitra 	= $tot_fruit_mitra+$fruit_mitra;
				$tot_cost 	= $tot_cost+$cost+$cost_mitra;
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo date("d-m-Y", strtotime($r['dateonly'])); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($other); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($vegie); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($fruit); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($other_mitra);?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($vegie_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($fruit_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($cost + $cost_mitra); ?></td>
		</tr>
		<?php 
			$no++;
			}
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="2"><b>Total</b></td>
			<td style="text-align: right;border: solid 1px black;"><b> <?php echo format_rupiah($tot_other); ?></b></td>
			<td style="text-align: right;border: solid 1px black;"><b> <?php echo format_rupiah($tot_vegie); ?></b></td>
			<td style="text-align: right;border: solid 1px black;"><b> <?php echo format_rupiah($tot_fruit); ?></b></td>
			<td style="text-align: right;border: solid 1px black;"><b> <?php echo format_rupiah($tot_other_mitra); ?></b></td>
			<td style="text-align: right;border: solid 1px black;"><b> <?php echo format_rupiah($tot_vegie_mitra); ?></b></td>
			<td style="text-align: right;border: solid 1px black;"><b> <?php echo format_rupiah($tot_fruit_mitra); ?></b></td>
			<td style="text-align: right;border: solid 1px black;"><b> <?php echo format_rupiah($tot_cost); ?></b></td>
		</tr>
	</table>
	<br />Keterangan: - Jumlah diatas dalam satuan Rupiah
</page>