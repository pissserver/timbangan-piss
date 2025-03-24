<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
    $mid	= $_POST['merchant'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    $query 	= mysql_query("select 	weighing_id, 
											goods_id, 
											reffer, 
											date_out,
											date_pay,
											date_check, 
											nopol, 
											weighing_sts2, 
											weight_in, 
											weight_out, 
											noncommodity,
											netto,
											cost_bayar, 
											cost,
											ko_id 
									from 	weighing_mitra 
									where 	weighing_sts = 'Lunas' and 
											merchant_id = '".$mid."' and 
											(date_pay between '".$date1."' and '".$date2."')");
                                        
    $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$mid."'");
    $jenis	= executeScalar("select merchant_type from merchant where merchant_id = '".$mid."'");
    $sql2 	= mysql_query("	select 	l.lapak_name 
                            from 	rent r, lapak l
                            where 	r.lapak_id = l.lapak_id 
                            and r.merchant_id = '".$mid."' 
                            and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
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

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Mitra Terbayar PerPedagang");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN TAGIHAN MITRA TERBAYAR PER PEDAGANG");

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:I1");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A3", "Nama Pedagang ")
          ->setCellValue("A4", "Jenis Pedagang")
          ->setCellValue("A5", "Lapak         ")
          ->setCellValue("A6", "TANGGAL      ")
          ->setCellValue("B3", ':')
          ->setCellValue("B4", ':')
          ->setCellValue("B5", ':')
          ->setCellValue("B6", ':')
          ->setCellValue("C3", $name.' ('.$mid.')' )
          ->setCellValue("C4", $jenis)
          ->setCellValue("C5", $lapak)
          ->setCellValue("C6", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A3:B6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Menulis header
    $sheet->setCellValue("A8", "NO")
          ->setCellValue("B8", "TANGGAL BAYAR")
          ->setCellValue("C8", "KODE KOM")
          ->setCellValue("D8", "TANGGAL TAGIHAN")
          ->setCellValue("E8", "NOTA TAGIHAN")
          ->setCellValue("F8", "NOPOL")
          ->setCellValue("G8", "KOMODITI")
          ->setCellValue("H8", "TONASE (KG)")
          ->setCellValue("I8", "TOTAL BAYAR (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A8:I8")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A8:I8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 9;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $goods	= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");

        if ($jenis == 'Buah') {
            $cost = $data['cost_bayar'];
        } else { 
            $cost = $data['cost'];
        }

        // if($data['reffer']=='null'||$data['reffer']==''){
        //     $weighing_id = 'TB'.$data['weighing_id'];
        // }else{
        //     $weighing_id = 'TB'.$data['reffer'];
        // }

        // Meratakan semua teks ke tengah, kecuali kolom G (Pedagang)
        $sheet->getStyle('A:I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom G (Pedagang) ke kiri
        // $sheet->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_pay'])))
              ->setCellValue("C" . $row, $data['ko_id'])
              ->setCellValue("D" . $row, date("d M Y", strtotime($data['date_check'])))
              ->setCellValue("E" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("F" . $row, $data['nopol'])
              ->setCellValue("G" . $row, $goods)
              ->setCellValue("H" . $row, $data['netto'])
              ->setCellValue("I" . $row, $cost);
            
        // Membuat format menjadi number
        $sheet->getStyle("H" . $row . ":I" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
    if ($row > 9) {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("H" . $row, "=SUM(H9:H" . ($row - 1) . ")")
              ->setCellValue("I" . $row, "=SUM(I9:I" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":F" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("H" . $row,  0)
              ->setCellValue("I" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":I" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":I" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("H" . $row . ":I" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A8:I" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Tagihan Mitra Terbayar PerPedagang' . ' (' .$name. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
