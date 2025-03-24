<?php
    function dataTabel()
    {
		require_once('ssp.class.customize.php');     
        $date_now       = date('Y-m-d');
        $date_plus      = strtotime('-15 day', strtotime($date_now));
        $week           = date('Y-m-j' , $date_plus);
        $sql_details    = array(
            'user' => 'root',
            'pass' => '',
            'db'   => 'db_piss',
            'host' => 'localhost'
			// 'user' => 'edo',
            // 'pass' => 'edocakep@007',
            // 'db'   => 'pios2',
            // 'host' => '192.168.1.46'
        );

        $table      = 'weighing';
        $primaryKey = 'weighing_id';
        $columns    = array(
                array(
                    'db' => 'weighing_id',
                    'dt' => 0, 
                    'field' => 'weighing_id',
                    'as' => 'weighing_id',
                    'formatter' => function($d, $row) {
                        static $no = 1;
                        return $no++;
                    }
                ),
                array( 'db' => 'a.weighing_id', 'dt' => 1 , 'field' => 'weighing_id', 'as' => 'weighing_id',
                    'formatter' => function($d, $row) {
                        return '<div style="vertical-align: middle; background-color: green; color: #fff; padding: 3px; border-radius: 5px; margin: 5px auto 5px auto; width: 110px;">TG'.$d.'</div>';
                    }
                ),
                array( 'db' => 'a.nopol', 'dt' => 2 , 'field' => 'nopol', 'as' => 'nopol'),
                array( 'db' => 'b.merchant_name', 'dt' => 3 , 'field' => 'merchant_name', 'as' => 'merchant_name'),
                array( 'db' => 'a.weight_in', 'dt' => 4 , 'field' => 'weight_in', 'as' => 'weight_in',
                    'formatter' => function($d, $row) {
                        if ($row['weight_in'] != NULL) {
                            return $d . ' Kg';
                        } elseif ($row['weight_in'] == NULL) {
                            return '0 Kg';
                        }
                    }
                ),
                array( 'db' => 'a.weight_out', 'dt' => 5 , 'field' => 'weight_out', 'as' => 'weight_out',
                    'formatter' => function($d, $row) {
                        if ($row['weight_out'] != NULL) {
                            return $d . ' Kg';
                        } elseif ($row['weight_out'] == NULL) {
                            return '0 Kg';
                        }
                    }
                ),
                array( 'db' => 'a.noncommodity', 'dt' => 6 , 'field' => 'noncommodity', 'as' => 'noncommodity',
                    'formatter' => function($d, $row) {
                        if ($row['noncommodity'] != NULL) {
                            return $d . ' Kg';
                        } elseif ($row['noncommodity'] == NULL) {
                            return '0 Kg';
                        }
                    }
                ),
                array( 'db' => 'a.rafaksi', 'dt' => 7 , 'field' => 'rafaksi', 'as' => 'rafaksi', 
                    'formatter' => function($d, $row) {
                        if ($row['rafaksi'] != NULL) {
                            return $d . ' Kg';
                        } elseif ($row['rafaksi'] == NULL) {
                            return '0 Kg';
                        }
                    }
                ),
                array( 'db' => 'a.netto', 'dt' => 8 , 'field' => 'netto', 'as' => 'netto',
                    'formatter' => function($d, $row) {
                        return $d . ' Kg';
                    }
                ),
                array(
                    'db' => 'CONCAT(a.weighing_sts, "<br>", "<div style=\'background-color: #428bca; color: #fff; padding: 3px; border-radius: 5px; margin: 0 auto 0 auto; width: 125px; font-size: 11px;\'>", a.weighing_sts2, "</div>")',
                    'dt' => 9,
                    'field' => 'weighing_sts',
                    'as' => 'weighing_sts'
                ),                
                array( 'db' => 'a.laborer_sts', 'dt' => 10 , 'field' => 'laborer_sts', 'as' => 'laborer_sts'),
                array(
                    'db' => 'CONCAT(TIME_FORMAT(a.time_in, "%H:%i"), "<br>", DATE_FORMAT(a.date_in, "%d %b %Y"))',
                    'dt' => 11,
                    'field' => 'time_in',
                    'as' => 'time_in',
                ),                
                array( 'db' => 'a.p1', 'dt' => 12 , 'field' => 'p1', 'as' => 'p1', 
                    'formatter' => function ($d,$row)
                        {
                            if($row['p1'] !="" || $row['p1'] !=NULL)
                            {
                                return '<i class="fa fa-check text-success" style="font-size:23px"></i>';
                            }
                            else
                            {
                                return '<i class="fa fa-close text-danger" style="font-size:23px"></i>';
                            }
                        }
                ),
                array( 'db' => 'a.date_check', 'dt' => 13 , 'field' => 'date_check', 'as' => 'date_check',
                        'formatter' => function ($d,$row)
                        {
                            // if($row['date_check'] !="" || $row['date_check'] !=NULL)
                            // {
                            //     return 'C';
                            // }
                            // else
                            // {
                            //     return '';
                            // }
                            if($row['date_check'] !="" || $row['date_check'] !=NULL)
                            {
                                return '<i class="fa fa-check text-success" style="font-size:23px"></i>';
                            }
                            else
                            {
                                return '<i class="fa fa-close text-danger" style="font-size:23px"></i>';
                            }
                        }
                    ),
                array( 'db' => 'a.p2', 'dt' => 14 , 'field' => 'p2', 'as' => 'p2', 
                    'formatter' => function ($d,$row)
                        {
                            if($row['p2'] !="" || $row['p2'] !=NULL)
                            {
                                return '<i class="fa fa-check text-success" style="font-size:23px"></i>';
                            }
                            else
                            {
                                return '<i class="fa fa-close text-danger" style="font-size:23px"></i>';
                            }
                        }
                ),
                array( 'db' => 'a.weighing_id', 'dt' => 15 , 'field' => 'weighing_id', 'as' => 'weighing_id',
                    'formatter' => function ($d,$row)
                    {
                        return "<button onClick='edit(\"".$d."\")' class='btn btn-outline btn-block btn-primary btn-xs'><i class='fa fa-check-square-o fa-fw'></i> Check</button>";
                    }
                ),
            );
        $join   = "from weighing a left join merchant b on(a.merchant_id=b.merchant_id)";
        $where  = "(a.date_out between '".$week."' and '".$date_now."') and (a.weighing_sts = 'Keluar' || a.weighing_sts = 'Batal' || a.weighing_sts = 'Lunas') and date_check is not null";
        $result = SSP::complex( $_GET, $sql_details, $table, $primaryKey,$columns,$join,$where,NULL);
        /*$start=$_REQUEST['start'];
        $idx=0;
            foreach($result['data'] as &$res)
            {
                $res[0]=(string)$start+1;
                $start++;
                $idx++;
            }*/
        echo json_encode($result);
    }
    dataTabel();
?>