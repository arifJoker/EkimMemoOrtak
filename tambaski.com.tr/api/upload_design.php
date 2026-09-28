<?php
/**
 * Ajax Müşteri Baskı Dosyası Yükleyici (PDF, AI, PSD, CDR, ZIP vb.)
 */
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz dosya yükleme isteği.'], 400);
}

$uploadRes = Helper::uploadFile(
    $_FILES['file'],
    'designs',
    ['pdf', 'ai', 'psd', 'cdr', 'eps', 'tiff', 'tif', 'zip', 'rar', '7z', 'jpg', 'jpeg', 'png', 'svg'],
    150 // 150 MB'a kadar destek
);

Helper::jsonResponse($uploadRes);
