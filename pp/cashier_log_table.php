<?php
	include "../connection.php";
?>
<!-- jquery -->
<script src="../dataTables/jquery.dataTables.js"></script>
<script src="../dataTables/dataTables.tableTools.js"></script>
<script src="../dataTables/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
	//data table
	$('#cashierLog').dataTable({"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="cashierLog" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 12px;">
	<thead>
		<tr>
			<th style="vertical-align: middle; text-align: center; width:5%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:11%;">NAMA KASIR</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">NO NOTA</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">NOPOL</th>
			<th style="vertical-align: middle; text-align: center; width:13%;">PEDAGANG</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">AKSI</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">KETERANGAN</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">JAM</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">TANGGAL</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no		= 1;
			$date_plus 	= strtotime('-7 day', strtotime($date_now));
			$yesterday	= date('Y-m-j' , $date_plus);
			$query 	= mysql_query("select * from cashier_log where date(action_time) >= '".$yesterday."' order by action_time desc");
			while($data=mysql_fetch_array($query)){
				$merchant 	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['cashier_merchant_id']."'");
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $no; ?></td>
			<td style="text-align: center; vertical-align: middle;"><?php echo $data['cashier_user']; ?></td>
			<td style="text-align:center; vertical-align: middle;">
				<?php 
					$wid 	= sprintf("%07d", $data['cashier_target']);
					$status = executeScalar("select weighing_sts from weighing where weighing_id = '".$data['cashier_target']."'");
					if($status=='Masuk'||$status=='Batal'){ 
						//$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$data['cashier_target']."'"); ?>
						
						<div style="vertical-align: middle; background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
							<?php echo 'TB'.$wid; ?>
						</div>
				<?php }
					elseif($status=='Keluar'||$status=='Lunas'){
						//$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$data['cashier_target']."'"); ?>
						
						<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
							<?php echo 'TG'.$wid; ?>
						</div>
					<?php } 
				?>
			</td>
			<td style="vertical-align: middle; text-align: center;"><?php echo $data['cashier_nopol']; ?></td>
			<td style="vertical-align: middle; text-align: left;"><?php echo $merchant; ?></td>
			<td style="vertical-align: middle; text-transform: capitalize;"><?php echo $data['cashier_action']; ?></td>
			<td style="vertical-align: middle; text-transform: capitalize;"><?php echo $data['cashier_reason'] != NULL ? $data['cashier_reason'] : 'Tidak ada keterangan' ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo date("H:i", strtotime($data['action_time'])); ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo date("d M Y", strtotime($data['action_time'])); ?></td>
		</tr>
		<?php 
			$no++;
			} ?>
	</tbody>
</table>
<!--</div>-->