<?php
/**
 * Kargo Anlaşmaları, Takip Linkleri ve Barkod Sınıfı
 */
class Cargo {
    /**
     * Kargo Firmaları ve Takip URL Şablonları
     */
    public static function getCompanies() {
        return [
            'Yurtiçi Kargo' => [
                'name' => 'Yurtiçi Kargo',
                'tracking_url' => 'https://www.yurticikargo.com/tr/online-servisler/gonderi-sorgula?code={CODE}',
                'icon' => 'bi bi-truck'
            ],
            'Aras Kargo' => [
                'name' => 'Aras Kargo',
                'tracking_url' => 'https://www.araskargo.com.tr/kargotakip?code={CODE}',
                'icon' => 'bi bi-truck'
            ],
            'MNG Kargo' => [
                'name' => 'MNG Kargo',
                'tracking_url' => 'https://www.mngkargo.com.tr/gonderitakip/{CODE}',
                'icon' => 'bi bi-truck'
            ],
            'Sürat Kargo' => [
                'name' => 'Sürat Kargo',
                'tracking_url' => 'https://www.suratkargo.com.tr/KargoTakip/?kargotakipno={CODE}',
                'icon' => 'bi bi-truck'
            ],
            'PTT Kargo' => [
                'name' => 'PTT Kargo',
                'tracking_url' => 'https://gonderitakip.ptt.gov.tr/Track/Verify?q={CODE}',
                'icon' => 'bi bi-truck'
            ],
            'HepsiJET' => [
                'name' => 'HepsiJET',
                'tracking_url' => 'https://www.hepsijet.com/gonderi-takibi/{CODE}',
                'icon' => 'bi bi-truck'
            ],
            'Sendeo' => [
                'name' => 'Sendeo',
                'tracking_url' => 'https://kargotakip.sendeo.com.tr/kargo-takip/{CODE}',
                'icon' => 'bi bi-truck'
            ]
        ];
    }

    /**
     * Takip Koduna Göre Canlı Kargo Takip Linki Üretici
     */
    public static function getTrackingLink($companyName, $trackingCode) {
        if (empty($trackingCode)) return null;

        $companies = self::getCompanies();
        if (isset($companies[$companyName])) {
            return str_replace('{CODE}', urlencode(trim($trackingCode)), $companies[$companyName]['tracking_url']);
        }

        return 'https://www.google.com/search?q=' . urlencode($companyName . ' ' . $trackingCode . ' kargo takip');
    }
}
