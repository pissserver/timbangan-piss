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
	$('#pay_accView').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="pay_accView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 12px;">
	<thead>
		<tr>
			<th style="width:5%; text-align:center;">ID NOTA</th>
			<th style="width:10%; text-align:center;">TGL TAGIHAN</th>
			<th style="width:8%; text-align:center;">NOPOL</th>
			<th style="width:8%; text-align:center;">KODE</th>
			<th style="width:25%; text-align:center;">PEDAGANG</th>
			<th style="width:8%; text-align:center;">JENIS</th>
			<th style="width:8%; text-align:center;">NETTO</th>
			<th style="width:10%; text-align:center;">SETORAN</th>
			<th style="width:8%; text-align:center;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
			//$date_plus 	= strtotime('-8 day', strtotime($date_now));
			//$onemonth	= date('Y-m-j' , $date_plus);
			$query 		= mysql_query("	select 	w.weighing_id, 
												w.merchant_id, w.nopol, 
												w.netto, 
												w.cost,
												w.cost_tonase, 
												w.weighing_sts, 
												w.weighing_sts3, 
												w.date_out, 
												w.date_check,
												m.merchant_type 
										from 	weighing w, merchant m	
										where 	w.merchant_id = m.merchant_id and 
												m.merchant_status = 'Aktif' and
												w.weighing_sts = 'Keluar' and 
												w.weighing_sts3 = 'Pending' and 
												(w.date_in  between '2016-09-01' and '".$date_now."') 
										order by weighing_id desc");
			while($data	= mysql_fetch_array($query)) { 
			$merchant 	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['weighing_id']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo date("d M Y", strtotime($data['date_out'])); ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['nopol']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['merchant_id']; ?></td>
			<td style="text-align:left; vertical-align: middle;"><?php echo $merchant; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['merchant_type']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['netto']; ?> Kg</td>
			<td style="text-align:right; vertical-align: middle;"><?php echo format_rupiah($data['cost_tonase']); ?></td>
			<td style="text-align:center; vertical-align: middle;">
				<a href="pay_action.php?task=cancel&type=ko&id=<?php echo $data['weighing_id']; ?>">
					<button class="btn btn-outline btn-block btn-primary btn-xs">Batal</button>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<!--</div>-->