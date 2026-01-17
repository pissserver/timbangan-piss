<?php
include "../connection.php";
error_reporting(0); // WAJIB supaya warning tidak merusak JSON

$limit  = intval($_POST['length']);
$start  = intval($_POST['start']);
$draw   = intval($_POST['draw']);
$search = mysql_real_escape_string($_POST['search']['value']);

$where = " WHERE DATE(c.action_time) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) ";

if($search != ""){
    $where .= " AND (
        c.cashier_user LIKE '%$search%' OR
        c.cashier_nopol LIKE '%$search%' OR
        c.cashier_action LIKE '%$search%' OR
        c.cashier_reason LIKE '%$search%' OR
        m.merchant_name LIKE '%$search%'
    ) ";
}

/* TOTAL FILTERED */
$qFiltered = mysql_query("
    SELECT COUNT(*) AS total
    FROM cashier_log c
    LEFT JOIN merchant m ON c.cashier_merchant_id = m.merchant_id
    $where
");
$totalFiltered = mysql_fetch_assoc($qFiltered)['total'];

/* TOTAL ALL (30 hari) */
$qTotal = mysql_query("
    SELECT COUNT(*) AS total
    FROM cashier_log
    WHERE DATE(action_time) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");
$totalData = mysql_fetch_assoc($qTotal)['total'];

/* DATA */
$sql = "
    SELECT c.*, m.merchant_name, w.weighing_sts
    FROM cashier_log c
    LEFT JOIN merchant m ON c.cashier_merchant_id = m.merchant_id
    LEFT JOIN weighing w ON c.cashier_target = w.weighing_id
    $where
    ORDER BY c.action_time DESC
    LIMIT $start, $limit
";

$q = mysql_query($sql);

$data = [];
$no = $start + 1;

while($row = mysql_fetch_assoc($q)){
    $wid = sprintf("%07d", $row['cashier_target']);
    
    $hint = "<div style='text-align:center;color:red;font-size:10px;margin-bottom:4px'>*Klik untuk tinjau nota</div>";

    if($row['weighing_sts']=='Masuk' || $row['weighing_sts']=='Batal'){
        $link = "<a href='listing.php?id_nota=$wid&task=preview' target='_blank' style='color:#fff'>TB$wid</a>";
        $badge = "<div style='background:#428bca;color:#fff;padding:3px 10px;border-radius:5px;display:inline-block;'>$link</div>";
    } else {
        $link = "<a href='listing.php?id_nota=$wid&task=preview' target='_blank' style='color:#fff'>TG$wid</a>";
        $badge = "<div style='background:#28a745;color:#fff;padding:3px 10px;border-radius:5px;display:inline-block;'>$link</div>";
    }

    $nota = "
        <div style='text-align:center'>
            $hint
            $badge
        </div>
    ";

    $data[] = [
        "<div style='text-align:center'>".$no++."</div>",
        "<div style='text-align:center'>".$row['cashier_user']."</div>",
        "<div style='text-align:center'>".$nota."</div>",
        "<div style='text-align:center'>".$row['cashier_nopol']."</div>",
        $row['merchant_name'],
        $row['cashier_action'],
        $row['cashier_reason'] ?: "Tidak ada keterangan",
        "<div style='text-align:center'>".date("H:i", strtotime($row['action_time']))."</div>",
        "<div style='text-align:center'>".date("d M Y", strtotime($row['action_time']))."</div>"
    ];


}

echo json_encode([
    "draw" => $draw,
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
]);
exit;
?>
