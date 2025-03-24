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
	$('#weighingView').dataTable({
		dom: "<'row'<'col-sm-8'l><'col-sm-4'f>>" +  
			"<'row'<'col-sm-12'tr>>" +
			"<'row'<'col-sm-7'i><'col-sm-5'p>>",
		"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="weighingView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size:12px;">
	<thead>
		<tr>
			<th style="width:5%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NO</th>
			<th style="width:5%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NO NOTA</th>
			<th style="width:7%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NOPOL</th>
			<th style="width:15%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">PEDAGANG</th>
			<th style="width:9%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">MASUK</th>
			<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">KELUAR</th>
			<th style="width:7%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NON KOMODITI</th>
			<th style="width:7%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">RAFAKSI</th>
			<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">NETTO</th>
			<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">STATUS</th>
			<th style="width:8%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">STATUS MITRA</th>
			<th style="width:10%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">JAM & TGL MASUK</th>
			<th style="width:6%; font-size:12px; text-align:center; vertical-align: middle;" colspan="3">STATUS PRINT</th>
			<th style="width:5%; font-size:12px; text-align:center; vertical-align: middle;" rowspan="2">AKSI</th>
		</tr>
		<tr>
			<th style="font-size:11px;" class="font-size:12px; text-center; vertical-align: middle;">TIMBANG</th>
			<th style="font-size:11px;" class="font-size:12px; text-center; vertical-align: middle;">CEK</th>
			<th style="font-size:11px;" class="font-size:12px; text-center; vertical-align: middle;">TAGIHAN</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$date_plus 	= strtotime('-15 day', strtotime($date_now));
			$week		= date('Y-m-j' , $date_plus);
			$query 		= mysql_query("select weighing_id, nopol, merchant_id, weight_in, weight_out, 
			noncommodity, rafaksi, netto, laborer_sts, cost, weighing_sts, weighing_sts2, time_in, date_in, date_check, p1, p2 from weighing  
			where (date_in  between '".$week."' and '".$date_now."') and 
			(weighing_sts = 'Keluar' || weighing_sts = 'Batal' || weighing_sts = 'Lunas') and
			date_check is not null
			order by weighing_id desc");
			$no			= 0;
			while($data=mysql_fetch_array($query)) { 
				$merchant 	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php $no++; echo $no; ?></td>   
			<td style="text-align:center; vertical-align: middle;">
				<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
					<?php echo 'TG'.$data['weighing_id']; ?>
				</div>
			</td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo $data['nopol']; ?></td>
			<td style=" vertical-align: middle; font-size:12px;"><?php echo $merchant; ?></td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo $data['weight_in'] != NULL ? $data['weight_in']. ' Kg' : '0 Kg'; ?></td> 
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo $data['weight_out'] != NULL ? $data['weight_out']. ' Kg' : '0 Kg'; ?></td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo $data['noncommodity'] != NULL ? $data['noncommodity']. ' Kg' : '0 Kg'; ?></td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo $data['rafaksi'] != NULL ? $data['rafaksi']. ' Kg' : '0 Kg'; ?></td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo $data['netto'] != NULL ? $data['netto']. ' Kg' : '0 Kg'; ?></td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;">
				<?php echo $data['weighing_sts']; ?>
				<br><div style='background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 0 auto 0 auto; width: 125px; font-size: 11px;\'><?php echo $data['weighing_sts2'] ?></div>
			</td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo $data['laborer_sts']; ?></td>
			<td style="text-align:center; vertical-align: middle; font-size:12px;"><?php echo date("H:s", strtotime($data['time_in'])); ?> <br /><?php echo date("d M Y", strtotime($data['date_in'])); ?></td>
			<td style="text-align:center; vertical-align: middle; ">
				<?php 
					if ($data['p1'] != NULL) { ?>
						<i class="fa fa-check text-success" style="font-size:23px"></i>
					<?php } else { ?>
						<i class="fa fa-times text-danger" style="font-size:23px"></i>
					<?php }
				?>
			</td>
			<td style="text-align:center; vertical-align: middle;">
				<?php 
					if ($data['date_check'] != NULL) { ?>
						<i class="fa fa-check text-success" style="font-size:23px"></i>
					<?php } elseif ($data['date_check'] == NULL) { ?>
						<i class="fa fa-times text-danger" style="font-size:23px"></i>
					<?php }
				?>
			</td>
			<td style="text-align:center; vertical-align: middle;">
				<?php 
					if ($data['p2'] != NULL) { ?>
						<i class="fa fa-check text-success" style="font-size:23px"></i>
					<?php } else { ?>
						<i class="fa fa-times text-danger" style="font-size:23px"></i>
					<?php }
				?>
			</td>
			<td style=" vertical-align: middle; font-size:12px;">
				<a href="check_edit.php?id=<?php echo $data['weighing_id']; ?>" style="text-decoration: none;">
					<button class="btn btn-outline btn-block btn-primary btn-xs"><i class="fa fa-check-square-o fa-fw"></i> Check</button>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<!--</div>-->