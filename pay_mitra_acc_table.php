<?php
	include "connection.php";
?>
<!-- jquery -->
<script src="dataTables/jquery.dataTables.js"></script>
<script src="dataTables/dataTables.tableTools.js"></script>
<script src="dataTables/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
	//data table
	$('#pay_accViewMitra').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="pay_accViewMitra" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 12px;">
	<thead>
		<tr>
			<th style="width:5%; text-align:center;">ID NOTA</th>
			<th style="width:10%; text-align:center;">TGL TAGIHAN</th>
			<th style="width:8%; text-align:center;">NOPOL</th>
			<th style="width:8%; text-align:center;">KODE</th>
			<th style="width:20%; text-align:center;">PEDAGANG</th>
			<th style="width:8%; text-align:center;">JENIS</th>
			<th style="width:10%; text-align:center;">NETTO</th>
			<!-- <th style="width:10%;">BAGI MITRA</th> -->
			<!-- <th style="width:10%;">SIMPAN MITRA</th>
			<th style="width:10%;">BAGI MANDOR</th>
			<th style="width:10%;">SIMPAN MANDOR</th> -->
			<th style="width:10%; text-align:center;">SETORAN</th>
			<!-- <th style="width:10%;">TOTAL</th> -->
			<!-- <th style="width:7%;">STATUS</th> -->
			<th style="width:7%; text-align:center;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
			//$date_plus 	= strtotime('-8 day', strtotime($date_now));
			//$onemonth	= date('Y-m-j' , $date_plus);
			$query = mysql_query("
									SELECT 
										w.weighing_id, 
										w.merchant_id, 
										w.netto,
										w.nopol, 
										w.cost_bagi_mitra,
										w.cost_simpan_mitra,
										w.cost_bagi_mandor,
										w.cost_simpan_mandor,
										w.cost_bayar,
										w.cost, 
										w.weighing_sts, 
										w.weighing_sts3, 
										w.date_out,
										(w.cost_bagi_mitra + w.cost_simpan_mitra) AS pendapatan_mitra,
										w.date_check,
										m.merchant_type 
									FROM 
										weighing_mitra w
									INNER JOIN 
										merchant m 
									ON 
										w.merchant_id = m.merchant_id
									WHERE 
										m.merchant_status = 'Aktif' AND
										w.weighing_sts = 'Keluar' AND 
										w.weighing_sts3 = 'Pending' AND
										w.date_in BETWEEN '2016-09-01' AND '".$date_now."'
									ORDER BY 
										w.weighing_id DESC
								");

			while($data	= mysql_fetch_array($query)) { 
				$merchant 	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['weighing_id']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo date("d M Y", strtotime($data['date_check'])); ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['nopol']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['merchant_id']; ?></td>
			<td style="vertical-align: middle;"><?php echo $merchant; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['merchant_type']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['netto']; ?> Kg</td>
			<!-- <td style="text-align:right;"><?php echo format_rupiah($data['pendapatan_mitra']); ?></td> -->
			<!-- <td style="text-align:right;"><?php echo format_rupiah($data['cost_simpan_mitra']); ?></td>
			<td style="text-align:right;"><?php echo format_rupiah($data['cost_bagi_mandor']); ?></td>
			<td style="text-align:right;"><?php echo format_rupiah($data['cost_simpan_mandor']); ?></td> -->
			<td style="text-align:right; vertical-align: middle;"><?php echo format_rupiah($data['cost_bayar']); ?></td>
			<!-- <td style="text-align:right;"><?php echo format_rupiah($data['cost']); ?></td> -->
			<!-- <td style="text-align:center;"><?php echo $data['weighing_sts']; ?></td> -->
			<td style="text-align:center; vertical-align: middle;">
				<a href="pay_action.php?task=cancel_mitra&type=ko&id=<?php echo $data['weighing_id']; ?>" style="text-decoration:none;" >
					<button class="btn btn-outline btn-block btn-success btn-xs"><i class="fa fa-times"></i>&nbsp;Batal</button>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<!--</div>-->