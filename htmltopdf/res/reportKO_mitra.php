<?php
	session_start();
	include "../connection.php";
	$ko			= $_POST['ko'];
	$date2 		= date("d M Y");
	$cashier_id	= executeScalar("select cashier_id from ko_mitra where ko_id = '".$ko."'");
	$cashier	= executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");
	$checker_id	= executeScalar("select receiver_id from ko_mitra where ko_id = '".$ko."'");
	$checker	= executeScalar("select checker_name from checker where checker_id = '".$checker_id."'");
	$ko_date	= executeScalar("select ko_date from ko_mitra where ko_id = '".$ko."'");
	$ko_total	= executeScalar("select ko_total from ko_mitra where ko_id = '".$ko."'");
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
                <td style="text-align: left;width: 50%">Tgl Cetak: <?php echo $date2; ?></td>
				<td style="text-align: center;width: 25%"></td>
                <td style="text-align: right;width: 25%">Halaman [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
    <div style="font-size: 14px; font-weight: bold; text-align: center; margin-top:0px; margin-right:65px;">
		RINCIAN SETORAN KASIR OPERASIONAL MITRA
		<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
	</div>
    <br/>
	<table style="margin-bottom: 5px;font-size:10pt; width:400px;" >
		<tr>
			<td style="width: 45%;">Kasir Operasional Mitra ID</td>
			<td style="width: 55%;">: KOM<?php echo $ko; ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Kasir Operasional</td>
			<td style="width: 65%;">: <?php echo $cashier; ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Penyetor</td>
			<td style="width: 65%;">: <?php echo $checker; ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Total Setoran</td>
			<td style="width: 65%;">: <?php echo format_rupiah($ko_total); ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Tanggal Bayar</td>
			<td style="width: 65%;">: <?php echo date("d M Y", strtotime($ko_date)); ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:8.5pt; width:100%; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th rowspan="2" style="text-align: center;height: 1%;width: 5%; border: solid 1px black;">NO</th>
			<th rowspan="2" style="text-align: center;width: 10%; border: solid 1px black;">TGL TAGIHAN</th>
			<!-- <th style="text-align: center;width: 11%;border: solid 1px black;">NOTA TIMBANG</th> -->
			<th rowspan="2" style="text-align: center;width: 12%;border: solid 1px black;">NOTA TAGIHAN</th>
			<th rowspan="2" style="text-align: center;width: 9%;border: solid 1px black;">NOPOL</th>
			<th rowspan="2" style="text-align: center;width: 20%;border: solid 1px black;">PEDAGANG</th>
			<th rowspan="2" style="text-align: center;width: 6%;border: solid 1px black;">JENIS</th>
			<th rowspan="2" style="text-align: center;width: 7%;border: solid 1px black;">TONASE (Kg)</th>
			<th colspan="2" style="text-align: center;width: 12%;border: solid 1px black;"><br>MITRA BONGKAR</th>
			<!-- <th rowspan="2" style="text-align: center;width: 8%;border: solid 1px black;">BAGI MITRA (RP)</th> -->
			<th rowspan="2" style="text-align: center;width: 8%;border: solid 1px black;">TOTAL SETOR (RP)</th>
		</tr>
		<tr>
			<th style="text-align: center;width: 8%;border: solid 1px black;">BAGI KULI <br> (RP)</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">BAGI MANDOR <br> (RP)</th>
		</tr>
		
		<?php
			//select tonase berdasarkan tanggal masuk
			$sql = "select 	weighing_id, 
							merchant_id, 
							checker_id, 
							reffer, 
							date_check, 
							nopol, 
							netto,
							(cost_bagi_mitra + cost_simpan_mitra) AS bagi_mitra,
							(cost_bagi_mandor + cost_simpan_mandor) AS bagi_mandor,  
							cost,
							cost_bayar
					from 	weighing_mitra 
					where 	weighing_sts = 'Lunas' and 
							ko_id = '".$ko."'";
			
			$query 	= mysql_query($sql);
			$no 	= 1;
			$grandTotal = 0;
			while($r= mysql_fetch_array($query)){
				$name		= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
				$jenis		= executeScalar("select merchant_type from merchant where merchant_id = '".$r['merchant_id']."'");
				$kode_jenis			= mb_substr($jenis, 0, 1, 'utf-8');

				//$wid 		= sprintf("%07d", $r['weighing_id']);
				//$rid 		= sprintf("%07d", $r['reffer']);
				//$year1 		= executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
				//$year2 		= executeScalar("select date_format(date_out, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo date("d M Y", strtotime($r['date_check'])); ?></td>
			<!-- <td style="text-align: center;border: solid 1px black;"><?php 
			if($r['reffer']=='null'||$r['reffer']==''||$r['reffer']=='0'){
				echo 'TB'.$r['weighing_id'];
			}else{
				echo 'TB'.$r['reffer'];
			} ?></td> -->
			<td style="text-align: center;border: solid 1px black;"><?php echo 'TG'.$r['weighing_id']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['nopol']; ?></td>
			<td style="text-align: left;border: solid 1px black; word-wrap: break-word; overflow-wrap: break-word; white-space: normal;"><?php echo $name; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $jenis; ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['netto']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['bagi_mitra']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['bagi_mandor']); ?></td>
			<!-- <td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['cost']); ?></td> -->
			<!-- <td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['cost_bayar']); ?></td> -->

			<?php if($kode_jenis == 'B') { ?>
				<td style="text-align: right; border: solid 1px black;"><?php echo format_no($r['cost_bayar']); ?></td>
			<?php } elseif($kode_jenis == 'S') { ?>
				<td style="text-align: right; border: solid 1px black;"><?php echo format_no($r['cost']); ?></td>
			<?php } ?>

		</tr>
		<?php 
			$no++;

			if ($kode_jenis == 'B') {
				$grandTotal = $grandTotal + $r['cost_bayar'];
			} elseif ($kode_jenis == 'S') {
				$grandTotal = $grandTotal + $r['cost'];
			}
			}
			// $cost 		= executeScalar("	select 	sum(cost) 
			// 								from 	weighing_mitra 
			// 								where 	weighing_sts = 'Lunas' and 
			// 										ko_id = '".$ko."'");

			$cost 				= executeScalar("	select 	sum(cost_bayar) 
											from 	weighing_mitra 
											where 	weighing_sts = 'Lunas' and 
													ko_id = '".$ko."'");

			$cost_bagi_mitra 	= executeScalar("	select 	sum(cost_bagi_mitra) + sum(cost_simpan_mitra) 
											from 	weighing_mitra 
											where 	weighing_sts = 'Lunas' and 
											ko_id = '".$ko."'");

			$cost_bagi_mandor 	= executeScalar("	select 	sum(cost_bagi_mandor) + sum(cost_simpan_mandor) 
											from 	weighing_mitra 
											where 	weighing_sts = 'Lunas' and 
											ko_id = '".$ko."'");

			$netto 				= executeScalar("	select 	sum(netto) 
											from 	weighing_mitra 
											where 	weighing_sts = 'Lunas' and 
											ko_id = '".$ko."'");
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="6"><b>Total </b></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($netto); ?> </td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($cost_bagi_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($cost_bagi_mandor); ?></td>
			<!-- <td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($cost); ?></td> -->
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($grandTotal); ?></td>
		</tr>
	</table>
	<br /><strong>Keterangan :</strong>
	<!-- <br>
		<ul>
			<li>Untuk tagihan dengan jenis Buah, total setoran yang diterima hanya uang bagi mandor</li>
			<li>Total Setor merupakan jumlah uang yang diterima oleh kasir, pada saat pelunasan tagihan</li>
		</ul> -->
</page>