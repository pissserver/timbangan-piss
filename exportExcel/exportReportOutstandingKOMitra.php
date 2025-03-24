<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

    $cashier= $_POST['cashier'];
    $date	= $_POST['date2'];
	$date_now 	= date("d M Y");

    // Query untuk mengambil data
    if($cashier=='Semua'){
        $sql = "select * from ko_mitra where ko_status != 'Acc' and ko_date <= '".$date."' order by ko_id";
        $cost = executeScalar("select sum(ko_total) from ko_mitra where ko_status != 'Acc'");
    }else{
        $sql = "select * from ko_mitra where ko_status != 'Acc' and cashier_id = '".$cashier."' and ko_date <= '".$date."' order by ko_id";
        $cost = executeScalar("select sum(ko_total) from ko_mitra where ko_status != 'Acc' and cashier_id = '".$cashier."'");
    }

    $query = mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Outstanding KO Mitra");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN OUTSTANDNG KOM MITRA");
    $sheet->setCellValue("A3", "TANGGAL AKHIR");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);
        
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
    // Merge cell
    $sheet->mergeCells("A1:F1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "KOM ID")
          ->setCellValue("C5", "NAMA KASIR")
          ->setCellValue("D5", "NAMA PENYETOR")
          ->setCellValue("E5", "TANGGAL KOM")
          ->setCellValue("F5", "TOTAL KOM (RP)");

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
        $checker = executeScalar("select checker_name from checker where checker_id = '".$data['receiver_id']."'");
        $cashier = executeScalar("select checker_name from checker where checker_id = '".$data['cashier_id']."'");

        // Meratakan semua teks ke tengah, kecuali kolom C:D (Nama)
        $sheet->getStyle('A:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom C:D (Nama) ke kiri
        $sheet->getStyle('C:D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, 'KOM'.$data['ko_id'])
              ->setCellValue("C" . $row, $cashier)
              ->setCellValue("D" . $row, $checker)
              ->setCellValue("E" . $row, date("d M Y", strtotime($data['ko_date'])))
              ->setCellValue("F" . $row, $data['ko_total']);
            
        // Membuat format menjadi number
        $sheet->getStyle("F" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":E" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("F" . $row, "=SUM(F6:F" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":E" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
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
    $sheet->getStyle("F" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A5:F" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Outstanding KO Tonase_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
