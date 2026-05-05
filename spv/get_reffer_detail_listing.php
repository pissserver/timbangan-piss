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

    // * KONDISI 1: Dipecah, Digabung
    /* =========================
    CASE 1: DIA CHILD (PECAHAN DARI NOTA LAIN) lalu Digabung ke Nota Lain (CHAIN)
    ========================= */
    if ($data['weighing_sts4'] == 'FromPecahan') {

        $parent1 = mysql_query("
            SELECT parent_id 
            FROM reffer_weighing 
            WHERE child_id = '$id'
            ORDER BY id ASC limit 1
        ");

        if (mysql_num_rows($parent1) > 0) {

            $html .= "
            <div class='ref-card'>
                <div class='ref-title'>
                    <i class='fa fa-exchange'></i> Pecahan dari nota
                </div>
                <div class='ref-list'>
            ";

            while ($c = mysql_fetch_assoc($parent1)) {
                $html .= "
                    <a href='listing.php?id_nota=".$c['parent_id']."' target='_blank'
                    class='ref-badge badge-child'>
                        TG".sprintf("%07d", $c['parent_id'])."
                    </a>
                ";
            }

            $html .= "</div></div>";
        }

        /* =========================
        DIA JUGA CHILD (CHAIN)
        ========================= */
        if ($data['weighing_sts2'] == 'Digabung') {

            $parent2 = mysql_query("
                SELECT parent_id 
                FROM reffer_weighing 
                WHERE child_id = '$id' 
                ORDER BY id ASC
                LIMIT 1 OFFSET 1
            ");

            if (mysql_num_rows($parent2) > 0) {

                $html .= "
                <div class='ref-card'>
                    <div class='ref-title'>
                        <i class='fa fa-arrow-up'></i> Digabung ke nota
                    </div>
                    <div class='ref-list'>
                ";

                while ($p = mysql_fetch_assoc($parent2)) {
                    $html .= "
                        <a href='listing.php?id_nota=".$p['parent_id']."' target='_blank'
                        class='ref-badge badge-chain'>
                            TG".sprintf("%07d", $p['parent_id'])."
                        </a>
                    ";
                }

                $html .= "</div></div>";
            }
        }
    }

    // * KONDISI 2: Digabung, Gabungan, FromGabungan
    /* =========================
    CASE 1: DIA CHILD
    ========================= */
    if ($data['weighing_sts2'] == 'Digabung' && $data['weighing_sts4'] == NULL) {

        $parent = mysql_query("
            SELECT parent_id 
            FROM reffer_weighing 
            WHERE child_id = '$id' 
            ORDER BY child_id ASC
            LIMIT 1
        ");

        if (mysql_num_rows($parent) > 0) {

            $html .= "
            <div class='ref-card'>
                <div class='ref-title'>
                    <i class='fa fa-link'></i> Digabung ke nota
                </div>
                <div class='ref-list'>
            ";

            while ($p = mysql_fetch_assoc($parent)) {
                $html .= "
                    <a href='listing.php?id_nota=".$p['parent_id']."' target='_blank'
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
                    <i class='fa fa-exchange'></i> Gabungan dari nota
                </div>
                <div class='ref-list'>
            ";

            while ($c = mysql_fetch_assoc($child)) {
                $html .= "
                    <a href='listing.php?id_nota=".$c['child_id']."' target='_blank'
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
                    <i class='fa fa-exchange'></i> Gabungan dari nota
                </div>
                <div class='ref-list'>
            ";

            while ($c = mysql_fetch_assoc($child)) {
                $html .= "
                    <a href='listing.php?id_nota=".$c['child_id']."' target='_blank'
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
                limit 1
            ");

            if (mysql_num_rows($parent) > 0) {

                $html .= "
                <div class='ref-card'>
                    <div class='ref-title'>
                        <i class='fa fa-arrow-up'></i> Digabung lagi ke nota
                    </div>
                    <div class='ref-list'>
                ";

                while ($p = mysql_fetch_assoc($parent)) {
                    $html .= "
                        <a href='listing.php?id_nota=".$p['parent_id']."' target='_blank'
                        class='ref-badge badge-chain'>
                            TG".sprintf("%07d", $p['parent_id'])."
                        </a>
                    ";
                }

                $html .= "</div></div>";
            }
        }
    }

    // * KONDISI 3: Dipecah, Pecahan
    /* =========================
    CASE 1: DIA PARENT
    ========================= */
    if ($data['weighing_sts2'] == 'Dipecah') {

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
                    <i class='fa fa-exchange'></i> Dipecah menjadi nota
                </div>
                <div class='ref-list'>
            ";

            while ($c = mysql_fetch_assoc($child)) {
                $html .= "
                    <a href='listing.php?id_nota=".$c['child_id']."' target='_blank'
                    class='ref-badge badge-child'>
                        TG".sprintf("%07d", $c['child_id'])."
                    </a>
                ";
            }

            $html .= "</div></div>";
        }
    }

    /* =========================
    CASE 2: DIA PARENT
    ========================= */
    if ($data['weighing_sts2'] == 'Pecahan') {

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
                    <i class='fa fa-link'></i> Pecahan dari Nota
                </div>
                <div class='ref-list'>
            ";

            while ($p = mysql_fetch_assoc($parent)) {
                $html .= "
                    <a href='listing.php?id_nota=".$p['parent_id']."' target='_blank'
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



    $html .= "</div>";

    echo $html;
?>