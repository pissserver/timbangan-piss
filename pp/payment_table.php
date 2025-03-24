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
	$('#commodityView').dataTable({"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="commodityView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="vertical-align: middle; text-align: center; width:10%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:50%;">JENIS TARIF</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">NOMINAL TARIF</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql 	= "select * from payment";
		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 
		?>
		<tr>
			<td style="vertical-align: middle; text-align:center;"><?php echo $no; ?></td>
			<td style="vertical-align: middle; text-align: center;"><?php echo $data['pay_name']; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo format_rupiah($data['pay_total']); ?></td>
			<td style="vertical-align: middle; text-align: center;">
				<a class="btn btn-xs btn-outline btn-primary btn-block modalEditPayment" data-toggle="modal" data-target="#modalEditPayment" data-id="<?php echo $data['pay_id']; ?>">
					<i class="fa fa-edit"></i>&nbsp;Edit
				</a>
				<a class="btn btn-xs btn-outline btn-danger btn-block modalDeletePayment" data-toggle="modal" data-target="#modalDeletePayment" data-id="<?php echo $data['pay_id']; ?>">
					<i class="fa fa-trash"></i>&nbsp;Hapus
				</a>
			</td>
		</tr>
		<?php $no++; } ?>
	</tbody>
</table>

<script>
	$(document).ready(function() {
		$('body').on('click', '.modalEditPayment', function() {
			var paymentID = $(this).data('id'); 

			$.ajax({
				url: 'payment_modal.php',
				type: 'GET',
				data: { id: paymentID }, 
				success: function(response) {
					$('#modalEditPayment .modal-content').html(response);
					$('#modalEditPayment').modal('show');
				}
			});
		});
	});

	$(document).ready(function() {
		$('body').on('click', '.modalDeletePayment', function() {
			var paymentID = $(this).data('id'); 

			$.ajax({
				url: 'payment_delete_modal.php',
				type: 'GET',
				data: { id: paymentID }, 
				success: function(response) {
					$('#modalDeletePayment .modal-content').html(response);
					$('#modalDeletePayment').modal('show');
				}
			});
		});
	});
</script>