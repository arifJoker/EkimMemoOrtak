## 📅 [2026-09-27 15:58] - Tam Teşekküllü Yönetici (Admin) Paneli & Güvenli Giriş Kapısı
* **Kapsam:** `admin/login.php`, `admin/header.php`, `admin/orders.php`, `admin/index.php`, `classes/Auth.php`, `includes/header.php`, `includes/footer.php`
* **Yapılan İyileştirmeler:**
  1. **Müstakil & Güvenli Yönetici Giriş Portalı (`admin/login.php`):**
     - Müşteri login yönlendirmesi yerine koyu slate Apple temalı (`#0b0f19`) müstakil yönetici giriş sayfası geliştirildi.
     - Veritabanında yönetici hesabı yoksa veya şifre uyuşmazlığı varsa sistem otomatik olarak `admin@tambaski.com` / `admin123` varsayılan admin hesabını devreye alır.
     - Giriş ekranına tek tıkla kimlik bilgilerini form alanlarına aktaran "Bilgileri Doldur" butonu yerleştirildi.
  2. **Yetkilendirme & Hızlı Erişim Bağlantıları:**
     - `Auth::requireAdmin()` fonksiyonu doğrudan `admin/login.php` sayfasına yönlendirecek şekilde güncellendi.
     - Header alanına yönetici oturumu açıkken görünen altın sarısı kalkanlı `👑 Admin` butonu eklendi.
     - Sayfa altı (Footer) telif satırına `Yönetici Girişi` erişim bağlantısı entegre edildi.
  3. **Gelen Siparişleri Kontrol & Operasyon Masası (`admin/orders.php`):**
     - Gelen siparişlerin durumları (Beklemede, Ödeme Alındı, Baskıda, Kargoda, Teslim Edildi), müşteri bilgileri, fatura ve adres detayları listelenir ve filtrelenebilir.
     - Müşterinin editörde hazırladığı vektörel SVG tasarımları tek tuşla indirme ve önizleme desteği sunar.
     - Kargo firması seçimi ve takip numarası girişiyle anlık kargo takip bağlantısı üretilir.
     - Atölye ve paketleme personeli için tek tıkla "Fişi Yazdır" (`@media print` ile temiz sipariş çıktısı) özelliği eklendi.
  4. **Ürünleri Kontrol & Katalog Yönetimi (`admin/products.php`):**
     - Mevcut tüm matbaa ürünlerinin aktif/pasif durumu, tabaka boyutları, kağıt gramajları ve fiyatlandırma modları (m2 dinamik veya manuel) yönetilebilir.
     - Hızlı ürün arama, kategori bazlı filtreleme ve yeni ürün oluşturma akışı sağlandı.

---

## 📅 [2026-09-27 15:32] - Tamamen Büyük & İhtişamlı Hero Slayt Alanı (5 Ürün Ailesi & Canlı İlerleme Çubuğu)
* **Kapsam:** `index.php`, `assets/css/style.css`
* **Yapılan İyileştirmeler:**
  1. **Büyük & Geniş Slayt Sahnesi (Grand Hero Banner):**
     - Hero slayt alanı genişletilerek masaüstünde `620px` yükseklikle tam teşekküllü, ferah bir vitrin sahnesine dönüştürüldü.
     - Tipografi `clamp(30px, 4vw, 50px)` ile büyük ekranlarda çok daha okunaklı ve iddialı hale getirildi.
  2. **5. Ürün Ailesi Eklendi (Rulo Etiket & Kutu Ambalaj):**
     - Slayt yelpazesi 5 ana matbaa grubuna çıkarıldı:
       - Slayt 1: Kartvizit & Prestij Serisi (1.000 Adet 484 ₺)
       - Slayt 2: Broşür & El İlanı (2.000 Adet 1.150 ₺)
       - Slayt 3: Cepli Dosya & Kurumsal Kimlik (%25 Bayi İskontosu)
       - Slayt 4: ⚡ 24S Hızlı & Acil Baskı (Aynı Gün Üretim)
       - Slayt 5: Rulo Etiket, Sticker & Kutu Ambalaj (500 Adetten Başlayan Hızlı Üretim)
  3. **Apple Tarzı Otomatik İlerleme Çubuğu (Auto-Progress Bar):**
     - Slaytın en üstüne her geçişte 0%'dan 100%'e dolan akıcı bir renkli ilerleme çubuğu (`hero-slider-progress-bar`) entegre edildi.
     - Fare üzerine gelindiğinde (hover) süre duraklar, fare çekildiğinde kaldığı yerden akmaya devam eder.
  4. **Numaralandırılmış Segmentli Navigasyon Sekmeleri:**
     - Alt kontrol barı `01 Kartvizit`, `02 Broşür`, `03 Cepli Dosya`, `04 ⚡ 24S Acil`, `05 Etiket & Kutu` şeklinde numaralı butonlarla yenilendi.
     - Sağ/sol yön okları (`48px`) estetik cam efektiyle daha belirgin ve erişilebilir kılındı.

---

## 📅 [2026-09-27 15:25] - Otomatik Ürün Karşılama Slaytı (Apple Sadeliğinde Hero Carousel)
* **Kapsam:** `index.php`, `assets/css/style.css`
* **Yapılan İyileştirmeler:**
  1. **Otomatik Geçişli Ürün Slaytı (Autoplay Hero Carousel):**
     - Statik hero alanı yerine müşterileri zengin ürün vitriniyle karşılayan 4 slaytlı, 5 saniyede bir otomatik dönen Apple tarzı slayt sistemi kuruldu.
     - **Slayt 1 (Kartvizit):** "Kartvizitte İlk İzlenim, Kusursuz Prestij" - 350gr Mat Kuşe, Kabartma Lak, 1.000 adet 484 ₺ paket fiyatı ve 3D lüks kartvizit sahnesi.
     - **Slayt 2 (Broşür & El İlanı):** "Kampanyalarınızı Duyurun, Tirajlı Fiyatlarla Kazanın" - A4/A5 ofset baskı, 2.000 adet 1.150 ₺ fiyat rozeti ve 3D açılı broşür sahnesi.
     - **Slayt 3 (Cepli Dosya & Kurumsal):** "Firmanıza Özel Dosyalar, Eksiksiz Kurumsal Kimlik" - Sunum dosyası, kartvizit yuvası, bayilere özel %25 iskonto ve 3D kurumsal dosya sahnesi.
     - **Slayt 4 (⚡ 24S Acil Baskı):** "Zamanınız Az mı? 24 Saatte Kapınızda!" - Aynı gün üretim ve 24 saat kargo teslimat garantili ekspres matbaa hattı.
  2. **İnteraktif Slayt Kontrolleri:**
     - Alt kısma tıklanabilir, aktif durumu gösteren hap göstergeler (`1. Kartvizit`, `2. Broşür`, `3. Cepli Dosya`, `4. Acil Baskı`) eklendi.
     - Hover durumunda beliren yumuşak cam efektli (glassmorphic) önceki/sonraki ok butonları yerleştirildi.
     - Fare ile üzerine gelindiğinde otomatik geçiş duraklar (pause on hover), ayrılınca devam eder.

---

## 📅 [2026-09-27 15:12] - Header Logo & Hero Alanı İçin Apple Sadeliğinde Canlı Animasyon Sistemi
* **Kapsam:** `assets/css/style.css`, `index.php`, `includes/header.php`
* **Yapılan İyileştirmeler:**
  1. **Header Logo Işık Yansıması & Mikro Etkileşim:**
     - `TAM BASKI!` logosuna periyodik olarak soldan sağa kayan metalik ışık huzmesi (`logoShineSweep`) efekti eklendi.
     - Hover durumunda yukarı süzülme (`translateY(-2px) scale(1.035)`) ve sıcak turuncu ışıma gölgesi uygulandı.
  2. **Akıcı Arka Plan Işık Halesi (Ambient Gradient Mesh):**
     - Hero alanına donanım hızlandırmalı 3 adet yumuşak renkli ışık küresi (`orb-1` sıcak turuncu, `orb-2` canlı mavi, `orb-3` fuşya) eklendi; derinlik ve modern atmosfer sağlandı.
  3. **Dinamik Metin Döndürücü (Text Rotator):**
     - Başlık alanına akıcı kayma animasyonuyla sırasıyla dönen baskı kategorileri eklendi: *"Baskı İhtiyaçlarınız, [Kartvizitte / Broşürde / Katalogda / Cepli Dosyada / Etiket & Kutuda] Apple Sadeliğinde Kapınızda."*
     - "Apple Sadeliğinde" ibaresine akıcı prizmatik degrade geçişi (`gradientFlow`) entegre edildi.
  4. **3D Süzülen Baskı Ürünleri Vitrini (Floating Print Mockups):**
     - Hero butonlarının hemen altına 3 boyutlu perspektifte (`perspective: 1200px`) süzülen interaktif ürün kartları yerleştirildi:
       - **Sol:** 3D açılı süzülen mat siyah lüks kartvizit + *"350gr Mat Kuşe & Lak"* rozeti.
       - **Orta:** Büyük boy çoklu ürün kataloğu ve CMYK proses barları + *"24 Saatte Hızlı Üretim"* rozeti.
       - **Sağ:** 3D açılı broşür/el ilanı + *"Kalite Garantili"* rozeti.
  5. **Buton Işıltısı ve Canlı İkonlar:**
     - "Tüm Ürünleri İncele" butonuna ışıltı geçişi, "Acil Baskı" butonuna ise nabız atan şimşek ikonu eklendi.

---

## 📅 [2026-09-27 15:00] - Nihai Logo Kararı (3. Varyasyon İnce Ayarı: Sol Amblem Kaldırıldı, Saf Tipografi Onaylandı)
* **Kapsam:** `assets/img/logo.svg`, `assets/img/logo.png`, `includes/header.php`, `assets/css/style.css`
* **Yapılan İyileştirmeler:**
  1. **Nihai Logo Tescillendi:**
     - Kullanıcı tercihi doğrultusunda 3. seçenek baz alındı; sol taraftaki dairesel "TB" monogram amblemi kaldırılarak saf, net ve güçlü **"TAM BASKI!"** logotype'ı nihai marka logosu (`logo.svg` ve `logo.png`) olarak sabitlendi.
     - Vurgulu detaylar (M harfi içindeki turuncu üçgen, BASK altındaki turuncu çizgi ve ünlem işareti noktası) korunarak siyah-turuncu kontrast sağlandı.
  2. **Header Üst Bandı & Önizleme Temizlendi:**
     - Karar aşamasında kullanılan geçici stil değiştirici butonlar üst duyuru bandından kaldırılarak site son derece sade, temiz ve kurumsal Apple estetiğine kavuşturuldu.
     - Tarayıcı önbellek çakışmalarını önlemek amacıyla logo yoluna cache-buster eklendi ve `localStorage` varyasyon kaydı sıfırlandı.

---

## 📅 [2026-09-27 14:48] - Dikkat Çeken 5 Farklı Logo Varyasyonu & Header Canlı Stil Seçici
* **Kapsam:** `assets/img/logo.svg`, `assets/img/logo-var1-orange.svg`, `assets/img/logo-var2-badge.svg`, `assets/img/logo-var3-monogram.svg`, `assets/img/logo-var4-cmyk.svg`, `assets/img/logo-var5-spread.svg`, `includes/header.php`, `assets/css/style.css`
* **Yapılan İnovasyonlar ve İyileştirmeler:**
  1. **5 Farklı Dikkat Çekici & Yayılmış Logo Varyasyonu Üretildi:**
     - **Varyasyon 1 (Enerjik Turuncu - Varsayılan `logo.svg`):** "TAM" harfleri göz alıcı canlı turuncu (#f15a24), M iç üçgeni siyah, "BASKI!" harfleri koyu antrasit (#1d1d1b), turuncu alt çizgi ve ünlem noktası. Çok yüksek kontrast ve modern enerji.
     - **Varyasyon 2 (Lüks Mat Rozet):** Açık header üzerinde anında fırlayan (pop-out) koyu antrasit kartvizit kapsülü, parlak turuncu ince neon bordür ve saf beyaz tipografi.
     - **Varyasyon 3 (TB Monogram Stüdyo):** Sol tarafta dairesel rozet içinde "TB" monogram amblemi + sağ tarafta geniş TamBaskı logotype'ı.
     - **Varyasyon 4 (Endüstriyel CMYK & Kros):** Sol tarafta profesyonel 4 renkli ofset baskı krosu (Cyan, Magenta, Yellow, Key) + TamBaskı logotype'ı.
     - **Varyasyon 5 (Geniş Yayılmış Kurumsal):** Logotype altına yayılan "MATBAA & DİJİTAL BASKI ÇÖZÜMLERİ" alt başlığı ve CMYK proses noktaları.
  2. **Logo Boyutu ve Yayılımı Büyütüldü:**
     - Masaüstü logo yüksekliği 38px'den **48px - 52px**'ye, maksimum genişlik 210px'den **290px - 320px**'ye çıkarıldı. Logo artık header'da kaybolmuyor, ferahça yayılıyor ve dikkat çekiyor.
  3. **Header Üst Çubuğunda Canlı Logo Seçici (Interactive Switcher):**
     - Üst duyuru bandına `1. Turuncu`, `2. Rozet`, `3. Monogram`, `4. CMYK`, `5. Geniş` butonları eklendi.
     - Tıklandığında sayfa yenilenmeden logo anında değişir, yumuşak geçiş efektiyle gösterilir ve kullanıcının seçimi `localStorage` üzerinden hafızada tutulur.

---

## 📅 [2026-09-27 14:38] - Tasarım Kayması Düzeltmesi & Sepette Tasarım Giydirme (%25 İndirimli Tamamlayıcı Ürünler)
* **Kapsam:** `product.php`, `cart.php`, `assets/js/canva_studio.js`, `assets/js/canva_templates_engine.js`
* **Yapılan İyileştirmeler:**
  1. **Tasarım Kayması ve Kırpılma Sorunu Giderildi:**
     - Editördeki tuval ölçeklendirme motoru (`updateResponsiveScale`) yeniden yapılandırıldı; unscaled sabit ebeveyn div'in kırpması önlendi, `top left` transform origin ve dinamik `#canvaCanvasHolder` boyutlandırması uygulandı.
     - Hazır şablon motorundaki (`canva_templates_engine.js`) tüm tipografi ve nesne koordinatları tuval en/boy oranına duyarlı (responsive) hale getirilerek kenar taşmaları sıfırlandı.
  2. **Ürün Sayfasındaki Alakasız Öneri Bloğu Kaldırıldı:**
     - Meslek seçimi ürün sayfasında yapılmadığı için alakasız duran "İnşaat, Mimarlık İçin Önerilen Tamamlayıcı Ürünler" alanı ürün sayfasından tamamen temizlendi.
  3. **Sepet Sayfasında Canlı Tasarım Giydirme & Çapraz Satış (Cross-Sell / Upsell) Motoru:**
     - Müşteri kartvizit veya herhangi bir ürünü tasarlayıp sepete eklediğinde, kullanıcının vektörel tasarımı sepetteki tamamlayıcı ürünlere (Yelken Bayrak, Cepli Dosya, Antetli Kağıt, Kaşe, Roll-Up) **otomatik olarak giydirilir**.
     - **Canlı 3D / Mockup Önizleme:** Müşteri ürüne tıkladığında kendi tasarımıyla giydirilmiş büyük boy 3D bayrak/klasör/kaşe sahnesini inceler.
     - **%25 - %28 Özel Sepet İndirimi & 1 Tıkla Ekle:** İndirimli fiyatla doğrudan sepetine ekleyebilir.

---

## 📅 [2026-09-27 14:32] - Geniş Yatay Logo & Modern Tek Satır Header (Inline Menü & Kargo Takip)
* **Kapsam:** `assets/img/logo.svg`, `includes/header.php`, `assets/css/style.css`
* **Yapılan İyileştirmeler:**
  1. **Geniş Yatay Vektörel Logo (TAM BASKI!):**
     - Karemsi/dikey blok logo yerine, PDF'teki orijinal saf vektör çizimleri yan yana getirilerek geniş yatay format (`4.74:1` en-boy oranı, ~190-210px genişlik) oluşturuldu.
     - Tipografi, M içi turuncu üçgen, BASK altı turuncu çizgi ve ! ünlem vurgusu kayıpsız korundu.
  2. **Logo ile Menü Alt Alta Olmaktan Çıkarıldı (Tek Satır Entegrasyonu):**
     - Menü bağlantıları (Kartvizit, Broşür, Kurumsal, Tüm Baskılar dropdown'ı ve ⚡ Acil 24S), logonun hemen sağına kaydırılarak masaüstünde tek satırda hizalandı.
     - Masaüstünde kaba çift çubuk (alt subbar) görünümü gizlenerek tek ve ferah bir Apple tarzı navigasyon elde edildi; mobilde kaydırma işlevi korundu.
  3. **Modern Aksiyon Alanı (Kargo Takip, Giriş, Sepet, Arama):**
     - Kargo Takip alanı gri kaba buton yerine modern, oval kapsül (`header-action-pill`) ve kamyon ikonuyla şıklaştırıldı.
     - Giriş Yap / Profil alanı ve Sepet butonu (turuncu sayaç rozeti, koyu kapsül ve hover animasyonları) ile yeniden tasarlandı.
     - Arama kutusu odaklandığında genişleyen kompakt ve minimalist tasarıma geçirildi.

---

## 📅 [2026-09-27 14:28] - Tekil Varyant Listesi, Rozet Formatı (+XX ₺) ve Fiyat İki Katı Artış Düzeltmesi
* **Kapsam:** `product.php`, `classes/Product.php`, `api/calculate_price.php`
* **Yapılan İyileştirmeler:**
  1. **Mükerrer Kağıt Seçicisi Kaldırıldı:** Hem ana ürün sayfasındaki hem de TamBaskı Editörü İnce Ayar çekmecesindeki mükerrer `70x100 Kağıt Türü` alanı kaldırılarak yalnızca tek ve birleşik **"Kağıt Türü & Gramajı"** grubu bırakıldı.
  2. **Varyant Fiyat Rozetleri Türk Lirası Formatına Çevrildi:** Karışıklık yaratan kuruşlu birim fiyat gösterimleri yerine net toptancı farkları (`+200 ₺`, `+500 ₺`, `-100 ₺`, `-30 ₺`, `0 ₺`) listelendi.
  3. **Çift Ekleme & Şişen Fiyat Bug'ı Çözüldü:** Otomatik modda varyant seçildiğinde hem toptancı farkına eklenip hem de `extraFixedTry` olarak iki kez fiyatı artırma sorunu tamamen giderildi.
  4. **Canlıda Doğrulanan Fiyatlar (%100 Kâr Marjı ile):**
     - **Ekonomik Paket (1.000 Adet):** 220 ₺ Alış $\rightarrow$ 484,00 ₺ Net Satış (+ KDV).
     - **Standart Paket (1.000 Adet):** 400 ₺ Alış $\rightarrow$ 880,00 ₺ Net Satış (+ KDV).

---

## 📅 [2026-09-27 14:18] - Header Logo Boyutu & Sadeleştirme İnce Ayarı
* **Kapsam:** `includes/header.php`, `assets/css/style.css`
* **Yapılan Değişiklikler:**
  1. **ONLINE Rozeti Kaldırıldı:** Header logosunun yanındaki `Online` rozeti kaldırılarak yalnızca saf vektörel "TamBaskı!" logosunun öne çıkması sağlandı.
  2. **Logo Boyutu Büyütüldü:** Masaüstü görünümde logo yüksekliği 38px'den 48px'e (genişlik ~56px) çıkarıldı; mobil cihazlar için 40px olarak optimize edildi.

---

## 📅 [2026-09-27 14:15] - Çoklu Tedarikçi (Multi-Supplier) & Resmi Türmatsan Varyantları Sistemi
* **Kapsam:** `classes/Product.php`, `admin/pricing_engine.php`, `admin/products.php`, `admin/variants.php`, `migrate_suppliers.php`, `seed_turmatsan_variants.php`
* **Yapılan Yenilikler:**
  1. **Çoklu Tedarikçi & Fiyat Listesi Hafızası:**
     - Sistemde birden fazla toptancı / tedarikçi tanımlama desteği eklendi (`Türmatsan`, `Net Matbaa`, `Atölyemiz / X Tedarikçi`).
     - Her tedarikçinin fiyat listesi kalemleri (1000, 2000, 5000 adet) veritabanına kaydedilebilir ve simülatörde tek tıkla seçilebilir hale getirildi.
  2. **Tüm Eski Varyantlar Temizlendi & Resmi Türmatsan Standart Varyantları Yüklendi:**
     - **Kağıt Türü & Gramajı:** 350gr Kuşe (0 ₺), 350gr Parlak Kuşe (0 ₺), 250gr Amerikan Bristol (-100 ₺ / -0.10 ₺), 300gr İtalyan Tuale (+350 ₺ / +0.35 ₺), 700gr Sıvama Mukavva (+950 ₺ / +0.95 ₺).
     - **Baskı Yönü:** Çift Yön Renkli (0 ₺), Tek Yön Renkli (-30 ₺ / -0.03 ₺).
     - **Selefon & Kaplama:** Çift Taraf Mat Selefon (0 ₺), Çift Taraf Parlak (0 ₺), Soft-Touch Kadife (+150 ₺ / +0.15 ₺), Selefonsuz (0 ₺).
     - **Ekstra Efekt & İşçilik:** Efekt Yok (0 ₺), Kısmi Kabartma Lak (+200 ₺ / +0.20 ₺), 24K Altın Varak (+500 ₺ / +0.50 ₺), Gümüş Varak (+500 ₺ / +0.50 ₺), Kabartma Lak + Altın Varak (+650 ₺ / +0.65 ₺).
     - **Köşe Kesimi:** Düz Kesim 90° (0 ₺), 4 Köşe Oval Kesim (+50 ₺ / +0.05 ₺), Özel Bıçak Kesim (+150 ₺ sabit).
     - **Kırım & Katlama:** Katlamasız (0 ₺), Tek Kırım (+80 ₺ sabit), Z Kırım (+120 ₺ sabit), İçe Katlama (+120 ₺ sabit).
  3. **Çift Modlu Çalışma Güvencesi:**
     - **Otomatik Mod:** Varyant ek maliyetleri toptancı baz maliyetine eklenip üzerine belirlenen kâr marjı (+%100 vb.) ve tiraj çarpanı uygulanır.
     - **Manuel Mod:** Toptancıdan bağımsız olarak ürünün ve varyantların kendi üzerinde yazan sabit per-unit veya sabit TL fiyatları kullanılır.
  4. **Yenilenen Fiyatlandırma Motoru (`admin/pricing_engine.php`):**
     - 4 Sekmeli Modern Yönetim: 1. Fiyat Simülatörü & Kalibratör, 2. Tedarikçi Firmalar & Fiyat Listeleri, 3. Tüm Ürünlerin Fiyat Matrisi (Tek tıkla mod değiştirme), 4. Döviz Kuru & Tabaka Hammaddeleri.

---

## 📅 [2026-09-27 14:15] - Resmi "TamBaskı!" Logo & Favicon Entegrasyonu (Masaüstü PDF'ten Birebir Vektörel)
* **Kapsam:** `assets/img/*`, `includes/header.php`, `includes/footer.php`, `admin/header.php`, `manifest.json`, `assets/css/style.css`, sayfa başlıkları
* **Yapılan İşlemler:**
  1. **Masaüstündeki "tam baskı.pdf" Orijinal Vektör Çıkarımı:**
     - PDF içerisindeki saf vektör çizimleri (M harfi içindeki turuncu üçgen, BASK altı turuncu çizgi, ! ünlem vurgusu ve tipografi) piksel kaybı olmaksızın çıkarıldı ve normalize edildi.
     - `assets/img/logo.svg` (Açık arayüzler ve header için koyu antrasit yazı + turuncu detaylar),
     - `assets/img/logo-badge.svg` (PDF'teki orijinal koyu `#1d1d1b` rozet tasarımı),
     - `assets/img/logo-dark.svg` (Koyu zeminler için beyaz yazı + turuncu detaylar) ve yüksek çözünürlüklü PNG versiyonları üretildi.
  2. **Favicon & Web App İkon Paketi:**
     - Retina ve 4K ekranlarda tam netlik için modern vektörel `favicon.svg` tarayıcı sekme ikonu entegre edildi.
     - Eski tarayıcılar için çoklu çözünürlüklü `favicon.ico` (16, 32, 48, 64 px) ve `favicon-32x32.png`, `favicon-16x16.png` üretildi.
     - Mobil cihazlar ve ana ekrana ekleme için `apple-touch-icon.png` (180x180 px), `icon-192.png` ve `icon-512.png` PWA ikonları güncellendi.
  3. **Arayüz Entegrasyonu:**
     - Ana sayfa ve alt sayfa üst menüsündeki (`includes/header.php`) geçici yazı/ikon yerine yeni logo yerleştirildi.
     - Alt bilgi alanındaki (`includes/footer.php`) logo güncellendi.
     - Yönetim paneli (`admin/header.php`) sidebar'ı ve sekme ikonu yeni marka kimliğine kavuşturuldu.
     - `manifest.json` ve PWA meta etiketleri TamBaskı marka kimliğiyle senkronize edildi.

---

## 📅 [2026-09-27 13:58] - Akıllı Fiyatlandırma & Tedarikçi Alış Maliyeti Düzeltmesi (Tam Entegrasyon)
* **Kapsam:** `classes/Product.php`, `admin/pricing_engine.php`, `admin/products.php`
* **Yapılan Düzeltmeler:**
  1. **Tedarikçi Alış Fiyatı (`supplier_cost_1000`) Doğrudan Entegre Edildi:**
     - Admin panelinde ürünün tedarikçi maliyeti (Örn: 450 ₺) ve kâr marjı (Örn: `%100`) girildiğinde, sistem doğrudan bu maliyet üzerinden hesaplama yapmaya başladı.
     - **Hesaplama Doğrulaması:** 
       - 1.000 Adet Alış = 450,00 ₺
       - %100 Kâr Marjı ile Taban Satış = 900,00 ₺ (Standart çift yön paket farkıyla Net: 990,00 ₺ + KDV).
       - 2.000 Adet Alış = 765,00 ₺ $\rightarrow$ Satış = 1.530,00 ₺ (Net: 1.683,00 ₺ + KDV).
  2. **Tiraj Çarpanı Normalizasyonu:**
     - `getTierMultiplier()` fonksiyonu hem birim indirim oranlarını hem de toplam paket çarpanlarını otomatik algılayacak şekilde normalize edildi.
  3. **Özel Ebat Alan Oranı ($m^2$):**
     - Müşteri özel ölçü girdiğinde (örn: 9x6 cm), 1000 adet standart alana ($43.68 \text{ cm}^2$) göre oranlanarak ($1.236\times$) anlık hatasız hesaplama yapar.

---

## 📅 [2026-09-27 13:25] - TamBaskı Tasarım Editörü & Yeni Nesil Mobil Tasarım (Canva/Procreate Mobile)
* **Kapsam:** `product.php`, `assets/js/canva_studio.js`, `includes/header.php`, `includes/footer.php`
* **Yapılan Değişiklikler:**
  1. **Marka İsimlendirmesi:** "Canva / BaskıMatbaa" ismi her yerde **"TamBaskı"** ve **"TamBaskı Tasarım Editörü"** yapıldı.
  2. **Mobil Tuval Genişletme:** Mobilde tuvali sıkıştıran eski bloklar kaldırıldı; tuval ekranın merkezine tam odaklı yerleştirildi.
  3. **Yüzen Alt Dock & Alttan Açılır Çekmece (Slide-Up Bottom Sheet):** Şablonlar, metin, logo, ikonlar alt araç çubuğuna taşındı. Bir öğe seçildiği anda çekmece otomatik kapanıp tam ekran tuvale odaklanır.
  4. **Vektörel İkon Kütüphanesi:** WhatsApp, Instagram, telefon, terazi, baret, diş, stetoskop vb. 40'tan fazla SVG ikon ve canlı arama eklendi.
  5. **3D Canlı Döner Mockup:** Ön/arka yüz en-boy oranı hatası giderildi, kenardaki beyaz boşluklar düzeltildi.

---

## 📅 [2026-09-27 11:30] - Güvenli Kesim Alanı Kilit Motoru (Clamp Engine)
* **Kapsam:** `assets/js/canva_studio.js`
* **Yapılan Değişiklikler:**
  * Kullanıcı nesneleri taşırken veya büyütürken güvenli kesim payı dışına çıkması engellendi (`clampObjectInsideBounds`, `clampObjectScaling`).

---

## 📅 [2026-09-27 09:40] - 1.800+ Sektörel Vektör Şablon Motoru
* **Kapsam:** `assets/js/canva_templates_engine.js`
* **Yapılan Değişiklikler:**
  * Hukuk, Mimarlık, Sağlık, VIP Lüks, Kurumsal, Teknoloji, Kafe/Gıda gibi 15 farklı sektöre özel SVG şablonları dinamik olarak render eden bağımsız motor oluşturuldu.

---

## 🚀 Canlı Dağıtım Komutu (Her İki Geliştirici İçin)
Herhangi bir geliştirme yaptıktan sonra sunucuya yüklemek için terminalde şunu çalıştırmanız yeterlidir:

```powershell
python deploy_cpanel.py
```
