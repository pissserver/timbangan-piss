<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$commodity	= $_POST['type'];
	$sort		= $_POST['sortir'];
	$date 		= date("d M Y");

    //Hitung Selisih Antara Hari Awal & Akhir
    $date3 = new DateTime($date1);
    $date4 = new DateTime($date2);
    $diff = $date3->diff($date4)->days + 1;
    

    // Query untuk mengambil data
    if($commodity=='Semua'){
		$sql = "select m.merchant_id as id, m.merchant_name as name, m.merchant_type as type, 
		count(w.weighing_id) as tot, sum(w.netto) as total
		from merchant m, weighing w
		where w.merchant_id = m.merchant_id and
		w.weighing_sts != 'Batal' and
		(w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')
		group by id order by ".$sort."";
	}else{
		$sql = "select m.merchant_id as id, m.merchant_name as name, m.merchant_type as type,
		count(w.weighing_id) as tot, sum(w.netto) as total
		from merchant m, weighing w
		where w.merchant_id = m.merchant_id and
		m.merchant_type = '".$_POST['type']."' and
		w.weighing_sts != 'Batal' and
		(w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')
		group by id order by ".$sort."";
	}

    if($commodity=='Semua'){
        $sql3 = "select merchant_id as id, merchant_name as name, merchant_type as type
        from merchant where merchant_status = 'Aktif' and merchant_type != 'Lainnya'
        order by name";
    }else{
        $sql3 = "select merchant_id as id, merchant_name as name, merchant_type as type
        from merchant where merchant_type = '".$_POST['type']."' 
        and merchant_status = 'Aktif' order by name";
    }

    $query 	= mysql_query($sql);
    $query2 	= mysql_query($sql3);

    // Buat objek PHPExcel
    $excel = new PHPExcel();

    // ==================== SHEET 1 =====================
    $sheet1 = $excel->setActiveSheetIndex(0);
    $sheet1->setTitle("Tonase Seluruh Pedagang");

    // Menulis Informasi di Atas Tabel
    $sheet1->setCellValue("A1", "REKAP PENDAPATAN TONASE SELURUH PEDAGANG");
    $sheet1->setCellValue("A3", "JENIS KOMODITI");
    $sheet1->setCellValue("A4", "TANGGAL");
    $sheet1->setCellValue("B3", " : ");
    $sheet1->setCellValue("B4", " : ");
    $sheet1->setCellValue("C3", $commodity);
    $sheet1->setCellValue("C4", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet1->getStyle("A1:B4")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet1->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet1->mergeCells("A1:H1");

    // Menulis header
    $sheet1->setCellValue("A6", "NO")
          ->setCellValue("B6", "KODE PEDAGANG")
          ->setCellValue("C6", "NAMA PEDAGANG")
          ->setCellValue("D6", "JENIS PEDAGANG")
          ->setCellValue("E6", "LAPAK")
          ->setCellValue("F6", "JUMLAH TIMBANG")
          ->setCellValue("G6", "RATA-RATA TONASE HARIAN (KG)")
          ->setCellValue("H6", "TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet1->getStyle("A6:H6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom C (Pedagang)
    $sheet1->getStyle('A6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
                                where r.lapak_id = l.lapak_id and r.merchant_id = '".$data['id']."' 
                                and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') order by l.lapak_name desc");

        $string	    = "";
        $counter    = 0;

        while($dataLapak=mysql_fetch_array($sql2)){
            if($counter!=0){
                $string = ", ".$string;
            }
            $string = $dataLapak['lapak_name'].$string;
            $counter++;
        }

        $lapak 	    = "".$string;
        $mean       = round($data['total'] / $diff);


        // Meratakan semua teks ke tengah, kecuali kolom C, E (Pedagang, Jenis)
        $sheet1->getStyle('A:H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom C, E (Pedagang, Jenis) ke kiri
        $sheet1->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet1->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet1->setCellValue("A" . $row, $no)
               ->setCellValue("B" . $row, $data['id'])
               ->setCellValue("C" . $row, $data['name'])
               ->setCellValue("D" . $row, $data['type'])
               ->setCellValue("E" . $row, $lapak)
               ->setCellValue("F" . $row, $data['tot'])
               ->setCellValue("G" . $row, $mean)
               ->setCellValue("H" . $row, $data['total']);

        // Membuat format menjadi number
        $sheet1->getStyle("F" . $row . ":H" . $row)->getNumberFormat()->setFormatCode('#,##0');

        $no++;
        $row++;
    }


    // ==================== SHEET 2 =====================
    $sheet2 = $excel->createSheet();
    $sheet2->setTitle("Tanpa Tonase Seluruh Pedagang");

    // Menulis Informasi di Atas Tabel
    $sheet2->setCellValue("A1", "PEDAGANG YANG TIDAK MEMILIKI TONASE");
    $sheet2->setCellValue("A3", "JENIS PEDAGANG");
    $sheet2->setCellValue("A4", "TANGGAL");
    $sheet2->setCellValue("B3", " : ");
    $sheet2->setCellValue("B4", " : ");
    $sheet2->setCellValue("C3", $commodity);
    $sheet2->setCellValue("C4", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));


    // Membuat text bold
    $sheet2->getStyle("A1:B4")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet2->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet2->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet2->mergeCells("A1:F1");

    // Menulis header
    $sheet2->setCellValue("A6", "NO")
           ->setCellValue("B6", "KODE PEDAGANG")
           ->setCellValue("C6", "NAMA PEDAGANG")
           ->setCellValue("D6", "JENIS PEDAGANG")
           ->setCellValue("E6", "LAPAK")
           ->setCellValue("F6", "TONASE (KG)");

    // Membuat Text Bold
    $sheet2->getStyle("A6:F6")->getFont()->setBold(true);
    // Meratakan semua teks ke tengah, kecuali kolom C (Pedagang)
    $sheet2->getStyle('A6:F6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no2	= 1;
    $row2   = 7;
    $total	= 0;

    // Looping untuk mengambil dan menuliskan data
    while ($data2 = mysql_fetch_array($query2)) {
        $total	= executeScalar("select sum(netto) from weighing where merchant_id = '".$data2['id']."' and
                                weighing_sts != 'Batal' and (date_check between '".$date1." 00:00:01' and '".$date2." 23:59:59')");
        $total = ($total == 0 || $total == '' || $total == NULL) ? 0 : $total;

        if($total==0||$total==''||$total==null){
            $sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
                    where r.lapak_id = l.lapak_id and r.merchant_id = '".$data2['id']."' 
                    and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') order by l.lapak_name");
            $string	= "";
            $counter= 0;
            while($dataLapak=mysql_fetch_array($sql2)){
                if($counter!=0){
                    $string = ", ".$string;
                }
                $string = $dataLapak['lapak_name'].$string;
                $counter++;
            }
            $lapak 	= "".$string;

            // Meratakan semua teks ke tengah, kecuali kolom C, E (Pedagang, Jenis)
            $sheet2->getStyle('A:F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            // Set alignment khusus untuk kolom C, E (Pedagang, Jenis) ke kiri
            $sheet2->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet2->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            // Menambahkan border
            $sheet2->getStyle("A6:F" . $row2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
            // Menulis data per baris
            $sheet2->setCellValue("A" . $row2, $no2)
                   ->setCellValue("B" . $row2, $data2['id'])
                   ->setCellValue("C" . $row2, $data2['name'])
                   ->setCellValue("D" . $row2, $data2['type'])
                   ->setCellValue("E" . $row2, $lapak)
                   ->setCellValue("F" . $row2, $total);

            // Membuat format menjadi number
            $sheet2->getStyle("F" . $row)->getNumberFormat()->setFormatCode('#,##0');

            $no2++;
            $row2++;
        }
    }

    // Menulis total pada baris terakhir
    if ($row > 7) {
        $sheet1->mergeCells("A" . $row . ":E" . $row); 
        $sheet1->setCellValue("A" . $row, "TOTAL")
               ->setCellValue("F" . $row, "=SUM(F7:F" . ($row - 1) . ")")
               ->setCellValue("G" . $row, "=SUM(G7:G" . ($row - 1) . ")")
               ->setCellValue("H" . $row, "=SUM(H7:H" . ($row - 1) . ")");
    } else {
        $sheet1->mergeCells("A" . $row . ":E" . $row); 
        $sheet1->setCellValue("A" . $row, "TOTAL")
               ->setCellValue("F" . $row,  0)
               ->setCellValue("G" . $row,  0)
               ->setCellValue("H" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet1->getStyle("A" . $row . ":H" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet1->getStyle("A" . $row . ":H" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet1->getStyle("F" . $row . ":H" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet1->getStyle("A6:H" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet2->getStyle("A6:F" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set sheet active
    $excel->setActiveSheetIndex(0);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Pendapatan Tonase Seluruh Pedagang' . ' (' .$commodity. ') by ' .$sort. '_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
