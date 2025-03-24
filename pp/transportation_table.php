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
	$('#checkerView').dataTable({"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="checkerView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="vertical-align: middle; text-align: center; width:10%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:50%;">NOPOL KENDARAAN</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">BERAT (KG)</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql 	= "select * from transportation order by transportation_id desc";
		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 
		?>
		<tr>
			<td style="vertical-align: middle; text-align:center;"><?php echo $no; ?></td>
			<td style="vertical-align: middle; text-align: center;"><?php echo $data['transportation_nopol']; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $data['transportation_weight']; ?> Kg</td>
			<td style="vertical-align: middle; text-align: center;">
				<a class="btn btn-xs btn-outline btn-primary btn-block modalEditTransportation" data-toggle="modal" data-target="#modalEditTransportation" data-id="<?php echo $data['transportation_id']; ?>">
					<i class="fa fa-edit"></i>&nbsp;Edit
				</a>
				<a class="btn btn-xs btn-outline btn-danger btn-block modalDeleteTransportation" data-toggle="modal" data-target="#modalDeleteTransportation" data-id="<?php echo $data['transportation_id']; ?>">
					<i class="fa fa-trash"></i>&nbsp;Hapus
				</a>
			</td>
		</tr>
		<?php
			$no++;
			}
		?>
	</tbody>
</table>

<script>
	$(document).ready(function() {
		$('body').on('click', '.modalEditTransportation', function() {
			var transportationId = $(this).data('id'); 

			$.ajax({
				url: 'transportation_modal.php',
				type: 'GET',
				data: { id: transportationId }, 
				success: function(response) {
					$('#modalEditTransportation .modal-content').html(response);
					$('#modalEditTransportation').modal('show');
				}
			});
		});
	});

	$(document).ready(function() {
		$('body').on('click', '.modalDeleteTransportation', function() {
			var transportationId = $(this).data('id'); 

			$.ajax({
				url: 'transportation_delete_modal.php',
				type: 'GET',
				data: { id: transportationId }, 
				success: function(response) {
					$('#modalDeleteTransportation .modal-content').html(response);
					$('#modalDeleteTransportation').modal('show');
				}
			});
		});
	});
</script>