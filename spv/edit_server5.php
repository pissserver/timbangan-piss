<?php
include "../connection.php";

$columns = [
    0=>'w.weighing_id',
    1=>'w.weighing_id',
    2=>'w.nopol',
    3=>'m.merchant_name',
    4=>'g.goods_name',
    5=>'w.weight_in',
    6=>'w.weight_out',
    7=>'w.netto',
    8=>'w.weighing_sts',
    9=>'w.time_in',
    10=>'w.date_in'
];

$limit  = $_POST['length'];
$start  = $_POST['start'];
$order  = $columns[$_POST['order'][0]['column']];
$dir    = $_POST['order'][0]['dir'];
$search = $_POST['search']['value'];

$date_now = date('Y-m-d');
$yesterday = date('Y-m-d',strtotime('-30 days'));

$searchSql="";
if($search!=''){
    $searchSql=" AND (
        w.weighing_id LIKE '%$search%' OR
        w.nopol LIKE '%$search%' OR
        m.merchant_name LIKE '%$search%' OR
        g.goods_name LIKE '%$search%' OR
        w.weighing_sts LIKE '%$search%'
    )";
}

$totalData = mysql_fetch_assoc(mysql_query("
    SELECT COUNT(*) total FROM weighing
    WHERE date_in BETWEEN '$yesterday' AND '$date_now'
    "))['total'];

$totalFiltered = mysql_fetch_assoc(mysql_query("
    SELECT COUNT(*) total FROM weighing w
    LEFT JOIN merchant m ON m.merchant_id=w.merchant_id
    LEFT JOIN goods g ON g.goods_id=w.goods_id
    WHERE w.date_in BETWEEN '$yesterday' AND '$date_now' $searchSql
    "))['total'];

$q = mysql_query("
    SELECT w.*,m.merchant_name,g.goods_name
    FROM weighing w
    LEFT JOIN merchant m ON m.merchant_id=w.merchant_id
    LEFT JOIN goods g ON g.goods_id=w.goods_id
    WHERE w.date_in BETWEEN '$yesterday' AND '$date_now'
    $searchSql
    ORDER BY $order $dir
    LIMIT $start,$limit
    ");

$data=[];
$no=$start+1;

while($r=mysql_fetch_assoc($q)){
    $wid=sprintf("%07d",$r['weighing_id']);

    if($r['weighing_sts']=='Masuk'||$r['weighing_sts']=='Batal'){
        $nota="<div style='text-align: center;background:#428bca;color:#fff;padding:3px;border-radius:5px;width:110px;margin:auto;line-height:20px;'>TB$wid</div>";
    }else{
        $nota="<div style='text-align: center;background:green;color:#fff;padding:3px;border-radius:5px;width:110px;margin:auto;line-height:20px;'>TG$wid</div>";
    }

    $data[]=[
        "<div style='text-align:center'>".$no++."</div>",
        $nota,
        "<div style='text-align:center'>{$r['nopol']}</div>",
        "<div style='text-align:left'>{$r['merchant_name']}</div>",
        "<div style='text-align:center'>{$r['goods_name']}</div>",
        "<div style='text-align:center'>".($r['weight_in']?:0)." Kg</div>",
        "<div style='text-align:center'>".($r['weight_out']?:0)." Kg</div>",
        "<div style='text-align:center'>".($r['netto']?:0)." Kg</div>",
        "<div style='text-align:center'>{$r['weighing_sts']}</div>",
        "<div style='text-align: center;background:#428bca;color:#fff;padding:3px;border-radius:5px;width:100px;margin: 5px auto 0 auto;'><i class='fa fa-arrow-circle-down fa-fw'></i>&nbsp;".date('H:i',strtotime($r['time_in']))."</div>
        <div style='text-align: center;background:#c42;color:#fff;padding:3px;border-radius:5px;width:100px;margin: 5px auto 0 auto;'><i class='fa fa-arrow-circle-up fa-fw'></i>&nbsp;".($r['time_out']?date('H:i',strtotime($r['time_out'])):'Belum')."</div>",
        "<div style='text-align: center;background:#428bca;color:#fff;padding:3px;border-radius:5px;width:130px;margin: 5px auto 0 auto;'><i class='fa fa-arrow-circle-down fa-fw'></i>&nbsp;".date('d M Y',strtotime($r['date_in']))."</div>
        <div style='text-align: center;background:#c42;color:#fff;padding:3px;border-radius:5px;width:130px;margin: 5px auto 0 auto;'><i class='fa fa-arrow-circle-up fa-fw'></i>&nbsp;".($r['date_out']?date('d M Y',strtotime($r['date_out'])):'Belum')."</div>",
        "<a style='border-radius: 20px; padding: 3px 7px;' class='btn btn-xs btn-outline btn-primary btn-block modalEditTime' data-id='{$r['weighing_id']}'><i class='fa fa-edit fa-fw'></i>&nbsp;Edit Jam</a>
        <a style='border-radius: 20px; padding: 3px 7px;' class='btn btn-xs btn-outline btn-primary btn-block modalEditDate' data-id='{$r['weighing_id']}'><i class='fa fa-edit fa-fw'></i>&nbsp;Edit Tanggal</a>"
    ];
}

echo json_encode([
    "draw"=>intval($_POST['draw']),
    "recordsTotal"=>$totalData,
    "recordsFiltered"=>$totalFiltered,
    "data"=>$data
]);
