<?php
	include "../connection.php";
	$date1		= $_POST['date1'];
	//$date1		= '2016-08-20';
	$date2		= $_POST['date2'];
	$commodity	= $_POST['commodity'];
	$sort		= $_POST['sortir'];
	$date 		= date("d-m-Y");
?>
<page backtop="8mm" backbottom="10mm" backleft="5mm" backright="5mm">
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
		REKAP TAGIHAN TONASE & MITRA TERBAYAR SELURUH PEDAGANG<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
	</div>
    <br/>
	<table style="margin-bottom: 5px;font-size:10pt; width:300px;" >
		<tr>
			<td style="width: 35%;">Tanggal</td>
			<td style="width: 65%;">: <?php
			if($date1==$date2){
				echo date("d-m-Y", strtotime($date1));
			}
			else{
				echo date("d-m-Y", strtotime($date1)).' sd '.date("d-m-Y", strtotime($date2));
			} ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Jenis Pedagang</td>
			<td style="width: 65%;">: <?php echo $commodity; ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:8pt; width:800px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 5%;height: 3%;border: solid 1px black;">NO</th>
			<th style="text-align: center;width: 35%;border: solid 1px black;">NAMA PEDAGANG</th>
			<th style="text-align: center;width: 10%;border: solid 1px black;">BLOK AKTIF</th>
			<th style="text-align: center;width: 6%;border: solid 1px black;">STS MITRA</th>
			<th style="text-align: center;width: 6%;border: solid 1px black;">TOTAL NOTA</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">TONASE (RP)</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">MITRA (RP)</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">TOTAL (RP)</th>
		</tr>
		<?php
			$sql 	= '';
			$jml 	= 0;
			$total 	= 0;
			$total_mitra = 0;
			$grandTotal = 0;
			if($commodity=='Semua'){
				$sql = "SELECT 
							m.merchant_id AS id, 
							m.merchant_name AS name, 
							m.active_block, 
							m.merchant_type AS type, 
							COUNT(w.weighing_id) AS tot,
							SUM(w.cost_tonase) AS cost_tonase,
							SUM(w.cost_bongkar) AS cost_bongkar,
							SUM(wm.cost) AS cost_mitra, 
							w.laborer_sts
						FROM 
							merchant m
						JOIN 
							weighing w ON m.merchant_id = w.merchant_id
						LEFT JOIN 
							weighing_mitra wm ON wm.weighing_id = w.weighing_id
						WHERE 
							w.weighing_sts = 'Lunas'
							AND w.date_pay BETWEEN '".$date1."' AND '".$date2."' 
							AND m.merchant_status = 'Aktif' 
							AND m.merchant_type != 'Lainnya'
						GROUP BY 
							m.merchant_id, m.merchant_name, m.active_block, m.merchant_type, w.laborer_sts
						ORDER BY 
							".$sort."";
			}else{
				$sql = "SELECT 
							m.merchant_id AS id, 
							m.merchant_name AS name, 
							m.active_block, 
							m.merchant_type AS type,
							COUNT(w.weighing_id) AS tot,
							SUM(w.cost_tonase) AS cost_tonase,
							SUM(w.cost_bongkar) AS cost_bongkar,
							SUM(wm.cost) AS cost_mitra,
							w.laborer_sts
						FROM 
							merchant m
						JOIN 
							weighing w ON m.merchant_id = w.merchant_id
						LEFT JOIN 
							weighing_mitra wm ON wm.weighing_id = w.weighing_id
						WHERE 
							m.merchant_type = '".$commodity."' 
							AND w.weighing_sts = 'Lunas' 
							AND (w.date_pay BETWEEN '".$date1."' AND '".$date2."') 
							AND m.merchant_status = 'Aktif' 
							AND m.merchant_type != 'Lainnya'
						GROUP BY 
							m.merchant_id, m.merchant_name, m.active_block, m.merchant_type, w.laborer_sts
						ORDER BY 
							".$sort."";
			}
			$query 	= mysql_query($sql);
			$no 	= 1;
			while($r= mysql_fetch_array($query)){
				$jenis			= mb_substr($r['id'], 0, 1, 'utf-8');

		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: left;border: solid 1px black;"><?php echo $r['name']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['active_block']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['laborer_sts']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo format_no($r['tot']); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($r['cost_tonase']); ?></td>
			<td style="text-align: right;border: solid 1px black;"> 
				<?php 
					if ($jenis == 'B') {
						echo format_no($r['cost_mitra']);
					} elseif ($jenis == 'S') {
						echo format_no($r['cost_bongkar']);
					}
				?>
			</td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($r['cost_tonase'] + $r['cost_mitra']); ?></td>
		</tr>
		<?php 
				$no++;
				$jml	= $jml+$r['tot'];
				$total 	= $total+$r['cost_tonase'];
				$total_mitra 	= $total_mitra+$r['cost_mitra'];
			}
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="4"><b>Total </b></td>
			<td style="text-align: center;border: solid 1px black;font-weight:bold;"><?php echo format_no($jml); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($total); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($total_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_no($total + $total_mitra); ?></td>
		</tr>
	</table>
	<br />Keterangan:
</page>