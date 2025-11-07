<?php
	include "../connection.php";

	function timeAgo($time) {
		$timestamp = strtotime($time);
		$timeDiff = time() - $timestamp; 
	
		if ($timeDiff < 60) {
			return "$timeDiff detik yang lalu";
		} elseif ($timeDiff < 3600) {
			return floor($timeDiff / 60) . " menit yang lalu";
		} elseif ($timeDiff < 86400) {
			return floor($timeDiff / 3600) . " jam yang lalu";
		} else {
			return floor($timeDiff / 86400) . " hari yang lalu";
		}
	}
?>
<!-- jquery -->
<script src="../dataTables/jquery.dataTables.js"></script>
<script src="../dataTables/dataTables.tableTools.js"></script>
<script src="../dataTables/dataTables.bootstrap.js"></script>
<script>
	$(document).ready(function() {
	//data table
	$('#transactionView').dataTable({"aLengthMenu": [[10, 50, 100, -1], [10, 50, 100, "All"]]});
	});
</script>
<!--<div class="table-responsive">-->
<table id="transactionView" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th style="width:5%; text-align:center">NO</th>
			<th style="width:5%; text-align:center">NO NOTA</th>
			<th style="width:6%; text-align:center">NOPOL</th>
			<th style="width:16%; text-align:center">PEDAGANG</th>
			<th style="width:10%; text-align:center">BARANG</th>
			<th style="width:8%; text-align:center">MASUK</th>
			<th style="width:8%; text-align:center">STATUS</th>
			<th style="width:6%; text-align:center">JAM & TGL MASUK</th>
			<th style="width:6%; text-align:center">PRINT TIMBANG</th>
			<th style="width:5%; text-align:center">AKSI</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no		= 1;
			$date_plus 	= strtotime('-1 day', strtotime($date_now));
			$yesterday	= date('Y-m-j' , $date_plus);
			$query 	= mysql_query("select * from weighing where (date_in  between '2016-03-15' and '$date_now') and weighing_sts = 'Masuk' order by weighing_id desc");
			while($data=mysql_fetch_array($query)){
			$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
			$goods 	= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");
			$sama 	= executeScalar("select count(transportation_id) from transportation where transportation_nopol = '".$data['nopol']."'");
		?>
		<tr>
			<td style="text-align:center; vertical-align: middle;"><?php echo $no; ?></td>
			<td style="text-align:center; vertical-align: middle;">
				<div style="vertical-align: middle; background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">		
					<?php echo 'TB'.$data['weighing_id']; ?>
				</div>
			</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['nopol']; ?></td>
			<td style=" vertical-align: middle;"><?php echo $merchant; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $goods; ?></td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['weight_in']; ?> Kg</td>
			<td style="text-align:center; vertical-align: middle;"><?php echo $data['weighing_sts']; ?></td>
			<td style="text-align:center; vertical-align: middle;">
				<?php echo date("H:i", strtotime($data['time_in'])).' <br> '.date("d M Y", strtotime($data['date_in'])); ?> 
				<br> 
				<div style="background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 0 auto; width: 125px; font-size: 11px;">
					<?php echo timeAgo($data['date_in'] . ' ' . $data['time_in']); ?>
				</div>
			</td>
			<td style="text-align:center; vertical-align: middle;">
				<?php if ($data['p1'] == NULL) { ?>
					<a style="text-decoration: none" href="../htmltopdf/nota_timbangan.php?id=<?php echo $data['weighing_id'];?>" target="_blank">
						<button type="button" class="btn btn-block btn-outline btn-danger" onclick="postAndReload()"> 
							<i class="fa fa-close"></i>&nbsp;Belum
						</button>
					</a>
				<?php } elseif ($data['p1'] != NULL) { ?>
					<a style="text-decoration: none" href="../htmltopdf/nota_timbangan.php?id=<?php echo $data['weighing_id']?>" target="_blank">
						<button type="button" class="btn btn-block btn-outline btn-success" onclick="postAndReload()"> 
							<i class="fa fa-check"></i>&nbsp;Selesai
						</button>
					</a>
				<?php } ?>
			</td>
			<td style="text-align:center; vertical-align: middle;">
				<a style="text-decoration: none" class="modalUbah" data-toggle="modal" data-target="#modalUbah" data-id="<?php echo $data['weighing_id']; ?>">
					<button class="btn btn-outline btn-block btn-warning btn-xs"><i class="fa fa-edit"></i>&nbsp;Ubah</button>
				</a>
				<?php 
					if($data['weighing_sts']=='Masuk'){
						echo '<a href="index2.php?id='.$data['weighing_id'].'" style="text-decoration: none">
							<button style="margin-top: 10px;" class="btn btn-outline btn-block btn-primary btn-xs"><i class="fa fa-arrow-circle-up"></i>&nbsp;Keluar</button>
						</a>';
					}
					if($sama==1&&$data['weighing_sts']=='Masuk'){
						echo '
						<a href="index3.php?id='.$data['weighing_id'].'" style="text-decoration: none">
							<button style="margin-top: 10px;" class="btn btn-outline btn-block btn-primary btn-xs"><i class="fa fa-minus-circle"></i>&nbsp;Kurangi Kir</button>
						</a>';
					}
				?>
			</td>
		</tr>
		<?php 
			$no++;
			} ?>
	</tbody>
</table>

<script>
	$(document).ready(function() {
		$('.modalUbah').on('click', function() {
			var weighingId = $(this).data('id'); 

			$.ajax({
				url: 'components/modal_edit_weightin.php',
				type: 'GET',
				data: { id: weighingId }, 
				success: function(response) {
					$('#modalUbah .modal-content').html(response);
					$('#modalUbah').modal('show');
				}
			});
		});
	});
</script>

<script>
	function postAndReload() {
		setTimeout(function() {
			location.reload();
		}, 100);
	}
</script>