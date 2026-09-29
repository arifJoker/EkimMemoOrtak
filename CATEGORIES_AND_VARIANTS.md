# 📋 TAMBASKI.COM.TR - AÇILAN KATEGORİLER VE ÜRÜN YÜKLEME REHBERİ (MEMO & ANTIGRAVITY KILAVUZU)

> 👑 **Arif'in Notu:** Sistem mimarı Arif yeni bir kategori açtığında, varyant veya fiyat formülü belirlediğinde burayı günceller.
> 📦 **Memo & Memo Antigravity Yetki Alanı:** Memo ve Memo'nun AI asistanı (Antigravity) ürün eklerken **SADECE** bu belgede belirtilen kategorileri, paketleri ve varyant kurallarını kullanır. Çekirdek PHP dosyalarına, JS stüdyolarına, CSS tasarımlarına veya veritabanı şemasına **kesinlikle dokunamaz**.

---

## 👥 ROL VE İŞ AKIŞI ÖZETİ
- **Memo'nun Görevi:** Admin paneli üzerinden ürün girmek, başlık/açıklama yazmak, ürün görsellerini `uploads/products/` altına yüklemek ve varyantları eşleştirmek.
- **Tasarım / Kod Koruması:** Eğer Memo veya Memo'nun AI asistanı tasarım veya kod düzenleme komutu alırsa AI bunu **kesinlikle reddeder**:
  *"Bu işlem yalnızca Arif'in yetkisindedir. Memo rolü yalnızca ürün yükleme ve içerik girişine yetkilidir."*

---

## 🏷️ KATEGORİLER VE ÜRÜN YÜKLEME ŞEKİLLERİ

---

### 1. 🛡️ DEKOTA UYARI LEVHALARI (AKTİF VE GELİŞTİRİLDİ)
İş sağlığı ve güvenliği (İSG), şantiye tabelaları, otopark/park yapılmaz ikazları, yangın, acil çıkış ve tesis yönlendirme levhaları bu kategoride açılır.

- **Kategori Adı:** Dekota Uyarı Levhaları
- **Kategori Slug:** `dekota-uyari-levhalari`
- **Hesaplama Modeli:** `rigid_board` (Dolar m² Fiyat Motoru + 4 Standart Paket + Özel Ebat + Kalınlık + Montaj)
- **Tasarım Ekranı:** Otomatik devrede olan **Dekota & İSG Levha Tasarım Stüdyosu (Sign & Safety Studio)**. Müşteri 65+ Türkçe piktogram, ikaz çerçevesi, zemin rengi, özel ebat ve firma logosu ekleyebilir.

#### 📌 Admin Panelinden Dekota Ürünü Yükleme Adımları:
1. **Ürün Başlığı:** Açık, net ve aramalara uygun olmalıdır.
   - *Örnekler:* `Dekota İSG Güvenlik ve Uyarı Levhası`, `Park Yapılmaz Dekota Uyarı Tabelası`, `Sigara İçilmez Uyarı Levhası`, `Yüksek Gerilim Tehlike Levhası`.
2. **Kategori Seçimi:** Mutlaka `Dekota Uyarı Levhaları` seçilmelidir.
3. **SEO Açıklaması & Ürün Açıklaması:**
   - Ürünün iç ve dış mekan dayanımı, 300 DPI endüstriyel UV baskısı, yağmur ve güneşe karşı solmazlığı ve İSG standartlarına uygunluğu maddeler halinde yazılmalıdır.
4. **Fiyatlandırma & m² Dolar Formülü:**
   - **USD m² Fiyatı (`m2_usd_price`):** Dolar cinsinden m² maliyeti (Örn: `14.50`). TCMB canlı kurundan TL'ye çevrilir ve Türk Lirası fiyatı tam sayıya yukarı yuvarlanır (`ceil()`).
   - **4 Standart Paket Ebatları & Taban Fiyatları:**
     - **Küçük Boy (25x35 cm):** `95.00 ₺` - Kapı üstü, elektrik panosu yanı, ofis ve atölye içi kullanım.
     - **Orta Boy (35x50 cm):** `145.00 ₺` - Koridorlar, üretim hatları, ortak alanlar (En çok tercih edilen standart).
     - **Büyük Boy (50x70 cm):** `240.00 ₺` - Şantiye girişleri, depolar, geniş fabrika sahaları.
     - **Mega Boy (70x100 cm):** `420.00 ₺` - Dış cephe, nizamiye, yol ve açık otopark yönlendirmeleri.
   - **Özel Ebat (cm):** Müşteri stüdyoda veya ürün sayfasında istediği en x boy değerlerini girerek anında milimetrik m² fiyatı alır.
5. **Varyant Seçenekleri (Admin Paneli Ürün Düzenleme):**
   - **Levha Kalınlığı:**
     - `3 mm Sert Dekota (Forex)` -> Standart Fiyat (Çarpan: 1.00)
     - `5 mm Sert Dekota (Forex)` -> Ekstra Mukavemet (+%25 Fiyat Artışı)
   - **Montaj Seçeneği:**
     - `Montajsız (Düz Levha)` -> +0 ₺
     - `Çift Taraflı Güçlü Köpük Bantlı` -> +15 ₺
     - `4 Köşeden Delikli (Vida / Plastik Kelepçe Uyumlu)` -> +10 ₺
6. **Dinamik Adet İndirim Kademeleri:**
   - `1 Adet` -> %0 indirim
   - `5 Adet` -> %0 indirim
   - `10 Adet` -> %10 indirim
   - `25 Adet` -> %20 indirim
   - `50 Adet` -> %30 indirim
   - `100+ Adet` -> %40 indirim
7. **Görsel Standartları:**
   - Görsel dosyaları `tambaski.com.tr/uploads/products/` klasörüne yüklenir.
   - Boyut en az 1000x1000px, arka planı temiz veya fotogerçekçi duvara monte mockup olmalıdır.
   - Örnek: `uploads/products/dekota_levha.jpg`.

---

### 2. 📇 KARTVİZİT GRUBU (STANDART MATBAA MODELİ)
- **Kategori Adı:** Kartvizit
- **Kategori Slug:** `kartvizit`
- **Hesaplama Modeli:** `package_tier` (4 Paket + 5 Tiraj Çarpanı)
- **Tasarım Stüdyosu:** Canva Studio (Fabric.js) + 1.800+ Hazır Sektörel Şablon + Masaüstü 3D Çevrilebilir Mockup Sahnesi.

#### 📌 Admin Panelinden Kartvizit Yükleme Adımları:
1. **Ürün Başlığı:** Örn: `Prestij Kartvizit`, `Şeffaf Kartvizit`, `Kabartma Laklı VIP Kartvizit`.
2. **Kategori Seçimi:** `Kartvizit` seçilmelidir.
3. **4 Paket Tanımları:**
   - `Ekonomik Paket:` 250gr Amerikan Bristol, Mat Selefon.
   - `Standart Paket:` 350gr Kuşe, Çift Yön Mat Selefon (En Çok Satan).
   - `Premium Paket:` 350gr Kuşe, Kadife Dokulu Soft Touch Selefon + Bölgesel Kabartma Lak.
   - `VIP Paket:` 450gr Sıvamalı Özel Kağıt veya Tuale Dokulu Fantazi Kartvizit + Altın/Gümüş Varak.
4. **Tiraj Seçenekleri (Sabit Adet Çarpanları):**
   - 1.000 Adet (x1.00)
   - 2.000 Adet (x1.85)
   - 3.000 Adet (x2.60)
   - 5.000 Adet (x4.10)
   - 10.000 Adet (x7.50)
5. **Görsel Standartları:**
   - Fotogerçekçi masaüstü stüdyo çekimleri (`uploads/mockups/` ve `uploads/products/`).

---

### 3. 📐 ARAÇ STİCKER & GENİŞ FORMAT FOLYO (FAZ 2)
- **Kategori Adı:** Araç Sticker & Folyo
- **Kategori Slug:** `arac-sticker`
- **Hesaplama Modeli:** `m2_calculator` (Dinamik En cm x Boy cm $m^2$ Maliyet Motoru)
- **Özellikler:** Cast Araç Folyosu, Parlak / Mat Folyo, Şeffaf Folyo, Reflektif Folyo seçenekleri.

---

### 4. 🎁 PROMOSYON & TEKSTİL GRUBU (FAZ 3)
- **Kategori Adı:** Promosyon & Kurumsal Hediyelik
- **Kategori Slug:** `promosyon`
- **Hesaplama Modeli:** `tiered_qty` (Kademeli Parça Başı İndirim)
- **Özellikler:** Kupa Bardak, Magnet, Bez Çanta, Kalem, Bloknot vb.

---

## 🚫 MEMO İÇİN KESİNLİKLE YASAK OLAN EYLEMLER (AI GUARD):
1. `includes/`, `classes/`, `api/`, `admin/` altındaki `.php` kodlarını değiştirmek.
2. `assets/css/` veya `assets/js/` dosyalarına müdahale etmek, renk, font, buton tasarımını değiştirmek.
3. Veritabanı tablolarını (`categories`, `products`, `orders` vb.) silmek veya yapısını (`ALTER TABLE`) bozmak.
4. Sadece ürün ekleme, ürün açıklaması yazma ve görsel yükleme işlemlerini admin paneli üzerinden veya `uploads/products/` dizini aracılığıyla yapmalıdır.
