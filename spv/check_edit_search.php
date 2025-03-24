<?php
    include 'connection.php';

    $id = $_GET['search_id'];

    $sql = mysql_query("select * from weighing where weighing_id = '".$id."'");

    if ($sql && mysql_num_rows($sql) > 0) {
        header("Location: check_edit.php?id=" . $id);
        exit;
    } else {
        echo "<script>window.alert('No Nota ".$id." tidak ada!');
			window.history.back()</script>";
			exit();
    }
?>
