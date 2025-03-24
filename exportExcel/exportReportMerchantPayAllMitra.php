<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$commodity	= $_POST['commodity'];
	$sort		= $_POST['sortir'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    if($commodity=='Semua'){
        $sql = "select 	m.merchant_id as id, 
                        m.merchant_name as name, 
                        m.active_block, 
                        m.merchant_type as type, 
                        count(w.weighing_id) as tot,
                        CASE
                            WHEN m.merchant_type = 'Buah' THEN SUM(w.cost_bayar)
                            ELSE SUM(w.cost)
                        END AS total
                from 	merchant m, weighing_mitra w
                where 	w.merchant_id = m.merchant_id and
                        w.weighing_sts = 'Lunas' and
                        (w.date_pay between '".$date1."' and '".$date2."')
                group by id 
                order by ".$sort."";
    }else{
        $sql = "select 	m.merchant_id as id, 
                        m.merchant_name as name, 
                        m.merchant_type as type,
                        m.active_block, 
                        count(w.weighing_id) as tot, 
                        CASE
                            WHEN m.merchant_type = 'Buah' THEN SUM(w.cost_bayar)
                            ELSE SUM(w.cost)
                        END AS total
                from 	merchant m, weighing_mitra w
                where 	w.merchant_id = m.merchant_id and
                        m.merchant_type = '".$commodity."' and 
                        w.weighing_sts = 'Lunas' and
                        (w.date_pay between '".$date1."' and '".$date2."')
                group by id 
                order by ".$sort."";
    }

    $query 	= mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Mitra Terbayar Seluruh Pedagang");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP TAGIHAN MITRA TERBAYAR SELURUH PEDAGANG");
    $sheet->setCellValue("A3", "JENIS PEDAGANG");
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
    $sheet->mergeCells("A1:G1");

    // Menulis header
    $sheet->setCellValue("A6", "NO")
          ->setCellValue("B6", "KODE PEDAGANG")
          ->setCellValue("C6", "NAMA PEDAGANG")
          ->setCellValue("D6", "JENIS PEDAGANG")
          ->setCellValue("E6", "BLOK AKTIF")
          ->setCellValue("F6", "TOTAL NOTA")
          ->setCellValue("G6", "TOTAL RUPIAH (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A6:I6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A6:I6')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A6:G6")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A6:G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 7;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {

        // Meratakan semua teks ke tengah, kecuali kolom G (Pedagang)
        $sheet->getStyle('A:G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom C (Pedagang) ke kiri
        $sheet->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, $data['id'])
              ->setCellValue("C" . $row, $data['name'])
              ->setCellValue("D" . $row, $data['type'])
              ->setCellValue("E" . $row, $data['active_block'])
              ->setCellValue("F" . $row, $data['tot'])
              ->setCellValue("G" . $row, $data['total']);
            
        // Membuat format menjadi number
        $sheet->getStyle("F" . $row . ":G" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":E" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("F" . $row, "=SUM(F7:F" . ($row - 1) . ")")
              ->setCellValue("G" . $row, "=SUM(G7:G" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":E" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("F" . $row,  0)
              ->setCellValue("G" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":G" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":G" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("F" . $row . ":G" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A6:G" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Tagihan Mita Terbayar Seluruh Pedagang' . ' (' .$commodity. ') by ' .$sort. '_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
