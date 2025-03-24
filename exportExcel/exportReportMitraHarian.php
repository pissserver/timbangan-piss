<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date_omzet'];
	$commodity	= $_POST['commodity'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    if ($commodity == "Semua") {
        $sql 	= "select wm.weighing_id, 
                                    wm.nopol, 
                                    wm.goods_id, 
                                    wm.checker_id, 
                                    wm.merchant_id, 
                                    wm.reffer, 
                                    wm.date_check,
                                    wm.weighing_sts, 
                                    wm.netto, 
                                    wm.cost_bayar,
                                    (wm.cost_bagi_mitra + wm.cost_simpan_mitra) AS bagi_mitra,
                                    (wm.cost_bagi_mandor + wm.cost_simpan_mandor) AS bagi_mandor,
                                    wm.cost,
                                    merchant_type
                            from 	weighing_mitra wm, 
                                    merchant m 
                            where 	wm.merchant_id = m.merchant_id and 
                                    wm.weighing_sts != 'Batal' and 
                                    (wm.date_check between '".$date1." 00:00:00' and '".$date1." 23:59:59')";
    } else {
        $sql 	= "select wm.weighing_id, 
                                    wm.nopol, 
                                    wm.goods_id, 
                                    wm.checker_id, 
                                    wm.merchant_id, 
                                    wm.reffer, 
                                    wm.date_check,
                                    wm.weighing_sts, 
                                    wm.netto, 
                                    wm.cost_bayar,
                                    (wm.cost_bagi_mitra + wm.cost_simpan_mitra) AS bagi_mitra,
                                    (wm.cost_bagi_mandor + wm.cost_simpan_mandor) AS bagi_mandor,
                                    wm.cost,
                                    merchant_type
                            from 	weighing_mitra wm, 
                                    merchant m 
                            where 	wm.merchant_id = m.merchant_id and 
                                    wm.weighing_sts != 'Batal' and 
                                    m.merchant_type = '".$commodity."' and
                                    (wm.date_check between '".$date1." 00:00:00' and '".$date1." 23:59:59')";
    }

    $query = mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Omzet Mitra Harian");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN OMZET MITRA & MANDOR HARIAN");
    $sheet->setCellValue("A3", "JENIS KOMODITI");
    $sheet->setCellValue("A4", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", $commodity);
    $sheet->setCellValue("C4", date('d M Y', strtotime($date1)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:K1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
        ->setCellValue("B6", "TANGGAL TAGIHAN")
        ->setCellValue("C6", "NOTA TAGIHAN")
        ->setCellValue("D6", "NOPOL")
        ->setCellValue("E6", "PEDAGANG")
        ->setCellValue("F6", "JENIS")
        ->setCellValue("G6", "STATUS")
        ->setCellValue("H6", "NETTO BONGKAR (KG)")
        ->setCellValue("I6", "BAGI MITRA (RP)")
        ->setCellValue("J6", "BAGI MANDOR (RP)")
        ->setCellValue("K6", "TOTAL MITRA (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:K6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom F (Pedagang)
    $sheet->getStyle('A6:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
        $kat_merchant	= executeScalar("select kat_merchant from merchant where merchant_id = '".$data['merchant_id']."'");
		$goods		= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");

        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_check'])))
              ->setCellValue("C" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("D" . $row, $data['nopol'])
              ->setCellValue("E" . $row, $name)
              ->setCellValue("F" . $row, $data['merchant_type'])
              ->setCellValue("G" . $row, $data['weighing_sts'])
              ->setCellValue("H" . $row, $data['netto'])
              ->setCellValue("I" . $row, $data['bagi_mitra'])
              ->setCellValue("J" . $row, $data['bagi_mandor'])
              ->setCellValue("K" . $row, $data['cost']);
            
        // Membuat format menjadi number
        $sheet->getStyle("H" . $row . ":K" . $row)->getNumberFormat()->setFormatCode('#,##0');

        // Perhitungan total
        // if ($data['weighing_sts'] != 'Batal' && $data['weighing_sts'] != 'Dipecah' && $data['weighing_sts'] != 'Digabung') {
        //     $weight_in += $data['weight_in'];
        //     $weight_out += $data['weight_out'];
        //     $noncommodity += $data['noncommodity'];
        //     $rafaksi += $data['rafaksi'];
        //     $netto += $data['netto'];
        // }

        $no++;
        $row++;
    }

    // Menulis total pada baris terakhir
    if ($row > 7) {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("H" . $row, "=SUM(H7:H" . ($row - 1) . ")")
              ->setCellValue("I" . $row, "=SUM(I7:I" . ($row - 1) . ")")
              ->setCellValue("J" . $row, "=SUM(J7:J" . ($row - 1) . ")")
              ->setCellValue("K" . $row, "=SUM(K7:K" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("H" . $row,  0)
              ->setCellValue("I" . $row,  0)
              ->setCellValue("J" . $row,  0)
              ->setCellValue("K" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":K" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":K" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("H" . $row . ":K" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:K" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Omzet Mitra & Mandor Harian' . ' (' .$commodity. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
