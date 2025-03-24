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
			<th style="text-align: center; vertical-align: middle; width:10%;">NO</th>
			<th style="text-align: center; vertical-align: middle; width:10%;">KODE</th>
			<th style="text-align: center; vertical-align: middle; width:20%;">NAMA</th>
			<th style="text-align: center; vertical-align: middle; width:20%;">PASSWORD</th>
			<th style="text-align: center; vertical-align: middle; width:20%;">POSISI</th>
			<th style="text-align: center; vertical-align: middle; width:20%;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql 	= "select * from checker where checker_role = 'checker' and status_aktif = 1 order by checker_name asc";
		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $no; ?></td>
			<td style="text-align: center; vertical-align: middle; text-transform: lowercase;"><?php echo $data['checker_code']; ?></td>
			<td style="text-align: center; vertical-align: middle; text-transform: capitalize;"><?php echo $data['checker_name']; ?></td>
			<td style="text-align: center; vertical-align: middle; text-transform: lowercase;"><?php echo $data['checker_pass']; ?></td>
			<td style="text-align: center; vertical-align: middle; text-transform: capitalize;">
				<div style="vertical-align: middle; background-color: <?php echo $data['checker_role'] == 'Cashier' ? '#428bca' : 'green'; ?>; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 100px;">		
					<i class="fa fa-user fa-fw"></i>&nbsp;<?php echo $data['checker_role']; ?>
				</div>
			</td>
			<td style="text-align: center; vertical-align: middle;">
				<a class="btn btn-xs btn-outline btn-primary btn-block modalEditCashier" data-toggle="modal" data-target="#modalEditCashier" data-id="<?php echo $data['checker_id']; ?>">
					<i class="fa fa-edit"></i>&nbsp;Edit
				</a>
				<a class="btn btn-xs btn-outline btn-danger btn-block modalDeleteCashier" data-toggle="modal" data-target="#modalDeleteCashier" data-id="<?php echo $data['checker_id']; ?>">
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
		$('body').on('click', '.modalEditCashier', function() {
			var checkerID = $(this).data('id'); 

			$.ajax({
				url: 'checker_modal.php',
				type: 'GET',
				data: { id: checkerID }, 
				success: function(response) {
					$('#modalEditCashier .modal-content').html(response);
					$('#modalEditCashier').modal('show');
				}
			});
		});
	});

	$(document).ready(function() {
		$('body').on('click', '.modalDeleteCashier', function() {
			var checkerID = $(this).data('id'); 

			$.ajax({
				url: 'checker_delete_modal.php',
				type: 'GET',
				data: { id: checkerID }, 
				success: function(response) {
					$('#modalDeleteCashier .modal-content').html(response);
					$('#modalDeleteCashier').modal('show');
				}
			});
		});
	});
</script>