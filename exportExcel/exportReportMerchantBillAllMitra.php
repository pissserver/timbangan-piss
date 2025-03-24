<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= '2016-10-01';
	$date2		= $_POST['date2'];
	$commodity	= $_POST['commodity'];
	$sort		= $_POST['sortir'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    if($commodity=='Semua'){
        $sql = "select 	m.merchant_id as id, 
                        m.merchant_name as name, 
                        m.merchant_type as type, 
                        SUM(cost) as total
                from 	merchant m, weighing_mitra w, rent r, lapak l
                where 	m.merchant_id = w.merchant_id and 
                        r.merchant_id = m.merchant_id and
                        r.lapak_id = l.lapak_id and
                        m.merchant_status = 'Aktif' and
                        (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
                        (w.date_pay > '".$date2."' || w.date_pay is NULL) and
                        w.weighing_sts != 'Batal' and
                        (w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')
                group by id order by ".$sort."";
    }else{
        $sql = "select 	m.merchant_id as id, 
                        m.merchant_name as name, 
                        m.merchant_type as type, 
                        SUM(cost) as total
                from 	merchant m, weighing_mitra w, rent r, lapak l
                where 	m.merchant_id = w.merchant_id and 
                        m.merchant_id = r.merchant_id and
                        r.lapak_id = l.lapak_id and
                        m.merchant_type = '".$commodity."' and 
                        m.merchant_status = 'Aktif' and
                        (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
                        (w.date_pay > '".$date2."' || w.date_pay is NULL) and
                        w.weighing_sts != 'Batal' and
                        (w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')
                group by id order by ".$sort."";
    }

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Belum Terbayar Semua Pedagang");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP TAGIHAN MITRA BELUM TERBAYAR SELURUH PEDAGANG");
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
    $sheet->mergeCells("A1:F1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "NAMA PEDAGANG")
          ->setCellValue("C6", "LAPAK")
          ->setCellValue("D6", "UMUR TAGIHAN")
          ->setCellValue("E6", "TOTAL NOTA")
          ->setCellValue("F6", "TOTAL (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:F6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom B, C (Pedagang, Lapak)
    $sheet->getStyle('A3:F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    $query = mysql_query($sql);
    while ($data = mysql_fetch_array($query)) {
        $sql2 	= mysql_query("	select 	l.lapak_name 
										from 	rent r, lapak l
										where 	r.lapak_id = l.lapak_id 
												and r.merchant_id = '".$data['id']."' 
												and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
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
		$total	= executeScalar("select sum(cost) as total 
										from 	weighing_mitra
										where 	merchant_id = '".$data['id']."' and
												(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
												(date_pay > '".$date2."' || date_pay is NULL) and
												weighing_sts != 'Batal' and
												(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");
				
		//Hitung Jumlah Nota
		$jml	= executeScalar("select count(weighing_id) 
										from 	weighing_mitra
										where 	merchant_id = '".$data['id']."' and
												(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
												(date_pay > '".$date2."' || date_pay is NULL) and
												weighing_sts != 'Batal' and
												(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung')");
				
		//Hitung Umur Tagihan
		$last_date	= executeScalar("select date_check 
											from 	weighing_mitra
											where 	merchant_id = '".$data['id']."' and
													(date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
													(date_pay > '".$date2."' || date_pay is NULL) and
													weighing_sts != 'Batal' and
													(weighing_sts2 != 'Dipecah' || weighing_sts2 != 'Digabung') order by weighing_id limit 1");
				
		$date3 	= new DateTime(date("Y-m-d", strtotime($last_date)));
		$date4 	= new DateTime(date("Y-m-d", strtotime($date2)));
		$diff 	= $date4->diff($date3)->format("%a");

        // Meratakan semua teks ke tengah, kecuali kolom B,C (Pedagang, lapak)
        $sheet->getStyle('A:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B,C (Pedagang, Lapak) ke kiri
        $sheet->getStyle('B:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, $data['name'])
              ->setCellValue("C" . $row, $lapak)
              ->setCellValue("D" . $row, $diff.' Hari')
              ->setCellValue("E" . $row, $jml)
              ->setCellValue("F" . $row, $total != NULL ? $total : 0);
            
        // Membuat format menjadi number
        $sheet->getStyle("E" . $row . ":F" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("E" . $row, "=SUM(E7:E" . ($row - 1) . ")")
              ->setCellValue("F" . $row, "=SUM(F7:F" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
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
    $sheet->getStyle("E" . $row . ":F" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:F" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Tagihan Mitra Belum Terbayar Seluruh Pedagang' . ' (' .$commodity. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
