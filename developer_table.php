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
			<th style="vertical-align: middle; text-align: center; width:10%;">AKSI</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">CONTACT</th>
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
						<div style="vertical-align: middle; background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 150px;">		
							<?php echo $data['type'] . $data['nota']; ?>
						</div>
				<?php }
					elseif($data['type']=='TG'){ ?>
						<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 150px;">		
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
			<td style="vertical-align: middle; text-align:center;">
				<a class="btn btn-warning btn-sm btn-outline modalEditRevision" style="width:80px;" data-toggle="modal" data-target="#modalEditRevision" data-id="<?php echo $data['revision_id']; ?>">
					<i class="fa fa-edit"></i>&nbsp;Edit
				</a>
				<a href="developer_action.php?task=delete&id=<?php echo $data['revision_id']; ?>" class="btn btn-danger btn-sm btn-outline" style="width:80px; margin-top:5px;">
					<i class="fa fa-times"></i>&nbsp;Batal
				</a>
			</td>
			<td style="vertical-align: middle; text-align:center;">
				<?php
				// Data dasar pesan
				$url = "https://89269b8d8d8a.ngrok-free.app/timbangan-piss/login.html?redirect=pp/developer.php";
				$message = "*REVISI TIMBANGAN*\n\n" .
					"Nama Pemohon : $checkerName\n" .
					"No Nota : {$data['type']}{$data['nota']}\n" .
					"Uraian Revisi : {$data['description']}\n" .
					"Alasan Revisi : {$data['reason']}\n\n" .
					"Mohon tindak lanjutnya. Terima kasih.\n\n" .
					"$url\n\n" .
					"~ Ini adalah pesan otomatis dari sistem Timbangan PISS ~";

				$encodedMessage = urlencode($message);


				// Daftar kontak WhatsApp
				$contacts = [
					['number' => '6282335326373', 'name' => 'PP - Aam'],
				];

				// Loop buat tombol
				foreach ($contacts as $c) {
					$waLink = "https://api.whatsapp.com/send?phone={$c['number']}&text={$encodedMessage}";
					echo '
					<a href="' . $waLink . '" target="_blank" 
					class="btn btn-success btn-sm" 
					style="display:block; width:90px; margin:4px auto; padding:5px 8px; text-align:center;">
						<i class="fa fa-whatsapp"></i> ' . $c['name'] . '
					</a>';
				}

				?>

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
		$('body').on('click', '.modalEditRevision', function() {
			var goodsID = $(this).data('id'); 

			$.ajax({
				url: 'developer_modal.php',
				type: 'GET',
				data: { id: goodsID }, 
				success: function(response) {
					$('#modalEditRevision .modal-content').html(response);
					$('#modalEditRevision').modal('show');
				}
			});
		});
	});
</script>
<!--</div>-->