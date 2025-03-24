<?php
	include "../connection.php";
	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$commodity	= $_POST['commodity'];
	$date 		= date("d-m-Y");
	$netto_mitra = 0;
	$cost_mitra = 0;
	$grandtot	= 0;
	$grandnetto	= 0;
	$grandNettoMitra = 0;
	$grandMitra	= 0;


?>
<page backtop="8mm" backbottom="10mm" backleft="0mm" backright="0mm">
	<page_header>
        <table style="width:95%; border:0px; margin-top:10px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left; width: 50%"></td>
                <td style="text-align: center; width: 25%"></td>
                <td style="text-align: right; width: 25%"></td>
            </tr>
        </table>
    </page_header>
    <page_footer>
        <table style="width: 95%; border: 0px black; margin-top:10px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left;width: 50%">Tgl Cetak: <?php echo $date; ?></td>
				<td style="text-align: center;width: 25%"></td>
                <td style="text-align: right;width: 25%">Halaman [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
    <div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:0px; margin-right:65px;">
		RINCIAN TAGIHAN TONASE & MITRA TERBAYAR
		<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)

	</div>
    <br/>
	<table style="margin-bottom: 5px;font-size:10pt; width:300px;" >
		<tr>
			<td style="width: 35%;">Tanggal</td>
			<td style="width: 65%;">: <?php 
			if($date1==$date2){
				echo date("d-m-Y", strtotime($date1));
			}else{
				echo date("d-m-Y", strtotime($date1)).' sd '.date("d-m-Y", strtotime($date2));
			} ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Jenis Pedagang</td>
			<td style="width: 65%;">: <?php echo $commodity; ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:7pt; width:850px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center; height: 2%; width: 3%; border: solid 1px black;">NO</th>
			<!-- <th style="text-align: center;width: 7%; border: solid 1px black;">TGL TAGIHAN</th> -->
			<th style="text-align: center;width: 7%; border: solid 1px black;">TGL BAYAR</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">NOTA TAGIHAN</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">STS NOTA</th>
			<th style="text-align: center;width: 22%;border: solid 1px black;">PEDAGANG</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">STS MITRA</th>
			<th style="text-align: center;width: 6%;border: solid 1px black;">NETTO TONASE (KG)</th>
			<th style="text-align: center;width: 6%;border: solid 1px black;">NETTO BONGKAR (KG)</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">TONASE (RP)</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">MITRA (RP)</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">TOTAL (RP)</th>
		</tr>
		<?php
			//select tonase berdasarkan tanggal masuk
			if ($commodity == 'Semua') {
				$sql = "SELECT 
							weighing_id, 
							merchant_id, 
							checker_id, 
							date_in, 
							date_pay, 
							nopol, 
							laborer_sts, 
							weighing_sts2, 
							netto, 
							rafaksi, 
							reffer, 
							cost_tonase, 
							cost_bongkar, 
							cost, 
							(SELECT wm.netto 
							FROM weighing_mitra wm 
							WHERE wm.weighing_id = weighing.weighing_id) AS netto_mitra, 
							(SELECT wm.cost 
							FROM weighing_mitra wm 
							WHERE wm.weighing_id = weighing.weighing_id) AS cost_mitra
						FROM 
							weighing 
						WHERE 
							weighing_sts = 'Lunas' 
							AND (date_pay BETWEEN '".$date1."' AND '".$date2."')";
			} else {
				$sql = "SELECT 
							w.weighing_id, 
							w.merchant_id, 
							w.checker_id, 
							w.date_in, 
							w.date_pay, 
							w.nopol, 
							w.laborer_sts, 
							w.weighing_sts2, 
							w.netto, 
							w.rafaksi, 
							w.reffer, 
							w.cost_tonase, 
							w.cost_bongkar, 
							w.cost, 
							(SELECT wm.netto 
							FROM weighing_mitra wm 
							WHERE wm.weighing_id = w.weighing_id) AS netto_mitra, 
							(SELECT SUM(wm.cost) 
							FROM weighing_mitra wm 
							WHERE wm.weighing_id = w.weighing_id) AS cost_mitra
						FROM 
							weighing w, 
							merchant m 
						WHERE 
							w.merchant_id = m.merchant_id 
							AND weighing_sts = 'Lunas' 
							AND m.merchant_type = '".$commodity."' 
							AND (w.date_pay BETWEEN '".$date1."' AND '".$date2."')";
			}
			
			$query 		= mysql_query($sql);
			$no 		= 1;

			while($r= mysql_fetch_array($query)){
				$name		= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
				$netto_mitra_digabung = executeScalar("select sum(netto) from weighing where (weighing_sts2 = 'Digabung' OR weighing_sts2 = 'Pecahan') and laborer_sts = 'Ya' and reffer ='".$r['weighing_id']."'");
				$jenis			= mb_substr($r['merchant_id'], 0, 1, 'utf-8');

				if ($jenis == 'B') {
					$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '9'");
				} elseif ($jenis == 'S') {
					$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '3'");
				}
				//$wid 		= sprintf("%07d", $r['weighing_id']);
				//$rid 		= sprintf("%07d", $r['reffer']);
				//$year1 		= executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
				//$year2 		= executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<!-- <td style="text-align: center;border: solid 1px black;"><?php echo date("d-m-Y", strtotime($r['date_in'])); ?></td> -->
			<td style="text-align: center;border: solid 1px black;"><?php echo date("d-m-Y", strtotime($r['date_pay'])); ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo 'TG'.$r['weighing_id']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['weighing_sts2']; ?></td>
			<td style="text-align: left;border: solid 1px black;"><?php echo $name; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['laborer_sts'] ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo format_no($r['netto']); ?></td>
			<td style="text-align: center;border: solid 1px black;">
				<?php 
					if ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] != 'Gabungan') {
						echo format_no($r['netto_mitra']);
					} elseif ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] == 'Gabungan') {
						echo format_no($netto_mitra_digabung);
					} elseif ($r['laborer_sts'] == 'Tidak' && $r['weighing_sts2'] != 'Gabungan') {
						echo '0';
					} elseif ($r['laborer_sts'] == 'Tidak' && $r['weighing_sts2'] == 'Gabungan') {
						echo '0';
					}
				?>
			</td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['cost_tonase']); ?></td> 
			<td style="text-align: right;border: solid 1px black;">
				<?php 
					if ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] != 'Gabungan') {
						echo format_no($r['cost_mitra']);
					} elseif ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] == 'Gabungan') {
						$cost_mitra = ($netto_mitra_digabung + $r['rafaksi']) * intval($tarif1);
						echo format_no($cost_mitra);
					} elseif ($r['laborer_sts'] == 'Tidak') {
						echo '0';
					}  
				?>
			</td> 
			<td style="text-align: right;border: solid 1px black;">
				<?php 
					if ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] != 'Gabungan') {
						echo format_no($r['cost_tonase'] + $r['cost_mitra']);
					} elseif ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] == 'Gabungan') {
						$cost_mitra = ($netto_mitra_digabung + $r['rafaksi']) * intval($tarif1);
						echo format_no($r['cost_tonase'] + $cost_mitra);
					} elseif ($r['laborer_sts'] == 'Tidak') {
						echo $r['cost_tonase'];
					}  
				?>
			</td> 
		</tr>
		<?php 
			$grandnetto	= $grandnetto+$r['netto'];
			$grandtot	= $grandtot+$r['cost_tonase'];

			if ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] != 'Gabungan') {
				$grandNettoMitra = $grandNettoMitra+$r['netto_mitra'];
			} elseif ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] == 'Gabungan') {
				$grandNettoMitra = $grandNettoMitra+$netto_mitra_digabung;
			}
			
			if ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] != 'Gabungan') {
				$grandMitra = $grandMitra+$r['cost_mitra'];
			} elseif ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] == 'Gabungan') {
				$grandMitra = $grandMitra+$cost_mitra;
			}
			
			$no++;
			}
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="6"><b>Total </b></td>
			<td style="text-align: center;border: solid 1px black;font-weight:bold;"><?php echo format_no($grandnetto); ?></td>
			<td style="text-align: center;border: solid 1px black;font-weight:bold;"><?php echo format_no($grandNettoMitra); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_rupiah($grandtot); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_rupiah($grandMitra); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_rupiah($grandtot + $grandMitra); ?></td>
		</tr>
	</table>
	<br />Keterangan:
</page>