<?php
	session_start();
	include "../connection.php";

	// $checker 	= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
	$id		= $_GET['id'];
	$edit	= mysql_query("select * from weighing where weighing_id = '".$id."'");
	while ($r = mysql_fetch_array($edit)){
		$merchant = executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
		$nick	= executeScalar("select merchant_nick from merchant where merchant_id = '".$r['merchant_id']."'");
		$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
		where r.lapak_id = l.lapak_id and r.merchant_id = '".$r['merchant_id']."'
		and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
		$goods 	= executeScalar("select goods_name from goods where goods_id='".$r['goods_id']."'");
?>
	<!-- Modal Ubah -->
				<div class="modal-header" style="background-color: #428bca; color: #fff; border-radius: 5px 5px 0px 0px;">
					<h4 id="header_ubah">Data Kendaraan (<?php 
					$wid = sprintf("%07d", $id);
					$year = executeScalar("select date_format(date_in, '%y') from weighing where weighing_id = '".$r['weighing_id']."'");
					echo 'TB'.$wid; ?>)</h4>
					<button style="margin-top: -35px;" type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row" style=" margin-right:5px; margin-left:5px;">
						<form class="form-horizontal" id="formUbah" name="formUbah" method="POST" action="weighing_action.php?task=edit&id=<?php echo $id; ?>">
							<div class="col-md-6">
								<div class="form-group" style="padding:10px;">
									<label>Nopol</label>
									<input type="text" class="form-control" id="nopol" name="nopol" value="<?php echo $r['nopol']; ?>" autofocus="autofocus" required>
									<br /><label>Pedagang</label>
									<select class="form-control chzn-select" name="merchant" id="merchant">
										<option value="<?php echo $r['merchant_id']; ?>" required><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.$lpk; ?></option>
										<?php 
										$sql = mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
										while($d1 = mysql_fetch_array($sql)){
											$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
											where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."' limit 1");
											$nick	= $d1['merchant_nick'];
											if($nick!=''){
												$nick = $nick.' / ';
											}else{
												$nick = '';
											}
										?>
										<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
									<?php } ?>
									</select>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group" style="padding:10px;">
									<label>Barang</label>
									<select class="form-control chzn-select" name="goods" id="goods" required>
										<option value="<?php echo $r['goods_id']; ?>" required><?php echo $goods; ?></option>
										<?php
											$sql = mysql_query("select goods_id, goods_name from goods where goods_id != '1' 
											and goods_id != '7' and goods_id != '96' and goods_id != '94' and goods_id != '95' 
											order by goods_name");
											while($data = mysql_fetch_array($sql))
										{?>
											<option value="<?php echo $data['goods_id'];?>"><?php echo $data['goods_name'];?></option>
										<?php } ?>
									</select>
								</div>
								<div class="form-group" style="padding:10px; margin-top:-10px;">
									<label>Keterangan</label>
									<input id="reason" name="reason" placeholder="Keterangan kendaraan masuk" type="text" class="form-control" required>                  
								</div>
							</div>
							<div class="form-group" style="padding:10px; margin-right:0px; margin-top:10px; margin-left:0px;">
								<button type="submit" class="btn btn-block btn-primary">Simpan <i class="fa fa-angle-double-right fa-lg"></i></button>
							</div>
						</form>
					</div>

    <script src="../js/plugins/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
    	$(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
    </script>
<?php } ?>