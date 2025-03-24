<?php
	include "../connection.php";
	$date	= $_POST['date'];
	$date2 	= date("d M Y");
?>
<page backtop="15mm" backbottom="10mm" backleft="3mm" backright="3mm">
	<page_header>
        <table style="width:96%; border:0px; margin-top:10px; margin-bottom:0px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left; width: 40%"></td>
                <td style="text-align: center; width: 26%"></td>
                <td style="text-align: right; width: 30%">Dicetak: <?php echo $date2; ?></td>
            </tr>
        </table>
    </page_header>
	<div style="font-size: 14px; font-weight: bold; text-align: center; margin-top:0px;">
		RINCIAN PENDAPATAN MITRA DIBAGIKAN (SEBELUM DIBULATKAN)
		<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
	</div>
	<table style="margin-top: 10px; margin-bottom: 5px;font-size:10pt; width:500px;" >
		<tr>
			<td style="width: 30%;">Tanggal Kerja Mitra</td>
			<td style="width: 70%;">: <?php echo date("d M Y", strtotime($date)); ?></td>
		</tr>
	</table>
	<br />
	<div style="font-size: 12px; font-weight: bold; text-align: left; margin-bottom:10px;">
		Dibagikan:
	</div>
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:10pt; width:900px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 3%;border: solid 1px black;">NO</th>
			<th style="text-align: center;width: 8%;border: solid 1px black;">NO NOTA</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">B001</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">B002</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">B003</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">B004</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">B005</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">B005</th>

			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S001</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S002</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S003</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S004</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S005</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S005</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S007</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S008</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S009</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S010</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S011</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">S012</th>

			<th style="text-align: center;width: 5.5%;border: solid 1px black;">M01</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">M02</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">M03</th>
			<th style="text-align: center;width: 5.5%;border: solid 1px black;">M04</th>

			<th style="text-align: center;width: 5.5%;border: solid 1px black;">PISS</th>
		</tr>
		<?php
			//select data tanpa berdasarkan tgl bayar
			// $query 		= mysql_query("	select 	weighing_id, 
			// 									B001, B002, B003, B004, B005, B006, B007, B008, B009, B010, B011, B012, B013, B014, B015,
			// 									S001, S002, S003, S004, S005, S006, S007, S008, S009, S010, S011, S012, S013, S014, S015, 
			// 									M01, M02, M03, M04
			// 							from 	laborer_fee 
			// 							where 	laborer_fee_sts = 'Baru' and 
			// 									date(laborer_fee_date) = '".$date."'");

			// SELECT DATA BERDASARKAN TGL BAYAR
			$query 	= mysql_query("	select 	lf.weighing_id, 
											B001,
											B002,
											B003,
											B004,
											B005,
											B006,
											S001,
											S002,
											S003,
											S004,
											S005,
											S006,
											S007,
											S008,
											S009,
											S010,
											S011,
											S012,						
											M01, 
											M02, 
											M03,
											M04,
											pios_fee 
									from 	laborer_fee lf
									JOIN 	weighing_mitra wm ON wm.weighing_id = lf.weighing_id
									where 	laborer_fee_sts = 'Baru' and date(date_check) = '".$date."'");
					
			$no 	= 1;
			$total1	= 0;
			$B001 = 0; $B002 = 0; $B003 = 0; $B004 = 0; $B005 = 0; $B006 = 0;
			$S001 = 0; $S002 = 0; $S003 = 0; $S004 = 0; $S005 = 0; $S006 = 0; $S007 = 0; $S008 = 0; $S009 = 0; $S010 = 0; $S011 = 0; $S012 = 0;
			$M01 = 0; $M02 = 0; $M03 = 0; $M04 = 0;
			$PISS = 0;

			while($r= mysql_fetch_array($query)){
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo "TG" . $r['weighing_id']; ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['B001']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['B002']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['B003']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['B004']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['B005']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['B006']); ?></td>

			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S001']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S002']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S003']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S004']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S005']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S006']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S007']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S008']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S009']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S010']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S011']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['S012']); ?></td>
			
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['M01']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['M02']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['M03']); ?></td>
			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['M04']); ?></td>

			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($r['pios_fee']); ?></td>
		</tr>
		<?php
				$no++;
				$B001 = $B001 + $r['B001'];
				$B002 = $B002 + $r['B002'];
				$B003 = $B003 + $r['B003'];
				$B004 = $B004 + $r['B004'];
				$B005 = $B005 + $r['B005'];
				$B006 = $B006 + $r['B006'];

				$S001 = $S001 + $r['S001'];
				$S002 = $S002 + $r['S002'];
				$S003 = $S003 + $r['S003'];
				$S004 = $S004 + $r['S004'];
				$S005 = $S005 + $r['S005'];
				$S006 = $S006 + $r['S006'];
				$S007 = $S007 + $r['S007'];
				$S008 = $S008 + $r['S008'];
				$S009 = $S009 + $r['S009'];
				$S010 = $S010 + $r['S010'];
				$S011 = $S011 + $r['S011'];
				$S012 = $S012 + $r['S012'];

				$M01 = $M01 + $r['M01'];
				$M02 = $M02 + $r['M02'];
				$M03 = $M03 + $r['M03'];
				$M04 = $M04 + $r['M04'];

				$PISS = $PISS + $r['pios_fee'];
			} 
			$total1 = 	$B001+
						$B002+
						$B003+
						$B004+
						$B005+
						$B006+
						$S001+
						$S002+
						$S003+
						$S004+
						$S005+
						$S006+
						$S007+
						$S008+
						$S009+
						$S010+
						$S011+
						$S012+
						$M01+
						$M02+
						$M03+
						$M04+
						$PISS;
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="2"><b>TOTAL </b></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($B001); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($B002); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($B003); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($B004); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($B005); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($B006); ?></td>

			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S001); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S002); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S003); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S004); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S005); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S006); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S007); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S008); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S009); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S010); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S011); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($S012); ?></td>
			
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($M01); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($M02); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($M03); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($M04); ?></td>

			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($PISS); ?></td>
		</tr>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="25"><b>Total Dibagi Rp.<?php echo format_no($total1); ?> </b></td>
			<!-- <td style="text-align: right;border: solid 1px black;font-weight:bold;" colspan="2">Rp <?php echo format_no($total1); ?></td> -->
		</tr>
		<tr>
			
		</tr>
	</table>
    <page_footer>
        <table style="width: 96%; border: 0px black; margin-top:10px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left;width: 50%"></td>
				<td style="text-align: center;width: 26%"></td>
                <td style="text-align: right;width: 26%">Halaman [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
</page>