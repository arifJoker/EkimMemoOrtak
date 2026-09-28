/**
 * TamBaskı - Dinamik 3D Kartvizit Doku, Video & Varak/Lak Simülasyon Motoru
 * Her baskı paketi (Ekonomik, Standart, Premium, VIP) için doku, kalınlık ve ışık efektlerini canlandırır.
 */

(function(window) {
    'use strict';

    const PACKAGE_CONFIGS = {
        ekonomik: {
            title: 'Ekonomik Paket (350gr Düz Kesim)',
            gsm: '350 GSM',
            thickness: '0.35 mm',
            finish: 'Mat Selefon Kaplama',
            corners: 'Standart Düz (90°)',
            features: ['350gr Mat Kuşe', 'Tek Yön Renkli Baskı', 'Düz Bıçak Kesim', 'Hızlı Heidelberg Ofset'],
            badge: 'En Uygun Fiyat',
            badgeBg: '#10b981',
            cardBg: 'linear-gradient(135deg, #ffffff 0%, #f8fafc 100%)',
            textColor: '#0f172a',
            subColor: '#64748b',
            accentColor: '#0071e3',
            foilEffect: false,
            lakEffect: false,
            textureType: 'smooth'
        },
        standart: {
            title: 'Standart Paket (Çift Yön Mat, Oval Köşe)',
            gsm: '350 GSM',
            thickness: '0.38 mm',
            finish: 'Çift Taraf Mat Selefon (Su İtici)',
            corners: 'Oval Köşe Kesim (4R Radyus)',
            features: ['350gr 1. Sınıf Kuşe', 'Çift Taraf Renkli Baskı', 'Çift Taraf Mat Koruma', 'Oval Kesim'],
            badge: 'En Çok Satan',
            badgeBg: '#0071e3',
            cardBg: 'linear-gradient(135deg, #ffffff 0%, #f1f5f9 100%)',
            textColor: '#0f172a',
            subColor: '#475569',
            accentColor: '#0071e3',
            foilEffect: false,
            lakEffect: false,
            textureType: 'smooth_oval'
        },
        premium: {
            title: 'Premium Paket (Kadife Soft-Touch & Kabartma Lak)',
            gsm: '450 GSM',
            thickness: '0.55 mm Tok Karton',
            finish: 'Soft-Touch Kadife Selefon & Bölgesel Kabartma Lak',
            corners: 'Özel Düz / Oval Seçenekli',
            features: ['450gr Ekstra Tok Kuşe', 'Kadife Dokulu Soft-Touch', '3D Kabartmalı Parlak Lak', 'Lüks Dokunsal His'],
            badge: 'Lüks Kadife Doku',
            badgeBg: '#8b5cf6',
            cardBg: 'linear-gradient(135deg, #18181b 0%, #09090b 100%)',
            textColor: '#ffffff',
            subColor: '#a1a1aa',
            accentColor: '#8b5cf6',
            foilEffect: false,
            lakEffect: true,
            textureType: 'velvet_spot_uv'
        },
        vip: {
            title: 'VIP Prestij Paket (24K Altın Varak & Tuale)',
            gsm: '700 GSM Triplex',
            thickness: '0.85 mm Ultra Kalın Sıvama',
            finish: '24K Ayna Altın Varak Yaldız & İtalyan Tuale',
            corners: 'Altın Yaldız Kenar Boyama',
            features: ['700gr Sıvamalı Ağır Karton', '24K Parlak Altın Varak', 'İtalyan Tuale Dokulu Yüzey', 'Altın Kenar Yaldızı'],
            badge: 'Maksimum Prestij',
            badgeBg: '#d97706',
            cardBg: 'radial-gradient(circle, #faf6ee 0%, #f3ece0 100%)',
            textColor: '#78350f',
            subColor: '#92400e',
            accentColor: '#d97706',
            foilEffect: true,
            lakEffect: false,
            textureType: 'gold_foil_tuale'
        }
    };

    const PackageShowcase = {
        currentPackage: 'standart',
        isHovered: false,

        init: function() {
            this.bindInteractiveEffects();
        },

        setPackage: function(pkgKey) {
            if (!PACKAGE_CONFIGS[pkgKey]) pkgKey = 'standart';
            this.currentPackage = pkgKey;
            const cfg = PACKAGE_CONFIGS[pkgKey];

            // 1. Dinamik Kart Sahnesini Güncelle
            const stage = document.getElementById('interactivePackageCard');
            if (stage) {
                stage.className = 'interactive-showcase-card pkg-' + pkgKey;
                stage.style.background = cfg.cardBg;
                
                // Kalınlık ve Köşe Sınıfları
                if (pkgKey === 'standart') {
                    stage.style.borderRadius = '14px';
                } else if (pkgKey === 'vip') {
                    stage.style.borderRadius = '8px';
                    stage.style.border = '2px solid #d97706';
                } else {
                    stage.style.borderRadius = '3px';
                    stage.style.border = '1px solid #e2e8f0';
                }
            }

            // 2. Kalınlık Gösterge Rozeti
            const thickBadge = document.getElementById('showcaseThicknessBadge');
            if (thickBadge) {
                thickBadge.innerHTML = `<i class="bi bi-layers-half text-warning me-1"></i> Kalınlık: <strong>${cfg.thickness} (${cfg.gsm})</strong>`;
            }

            // 3. Yüzey Efekti Rozeti
            const finishBadge = document.getElementById('showcaseFinishBadge');
            if (finishBadge) {
                finishBadge.innerHTML = `<i class="bi bi-stars text-primary me-1"></i> Doku: <strong>${cfg.finish}</strong>`;
            }

            // 4. Kart İçi Başlık & Logo
            const logoImg = document.getElementById('showcaseLogoImg');
            if (logoImg) {
                if (pkgKey === 'vip') {
                    logoImg.style.filter = 'sepia(1) saturate(5) hue-rotate(5deg) brightness(0.9) drop-shadow(0 2px 4px rgba(217,119,6,0.5))';
                } else if (pkgKey === 'premium') {
                    logoImg.style.filter = 'brightness(0) invert(1) drop-shadow(0 2px 6px rgba(168,85,247,0.7))';
                } else {
                    logoImg.style.filter = 'none';
                }
            }
        },

        bindInteractiveEffects: function() {
            const card = document.getElementById('interactivePackageCard');
            if (!card) return;

            const handleMove = (e) => {
                const rect = card.getBoundingClientRect();
                const x = (e.clientX || (e.touches && e.touches[0].clientX)) - rect.left;
                const y = (e.clientY || (e.touches && e.touches[0].clientY)) - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const rotateX = ((y - centerY) / centerY) * -12;
                const rotateY = ((x - centerX) / centerX) * 14;

                card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.03, 1.03, 1.03)`;

                // Işık Yansıması & Varak Parıltısı Pozisyonu
                const lightBeam = document.getElementById('cardLightGleam');
                if (lightBeam) {
                    const percentX = (x / rect.width) * 100;
                    const percentY = (y / rect.height) * 100;
                    lightBeam.style.background = `radial-gradient(circle at ${percentX}% ${percentY}%, rgba(255,255,255,0.6) 0%, rgba(255,255,255,0) 60%)`;
                }
            };

            const handleLeave = () => {
                card.style.transform = 'perspective(800px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
                const lightBeam = document.getElementById('cardLightGleam');
                if (lightBeam) {
                    lightBeam.style.background = 'transparent';
                }
            };

            card.addEventListener('mousemove', handleMove);
            card.addEventListener('mouseleave', handleLeave);
            card.addEventListener('touchmove', handleMove, { passive: true });
            card.addEventListener('touchend', handleLeave);
        },

        playCinematicVideo: function() {
            const modalEl = document.getElementById('packageVideoModal');
            if (!modalEl) return;

            const cfg = PACKAGE_CONFIGS[this.currentPackage] || PACKAGE_CONFIGS.vip;
            const modalTitle = document.getElementById('pkgVideoModalTitle');
            if (modalTitle) modalTitle.textContent = `${cfg.title} • Sinematik 3D Video`;

            const videoPlayer = document.getElementById('packageModalVideoPlayer');
            const siteUrl = window.SITE_URL || '';
            const videoUrl = this.currentPackage === 'premium' 
                ? (siteUrl + '/uploads/videos/tambaski_premium_showcase.mp4') 
                : (siteUrl + '/uploads/videos/tambaski_vip_showcase.mp4');
            
            if (videoPlayer) {
                videoPlayer.src = videoUrl;
                videoPlayer.play().catch(() => {});
            }

            if (window.bootstrap && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance ? bootstrap.Modal.getOrCreateInstance(modalEl) : new bootstrap.Modal(modalEl);
                modal.show();
            }
        }
    };

    window.stopPackageModalVideo = function() {
        const videoPlayer = document.getElementById('packageModalVideoPlayer');
        if (videoPlayer) {
            videoPlayer.pause();
            videoPlayer.src = '';
        }
    };

    window.PackageShowcase = PackageShowcase;

})(window);
