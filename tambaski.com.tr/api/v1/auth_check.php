<?php
/**
 * REST API v1 Kimlik Doğrulama Middleware
 */
require_once __DIR__ . '/../../config/config.php';

header('Content-Type: application/json; charset=utf-8');

// Header'dan veya Query'den API Key al
$apiKey = $_SERVER['HTTP_X_API_KEY'] ?? null;
if (!$apiKey && isset($_SERVER['HTTP_AUTHORIZATION'])) {
    if (preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
        $apiKey = $matches[1];
    }
}
if (!$apiKey) {
    $apiKey = $_GET['api_key'] ?? null;
}

if (!$apiKey) {
    Helper::jsonResponse([
        'success' => false,
        'error'   => 'Yetkisiz erişim. Lütfen "X-API-Key" header veya "Authorization: Bearer <API_KEY>" ile API anahtarınızı iletin.',
        'docs'    => SITE_URL . '/api/v1/'
    ], 401);
}

$authRes = Auth::verifyApiKey($apiKey);
if (!$authRes['valid']) {
    Helper::jsonResponse([
        'success' => false,
        'error'   => $authRes['error'] ?? 'Geçersiz API Anahtarı.'
    ], 403);
}

$currentApiKeyData = $authRes['data'];
