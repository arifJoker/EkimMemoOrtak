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
        $merchantOid = $order['order_number'];
        $userName = $order['customer_name'];
        $address = $order['shipping_address'] . ' ' . $order['shipping_district'] . '/' . $order['shipping_city'];
        $phone = preg_replace('/[^0-9]/', '', $order['customer_phone']);

        $ip = $userIp ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $merchantOkUrl = SITE_URL . '/success.php?order_number=' . $order['order_number'];
        $merchantFailUrl = SITE_URL . '/checkout.php?status=failed&order_number=' . $order['order_number'];

        // Sepet Kalemleri Dizisi
        $userBasket = [];
        if (!empty($order['items'])) {
            foreach ($order['items'] as $item) {
                $userBasket[] = [
                    $item['product_name'] . ' (' . $item['quantity'] . ' Adet)',
                    number_format($item['total_price'], 2, '.', ''),
                    1
                ];
            }
        } else {
            $userBasket[] = ['Online Matbaa Siparişi', number_format($order['total_amount'], 2, '.', ''), 1];
        }

        $userBasketJson = base64_encode(json_encode($userBasket));

        // Karmaşık Token Hash Formülü
        $hashStr = $this->merchantId . $ip . $merchantOid . $email . $paymentAmount . $userBasketJson . "0" . "0" . "TL" . $this->testMode;
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
            'no_installment'    => 0,
            'max_installment'   => 12,
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
