# 📋 TAMBASKI.COM.TR - AÇILAN KATEGORİLER VE VARYANT REHBERİ

> **Arif'in Not Alanı:** Arif yeni bir kategori açtığında veya yeni varyant kuralları belirlediğinde buraya yazar.
> **Memo'nun Çalışma Alanı:** Memo ürün yüklerken yalnızca burada belirtilen aktif kategorileri ve tanımlı varyantları kullanır. Tasarım veya kodlara dokunamaz.

---

## 🏷️ Aktif Kategoriler ve Hesaplama Modelleri

### 1. 📇 Kartvizit Grubu (Şu Anki Kusursuz Çalışan Modelimiz)
- **Kategori Adı:** Kartvizit
- **Kategori Slug:** `kartvizit`
- **Hesaplama & Varyant Modeli:** `package_tier` (4 Hazır Paket & Tiraj Çarpanı)
- **Özellikler & Tasarım Ekranı:**
  - **4 Paket:** Ekonomik (250gr Bristol), Standart (350gr Kuşe Mat Selefon), Premium (Kadife Selefon & Kabartma Lak), VIP Prestij (Tuale / Altın Varak)
  - **Tiraj Seçimi:** 1.000, 2.000, 3.000, 5.000, 10.000 Adet
  - **Tasarım Stüdyosu:** Tek Buton "Tasarlamaya Başla" -> Canva Studio (Fabric.js) + Hızlı Şablon Bilgi Kutusu + Masaüstü Fotogerçekçi 3D Çevrilebilir Mockup Sahnesi + WhatsApp Grafiker Desteği

---

### 2. 🛡️ Dekota Uyarı Levhaları (AKTİF AÇILDI & KULLANIMA HAZIR)
- **Kategori Adı:** Dekota Uyarı Levhaları
- **Kategori Slug:** `dekota-uyari-levhalari`
- **Hesaplama & Varyant Modeli:** `rigid_board` (4 Standart Ebat Paketi + Özel Ölçü $m^2$ + Kalınlık + Montaj + Kademeli Adet İndirimi)
- **Paketler (Standart Boyutlar):**
  - **Küçük Boy:** 25x35 cm (95 ₺) - Kapı üstü, pano yanı, ofis ve atölye içi kullanım
  - **Orta Boy:** 35x50 cm (145 ₺) - Koridorlar, üretim hatları, elektrik panoları
  - **Büyük Boy:** 50x70 cm (240 ₺) - Fabrika girişleri, şantiyeler, geniş depo alanları
  - **Mega Boy:** 70x100 cm (420 ₺) - Dış cephe, nizamiyeler, otopark yönlendirme
  - **Özel Ölçü (m²):** En (cm) x Boy (cm) canlı $m^2$ hesaplama (550 ₺/m²)
- **Varyant Seçenekleri:**
  - **Levha Kalınlığı:** 3 mm Sert Dekota (Standart) / 5 mm Sert Dekota (+%25 Ekstra Dayanıklı)
  - **Montaj Seçeneği:** Montajsız / Çift Taraflı Köpük Bant (+15 ₺) / 4 Köşeden Delikli (+10 ₺)
  - **Adet İndirimi Kademeleri:** 1 Adet (%0), 5 Adet (%0), 10 Adet (%10), 25 Adet (%20), 50 Adet (%30), 100+ Adet (%40)
- **Tasarım Stüdyosu:** 
  - Özel "Levhayı Tasarla / Özelleştir" Sign & Safety Studio (Fabric.js).
  - Türkçe İSG ve Uyarı Piktogram kütüphanesi (Baret, Gözlük, Maske, Eldiven, Sigara İçilmez, Ateşle Yaklaşma, Acil Çıkış, Yangın Tüpü, İlkyardım, Yüksek Gerilim vb.).
  - Hazır Uyarı Bantları (DİKKAT, TEHLİKE, UYARI, YASAKTIR, GÜVENLİK).
  - Firma logosu ekleme ve özel metin yazma alanı.
  - Sarı/Siyah ve Kırmızı/Beyaz ikaz çerçeveleri.
- **Mevcut Test Ürünü:** `dekota-isg-guvenlik-uyari-levhasi` (URL: `/product.php?slug=dekota-isg-guvenlik-uyari-levhasi`)

---

### 3. 📐 Araç Sticker & Geniş Format Folyo Grubu (Sıradaki Faz)
- **Kategori Adı:** Araç Sticker & Folyo
- **Kategori Slug:** `arac-sticker`
- **Hesaplama & Varyant Modeli:** `m2_calculator` (Dinamik En x Boy $m^2$ Maliyet Motoru)
- **Planlanan Varyantlar:** Cast Araç Folyosu, Standart Parlak Folyo, Şeffaf Folyo, Reflektif Folyo

---

### 4. 🎁 Promosyon & Kademeli Parça Grubu (Sonraki Faz)
- **Kategori Adı:** Promosyon & Tekstil
- **Hesaplama & Varyant Modeli:** `tiered_qty` (Kademeli Parça Başı İskonto)
- **Planlanan Varyantlar:** Kupa Bardak, Magnet, Bez Çanta vb. (25, 50, 100, 250, 500+ kademe)

---

## 📝 Memo İçin Ürün Yükleme Kuralları:
1. Ürün görsellerini yüksek kaliteli (en az 1200x1200px) ve temiz arka planlı olarak `uploads/products/` klasörüne ekleyin.
2. Tüm ürün başlıkları, açıklamaları ve etiketleri **%100 Türkçe** olmalıdır.
3. Dekota kategorisine ürün eklerken `dekota-uyari-levhalari` kategorisini seçin ve 4 ebat paketini (Küçük, Orta, Büyük, Mega) kullanın.
4. Tasarım şablonlarına, CSS kodlarına, JS dosyalarına ve çekirdek PHP kodlarına dokunmayınız. Sadece ürün ve içerik girişinden sorumlusunuz.
