<?php
/**
 * TAMBASKI.COM.TR - Admin Çıkış İşlemi
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_user']);
header("Location: login.php");
exit;
