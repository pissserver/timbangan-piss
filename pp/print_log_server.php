<?php
include "../connection.php";
error_reporting(0);

$limit  = intval($_POST['length']);
$start  = intval($_POST['start']);
$draw   = intval($_POST['draw']);
$search = mysql_real_escape_string($_POST['search']['value']);

$where = " 
    WHERE wp.print_role = 'cashier'
    AND wp.print_date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()
";

if($search != ""){
    $where .= " AND (
        wp.print_user LIKE '%$search%' OR
        wp.print_nopol LIKE '%$search%' OR
        wp.print_action LIKE '%$search%' OR
        m.merchant_name LIKE '%$search%'
    ) ";
}

/* total 30 hari */
$qTotal = mysql_query("
    SELECT COUNT(*) AS total
    FROM weighing_print wp
    WHERE wp.print_role = 'cashier'
    AND wp.print_date BETWEEN DATE_SUB(NOW(), INTERVAL 30 DAY) AND NOW()
");
$totalData = mysql_fetch_assoc($qTotal)['total'];

/* filtered */
$qFiltered = mysql_query("
    SELECT COUNT(*) AS total
    FROM weighing_print wp
    LEFT JOIN merchant m ON wp.print_merchant_id = m.merchant_id
    $where
");
$totalFiltered = mysql_fetch_assoc($qFiltered)['total'];

$sql = "
    SELECT wp.*, m.merchant_name, w.weighing_sts
    FROM weighing_print wp
    LEFT JOIN merchant m ON wp.print_merchant_id = m.merchant_id
    LEFT JOIN weighing w ON wp.weighing_id = w.weighing_id
    $where
    ORDER BY wp.print_date DESC
    LIMIT $start, $limit
";

$q = mysql_query($sql);

$data = [];
$no = $start + 1;

while($row = mysql_fetch_assoc($q)){
    $wid = sprintf("%07d", $row['weighing_id']);

    if($row['weighing_sts']=='Masuk' || $row['weighing_sts']=='Batal'){
        $nota = "<div style='background:#428bca;color:#fff;padding:3px;border-radius:5px;width:100px;text-align:center'>TB$wid</div>";
    } else {
        $nota = "<div style='background:green;color:#fff;padding:3px;border-radius:5px;width:100px;text-align:center'>TG$wid</div>";
    }

    $data[] = [
        $no++,
        $row['print_user'],
        $nota,
        $row['print_nopol'],
        $row['merchant_name'],
        $row['print_action'],
        date("H:i", strtotime($row['print_date'])),
        date("d M Y", strtotime($row['print_date']))
    ];
}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
exit;
