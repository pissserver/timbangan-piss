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
	$('#payViewMitra').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="payViewMitra" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 12px;">
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
			//$onemonth	= date('Y-m-j' , $date_plus); date_check != null
			$query2 		= mysql_query("	select 	w.weighing_id, 
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
										from 	weighing_mitra w, merchant m	
										where 	w.merchant_id = m.merchant_id and 
												m.merchant_status = 'Aktif' and
												w.weighing_sts = 'Keluar' and 
												w.weighing_sts3 is null and
												(w.date_in  between '2016-09-01' and '".$date_now."')  and 
												w.date_check is not null
										order by weighing_id desc");
			while($data2 = mysql_fetch_array($query2)) { 
				$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$data2['merchant_id']."'");
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data2['weighing_id']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo date("d M Y", strtotime($data2['date_check'])); ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data2['nopol']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data2['merchant_id']; ?></td>
			<td style=" vertical-align: middle;"><?php echo $merchant; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data2['merchant_type']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data2['netto']; ?> Kg</td>
			<!-- <td style="text-align:right;"><?php echo format_rupiah($data2['pendapatan_mitra']); ?></td> -->
			<!-- <td style="text-align:right;"><?php echo format_rupiah($data2['cost_simpan_mitra']); ?></td>
			<td style="text-align:right;"><?php echo format_rupiah($data2['cost_bagi_mandor']); ?></td>
			<td style="text-align:right;"><?php echo format_rupiah($data2['cost_simpan_mandor']); ?></td> -->
			<td style="text-align:right; vertical-align: middle;"><?php echo format_rupiah($data2['cost_bayar']); ?></td>
			<!-- <td style="text-align:right;"><?php echo format_rupiah($data2['cost']); ?></td> -->
			<!-- <td style="text-align:center;"><?php echo $data2['weighing_sts']; ?></td> -->
			<td style="text-align:center; vertical-align: middle;">
				<?php 
					if($data2['weighing_sts']!='Lunas'||$data2['weighing_sts3']!='Pending'){
						echo '<a href="pay_action.php?task=pay_mitra&type=ko&id='.$data2['weighing_id'].'" style="text-decoration:none;" >
							<button class="btn btn-outline btn-block btn-success btn-xs"><i class="fa fa-money"></i>&nbsp;Bayar</button>
						</a>';
					}
				?>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<!--</div>-->