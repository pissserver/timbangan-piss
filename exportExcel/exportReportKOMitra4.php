<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$date_now 	= date("d M Y");

    // Query untuk mengambil data
    $sql 	= "select sum(ko_total) as total, receiver_id from ko_mitra where ko_date between '".$date1."' and '".$date2."' group by receiver_id";

    $query = mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("KOM Mitra PerChecker");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "REKAP TAGIHAN MITRA KOM PERCHECKER");
    $sheet->setCellValue("A3", "TANGGAL SETORAN KOM");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);
        
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:D1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "NAMA PENYETOR")
          ->setCellValue("C5", "TOTAL TAGIHAN")
          ->setCellValue("D5", "TOTAL SETORAN KOM (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A5:I5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A5:I5')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A5:D5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A5:D5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $checker = executeScalar("select checker_name from checker where checker_id = '".$data['receiver_id']."'");
        $invoice = executeScalar("	select 	count(w.weighing_id) as tot 
                                    from 	weighing_mitra w, ko_mitra k 
                                    where 	k.ko_id = w.ko_id and
                                            (k.ko_date between '".$date1."' and '".$date2."') and
                                            k.receiver_id = '".$data['receiver_id']."'");

        // Meratakan semua teks ke tengah
        $sheet->getStyle('A:D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom B (Nama) ke kiri
        $sheet->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, $checker)
              ->setCellValue("C" . $row, $invoice)
              ->setCellValue("D" . $row, $data['total']);
            
        // Membuat format menjadi number
        $sheet->getStyle("C" . $row . ":D" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
    if ($row > 6) {
        $sheet->mergeCells("A" . $row . ":B" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row, "=SUM(C6:C" . ($row - 1) . ")")
              ->setCellValue("D" . $row, "=SUM(D6:D" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":G" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("C" . $row,  0)
              ->setCellValue("D" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":D" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":D" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("C" . $row . ":D" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A5:D" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rekap Tagihan Mitra KOM PerChecker' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
