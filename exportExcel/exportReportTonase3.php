<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$date 		= date("d M Y");

    
    // Buat objek PHPExcel
    $excel = new PHPExcel();
    

    // ==================== SHEET 1 =====================

    // Query untuk mengambil data
    $query1 = mysql_query("select * from goods where goods_type = 'Buah' order by goods_name");

    $sheet1 = $excel->setActiveSheetIndex(0);
    $sheet1->setTitle("Tonase Komoditi (Buah)");

      // Menulis Informasi di Atas Tabel
      $sheet1->setCellValue("A1", "REKAP PENDAPATAN TONASE SEMUA KOMODITI (BUAH)");
      $sheet1->setCellValue("A3", "TANGGAL");
      $sheet1->setCellValue("B3", " : ");
      $sheet1->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

      // Membuat text bold
      $sheet1->getStyle("A1:B3")->getFont()->setBold(true);
    
      // Meratakan semua teks ke tengah
      $sheet1->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      $sheet1->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
      // Merge cell
      $sheet1->mergeCells("A1:C1");
      
    // Menulis header
    $sheet1->setCellValue("A5", "NO")
          ->setCellValue("B5", "NAMA KOMODITI SAYUR")
          ->setCellValue("C5", "JUMLAH TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:M5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:M5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet1->getStyle("A5:C5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom B (Komoditi)
    $sheet1->getStyle('A5:C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no1	    = 1;
    $total1		= 0;
    $row1     = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data1 = mysql_fetch_array($query1)) {
        $tonase1		= executeScalar("select sum(netto) from weighing 
                                    where goods_id = '".$data1['goods_id']."' and 
                                    weighing_sts != 'Batal' and 
                                    (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')");
        
        // Jika data kosong
        $tonase1 = ($tonase1 == 0) ? 0 : $tonase1;

        // Meratakan semua teks ke tengah, kecuali kolom B (Pedagang)
        $sheet1->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B (Pedagang) ke kiri
        $sheet1->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet1->setCellValue("A" . $row1, $no1)
              ->setCellValue("B" . $row1, $data1['goods_name'])
              ->setCellValue("C" . $row1, $tonase1);

        // Membuat format menjadi number
        $sheet1->getStyle("C" . $row1)->getNumberFormat()->setFormatCode('#,##0');
      
        $no1++;
        $row1++;
    }

    // Menulis total pada baris terakhir
    if ($row1 > 6) {
        $sheet1->mergeCells("A" . $row1 . ":B" . $row1); 
        $sheet1->setCellValue("A" . $row1, "TOTAL")
              ->setCellValue("C" . $row1, "=SUM(C6:C" . ($row1 - 1) . ")");
    } else {
        $sheet1->mergeCells("A" . $row1 . ":B" . $row1); 
        $sheet1->setCellValue("A" . $row1, "TOTAL")
              ->setCellValue("C" . $row1, 0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet1->getStyle("A" . $row1 . ":M" . $row1)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet1->getStyle("A" . $row1 . ":M" . $row1)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet1->getStyle("A" . $row1 . ":C" . $row1)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet1->getStyle("A" . $row1 . ":C" . $row1)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      // Membuat format menjadi number
      $sheet1->getStyle("C" . $row1)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet1->getStyle("A5:C" . $row1)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


    // ==================== SHEET 2 =====================

    // Query untuk mengambil data
    $query2 = mysql_query("select * from goods where goods_type = 'Sayur' order by goods_name");

    $sheet2 = $excel->createSheet();
    $sheet2->setTitle("Tonase Komoditi (Sayur)");

    // Menulis Informasi di Atas Tabel
    $sheet2->setCellValue("A1", "REKAP PENDAPATAN TONASE SEMUA KOMODITI (SAYUR)");
    $sheet2->setCellValue("A3", "TANGGAL");
    $sheet2->setCellValue("B3", " : ");
    $sheet2->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet2->getStyle("A1:B3")->getFont()->setBold(true);
  
    // Meratakan semua teks ke tengah
    $sheet2->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet2->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
    // Merge cell
    $sheet2->mergeCells("A1:C1");

    // Menulis header
    $sheet2->setCellValue("A5", "NO")
          ->setCellValue("B5", "NAMA KOMODITI SAYUR")
          ->setCellValue("C5", "JUMLAH TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:M5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:M5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet2->getStyle("A5:C5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom B (Komoditi)
    $sheet2->getStyle('A5:C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no2		= 1;
    $row2   = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data2 = mysql_fetch_array($query2)) {
        $tonase2		= executeScalar("select sum(netto) from weighing 
                                    where goods_id = '".$data2['goods_id']."' and 
                                    weighing_sts != 'Batal' and 
                                    (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')");
        
        // Jika data kosong
        $tonase2 = ($tonase2 == 0) ? 0 : $tonase2;

        // Meratakan semua teks ke tengah, kecuali kolom B (Pedagang)
        $sheet2->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B (Pedagang) ke kiri
        $sheet2->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet2->setCellValue("A" . $row2, $no2)
              ->setCellValue("B" . $row2, $data2['goods_name'])
              ->setCellValue("C" . $row2, $tonase2);
        
      // Membuat format menjadi number
      $sheet2->getStyle("C" . $row2)->getNumberFormat()->setFormatCode('#,##0');

        $no2++;
        $row2++;
    }

    // Menulis total pada baris terakhir
    if ($row2 > 6) {
        $sheet2->mergeCells("A" . $row2 . ":B" . $row2); 
        $sheet2->setCellValue("A" . $row2, "TOTAL")
              ->setCellValue("C" . $row2, "=SUM(C6:C" . ($row2 - 1) . ")");
    } else {
        $sheet2->mergeCells("A" . $row2 . ":I" . $row2); 
        $sheet2->setCellValue("A" . $row2, "TOTAL")
              ->setCellValue("C" . $row2, 0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet2->getStyle("A" . $row2 . ":M" . $row2)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet2->getStyle("A" . $row2 . ":M" . $row2)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet2->getStyle("A" . $row2 . ":C" . $row2)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet2->getStyle("A" . $row2 . ":C" . $row2)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      // Membuat format menjadi number
      $sheet2->getStyle("C" . $row2)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet2->getStyle("A5:C" . $row2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


    // ==================== SHEET 3 =====================

    // Query untuk mengambil data
    $query3 = mysql_query("select * from goods where goods_type = 'Pelengkap' order by goods_name");

    $sheet3 = $excel->createSheet();
    $sheet3->setTitle("Tonase Komoditi (Pelengkap)");

    // Menulis Informasi di Atas Tabel
    $sheet3->setCellValue("A1", "REKAP PENDAPATAN TONASE SEMUA KOMODITI (PELENGKAP)");
    $sheet3->setCellValue("A3", "TANGGAL");
    $sheet3->setCellValue("B3", " : ");
    $sheet3->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet3->getStyle("A1:B3")->getFont()->setBold(true);
  
    // Meratakan semua teks ke tengah
    $sheet3->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet3->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  
    // Merge cell
    $sheet3->mergeCells("A1:C1");

    // Menulis header
    $sheet3->setCellValue("A5", "NO")
          ->setCellValue("B5", "NAMA KOMODITI PELENGKAP")
          ->setCellValue("C5", "JUMLAH TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:M5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:M5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet3->getStyle("A5:C5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom B (Komoditi)
    $sheet3->getStyle('A5:C5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no3	      = 1;
    $row3       = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data3 = mysql_fetch_array($query3)) {
        $tonase3		= executeScalar("select sum(netto) from weighing 
                                    where goods_id = '".$data3['goods_id']."' and 
                                    weighing_sts != 'Batal' and 
                                    (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')");
        
        // Jika data kosong
        $tonase3 = ($tonase3 == 0) ? 0 : $tonase3;

        // Meratakan semua teks ke tengah, kecuali kolom B (Pedagang)
        $sheet3->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B (Pedagang) ke kiri
        $sheet3->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet3->setCellValue("A" . $row3, $no3)
              ->setCellValue("B" . $row3, $data3['goods_name'])
              ->setCellValue("C" . $row3, $tonase3);
        
            // Membuat format menjadi number
            $sheet3->getStyle("C" . $row3)->getNumberFormat()->setFormatCode('#,##0');

        $no3++;
        $row3++;
    }

    // Menulis total pada baris terakhir
    if ($row3 > 6) {
        $sheet3->mergeCells("A" . $row3 . ":B" . $row3); 
        $sheet3->setCellValue("A" . $row3, "TOTAL")
              ->setCellValue("C" . $row3, "=SUM(C6:C" . ($row3 - 1) . ")");
    } else {
        $sheet3->mergeCells("A" . $row3 . ":I" . $row3); 
        $sheet3->setCellValue("A" . $row3, "TOTAL")
              ->setCellValue("C" . $row3, 0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet3->getStyle("A" . $row3 . ":M" . $row3)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet3->getStyle("A" . $row3 . ":M" . $row3)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet3->getStyle("A" . $row3 . ":C" . $row3)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet3->getStyle("A" . $row3 . ":C" . $row3)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

      // Membuat format menjadi number
      $sheet3->getStyle("C" . $row3)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet3->getStyle("A5:C" . $row3)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


    // Set sheet active
    $excel->setActiveSheetIndex(0);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Tonase PerKomoditi_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
