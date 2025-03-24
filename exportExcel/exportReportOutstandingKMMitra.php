<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

    $cashier= $_POST['cashier'];
	$date_now 	= date("d M Y");

    // Query untuk mengambil data
    if($cashier=='Semua'){
        $sql = "select * from km_mitra where km_status != 'Acc' order by km_id";
    }else{
        $sql = "select 	distinct(m.km_id), 
                        m.km_date, 
                        m.km_status, 
                        m.km_total 
                from km_mitra m, ko_mitra o 
                where 	o.km_id = m.km_id and 
                        o.cashier_id = '".$cashier."' and
                        m.km_status != 'Acc' order by m.km_id ";
    }

    $query = mysql_query($sql);

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Outstanding KM Mitra");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN OUTSTANDNG KMM MITRA");

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
            
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
    // Merge cell
    $sheet->mergeCells("A1:F1");

    // Menulis header
    $sheet->setCellValue("A3", "NO")
          ->setCellValue("B3", "KMM ID")
          ->setCellValue("C3", "NAMA KASIR")
          ->setCellValue("D3", "TANGGAL KMM")
          ->setCellValue("E3", "TOTAL KMM (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A3:I3')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A3:I3')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A3:E3")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 4;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $cashier_id	= executeScalar("select cashier_id from ko where km_id = '".$data['km_id']."'");
        $cashier	= executeScalar("select checker_name from checker where checker_id = '".$cashier_id."'");

        // Meratakan semua teks ke tengah, kecuali kolom C (Nama)
        $sheet->getStyle('A:E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom C (Nama) ke kiri
        $sheet->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, 'KMM'.$data['km_id'])
              ->setCellValue("C" . $row, $cashier)
              ->setCellValue("D" . $row, date("d M Y", strtotime($data['km_date'])))
              ->setCellValue("E" . $row, $data['km_total']);
            
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
    if ($row > 4) {
        $sheet->mergeCells("A" . $row . ":D" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("E" . $row, "=SUM(E4:E" . ($row - 1) . ")");
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
    $sheet->getStyle("A3:E" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Outstanding KMM Mitra_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
