<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1	 = $_POST['date1'];
	$date2	 = $_POST['date2'];
	$dateNow = date("d M Y");

    // Query untuk mengambil data
    $query 	= mysql_query("	select 	fee_id, 
									sum(save_B001) as B001, 
									sum(save_B002) as B002, 
									sum(save_B003) as B003, 
									sum(save_B004) as B004, 
									sum(save_B005) as B005, 
									sum(save_B006) as B006, 
									sum(save_S001) as S001, 
									sum(save_S002) as S002, 
									sum(save_S003) as S003, 
									sum(save_S004) as S004, 
									sum(save_S005) as S005, 
									sum(save_S006) as S006, 
									sum(save_S007) as S007, 
									sum(save_S008) as S008, 
									sum(save_S009) as S009,
									sum(save_S010) as S010,
									sum(save_S011) as S011,
									sum(save_S012) as S012,
									sum(save_M01) as M01,
									sum(save_M02) as M02,
									sum(save_M03) as M03,
									sum(save_M04) as M04, 
									fee_date as date
							from 	laborer_fee2 
							where 	fee_date between '".$date1."' and '".$date2."'
							group by date");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Rekap Mitra Dibagi (Setelah)");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN PENDAPATAN MITRA DISIMPAN (SETELAH DIBULATKAN)");
    $sheet->setCellValue("A3", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);
        
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:Y1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "TANGGAL")
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
          ->setCellValue("X5", "M04")
          ->setCellValue("Y5", "TOTAL (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:I5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A5:Y5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A5:Y5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
        $sheet->getStyle('A:Y')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        // $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $keys = [
            'B001', 'B002', 'B003', 'B004', 'B005', 'B006',
            'S001', 'S002', 'S003', 'S004', 'S005', 'S006', 'S007', 'S008', 'S009', 'S010', 'S011', 'S012',
            'M01', 'M02', 'M03', 'M04'
        ];

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date('d M Y', strtotime($data['date'])));

        $colIndex = "C";
        
        foreach($keys as $key) {
            $sheet->setCellValue($colIndex . $row, isset($data[$key]) ? $data[$key] : 0);
            
            $colIndex++;
            $colIndexEnd = $colIndex;
        }

        $sheet->setCellValue("Y" . $row, "=SUM(C" . $row . ":X" . $row . ")");
            
        // Membuat format menjadi number
        $sheet->getStyle("I" . $row . ":Y" . $row)->getNumberFormat()->setFormatCode('#,##0');

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

        $sheet->setCellValue("Y" . $row, "=SUM(C" . $row . ":X" . $row . ")");

    } else {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL");

            // Membuat Text Bold
        $sheet->getStyle("A" . $row . ":X" . $row)->getFont()->setBold(true);

        for ($col = 'C'; $col <= 'X'; $col++) {
            $sheet->setCellValue($col . $row, 0);
        }
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":Y" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":Y" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("I" . $row . ":Y" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A5:Y" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Pendapatan Mitra Dibagikan (Setelah Dibulatkan)_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
