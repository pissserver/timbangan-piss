<?php
    session_start();
    include "../connection.php";
    include "components/role_session.php";

    $id = $_POST['id'];

    // ambil data utama
    $q = mysql_query("SELECT * FROM weighing WHERE weighing_id = '$id'");
    $data = mysql_fetch_assoc($q);

    $html = "";

    /* =========================
    WRAPPER
    ========================= */
    $html .= "<div class='ref-container'>";

    /* =========================
    CASE 1: DIA CHILD
    ========================= */
    if ($data['weighing_sts2'] == 'Digabung' && $data['weighing_sts4'] == NULL) {

        $parent = mysql_query("
            SELECT parent_id 
            FROM reffer_weighing 
            WHERE child_id = '$id' 
            ORDER BY child_id ASC
        ");

        if (mysql_num_rows($parent) > 0) {

            $html .= "
            <div class='ref-card'>
                <div class='ref-title'>
                    <i class='fa fa-link'></i> Digabung ke Nota
                </div>
                <div class='ref-list'>
            ";

            while ($p = mysql_fetch_assoc($parent)) {
                $html .= "
                    <a href='check_edit.php?id=".$p['parent_id']."' target='_blank'
                    class='ref-badge badge-parent'>
                        TG".sprintf("%07d", $p['parent_id'])."
                    </a>
                ";
            }

            $html .= "</div></div>";

        } else {
            $html .= "
            <div class='ref-empty'>
                <i class='fa fa-info-circle'></i> Tidak ada relasi parent
            </div>
            ";
        }
    }

    /* =========================
    CASE 2: DIA PARENT
    ========================= */
    if ($data['weighing_sts2'] == 'Gabungan') {

        $child = mysql_query("
            SELECT child_id 
            FROM reffer_weighing 
            WHERE parent_id = '$id'
            ORDER BY child_id ASC
        ");

        if (mysql_num_rows($child) > 0) {

            $html .= "
            <div class='ref-card'>
                <div class='ref-title'>
                    <i class='fa fa-exchange'></i> Gabungan dari
                </div>
                <div class='ref-list'>
            ";

            while ($c = mysql_fetch_assoc($child)) {
                $html .= "
                    <a href='check_edit.php?id=".$c['child_id']."' target='_blank'
                    class='ref-badge badge-child'>
                        TG".sprintf("%07d", $c['child_id'])."
                    </a>
                ";
            }

            $html .= "</div></div>";
        }
    }

    /* =========================
    CASE 3: DIA PARENT DAN ADA CHILD (DARI GABUNGAN)
    ========================= */
    if ($data['weighing_sts2'] == 'Digabung' && $data['weighing_sts4'] == 'FromGabungan') {

        $child = mysql_query("
            SELECT child_id 
            FROM reffer_weighing 
            WHERE parent_id = '$id'
            ORDER BY child_id ASC
        ");

        if (mysql_num_rows($child) > 0) {

            $html .= "
            <div class='ref-card'>
                <div class='ref-title'>
                    <i class='fa fa-exchange'></i> Gabungan dari
                </div>
                <div class='ref-list'>
            ";

            while ($c = mysql_fetch_assoc($child)) {
                $html .= "
                    <a href='check_edit.php?id=".$c['child_id']."' target='_blank'
                    class='ref-badge badge-child'>
                        TG".sprintf("%07d", $c['child_id'])."
                    </a>
                ";
            }

            $html .= "</div></div>";
        }

        /* =========================
        DIA JUGA CHILD (CHAIN)
        ========================= */
        if ($data['weighing_sts2'] == 'Digabung') {

            $parent = mysql_query("
                SELECT parent_id 
                FROM reffer_weighing 
                WHERE child_id = '$id' 
                ORDER BY child_id ASC
            ");

            if (mysql_num_rows($parent) > 0) {

                $html .= "
                <div class='ref-card'>
                    <div class='ref-title'>
                        <i class='fa fa-arrow-up'></i> Digabung lagi ke
                    </div>
                    <div class='ref-list'>
                ";

                while ($p = mysql_fetch_assoc($parent)) {
                    $html .= "
                        <a href='check_edit.php?id=".$p['parent_id']."' target='_blank'
                        class='ref-badge badge-chain'>
                            TG".sprintf("%07d", $p['parent_id'])."
                        </a>
                    ";
                }

                $html .= "</div></div>";
            }
        }
    }



    /* =========================
    EMPTY GLOBAL (kalau kosong semua)
    ========================= */
    if ($html == "<div class='ref-container'>") {
        $html .= "
        <div class='ref-empty'>
            <i class='fa fa-folder-open'></i><br>
            Tidak ada relasi nota
        </div>
        ";
    }

    $html .= "</div>";

    echo $html;
?>