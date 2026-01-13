<?php include "../connection.php"; ?>

<script src="../dataTables/jquery.dataTables.js"></script>
<script src="../dataTables/dataTables.bootstrap.js"></script>

<table id="cashier_log" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 12px;">
    <thead>
        <tr>
			<th style="vertical-align: middle; text-align: center; width:5%;">NO</th>
			<th style="vertical-align: middle; text-align: center; width:11%;">NAMA KASIR</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">NO NOTA</th>
			<th style="vertical-align: middle; text-align: center; width:8%;">NOPOL</th>
			<th style="vertical-align: middle; text-align: center; width:13%;">PEDAGANG</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">AKSI</th>
			<th style="vertical-align: middle; text-align: center; width:20%;">KETERANGAN</th>
			<th style="vertical-align: middle; text-align: center; width:5%;">JAM</th>
			<th style="vertical-align: middle; text-align: center; width:10%;">TANGGAL</th>
		</tr>
    </thead>
</table>

<script>
	$(document).ready(function() {
		$('#cashier_log').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				url: "cashier_log_server.php",
				type: "POST"
			},
			pageLength: 10,          // ← INI: default tampil 10 baris
			lengthMenu: [10, 25, 50, 100], // ← opsi dropdown
			order: [[8, "desc"]],
			columnDefs: [
				{ orderable: false, targets: [0,5,6] }
			]
		});
	});
</script>
