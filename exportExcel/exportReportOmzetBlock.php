<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$block	    = $_POST['block'];
	$sort	    = $_POST['sortir'];
	$date 		= date("d M Y");
    $total3     = 0;

    //Hitung Selisih Antara Hari Awal & Akhir
    $date3 = new DateTime($date1);
    $date4 = new DateTime($date2);
    $diff = $date3->diff($date4)->days + 1;

    // Hitung Luas Lapak
    $size 	= executeScalar("select sum(lapak_size) as size from lapak where block_id = '".$block."' ");
    

    // Query untuk mengambil data
    if($_POST['sortir']=='lapak'){
        $query 	= mysql_query("select r.merchant_id as id, m.merchant_name as name, 
                                count(w.weighing_id) as tot, l.block_id, sum(w.netto) as total
                                from merchant m, rent r, lapak l, weighing w
                                where r.lapak_id = l.lapak_id and
                                r.merchant_id = m.merchant_id and
                                w.merchant_id = m.merchant_id and 
                                (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') and
                                l.block_id = '".$block."' and
                                (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
                                w.weighing_sts != 'Batal' and
                                (w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')
                                group by id order by l.lapak_name");
    }
    elseif($_POST['sortir']=='tonase'){
        $query 	= mysql_query("select r.merchant_id as id, m.merchant_name as name, 
                                count(w.weighing_id) as tot, l.block_id, sum(w.netto) as total
                                from merchant m, rent r, lapak l, weighing w
                                where r.lapak_id = l.lapak_id and
                                r.merchant_id = m.merchant_id and
                                w.merchant_id = m.merchant_id and 
                                (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') and
                                l.block_id = '".$block."' and
                                (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59') and
                                w.weighing_sts != 'Batal' and
                                (w.weighing_sts2 != 'Dipecah' || w.weighing_sts2 != 'Digabung')
                                group by id order by total desc");
    }

    $query2 	= mysql_query("select m.merchant_id as id, m.merchant_name as name
                                from merchant m, rent r, lapak l
                                where m.merchant_id = r.merchant_id and
                                r.lapak_id = l.lapak_id and
                                m.merchant_status = 'Aktif' and 
                                (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') and
                                l.block_id = '".$block."'
                                group by id order by name");


    // Buat objek PHPExcel
    $excel = new PHPExcel();

    // ==================== SHEET 1 =====================
    $sheet1 = $excel->setActiveSheetIndex(0);
    $sheet1->setTitle("Pendapatan Tonase Block");

    // Menulis Informasi di Atas Tabel
    $sheet1->setCellValue("A1", "RINCIAN PENDAPATAN TONASE BLOCK " . $block);
    $sheet1->setCellValue("A3", "TANGGAL");
    $sheet1->setCellValue("A4", "DISUSUN BERDASARKAN");
    $sheet1->setCellValue("B3", " : ");
    $sheet1->setCellValue("B4", " : ");
    $sheet1->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));
    $sheet1->setCellValue("C4", $sort);

    // Membuat text bold
    $sheet1->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet1->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet1->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet1->mergeCells("A1:G1");

    // Menulis header
    $sheet1->setCellValue("A9", "NO")
          ->setCellValue("B9", "KODE PEDAGANG")
          ->setCellValue("C9", "NAMA PEDAGANG")
          ->setCellValue("D9", "LAPAK")
          ->setCellValue("E9", "TOTAL NOTA")
          ->setCellValue("F9", "RATA-RATA TONASE HARIAN (KG)")
          ->setCellValue("G9", "TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet1->getStyle("A9:G9")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom C (Pedagang)
    $sheet1->getStyle('A9:G9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 10;
    $netto2	    = 0;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $sql1 	= mysql_query("select l.lapak_name from rent r, lapak l
                                where r.lapak_id = l.lapak_id and r.merchant_id = '".$data['id']."' 
                                and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') order by l.lapak_name desc");

        $string	    = "";
        $counter    = 0;

        while($dataLapak=mysql_fetch_array($sql1)){
            if($counter!=0){
                $string = ", ".$string;
            }
            $string = $dataLapak['lapak_name'].$string;
            $counter++;
        }

        $lapak 	    = "".$string;
        $jml	= executeScalar("select count(l.lapak_id) as jml from rent r, lapak l
                                where r.lapak_id = l.lapak_id and 
                                r.merchant_id = '".$data['id']."' and
                                l.block_id = '".$block."' and
                                (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
                                order by l.lapak_name desc");
        $tot	= $data['tot']/$jml;
        $total	= $data['total']/$jml;
        $mean	= round($total/$diff);


        // Meratakan semua teks ke tengah, kecuali kolom C, D (Pedagang, Jenis)
        $sheet1->getStyle('A:G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom C, D (Pedagang, Jenis) ke kiri
        $sheet1->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
        $sheet1->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet1->setCellValue("A" . $row, $no)
               ->setCellValue("B" . $row, $data['id'])
               ->setCellValue("C" . $row, $data['name'])
               ->setCellValue("D" . $row, $lapak)
               ->setCellValue("E" . $row, $tot)
               ->setCellValue("F" . $row, $mean)
               ->setCellValue("G" . $row, $total);

        $netto2 = $netto2+$total;

        // Membuat format menjadi number
        $sheet1->getStyle("E" . $row . ":G" . $row)->getNumberFormat()->setFormatCode('#,##0');

        $no++;
        $row++;
    }


    // ==================== SHEET 2 =====================
    $sheet2 = $excel->createSheet();
    $sheet2->setTitle("Pedagang Tanpa Tonase Block");

    // Menulis Informasi di Atas Tabel
    $sheet2->setCellValue("A1", "PEDAGANG YANG TIDAK MEMILIKI TONASE");
    $sheet2->setCellValue("A3", "TANGGAL");
    $sheet2->setCellValue("A4", "DISUSUN BERDASARKAN");
    $sheet2->setCellValue("B3", " : ");
    $sheet2->setCellValue("B4", " : ");
    $sheet2->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));
    $sheet2->setCellValue("C4", $sort);

    // Membuat text bold
    $sheet2->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet2->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet2->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet2->mergeCells("A1:E1");

    // Menulis header
    $sheet2->setCellValue("A6", "NO")
           ->setCellValue("B6", "KODE PEDAGANG")
           ->setCellValue("C6", "NAMA PEDAGANG")
           ->setCellValue("D6", "LAPAK")
           ->setCellValue("E6", "TONASE (KG)");

    // Membuat Text Bold
    $sheet2->getStyle("A6:E6")->getFont()->setBold(true);
    // Meratakan semua teks ke tengah, kecuali kolom C, D (Pedagang, Lapak)
    $sheet2->getStyle('A6:E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no2	= 1;
    $row2   = 7;
    $total	= 0;

    // Looping untuk mengambil dan menuliskan data
    while ($data2 = mysql_fetch_array($query2)) {
        $total	= executeScalar("select sum(netto) from weighing where merchant_id = '".$data2['id']."' and
                                weighing_sts != 'Batal' and (date_check between '".$date1."' and '".$date2."')");
        
        $total = ($total == 0 || $total == '' || $total == NULL) ? 0 : $total;

        if($total==0||$total==''||$total==null){
            $sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
                                    where r.lapak_id = l.lapak_id and r.merchant_id = '".$data2['id']."'
                                    and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') order by l.lapak_name desc");
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

            // Meratakan semua teks ke tengah, kecuali kolom C, D (Pedagang, Jenis)
            $sheet2->getStyle('A:E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            // Set alignment khusus untuk kolom C, D (Pedagang, Jenis) ke kiri
            $sheet2->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $sheet2->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            // Menambahkan border
            $sheet2->getStyle("A6:E" . $row2)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    
            // Menulis data per baris
            $sheet2->setCellValue("A" . $row2, $no2)
                   ->setCellValue("B" . $row2, $data2['id'])
                   ->setCellValue("C" . $row2, $data2['name'])
                   ->setCellValue("D" . $row2, $lapak)
                   ->setCellValue("E" . $row2, $total);

            $no2++;
            $row2++;
        }
    }

    // Menulis total pada baris terakhir
    if ($row > 10) {
        $sheet1->mergeCells("A" . $row . ":D" . $row); 
        $sheet1->setCellValue("A" . $row, "TOTAL")
               ->setCellValue("E" . $row, "=SUM(E10:E" . ($row - 1) . ")")
               ->setCellValue("F" . $row, "=SUM(F10:F" . ($row - 1) . ")")
               ->setCellValue("G" . $row, "=SUM(G10:G" . ($row - 1) . ")");
    } else {
        $sheet1->mergeCells("A" . $row . ":D" . $row); 
        $sheet1->setCellValue("A" . $row, "TOTAL")
               ->setCellValue("E" . $row,  0)
               ->setCellValue("F" . $row,  0)
               ->setCellValue("G" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet1->getStyle("A" . $row . ":G" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet1->getStyle("A" . $row . ":G" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet1->getStyle("E" . $row . ":G" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet1->getStyle("A9:G" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet2->getStyle("A6:E" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set sheet active
    $excel->setActiveSheetIndex(0);

    // Menulis Informasi di Atas Tabel
    $total3 = $netto2/$size;
    $sheet1->setCellValue("A6", "LUAS BLOCK")
          ->setCellValue("A7", $size . ' M2')
          ->setCellValue("B6", "EFISIENSI LAPAK /M2")
          ->setCellValue("B7", $netto2 . ' / ' . $size. ' M2 = ' . $total3 . ' /M2' );

    // Membuat text bold
    $sheet1->getStyle("A6:B7")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet1->getStyle("A6:B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Menambahkan border pada seluruh tabel
    $sheet1->getStyle("A6:B7")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Tonase Block' . ' (' .$block. ') by ' .$sort. '_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
