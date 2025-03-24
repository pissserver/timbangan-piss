<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$ko			= $_POST['ko'];
	$date2 		= date("d M Y");

    // Query untuk mengambil data
    $sql = "select 	weighing_id, 
            merchant_id, 
            checker_id, 
            reffer, 
            date_check, 
            nopol, 
            netto,
            (cost_bagi_mitra + cost_simpan_mitra) AS bagi_mitra,
            (cost_bagi_mandor + cost_simpan_mandor) AS bagi_mandor,  
            cost,
            cost_bayar
        from 	weighing_mitra 
        where 	weighing_sts = 'Lunas' and 
            ko_id = '".$ko."'";
    
    $query = mysql_query($sql);

    $cashier_id	= executeScalar("select cashier_id from ko_mitra where ko_id = '".$ko."'");
    $cashier	= executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");
    $checker_id	= executeScalar("select receiver_id from ko_mitra where ko_id = '".$ko."'");
    $checker	= executeScalar("select checker_name from checker where checker_id = '".$checker_id."'");
    $ko_date	= executeScalar("select ko_date from ko_mitra where ko_id = '".$ko."'");
    $ko_total	= executeScalar("select ko_total from ko_mitra where ko_id = '".$ko."'");

    $query = mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Rincian KOM Mitra");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN SETORAN KASIR OPERASIONAL MITRA");

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:J1");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A3", "KASIR OPERASIONAL MITRA ID  ")
          ->setCellValue("A4", "KASIR OPERASIONAL           ")
          ->setCellValue("A5", "PENYETOR                    ")
          ->setCellValue("A6", "TOTAL SETORAN               ")
          ->setCellValue("A7", "TANGGAL BAYAR                 ")
          ->setCellValue("B3", ':')
          ->setCellValue("B4", ':')
          ->setCellValue("B5", ':')
          ->setCellValue("B6", ':')
          ->setCellValue("B7", ':')
          ->setCellValue("C3", $ko )
          ->setCellValue("C4", $cashier)
          ->setCellValue("C5", $checker)
          ->setCellValue("C6", $ko_total)
          ->setCellValue("C7", date('d M Y', strtotime($ko_date)));

    // Membuat text bold
    $sheet->getStyle("A3:C7")->getFont()->setBold(true);

    // Membuat format menjadi number
    $sheet->getStyle("C6")->getNumberFormat()->setFormatCode('#,##0');

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("C3:C7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


    // Menulis header
    $sheet->setCellValue("A9", "NO")
          ->setCellValue("B9", "TANGGAL TAGIHAN")
          ->setCellValue("C9", "NOTA TAGIHAN")
          ->setCellValue("D9", "NOPOL")
          ->setCellValue("E9", "PEDAGANG")
          ->setCellValue("F9", "JENIS PEDAGANG")
          ->setCellValue("G9", "TONASE (KG)")
          ->setCellValue("H9", "BAGI KULI (RP)")
          ->setCellValue("I9", "BAGI MANDOR (RP)")
          ->setCellValue("J9", "TOTAL (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A9:J9")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A9:J9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 10;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name		= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
        $jenis		= executeScalar("select merchant_type from merchant where merchant_id = '".$data['merchant_id']."'");
        $kode_jenis	= mb_substr($jenis, 0, 1, 'utf-8');

        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:J')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // if($r['reffer']=='null'||$r['reffer']==''){
        //     $weigingID = 'TB'.$r['weighing_id'];
        // }else{
        //     $weigingID = 'TB'.$r['reffer'];
        // }

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_check'])))
            //   ->setCellValue("C" . $row, $weigingID)
              ->setCellValue("C" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("D" . $row, $data['nopol'])
              ->setCellValue("E" . $row, $name)
              ->setCellValue("F" . $row, $jenis)
              ->setCellValue("G" . $row, $data['netto'])
              ->setCellValue("H" . $row, $data['bagi_mitra'])
              ->setCellValue("I" . $row, $data['bagi_mandor'])
              ->setCellValue("J" . $row, ($kode_jenis == 'B') ? $data['cost_bayar'] : (($kode_jenis == 'S') ? $data['cost'] : 0));
            
        // Membuat format menjadi number
        $sheet->getStyle("G" . $row . ":J" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
    if ($row > 10) {
        $sheet->mergeCells("A" . $row . ":F" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("G" . $row, "=SUM(G10:G" . ($row - 1) . ")")
              ->setCellValue("H" . $row, "=SUM(H10:H" . ($row - 1) . ")")
              ->setCellValue("I" . $row, "=SUM(I10:I" . ($row - 1) . ")")
              ->setCellValue("J" . $row, "=SUM(J10:J" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("G" . $row,  0)
              ->setCellValue("H" . $row,  0)
              ->setCellValue("I" . $row,  0)
              ->setCellValue("J" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":J" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":J" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("G" . $row . ":J" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A9:J" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Setoran Kasir Operasional Mitra_' . date('Ymd') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
