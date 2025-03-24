<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date_1 	= date("d/m/Y");
	$date 		= date("2016-03-14");
	$commodity	= $_POST['commodity'];
	$date2		= $_POST['date2'];

    // Query untuk mengambil data
    if($commodity=='Semua'){
        $sql = "select w.weighing_id, w.date_in, w.time_in, w.merchant_id, m.merchant_name, w.nopol, w.weight_in, goods_id 
        from weighing w, merchant m 
        where w.merchant_id = m.merchant_id and 
        (w.date_in between '".$date."' and '".$date2."') and 
        w.date_out is NULL and
        m.merchant_status = 'Aktif'
        AND w.weighing_sts != 'Batal'";
    }else{
        $sql = "select w.weighing_id, w.date_in, w.time_in, w.merchant_id, m.merchant_name, w.nopol, w.weight_in, goods_id 
        from weighing w, merchant m 
        where w.merchant_id = m.merchant_id and 
        (w.date_in between '".$date."' and '".$date2."') and 
        w.date_out is NULL and
        m.merchant_type = '".$commodity."' and 
        m.merchant_status = 'Aktif'
        AND w.weighing_sts != 'Batal'";
    }

    $query 	= mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Laporan Outstanding Timbang ");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "LAPORAN TRANSAKSI OUTSTANDING TIMBANG");
    $sheet->setCellValue("A3", "JENIS KOMODITI");
    $sheet->setCellValue("A4", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", $commodity);
    $sheet->setCellValue("C4", date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Meratakan semua teks ke kiri
    $sheet->getStyle("A3:A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    
    // Merge cell
    $sheet->mergeCells("A1:I1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "TANGGAL MASUK")
          ->setCellValue("C6", "JAM")
          ->setCellValue("D6", "NOTA TIMBANG")
          ->setCellValue("E6", "PEDAGANG")
          ->setCellValue("F6", "NOPOL")
          ->setCellValue("G6", "JENIS KOMODITI")
          ->setCellValue("H6", "LAPAK")
          ->setCellValue("I6", "BERAT MASUK (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:I6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $total		= 0;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
        $goods	= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");
        $lpk 	= executeScalar("select l.lapak_name from rent r, lapak l 
                where r.lapak_id = l.lapak_id and r.merchant_id = '".$data['merchant_id']."' 
                and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') limit 1");
        $weighing_id = " " . $data['weighing_id'];
        
        // Jika data kosong
        $data['weight_in'] = $data['weight_in'] == '' ? 0 : $data['weight_in'];

        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_in'])))
              ->setCellValue("C" . $row, date("H:i", strtotime($data['time_in'])))
              ->setCellValue("D" . $row, $weighing_id)
              ->setCellValue("E" . $row, $name)
              ->setCellValue("F" . $row, $data['nopol'])
              ->setCellValue("G" . $row, $goods)
              ->setCellValue("H" . $row, $lpk)
              ->setCellValue("I" . $row, $data['weight_in']);
            
        // Membuat format menjadi number
        $sheet->getStyle("I" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row, "=SUM(I7:I" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":I" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":I" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("I" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:I" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Laporan Transaksi Outstanding Timbang' . ' (' .$commodity. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
