<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= '2016-10-01';
	$date2		= $_POST['date2'];
	$commodity	= $_POST['commodity'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    if($commodity=='Semua'){
        $sql = "select 	w.weighing_id, 
                        m.merchant_id, 
                        w.checker_id, 
                        w.reffer, 
                        w.date_out, 
                        w.date_check, 
                        w.date_pay, 
                        w.nopol, 
                        w.netto, 
                        w.cost_tonase,
                        w.cost, 
                        m.merchant_name as name,
                        m.merchant_type as jenis
                from 	weighing w, 
                        merchant m 
                where 	w.merchant_id = m.merchant_id 
                        AND m.merchant_status = 'Aktif' 
                        and (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
                        (w.date_pay > '".$date2."' || w.date_pay is NULL) and
                        w.weighing_sts != 'Batal' and
                        (w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')";
    }
    else{
        $sql = "select 	w.weighing_id, 
                        m.merchant_id, 
                        w.checker_id, 
                        w.reffer, 
                        w.date_out, 
                        w.date_check, 
                        w.date_pay, 
                        w.nopol, 
                        w.netto, 
                        w.cost_tonase,
                        w.cost, 
                        m.merchant_name as name,
                        m.merchant_type as jenis 
                from 	weighing w, 
                        merchant m 
                where 	w.merchant_id = m.merchant_id
                        AND m.merchant_status = 'Aktif' 
                        and m.merchant_type = '".$commodity."' and 
                        (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
                        (w.date_pay > '".$date2."' || w.date_pay is NULL) and
                        w.weighing_sts != 'Batal' and
                        (w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')";
    }

    $query 	= mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Laporan Tonase Belum Terbayar");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN TAGIHAN TONASE BELUM TERBAYAR");
    $sheet->setCellValue("A3", "JENIS PEDAGANG");
    $sheet->setCellValue("A4", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", $commodity);
    $sheet->setCellValue("C4", date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:I1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "TANGGAL TAGIHAN")
          ->setCellValue("C6", "NOPOL")
          ->setCellValue("D6", "NOTA TAGIHAN")
          ->setCellValue("E6", "PEDAGANG")
          ->setCellValue("F6", "JENIS PEDAGANG")
          ->setCellValue("G6", "UMUR TAGIHAN")
          ->setCellValue("H6", "TONASE (KG)")
          ->setCellValue("I6", "TOTAL (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:I6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $date3 		= new DateTime(date("Y-m-d", strtotime($data['date_check'])));
        $date4 		= new DateTime(date("Y-m-d", strtotime($date2)));
        $diff 		= $date4->diff($date3)->format("%a");

        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_check'])))
              ->setCellValue("C" . $row, $data['nopol'])
              ->setCellValue("D" . $row, 'TG'.$data['weighing_id'])
              ->setCellValue("E" . $row, $data['name'])
              ->setCellValue("F" . $row, $data['jenis'])
              ->setCellValue("G" . $row, $diff. ' Hari')
              ->setCellValue("H" . $row, $data['netto'])
              ->setCellValue("I" . $row, $data['cost_tonase']);
        
        // Membuat format menjadi number
        $sheet->getStyle("H" . $row . ":I" . $row)->getNumberFormat()->setFormatCode('#,##0');
            
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
              ->setCellValue("I" . $row, "=SUM(I7:I" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("H" . $row,  0)
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
    $sheet->getStyle("H" . $row . ":I" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:I" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Tagihan Tonase Belum Terbayar' . ' (' .$commodity. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
