<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date		= '2016-10-01';
	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    $sql = "select DATE(date_check) dateonly
			from weighing w
			where (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
			w.weighing_sts != 'Batal' and
			(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')
			group by dateonly";

    $query 	= mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Laporan Tonase Belum Terbayar");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP TAGIHAN TONASE BELUM TERBAYAR (PER TANGGAL)");
    $sheet->setCellValue("A3", "TANGGAL AWAL");
    $sheet->setCellValue("A4", "TANGGAL AKHIR");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date1)));
    $sheet->setCellValue("C4", date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:F1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "TANGGAL TAGIHAN")
          ->setCellValue("C6", "LAINNYA (RP)")
          ->setCellValue("D6", "SAYURAN (RP)")
          ->setCellValue("E6", "BUAH (RP)")
          ->setCellValue("F6", "TOTAL (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:F6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $date3 		= date("Y-m-d", strtotime($data['dateonly']));
        $other = executeScalar("select sum(w.cost_tonase) from weighing w, merchant m 
				where w.merchant_id = m.merchant_id and 
				m.merchant_status = 'Aktif' and
				m.merchant_type = 'Lainnya' and 
				(w.date_check between '".$date." 00:00:00' and '".$date3." 23:59:59') and
				(w.date_pay > '".$date3."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				
				$vegie = executeScalar("select sum(w.cost_tonase) from weighing w, merchant m 
				where w.merchant_id = m.merchant_id and 
				m.merchant_status = 'Aktif' and
				m.merchant_type = 'Sayur' and 
				(w.date_check between '".$date." 00:00:00' and '".$date3." 23:59:59') and
				(w.date_pay > '".$date3."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				
				$fruit = executeScalar("select sum(w.cost_tonase) from weighing w, merchant m 
				where w.merchant_id = m.merchant_id and 
				m.merchant_status = 'Aktif' and
				m.merchant_type = 'Buah' and 
				(w.date_check between '".$date." 00:00:00' and '".$date3." 23:59:59') and
				(w.date_pay > '".$date3."' || w.date_pay is NULL) and
				w.weighing_sts != 'Batal' and
				(w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')");
				


        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        // $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['dateonly'])))
              ->setCellValue("C" . $row, $other != null ? $other : '0')
              ->setCellValue("D" . $row, $vegie != null ? $vegie : '0')
              ->setCellValue("E" . $row, $fruit != null ? $fruit : '0')
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
    if ($row > 7) {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row, "=SUM(C7:C" . ($row - 1) . ")")
              ->setCellValue("D" . $row, "=SUM(D7:D" . ($row - 1) . ")")
              ->setCellValue("E" . $row, "=SUM(E7:E" . ($row - 1) . ")")
              ->setCellValue("F" . $row, "=SUM(F7:F" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
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
    $sheet->getStyle("A6:F" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Tagihan Tonase Belum Terbayar (Per Tanggal)_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
