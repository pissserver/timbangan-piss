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
		$date 			= date("d M Y");
		$jam 			= date("H:i");
		$id				= $_GET['id'];
		$idq 			= sprintf("%07d", $id);
		$tarif			= 0;
		$sql 			= mysql_query("select * from weighing where weighing_id = '".$id."'");
		while($r= mysql_fetch_array($sql)){
			$name		= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
			$nick		= executeScalar("select merchant_nick from merchant where merchant_id = '".$r['merchant_id']."'");
			$checker	= executeScalar("select checker_code from checker where checker_id = '".$r['checker_id']."'");
			$checkerName= executeScalar("select checker_name from checker where checker_id = '".$r['checker_id']."'");
			$goods		= executeScalar("select goods_name from goods where goods_id = '".$r['goods_id']."'");
			$nc			= executeScalar("select noncommodity_name from noncommodity where noncommodity_id = '".$r['noncommodity_id']."'");
			$time1		= executeScalar("select TIME_FORMAT(`time_in`, '%H:%i') from weighing where weighing_id = '".$r['weighing_id']."'");
			$time2		= executeScalar("select TIME_FORMAT(`time_out`, '%H:%i') from weighing where weighing_id = '".$r['weighing_id']."'");
			$date_in	= date("d M Y", strtotime($r['date_in']));
			$date_out	= date("d M Y", strtotime($r['date_out']));

			//simpan data siapa yg ngeprint
			$dt_now = date('Y-m-d H:i:s');
			$print_user = !empty($spv_name) ? $spv_name : $cashier_name;
			$print_role = !empty($spv_name) ? 'spv' : 'cashier';

			$insert = mysql_query("INSERT INTO weighing_print 
					(weighing_id, print_user, print_role, print_date, print_nopol, print_merchant_id, print_action) 
					VALUE 
					('$id', '$print_user', '$print_role', '$dt_now', '".$r['nopol']."', '".$r['merchant_id']."', 'Print Nota Hasil Timbang')");
	?>
	<page_header>
        
    </page_header>
	<table style="width:100%; border:0px; margin-top:0px; font-size:9pt;">
		<tr>
			<td style="text-align: left; width: 50%">No Nota: TB<?php echo $idq; ?></td>
			<td style="text-align: center; width: 1%"></td>
			<td style="text-align: right; width: 45%"><?php echo date("d M Y", strtotime($date)).' ('.$jam.')'; ?></td>
		</tr>
	</table>
	<br />
    <div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:0px;">
		REKAP HASIL TIMBANG KENDARAAN
	</div>
	<p style="font-size: 10px; font-weight: bold; text-align: center; margin-top:0px;">(PASAR INDUK SURABAYA SIDOTOPO)</p>
	<br/>
	<table style="width: 100%; margin-bottom: 5px;font-size:11px; margin-top:-5px;" id="tableCheckout">
		<tr>
			<td style="padding-bottom: 10px;">Nama / Alias / Kode </td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="width: 70%; padding-bottom: 10px;"><?php echo $name. ' / ' .($nick != '' ? $nick : '-'). ' / ' . $r['merchant_id']; ?> </td>
		</tr>
		<tr>
			<td style="padding-bottom: 10px;">Nopol</td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="padding-bottom: 10px;"><?php echo $r['nopol']; ?></td>
		</tr>
		<tr>
			<td style="padding-bottom: 10px;">Tgl & Jam Masuk</td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="padding-bottom: 10px;"><?php echo $date_in.' ('.$time1.')'; ?></td>
		</tr>
		<tr>
			<td style="padding-bottom: 10px;">Tgl & Jam Keluar</td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="padding-bottom: 10px;"><?php echo $date_out.' ('.$time2.')'; ?></td>
		</tr>
		<tr>
			<td style="padding-bottom: 10px;">Barang</td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="padding-bottom: 10px;"><?php echo $goods; ?></td>
		</tr>
		<tr>
			<td style="padding-bottom: 10px;">Berat Masuk</td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="padding-bottom: 10px;"><?php echo format_no($r['weight_in']); ?> Kg</td>
		</tr>
		<tr>
			<td style="padding-bottom: 10px;">Berat Keluar</td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="padding-bottom: 10px;"><?php echo format_no($r['weight_out']); ?> Kg</td>
		</tr>
		<!-- <tr><td style="padding-bottom: 10px;">Berat NonKomoditi	</td><td style="padding-bottom: 10px;"> : 0 Kg</td></tr> -->
		<tr>
			<td style="padding-bottom: 10px;">Berat Bersih</td>
			<td style="padding-bottom: 10px;"> : </td>
			<td style="padding-bottom: 10px;"><b style="font-size: 12px; font-weight: bold;"><?php echo format_no($r['netto']); ?></b> Kg</td>
		</tr>
	</table><br/>

		<table style="width: 100%; border: 0px black; font-size:7pt; margin-top:30px;">
			<tr>
				<th style="text-align: center;width: 50%;">Kasir</th>
				<th></th>
				<th style="text-align: center;width: 50%;">Penerima Nota</th>
			</tr>
			<tr>
				<td style="text-align: center;width: 50%;"><br><br><br>&nbsp;( <?php echo $checkerName ?> )</td>
				<td></td>
				<td style="text-align: center;width: 50%;"><br><br><br>&nbsp;( ................... )</td>
			</tr>
		</table>
		<!-- <br><br>
		<div style="font-size: 9px; font-weight: bold; text-align: center;">
			== PASAR INDUK SURABAYA SIDOTOPO ==
		</div> -->
	<?php } ?>
</page>