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

### 2. 📐 Araç Sticker & Geniş Format Folyo Grubu (2. Faz)
- **Kategori Adı:** Araç Sticker & Folyo
- **Kategori Slug:** `arac-sticker`
- **Hesaplama & Varyant Modeli:** `m2_calculator` (Dinamik En x Boy $m^2$ Maliyet Motoru)
- **Planlanan Varyantlar:**
  - **Canlı Ebat Girişi:** En (cm) ve Boy (cm) -> Otomatik $m^2$ hesaplama
  - **Malzeme Türü:** Cast Araç Folyosu, Standart Parlak Folyo, Şeffaf Folyo, Reflektif Folyo
  - **Laminasyon:** Parlak Laminasyon, Mat Laminasyon, Laminasyonsuz
  - **İşçilik & Kesim:** Düz Kesim, Dekupe (Şekilli) Kesim, Ayıklanmış & Transferli

---

### 3. 🛡️ Dekota & Sert Zemin / Levha Grubu (3. Faz)
- **Kategori Adı:** Dekota (Forex) & Levha Baskı
- **Kategori Slug:** `dekota-baski`
- **Hesaplama & Varyant Modeli:** `rigid_board` (Kalınlık + Ebat + Toplu Alım İskontosu)
- **Planlanan Varyantlar:**
  - **Kalınlık:** 3mm Dekota, 5mm Dekota, 8mm Dekota, 10mm Dekota
  - **Ebat:** Standart Ebatlar (35x50 cm, 50x70 cm, 70x100 cm) veya Özel $m^2$
  - **Kesim:** Düz Giyotin Kesim, CNC / Lazer Özel Form Kesim
  - **Toplu Adet İskontosu:** 1-5 Adet, 6-20 Adet, 21-50 Adet, 50+ Adet

---

### 4. 🎁 Promosyon & Kademeli Parça Grubu (4. Faz)
- **Kategori Adı:** Promosyon & Tekstil
- **Hesaplama & Varyant Modeli:** `tiered_qty` (Kademeli Parça Başı İskonto)
- **Planlanan Varyantlar:** Kupa Bardak, Magnet, Bez Çanta vb. (25, 50, 100, 250, 500+ kademe)

---

## 📝 Memo İçin Ürün Yükleme Kuralları:
1. Ürün görsellerini yüksek kaliteli (en az 1200x1200px) ve temiz arka planlı olarak `uploads/products/` klasörüne ekleyin.
2. Kartvizit kategorisine ürün eklerken 4 paketi (Ekonomik, Standart, Premium, VIP) ve 1.000 adet fiyatını tanımlayın.
3. Tasarım şablonlarına, CSS kodlarına ve çekirdek dosyalara dokunmayınız. Sadece ürün ve içerik girişinden sorumlusunuz.
