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
			<th style="vertical-align: middle; text-align: center; width:50%;">NAMA NONKOMODITI</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">BERAT (KG)</th>
			<th style="vertical-align: middle; text-align: center; width:20%; text-align:center;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql 	= "select * from noncommodity order by noncommodity_id";
		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $no; ?></td>
			<td style="text-align: center; vertical-align: middle;"><?php echo $data['noncommodity_name']; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['noncommodity_weight']; ?> Kg</td>
			<td style="text-align: center; vertical-align: middle;">
				<a class="btn btn-xs btn-outline btn-primary btn-block modalEditNonCommodity" data-toggle="modal" data-target="#modalEditNonCommodity" data-id="<?php echo $data['noncommodity_id']; ?>">
					<i class="fa fa-edit"></i>&nbsp;Edit
				</a>
				<a class="btn btn-xs btn-outline btn-danger btn-block modalDeleteNonCommodity" data-toggle="modal" data-target="#modalDeleteNonCommodity" data-id="<?php echo $data['noncommodity_id']; ?>">
					<i class="fa fa-trash"></i>&nbsp;Hapus
				</a>
			</td>
		</tr>
		<?php $no++; } ?>
	</tbody>
</table>

<script>
	$(document).ready(function() {
		$('body').on('click', '.modalEditNonCommodity', function() {
			var nonCommodityID = $(this).data('id'); 

			$.ajax({
				url: 'noncommodity_modal.php',
				type: 'GET',
				data: { id: nonCommodityID }, 
				success: function(response) {
					$('#modalEditNonCommodity .modal-content').html(response);
					$('#modalEditNonCommodity').modal('show');
				}
			});
		});
	});

	$(document).ready(function() {
		$('body').on('click', '.modalDeleteNonCommodity', function() {
			var nonCommodityID = $(this).data('id'); 

			$.ajax({
				url: 'noncommodity_delete_modal.php',
				type: 'GET',
				data: { id: nonCommodityID }, 
				success: function(response) {
					$('#modalDeleteNonCommodity .modal-content').html(response);
					$('#modalDeleteNonCommodity').modal('show');
				}
			});
		});
	});
</script>