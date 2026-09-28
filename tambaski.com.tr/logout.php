<?php
require_once __DIR__ . '/config/config.php';
Auth::logout();
Helper::setFlash('info', 'Başarıyla çıkış yaptınız.');
header("Location: " . SITE_URL . "/");
exit;
