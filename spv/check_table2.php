<?php
	include "connection.php";
?>
<!-- jquery -->
<script src="../dataTables/jquery.dataTables.js"></script>
<script src="../dataTables/dataTables.tableTools.js"></script>
<script src="../dataTables/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
	//data table
	$('#checkBeforeView').dataTable({
		dom: "<'row'<'col-sm-8'l><'col-sm-4'f>>" +  
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-7'i><'col-sm-5'p>>",
		"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="checkBeforeView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="vertical-align: middle; width:4%; text-align:center;">NO</th>
			<th style="vertical-align: middle; width:4%; text-align:center;">NO NOTA</th>
			<th style="vertical-align: middle; width:5%; text-align:center;">NOPOL</th>
			<th style="vertical-align: middle; width:15%; text-align:center;">PEDAGANG</th>
			<th style="vertical-align: middle; width:8%; text-align:center;">MASUK</th>
			<th style="vertical-align: middle; width:8%; text-align:center;">KELUAR</th>
			<th style="vertical-align: middle; width:8%; text-align:center;">NON KOMODITI</th>
			<th style="vertical-align: middle; width:8%; text-align:center;">RAFAKSI</th>
			<th style="vertical-align: middle; width:8%; text-align:center;">NETTO</th>
			<th style="vertical-align: middle; width:8%; text-align:center;">STATUS</th>
			<th style="vertical-align: middle; width:10%; text-align:center;">JAM & TGL MASUK</th>
			<th style="vertical-align: middle; width:6%; text-align:center;">PRINT TIMBANG</th>
			<th style="vertical-align: middle; width:6%; text-align:center;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$date_plus 	= strtotime('-15 day', strtotime($date_now));
			$week		= date('Y-m-j' , $date_plus);
			$query 		= mysql_query("select weighing_id, nopol, merchant_id, weight_in, weight_out, 
			noncommodity, rafaksi, netto, cost, weighing_sts, time_in, date_in, p1, p2 from weighing  
			where (date_out between '".$week."' and '".$date_now."') and 
			(weighing_sts = 'Keluar' || weighing_sts = 'Lunas') and
			date_check is null
			order by weighing_id desc");
			$no			= 0;
			while($data=mysql_fetch_array($query)) { 
				$merchant 	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
				$c 	= executeScalar("select date_check from weighing where weighing_id = '".$data['weighing_id']."'");
				if($c!=''||$c!=null){ //Jika tgl check Tidak Kosong / Ada
					$c = 'C';
				}else{
					$c = '';
				}
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php $no++; echo $no; ?></td>
			<td style="text-align:center; vertical-align: middle;">
				<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
					<?php echo 'TG'.$data['weighing_id']; ?>
				</div>
			</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['nopol']; ?></td>
			<td style="vertical-align: middle;"><?php echo $merchant; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['weight_in'] != NULL ? $data['weight_in'] : '0'; ?> Kg</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['weight_out'] != NULL ? $data['weight_out'] : '0'; ?> Kg</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['noncommodity'] != NULL ? $data['noncommodity'] : '0'; ?> Kg</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['rafaksi']!= NULL ? $data['rafaksi'] : '0'; ?> Kg</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['netto']; ?> Kg</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['weighing_sts']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo date("H:s", strtotime($data['time_in'])); ?> <br /><?php echo date("d M Y", strtotime($data['date_in'])); ?></td>
			<!-- <td style="text-align:center; vertical-align: middle;"><?php echo $data['p1'].' '.$data['p2'].' '.$c; ?></td> -->
			<td style="text-align:center; vertical-align: middle;">
				<?php if ($data['p1'] == NULL) { ?>
					<i class="fa fa-close text-danger" style="font-size:23px"></i>
				<?php } elseif ($data['p1'] != NULL) { ?>
					<i class="fa fa-check text-success" style="font-size:23px"></i>
				<?php } ?>
			</td>
			<td style=" vertical-align: middle;">
				<a style="text-decoration:none;" href="../htmltopdf/weighing_result.php?id=<?php echo $data['weighing_id']; ?>" target="blank">
					<button class="btn btn-outline btn-block btn-primary btn-xs"><i class="fa fa-print fa-fw"></i> Rekap Timbang</button>
				</a>
				<a style="text-decoration:none;" href="check_edit.php?id=<?php echo $data['weighing_id']; ?>">
					<button style="margin-top: 10px;" class="btn btn-outline btn-block btn-primary btn-xs"><i class="fa fa-check-square-o fa-fw"></i> Check</button>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<!--</div>-->