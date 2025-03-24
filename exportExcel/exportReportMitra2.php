<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date	= $_POST['date'];
	$date2 	= date("d M Y");

    // Query untuk mengambil data
    $query 	= mysql_query("	select 	lf.weighing_id, 
											B001_save,
											B002_save,
											B003_save,
											B004_save,
											B005_save,
											B006_save,
											S001_save,
											S002_save,
											S003_save,
											S004_save,
											S005_save,
											S006_save,
											S007_save,
											S008_save,
											S009_save,
											S010_save,
											S011_save,
											S012_save,					
											M01_save, 
											M02_save, 
											M03_save,
											M04_save 
									from 	laborer_fee lf
									JOIN 	weighing_mitra wm ON wm.weighing_id = lf.weighing_id
									where 	laborer_fee_sts = 'Baru' and 
											date(date_check) = '".$date."'");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Rincian Mitra Simpan (Sebelum)");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN PENDAPATAN MITRA DISIMPAN (SEBELUM DIBULATKAN)");
    $sheet->setCellValue("A3", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:X1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "NO NOTA")
          ->setCellValue("C5", "B001")
          ->setCellValue("D5", "B002")
          ->setCellValue("E5", "B003")
          ->setCellValue("F5", "B004")
          ->setCellValue("G5", "B005")
          ->setCellValue("H5", "B006")
          ->setCellValue("I5", "S001")
          ->setCellValue("J5", "S002")
          ->setCellValue("K5", "S003")
          ->setCellValue("L5", "S004")
          ->setCellValue("M5", "S005")
          ->setCellValue("N5", "S006")
          ->setCellValue("O5", "S007")
          ->setCellValue("P5", "S008")
          ->setCellValue("Q5", "S009")
          ->setCellValue("R5", "S010")
          ->setCellValue("S5", "S011")
          ->setCellValue("T5", "S012")
          ->setCellValue("U5", "MO1")
          ->setCellValue("V5", "M02")
          ->setCellValue("W5", "M03")
          ->setCellValue("X5", "M04");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:I5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A5:X5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A5:X5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 6;

    $total1	= 0;
    $keys = [
        'B001', 'B002', 'B003', 'B004', 'B005', 'B006',
        'S001', 'S002', 'S003', 'S004', 'S005', 'S006', 'S007', 'S008', 'S009', 'S010', 'S011', 'S012',
        'M01', 'M02', 'M03', 'M04'
    ];

    foreach ($keys as $key) {
        $key = 0;
    }

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {

        // Meratakan semua teks ke tengah
        $sheet->getStyle('A:X')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        // $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $keys = [
            'B001', 'B002', 'B003', 'B004', 'B005', 'B006',
            'S001', 'S002', 'S003', 'S004', 'S005', 'S006', 'S007', 'S008', 'S009', 'S010', 'S011', 'S012',
            'M01', 'M02', 'M03', 'M04'
        ];

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, 'TG' . $data['weighing_id']);

        $colIndex = "C";
        
        foreach($keys as $key) {
            $sheet->setCellValue($colIndex . $row, isset($data[$key]) ? $data[$key] : 0);
            $colIndex++;
        }
            
        // Membuat format menjadi number
        $sheet->getStyle("I" . $row . ":X" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->setCellValue("A" . $row, "TOTAL");

        for ($col = 'C'; $col <= 'X'; $col++) {
            $sheet->setCellValue($col . $row, "=SUM(" . $col . "6:" . $col . ($row - 1) . ")");
        }

        // Membuat Text Bold
        $sheet->getStyle("A" . $row . ":X" . $row)->getFont()->setBold(true);

        $row++;
        $sheet->mergeCells("A" . $row . ":X" . $row);
        $sheet->setCellValue("A" . $row, "=SUM(A" . ($row - 1) . ":X" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL");

            // Membuat Text Bold
        $sheet->getStyle("A" . $row . ":X" . $row)->getFont()->setBold(true);

        for ($col = 'C'; $col <= 'X'; $col++) {
            $sheet->setCellValue($col . $row, 0);
        }

        $row++;
        $sheet->mergeCells("A" . $row . ":X" . $row);
        $sheet->setCellValue("A" . $row, 0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":X" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":X" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("I" . $row . ":X" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A5:X" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Mitra Disimpan (Sebelum Dibulatkan)_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
