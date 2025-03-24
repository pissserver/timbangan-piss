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
    $query 	= mysql_query("select * from weighing 
                            where weighing_sts != 'Batal' and merchant_id = '".$mid."' and goods_id = '".$goods."' and 
                            (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')");
                                        
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
    $sheet->setCellValue("A1", "RINCIAN PENDAPATAN TONASE PER PEDAGANG & KOMODITI");

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:L1");

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
    $sheet->getStyle("A3:B6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Menulis header
    $sheet->setCellValue("A8", "NO")
          ->setCellValue("B8", "TANGGAL KELUAR")
          ->setCellValue("C8", "NOTA TIMBANG")
          ->setCellValue("D8", "NOTA TAGIHAN")
          ->setCellValue("E8", "NOPOL")
          ->setCellValue("F8", "STATUS MITRA")
          ->setCellValue("G8", "STATUS NOTA")
          ->setCellValue("H8", "BERAT MASUK (KG)")
          ->setCellValue("I8", "BERAT KELUAR (KG)")
          ->setCellValue("J8", "NONKOMODITI (KG)")
          ->setCellValue("K8", "RAFAKSI (KG)")
          ->setCellValue("L8", "TONASE (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A8:L8")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A8:L8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 9;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $goods	= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");

        if($data['reffer']=='null'||$data['reffer']==''){
            $weighing_id = 'TB'.$data['weighing_id'];
        }else{
            $weighing_id = 'TB'.$data['reffer'];
        }

        $data['weight_in'] = ($data['weight_in'] != 0) ? $data['weight_in'] : 0;
        $data['weight_out'] = ($data['weight_out'] != 0) ? $data['weight_out'] : 0;
        $data['noncommodity'] = ($data['noncommodity'] != 0) ? $data['noncommodity'] : 0;
        $data['rafaksi'] = ($data['rafaksi'] != 0) ? $data['rafaksi'] : 0;
        $data['netto'] = ($data['netto'] != 0) ? $data['netto'] : 0;

        // Meratakan semua teks ke tengah, kecuali kolom G (Pedagang)
        $sheet->getStyle('A:L')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom G (Pedagang) ke kiri
        // $sheet->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_out'])))
              ->setCellValue("C" . $row, $weighing_id)
              ->setCellValue("D" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("E" . $row, $data['nopol'])
              ->setCellValue("F" . $row, $data['laborer_sts'])
              ->setCellValue("G" . $row, $data['weighing_sts2'])
              ->setCellValue("H" . $row, $data['weight_in'])
              ->setCellValue("I" . $row, $data['weight_out'])
              ->setCellValue("J" . $row, $data['noncommodity'])
              ->setCellValue("K" . $row, $data['rafaksi'])
              ->setCellValue("L" . $row, $data['netto']);
        

        // Membuat format menjadi number
        $sheet->getStyle("H" . $row . ":L" . $row)->getNumberFormat()->setFormatCode('#,##0');

        $no++;
        $row++;
    }

    // Menulis total pada baris terakhir
    if ($row > 9) {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("H" . $row, "=SUM(H9:H" . ($row - 1) . ")")
              ->setCellValue("I" . $row, "=SUM(I9:I" . ($row - 1) . ")")
              ->setCellValue("J" . $row, "=SUM(J9:J" . ($row - 1) . ")")
              ->setCellValue("K" . $row, "=SUM(K9:K" . ($row - 1) . ")")
              ->setCellValue("L" . $row, "=SUM(L9:L" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":F" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("H" . $row,  0)
              ->setCellValue("I" . $row,  0)
              ->setCellValue("J" . $row,  0)
              ->setCellValue("K" . $row,  0)
              ->setCellValue("L" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":L" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":L" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("H" . $row . ":L" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A8:L" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Tonase PerPedagang & Komoditi' . ' (' .$name. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
