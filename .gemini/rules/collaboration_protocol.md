# 🤖 ANTIGRAVITY & AI ÇİFT PROJE ORTAK ÇALIŞMA PROTOKOLÜ

Bu kural, `EkimMemoOrtak` altındaki her iki proje (`tambaski.com.tr` ve `bykcut.com.tr`) için geçerlidir.

---

## 🚀 YENİ SOHBET BAŞLANGIÇ PROTOKOLÜ (ZORUNLU 📌)

Kullanıcı yeni bir sohbet başlattığında veya projeyle ilgili ilk mesajı attığında:

1. **Proje Seçimi Sorulacak:**
   - *"Hangi proje üzerinde çalışacağız?"*
     1. **`tambaski.com.tr`**
     2. **`bykcut.com.tr`**

2. **Son 5 İşlem & Durum Raporu:**
   - Kullanıcı projeyi seçtiğinde (veya mesajında belirttiyse), ilgili projenin `ACTIVITY_LOG.md` dosyasını oku.
   - **En son yapılan 5 işlemi** madde madde kullanıcıya göster (Tarih, Geliştirici, Yapılan İşlem).
   - `PROJECT_STATE.md` dosyasındaki kilit durumunu kontrol edip bildir (Kilit boşta mı, dolu mu?).

---

## 🎯 GÖREV ÇALIŞMA PROTOKOLÜ

1. **Kilit Alma:** Seçilen projedeki `PROJECT_STATE.md` dosyasında kilidi al (`[DOLU] Arif` veya `[DOLU] Memo`).
2. **Mimari & Şema İnceleme:** O projenin `ARCHITECTURE.md` şemasına ve `TECHNICAL_DOC.md` dosyasına bakarak kodu yaz.
3. **Canlı Güncelleme (Her işlem sonunda ZORUNLU):**
   - `ARCHITECTURE.md` içindeki Mermaid şemasına yeni sayfa/fonksiyonu ekle.
   - `ACTIVITY_LOG.md` en üstüne yeni log kaydını ekle (Tarih, Yapan kişi, Yapılan iş).
   - `TECHNICAL_DOC.md` dizin yapısını güncelle.
   - `PROJECT_STATE.md` kilidini tekrar **🟢 [BOŞTA]** yap.
4. **Otomatik Senkronizasyon:** Değişiklikleri Git ile commit & push et.
