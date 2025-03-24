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
			<th style="text-align: center; vertical-align: middle; width:20%;">STATUS</th>
			<th style="text-align: center; vertical-align: middle; width:20%;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql 	= "	select sc.id_sc, g.goods_name, g.goods_type, sc.status_aktif  
					from seasonalcommodity sc
					join goods g on g.goods_id = sc.goods_id 
					order by g.goods_name";
		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle"><?php echo $no; ?></td>
			<td style="text-align: center; vertical-align: middle;"><?php echo $data['goods_name']; ?></td>
			<td style="text-align: center; vertical-align: middle;">
				<div style="vertical-align: middle; background-color: <?php echo $data['goods_type'] == 'Buah' ? '#428bca' : ($data['goods_type'] == 'Sayur' ? 'green' : '#EB8317'); ?>; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
					<i class="fa fa-cube fa-fw"></i>&nbsp;<?php echo $data['goods_type']; ?>
				</div>
			</td>
			<td style="text-align: center; vertical-align: middle;">
				<?php if ($data['status_aktif'] == 1) { ?>
					<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
						<i class="fa fa-check fa-fw"></i>Aktif
					</div>
				<?php } else { ?> 
					<div style="vertical-align: middle; background-color: #D43F3A; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
						<i class="fa fa-close fa-fw"></i>Tidak Aktif
					</div>
				<?php }  ?>
			</td>
			<td style="text-align: center; vertical-align: middle;">
				<center>
					<?php 
						if ($data['status_aktif'] == 1) { ?>
							<a class="btn btn-outline btn-block btn-warning btn-xs" href="seasonalcommodity_action.php?task=nonaktifkan&id=<?php echo $data['id_sc']; ?>">
								<i class="fa fa-minus-square fa-fw"></i>&nbsp;Nonaktifkan
							</a>
						<?php } else{?>
							<a class="btn btn-outline btn-block btn-success btn-xs" href="seasonalcommodity_action.php?task=aktifkan&id=<?php echo $data['id_sc']; ?>">
								<i class="fa fa-check-square fa-fw"></i>&nbsp;Aktifkan
							</a>
						<?php } ?>
						<a style="margin-top: 5px;" class="btn btn-xs btn-outline btn-danger btn-block modalDeleteSeasonalCommodity" data-toggle="modal" data-target="#modalDeleteSeasonalCommodity" data-id="<?php echo $data['id_sc']; ?>">
							<i class="fa fa-trash"></i>&nbsp;Hapus
						</a>
				</center>
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
		$('body').on('click', '.modalDeleteSeasonalCommodity', function() {
			var SCID = $(this).data('id'); 

			$.ajax({
				url: 'seasonalcommodity_delete_modal.php',
				type: 'GET',
				data: { id: SCID }, 
				success: function(response) {
					$('#modalDeleteSeasonalCommodity .modal-content').html(response);
					$('#modalDeleteSeasonalCommodity').modal('show');
				}
			});
		});
	});
</script>