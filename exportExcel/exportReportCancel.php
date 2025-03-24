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
        $sql = "select * from weighing where weighing_sts = 'Batal' and 
        (date_in between '".$date1."' and '".$date2."')";
    }
    else{
        $sql = "select * from weighing w, merchant m 
        where w.merchant_id = m.merchant_id and 
        weighing_sts = 'Batal' and 
        m.merchant_type = 'Sayur' and m.merchant_status = 'Aktif' and 
        (w.date_in between '".$date1."' and '".$date2."')";
    
    }

    $query 	= mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Laporan Timbang Batal");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "LAPORAN TRANSAKSI TIMBANG BATAL");
    $sheet->setCellValue("A3", "JENIS KOMODITI");
    $sheet->setCellValue("A4", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", $commodity);
    $sheet->setCellValue("C4", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Meratakan semua teks ke kiri
    $sheet->getStyle("A3:A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    // Merge cell
    $sheet->mergeCells("A1:M1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "TANGGAL TRANSAKSI")
          ->setCellValue("C6", "NOTA TIMBANG")
          ->setCellValue("D6", "NOTA TAGIHAN")
          ->setCellValue("E6", "NOPOL")
          ->setCellValue("F6", "PEDAGANG")
          ->setCellValue("G6", "KOMODITI")
          ->setCellValue("H6", "MITRA")
          ->setCellValue("I6", "STATUS")
          ->setCellValue("J6", "BERAT MASUK (KG)")
          ->setCellValue("K6", "BERAT KELUAR (KG)")
          ->setCellValue("L6", "NONKOMODITI (KG)")
          ->setCellValue("M6", "TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:M1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:M6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:M6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $total		= 0;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
		$goods		= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");
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
        $data['netto'] = $data['netto'] == '' ? 0 : $data['netto'];

        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:M')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        $sheet->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_in'])))
              ->setCellValue("C" . $row, $weighing_id2)
              ->setCellValue("D" . $row, $weighing_id)
              ->setCellValue("E" . $row, $data['nopol'])
              ->setCellValue("F" . $row, $name)
              ->setCellValue("G" . $row, $goods)
              ->setCellValue("H" . $row, $data['laborer_sts'])
              ->setCellValue("I" . $row, $data['weighing_sts2'])
              ->setCellValue("J" . $row, $data['weight_in'])
              ->setCellValue("K" . $row, $data['weight_out'])
              ->setCellValue("L" . $row, $data['noncommodity'])
              ->setCellValue("M" . $row, $data['netto']);
            
        // Membuat format menjadi number
        $sheet->getStyle("J" . $row . ":M" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":I" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("J" . $row, "=SUM(J7:J" . ($row - 1) . ")")
              ->setCellValue("K" . $row, "=SUM(K7:K" . ($row - 1) . ")")
              ->setCellValue("L" . $row, "=SUM(L7:L" . ($row - 1) . ")")
              ->setCellValue("M" . $row, "=SUM(M7:M" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":I" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("J" . $row, 0)
              ->setCellValue("K" . $row, 0)
              ->setCellValue("L" . $row, 0)
              ->setCellValue("M" . $row, 0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":M" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":M" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":M" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle('A' . $row . ':M' . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("J" . $row . ":M" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:M" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Laporan Transaksi Timbang Batal_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
