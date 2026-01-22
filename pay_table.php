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
	$('#payView').dataTable({"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]]});
	});
</script>

<div id="payTableWrapper" class="slide-normal">
	<table id="payView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" style="font-size: 12px;">
		<thead>
			<tr>
				<th style="width:5%; text-align:center;">ID NOTA</th>
				<th style="width:15%; text-align:center;">TGL TAGIHAN</th>
				<th style="width:10%; text-align:center;">NOPOL</th>
				<!-- <th style="width:8%; text-align:center;">KODE</th> -->
				<th style="width:25%; text-align:center;">PEDAGANG</th>
				<!-- <th style="width:8%; text-align:center;">JENIS</th> -->
				<th style="width:15%; text-align:center;">NETTO</th>
				<th style="width:20%; text-align:center;">SETORAN</th>
				<!-- <th style="width:7%;">STATUS</th> -->
				<th style="width:10%; text-align:center;">AKSI</th>
			</tr>
		</thead>
		<tbody>
			<?php
				//$date_plus 	= strtotime('-8 day', strtotime($date_now));
				//$onemonth	= date('Y-m-j' , $date_plus); date_check != null
				$query 		= mysql_query("	select 	w.weighing_id, 
													w.merchant_id, w.nopol, 
													w.netto, 
													w.cost,
													w.cost_tonase, 
													w.weighing_sts, 
													w.weighing_sts3, 
													w.date_out, 
													w.date_check,
													m.merchant_type 
											from 	weighing w, merchant m	
											where 	w.merchant_id = m.merchant_id and 
													m.merchant_status = 'Aktif' and
													w.weighing_sts = 'Keluar' and 
													w.weighing_sts3 is null and
													(w.date_in  between '2016-09-01' and '".$date_now."')  and 
													w.date_check is not null
											order by weighing_id desc");
				while($data	= mysql_fetch_array($query)) { 
					$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
			?>
			<tr>
				<td style="text-align:center; vertical-align: middle;"><?php echo $data['weighing_id']; ?></td>
				<td style="text-align:center; vertical-align: middle;"><?php echo date("d M Y", strtotime($data['date_check'])); ?></td>
				<td style="text-align:center; vertical-align: middle;"><?php echo $data['nopol']; ?></td>
				<!-- <td style="text-align:center; vertical-align: middle;"><?php echo $data['merchant_id']; ?></td> -->
				<td style="vertical-align: middle;">
					<?php echo $merchant; ?><br>
					<small style="color: #646464"><?php echo $data['merchant_type']; ?></small><br>
					<small style="color: #7f7d7d; font-weight: 600;"><?php echo $data['merchant_id']; ?></small>
				</td>
				<!-- <td style="text-align:center; vertical-align: middle;"><?php echo $data['merchant_type']; ?></td> -->
				<td style="text-align:center; vertical-align: middle;"><?php echo $data['netto']; ?> Kg</td>
				<td style="text-align:center; vertical-align: middle;"><?php echo format_rupiah($data['cost_tonase']); ?></td>
				<!-- <td style="text-align:center;"><?php echo $data['weighing_sts']; ?></td> -->
				<td style="vertical-align: middle;">
					<?php 
						if($data['weighing_sts']!='Lunas'||$data['weighing_sts3']!='Pending'){
							echo '<a href="pay_action.php?task=pay&type=ko&id='.$data['weighing_id'].'" style="text-decoration:none;" >
								<button class="btn btn-block btn-outline btn-primary btn-xs"><i class="fa fa-money"></i>&nbsp;Bayar</button>
							</a>';
						}
					?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

<script>
$(document).on('click', '.btn-bayar', function () {

    var id = $(this).data('id');
    var row = $('#row-' + id);

    /* 1. ROW TERBANG KE KANAN */
    row.addClass('fly-right');

    /* 2. EKSEKUSI BACKEND TANPA RELOAD */
    $.ajax({
        url: 'pay_action.php',
        type: 'GET',
        data: {
            task: 'pay',
            type: 'ko',
            id: id
        },
        success: function () {

            /* 3. TABEL GESER KELUAR */
            $('#payTableWrapper').addClass('slide-out-right');

            /* 4. RELOAD DATA PANEL */
            $('#Area1').load('pay_tagihan_tonase.php');
            $('#Area2').load('pay_penampungan_tonase.php');

            /* 5. MASUK KEMBALI DARI KIRI */
            setTimeout(function () {

                $('#payTableWrapper')
                    .removeClass('slide-out-right')
                    .addClass('slide-in-left');

                setTimeout(function () {

                    $('#payTableWrapper')
                        .removeClass('slide-in-left')
                        .addClass('slide-normal')
                        .load('pay_table.php');

                }, 150);

            }, 500);
        }
    });

});
</script>
