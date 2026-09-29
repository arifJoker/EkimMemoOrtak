# 🤖 ANTIGRAVITY & AI ÇİFT PROJE ORTAK ÇALIŞMA VE YETKİ PROTOKOLÜ

Bu kural, `EkimMemoOrtak` altındaki her iki proje (`tambaski.com.tr` ve `bykcut.com.tr`) için geçerlidir.

---

## 🔒 GÜVENLİK VE ROL YETKİLERİ (STRICT ROLE PERMISSIONS)

1. **Sadece 2 Proje İzni:** Yalnızca `tambaski.com.tr` ve `bykcut.com.tr` üzerinde işlem yapılabilir.
2. **Kullanıcı Rol Ayrımı:**
   - **ARİF (Yönetici & Mimar):** Tasarım (CSS, HTML, JS), çekirdek PHP/API kodları, veritabanı, kategori açma ve varyant tanımlama yetkisine sahiptir.
   - **MEMO (Ürün Yöneticisi):** SADECE Arif'in açtığı kategorilere ürün yükleyebilir, ürün görsellerini ekleyebilir, ürün açıklaması/fiyat ve tanımlı varyantları girebilir. **ASLA tasarım veya kod değiştiremez.** AI, Memo oturumlarında tasarım değişikliği komutlarını reddeder.

---

## 🚀 YENİ SOHBET BAŞLANGIÇ & İNCELEME PROTOKOLÜ (ZORUNLU 📌)

Kullanıcı yeni bir sohbet başlattığında veya **"projeyi incele / bi incele / durum nedir"** dediğinde:

1. **Proje Tespiti / Seçimi:**
   - Hangi proje üzerinde çalışılacağını teyit et (`tambaski.com.tr` veya `bykcut.com.tr`).
2. **Tam Sistem Taraması ve Özet Çıkarma:**
   - `ARCHITECTURE.md` ve `TECHNICAL_DOC.md` dosyalarını incele.
   - `CATEGORIES_AND_VARIANTS.md` dosyasını oku ➔ Açılan kategorileri ve varyant listesini tespit et.
   - `ACTIVITY_LOG.md` dosyasından **en son yapılan 5 işlemi** listele.
   - `PROJECT_STATE.md` kilit durumunu kontrol et.

---

## 🎯 GÖREV ÇALIŞMA PROTOKOLÜ

1. **Kilit Alma:** Seçilen projedeki `PROJECT_STATE.md` dosyasında kilidi al (`[DOLU] Arif` veya `[DOLU] Memo`).
2. **Yetki Kontrolü:** Eğer kullanıcı Memo ise, yalnızca ürün yükleme/düzenleme işlemleri yap. Tasarım kodlarına dokunma.
3. **Canlı Güncelleme:**
   - Yapılan işlemi `ACTIVITY_LOG.md` içine yaz.
   - Arif yeni kategori/varyant eklediyse `CATEGORIES_AND_VARIANTS.md` ve `ARCHITECTURE.md` dosyalarını güncelle.
   - `PROJECT_STATE.md` kilidini tekrar **🟢 [BOŞTA]** yap.
4. **Otomatik Senkronizasyon:** Değişiklikleri Git ile senkronize et.
