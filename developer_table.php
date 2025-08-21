<?php
	include "connection.php";
?>
<!-- jquery -->
<script src="dataTables/jquery.dataTables.js"></script>
<script src="dataTables/dataTables.tableTools.js"></script>
<script src="dataTables/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
	//data table
	$('#developerView').dataTable({"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="developerView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="vertical-align: middle; text-align: center; width:5%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:15%;">NAMA PEMOHON</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">NO NOTA</th>
			<th style="vertical-align: middle; text-align: center; width:25%;">URAIAN REVISI</th>
			<th style="vertical-align: middle; text-align: center; width:25%;">ALASAN REVISI</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">TGL & JAM PENGAJUAN</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">STATUS</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$sql 	= "SELECT * FROM revision WHERE status1 IS NULL ORDER BY revision_id DESC";
		$query 	= mysql_query($sql);
		$no		= 1;
			while($data=mysql_fetch_array($query)) { 
				$checkerName = executeScalar("SELECT checker_name FROM checker WHERE checker_id = '".$data['applicant']."'");	
		?>
		<tr>
			<td style="vertical-align: middle; text-align:center;"><?php echo $no; ?></td>
			<td style="vertical-align: middle; text-align:center;"><?php echo $checkerName; ?></td>
			<td style="vertical-align: middle; text-align:center;">
				<?php
					if($data['type']=='TB'){ ?>
						<div style="vertical-align: middle; background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
							<?php echo $data['type'] . $data['nota']; ?>
						</div>
				<?php }
					elseif($data['type']=='TG'){ ?>
						<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
							<?php echo $data['type'] . $data['nota']; ?>
						</div>
					<?php } 
				?>
			</td>
			<td style="vertical-align: middle; text-align:left;"><?php echo $data['description']; ?></td>
			<td style="vertical-align: middle; text-align:left;"><?php echo $data['reason']; ?></td>
			<td style="vertical-align: middle; text-align:center;">
				<?php echo date('d M Y', strtotime($data['date'])); ?><br>
				<?php echo date('H : i', strtotime($data['time'])); ?>
			</td>
			<td style="vertical-align: middle; text-align:center;">
				<div style="vertical-align: middle; background-color: #EB8317; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 150px;">		
					<i class="fa fa-spinner fa-fw"></i>&nbsp;<?php echo $data['status1'] == NULL ? 'Menunggu Acc' : ''; ?>
				</div>
			</td>
		</tr>
		<?php
			$no++;
			}
		?>
	</tbody>
</table>
<!--</div>-->