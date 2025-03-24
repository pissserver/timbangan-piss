<?php
    include "../connection.php";
    require_once '../libs/PHPExcel/Classes/PHPExcel.php'; 
    // Load library PHPExcel

	$date1		= $_POST['date1'];
	$date2		= $_POST['date2'];
    $mid	= $_POST['merchant'];
	$date 		= date("d M Y");

    // Query untuk mengambil data
    $query 	= mysql_query("	select 	weighing_id, 
											goods_id, 
											reffer, 
											date_out, 
											nopol, 
											weighing_sts2, 
											weight_in, 
											weight_out, 
											noncommodity, 
											rafaksi, 
											netto, 
											cost_tonase,
											cost 
									from 	weighing 
									where 	weighing_sts = 'Lunas' and 
											merchant_id = '".$mid."' and 
											(date_pay between '".$date1."' and '".$date2."')");
                                        
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
    $sheet->setTitle("Tonase Terbayar PerPedagang");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A1", "RINCIAN TAGIHAN TONASE TERBAYAR PER PEDAGANG");
    // Membuat text bold
    $sheet->getStyle("A1")->getFont()->setBold(true);
    // Meratakan semua teks ke tengah
    $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    // Merge cell
    $sheet->mergeCells("A1:H1");

    // Menulis Informasi di Atas Tabel
    $sheet->setCellValue("A3", "NAMA PEDAGANG ")
          ->setCellValue("A4", "LAPAK         ")
          ->setCellValue("A5", "TANGGAL       ")
          ->setCellValue("B3", ':')
          ->setCellValue("B4", ':')
          ->setCellValue("B5", ':')
          ->setCellValue("C3", $name.' ('.$mid.')' )
          ->setCellValue("C4", $lapak)
          ->setCellValue("C5", date('d M Y', strtotime($date1)) . ' s/d ' . date('d M Y', strtotime($date2)));

    // Membuat text bold
    $sheet->getStyle("A3:B5")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle("B3:B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Menulis header
    $sheet->setCellValue("A7", "NO")
          ->setCellValue("B7", "TANGGAL BAYAR")
          ->setCellValue("C7", "NOTA TIMBANG")
          ->setCellValue("D7", "NOTA TAGIHAN")
          ->setCellValue("E7", "NOPOL")
          ->setCellValue("F7", "KOMODITI")
          ->setCellValue("G7", "TONASE (KG)")
          ->setCellValue("H7", "TOTAL BAYAR (RP)");

    // Mengatur warna latar belakang hijau untuk header
    // $sheet->getStyle('A1:I1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle('A1:I1')->getFill()->getStartColor()->setRGB('7FBF7F'); // Warna Hijau

    // Membuat Text Bold
    $sheet->getStyle("A7:H7")->getFont()->setBold(true);

    // Meratakan semua teks ke tengah
    $sheet->getStyle('A7:H7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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

        // Meratakan semua teks ke tengah, kecuali kolom G (Pedagang)
        $sheet->getStyle('A:H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        // Set alignment khusus untuk kolom G (Pedagang) ke kiri
        // $sheet->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        // Menulis data per baris
        $sheet->setCellValue("A" . $row, $no)
              ->setCellValue("B" . $row, date("d M Y", strtotime($data['date_out'])))
              ->setCellValue("C" . $row, $weighing_id)
              ->setCellValue("D" . $row, 'TG' . $data['weighing_id'])
              ->setCellValue("E" . $row, $data['nopol'])
              ->setCellValue("F" . $row, $goods)
              ->setCellValue("G" . $row, $data['netto'])
              ->setCellValue("H" . $row, $data['cost_tonase']);

        // Membuat format menjadi number
        $sheet->getStyle("G" . $row . ":H" . $row)->getNumberFormat()->setFormatCode('#,##0');
            

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
        $sheet->mergeCells("A" . $row . ":F" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("G" . $row, "=SUM(G8:G" . ($row - 1) . ")")
              ->setCellValue("H" . $row, "=SUM(H8:H" . ($row - 1) . ")");
    } else {
        $sheet->mergeCells("A" . $row . ":F" . $row); 
        $sheet->setCellValue("A" . $row, "TOTAL")
              ->setCellValue("G" . $row,  0)
              ->setCellValue("H" . $row,  0);
    }

    // Mengatur warna latar belakang kuning untuk baris total
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    // $sheet->getStyle("A" . $row . ":I" . $row)->getFill()->getStartColor()->setRGB('FFFF00'); // Warna Kuning

    // Membuat Text Bold
    $sheet->getStyle("A" . $row . ":H" . $row)->getFont()->setBold(true);

    // Meratakan teks "TOTAL" ke tengah
    $sheet->getStyle("A" . $row . ":H" . $row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    // Membuat format menjadi number
    $sheet->getStyle("G" . $row . ":H" . $row)->getNumberFormat()->setFormatCode('#,##0');

    // Menambahkan border pada seluruh tabel
    $sheet->getStyle("A7:H" . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

    // Set header untuk download file Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="Rincian Tagihan Tonase Terbayar PerPedagang' . ' (' .$name. ')_' . date('d M Y') . '.xls"');
    header('Cache-Control: max-age=0');

    // Menyimpan dan mendownload file Excel
    $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
    $objWriter->save('php://output');
    exit;
?>
