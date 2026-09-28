<?php
/**
 * Ajax Sepet İşlemleri (Ekle, Sil, Kupon)
 */
require_once __DIR__ . '/../config/config.php';

$action = $_POST['action'] ?? ($_GET['action'] ?? '');
$cart = new Cart();

switch ($action) {
    case 'add':
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 100);
        $options = $_POST['options'] ?? [];
        $customSize = !empty($_POST['custom_width']) && !empty($_POST['custom_height']) ? [
            'width'  => (float)$_POST['custom_width'],
            'height' => (float)$_POST['custom_height']
        ] : null;

        $designData = [
            'type'    => $_POST['design_type'] ?? 'none',
            'file'    => $_POST['design_file'] ?? null,
            'svg'     => $_POST['design_svg'] ?? null,
            'preview' => $_POST['design_preview'] ?? null,
            'notes'   => $_POST['design_notes'] ?? null
        ];

        $selectedPackage = trim($_POST['selected_package'] ?? $_POST['package'] ?? 'standart');
        $customPaperId = (int)($_POST['custom_paper_id'] ?? 0);

        $res = $cart->add($productId, $quantity, $options, $customSize, $designData, $selectedPackage, $customPaperId);
        Helper::jsonResponse($res);
        break;

    case 'remove':
        $itemId = (int)($_POST['item_id'] ?? 0);
        $res = $cart->remove($itemId);
        $summary = $cart->getSummary();
        Helper::jsonResponse(['success' => $res, 'summary' => $summary]);
        break;

    case 'apply_coupon':
        $code = trim($_POST['code'] ?? '');
        $res = $cart->applyCoupon($code);
        Helper::jsonResponse($res);
        break;

    case 'remove_coupon':
        $res = $cart->removeCoupon();
        Helper::jsonResponse($res);
        break;

    case 'get_summary':
        $summary = $cart->getSummary();
        Helper::jsonResponse(['success' => true, 'summary' => $summary]);
        break;

    default:
        Helper::jsonResponse(['success' => false, 'error' => 'Geçersiz eylem.'], 400);
        break;
}
