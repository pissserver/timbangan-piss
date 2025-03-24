<?php
	include "../connection.php";
?>
<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm">
    <?php
		$year 		= date("y");
		$date_now 		= date("Y-m-d");
		$date		= $_GET['date'];
		$work_date	= $_GET['date'];	
		
		$total_share		= executeScalar("select sum(fee_B001)+
											sum(fee_B002)+
											sum(fee_B003)+
											sum(fee_B004)+
											sum(fee_B005)+
											sum(fee_B006)+
											sum(fee_S001)+
											sum(fee_S002)+
											sum(fee_S003)+
											sum(fee_S004)+
											sum(fee_S005)+
											sum(fee_S006)+
											sum(fee_S007)+
											sum(fee_S008)+
											sum(fee_S009)+
											sum(fee_S010)+
											sum(fee_S011)+
											sum(fee_S012)+
											sum(fee_M01)+
											sum(fee_M02)+
											sum(fee_M03)+
											sum(fee_M04) 
									from laborer_fee2 
									where fee_date = '".$date."'");

			// Lakukan query untuk mendapatkan kode pekerja
			$result = mysql_query("SELECT laborer_code FROM laborer WHERE laborer_name != '-'");

			// Pastikan hasil tidak kosong
			if ($result) {
				$fees = [];
				$names = [];

				// Mengambil semua kode pekerja dan menyimpannya dalam array
				while ($row = mysql_fetch_assoc($result)) {
					$code = $row['laborer_code'];

					// Periksa kode sesuai dengan kriteria yang ditentukan
					if ((strpos($code, 'B') === 0 && $code <= 'B006') || 
						(strpos($code, 'S') === 0 && $code <= 'S012') || 
						(strpos($code, 'M') === 0 && $code <= 'M004')) {
						
						// Mengambil total fee dari laborer_fee2 berdasarkan kode dan tanggal
						$fee = executeScalar("SELECT SUM(fee_$code) FROM laborer_fee2 WHERE DATE(fee_date) = '".$date."'");

						// Mengambil nama pekerja berdasarkan laborer_code
						$name = executeScalar("SELECT laborer_name FROM laborer WHERE laborer_code = '$code'");

						// Menyimpan fee dan nama ke dalam array
						$fees[$code] = $fee;
						$names[$code] = $name;
					}
				}
			} else {
				// Tangani jika query gagal
				echo "Error: " . mysql_error();
			}

	?>
	<table style="width:95%; border:0px; margin-top:0px; margin-left:5px; margin-right:5px; font-size:9pt;">
		<tr>
			<td style="text-align: left; width: 50%">PISS</td>
			<td style="text-align: center; width: 5%"></td>
			<td style="text-align: right; width: 45%"><?php echo date("d-m-Y", strtotime($date_now)); ?></td>
		</tr>
	</table><br/>
    <div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:0px; margin-bottom:5px;">
		PENGEMBALIAN UPAH MITRA
	</div>
	<table style="margin-bottom: 5px;font-size:10pt;" id="tableCheckout">
		<tr><td>Tanggal Kerja	</td><td> : <?php echo date("d-m-Y", strtotime($work_date)); ?> </td></tr>
		<tr><td>Total Uang		</td><td> : <b><?php echo format_rupiah($total_share); ?></b> </td></tr>
	</table>
    <br/><br>
	<table style="border: solid 1px black; border-collapse: collapse; margin-bottom: 5px;font-size:10pt; width:300px; margin-right:0px;" id="tableNonkomoditi">
		<tr>
			<th style="text-align: left;width: 10%; border: solid 1px black; text-align:center;">No</th>
			<th style="text-align: center;width: 45%;border: solid 1px black;">Nama (Kode)</th>
			<th style="text-align: center;width: 30%;border: solid 1px black;">Jumlah (RP)</th>
			<th style="text-align: left;width: 15%; border: solid 1px black; text-align:center;">TTD</th>
		</tr>
		
		<?php 
			$index = 1;
			foreach ($fees as $code => $amount) {
				$name = isset($names[$code]) ? $names[$code] : 'Nama Tidak Ditemukan';
				
				echo "<tr style='font-size: 11px;'>
						<td style='border: solid 1px black; text-align:center;'>$index.</td>
						<td style='border: solid 1px black; text-align:left;'>&nbsp;$name ($code)</td>
						<td style='border: solid 1px black; text-align:right;'>&nbsp;" . format_no($amount) . "</td>
						<td style='border: solid 1px black; text-align:center;'><p>&nbsp;</p></td>
					</tr>";

				$index++;
			}
		?>

		<tr style='font-size: 12px;'>
			<td style="border: solid 1px black; text-align:center;" colspan="2"><b> Total</b></td>
			<td colspan="2" style="border: solid 1px black; text-align:center"><b> <?php echo format_rupiah($total_share); ?></b></td>
		</tr>
	</table>

	<table style="width: 95%;  border: 0px black; margin-top:150px; margin-left:25px; margin-right:25px; font-size:9pt;">
		<tr>
			<td style="text-align: left;width: 35%">Manajemen</td>
			<td style="text-align: center;width: 15%"></td>
			<td style="text-align: right;width: 35%">Security </td>
		</tr>
	</table>
</page>