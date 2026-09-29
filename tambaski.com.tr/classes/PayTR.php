<?php
/**
 * PayTR Sanal POS & iFrame Entegrasyon Sınıfı
 */
class PayTR {
    private $merchantId;
    private $merchantKey;
    private $merchantSalt;
    private $testMode;

    public function __construct() {
        $this->merchantId = Helper::getSetting('paytr_merchant_id', '');
        $this->merchantKey = Helper::getSetting('paytr_merchant_key', '');
        $this->merchantSalt = Helper::getSetting('paytr_merchant_salt', '');
        $this->testMode = (int)Helper::getSetting('paytr_test_mode', '1');
    }

    /**
     * PayTR iFrame Token Alır
     */
    public function getToken($order, $userIp = null) {
        if (empty($this->merchantId) || empty($this->merchantKey) || empty($this->merchantSalt)) {
            return ['success' => false, 'error' => 'PayTR API bilgileri henüz yapılandırılmamış.'];
        }

        $email = $order['customer_email'];
        $paymentAmount = (int)round($order['total_amount'] * 100); // Kuruş cinsinden
        // PayTR merchant_oid alfanümerik olmalıdır, özel karakter içeremez
        $merchantOid = preg_replace('/[^a-zA-Z0-9]/', '', (string)$order['order_number']);
        if (empty($merchantOid)) {
            $merchantOid = 'TB' . $order['id'] . time();
        }
        $userName = $order['customer_name'];
        $address = $order['shipping_address'] . ' ' . $order['shipping_district'] . '/' . $order['shipping_city'];
        $phone = preg_replace('/[^0-9]/', '', $order['customer_phone']);

        $ip = $userIp;
        if (empty($ip)) {
            if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            }
        }
        $ip = trim($ip);
        if ($ip === '::1' || empty($ip)) {
            $ip = '127.0.0.1';
        }

        $merchantOkUrl = SITE_URL . '/success.php?order_number=' . $order['order_number'];
        $merchantFailUrl = SITE_URL . '/checkout.php?status=failed&order_number=' . $order['order_number'];

        // Sepet Kalemleri Dizisi (PayTR kuralı: Kalem toplamları kuruşu kuruşuna payment_amount'a eşit olmalıdır)
        $userBasket = [];
        $calculatedBasketTotal = 0;
        if (!empty($order['items'])) {
            foreach ($order['items'] as $item) {
                $itemPrice = round((float)$item['total_price'], 2);
                if ($itemPrice > 0) {
                    $calculatedBasketTotal += $itemPrice;
                    $userBasket[] = [
                        mb_substr($item['product_name'], 0, 45, 'UTF-8') . ' (' . $item['quantity'] . ' Adet)',
                        number_format($itemPrice, 2, '.', ''),
                        1
                    ];
                }
            }
        }

        $shippingFee = round((float)($order['shipping_fee'] ?? 0), 2);
        if ($shippingFee > 0) {
            $calculatedBasketTotal += $shippingFee;
            $userBasket[] = ['Kargo ve Teslimat Bedeli', number_format($shippingFee, 2, '.', ''), 1];
        }

        $orderTotal = round((float)$order['total_amount'], 2);
        $diff = round($orderTotal - $calculatedBasketTotal, 2);
        if (abs($diff) >= 0.01) {
            if ($diff > 0) {
                $userBasket[] = ['Hizmet / KDV Bedeli', number_format($diff, 2, '.', ''), 1];
            } else {
                // Eğer indirim nedeniyle sepet tutarı siparişten büyükse tek güvenli kalem olarak sipariş tutarını ilet
                $userBasket = [
                    ['TamBaskı Siparişi #' . $order['order_number'], number_format($orderTotal, 2, '.', ''), 1]
                ];
            }
        }

        if (empty($userBasket)) {
            $userBasket = [
                ['TamBaskı Siparişi #' . $order['order_number'], number_format($orderTotal, 2, '.', ''), 1]
            ];
        }

        $userBasketJson = base64_encode(json_encode($userBasket, JSON_UNESCAPED_UNICODE));

        $noInstallment = 0;
        $maxInstallment = 0; // 0 = Tüm taksit seçenekleri aktif

        // PayTR Token Hash: merchant_id + user_ip + merchant_oid + email + payment_amount + user_basket + no_installment + max_installment + currency + test_mode
        $hashStr = $this->merchantId . $ip . $merchantOid . $email . $paymentAmount . $userBasketJson . $noInstallment . $maxInstallment . "TL" . $this->testMode;
        $paytrToken = base64_encode(hash_hmac('sha256', $hashStr . $this->merchantSalt, $this->merchantKey, true));

        $postVals = [
            'merchant_id'       => $this->merchantId,
            'user_ip'           => $ip,
            'merchant_oid'      => $merchantOid,
            'email'             => $email,
            'payment_amount'    => $paymentAmount,
            'paytr_token'       => $paytrToken,
            'user_basket'       => $userBasketJson,
            'debug_on'          => $this->testMode,
            'no_installment'    => $noInstallment,
            'max_installment'   => $maxInstallment,
            'user_name'         => $userName,
            'user_address'      => $address,
            'user_phone'        => $phone,
            'merchant_ok_url'   => $merchantOkUrl,
            'merchant_fail_url' => $merchantFailUrl,
            'timeout_limit'     => '30',
            'currency'          => 'TL',
            'test_mode'         => $this->testMode
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postVals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            $err = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'error' => 'PayTR bağlantı hatası: ' . $err];
        }
        curl_close($ch);

        $res = json_decode($result, true);
        if ($res['status'] === 'success') {
            return [
                'success' => true,
                'token'   => $res['token'],
                'iframe_url' => 'https://www.paytr.com/odeme/guvenli/' . $res['token']
            ];
        }

        return ['success' => false, 'error' => $res['reason'] ?? 'PayTR token oluşturulamadı.'];
    }

    /**
     * PayTR Webhook Callback Doğrulaması
     */
    public function verifyCallback($postData) {
        $merchantOid = $postData['merchant_oid'] ?? '';
        $status = $postData['status'] ?? '';
        $totalAmount = $postData['total_amount'] ?? '';
        $hash = $postData['hash'] ?? '';

        $hashStr = $merchantOid . $this->merchantSalt . $status . $totalAmount;
        $expectedHash = base64_encode(hash_hmac('sha256', $hashStr, $this->merchantKey, true));

        if (hash_equals($expectedHash, $hash)) {
            return [
                'valid'        => true,
                'order_number' => $merchantOid,
                'status'       => $status, // 'success' or 'failed'
                'total_amount' => $totalAmount / 100
            ];
        }

        return ['valid' => false, 'error' => 'Geçersiz PayTR Callback İmzası (Hash mismatch)'];
    }
}
