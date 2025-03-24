<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$commodity	= $_POST['commodity'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    if($commodity=='Semua'){
        $sql = "select weighing_id, merchant_id, checker_id, goods_id, date_in, laborer_sts, weighing_sts, 
                weighing_sts2, weight_in, weight_out, noncommodity, rafaksi, netto, reffer, nopol, date_check	
                from weighing where weighing_sts != 'Batal' and 
                (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')";
    }else{
        $sql = "select w.weighing_id, w.merchant_id, w.checker_id, w.goods_id, w.date_in, w.laborer_sts, w.weighing_sts, 
                w.weighing_sts2, w.weight_in, w.weight_out, w.noncommodity, w.rafaksi, w.netto, W.reffer, W.nopol, w.date_check
                from weighing w, merchant m 
                where w.merchant_id = m.merchant_id and 
                weighing_sts != 'Batal' and 
                m.merchant_type = '".$commodity."' and 
                (w.date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')";
    }

    $query 	= mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Rincian Tonase Harian");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN PENDAPATAN TONASE HARIAN");
    $sheet->setCellValue("A3", "JENIS KOMODITI");
    $sheet->setCellValue("A4", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", $commodity);
    $sheet->setCellValue("C4", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:P1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "TANGGAL MASUK")
          ->setCellValue("C6", "NOTA TIMBANG")
          ->setCellValue("D6", "NOTA TAGIHAN")
          ->setCellValue("E6", "NOPOL")
          ->setCellValue("F6", "PEDAGANG")
          ->setCellValue("G6", "JENIS")
          ->setCellValue("H6", "KOMODITI")
          ->setCellValue("I6", "LAPAK")
          ->setCellValue("J6", "STATUS MITRA")
          ->setCellValue("K6", "STATUS NOTA")
          ->setCellValue("L6", "BERAT MASUK (KG)")
          ->setCellValue("M6", "BERAT KELUAR (KG)")
          ->setCellValue("N6", "NON KOMODITI (KG)")
          ->setCellValue("O6", "RAFAKSI (KG)")
          ->setCellValue("P6", "TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:P6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom F (Pedagang)
    $sheet->getStyle('A6:P6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
        $jenis		= executeScalar("select merchant_type from merchant where merchant_id = '".$data['merchant_id']."'");
        $lapak 		= executeScalar("select l.lapak_name from rent r, lapak l 
							where r.lapak_id = l.lapak_id and r.merchant_id = '".$data['merchant_id']."' 
							and (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal') limit 1");
		$goods		= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");

        if($data['reffer']=='null'||$data['reffer']==''){
            $weighing_id = 'TB'.$data['weighing_id'];
        }else{
            $weighing_id = 'TB'.$data['reffer'];
        }

        // Meratakan semua teks ke tengah, kecuali kolom F, I (Pedagang, Lapak)
        $sheet->getStyle('A:P')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom F, I (Pedagang, Lapak) ke kiri
        $sheet->getStyle('F:I')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_check'])))
              ->setCellValue("C" . $row, $weighing_id)
              ->setCellValue("D" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("E" . $row, $data['nopol'])
              ->setCellValue("F" . $row, $name)
              ->setCellValue("G" . $row, $jenis)
              ->setCellValue("H" . $row, $goods)
              ->setCellValue("I" . $row, $lapak)
              ->setCellValue("J" . $row, $data['laborer_sts'])
              ->setCellValue("K" . $row, $data['weighing_sts2'])
              ->setCellValue("L" . $row, $data['weight_in'])
              ->setCellValue("M" . $row, $data['weight_out'])
              ->setCellValue("N" . $row, $data['noncommodity'])
              ->setCellValue("O" . $row, $data['rafaksi'])
              ->setCellValue("P" . $row, $data['netto']);
            
        // Membuat format menjadi number
        $sheet->getStyle("L" . $row . ":P" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":K" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("L" . $row, "=SUM(L7:L" . ($row - 1) . ")")
              ->setCellValue("M" . $row, "=SUM(M7:M" . ($row - 1) . ")")
              ->setCellValue("N" . $row, "=SUM(N7:N" . ($row - 1) . ")")
              ->setCellValue("O" . $row, "=SUM(O7:O" . ($row - 1) . ")")
              ->setCellValue("P" . $row, "=SUM(P7:P" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":K" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("L" . $row,  0)
              ->setCellValue("M" . $row,  0)
              ->setCellValue("N" . $row,  0)
              ->setCellValue("O" . $row,  0)
              ->setCellValue("P" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":P" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":P" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("L" . $row . ":P" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:P" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Tonase Harian' . ' (' .$commodity. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
