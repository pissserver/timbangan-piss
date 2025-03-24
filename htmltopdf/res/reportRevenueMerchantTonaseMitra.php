<?php
	include "../connection.php";
	$date1	= $_POST['date1'];
	$date2	= $_POST['date2'];
	$mid	= $_POST['merchant'];
	$date 	= date("d-m-Y");
	$name	= executeScalar("select merchant_name from merchant where merchant_id = '".$mid."'");
	$sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
				where r.lapak_id = l.lapak_id and 
				r.merchant_id = '".$mid."' and
				(r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
				order by l.lapak_name");
	$string	= "";
	$counter= 0;
	while($row=mysql_fetch_array($sql2)){
		if($counter!=0){
			$string = ", ".$string;
		}
		$string = $row['lapak_name'].$string;
		$counter++;
	}
	$lapak 	= "".$string;
?>
<page backtop="15mm" backbottom="10mm" backleft="mm" backright="0mm">
	<page_header>
		<div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:20px;">
			RINCIAN TAGIHAN TONASE & MITRA TERBAYAR PER PEDAGANG<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
		</div>
    </page_header>
	<table style="margin-bottom: 5px;font-size:10pt; width:450px;" >
		<tr>
			<td style="width: 25%;">Nama Pedagang</td>
			<td style="width: 75%;">: <?php echo $name.' ('.$mid.')'; ?></td>
		</tr>
		<tr>
			<td style="width: 25%;">Lapak</td>
			<td style="width: 75%;">: <?php echo $lapak; ?></td>
		</tr>
		<tr>
			<td style="width: 25%;">Tanggal</td>
			<td style="width: 75%;">: <?php 
			if($date1==$date2){
				echo date("d-m-Y", strtotime($date1));
			}
			else{
				echo date("d-m-Y", strtotime($date1)).' sd '.date("d-m-Y", strtotime($date2));
			} ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:7pt; width:850px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 3%;height: 3%;border: solid 1px black;">NO</th>
			<th style="text-align: center;width: 8%; border: solid 1px black;">TGL BAYAR</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">NOTA TAGIHAN</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">STS NOTA</th>
			<th style="text-align: center;width: 7%;border: solid 1px black;">NOPOL</th>
			<th style="text-align: center;width: 7%;border: solid 1px black;">KOMODITI</th>
			<th style="text-align: center;width: 5%;border: solid 1px black;">STS MITRA</th>
			<th style="text-align: center;width: 7%;border: solid 1px black;">NETTO TONASE (KG)</th>
			<th style="text-align: center;width: 7%;border: solid 1px black;">NETTO BONGKAR (KG)</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">TONASE (RP)</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">MITRA (RP)</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">TOTAL (RP)</th>
		</tr>
		<?php
			$query = mysql_query("SELECT 
			w.weighing_id, 
			w.goods_id, 
			w.reffer, 
			w.date_out, 
			w.nopol, 
			w.weighing_sts2, 
			w.weight_in, 
			w.weight_out, 
			w.noncommodity, 
			w.rafaksi, 
			w.netto,
			w.laborer_sts, 
			w.cost_tonase,
			w.cost_bongkar,
			w.cost,
			m.merchant_id, 
			wm.netto AS netto_mitra,
			wm.cost AS cost_mitra
		FROM 
			weighing w
		JOIN 
			merchant m ON w.merchant_id = m.merchant_id
		LEFT JOIN 
			weighing_mitra wm ON wm.weighing_id = w.weighing_id
		WHERE 
			w.weighing_sts = 'Lunas' 
			AND w.merchant_id = '".$mid."' 
			AND w.date_pay BETWEEN '".$date1."' AND '".$date2."';
		
		");

			$no 	= 1;
			$netto 	= 0;
			$nettoMitra 	= 0;
			$difference = 0;
			$cost 	= 0;
			$cost_mitra = 0;
			$total_cost_mitra = 0;
			$total_cost = 0;
			while($r= mysql_fetch_array($query)){
				$goods	= executeScalar("select goods_name from goods where goods_id = '".$r['goods_id']."'");
				$netto_mitra_digabung = executeScalar("select sum(netto) from weighing where (weighing_sts2 = 'Digabung' OR weighing_sts2 = 'Pecahan') and laborer_sts = 'Ya' and reffer ='".$r['weighing_id']."'");
				$jenis			= mb_substr($r['merchant_id'], 0, 1, 'utf-8');

				if ($jenis == 'B') {
					$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '9'");
				} elseif ($jenis == 'S') {
					$tarif1	 		= executeScalar("select pay_total from payment where pay_id = '3'");
				}
				
				//$wid 	= sprintf("%07d", $r['weighing_id']);
				//$rid 	= sprintf("%07d", $r['reffer']);
				//$year1 	= executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
				//$year2 	= executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo date("d-m-Y", strtotime($r['date_out'])); ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo 'TG'.$r['weighing_id']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['weighing_sts2']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['nopol']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $goods; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['laborer_sts']; ?> </td> 
			<td style="text-align: center;border: solid 1px black;"><?php echo format_no($r['netto']); ?> </td> 
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
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($r['cost_tonase']); ?></td>
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
				$no++;
				$netto 	= $netto+$r['netto'];
				$cost 	= $cost+$r['cost_tonase'];

				if ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] != 'Gabungan') {
					$nettoMitra 	= $nettoMitra+$r['netto_mitra'];
				} elseif ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] == 'Gabungan') {
					$nettoMitra = $nettoMitra+$netto_mitra_digabung;
				}

				if ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] != 'Gabungan') {
					$total_cost_mitra = $total_cost_mitra+$r['cost_mitra'];
				} elseif ($r['laborer_sts'] == 'Ya' && $r['weighing_sts2'] == 'Gabungan') {
					$total_cost_mitra = $total_cost_mitra+$cost_mitra;
				}
			} 
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="7"><b>TOTAL </b></td>
			<td style="text-align: center;border: solid 1px black;font-weight:bold;"><?php echo format_no($netto); ?> </td>
			<td style="text-align: center;border: solid 1px black;font-weight:bold;"><?php echo format_no($nettoMitra); ?> </td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_rupiah($cost); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_rupiah($total_cost_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_rupiah($cost + $total_cost_mitra); ?></td>
		</tr>
	</table>
    <page_footer>
        <table style="width: 95%; border: 0px black; margin-top:10px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left;width: 50%">Tgl Cetak: <?php echo $date; ?></td>
				<td style="text-align: center;width: 25%"></td>
                <td style="text-align: right;width: 25%">Halaman [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
</page>