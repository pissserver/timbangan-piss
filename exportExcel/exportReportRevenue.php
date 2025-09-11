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
        $sql = "select 	weighing_id, 
                        merchant_id, 
                        checker_id, 
                        reffer, 
                        date_in, 
                        date_pay, 
                        nopol, 
                        netto, 
                        cost_tonase,
                        cost
                from weighing 
                where weighing_sts = 'Lunas' and 
                (date_pay between '".$date1."' and '".$date2."')
                ORDER BY date_pay ASC";
    }else{
        $sql = "select 	w.weighing_id, 
                        w.merchant_id, 
                        w.checker_id, 
                        w.reffer, 
                        w.date_in, 
                        w.date_pay, 
                        w.nopol, 
                        w.netto, 
                        w.cost_tonase,
                        w.cost 
                from 	weighing w, 
                        merchant m 
                where 	w.merchant_id = m.merchant_id and 
                        weighing_sts = 'Lunas' and 
                        m.merchant_type = '".$commodity."' and
                        (w.date_pay between '".$date1."' and '".$date2."')
                        ORDER BY date_pay ASC";
    }

    $query 	= mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Laporan Tonase Terbayar");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN TAGIHAN TONASE TERBAYAR");
    $sheet->setCellValue("A3", "JENIS PEDAGANG");
    $sheet->setCellValue("A4", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("B4", " : ");
    $sheet->setCellValue("C3", $commodity);
    $sheet->setCellValue("C4", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B4")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1:B4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A3:A4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    // Merge cell
    $sheet->mergeCells("A1:J1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "TANGGAL TAGIHAN")
          ->setCellValue("C6", "TANGGAL BAYAR")
          ->setCellValue("D6", "NOTA TIMBANG")
          ->setCellValue("E6", "NOTA TAGIHAN")
          ->setCellValue("F6", "NOPOL")
          ->setCellValue("G6", "PEDAGANG")
          ->setCellValue("H6", "JENIS PEDAGANG")
          ->setCellValue("I6", "TONASE (KG)")
          ->setCellValue("J6", "TOTAL (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:J6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:J6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$data['merchant_id']."'");
        $jenis		= executeScalar("select merchant_type from merchant where merchant_id = '".$data['merchant_id']."'");

        if($data['reffer']=='null'||$data['reffer']==''){
            $weighing_id = 'TB'.$data['weighing_id'];
        }else{
            $weighing_id = 'TB'.$data['reffer'];
        }

        // Meratakan semua teks ke tengah, kecuali kolom G (Pedagang)
        $sheet->getStyle('A:J')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom G (Pedagang) ke kiri
        $sheet->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_in'])))
              ->setCellValue("C" . $row, date("d M Y", strtotime($data['date_pay'])))
              ->setCellValue("D" . $row, $weighing_id)
              ->setCellValue("E" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("F" . $row, $data['nopol'])
              ->setCellValue("G" . $row, $name)
              ->setCellValue("H" . $row, $jenis)
              ->setCellValue("I" . $row, $data['netto'])
              ->setCellValue("J" . $row, $data['cost_tonase']);
            
        // Membuat format menjadi number
        $sheet->getStyle("I" . $row . ":J" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row, "=SUM(I7:I" . ($row - 1) . ")")
              ->setCellValue("J" . $row, "=SUM(J7:J" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row,  0)
              ->setCellValue("J" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":J" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":J" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("I" . $row . ":J" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:J" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Tagihan Tonase Terbayar' . ' (' .$commodity. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
