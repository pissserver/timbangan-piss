<?php
session_start();
include "connection.php";

// simpan redirect jika dikirim lewat URL
if (isset($_POST['redirect']) && $_POST['redirect'] != '') {
    $_SESSION['redirect_after_login'] = $_POST['redirect'];
}

if (!isset($_POST["user"]) || !isset($_POST["pass"])) {
    echo "<script>alert('Harap masukkan username dan password!'); window.location='login.html';</script>";
    exit();
}

$user = mysql_real_escape_string(trim($_POST["user"]));
$pass = mysql_real_escape_string(trim($_POST["pass"]));

// ============================
// LOGIN CASHIER (Checker)
// ============================

$login = mysql_query("SELECT checker_name, checker_code, checker_role FROM checker 
                      WHERE checker_code = '$user' AND checker_pass = '$pass' 
                      AND checker_sts = 'Aktif' AND status_aktif = 1 AND checker_role = 'Cashier'");

if (mysql_num_rows($login) == 1) {
    $r = mysql_fetch_array($login);
    $_SESSION['checker'] = $r['checker_code'];
    $_SESSION['role'] = $r['checker_role'];
    $_SESSION['show_welcome_modal'] = true;

    include "components/welcome_animation.php";
    echo "<script>setTimeout(function() { window.location.href = 'dashboard.php'; }, 3250);</script>";
    exit();
}


// ============================
// LOGIN ADMIN (PP, KEUANGAN, SPV)
// ============================

$login2 = mysql_query("SELECT id_admin, username, role FROM admin WHERE username = '$user' AND password = '$pass'");
if (mysql_num_rows($login2) == 1) {
    $role = mysql_fetch_array($login2);
    $_SESSION['id_admin'] = $role['id_admin'];
    $_SESSION['username'] = $role['username'];
    $_SESSION['role'] = $role['role'];
    $_SESSION['show_welcome_modal'] = true;

    switch (strtolower($role['role'])) {
        case 'pp':
            // Ambil redirect jika ada (hanya berlaku untuk PP)
            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : null;
            unset($_SESSION['redirect_after_login']);
            
            if ($redirect) {
                if (strpos($redirect, 'pp/') !== 0) {
                    $redirect = 'pp/' . $redirect;
                }
                include "pp/components/welcome_animation.php";
                echo "<script>setTimeout(function() { window.location.href = 'pp/developer.php'; }, 3250);</script>";
                exit();
            } else {
                $redirect = 'pp/dashboard.php';
                include "pp/components/welcome_animation.php";
                echo "<script>setTimeout(function() { window.location.href = 'pp/dashboard.php'; }, 3250);</script>";
                exit();
            }
        case 'keuangan':
            include "kk/components/welcome_animation.php";
            echo "<script>setTimeout(function() { window.location.href = 'kk/dashboard.php'; }, 3250);</script>";
            break;
        case 'spv':
            include "spv/components/welcome_animation.php";
            echo "<script>setTimeout(function() { window.location.href = 'spv/dashboard.php'; }, 3250);</script>";
            break;
        default:
            echo "<script>alert('Anda Tidak Memiliki Hak Untuk Masuk!'); window.location='login.html';</script>";
    }
    exit();
}

echo "<script>alert('Username atau password salah!'); window.location='login.html';</script>";
exit();
