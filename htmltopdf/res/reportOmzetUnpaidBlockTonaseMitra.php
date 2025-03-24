<?php
	include "../connection.php";
	$date1	= '2016-10-01';
	$date2	= $_POST['date2'];
	$block	= $_POST['block'];
	$total3 = 0;
	$date 	= date("d-m-Y");
?>
<page backtop="15mm" backbottom="10mm" backleft="mm" backright="0mm">
	<page_header>
		<div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:20px; margin-right:65px;">
			REKAP TAGIHAN TONASE & MITRA BELUM TERBAYAR BLOK <?php echo $block; ?>
			<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
		</div>
    </page_header>
	<table style="margin-bottom: 5px;font-size:10pt; width:450px;" >
		<tr>
			<td style="width: 35%;">Tanggal Akhir Tagihan</td>
			<td style="width: 65%;">: <?php echo date("d-m-Y", strtotime($date2)); ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:8pt; width:680px; margin-right:0px; table-layout: fixed;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 3%; border: solid 1px black;">NO</th>
			<th style="text-align: center;width: 25%;border: solid 1px black;">PEDAGANG</th>
			<th style="text-align: center;width: 23%;border: solid 1px black; ">LAPAK</th>
			<th style="text-align: center;width: 6%;border: solid 1px black;">TOTAL NOTA</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">UMUR TAGIHAN</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">STS MITRA</th>
			<th style="text-align: center;width: 10%;border: solid 1px black;">TONASE (RP)</th>
			<th style="text-align: center;width: 10%;border: solid 1px black;">MITRA (RP)</th>
			<th style="text-align: center;width: 10%;border: solid 1px black;">TOTAL (RP)</th>
		</tr>
		<?php
			$query = mysql_query("select m.merchant_id as id, m.merchant_name as name
			from merchant m, rent r, lapak l
			where m.merchant_id = r.merchant_id and
			r.lapak_id = l.lapak_id and
			(r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') and 
			l.block_id = '".$block."'
			group by id order by name");
			//r.rent_sts2 != 'Pindah'
			$no 	= 1;
			$total_cost_tonase	= 0;
			$total_cost_mitra	= 0;
			while($r= mysql_fetch_array($query)){
				$sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
						where r.lapak_id = l.lapak_id and 
						r.merchant_id = '".$r['id']."' and
						l.block_id = '".$block."' and
						(r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
						order by l.lapak_name desc");
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
				/*$jml	= executeScalar("select count(l.lapak_id) as jml from rent r, lapak l
						where r.lapak_id = l.lapak_id and 
						r.merchant_id = '".$r['id']."' and
						l.block_id = '".$block."' and
						(r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
						order by l.lapak_name desc");*/
						
				$jml	= executeScalar("select count(weighing_id) from weighing
					where merchant_id = '".$r['id']."' and
					(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
					(date_pay > '".$date2."' || date_pay is NULL) and
					weighing_sts != 'Batal' and
					(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");

				$laborer_sts	= executeScalar("select laborer_sts from weighing
					where merchant_id = '".$r['id']."' and
					(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
					(date_pay > '".$date2."' || date_pay is NULL) and
					weighing_sts != 'Batal' and
					(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");
					
				$cost_tonase	= executeScalar("select sum(cost_tonase) from weighing
					where merchant_id = '".$r['id']."' and
					(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
					(date_pay > '".$date2."' || date_pay is NULL) and
					weighing_sts != 'Batal' and
					(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");

				$cost_mitra	= executeScalar("select sum(cost) from weighing_mitra
					where merchant_id = '".$r['id']."' and
					(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
					(date_pay > '".$date2."' || date_pay is NULL) and
					weighing_sts != 'Batal' and
					(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");
					
				if($jml>0){
					$last_date	= executeScalar("select date_check from weighing
					where merchant_id = '".$r['id']."' and
					(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
					(date_pay > '".$date2."' || date_pay is NULL) and
					weighing_sts != 'Batal' and
					(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung') order by weighing_id limit 1");
					//Hitung Umur Tagihan
					$date3 	= new DateTime(date("Y-m-d", strtotime($last_date)));
					$date4 	= new DateTime(date("Y-m-d", strtotime($date2)));
					$diff 	= $date4->diff($date3)->format("%a");
				}
				
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: left;border: solid 1px black;"><?php echo $r['name']; ?></td>
			<td style="text-align: left; border: solid 1px black; max-width: 200px; word-wrap: break-word; word-break: break-word; white-space: normal;">
				<?php echo $lapak; ?>
			</td>
			<td style="text-align: center;border: solid 1px black;"><?php echo format_no($jml); ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php if($jml>0){ echo $diff.' Hari'; } ?></td>
			<td style="text-align: center;border: solid 1px black;"> 
				<?php 
					if ($laborer_sts == NULL) {
						echo '';
					} else {
						echo $laborer_sts;
					}
				?>
			</td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($cost_tonase); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($cost_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($cost_tonase + $cost_mitra); ?></td>
		</tr>
		<?php 
				$no++;
				$total_cost_tonase = $total_cost_tonase+$cost_tonase;
				$total_cost_mitra = $total_cost_mitra+$cost_mitra;
			}
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="6"><b>TOTAL </b></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($total_cost_tonase); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($total_cost_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($total_cost_tonase + $total_cost_mitra); ?></td>
		</tr>
	</table>
	<br />
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