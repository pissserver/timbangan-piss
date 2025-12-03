<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	    $year 		= date("y");
		$date_now 		= date("d M Y");
		$date		= $_GET['date'];
		$work_date	= $_GET['date'];	
		
		$total_share		= executeScalar("select sum(fee_B001)+
											sum(fee_B002)+
											sum(fee_B003)+
											sum(fee_B004)+
											sum(fee_B005)+
											sum(fee_B006)+
											sum(fee_S001)+
											sum(fee_S002)+
											sum(fee_S003)+
											sum(fee_S004)+
											sum(fee_S005)+
											sum(fee_S006)+
											sum(fee_S007)+
											sum(fee_S008)+
											sum(fee_S009)+
											sum(fee_S010)+
											sum(fee_S011)+
											sum(fee_S012)+
											sum(fee_M01)+
											sum(fee_M02)+
											sum(fee_M03)+
											sum(fee_M04) 
									from laborer_fee2 
									where fee_date = '".$date."'");

			// Lakukan query untuk mendapatkan kode pekerja
			$result = mysql_query("SELECT laborer_code FROM laborer WHERE laborer_name != '-'");

			// Pastikan hasil tidak kosong
			if ($result) {
				$fees = [];
				$names = [];

				// Mengambil semua kode pekerja dan menyimpannya dalam array
				while ($row = mysql_fetch_assoc($result)) {
					$code = $row['laborer_code'];

					// Periksa kode sesuai dengan kriteria yang ditentukan
					if ((strpos($code, 'B') === 0 && $code <= 'B006') || 
						(strpos($code, 'S') === 0 && $code <= 'S012') || 
						(strpos($code, 'M') === 0 && $code <= 'M04')) {
						
						// Mengambil total fee dari laborer_fee2 berdasarkan kode dan tanggal
						$fee = executeScalar("SELECT SUM(fee_$code) FROM laborer_fee2 WHERE DATE(fee_date) = '".$date."'");

						// Mengambil nama pekerja berdasarkan laborer_code
						$name = executeScalar("SELECT laborer_name FROM laborer WHERE laborer_code = '$code'");

						// Menyimpan fee dan nama ke dalam array
						$fees[$code] = $fee;
						$names[$code] = $name;
					}
				}
			} else {
				// Tangani jika query gagal
				echo "Error: " . mysql_error();
			}

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Pengembalian Upah Mitra");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "PENGEMBALIAN UPAH MITRA");
    $sheet->setCellValue("A3", "TANGGAL KERJA");
    $sheet->setCellValue("A4", "TOTAL UANG");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", date("d M Y", strtotime($work_date)));
    $sheet->setCellValue("C4", format_rupiah($total_share));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A3:A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    // Merge cell
    $sheet->mergeCells("A1:J1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "NAMA (KODE)")
          ->setCellValue("C6", "JUMLAH (RP)")
          ->setCellValue("D6", "TTD");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:D6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // ===== SORTING KODE B → S → M (PHP 5 compatible) =====
			uksort($fees, function($a, $b) {
				$prefixA = $a[0];
				$prefixB = $b[0];

				// Urutan grup
				$order = ['B' => 1, 'S' => 2, 'M' => 3];

				// Kalau salah satu prefix tidak dikenal → anggap sama
				if (!isset($order[$prefixA]) || !isset($order[$prefixB])) {
					return 0;
				}

				// Jika prefix berbeda → urutkan berdasarkan grup
				if ($prefixA !== $prefixB) {
					if ($order[$prefixA] == $order[$prefixB]) return 0;
					return ($order[$prefixA] < $order[$prefixB]) ? -1 : 1;
				}

				// Jika prefix sama → urutkan berdasarkan angka
				$numA = (int)substr($a, 1);
				$numB = (int)substr($b, 1);

				if ($numA == $numB) return 0;
				return ($numA < $numB) ? -1 : 1;
			});

			uksort($names, function($a, $b) {
				$prefixA = $a[0];
				$prefixB = $b[0];

				$order = ['B' => 1, 'S' => 2, 'M' => 3];

				// Kalau salah satu prefix tidak dikenal → anggap sama
				if (!isset($order[$prefixA]) || !isset($order[$prefixB])) {
					return 0;
				}

				if ($prefixA !== $prefixB) {
					if ($order[$prefixA] == $order[$prefixB]) return 0;
					return ($order[$prefixA] < $order[$prefixB]) ? -1 : 1;
				}

				$numA = (int)substr($a, 1);
				$numB = (int)substr($b, 1);

				if ($numA == $numB) return 0;
				return ($numA < $numB) ? -1 : 1;
			});

    // Looping untuk mengambil dan menuliskan data
    foreach ($fees as $code => $amount) {
        $name = isset($names[$code]) ? $names[$code] : 'Nama Tidak Ditemukan';

        $sheet->getStyle('A:D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $sheet->setCellValue("A" . $row, $no)
            ->setCellValue("B" . $row, $name . " (" . $code . ")")
            ->setCellValue("C" . $row, $amount)
            ->setCellValue("D" . $row, '');

        $sheet->getStyle("C" . $row)->getNumberFormat()->setFormatCode('#,##0');
        
        $no++;
        $row++;
    }

    // Menulis total pada baris terakhir
    if ($row > 7) {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->mergeCells("C" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row, "=SUM(C7:C" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->mergeCells("C" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":D" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":D" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("C" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:D" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Mitra Dibagi_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
