<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

    $date1 = $_POST['date1'];
    $date2 = $_POST['date2'];
    $date = date("d-m-Y");

    // Query untuk mengambil data
    $query = mysql_query("SELECT w.*, m.merchant_name
                        FROM weighing w
                        JOIN merchant m ON w.merchant_id = m.merchant_id
                        WHERE m.merchant_status = 'Aktif' 
                        AND (w.date_in BETWEEN '".$date1."' AND '".$date2."')
                        AND w.weighing_sts NOT IN ('Gabungan', 'Pecahan', 'Tanpa Timbang')
                        AND w.p1 IS NOT NULL");

    // Buat objek PHPExcel
    $excel = new PHPExcel();
    $sheet = $excel->getActiveSheet();
    $sheet->setTitle("Laporan Transaksi Timbang ");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "LAPORAN TRANSAKSI TIMBANG HARIAN");
    $sheet->setCellValue("A3", "TANGGAL");
    $sheet->setCellValue("B3", " : ");
    $sheet->setCellValue("C3", date('d M Y', strtotime($date1)) . " s/d " . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A1:B3")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1:B3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Merge cell
    $sheet->mergeCells("A1:M1");

    // Menulis header
    $sheet->setCellValue("A5", "NO")
          ->setCellValue("B5", "TANGGAL MASUK")
          ->setCellValue("C5", "NOTA TIMBANG")
          ->setCellValue("D5", "NOPOL")
          ->setCellValue("E5", "PEDAGANG")
          ->setCellValue("F5", "KOMODITI")
          ->setCellValue("G5", "STATUS KENDARAAN")
          ->setCellValue("H5", "STATUS NOTA")
          ->setCellValue("I5", "B.MASUK (KG)")
          ->setCellValue("J5", "B.KELUAR (KG)")
          ->setCellValue("K5", "NK (KG)")
          ->setCellValue("L5", "RAFAKSI (KG)")
          ->setCellValue("M5", "TONASE (KG)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:M1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:M1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A5:M5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
    $sheet->getStyle('A5:M5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no = 1;
    $weight_in = 0;
    $weight_out = 0;
    $noncommodity = 0;
    $rafaksi = 0;
    $netto = 0;
    $row = 6;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $name = $data['merchant_name'];
        $goods = executeScalar("SELECT goods_name FROM goods WHERE goods_id = '".$data['goods_id']."'");

        if($data['reffer']=='null'||$data['reffer']==''){
            $weighing_id = "TB" . $data['weighing_id'];
        }else{
            $weighing_id = "TB" . $data['reffer'];
        }
        
        // Jika data kosong
        $data['weight_in'] = $data['weight_in'] == '' ? 0 : $data['weight_in'];
        $data['weight_out'] = $data['weight_out'] == '' ? 0 : $data['weight_out'];
        $data['noncommodity'] = $data['noncommodity'] == '' ? 0 : $data['noncommodity'];
        $data['rafaksi'] = $data['rafaksi'] == '' ? 0 : $data['rafaksi'];
        $data['netto'] = $data['netto'] == '' ? 0 : $data['netto'];

        // Meratakan semua teks ke tengah, kecuali kolom E (Pedagang)
        $sheet->getStyle('A:M')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom E (Pedagang) ke kiri
        $sheet->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_in'])))
              ->setCellValue("C" . $row, $weighing_id)
              ->setCellValue("D" . $row, $data['nopol'])
              ->setCellValue("E" . $row, $name)
              ->setCellValue("F" . $row, $goods)
              ->setCellValue("G" . $row, $data['weighing_sts'])
              ->setCellValue("H" . $row, $data['weighing_sts2'])
              ->setCellValue("I" . $row, $data['weight_in'])
              ->setCellValue("J" . $row, $data['weight_out'])
              ->setCellValue("K" . $row, $data['noncommodity'])
              ->setCellValue("L" . $row, $data['rafaksi'])
              ->setCellValue("M" . $row, $data['netto']);
            
        $sheet->getStyle("I" . $row . ":M" . $row)->getNumberFormat()->setFormatCode('#,##0');

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
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row, "=SUM(I6:I" . ($row - 1) . ")")
              ->setCellValue("J" . $row, "=SUM(J6:J" . ($row - 1) . ")")
              ->setCellValue("K" . $row, "=SUM(K6:K" . ($row - 1) . ")")
              ->setCellValue("L" . $row, "=SUM(L6:L" . ($row - 1) . ")")
              ->setCellValue("M" . $row, "=SUM(M6:M" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row, 0)
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
    $sheet->getStyle("A" . $row . ":M" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("I" . $row . ":M" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A5:M" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Laporan Transaksi Timbang Harian_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
