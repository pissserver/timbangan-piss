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
<table id="transactionView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 13px;">
	<thead>
		<tr>
			<th style="text-align: center; vertical-align: middle; width:3%;">NO</th>
			<th style="text-align: center; vertical-align: middle; width:5%;">NO NOTA</th>
			<th style="text-align: center; vertical-align: middle; width:5%;">NOPOL</th>
			<th style="text-align: center; vertical-align: middle; width:15%;">PEDAGANG</th>
			<th style="text-align: center; vertical-align: middle; width:7%;">BARANG</th>
			<th style="text-align: center; vertical-align: middle; width:5%;">MASUK</th>
			<th style="text-align: center; vertical-align: middle; width:5%;">KELUAR</th>
			<th style="text-align: center; vertical-align: middle; width:5%;">NETTO</th>
			<th style="text-align: center; vertical-align: middle; width:5%;">STATUS</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">JAM <br>MASUK & KELUAR</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">TANGGAL <br>MASUK & KELUAR</th>
			<th style="text-align: center; vertical-align: middle; width:5%;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no		= 1;
			$date_plus 	= strtotime('-30 day', strtotime($date_now));
			$yesterday	= date('Y-m-j' , $date_plus);
			$query 	= mysql_query("select * from weighing where (date_in  between '$yesterday' and '$date_now') and weighing_sts = 'Keluar' order by weighing_id desc");
			while($data=mysql_fetch_array($query)){
			$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
			$goods 	= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");
			$sama 	= executeScalar("select count(transportation_id) from transportation where transportation_nopol = '".$data['nopol']."'");
		?>
		<tr>
			<td style="vertical-align: middle; text-align:center;"><?php echo $no; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['weighing_id']; ?></td>
			<td style="vertical-align: middle; text-align: center;"><?php echo $data['nopol']; ?></td>
			<td style="vertical-align: middle; text-align: left"><?php echo $merchant; ?></td>
			<td style="vertical-align: middle; text-align: center;"><?php echo $goods; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['weight_in'] != NULL ? $data['weight_in'].' Kg' : '0 Kg'; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['weight_out'] != NULL ? $data['weight_out'].' Kg' : '0 Kg'; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['netto'] != NULL ? $data['netto'].' Kg' : '0 Kg'; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['weighing_sts']; ?></td>
			<td style="vertical-align: middle; text-align:center;">
				<div style="background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 0 auto; width: 100px;">		
					<i class="fa fa-arrow-circle-down fa-fw"></i>&nbsp;<?php echo date("H:i", strtotime($data['time_in'])); ?>
				</div>
				<div style="background-color:rgb(202, 66, 66); color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 0 auto; width: 100px;">		
					<i class="fa fa-arrow-circle-up fa-fw"></i>&nbsp;<?php echo $data['time_out'] != NULL ? date("H:i", strtotime($data['time_out'])) : 'Belum'; ?>
				</div>
			</td>
			<td style="vertical-align: middle; text-align:center;">
				<div style="background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 0 auto; width: 130px;">		
					<i class="fa fa-arrow-circle-down fa-fw"></i>&nbsp;<?php echo date("d M Y", strtotime($data['date_in'])); ?>
				</div>
				<div style="background-color:rgb(202, 66, 66); color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 0 auto; width: 130px;">		
					<i class="fa fa-arrow-circle-up fa-fw"></i>&nbsp;<?php echo $data['date_out'] != NULL ? date("d M Y", strtotime($data['date_out'])) : 'Belum'; ?>
				</div>
			</td>
			<td style="text-align: center; vertical-align: middle;">
				<!-- Button HTML (to Trigger Modal) -->
				<a class="btn btn-xs btn-outline btn-primary btn-block modalIn" data-toggle="modal" data-target="#modalIn" data-id="<?php echo $data['weighing_id']; ?>"><i class="fa fa-arrow-circle-down fa-fw"></i>&nbsp;Masuk</a>
				<a class="btn btn-xs btn-outline btn-primary btn-block modalCancelOut" data-toggle="modal" data-target="#modalCancelOut" data-id="<?php echo $data['weighing_id']; ?>"><i class="fa fa-close fa-fw"></i>&nbsp;Batal</a>
			</td>
		</tr>
		<?php 
			$no++;
			} ?>
	</tbody>
</table>

<script>
	$(document).ready(function() {
		$('body').on('click', '.modalIn', function() {
			var weighingId = $(this).data('id'); 

			$.ajax({
				url: 'cancel_modal2_in.php',
				type: 'GET',
				data: { id: weighingId }, 
				success: function(response) {
					$('#modalIn .modal-content').html(response);
					$('#modalIn').modal('show');
				}
			});
		});
	});

	$(document).ready(function() {
		$('body').on('click', '.modalCancelOut', function() {
			var weighingId = $(this).data('id'); 

			$.ajax({
				url: 'cancel_modal2.php',
				type: 'GET',
				data: { id: weighingId }, 
				success: function(response) {
					$('#modalCancelOut .modal-content').html(response);
					$('#modalCancelOut').modal('show');
				}
			});
		});
	});
</script>