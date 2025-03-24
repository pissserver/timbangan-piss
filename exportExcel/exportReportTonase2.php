<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$commodity	= $_POST['goods'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    $query 	= mysql_query("select distinct(w.weighing_id), w.weight_in, w.weight_out, w.noncommodity, w.rafaksi,
                            w.netto, w.date_in, w.date_out, w.reffer, w.merchant_id, w.goods_id
                            from weighing w
                            where w.weighing_sts != 'Batal' and
                            w.goods_id = '".$commodity."' and
                            (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')");
    $goods		= executeScalar("select goods_name from goods where goods_id = '".$commodity."'");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Pendapatan Tonase PerKomoditi");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN PENDAPATAN TONASE PER KOMODITI");
    $sheet->setCellValue("A3", "KOMODITI");
    $sheet->setCellValue("A4", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", $goods);
    $sheet->setCellValue("C4", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:K1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "TANGGAL KELUAR")
          ->setCellValue("C6", "NOTA TIMBANG")
          ->setCellValue("D6", "NOTA TAGIHAN")
          ->setCellValue("E6", "PEDAGANG")
          ->setCellValue("F6", "LAPAK")
          ->setCellValue("G6", "BERAT MASUK (KG)")
          ->setCellValue("H6", "BERAT KELUAR (KG)")
          ->setCellValue("I6", "NONKOMODITI (KG)")
          ->setCellValue("J6", "RAFAKSI (KG)")
          ->setCellValue("K6", "TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:M6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:M6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:K6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:K6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $total		= 0;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
		$goods		= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");
        $lapak 	= executeScalar("select l.lapak_name from rent r, lapak l 
                                where r.lapak_id = l.lapak_id and r.merchant_id = '".$data['merchant_id']."' 
                                and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') limit 1");

        $weighing_id = "TG" . $data['weighing_id'];

        if($data['reffer']=='null'||$data['reffer']==''){
            $weighing_id2 = 'TB'.$data['weighing_id'];
        }else{
            $weighing_id2 = 'TB'.$data['reffer'];
        }
        
        // Jika data kosong
        $data['weight_in'] = $data['weight_in'] == '' ? 0 : $data['weight_in'];
        $data['weight_out'] = $data['weight_out'] == '' ? 0 : $data['weight_out'];
        $data['noncommodity'] = $data['noncommodity'] == '' ? 0 : $data['noncommodity'];
        $data['rafaksi'] = $data['rafaksi'] == '' ? 0 : $data['rafaksi'];
        $data['netto'] = $data['netto'] == '' ? 0 : $data['netto'];

        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:K')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_in'])))
              ->setCellValue("C" . $row, $weighing_id2)
              ->setCellValue("D" . $row, $weighing_id)
              ->setCellValue("E" . $row, $name)
              ->setCellValue("F" . $row, $lapak)
              ->setCellValue("G" . $row, $data['weight_in'])
              ->setCellValue("H" . $row, $data['weight_out'])
              ->setCellValue("I" . $row, $data['noncommodity'])
              ->setCellValue("J" . $row, $data['rafaksi'])
              ->setCellValue("K" . $row, $data['netto']);

        // Membuat format menjadi number
        $sheet->getStyle("G" . $row . ":K" . $row)->getNumberFormat()->setFormatCode('#,##0');
            
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
        $sheet->mergeCells("A" . $row . ":F" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("G" . $row, "=SUM(G7:G" . ($row - 1) . ")")
              ->setCellValue("H" . $row, "=SUM(H7:H" . ($row - 1) . ")")
              ->setCellValue("I" . $row, "=SUM(I7:I" . ($row - 1) . ")")
              ->setCellValue("J" . $row, "=SUM(J7:J" . ($row - 1) . ")")
              ->setCellValue("K" . $row, "=SUM(K7:K" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":I" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("G" . $row, 0)
              ->setCellValue("H" . $row, 0)
              ->setCellValue("I" . $row, 0)
              ->setCellValue("J" . $row, 0)
              ->setCellValue("K" . $row, 0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":M" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":M" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":K" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":K" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("G" . $row . ":K" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:K" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Tonase PerKomoditi (' . $commodity . ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
