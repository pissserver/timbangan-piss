<?php
include "../connection.php";
?>

<script src="../dataTables/jquery.dataTables.js"></script>
<script src="../dataTables/dataTables.bootstrap.js"></script>

<table id="transactionView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size:13px">
	<thead>
		<tr>
			<th style="text-align:center;width:3%">NO</th>
			<th style="text-align:center;width:5%">NO NOTA</th>
			<th style="text-align:center;width:5%">NOPOL</th>
			<th style="text-align:center;width:15%">PEDAGANG</th>
			<th style="text-align:center;width:7%">BARANG</th>
			<th style="text-align:center;width:5%">MASUK</th>
			<th style="text-align:center;width:5%">KELUAR</th>
			<th style="text-align:center;width:5%">NETTO</th>
			<th style="text-align:center;width:5%">STATUS</th>
			<th style="text-align:center;width:8%">JAM<br>MASUK & KELUAR</th>
			<th style="text-align:center;width:8%">TANGGAL<br>MASUK & KELUAR</th>
			<th style="text-align:center;width:5%">AKSI</th>
		</tr>
	</thead>

	<tbody></tbody>
</table>

<script>
	$(function(){
		$('#transactionView').DataTable({
			processing:true,
			serverSide:true,
			pageLength:10,
			lengthMenu:[[10,50,100],[10,50,100]],
			ajax:{
				url:'edit_server.php',
				type:'POST'
			},
			order:[[0,'desc']],
			columnDefs:[
				{orderable:false, targets:[11]}
			]
		});

		$('body').on('click','.modalWeightIn',function(){
			var id=$(this).data('id');
			$.get('edit_modal1.php',{id:id},function(res){
				$('#modalWeightIn .modal-content').html(res);
				$('#modalWeightIn').modal('show');
			});
		});

		$('body').on('click','.modalWeightOut',function(){
			var id=$(this).data('id');
			$.get('edit_modal2.php',{id:id},function(res){
				$('#modalWeightOut .modal-content').html(res);
				$('#modalWeightOut').modal('show');
			});
		});

		$('body').on('click','.modalNetto',function(){
			var id=$(this).data('id');
			$.get('edit_modal3.php',{id:id},function(res){
				$('#modalNetto .modal-content').html(res);
				$('#modalNetto').modal('show');
			});
		});
	});
</script>
