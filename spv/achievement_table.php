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
			<th style="vertical-align: middle; text-align: center; width:50%;">NAMA KASIR</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">BADGE</th>
			<th style="vertical-align: middle; text-align: center; width:20%; text-align:center;">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql = "
				SELECT 
					c.checker_id,
					c.checker_name, 
					COUNT(w.weighing_id) AS total_tonase
				FROM checker c
				JOIN weighing w ON w.checker_id = c.checker_id
				WHERE status_aktif = '1' AND checker_role = 'Cashier' AND weighing_sts = 'Lunas'
				GROUP BY c.checker_id, c.checker_name
				ORDER BY total_tonase DESC
			";

		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 

			// LEVEL CHECK
			if ($data['total_tonase'] > 25000) {
				$level = "Platinum";
				$color = "linear-gradient(45deg, #3d3d3d, #666666)";
			} elseif ($data['total_tonase'] > 15000) {
				$level = "Gold";
				$color = "linear-gradient(45deg, #ffd700, #ffbf00)";
			} elseif ($data['total_tonase'] > 10000) {
				$level = "Silver";
				$color = "linear-gradient(45deg, #c0c0c0, #e0e0e0)";
			} else {
				$level = "Basic";
				$color = "linear-gradient(45deg, #b87333, #cd7f32)"; // bronze
			}
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $no; ?></td>
			<td style="text-align: left; vertical-align: middle;"><?php echo $data['checker_name']; ?></td>
			<td style="text-align:center; vertical-align: middle;">
				<?php if ($level == "Basic") { ?>
					<div class="level-badge basic">
						<i class="fa fa-shield"></i>&nbsp;Basic
					</div>
				<?php } ?>

				<?php if ($level == "Silver") { ?>
					<div class="level-badge silver">
						<i class="fa fa-shield"></i>&nbsp;Silver
					</div>
				<?php } ?>

				<?php if ($level == "Gold") { ?>
					<div class="level-badge gold">
						<i class="fa fa-shield"></i>&nbsp;Gold
					</div>
				<?php } ?>

				<?php if ($level == "Platinum") { ?>
					<div class="level-badge platinum">
						<i class="fa fa-diamond"></i>&nbsp;Platinum
					</div>
				<?php } ?>
			</td>
			<td style="text-align: center; vertical-align: middle;">
				<a href="achievement_detail.php?id=<?php echo $data['checker_id']; ?>" class="btn btn-xs btn-outline btn-primary btn-block">
					<i class="fa fa-eye"></i>&nbsp;&nbsp;Lihat Detail
				</a>
			</td>
		</tr>
		<?php $no++; } ?>
	</tbody>
</table>
