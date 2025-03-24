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
	$('#transactionView').dataTable({"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="transactionView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="vertical-align: middle; text-align: center; width:5%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:13%;">NAMA SPV</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">NO NOTA</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">NOPOL</th>
			<th style="vertical-align: middle; text-align: center; width:13%;">PEDAGANG</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">AKSI</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">JAM</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">TANGGAL</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no		= 1;
			date_default_timezone_set('Asia/Jakarta');
			$date_now   = date('Y-m-d 23:59:59');
			$date_plus  = strtotime('-30 days', strtotime($date_now));
			$yesterday  = date('Y-m-d 00:00:00', $date_plus);
			$query 	= mysql_query("select * from weighing_print where print_date between '".$yesterday."' and '".$date_now."' and print_role = 'spv' order by print_date desc");
			while($data=mysql_fetch_array($query)){
		?>
		<tr>
			<td style="vertical-align: middle; text-align:center;"><?php echo $no; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['print_user']; ?></td>
			<td style="text-align:center; vertical-align: middle;">
				<?php 
					$wid 	= sprintf("%07d", $data['weighing_id']);
					$status = executeScalar("select weighing_sts from weighing where weighing_id = '".$data['weighing_id']."'");
					if($status=='Masuk'||$status=='Batal'){ 
						//$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$data['spv_target']."'"); ?>
						
						<div style="vertical-align: middle; background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
							<?php echo 'TB'.$wid; ?>
						</div>
				<?php }
					elseif($status=='Keluar'||$status=='Lunas'){
						//$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$data['spv_target']."'"); ?>
						
						<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
							<?php echo 'TG'.$wid; ?>
						</div>
					<?php } 
				?>
			</td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['print_nopol']; ?></td>
			<td style="vertical-align: middle; text-align:left;"><?php echo $data['print_merchant_id']; ?></td>
			<td style="vertical-align: middle; text-align:left;"><?php echo $data['print_action']; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo date("H:i", strtotime($data['print_date'])); ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo date("d M Y", strtotime($data['print_date'])); ?></td>
		</tr>
		<?php 
			$no++;
			} ?>
	</tbody>
</table>
<!--</div>-->