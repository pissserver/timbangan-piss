<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
	$mid		= $_POST['merchant'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    $query 	= mysql_query("select * from weighing 
                            where weighing_sts != 'Batal' and 
                            merchant_id = '".$mid."' and 
                            (date_check between '".$date1." 00:00:00' and '".$date2." 23:59:59')");
                                        
    $name	= executeScalar("select merchant_name from merchant where merchant_id = '".$mid."'");
    $sql2 	= mysql_query("select l.lapak_name from rent r, lapak l
                            where r.lapak_id = l.lapak_id and 
                            r.merchant_id = '".$mid."' and
                            (r.rent_sts2 != 'Pindah' && r.rent_sts2 != 'Batal')
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
    $sheet->setTitle("Pendapatan Tonase PerPedagang");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN PENDAPATAN TONASE PER PEDAGANG");

    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
    
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    // Merge cell
    $sheet->mergeCells("A1:M1");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A3", "NAMA PEDAGANG ")
          ->setCellValue("A4", "LAPAK         ")
          ->setCellValue("A5", "TANGGAL       ")
          ->setCellValue("B3", ':')
          ->setCellValue("B4", ':')
          ->setCellValue("B5", ':')
          ->setCellValue("C3", $name.' ('.$mid.')' )
          ->setCellValue("C4", $lapak)
          ->setCellValue("C5", date("d M Y", strtotime($date1)).' s/d '.date("d M Y", strtotime($date2)));  

    // Membuat text bold
    $sheet->getStyle("A3:B5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Menulis header
    $sheet->setCellValue("A7", "NO")
          ->setCellValue("B7", "TANGGAL KELUAR")
          ->setCellValue("C7", "NOTA TIMBANG")
          ->setCellValue("D7", "NOTA TAGIHAN")
          ->setCellValue("E7", "NOPOL")
          ->setCellValue("F7", "KOMODITI")
          ->setCellValue("G7", "STATUS MITRA")
          ->setCellValue("H7", "STATUS NOTA")
          ->setCellValue("I7", "BERAT MASUK (KG)")
          ->setCellValue("J7", "BERAT KELUAR (KG)")
          ->setCellValue("K7", "NONKOMODITI (KG)")
          ->setCellValue("L7", "RAFAKSI (KG)")
          ->setCellValue("M7", "TONASE (KG)")
          ->setCellValue("N7", "TOTAL (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A7:N7")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A7:N7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    $no			= 1;
    $row        = 8;

    // Looping untuk mengambil dan menuliskan data
    while ($data = mysql_fetch_array($query)) {
        $goods	= executeScalar("select goods_name from goods where goods_id = '".$data['goods_id']."'");

        if($data['reffer']=='null'||$data['reffer']==''){
            $weighing_id = 'TB'.$data['weighing_id'];
        }else{
            $weighing_id = 'TB'.$data['reffer'];
        }

        // Meratakan semua teks ke tengah
        $sheet->getStyle('A:N')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom G (Pedagang) ke kiri
        // $sheet->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_out'])))
              ->setCellValue("C" . $row, $weighing_id)
              ->setCellValue("D" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("E" . $row, $data['nopol'])
              ->setCellValue("F" . $row, $goods)
              ->setCellValue("G" . $row, $data['laborer_sts'])
              ->setCellValue("H" . $row, $data['weighing_sts2'])
              ->setCellValue("I" . $row, $data['weight_in'] != 0 ? $data['weight_in'] : '0')
              ->setCellValue("J" . $row, $data['weight_out'] != 0 ? $data['weight_out'] : '0')
              ->setCellValue("K" . $row, $data['noncommodity'] != 0 ? $data['noncommodity'] : '0')
              ->setCellValue("L" . $row, $data['rafaksi'] != 0 ? $data['rafaksi'] : '0')
              ->setCellValue("M" . $row, $data['netto'] != 0 ? $data['netto'] : '0')
              ->setCellValue("N" . $row, $data['cost_tonase'] != 0 ? $data['cost_tonase'] : '0');
            
        // Membuat format menjadi number
        $sheet->getStyle("I" . $row . ":N" . $row)->getNumberFormat()->setFormatCode('#,##0');
        
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
    if ($row > 8) {
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row, "=SUM(I8:I" . ($row - 1) . ")")
              ->setCellValue("J" . $row, "=SUM(J8:J" . ($row - 1) . ")")
              ->setCellValue("K" . $row, "=SUM(K8:K" . ($row - 1) . ")")
              ->setCellValue("L" . $row, "=SUM(L8:L" . ($row - 1) . ")")
              ->setCellValue("M" . $row, "=SUM(M8:M" . ($row - 1) . ")")
              ->setCellValue("N" . $row, "=SUM(N8:N" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":H" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("I" . $row,  0)
              ->setCellValue("J" . $row,  0)
              ->setCellValue("K" . $row,  0)
              ->setCellValue("L" . $row,  0)
              ->setCellValue("M" . $row,  0)
              ->setCellValue("N" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":N" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":N" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("I" . $row . ":N" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A7:N" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Pendapatan Tonase PerPedagang' . ' (' .$name. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
