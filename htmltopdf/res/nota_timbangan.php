<?php
    session_start();
    include "../connection.php";

    $cashier_name = '';
    $spv_name = '';
    
	if (!empty($_SESSION['checker'])) {
		$cashier_name = executeScalar("SELECT checker_name FROM checker WHERE checker_code = '".$_SESSION['checker']."'");
	} elseif (isset($_SESSION['role']) && (strtolower($_SESSION['role']) == 'spv')) {
		$spv_name = executeScalar("SELECT name FROM admin WHERE username = '".$_SESSION['username']."'");
	} else {
		header("Location: login.html");
		exit();
	}

	date_default_timezone_set('Asia/Jakarta');
?>
<page backtop="0mm" backbottom="0mm" backleft="5mm" backright="5mm">
    <?php
		$date 		= date("d M Y");
		$jam 		= date("H:i");
		$id			= $_GET['id'];
		$idq 		= sprintf("%07d", $id);
		$sql 		= mysql_query("select * from weighing where weighing_id = '".$id."'");
		
		while($r= mysql_fetch_array($sql)){
				$prnt = mysql_query("UPDATE `weighing` SET p1	= '1' where weighing_id 	= '".$id."'");
				//simpan data siapa yg ngeprint
				$dt_now = date('Y-m-d H:i:s');
				$print_user = !empty($spv_name) ? $spv_name : $cashier_name;
				$print_role = !empty($spv_name) ? 'spv' : 'cashier';
		
				$insert = mysql_query("INSERT INTO weighing_print 
						(weighing_id, print_user, print_role, print_date, print_nopol, print_merchant_id, print_action) 
						VALUE 
						('$id', '$print_user', '$print_role', '$dt_now', '".$r['nopol']."', '".$r['merchant_id']."', 'Print Nota Timbangan')");

				$name		= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
				$nick		= executeScalar("select merchant_nick from merchant where merchant_id = '".$r['merchant_id']."'");
				$checker	= executeScalar("select checker_code from checker where checker_id = '".$r['checker_id']."'");
				$lapak 		= executeScalar("select l.lapak_name from rent r, lapak l 
				where r.lapak_id = l.lapak_id and r.merchant_id = '".$r['merchant_id']."' 
				and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') limit 1");
				$goods		= executeScalar("select goods_name from goods where goods_id = '".$r['goods_id']."'");
				$time1		= executeScalar("select TIME_FORMAT(`time_in`, '%H:%i') from weighing where weighing_id = '".$r['weighing_id']."'");
				//$time1	= $r['date_in'].' '.$r['time_in'];
				//$time		= time_format($r['time_in'], '%H:%i');
	?>
	<page_header>
    </page_header>
	<table style="width:100%; border:0px; margin-top:0px; margin-right:5px; font-size:9pt;">
		<tr>
			<td style="text-align: left; width: 50%">No Nota: TB<?php echo $idq; ?></td>
			<td style="text-align: center; width: 5%"></td>
			<td style="text-align: right; width: 45%"><?php echo date("d M Y", strtotime($date)).' ('.$jam.') <b>('.$checker.')</b>'; ?></td>
		</tr>
	</table>
	<br />
    <div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:-5px; margin-bottom:5px;">
		NOTA TIMBANG
	</div>
	<table style="font-size:9pt; width:100%;" id="tableCheckout" >
		<tr>
			<td>Nama / Alias / Kode	</td>
			<td> : </td>
			<td style="width: 70%;"><?php echo $name.' / '.$nick. ' / '.$r['merchant_id']; ?> </td>
		</tr>
		<tr>
			<td>Lapak</td>
			<td> : </td>
			<td><?php echo $lapak; ?> </td>
		</tr>
		<tr>
			<td>Nopol</td>
			<td> : </td>
			<td><?php echo $r['nopol']; ?></td>
		</tr>
		<tr>
			<td>Berat Masuk</td>
			<td> : </td>
			<td><b><?php echo format_no($r['weight_in']); ?></b> Kg <span style="margin-left: 5px;">Barang : <?php echo $goods; ?></span></td>
    	</tr>
	</table>
    <br/>
	<table style="border: solid 1px black; border-collapse: collapse; font-size:9pt;width:300px;margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: center;width: 7%; border: solid 1px black; text-align:center;">No</th>
			<th style="text-align: center;width: 40%;border: solid 1px black;">Non Komoditi</th>
			<th style="text-align: center;width: 33%;border: solid 1px black;">Jenis / Berat</th>
			<th style="text-align: center;width: 20%;border: solid 1px black;">Jumlah</th>
		</tr>
		<tr>
			<td style="border: solid 1px black; text-align:center;">1.</td>
			<td style="border: solid 1px black;">Peti</td>
			<td style="border: solid 1px black;"> </td>
			<td style="border: solid 1px black;"> </td>
		</tr>
		<tr>
			<td style="border: solid 1px black; text-align:center;">2.</td>
			<td style="border: solid 1px black;">Krat / Keranjang</td>
			<td style="border: solid 1px black;"> </td>
			<td style="border: solid 1px black;"> </td>
		</tr>
		<tr>
			<td style="border: solid 1px black; text-align:center;">3.</td>
			<td style="border: solid 1px black;">Kardus</td>
			<td style="border: solid 1px black;"> </td>
			<td style="border: solid 1px black;"> </td>
		</tr>
		<!-- <tr>
			<td style="border: solid 1px black; text-align:center;">4.</td>
			<td style="border: solid 1px black;">Damen</td>
			<td style="border: solid 1px black;"> </td>
			<td style="border: solid 1px black;"> </td>
		</tr> -->
		<tr>
			<td style="border: solid 1px black; text-align:center;">4.</td>
			<td style="border: solid 1px black;">Orang</td>
			<td style="border: solid 1px black;"> </td>
			<td style="border: solid 1px black;"> </td>
		</tr>
	</table>
	<br />
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:9pt;width:300px;margin-right:0px;" id="tablePedagang">
		<tr style="border: 1px black;">
			<th style="text-align: center;width: 7%; border: solid 1px black; text-align:center;">No</th>
			<th style="text-align: center;width: 48%;border: solid 1px black;">Pedagang Lain</th>
			<th style="text-align: center;width: 25%;border: solid 1px black;">Netto</th>
			<th style="text-align: center;width: 20%;border: solid 1px black;">TTD</th>
		</tr>
		<?php
			$i=0;
			while ($i<3) {
			$i++;
		?>
		<tr>
			<td style="border: solid 1px black; text-align:center;"><?php echo $i; ?>.</td>
			<td style="border: solid 1px black; text-align:center;"></td>
			<td style="border: solid 1px black; text-align:center;"></td>
			<td style="border: solid 1px black; text-align:center;"></td>
		</tr>
		<?php } ?>
	</table>
	<?php } ?>
	<br/>
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:9pt;width:300px;margin-right:0px;" id="tbMitraMandor">
		<tr style="border: 1px black;">
			<th style="text-align: left;width: 75%; border: solid 1px black; text-align:center;">Mitra</th>
			<th style="text-align: left;width: 25%;border: solid 1px black; text-align:center;">Mandor</th>
		</tr>
		<tr>
			<td style="border: solid 1px black;"><br/><br/><br/></td>
			<td style="border: solid 1px black;"></td>
		</tr>
	</table>
	<br/>
	<table style="border-collapse: collapse; margin-bottom: 5px;font-size:7pt;width:300px;margin-right:0px;" id="tableTtd">
		<tr>
            <th style="text-align: center;width: 33%;">Kasir</th>
			<th></th>
            <th style="text-align: center;width: 33%;">Checker</th>
        </tr>
        <tr>
            <td style="text-align: center;width: 50%;"><br><br>&nbsp;( ................... )</td>
			<td></td>
            <td style="text-align: center;width: 50%;"><br><br>&nbsp;( ................... )</td>
        </tr>
	</table>
	<!-- <div style="font-size: 14px; font-weight: bold; text-align: center; margin-top:10px; margin-bottom:5px;">
		== PASAR INDUK SURABAYA SIDOTOPO ==
	</div> -->
<!-- 	
    <page_footer>
    </page_footer> -->
</page>