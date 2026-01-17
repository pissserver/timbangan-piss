<?php
	include "../connection.php";
?>
<!-- jquery -->
<script src="../dataTables/jquery.dataTables.js"></script>
<script src="../dataTables/dataTables.bootstrap.js"></script>

<table id="transactionView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="vertical-align: middle; text-align: center; width:3%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">NO NOTA</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">NOPOL</th>
			<th style="vertical-align: middle; text-align: center; width:15%;">PEDAGANG</th>
			<th style="vertical-align: middle; text-align: center; width:7%;">BARANG</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">MASUK</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">KELUAR</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">NETTO</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">STATUS</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">JAM <br>MASUK & KELUAR</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">TANGGAL <br>MASUK & KELUAR</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">AKSI</th>
		</tr>
	</thead>
	<tbody></tbody>
</table>

<script>
	$(function() {
		$('#transactionView').DataTable({
			processing: true,
			serverSide: true,
			pageLength: 10,
			lengthMenu: [[10, 50, 100], [10, 50, 100]],
			ajax: {
				url: 'edit_server2.php',
				type: 'POST'
			},
			order: [[0, 'desc']],
			columnDefs: [
				{ orderable: false, targets: [11] }
			]
		});


		$('body').on('click','.modalWeightOut',function(){
			var id=$(this).data('id');
			$.get('edit_modal2.php',{id:id},function(res){
				$('#modalWeightOut .modal-content').html(res);
				$('#modalWeightOut').modal('show');
			});
		});
	});
</script>