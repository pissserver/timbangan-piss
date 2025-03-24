<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    $query = mysql_query("select distinct(date(date_check)) as do from weighing 
                        where weighing_sts != 'Batal' and 
                        (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Rekap Tonase Harian");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP PENDAPATAN TONASE HARIAN");
    $sheet->setCellValue("A3", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:F1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "TANGGAL")
          ->setCellValue("C5", "LAINNYA (KG)")
          ->setCellValue("D5", "SAYUR (KG)")
          ->setCellValue("E5", "BUAH (KG)")
          ->setCellValue("F5", "JUMLAH TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:I5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A5:F5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $fruit = executeScalar("select sum(w.netto) from weighing w, merchant m 
                                where w.merchant_id = m.merchant_id and 
                                (w.date_check between '".$data['do']." 00:00:00' and '".$data['do']." 23:59:59') and 
                                m.merchant_type = 'Buah' and 
                                w.weighing_sts != 'Batal'");

        $vegie = executeScalar("select sum(w.netto) from weighing w, merchant m 
                                where w.merchant_id = m.merchant_id and 
                                (w.date_check between '".$data['do']." 00:00:00' and '".$data['do']." 23:59:59') and 
                                m.merchant_type = 'Sayur' and 
                                w.weighing_sts != 'Batal'");

        $other = executeScalar("select sum(w.netto) from weighing w, merchant m 
                                where w.merchant_id = m.merchant_id and 
                                (w.date_check between '".$data['do']." 00:00:00' and '".$data['do']." 23:59:59') and 
                                m.merchant_type = 'Lainnya' and 
                                w.weighing_sts != 'Batal'");

        // Meratakan semua teks ke tengah
        $sheet->getStyle('A:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        // $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['do'])))
              ->setCellValue("C" . $row, $other == 0 ? '0' : $other)
              ->setCellValue("D" . $row, $vegie == 0 ? '0' : $vegie)
              ->setCellValue("E" . $row, $fruit == 0 ? '0' : $fruit)
              ->setCellValue("F" . $row, "=SUM(C" . $row . ":E" . $row . ")");
            
        // Membuat format menjadi number
        $sheet->getStyle("C" . $row . ":F" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
    if ($row > 6) {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row, "=SUM(C6:C" . ($row - 1) . ")")
              ->setCellValue("D" . $row, "=SUM(D6:D" . ($row - 1) . ")")
              ->setCellValue("E" . $row, "=SUM(E6:E" . ($row - 1) . ")")
              ->setCellValue("F" . $row, "=SUM(F6:F" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":K" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row,  0)
              ->setCellValue("D" . $row,  0)
              ->setCellValue("E" . $row,  0)
              ->setCellValue("F" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":F" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":F" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("C" . $row . ":F" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A5:F" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Pendapatan Tonase Harian_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
