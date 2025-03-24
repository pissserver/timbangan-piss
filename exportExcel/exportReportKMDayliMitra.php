<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$date_now 	= date("d M Y");

    // Query untuk mengambil data
    $query 	= mysql_query("select DISTINCT(km_date) as kmdt from km_mitra where km_date between '".$date1."' and '".$date2."'");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("KMM Mitra Harian");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP TAGIHAN MITRA KMM HARIAN");
    $sheet->setCellValue("A3", "TANGGAL SETORAN KMM");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);
        
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:E1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "TANGGAL KMM")
          ->setCellValue("C5", "KMM AWAL")
          ->setCellValue("D5", "KMM AKHIR")
          ->setCellValue("E5", "TOTAL KMM (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:I5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A5:E5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A5:E5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $first_km 		= executeScalar("select km_id from km_mitra where km_date = '".$data['kmdt']."' order by km_id asc limit 1");
        $last_km 		= executeScalar("select km_id from km_mitra where km_date = '".$data['kmdt']."' order by km_id desc limit 1");
        $total_dayli 	= executeScalar("select sum(km_total) from km_mitra where km_date = '".$data['kmdt']."'");

        // Meratakan semua teks ke tengah
        $sheet->getStyle('A:E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B (Nama) ke kiri
        // $sheet->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date('d M Y', strtotime($data['kmdt'])))
              ->setCellValue("C" . $row, $first_km)
              ->setCellValue("D" . $row, $last_km)
              ->setCellValue("E" . $row, $total_dayli);
            
        // Membuat format menjadi number
        $sheet->getStyle("E" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("E" . $row, "=SUM(E6:E" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("E" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":E" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":E" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("E" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A5:E" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Tagihan Mitra KMM Harian' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
