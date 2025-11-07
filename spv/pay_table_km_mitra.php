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
	$('#payMitraView').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="payMitraView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 11px;">
	<thead>
		<tr>
		<th style="vertical-align: middle; text-align: center; width:5%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">ID KMM</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">NAMA KASIR</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">TOTAL SETORAN</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">TANGGAL PENGAJUAN</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">STATUS</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no			= 0;
			$query 		= mysql_query("select * from km_mitra where km_status = 'Diajukan' order by km_id DESC");
			while($data	= mysql_fetch_array($query)) { 
				$cashier_id	= executeScalar("select cashier_id from ko where km_id = '".$data['km_id']."'");
				$cashier	= executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");
				$no++;
		?>
		<tr>
			<td style="vertical-align: middle; text-align:center;"><?php echo $no; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['km_id']; ?></td>
			<td style="vertical-align: middle; text-align:left;"><?php echo $cashier; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo format_rupiah($data['km_total']); ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo date("d M Y", strtotime($data['km_date'])); ?></td>
			<td style="vertical-align: middle; text-align:center;">
				<div style="vertical-align: middle; background-color: #EB8317; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 150px;">		
					<i class="fa fa-spinner fa-fw"></i>&nbsp;<?php echo $data['km_status'] == 'Diajukan' ? 'Menunggu Acc' : ''; ?>
				</div>
			</td>
			<td style="vertical-align: middle; text-align: center;"><a href="pay_action.php?task=acc_mitra&id=<?php echo $data['km_id']; ?>" style="text-decoration: none;">
					<button class="btn btn-outline btn-block btn-success btn-xs"><i class="fa fa-check"></i>&nbsp;Acc</button>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<!--</div>-->