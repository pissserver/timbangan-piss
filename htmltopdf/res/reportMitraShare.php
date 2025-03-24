<?php
	session_start();
	include "../connection.php";
	$date1	= $_POST['date1'];
	$date2	= $_POST['date2'];

	// Jumlah fee mandor ditambahkan dengan save mandor (Untuk Sementara)
	$query 	= mysql_query("	select 	fee_id, 
									sum(fee_B001) as B001, 
									sum(fee_B002) as B002, 
									sum(fee_B003) as B003, 
									sum(fee_B004) as B004, 
									sum(fee_B005) as B005, 
									sum(fee_B006) as B006, 
									sum(fee_S001) as S001, 
									sum(fee_S002) as S002, 
									sum(fee_S003) as S003, 
									sum(fee_S004) as S004, 
									sum(fee_S005) as S005, 
									sum(fee_S006) as S006, 
									sum(fee_S007) as S007, 
									sum(fee_S008) as S008, 
									sum(fee_S009) as S009,
									sum(fee_S010) as S010,
									sum(fee_S011) as S011,
									sum(fee_S012) as S012,
									sum(fee_M01) + sum(save_M01) as M01,
									sum(fee_M02) + sum(save_M02) as M02,
									sum(fee_M03) + sum(save_M03) as M03,
									sum(fee_M04) + sum(save_M04) as M04, 
									fee_date as date
							from 	laborer_fee2 
							where 	fee_date between '".$date1."' and '".$date2."'
							group by date");
?>
<page backtop="8mm" backbottom="10mm" backleft="5mm" backright="5mm">
	<page_header>
        <table style="width:94%; border:0px; margin-top:10px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left; width: 50%"></td>
                <td style="text-align: center; width: 25%"></td>
                <td style="text-align: right; width: 25%"></td>
            </tr>	
        </table>
    </page_header>
    <page_footer>
        <table style="width: 94%; border: 0px black; margin-top:10px; margin-left:5px; margin-right:5px; font-size:8pt;">
            <tr>
                <td style="text-align: left;width: 50%">Tgl Cetak: <?php echo date('d M Y', strtotime($date2)); ?></td>
				<td style="text-align: center;width: 25%"></td>
                <td style="text-align: right;width: 25%">Halaman [[page_cu]]/[[page_nb]]</td>
            </tr>
        </table>
    </page_footer>
    <div style="font-size: 14px; font-weight: bold; text-align: center; margin-top:0px; margin-right:65px;">
		REKAP PENDAPATAN MITRA DIBAGIKAN (SETELAH DIBULATKAN)
		<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
	</div>
	<br />
	<br />
	<table style="margin-bottom: 5px;font-size:11pt; width:400px;" >
		<tr>
			<td style="width: 30%;">Tanggal</td>
			<td style="width: 70%;">: <?php echo date("d M Y", strtotime($date1)); ?> sd <?php echo date("d M Y", strtotime($date2)); ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px; margin-top: 0px; font-size:5.5pt; width:1000px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 2%;border: solid 1px black;">NO</th>
			<th style="text-align: center;width: 6%;border: solid 1px black;">TANGGAL</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">B001</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">B002</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">B003</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">B004</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">B005</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">B006</th>

			<th style="text-align: center;width: 4%;border: solid 1px black;">S001</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S002</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S003</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S004</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S005</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S006</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S007</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S008</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S009</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S010</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S011</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">S012</th>

			<th style="text-align: center;width: 4%;border: solid 1px black;">M01</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">M02</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">M03</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">M04</th>

			<th style="text-align: center;width: 4%;border: solid 1px black;">PISS</th>
			<th style="text-align: center;width: 4%;border: solid 1px black;">TOTAL (RP)</th>
		</tr>
		<?php
			$no			= 1;
			$grandtotal = 0;
			$B001		= 0;
			$B002		= 0;
			$B003		= 0;
			$B004		= 0;
			$B005		= 0;
			$B006		= 0;
			$S001		= 0;
			$S002		= 0;
			$S003		= 0;
			$S004		= 0;
			$S005		= 0;
			$S006		= 0;
			$S007		= 0;
			$S008		= 0;
			$S009		= 0;
			$S010		= 0;
			$S011		= 0;
			$S012		= 0;
			$M01		= 0;
			$M02		= 0;
			$M03		= 0;
			$M04		= 0;
			$PISSTOTAL 	= 0;
			while($r= mysql_fetch_array($query)){
				$PISS = executeScalar("SELECT SUM(pios_fee) 
						FROM laborer_fee 
						WHERE laborer_fee_sts = 'Baru' 
						AND DATE(laborer_fee_date) = '".$r['date']."'");
				$total = 0;
				$total = 	$r['B001']+
							$r['B002']+
							$r['B003']+
							$r['B004']+
							$r['B005']+
							$r['B006']+
							$r['S001']+
							$r['S002']+
							$r['S003']+
							$r['S004']+
							$r['S005']+
							$r['S006']+
							$r['S007']+
							$r['S008']+
							$r['S009']+
							$r['S010']+
							$r['S011']+
							$r['S012']+
							$r['M01']+
							$r['M02']+
							$r['M03']+
							$r['M04']+
							$PISS;
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo date("d M Y", strtotime($r['date'])); ?></td>
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

			<td style="text-align: right;border: solid 1px black;"><?php echo format_no($PISS); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($total); ?></td>
		</tr>
		<?php 
				$no++;
				$B001 = $B001+$r['B001'];
				$B002 = $B002+$r['B002'];
				$B003 = $B003+$r['B003'];
				$B004 = $B004+$r['B004'];
				$B005 = $B005+$r['B005'];
				$B006 = $B006+$r['B006'];
				$S001 = $S001+$r['S001'];
				$S002 = $S002+$r['S002'];
				$S003 = $S003+$r['S003'];
				$S004 = $S004+$r['S004'];
				$S005 = $S005+$r['S005'];
				$S006 = $S006+$r['S006'];
				$S007 = $S007+$r['S007'];
				$S008 = $S008+$r['S008'];
				$S009 = $S009+$r['S009'];
				$S010 = $S010+$r['S010'];
				$S011 = $S011+$r['S011'];
				$S012 = $S012+$r['S012'];
				$M01  = $M01+$r['M01'];
				$M02  = $M02+$r['M02'];
				$M03  = $M03+$r['M03'];
				$M04  = $M04+$r['M04'];
				$PISSTOTAL  = $PISSTOTAL+$PISS;
				$grandtotal = $grandtotal+$total;
			}
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="2"><b>Total </b></td>
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
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"><?php echo format_no($PISSTOTAL); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($grandtotal); ?></td>
		</tr>
	</table>
	<br />Keterangan:
	<br /><br /><br /><br /><br />
</page>