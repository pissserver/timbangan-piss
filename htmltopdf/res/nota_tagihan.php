<?php
    session_start();
    include "../connection.php";

    $cashier_name = '';
    $spv_name = '';
    
    if (!empty($_SESSION['checker'])) {
        $cashier_name = executeScalar("SELECT checker_name FROM checker WHERE checker_code = '".$_SESSION['checker']."'");
    } elseif (isset($_SESSION['role']) && (strtolower($_SESSION['role']) == 'spv')) {
        $spv_name = executeScalar("SELECT name FROM admin WHERE username = '".$_SESSION['username']."'");
    } else {
        header("Location: login.html");
        exit();
    }

	date_default_timezone_set('Asia/Jakarta');
	$dt_now	= date("Y-m-d H:i:s");
            
?>
<page backtop="0mm" backbottom="0mm" backleft="5mm" backright="5mm">
	<?php
        $date 			= date("d M Y");
        $jam 			= date("H:i");
        $dt_now			= date("Y-m-d H:i:s");
        $id				= $_GET['id'];
        $uid			= $_GET['u'];
        $idq 			= sprintf("%07d", $id);
        $tarif			= 0;
        $biayatonase 	= 0;
        $biayamitra 	= 0;
        $cost_tonase	= executeScalar("select cost_tonase from weighing where weighing_id = '".$id."'");
        $sql 			= mysql_query("select * from weighing where weighing_id = '".$id."'");

        while ($r= mysql_fetch_array($sql)) {
            $prnt 			= mysql_query("update weighing set p2 = '2' where weighing_id = '".$id."'");
            $jenis			= mb_substr($r['merchant_id'], 0, 1, 'utf-8');
            $tarif			= 0;
            $name			= executeScalar("select merchant_name from merchant where merchant_id = '".$r['merchant_id']."'");
            $nick			= executeScalar("select merchant_nick from merchant where merchant_id = '".$r['merchant_id']."'");
            $checker		= executeScalar("select checker_code from checker where checker_id = '".$r['checker_id']."'");
            $lapak 			= executeScalar("select l.lapak_name from rent r, lapak l where r.lapak_id = l.lapak_id and 
											r.merchant_id = '".$r['merchant_id']."' and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') limit 1");
            $goods			= executeScalar("select goods_name from goods where goods_id = '".$r['goods_id']."'");
            $nc				= executeScalar("select noncommodity_name from noncommodity where noncommodity_id = '".$r['noncommodity_id']."'");
            $time1			= executeScalar("select TIME_FORMAT(`time_in`, '%H:%i') from weighing where weighing_id = '".$r['weighing_id']."'");
            $time2			= executeScalar("select TIME_FORMAT(`time_out`, '%H:%i') from weighing where weighing_id = '".$r['weighing_id']."'");
            $date_in		= date("d M Y", strtotime($r['date_in']));
            $date_out		= date("d M Y", strtotime($r['date_out']));
            $date_cek		= date('d M Y', strtotime($r['date_check'])) . ' (' . date('H:i', strtotime($r['date_check'])) . ')';
            $nc_weight		= $r['noncommodity'];
            $kat_merchant 	= executeScalar("select kat_merchant from merchant where merchant_id = '".$r['merchant_id']."'");
            $tarif_min		= executeScalar("select pay_total from payment where pay_id = 1");
            $netto_mitra    = executeScalar("SELECT SUM(netto) FROM weighing WHERE (weighing_sts2 = 'Digabung' OR weighing_sts2 = 'Pecahan') AND laborer_sts = 'Ya' AND reffer = '".$id."'");
            $reffer_query   = mysql_query("select weighing_id, laborer_sts from weighing where reffer = '".$id."'");
            $reffer_array   = [];
            // Periksa apa kah termasuk komoditi khusus
            $cekseasonalcommodity 	= executeScalar("select count(id_sc) from seasonalcommodity where goods_id = '".$r['goods_id']."' AND status_aktif = '1'");

            // Check reffer query
            while ($row = mysql_fetch_array($reffer_query)) {
                if ($row['laborer_sts'] == "Ya") {
                    $reffer_array[] = 'TG' . $row['weighing_id'] . ' (Y)';
                } elseif ($row['laborer_sts'] == "Tidak") {
                    $reffer_array[] = 'TG' . $row['weighing_id'] . ' (T)';
                }
            }

            if ($nc_weight!=0||$nc_weight!='') {
                $nc_weight	= $nc_weight;
            } else {
                $nc_weight	= '-';
            }
            if ($r['reffer']!='') {
                $reffer = $r['reffer'];
            } else {
                $reffer = '-';
            }
            if ($nc!=''||$nc!=0) {
                $nc = $nc;
            } else {
                $nc = '-';
            }

            if ($jenis=='B') {
                if ($r['laborer_sts']=='Ya') {
                    $tarif1	 		= executeScalar("select pay_total from payment where pay_id = '9'");
                    $tarif2	 		= executeScalar("select pay_total from payment where pay_id = '8'");
                    $tarif			= intval($tarif1+$tarif2);
                    $biayatonase 	= $r['netto'] * $tarif2;
                    $biayamitra 	= $r['netto'] * $tarif1;
                    $tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
                // if ($biayatonase < $tarifminim) {
                    // 	$biayatonase = $tarifminim;
                    // }
                } else {
                    $tarif2	 		= executeScalar("select pay_total from payment where pay_id = '8'");
                    $tarif1 		= 0;
                    $biayatonase 	= $r['netto'] * $tarif2;
                    $biayamitra 	= $r['netto'] * $tarif1;
                    $tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
                    // if ($biayatonase < $tarifminim) {
                    // 	$biayatonase = $tarifminim;
                    // }
                }
                // if($r['merchant_id']=='B0072'&&$r['goods_id']==2){
                // 	$tarif 	= executeScalar("select pay_total from payment where pay_id = '2'");
                // }
            } elseif ($jenis=='S'&&$r['laborer_sts']=='Ya') {
                $tarif1	 		= executeScalar("select pay_total from payment where pay_id = '3'");
                $tarif2	 		= executeScalar("select pay_total from payment where pay_id = '2'");
                // Pengecekan apabila pedagang merupakan pedagang trial di lapak
                if ($kat_merchant == 'Trial Lapak') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }

                // Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
                if ($kat_merchant == 'Trial Lapak Jalan') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }
                $tarif			= intval($tarif1+$tarif2);
                $biayatonase 	= $r['netto'] * $tarif2;
                $biayamitra 	= $r['netto'] * $tarif1;
                $tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
                if ($biayatonase < $tarifminim) {
                    $biayatonase = $tarifminim;
                }
            } elseif ($jenis=='S'&&$r['laborer_sts']=='Tidak') {
                $tarif2 		= executeScalar("select pay_total from payment where pay_id = '2'");
                // Pengecekan apabila pedagang merupakan pedagang trial di lapak
                if ($kat_merchant == 'Trial Lapak') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }

                // Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
                if ($kat_merchant == 'Trial Lapak Jalan') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }
                $tarif1 		= 0;
                $biayatonase 	= $r['netto'] * $tarif2;
                $biayamitra 	= $r['netto'] * $tarif1;
                $tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
                if ($biayatonase < $tarifminim) {
                    $biayatonase = $tarifminim;
                }
            } elseif ($jenis=='L'&&$r['laborer_sts']=='Ya') {
                $tarif1	 	= executeScalar("select pay_total from payment where pay_id = '3'");
                $tarif2	 	= executeScalar("select pay_total from payment where pay_id = '2'");
                // Pengecekan apabila pedagang merupakan pedagang trial di lapak
                if ($kat_merchant == 'Trial Lapak') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }

                // Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
                if ($kat_merchant == 'Trial Lapak Jalan') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }
                $tarif		= intval($tarif1+$tarif2);
                $biayatonase 	= $r['netto'] * $tarif2;
                $biayamitra 	= $r['netto'] * $tarif1;
                $tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
                if ($biayatonase < $tarifminim) {
                    $biayatonase = $tarifminim;
                }
            } elseif ($jenis=='L'&&$r['laborer_sts']=='Tidak') {
                $tarif2	 		= executeScalar("select pay_total from payment where pay_id = '2'");
                $tarif1 		= 0;
                // Pengecekan apabila pedagang merupakan pedagang trial di lapak
                if ($kat_merchant == 'Trial Lapak') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '15'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }

                // Pengecekan apabila pedagang merupakan pedagang trial di lapak di jalan
                if ($kat_merchant == 'Trial Lapak Jalan') {
                    $tarif2 = executeScalar("select pay_total from payment where pay_id = '16'");
                    // if ($jml_kehadiran_hari_ini > 1) {
                    // 	$tarif2 = 0;
                    // }
                }
                $biayatonase 	= $r['netto'] * $tarif2;
                $biayamitra 	= $r['netto'] * $tarif1;
                $tarifminim	 	= executeScalar("select pay_total from payment where pay_id = '1'");
                if ($biayatonase < $tarifminim) {
                    $biayatonase = $tarifminim;
                }
            }

            if ($cekseasonalcommodity > 0) {
                $tarif2 = executeScalar("select pay_total from payment where pay_id = '14'");
            } ?>
	<page_header>

	</page_header>
	<table style="width:100%; border:0px; margin-top:0px; font-size:9pt;">
		<tr>
			<td style="text-align: left; width: 50%">No Nota: TG<?php echo $idq; ?></td>
			<td style="text-align: center; width: 5%"></td>
			<td style="text-align: right; width: 45%"><?php echo $date.' ('.$jam.') <b>('.$checker.')</b>'; ?></td>
		</tr>
	</table>
	<br />
	<div style="font-size: 12px; font-weight: bold; text-align: center; margin-top:-5px;">
		NOTA TAGIHAN TONASE
	</div>
	<br />
	<!-- <div style="font-size: 12px; font-weight: bold; margin-top: -5px;">Data timbangan akhir adalah sebagai berikut:</div> -->
	<table style="width: 100%; border:0px; margin-top: -5px; margin-bottom: 5px; font-size:11px;" id="tableCheckout;">
		<tr>
			<td>Nama / Alias / Kode</td>
            <td>:</td>
			<td style="width: 70%;">
				<?php echo $name. ' / ' .$nick. ' / ' . $r['merchant_id']; ?>
			</td>

            <td></td>
            <td></td>
		</tr>
		<tr>
			<td>Lapak </td>
            <td>:</td>
			<td>
                <?php 
                    if ($lapak == NULL) {
                        echo '-';
                    } else {
                        echo $lapak;
                    }
                ?>
            </td>
		</tr>
		<tr>
			<td>Nopol </td>
            <td>:</td>
			<td><?php echo $r['nopol']; ?>
			</td>
		</tr>
		<tr>
			<td>Tgl & Jam Masuk </td>
            <td>:</td>
			<td>
				<?php echo $date_in.' ('.$time1.')' ?>
			</td>
		</tr>
		<tr>
			<td>Tgl & Jam Keluar </td>
            <td>:</td>
			<td>
				<?php echo $date_out.' ('.$time2.')' ?>
			</td>
		</tr>
		<tr>
			<td>Tgl & Jam Check </td>
            <td>:</td>
			<td>
                <?php 
                    echo $date_cek; 
                ?>
            </td>
		</tr>
		<tr>
			<td>Barang </td>
            <td>:</td>
			<td><?php echo $goods; ?></td>
		</tr>
		<tr>
			<td>Status Mitra </td>
            <td>:</td>
			<td>
				<?php echo $r['laborer_sts']; ?>
			</td>
		</tr>
		<tr>
			<td>Status Nota </td>
            <td>:</td>
			<td><?php if ($r['weighing_sts2']=='Pecahan') {
                $nett = executeScalar("select netto from weighing where weighing_id = '".$r['reffer']."'");
                echo $r['weighing_sts2'].' (Netto Awal '.$nett.')';
            } else {
                echo $r['weighing_sts2'];
            } ?></td>
		</tr>
		<tr>
			<td>Berat Masuk </td>
            <td>:</td>
			<td>
				<b><?php echo format_no($r['weight_in']);?></b> Kg <span style='margin-left: 20px;'>Berat Keluar : <b><?php echo format_no($r['weight_out']);?></b> Kg</span>
			</td>
        </tr>
		<tr>
			<td>Berat NonKomoditi </td>
            <td>:</td>
			<td><b><?php echo $nc_weight; ?></b> Kg</td>
		</tr>
            <?php
                if ($r['rafaksi'] == NULL && $jenis == 'B') {
                    echo "<tr>";
                    echo "<td>Rafaksi (Potongan)</td>";
                    echo "<td>:</td>";
                    echo "<td><b style=' font-weight: bold;'>0</b> Kg</td>";
                    echo "</tr>";
                } elseif ($r['rafaksi'] != NULL && $jenis == 'B') {
                    echo "<tr>";
                    echo "<td>Rafaksi (Potongan)</td>";
                    echo "<td>:</td>";
                    echo "<td><b style=' font-weight: bold;'>" . $r['rafaksi'] . "</b> Kg</td>";
                    echo "</tr>";
                }
            ?>
		<tr>
			<td>
                <?php if ($r['weighing_sts2'] == "Gabungan" || $jenis == 'B') {
                    echo "Berat Bersih Tonase";
                } else {
                    echo "Berat Bersih";
                } ?>
            </td>
            <td>:</td>
			<td>
                <b><?php echo format_no($r['netto']); ?></b> Kg
            </td>
		</tr>
        <?php
            if ($r['weighing_sts2'] == "Gabungan" && $r['laborer_sts'] == "Ya") {
                echo "<tr>";
                echo "<td>Berat Bersih Bongkar</td>";
                echo "<td>:</td>";
                echo "<td><b style=' font-weight: bold;'>" . format_no($netto_mitra) . "</b> Kg</td>";
                echo "</tr>";
            } elseif ($jenis == 'B') {
                echo "<tr>";
                echo "<td>Berat Bersih Bongkar</td>";
                echo "<td>:</td>";
                echo "<td><b style='font-weight: bold;'>" . format_no($r['netto'] + $r['rafaksi']) . "</b> Kg</td>";
                echo "</tr>";
            }
        ?>
		<tr>
			<td>
				<?php if ($cost_tonase <= $tarif_min) {
                    echo "Tarif Tonase 0-210 kg";
                } else {
                    echo "Tarif Tonase";
                } ?>
			</td>
            <td>:</td>
			<td>
				<?php if ($biayatonase <= $tarifminim && $jenis != 'B') { ?>
                    <b style="font-size: 14px; font-weight: bold;"><?php echo format_rupiah($cost_tonase); ?></b>
                <?php } else {
                    echo "Rp ".intval($tarif2)." /kg = "; ?>
                    <b style="font-size: 14px; font-weight: bold;"><?php echo format_rupiah($cost_tonase); ?></b><?php
                } ?>
			</td>
		</tr>
            <?php
                if ($r['cost_bongkar'] > 0 && $r['laborer_sts'] == 'Ya' && $jenis == 'B') {
                    echo "<tr>";
                    echo "<td>Tarif Bongkar</td>";
                    echo "<td>:</td>";
                    echo "<td> Rp " . intval($tarif1). " /kg = " . "<b style='font-size: 14px; font-weight: bold;'>" . format_rupiah(($r['netto'] + $r['rafaksi']) * intval($tarif1)) . "</b></td>";
                    echo "</tr>";
                } elseif ($r['cost_bongkar'] > 0 && $r['laborer_sts'] == 'Ya' && $jenis != 'B') {
                    if ($r['weighing_sts2'] == 'Gabungan') {
                        echo "<tr>";
                        echo "<td>Tarif Bongkar</td>";
                        echo "<td>:</td>";
                        echo "<td> Rp " . intval($tarif1). " /kg = " . "<b style='font-size: 14px; font-weight: bold;'>" . format_rupiah($netto_mitra * intval($tarif1)) . "</b></td>";
                        echo "</tr>";
                    } else {
                        echo "<tr>";
                        echo "<td>Tarif Bongkar</td>";
                        echo "<td>:</td>";
                        echo "<td> Rp " . intval($tarif1). " /kg = " . "<b style='font-size: 14px; font-weight: bold;'>" . format_rupiah($r['netto'] * intval($tarif1)) . "</b></td>";
                        echo "</tr>";
                    }
                }
            ?>
		<tr>
			<td>
                <?php if ($r['cost_bongkar'] > 0 && $r['laborer_sts'] == 'Ya' || $cost_tonase > $tarif_min) {
                    echo "Total Tagihan"?>
                <?php } ?>
            </td>
            <td>
                <?php if ($r['cost_bongkar'] > 0 && $r['laborer_sts'] == 'Ya' || $cost_tonase > $tarif_min) {
                    echo ":"?>
                <?php } ?>
            </td>
			<td> 
                <?php
                    if (($r['cost_bongkar'] > 0 && $r['laborer_sts'] == 'Ya') || $cost_tonase > $tarif_min) {
                        if ($jenis == 'B') {
                            // Kondisi ketika $jenis == 'B'
                            echo '<b style="font-size: 16px; font-weight: bold;">' . format_rupiah($r['cost_tonase'] + ($r['netto'] + $r['rafaksi']) * intval($tarif1)) . '</b>';
                        } elseif ($jenis != 'B') {
                            // Kondisi ketika $jenis != 'B'
                            if ($r['weighing_sts2'] == 'Gabungan') {
                                echo '<b style="font-size: 16px; font-weight: bold;">' . format_rupiah($r['cost_tonase'] + ($netto_mitra  * intval($tarif1))) . '</b>';
                            } else {
                                echo '<b style="font-size: 16px; font-weight: bold;">' . format_rupiah($r['cost_tonase'] + ($r['netto']  * intval($tarif1))) . '</b>';
                            }
                        }
                    }
                ?>
			</td>
		</tr>
		<!-- <tr><td>Tarif Bongkar  		</td><td> : <?php if ($r['laborer_sts'] != "Ya") {
                echo "-";
            } else {
                echo "Rp. ".intval($tarif1)." /kg = "; ?><b
			style="font-size: 14px; font-weight: bold;"><?php echo format_rupiah($biayamitra); ?></b><?php
            } ?></td>
		</tr> -->
		<!-- <tr><td>Total Biaya Tonase 	</td><td> : <b style="font-size: 14px; font-weight: bold;"><?php echo format_rupiah($biayatonase); ?></b>
		</td>
		</tr>
		<tr>
			<td>Total Biaya Mitra </td>
			<td> : <b
					style="font-size: 14px; font-weight: bold;"><?php echo format_rupiah($biayamitra); ?></b>
			</td>
		</tr> -->
		<!-- <tr><td>Total Biaya 	 	</td><td> : <b style="font-size: 14px; font-weight: bold;"><?php echo format_rupiah($r['cost']); ?></b>
		</td>
		</tr> -->
		<?php
            // jika ada mitra
            if ($r['laborer_sts']=='Ya' && $r['weighing_sts2'] != 'Gabungan') {
                
                $codes = [
                    'B001', 'B002', 'B003', 'B004', 'B005', 'B006', 'B007', 'B008', 'B009', 'B010', 'B011', 'B012', 'B013', 'B014', 'B015',
                    'S001', 'S002', 'S003', 'S004', 'S005', 'S006', 'S007', 'S008', 'S009', 'S010', 'S011', 'S012', 'S013', 'S014', 'S015',
                    'M01', 'M02', 'M03', 'M04'
                ];
                
                $code_results = [];
                
                foreach ($codes as $code) {
                    $sum = executeScalar("SELECT SUM($code) FROM laborer_fee WHERE weighing_id = '".$id."'");
                    if ($sum > 0) {
                        $code_results[] = $code; // Tambahkan kolom ke hasil jika ada nilai
                    }
                }
                
                $finalCodes = implode(', ', $code_results);
                ?>

                <tr>
                    <td style="vertical-align: top;">
                        Kode Mitra & Mandor
                    </td>
                    <td style="vertical-align: top;">
                        :
                    </td>
                    <td width="190" style="vertical-align: top;"> 
                        <?php echo $finalCodes ?>
                    </td>
                </tr>
            <?php } elseif ($r['weighing_sts2'] == 'Gabungan') { ?>
                <tr>
                    <td style="vertical-align: top;">
                        Nota Referensi
                    </td>
                    <td style="vertical-align: top;">
                        :
                    </td>
                    <td width="190" style="vertical-align: top;"> 
                        <?php
                            $finalReffer = implode(", ", $reffer_array);
                            echo $finalReffer;
                        ?>
                    </td>
                </tr>
            <?php }?>
    </table><br />

	<table style="width:95%; border:0px; margin-top: -15px; font-size:7pt;">
		<tr>
			<td style="text-align: left; width: 10%; vertical-align: top;"><b>NB :</b></td>
			<td style="text-align: left; width: 5%; vertical-align: top;">•</td>
			<td style="text-align: left; width: 85%; vertical-align: top;">
				Nota harap disimpan untuk bukti bayar. Cek Kembali.
			</td>
		</tr>
		<tr>
			<td style="text-align: left; width: 10%; vertical-align: top;"></td>
			<td style="text-align: left; width: 5%; vertical-align: top;">•</td>
			<td style="text-align: left; width: 85%; vertical-align: top;">
				Tarif Bongkar merupakan "Pengembalian" Mitra Bongkar.
			</td>
		</tr>
	</table>
    <!-- <hr style="border: 1px dashed #525659; height: 1px;"> -->
     <br>
	<page_footer>
        <table style="width: 100%; high: 50px; border: solid 0px black; border-collapse: collapse; margin-top:5px; font-size:7pt;">
            <tr>
                <th style="text-align: center;width: 33%;">Kasir</th>
                <th style="text-align: center;width: 34%;">Penerima Nota</th>
                <th style="text-align: center;width: 33%;">Checker</th>
            </tr>
            <tr>
                <td style="text-align: center;width: 33%;"><br><br>&nbsp;( ................ )</td>
                <td style="text-align: center;width: 34%;"><br><br>&nbsp;( ................ )</td>
                <td style="text-align: center;width: 33%;"><br><br>&nbsp;( ................ )</td>
            </tr>
        </table>
        <br><br>
        <div style="font-size: 9px; font-weight: bold; text-align: center;">
            == PASAR INDUK SURABAYA SIDOTOPO ==
        </div>
	</page_footer>

	<?php 

        //simpan data siapa yg ngeprint
        $print_user = !empty($spv_name) ? $spv_name : $cashier_name;
        $print_role = !empty($spv_name) ? 'spv' : 'cashier';

        $insert = mysql_query("INSERT INTO weighing_print (weighing_id, print_user, print_role, print_date, print_nopol, print_merchant_id, print_action) 
                            VALUE ('$id', '$print_user', '$print_role', '$dt_now', '".$r['nopol']."', '".$r['merchant_id']."', 'Print Nota Tagihan')");
    } ?>
</page>