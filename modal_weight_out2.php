<?php
    session_start();
	include "connection.php";
    //Validasi role
	if($_SESSION['checker']==''){
		header("Location:login.html");
	}
	function headScript(){
		$headScript=file_get_contents("headScript.html");
		return $headScript;
	}
	
	$checker 	= executeScalar("select checker_name from checker where checker_code = '".$_SESSION['checker']."'");
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

    <!-- Modal Weight Out2 -->
    <div class="modal-header" style="background-color: #428bca; color: #fff; border-radius: 5px 5px 0px 0px;">
        <i class="fa fa-arrow-circle-down fa-fw"></i> Kendaraan Keluar Manual
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <div class="row" style=" margin-right:5px; margin-left:5px;">
            <form class="form-horizontal" id="formWeighingIn" name="formWeighingIn" method="POST" action="weighing_action.php?task=out2&id=<?php echo $id ?>">
                    <fieldset>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Nopol</label>
                                    <input type="text" class="form-control" id="nopol" name="nopol" value="<?php echo $r['nopol']; ?>" required readonly>                   
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Berat Kendaraan Masuk</label>
                                    <input id="weight_in" name="weight_in" value="<?php echo $r['weight_in']; ?> Kg" type="text" style="text-align: left;" class="form-control"  disabled>                
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Pedagang</label>
                                    <select class="form-control" name="merchant" id="merchantName" readonly>
										<option value="<?php echo $r['merchant_id']; ?>" required><?php echo $merchant.' / '.$nick.' / '.$r['merchant_id'].' / '.$lpk; ?></option>
											<?php 
												$sql = mysql_query("select merchant_id, merchant_name, merchant_nick from merchant where merchant_status = 'Aktif' order by merchant_nick, merchant_name");
												while($d1 = mysql_fetch_array($sql)){
													$lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
																			where r.lapak_id = l.lapak_id and r.merchant_id = '".$d1['merchant_id']."'
																			and r.rent_sts != 'Batal' and r.rent_sts2 != 'Pindah' limit 1");
													$nick2	= executeScalar("select merchant_nick from merchant where merchant_id = '".$d1['merchant_id']."'");
													if($nick2!=''){
														$nick2 = $nick2.' /  ';
													}else{
														$nick2 = '';
													} ?>
												<option value="<?php echo $d1['merchant_id'];?>"><?php echo $nick.$d1['merchant_name'].' / '.$d1['merchant_id'].' / '.$lpk;?></option>
											<?php } ?>
									</select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Tanggal Masuk</label>
                                    <input id="date_in" name="date_in" type="date" value="<?php echo date('Y-m-d', strtotime($r['date_in'])) ?>" class="form-control" required disabled> 
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Barang</label>
                                    <select class="form-control" name="goods" id="goodsName" readonly required>
										<option value="<?php echo $r['goods_id']; ?>" required><?php echo $goods; ?></option>
											<?php
												$sql 		= mysql_query("select goods_id, goods_name from goods order by goods_name");
												while($data = mysql_fetch_array($sql))
											{?>
											    <option value="<?php echo $data['goods_id'];?>"><?php echo $data['goods_name'];?></option>
											<?php } ?>
									</select>              
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Jam Masuk</label>
                                    <input id="time_in" name="time_in" type="time" value="<?php echo date('H:i', strtotime($r['time_in'])) ?>" class="form-control" required disabled>                  
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Berat Kendaraan Keluar</label>
                                    <input id="weight_out" name="weight_out" placeholder="Berat Kendaraan Keluar (Kg)" type="text" class="form-control" required>                  
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group" style="padding:10px; margin-top:-10px;">
                                    <label>Keterangan</label>
                                    <input id="reason" name="reason" placeholder="Alasan input manual" type="text" class="form-control" required>                  
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group" style="padding:10px;">
                                    <center><button type="submit" class="btn btn-primary btn-block">Masuk <i class="fa fa-angle-double-right fa-lg"></i></button></center>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
        </div>
    </div>

    <script>
		const selectMerchantName = document.getElementById('merchantName');
		const selectGoodsName = document.getElementById('goodsName');

		selectMerchantName.addEventListener('mousedown', function(event){
			event.preventDefault();
		});

		selectGoodsName.addEventListener('mousedown', function(event){
			event.preventDefault();
		});
	</script>
    <script src="js/plugins/chosen/chosen.jquery.js"></script>
    <script type="text/javascript">
        $(".chzn-select").chosen({"search_contains": true,"width":"100%", "no_results_text":"Hasil tidak ditemukan !"});
    </script>

<?php } ?>