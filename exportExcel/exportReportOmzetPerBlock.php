<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 

    // Load library PHPExcel
    $month1 = $_POST['month1'];
    $month1 = intval($month1);
    $month2 = $_POST['month2'];
    $month2 = intval($month2);
    $year1 = $_POST['year1'];
    $year1 = intval($year1);
    $year2 = $_POST['year2'];
    $year2 = intval($year2);
    $block = $_POST['block'];

    // Hitung jumlah bulan
    $month3 = 0; 
    $month3 = intval($month2 - $month1 + 1);
    $year3 = intval($year2 - $year1);
    $monthTH  = $month1;
    $monthName  = '';
    $yearTH = $year1;
    $year = 0;

    $totalID 	= executeScalar("SELECT COUNT(DISTINCT m.merchant_id)
                                FROM merchant m
                                JOIN rent r ON m.merchant_id = r.merchant_id
                                JOIN lapak l ON r.lapak_id = l.lapak_id
                                WHERE r.rent_sts2 NOT IN ('Pindah', 'Batal')
                                AND l.block_id = '".$block."'
                                ");

    if ($year3 >= 1) {
        $year = intval(12 * $year3);
        $month3 = $month3 + $year;
    }

    // Format nama bulan
    if ($month1 == $month2 && $year1 == $year2) {
        $monthName = date('F', mktime(0, 0, 0, $month1, 1, $year1));
        $distanceMonth = sprintf('%s %d', $monthName, $year1);
    } else {
        $monthName1 = date('F', mktime(0, 0, 0, $month1, 1, $year1));
        $monthName2 = date('F', mktime(0, 0, 0, $month2, 1, $year2));
        $distanceMonth = sprintf('%s %d sd %s %d', $monthName1, $year1, $monthName2, $year2);
    }

    // Query untuk mengambil data
    $query 	= mysql_query("select m.merchant_id as id, m.merchant_name as name
                            from merchant m, rent r, lapak l
                            where m.merchant_id = r.merchant_id and
                            r.lapak_id = l.lapak_id and
                            (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') and
                            l.block_id = '".$block."'
                            group by id order by l.lapak_name");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Tonase Bulanan Block");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A3", "Bulan")
        ->setCellValue("A4", "Jumlah Bulan")
        ->setCellValue("A5", "Jumlah Pedagang")
        ->setCellValue("B3", ':')
        ->setCellValue("B4", ':')
        ->setCellValue("B5", ':')
        ->setCellValue("C3", $distanceMonth)
        ->setCellValue("C4", $month3 . ' Bulan')
        ->setCellValue("C5", $totalID . ' Pedagang');

    // Membuat text bold
    $sheet->getStyle("A3:B5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Menulis header
    $monthName = date('M', mktime(0, 0, 0, $month1, 1, $year1));
    $sheet->setCellValue("A7", "NO")
        ->setCellValue("B7", "NAMA PEDAGANG")
        ->setCellValue("C7", "LAPAK")
        ->setCellValue("D7", $monthName." ".$yearTH);

    $columnIndex = 4; // Mulai dari kolom D
    $alphabet = range('A', 'Z'); // Menangani kolom A-Z
    $startRow = 7;
    $noHeader = 1;

    while($noHeader < $month3){
        $monthTH = $monthTH + 1;
        if($monthTH > 12){
            $monthTH = $monthTH - 12;
            $yearTH = $yearTH + 1;
        }
        $monthName = date('M', mktime(0, 0, 0, $monthTH, 10));

        // Menentukan kolom berdasarkan indeks
        $column = $alphabet[$columnIndex % 26]; 
        if ($columnIndex >= 26) {
            $column = $alphabet[intdiv($columnIndex, 26) - 1] . $alphabet[$columnIndex % 26]; 
        }

        // Menulis bulan pada koordinat yang valid, misalnya D5, E5, dst.
        $sheet->setCellValue($column . $startRow, $monthName . " " . $yearTH);

        $columnIndex++;
        $noHeader++;
    }

    $totalColumn = $alphabet[$columnIndex % 26];
    if ($columnIndex >= 26) {
        $totalColumn = $alphabet[intdiv($columnIndex, 26) - 1] . $alphabet[$columnIndex % 26];
    }
    $sheet->setCellValue($totalColumn . '7', "TOTAL (KG)");

    // Membuat Text Bold
    $sheet->getStyle("A7:" . $totalColumn . '7')->getFont()->setBold(true);
    $sheet->getStyle('A7:' . $totalColumn . '7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no = 1;
    $row = 8;

    while ($data = mysql_fetch_array($query)) {
        $monthLoop = $month1;
        $yearLoop = $year1;
        $total1 = 0;
        $totalLoop = 0;
        $columnResult = 4;

        $sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
                                where r.lapak_id = l.lapak_id and 
                                r.merchant_id = '".$data['id']."' and
                                l.block_id = '".$block."' and
                                (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
                                order by l.lapak_name desc");
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

        // Mendapatkan data tonase
        $tonase1 = executeScalar("select sum(netto) from weighing 
                                where merchant_id = '".$data['id']."' and 
                                weighing_sts != 'Batal' and 
                                month(date_check) = '".$month1."' and 
                                year(date_check) = '".$year1."'");

        $tonase1 = ($tonase1 != NULL) ? $tonase1 : 0;
        $total1 = $total1 + $tonase1;

        $column = $alphabet[$columnResult % 26];
        if ($columnResult >= 26) {
            $column = $alphabet[intdiv($columnResult, 26) - 1] . $alphabet[$columnResult % 26];
        }
        // Meratakan semua teks ke tengah
        $sheet->getStyle('A:' . $column)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B (Pedagang) ke kiri
        $sheet->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
            ->setCellValue("B" . $row, $data['name'])
            ->setCellValue("C" . $row, $lapak)
            ->setCellValue("D" . $row, $tonase1);

        $count = 1;
        $columnResult = 4; // Mulai dari kolom D

        while($count < $month3){
            $monthLoop = $monthLoop + 1;
            if($monthLoop > 12){
                $monthLoop = $monthLoop - 12;
                $yearLoop  = $yearLoop + 1;
            }
            

            $tonaseLoop = executeScalar("select sum(netto) from weighing 
                                        where merchant_id = '".$data['id']."' and 
                                        weighing_sts != 'Batal' and 
                                        month(date_check) = '".$monthLoop."' and 
                                        year(date_check) = '".$yearLoop."'");

            $tonaseLoop = ($tonaseLoop != NULL) ? $tonaseLoop : 0;

            // Menulis tonase pada kolom yang sesuai
            $column = $alphabet[$columnResult % 26];
            if ($columnResult >= 26) {
                $column = $alphabet[intdiv($columnResult, 26) - 1] . $alphabet[$columnResult % 26];
            }

            $sheet->getStyle("A" . $row . ":" . $column . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            $sheet->setCellValue($column . $row, $tonaseLoop);
            $totalLoop = $totalLoop + $tonaseLoop;

            $columnResult++;
            $count++;
        }

        $lastColumnIndex = $columnResult - 1;
        $lastColumn = $alphabet[$lastColumnIndex % 26]; 
        $totalColumn = $alphabet[$columnIndex % 26];

        if ($columnIndex >= 26 && $lastColumnIndex >= 26) {
            $totalColumn = $alphabet[intdiv($columnIndex, 26) - 1] . $alphabet[$columnIndex % 26];
            $lastColumn = $alphabet[intdiv($lastColumnIndex, 26) - 1] . $alphabet[$lastColumnIndex % 26];
        }
        
        // Meratakan semua teks ke tengah
        $sheet->getStyle($totalColumn)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Menuliskan formula SUM yang benar di kolom total
        $sheet->setCellValue($totalColumn . $row, "=SUM(D" . $row . ":" . $lastColumn . $row . ")");

        // Membuat format menjadi number
        $sheet->getStyle("D" . $row . ":" . $totalColumn . $row)->getNumberFormat()->setFormatCode('#,##0');


        $no++;
        $row++;
    }

    if ($row > 8) {
        // Menghitung total semua kolom dinamis
        $sheet->mergeCells("A" . $row . ":C" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("D" . $row, "=SUM(D8:D" . ($row - 1) . ")");

        $sheet->getStyle("A" . $row)->getFont()->setBold(true);
        
        $columnTotal = 4; // Mulai dari kolom D
        while ($columnTotal <= $columnResult) { // Loop sampai ke kolom terakhir
            $column = $alphabet[$columnTotal % 26];
            if ($columnTotal >= 26) {
                $column = $alphabet[intdiv($columnTotal, 26) - 1] . $alphabet[$columnTotal % 26];
            }
            $sheet->setCellValue($column . $row, "=SUM(" . $column . "8:" . $column . ($row - 1) . ")");
            $columnTotal++;
        }
    } else {
        // Menghitung total semua kolom dinamis
        $sheet->mergeCells("A" . $row . ":C" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
            ->setCellValue("D" . $row, 0);

        $sheet->getStyle("A" . $row)->getFont()->setBold(true);

        $columnTotal = 4; // Mulai dari kolom D
        while ($columnTotal <= $columnResult) { // Loop sampai ke kolom terakhir
            $column = $alphabet[$columnTotal % 26];
            if ($columnTotal >= 26) {
                $column = $alphabet[intdiv($columnTotal, 26) - 1] . $alphabet[$columnTotal % 26];
            }
            $sheet->setCellValue($column . $row, 0);
            $columnTotal++;
        }
    }

    $sheet->getStyle("A" . $row . ":" . $column . $row)->getFont()->setBold(true);
    $sheet->getStyle("A" . $row . ":" . $column . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("D" . $row . ":" . $column . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A7:" . $column . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN PENDAPATAN TONASE BULANAN BLOCK " . $block);

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:" . $totalColumn . '1');

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Tonase Bulanan Block' . ' (' .$block. ')_'  . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
