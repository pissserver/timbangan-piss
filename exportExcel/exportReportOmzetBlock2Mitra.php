<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1	= '2016-10-01';
	$date2	= $_POST['date2'];
	$block	= $_POST['block'];
	$date 	= date("d M Y");

    // Query untuk mengambil data
    $query = mysql_query("	select 	m.merchant_id as id, 
                                m.merchant_name as name
                            from 	merchant m, rent r, lapak l
                            where 	m.merchant_id = r.merchant_id and
                                r.lapak_id = l.lapak_id and
                                (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') and 
                                l.block_id = '".$block."'
                            group by id order by name");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Mitra Belum Terbayar PerBlock");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP TAGIHAN MITRA BELUM TERBAYAR BLOCK " . $block);
    $sheet->setCellValue("A3", "TANGGAL AKHIR TAGIHAN");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:F1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "NAMA PEDAGANG")
          ->setCellValue("C5", "LAPAK")
          ->setCellValue("D5", "TOTAL NOTA")
          ->setCellValue("E5", "UMUR TAGIHAN")
          ->setCellValue("F5", "TOTAL (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:I5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A5:F5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom C (Pedagang)
    $sheet->getStyle('A5:F5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $sql2 	= mysql_query("	select	l.lapak_name 
										from 	rent r, lapak l
										where 	r.lapak_id = l.lapak_id 
												and r.merchant_id = '".$data['id']."' 
												and l.block_id = '".$block."' and
												(r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
										order by l.lapak_name desc");
		$string	= "";
    	$counter= 0;
		while($rowData=mysql_fetch_array($sql2)){
			if($counter!=0){
				$string = ", ".$string;
			}
			$string = $rowData['lapak_name'].$string;
			$counter++;
		}
		$lapak 	= "".$string;

        $jml	= executeScalar("select count(weighing_id) 
										from weighing_mitra
										where merchant_id = '".$data['id']."' and
										(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
										(date_pay > '".$date2."' || date_pay is NULL) and
										weighing_sts != 'Batal' and
										(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");
					
		$total	= executeScalar("select sum(cost) 
										from 	weighing_mitra
										where 	merchant_id = '".$data['id']."' and
												(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
												(date_pay > '".$date2."' || date_pay is NULL) and
												weighing_sts != 'Batal' and
												(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");
					
        $diff = 0;
		if($jml>0){
		    $last_date	= executeScalar("select date_check 
												from 	weighing_mitra
												where 	merchant_id = '".$data['id']."' and
														(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
														(date_pay > '".$date2."' || date_pay is NULL) and
														weighing_sts != 'Batal' and
														(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung') order by weighing_id limit 1");
			//Hitung Umur Tagihan
			$date3 	= new DateTime(date("Y-m-d", strtotime($last_date)));
			$date4 	= new DateTime(date("Y-m-d", strtotime($date2)));
			$diff 	= $date4->diff($date3)->format("%a");
		}

        // Meratakan semua teks ke tengah, kecuali kolom B, C (Pedagang, Lapak)
        $sheet->getStyle('A:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B, C (Pedagang, Lapak) ke kiri
        $sheet->getStyle('B:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, $data['name'])
              ->setCellValue("C" . $row, $lapak)
              ->setCellValue("D" . $row, $jml)
              ->setCellValue("E" . $row, $diff.' Hari')
              ->setCellValue("F" . $row, $total != NULL ? $total : 0);
            
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
        $sheet->mergeCells("A" . $row . ":G" . $row); 
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
    header('Content-Disposition: attachment;filename="Rekap Tagihan Mitra Belum Terbayar Block' . ' (' .$block. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
