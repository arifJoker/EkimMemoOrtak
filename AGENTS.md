# 🤖 AI ORTAK ÇALIŞMA VE YETKİ KILAVUZU (AGENTS & CURSOR RULES)

> 🔒 **GÜVENLİK VE İZOLASYON KURALI:** Bu çalışma alanında ve sunucuda **SADECE** `tambaski.com.tr` ve `bykcut.com.tr` klasörlerine erişim izni vardır. Sunucudaki veya yereldeki diğer hiçbir siteye veya üst dizine kesinlikle erişilemez.

---

## 👥 ROL VE YETKİ MATRİSİ (ROLE-BASED PERMISSIONS)

### 👑 1. ARİF (Sistem Mimarı, Tasarımcı & Kurucu):
- **Tam Yetkili (Full Access):**
  - Tüm arayüz tasarımı, HTML/CSS/JS, UI/UX şablonları.
  - Sistem mimarisi, çekirdek PHP/API kodları, veritabanı yapısı.
  - Yeni kategori açma, varyant grupları ve fiyat algoritmaları belirleme.
  - Açılan kategorileri ve varyantları `CATEGORIES_AND_VARIANTS.md` dosyasına not olarak yazar.

### 📦 2. MEMO (Ürün ve İçerik Yöneticisi):
- **YETKİ ALANI (SADECE ÜRÜN GİRİŞİ):**
  - Arif'in açtığı kategorilere yeni ürün ekleme, ürün düzenleme.
  - Ürün başlıkları, SEO açıklamaları, ürün etiketleri girme.
  - Ürün fotoğrafları ve mockup görsellerini `uploads/products/` altına ekleme.
  - `CATEGORIES_AND_VARIANTS.md` ve `MEMO_PRODUCT_GUIDE.md` dosyalarında Arif'in belirttiği varyantları (boyut, adet, paket, m² fiyatları) ürüne atama.
- 🚫 **KESİNLİKLE YASAK ALANLAR (AI ENGELİ):**
  - Tasarım değiştirme, tema/CSS düzenleme, HTML/şablon kodlarına müdahale etme.
  - Çekirdek PHP dosyalarını, API'leri veya veritabanı şemasını değiştirme.
  - *Kural:* Eğer Memo veya Memo'nun AI asistanı tasarım/kod değiştirme talebi alırsa, AI bunu **kesinlikle reddedecek** ve *"Bu işlem yalnızca Arif'in yetkisindedir. Memo rolü yalnızca ürün yükleme ve içerik girişine yetkilidir."* uyarısı verecektir.

---

## 🚀 SOHBET BAŞLANGIÇ & İŞLEM PROTOKOLÜ

1. **Sohbet Başlangıcı:**
   - Hangi proje (`tambaski.com.tr` veya `bykcut.com.tr`) üzerinde çalışılacağını teyit et.
   - İlgili projenin `ACTIVITY_LOG.md` dosyasından **son 5 işlemi** listele.
   - `PROJECT_STATE.md` kilit durumunu kontrol et.
   - Memo ile çalışılıyorsa: `MEMO_PRODUCT_GUIDE.md` ve `CATEGORIES_AND_VARIANTS.md` dosyalarındaki güncel ürün yükleme kurallarını oku.

2. **Kilit Kontrolü:** İstenen modül kilitliyse kullanıcıyı uyar. Değilse `PROJECT_STATE.md` kilidini al.

3. **Canlı Güncelleme (Zorunlu):**
   - Yeni ürün eklendiğinde `ACTIVITY_LOG.md` içine log düş.
   - Eğer Arif yeni kategori/şablon eklediyse `ARCHITECTURE.md` ve `CATEGORIES_AND_VARIANTS.md` dosyalarını güncelle.
   - `PROJECT_STATE.md` kilidini tekrar **🟢 [BOŞTA]** yap.
   - Kodları/verileri Git ile senkronize et.
