<?php include "../connection.php"; ?>

<script src="../dataTables/jquery.dataTables.js"></script>
<script src="../dataTables/dataTables.bootstrap.js"></script>

<table id="transactionView" class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th style="vertical-align: middle; text-align: center;">NO</th>
            <th style="vertical-align: middle; text-align: center;">NAMA KASIR</th>
            <th style="vertical-align: middle; text-align: center;">NO NOTA</th>
            <th style="vertical-align: middle; text-align: center;">NOPOL</th>
            <th style="vertical-align: middle; text-align: center;">PEDAGANG</th>
            <th style="vertical-align: middle; text-align: center;">AKSI</th>
            <th style="vertical-align: middle; text-align: center;">JAM</th>
            <th style="vertical-align: middle; text-align: center;">TANGGAL</th>
        </tr>
    </thead>
</table>

<script>
$(document).ready(function() {
    $('#transactionView').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "print_log_server.php",
            type: "POST"
        },
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[7, "desc"]],
        columnDefs: [
            { orderable: false, targets: [0,2] }
        ]
    });
});
</script>
