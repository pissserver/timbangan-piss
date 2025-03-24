<?php
	include "../connection.php";
	$date1		= '2016-10-01';
	$date2		= $_POST['date2'];
	$commodity	= $_POST['commodity'];
	$sort		= $_POST['sortir'];
	$date 		= date("d-m-Y");
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
		REKAP TAGIHAN TONASE & MITRA BELUM TERBAYAR SELURUH PEDAGANG
		<br>PASAR INDUK SURABAYA SIDOTOPO (PISS)
	</div>
    <br/>
	<table style="margin-bottom: 5px;font-size:10pt; width:300px;" >
		<tr>
			<td style="width: 35%;">Tanggal</td>
			<td style="width: 65%;">: <?php echo date("d-m-Y", strtotime($date2)); ?></td>
		</tr>
		<tr>
			<td style="width: 35%;">Jenis Pedagang</td>
			<td style="width: 65%;">: <?php echo $commodity; ?></td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:7pt; width:800px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 3%; border: solid 1px black;">NO</th>
			<th style="text-align: center;width: 20%;border: solid 1px black;">PEDAGANG</th>
			<th style="text-align: center;width: 17%;border: solid 1px black;">LAPAK</th>
			<th style="text-align: center;width: 7%;border: solid 1px black;">UMUR TAGIHAN</th>
			<th style="text-align: center;width: 7%;border: solid 1px black;">STS MITRA</th>
			<th style="text-align: center;width: 7%;border: solid 1px black;">TOTAL NOTA</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">TONASE (RP)</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">MITRA (RP)</th>
			<th style="text-align: center;width: 9%;border: solid 1px black;">TOTAL (RP)</th>
		</tr>
		<?php
			$sql 	= '';
			$jml2 	= 0;
			$total_tonase = 0;
			$total_mitra = 0;
			if($sort==''){}
			if($commodity=='Semua'){
				$sql = "select m.merchant_id as id, m.merchant_name as name, m.merchant_type as type, SUM(cost) as total, laborer_sts
				from merchant m, weighing w, rent r, lapak l
				where m.merchant_id = w.merchant_id and 
				r.merchant_id = m.merchant_id and
				r.lapak_id = l.lapak_id and
				m.merchant_status = 'Aktif' and
				(w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
				(w.date_pay > '".$date2."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')
				group by id order by ".$sort."";
			}else{
				$sql = "select m.merchant_id as id, m.merchant_name as name, m.merchant_type as type, SUM(cost) as total, laborer_sts
				from merchant m, weighing w, rent r, lapak l
				where m.merchant_id = w.merchant_id and 
				m.merchant_id = r.merchant_id and
				r.lapak_id = l.lapak_id and
				m.merchant_type = '".$commodity."' and 
				m.merchant_status = 'Aktif' and
				(w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
				(w.date_pay > '".$date2."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')
				group by id order by ".$sort."";
			}
			$query 	= mysql_query($sql);
			$no 	= 1;
			while($r= mysql_fetch_array($query)){
				$sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
						where r.lapak_id = l.lapak_id and 
						r.merchant_id = '".$r['id']."' and
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
				
				//Hitung Jumlah Nota
				$jml	= executeScalar("select count(weighing_id) from weighing
				where merchant_id = '".$r['id']."' and
				(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
				(date_pay > '".$date2."' || date_pay is NULL) and
				weighing_sts != 'Batal' and
				(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");
				
				//Hitung Umur Tagihan
				$last_date	= executeScalar("select date_check from weighing
				where merchant_id = '".$r['id']."' and
				(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
				(date_pay > '".$date2."' || date_pay is NULL) and
				weighing_sts != 'Batal' and
				(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung') order by weighing_id limit 1");
				
				$date3 	= new DateTime(date("Y-m-d", strtotime($last_date)));
				$date4 	= new DateTime(date("Y-m-d", strtotime($date2)));
				$diff 	= $date4->diff($date3)->format("%a");
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;"><?php echo $no; ?></td>
			<td style="text-align: left;border: solid 1px black;"><?php echo $r['name']; ?></td>
			<td style="text-align: left;border: solid 1px black;"><?php echo $lapak; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $diff.' Hari'; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo $r['laborer_sts']; ?></td>
			<td style="text-align: center;border: solid 1px black;"><?php echo format_no($jml); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($cost_tonase); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($cost_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;"> <?php echo format_no($cost_tonase + $cost_mitra); ?></td>
		</tr>
		<?php 
				$no++;
				$jml2	= $jml2+$jml;
				$total_tonase = $total_tonase + $cost_tonase;
				$total_mitra = $total_mitra + $cost_mitra;
			}
		?>
		<tr>
			<td style="text-align: center;border: solid 1px black;" colspan="5"><b>Total </b></td>
			<td style="text-align: center;border: solid 1px black;font-weight:bold;"><?php echo format_no($jml2); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_rupiah($total_tonase); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_rupiah($total_mitra); ?></td>
			<td style="text-align: right;border: solid 1px black;font-weight:bold;"> <?php echo format_rupiah($total_tonase + $total_mitra); ?></td>
		</tr>
	</table>
	<br />Keterangan:
</page>