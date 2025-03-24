<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$km			= $_POST['km'];
	$date2 		= date("d M Y");

    // Query untuk mengambil data
    $sql 	= "select * from ko where ko_status = 'Acc' and km_id = '".$km."'";

    $cashier_id	= executeScalar("select cashier_id from ko where km_id = '".$km."'");
    $cashier	= executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");
    $km_date	= executeScalar("select km_date from km where km_id = '".$km."'");
    $km_total	= executeScalar("select km_total from km where km_id = '".$km."'");
    $km_cash	= executeScalar("select km_cash from km where km_id = '".$km."'");

    $query = mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Rincian KO ke KM");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN SETORAN TONASE KO ke KM");

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:E1");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A3", "KM ID              ")
          ->setCellValue("A4", "NAMA KASIR         ")
          ->setCellValue("A5", "TANGGAL SETORAN KM ")
          ->setCellValue("A6", "TOTAL SETORAN      ")
          ->setCellValue("A7", "UANG YANG DITERIMA ")
          ->setCellValue("A8", "SELISIH            ")
          ->setCellValue("B3", ':')
          ->setCellValue("B4", ':')
          ->setCellValue("B5", ':')
          ->setCellValue("B6", ':')
          ->setCellValue("B7", ':')
          ->setCellValue("B8", ':')
          ->setCellValue("C3", $km )
          ->setCellValue("C4", $cashier)
          ->setCellValue("C5", date('d M Y', strtotime($km_date)))
          ->setCellValue("C6", $km_total)
          ->setCellValue("C7", $km_cash)
          ->setCellValue("C8", '=C7-C6');

    // Membuat text bold
    $sheet->getStyle("A3:C8")->getFont()->setBold(true);

    // Membuat format menjadi number
    $sheet->getStyle("C6:C8")->getNumberFormat()->setFormatCode('#,##0');

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle("C3:C8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);


    // Menulis header
    $sheet->setCellValue("A10", "NO")
          ->setCellValue("B10", "KO ID")
          ->setCellValue("C10", "NAMA PENYETOR")
          ->setCellValue("D10", "TANGGAL KO")
          ->setCellValue("E10", "TOTAL KO (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A10:E10")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A10:E10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 11;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $checker= executeScalar("select checker_name from checker where checker_id = '".$data['receiver_id']."'");

        // Meratakan semua teks ke tengah, kecuali kolom C (Nama)
        $sheet->getStyle('A:E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom C (Nama) ke kiri
        $sheet->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // if($r['reffer']=='null'||$r['reffer']==''){
        //     $weigingID = 'TB'.$r['weighing_id'];
        // }else{
        //     $weigingID = 'TB'.$r['reffer'];
        // }

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, 'KO' . $data['ko_id'])
              ->setCellValue("C" . $row, $checker)
              ->setCellValue("D" . $row, date("d M Y", strtotime($data['ko_date'])))
              ->setCellValue("E" . $row, $data['ko_total']);
            
        // Membuat format menjadi number
        $sheet->getStyle("E" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
    if ($row > 11) {
        $sheet->mergeCells("A" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("E" . $row, "=SUM(E11:E" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("E" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":E" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":E" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("E" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A10:E" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Setoran Tonase KO ke KM_' . date('Ymd') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
