<?php
/**
 * iyzico Sanal POS & Checkout Form Entegrasyon Sınıfı
 * Paylaşımlı hostingde Composer/Vendor bağımlılığı gerektirmeyen bağımsız cURL motoru
 */
class Iyzico {
    private $apiKey;
    private $secretKey;
    private $baseUrl;

    public function __construct() {
        $this->apiKey = Helper::getSetting('iyzico_api_key', '');
        $this->secretKey = Helper::getSetting('iyzico_secret_key', '');
        $this->baseUrl = rtrim(Helper::getSetting('iyzico_base_url', 'https://sandbox-api.iyzipay.com'), '/');
    }

    /**
     * iyzico Checkout Form Başlatır
     */
    public function initializeCheckoutForm($order, $userIp = null) {
        if (empty($this->apiKey) || empty($this->secretKey)) {
            return ['success' => false, 'error' => 'iyzico API anahtarları yapılandırılmamış.'];
        }

        $ip = $userIp ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $callbackUrl = SITE_URL . '/api/iyzico_callback.php';

        $names = explode(' ', trim($order['customer_name']));
        $surname = count($names) > 1 ? array_pop($names) : 'Müşteri';
        $name = implode(' ', $names);

        $basketItems = [];
        if (!empty($order['items'])) {
            foreach ($order['items'] as $item) {
                $basketItems[] = [
                    'id'                => 'ITEM-' . $item['id'],
                    'name'              => mb_substr($item['product_name'], 0, 50),
                    'category1'         => 'Matbaa',
                    'itemType'          => 'PHYSICAL',
                    'price'             => number_format($item['total_price'], 2, '.', '')
                ];
            }
        } else {
            $basketItems[] = [
                'id'        => 'ITEM-1',
                'name'      => 'Baskı ve Matbaa Hizmeti',
                'category1' => 'Matbaa',
                'itemType'  => 'PHYSICAL',
                'price'     => number_format($order['total_amount'], 2, '.', '')
            ];
        }

        $requestData = [
            'locale'            => 'tr',
            'conversationId'    => $order['order_number'],
            'price'             => number_format($order['subtotal'], 2, '.', ''),
            'paidPrice'         => number_format($order['total_amount'], 2, '.', ''),
            'currency'          => 'TRY',
            'basketId'          => 'BASKET-' . $order['order_number'],
            'paymentGroup'      => 'PRODUCT',
            'callbackUrl'       => $callbackUrl,
            'enabledInstallments' => [2, 3, 6, 9, 12],
            'buyer' => [
                'id'            => 'USR-' . ($order['user_id'] ?: 'GUEST'),
                'name'          => $name ?: 'Musteri',
                'surname'       => $surname ?: 'Musteri',
                'gsmNumber'     => $order['customer_phone'] ?: '+905550000000',
                'email'         => $order['customer_email'],
                'identityNumber'=> $order['tax_number'] ?: '11111111111',
                'registrationAddress' => $order['shipping_address'] ?: 'Istanbul',
                'ip'            => $ip,
                'city'          => $order['shipping_city'] ?: 'Istanbul',
                'country'       => 'Turkey'
            ],
            'shippingAddress' => [
                'contactName'   => $order['customer_name'],
                'city'          => $order['shipping_city'] ?: 'Istanbul',
                'country'       => 'Turkey',
                'address'       => $order['shipping_address'] ?: 'Istanbul'
            ],
            'billingAddress' => [
                'contactName'   => $order['customer_name'],
                'city'          => $order['shipping_city'] ?: 'Istanbul',
                'country'       => 'Turkey',
                'address'       => $order['shipping_address'] ?: 'Istanbul'
            ],
            'basketItems' => $basketItems
        ];

        $jsonPayload = json_encode($requestData);
        $randomKey = uniqid();
        $authString = 'apiKey:' . $this->apiKey . '&randomKey:' . $randomKey . '&signature:' . base64_encode(sha1($this->apiKey . $randomKey . $this->secretKey . $jsonPayload, true));

        $ch = curl_init($this->baseUrl . '/payment/iyzipay/checkoutform/initialize/auth/ecom');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: IYZWS ' . $authString,
            'x-iyzi-rnd: ' . $randomKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response, true);
        if ($res && ($res['status'] ?? '') === 'success') {
            return [
                'success'           => true,
                'checkout_form_content' => $res['checkoutFormContent'],
                'token'             => $res['token'],
                'payment_page_url'  => $res['paymentPageUrl'] ?? null
            ];
        }

        return ['success' => false, 'error' => $res['errorMessage'] ?? 'iyzico ödeme formu başlatılamadı.'];
    }

    /**
     * iyzico Sonuç Doğrulaması
     */
    public function getPaymentResult($token) {
        $jsonPayload = json_encode(['locale' => 'tr', 'token' => $token]);
        $randomKey = uniqid();
        $authString = 'apiKey:' . $this->apiKey . '&randomKey:' . $randomKey . '&signature:' . base64_encode(sha1($this->apiKey . $randomKey . $this->secretKey . $jsonPayload, true));

        $ch = curl_init($this->baseUrl . '/payment/iyzipay/checkoutform/auth/ecom/detail');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: IYZWS ' . $authString,
            'x-iyzi-rnd: ' . $randomKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);
        curl_close($ch);

        $res = json_decode($response, true);
        if ($res && ($res['status'] ?? '') === 'success' && ($res['paymentStatus'] ?? '') === 'SUCCESS') {
            return [
                'success'           => true,
                'order_number'      => $res['conversationId'],
                'payment_id'        => $res['paymentId'],
                'price'             => $res['paidPrice']
            ];
        }

        return ['success' => false, 'error' => $res['errorMessage'] ?? 'Ödeme doğrulanamadı.'];
    }
}
