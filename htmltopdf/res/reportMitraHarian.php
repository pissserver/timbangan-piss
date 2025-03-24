<?php
	include "../connection.php";
	$date1		= $_POST['date_omzet'];
	$date 		= date("d M Y");
	$commodity	= $_POST['commodity'];
	$netto		= 0;
	$cost		= 0;
	$cost_bagi	= 0;
	$cost_bagi_mandor	= 0;
?>
<page backtop="15mm" backbottom="10mm" backleft="5mm" backright="5mm">
	<page_header>
		<div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:20px; margin-right:65px;">
			RINCIAN OMZET MITRA & MANDOR HARIAN
			<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
		</div>
    </page_header>
	<table style="margin-bottom: 5px;font-size:10pt; width:250px; margin-left:-25px;" >
		<tr>
			<td style="width: 40%;">Tanggal Omzet</td>
			<td style="width: 60%;">: <?php echo date("d M Y", strtotime($date1)); ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Komoditi</td>
			<td style="width: 65%;">: <?php echo $commodity; ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px; margin-left:-25px; font-size:8pt; width:100%; margin-right:0px;" id="tableOmzetHarianMitra">
		<tr>
			<th style="text-align: center; width: 3%;  border: solid 1px black;">NO</th>
			<th style="text-align: center; width: 10%;  border: solid 1px black;">TGL TAGIHAN</th>
			<th style="text-align: center; width: 12%; border: solid 1px black;">NOTA TAGIHAN</th>
			<th style="text-align: center; width: 7%; border: solid 1px black;">NOPOL</th>
			<th style="text-align: center; width: 17%; border: solid 1px black;">PEDAGANG</th>
			<th style="text-align: center; width: 6%; border: solid 1px black;">JENIS</th>
			<th style="text-align: center; width: 6%; border: solid 1px black;">STS</th>
			<th style="text-align: center; width: 9%; border: solid 1px black;">NETTO BONGKAR (KG)</th>
			<th style="text-align: center; width: 9%; border: solid 1px black;">BAGI MITRA (RP)</th>
			<th style="text-align: center; width: 9%; border: solid 1px black;">BAGI MANDOR (RP)</th>
			<th style="text-align: center; width: 9%; border: solid 1px black;">TOTAL MITRA (RP)</th>
		</tr>
		<?php
			// Select omzet with commodity
			if ($commodity == "Semua") {
				$query 	= "select wm.weighing_id, 
											wm.nopol, 
											wm.goods_id, 
											wm.checker_id, 
											wm.merchant_id, 
											wm.reffer, 
											wm.date_check,
											wm.weighing_sts, 
											wm.netto, 
											wm.cost_bayar,
											(wm.cost_bagi_mitra + wm.cost_simpan_mitra) AS bagi_mitra,
											(wm.cost_bagi_mandor + wm.cost_simpan_mandor) AS bagi_mandor,
											wm.cost,
											merchant_type
									from 	weighing_mitra wm, 
											merchant m 
									where 	wm.merchant_id = m.merchant_id and 
											wm.weighing_sts != 'Batal' and 
											(wm.date_check between '".$date1." 00:00:00' and '".$date1." 23:59:59')";
			} else {
				$query 	= "select wm.weighing_id, 
											wm.nopol, 
											wm.goods_id, 
											wm.checker_id, 
											wm.merchant_id, 
											wm.reffer, 
											wm.date_check,
											wm.weighing_sts, 
											wm.netto, 
											wm.cost_bayar,
											(wm.cost_bagi_mitra + wm.cost_simpan_mitra) AS bagi_mitra,
											(wm.cost_bagi_mandor + wm.cost_simpan_mandor) AS bagi_mandor,
											wm.cost,
											merchant_type
									from 	weighing_mitra wm, 
											merchant m 
									where 	wm.merchant_id = m.merchant_id and 
											wm.weighing_sts != 'Batal' and 
											m.merchant_type = '".$commodity."' and
											(wm.date_check between '".$date1." 00:00:00' and '".$date1." 23:59:59')";
			}
			
			// echo $query;exit();
			$query_data = mysql_query($query);
			$no 		= 1;
			while($r= mysql_fetch_array($query_data)){
				$name			= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
				$kat_merchant	= executeScalar("select kat_merchant from merchant where merchant_id = '".$r['merchant_id']."'");
				$goods			= executeScalar("select goods_name from goods where goods_id = '".$r['goods_id']."'");
				//$wid 			= sprintf("%07d", $r['weighing_id']);
				//$year1 		= executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;height: 5px;"> <?php echo $no; ?></td>
			<td style="text-align: center;border: solid 1px black;"> <?php echo date("d M Y", strtotime($r['date_check'])); ?></td>
			<td style="text-align: center;border: solid 1px black;"> <?php echo 'TG'.$r['weighing_id']; ?></td>
			<td style="text-align: center;border: solid 1px black;"> <?php echo $r['nopol']; ?></td>
			<td style="text-align: left;border: solid 1px black;"> <?php echo $name; ?></td>
			<td style="text-align: center;border: solid 1px black;"> <?php echo $r['merchant_type']; ?></td>
			<td style="text-align: center;border: solid 1px black;"> <?php echo $r['weighing_sts']; ?></td>
			<td style="text-align: center;border: solid 1px black;"> <?php echo format_no($r['netto']); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($r['bagi_mitra']); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($r['bagi_mandor']); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($r['cost']); ?></td>
		</tr>
		<?php 
			$no++;
			if($r['weighing_sts']!='Batal'||$r['weighing_sts']!='Dipecah'||$r['weighing_sts']!='Digabung')
			$netto 				= $netto+$r['netto'];
			$cost 				= $cost+$r['cost'];
			$cost_bagi 			= $cost_bagi+$r['bagi_mitra'];
			$cost_bagi_mandor 	= $cost_bagi_mandor+$r['bagi_mandor'];
			} 
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="7"><b>TOTAL </b></td>
			<td style="text-align: center;border: solid 1px black;font-weight:bold;"><?php echo format_no($netto); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($cost_bagi); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($cost_bagi_mandor); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($cost); ?></td>
		</tr>
	</table>
    <page_footer>
        <table style="width: 98%; border: 0px black; margin-top:10px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left;width: 50%">Tgl Cetak: <?php echo $date; ?></td>
				<td style="text-align: center;width: 25%"></td>
                <td style="text-align: right;width: 25%">Halaman [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
</page>