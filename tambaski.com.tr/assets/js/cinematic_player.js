/**
 * TamBaskı - 60 FPS HTML5 Canvas Sinematik Kartvizit Video Simülatörü
 * Gerçek zamanlı 360 derece döndürme, altın varak ışık taraması ve doku simülasyonu sunar.
 */

(function(window) {
    'use strict';

    let animFrameId = null;
    let canvas = null;
    let ctx = null;
    let angle = 0;
    let lightSweep = 0;
    let activePackage = 'vip';

    function initCanvas() {
        canvas = document.getElementById('cinematicVideoCanvas');
        if (!canvas) return;
        ctx = canvas.getContext('2d');
    }

    function renderScene() {
        if (!ctx || !canvas) return;

        const w = canvas.width;
        const h = canvas.height;

        // Arka Plan: Koyu Lüks Stüdyo Işıklandırması
        const bgGrad = ctx.createRadialGradient(w/2, h/2, 40, w/2, h/2, w/1.2);
        bgGrad.addColorStop(0, '#1e293b');
        bgGrad.addColorStop(1, '#090d16');
        ctx.fillStyle = bgGrad;
        ctx.fillRect(0, 0, w, h);

        // Stüdyo Zemin Gölgelendirmesi
        ctx.save();
        ctx.translate(w/2, h/2 + 130);
        ctx.scale(1, 0.25);
        ctx.beginPath();
        ctx.arc(0, 0, 160 + Math.sin(angle) * 15, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(0, 0, 0, 0.45)';
        ctx.filter = 'blur(14px)';
        ctx.fill();
        ctx.filter = 'none';
        ctx.restore();

        // 3D Dönen Kartvizit Gövdesi
        ctx.save();
        ctx.translate(w/2, h/2 - 10);

        // Sinematik Salınım & Dönüş Açısı
        const cardAngle = Math.sin(angle) * 0.45;
        const tiltY = Math.cos(angle * 0.7) * 0.15;
        const scaleX = Math.cos(cardAngle);

        ctx.transform(scaleX, tiltY, 0, 1, 0, 0);

        const cardW = 340;
        const cardH = 200;
        const radius = activePackage === 'standart' ? 16 : (activePackage === 'vip' ? 10 : 4);

        // Kart Kalınlığı & Kenar Sıvaması Çizimi (3D Edge Depth)
        let edgeDepth = 4;
        let edgeColor = '#cbd5e1';
        if (activePackage === 'vip') {
            edgeDepth = 8; // 700gr Triplex Kalın Kart
            edgeColor = '#d4af37'; // 24K Altın Kenar Yaldızı
        } else if (activePackage === 'premium') {
            edgeDepth = 6;
            edgeColor = '#27272a';
        }

        for (let i = edgeDepth; i > 0; i--) {
            ctx.beginPath();
            ctx.roundRect(-cardW/2 + (scaleX > 0 ? -i : i), -cardH/2 + i, cardW, cardH, radius);
            ctx.fillStyle = edgeColor;
            ctx.fill();
        }

        // Kart Ön Yüzü
        ctx.beginPath();
        ctx.roundRect(-cardW/2, -cardH/2, cardW, cardH, radius);

        let corporateLogo = window.__tbCorporateLogo;
        if (!corporateLogo) {
            corporateLogo = new Image();
            corporateLogo.src = (window.SITE_URL || '') + '/assets/img/logo.svg';
            window.__tbCorporateLogo = corporateLogo;
        }

        if (activePackage === 'vip') {
            // VIP: İtalyan Tuale & 24K Altın Varak
            ctx.fillStyle = '#fbf9f4';
            ctx.fill();
            ctx.lineWidth = 2.5;
            ctx.strokeStyle = '#d4af37';
            ctx.stroke();

            // Altın Çerçeve
            ctx.strokeRect(-cardW/2 + 15, -cardH/2 + 15, cardW - 30, cardH - 30);

            // Gerçek Logo Çizimi (Varsa)
            if (corporateLogo.complete && corporateLogo.naturalWidth > 0) {
                ctx.drawImage(corporateLogo, -85, -70, 170, 36);
            } else {
                ctx.fillStyle = '#b45309';
                ctx.font = 'bold 20px "Montserrat", sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('TAM BASKI', 0, -45);
            }

            ctx.fillStyle = '#d97706';
            ctx.font = 'bold 11px "Inter", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('24K GOLD FOIL • 700 GSM VIP', 0, -20);

            ctx.fillStyle = '#451a03';
            ctx.font = 'bold 16px "Playfair Display", serif';
            ctx.fillText('Arif Uz', 0, 15);

            ctx.fillStyle = '#78350f';
            ctx.font = '11px "Inter", sans-serif';
            ctx.fillText('Yönetim Kurulu Başkanı', 0, 35);
            ctx.fillText('www.tambaski.com.tr', 0, 68);

        } else if (activePackage === 'premium') {
            // Premium: Soft-Touch Siyah Kadife & 3D Kabartma Lak
            ctx.fillStyle = '#121215';
            ctx.fill();

            // Gerçek Logo Çizimi (Spot UV Parlak)
            if (corporateLogo.complete && corporateLogo.naturalWidth > 0) {
                ctx.drawImage(corporateLogo, -85, -70, 170, 36);
            } else {
                ctx.fillStyle = '#ffffff';
                ctx.font = 'bold 20px "Montserrat", sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('TAM BASKI', 0, -45);
            }

            ctx.fillStyle = '#f15a24';
            ctx.font = 'bold 11px "Inter", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('SOFT-TOUCH VELVET • 3D SPOT UV', 0, -20);

            ctx.fillStyle = '#f4f4f5';
            ctx.font = 'bold 15px "Montserrat", sans-serif';
            ctx.fillText('Murat Sancak', 0, 15);

            ctx.fillStyle = '#a1a1aa';
            ctx.font = '11px "Inter", sans-serif';
            ctx.fillText('Kreatif Direktör', 0, 35);
            ctx.fillText('www.tambaski.com.tr', 0, 68);

        } else {
            // Ekonomik / Standart
            ctx.fillStyle = '#ffffff';
            ctx.fill();
            ctx.lineWidth = 1;
            ctx.strokeStyle = '#e2e8f0';
            ctx.stroke();

            // Gerçek Logo Çizimi
            if (corporateLogo.complete && corporateLogo.naturalWidth > 0) {
                ctx.drawImage(corporateLogo, -85, -70, 170, 36);
            } else {
                ctx.fillStyle = '#1d1d1b';
                ctx.font = 'bold 20px "Montserrat", sans-serif';
                ctx.textAlign = 'center';
                ctx.fillText('TAM BASKI', 0, -45);
            }

            ctx.fillStyle = '#f15a24';
            ctx.font = 'bold 11px "Inter", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(activePackage === 'standart' ? '350gr Kuşe • Çift Yön Mat • Oval Kesim' : '350gr Kuşe • Mat Selefon • Düz Kesim', 0, -20);

            ctx.fillStyle = '#0f172a';
            ctx.font = 'bold 15px "Inter", sans-serif';
            ctx.fillText('Kurumsal İletişim', 0, 15);

            ctx.fillStyle = '#64748b';
            ctx.font = '11px "Inter", sans-serif';
            ctx.fillText('info@tambaski.com.tr  •  0850 444 00 00', 0, 38);
            ctx.fillText('www.tambaski.com.tr', 0, 68);
        }

        // Dinamik Altın Işık & Yaldız Parlama Süpürmesi (Metallic Light Sweep)
        lightSweep = (lightSweep + 0.02) % 3;
        const sweepX = -cardW/2 + (lightSweep * (cardW + 100)) - 100;
        
        ctx.save();
        ctx.beginPath();
        ctx.roundRect(-cardW/2, -cardH/2, cardW, cardH, radius);
        ctx.clip();

        const sweepGrad = ctx.createLinearGradient(sweepX, -cardH/2, sweepX + 80, cardH/2);
        if (activePackage === 'vip') {
            sweepGrad.addColorStop(0, 'rgba(255, 235, 150, 0)');
            sweepGrad.addColorStop(0.5, 'rgba(255, 255, 255, 0.7)');
            sweepGrad.addColorStop(1, 'rgba(255, 215, 0, 0)');
        } else if (activePackage === 'premium') {
            sweepGrad.addColorStop(0, 'rgba(168, 85, 247, 0)');
            sweepGrad.addColorStop(0.5, 'rgba(255, 255, 255, 0.55)');
            sweepGrad.addColorStop(1, 'rgba(168, 85, 247, 0)');
        } else {
            sweepGrad.addColorStop(0, 'rgba(255, 255, 255, 0)');
            sweepGrad.addColorStop(0.5, 'rgba(255, 255, 255, 0.35)');
            sweepGrad.addColorStop(1, 'rgba(255, 255, 255, 0)');
        }

        ctx.fillStyle = sweepGrad;
        ctx.fillRect(-cardW/2, -cardH/2, cardW, cardH);
        ctx.restore();

        ctx.restore();

        angle += 0.025;
        animFrameId = requestAnimationFrame(renderScene);
    }

    window.startCinematicCanvas = function(pkgKey) {
        activePackage = pkgKey || 'vip';
        if (animFrameId) cancelAnimationFrame(animFrameId);
        initCanvas();
        angle = 0;
        lightSweep = 0;
        renderScene();
    };

    window.stopCinematicCanvas = function() {
        if (animFrameId) {
            cancelAnimationFrame(animFrameId);
            animFrameId = null;
        }
    };

})(window);
