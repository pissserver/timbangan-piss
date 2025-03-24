<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$mid		= $_POST['merchant'];
	$goods		= $_POST['goods'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    $sql = "select distinct(date(date_check)) as do from weighing 
            where merchant_id = '".$mid."' and 
            goods_id = '".$goods."' and 
            weighing_sts != 'Batal' and 
            (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')";

    $query = mysql_query($sql);
                                        
    $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$mid."'");
    $goods_name	= executeScalar("select goods_name from goods where goods_id = '".$goods."'");
    $sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
                            where r.lapak_id = l.lapak_id and 
                            r.merchant_id = '".$mid."' and
                            (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
                            order by l.lapak_name");
    $string	= "";
    $counter= 0;
    while($row=mysql_fetch_array($sql2)){
        if($counter!=0){
            $string = ", ".$string;
        }
        $string = $row['lapak_name'].$string;
        $counter++;
    }
    $lapak 	= "".$string;

    if($date1==$date2){
        $dateHeader = date("d M Y", strtotime($date1));
    }
    else{
        $dateHeader = date("d M Y", strtotime($date1)).' sd '.date("d M Y", strtotime($date2));
    }

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Tonase PerPedagang PerKomoditi");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP PENDAPATAN TONASE PER PEDAGANG & KOMODITI");

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
        
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
    // Merge cell
    $sheet->mergeCells("A1:C1");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A3", "Nama Pedagang ")
          ->setCellValue("A4", "Lapak         ")
          ->setCellValue("A5", "Komoditi      ")
          ->setCellValue("A6", "Tanggal       ")
          ->setCellValue("B3", ':')
          ->setCellValue("B4", ':')
          ->setCellValue("B5", ':')
          ->setCellValue("B6", ':')
          ->setCellValue("C3", $name.' ('.$mid.')' )
          ->setCellValue("C4", $lapak)
          ->setCellValue("C5", $goods_name)
          ->setCellValue("C6", $dateHeader);

    // Membuat text bold
    $sheet->getStyle("A3:C6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Menulis header
    $sheet->setCellValue("A8", "NO")
          ->setCellValue("B8", "TANGGAL")
          ->setCellValue("C8", "TOTAL (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A8:C8")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A8:C8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 9;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $tonase = executeScalar("select sum(netto) from weighing 
                                where merchant_id = '".$mid."' and 
                                goods_id = '".$goods."' and
                                weighing_sts != 'Batal' and 
                                (date_check between '".$data['do']." 00:00:00' and '".$data['do']." 23:59:59')");

        $tonase = ($tonase != 0) ? $tonase : 0;

        // Meratakan semua teks ke tengah, kecuali kolom G (Pedagang)
        $sheet->getStyle('A:C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom G (Pedagang) ke kiri
        // $sheet->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['do'])))
              ->setCellValue("C" . $row, $tonase);
        
        // Membuat format menjadi number
        $sheet->getStyle("C" . $row)->getNumberFormat()->setFormatCode('#,##0');

        $no++;
        $row++;
    }

    // Menulis total pada baris terakhir
    if ($row > 9) {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row, "=SUM(C9:C" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":F" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":C" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":C" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("C" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A8:C" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Pendapatan Tonase PerPedagang & Komoditi' . ' (' .$name. ')_' . date('Ymd') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
