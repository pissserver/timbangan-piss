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
	$('#goodsView').dataTable({"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="goodsView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="text-align: center; vertical-align: middle; width:10%;">NO</th>
			<th style="text-align: center; vertical-align: middle; width:40%;">NAMA KOMODITI</th>
			<th style="text-align: center; vertical-align: middle; width:20%;">KATEGORI</th>
			<th style="text-align: center; vertical-align: middle; width:20%;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql 	= "select * from goods order by goods_name";
		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $no; ?></td>
			<td style="text-align: center; vertical-align: middle;"><?php echo $data['goods_name']; ?></td>
			<td style="text-align: center; vertical-align: middle;">
				<div style="vertical-align: middle; background-color: <?php echo $data['goods_type'] == 'Buah' ? '#428bca' : ($data['goods_type'] == 'Sayur' ? 'green' : '#EB8317'); ?>; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
					<i class="fa fa-cube fa-fw"></i>&nbsp;<?php echo $data['goods_type']; ?>
				</div>
			</td>
			<td style="text-align: center; vertical-align: middle;">
				<a class="btn btn-xs btn-outline btn-primary btn-block modalEditGoods" data-toggle="modal" data-target="#modalEditGoods" data-id="<?php echo $data['goods_id']; ?>">
					<i class="fa fa-edit"></i>&nbsp;Edit
				</a>
				<a class="btn btn-xs btn-outline btn-danger btn-block modalDeleteGoods" data-toggle="modal" data-target="#modalDeleteGoods" data-id="<?php echo $data['goods_id']; ?>">
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
		$('body').on('click', '.modalEditGoods', function() {
			var goodsID = $(this).data('id'); 

			$.ajax({
				url: 'goods_modal.php',
				type: 'GET',
				data: { id: goodsID }, 
				success: function(response) {
					$('#modalEditGoods .modal-content').html(response);
					$('#modalEditGoods').modal('show');
				}
			});
		});
	});

	$(document).ready(function() {
		$('body').on('click', '.modalDeleteGoods', function() {
			var goodsID = $(this).data('id'); 

			$.ajax({
				url: 'goods_delete_modal.php',
				type: 'GET',
				data: { id: goodsID }, 
				success: function(response) {
					$('#modalDeleteGoods .modal-content').html(response);
					$('#modalDeleteGoods').modal('show');
				}
			});
		});
	});
</script>