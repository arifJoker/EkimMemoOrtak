# 🤖 ANTIGRAVITY & AI ÇİFT PROJE ORTAK ÇALIŞMA PROTOKOLÜ

Bu kural, `EkimMemoOrtak` altındaki her iki proje (`tambaski.com.tr` ve `bykcut.com.tr`) için geçerlidir.

---

## 🔒 KESİNLİKLE UYULACAK GÜVENLİK VE İZOLASYON KURALLARI

1. **Sadece 2 Proje Yetkisi:** Bu depoda ve sunucuda **YALNIZCA** `tambaski.com.tr` ve `bykcut.com.tr` klasörleri üzerinde çalışılabilir.
2. **Dış Dizin Koruması:** Sunucudaki veya yereldeki diğer hiçbir siteye, üst dizine (`/home/arifuzco` altındaki diğer siteler: örn. gezisoft, cafe, erp vb.) veya özel dosyalara **kesinlikle erişilmeyecek, okunmayacak ve değiştirilmeyecektir.**
3. **İzolasyon Garantisi:** Yapılan tüm işlemler, kilitler, şemalar ve cPanel yüklemeleri sadece bu iki projenin kendi sınırları içinde gerçekleşir.

---

## 🚀 YENİ SOHBET BAŞLANGIÇ & İNCELEME PROTOKOLÜ (ZORUNLU 📌)

Kullanıcı yeni bir sohbet başlattığında veya **"projeyi incele / bi incele / durum nedir"** dediğinde:

1. **Proje Tespiti / Seçimi:**
   - Kullanıcı belirtmediyse sor: *"Hangi proje üzerinde çalışacağız? (1: tambaski.com.tr, 2: bykcut.com.tr)"*
   - Kullanıcı belirttiğinde (veya tek proje seçildiğinde) o projenin kök dizinine odaklan.

2. **Tam Sistem Taraması ve Özet Çıkarma (İncele Komutu):**
   - `ARCHITECTURE.md` şemasını oku ➔ Sistemin görsel mimarisini, sayfalarını ve veri akışını açıkla.
   - `TECHNICAL_DOC.md` dosyasını oku ➔ Hangi dosyanın ne iş yaptığını ve modülleri kavra.
   - `ACTIVITY_LOG.md` dosyasını oku ➔ **En son yapılan 5 işlemi** (Tarih, Yapan, Detay) listele.
   - `PROJECT_STATE.md` dosyasını oku ➔ Kilit durumunu bildir (Çakışma var mı?).

3. **Kullanıcıya Sunum:**
   - Projenin güncel durumunu, son yapılanları ve sıradaki adımları net, düzenli bir özet olarak kullanıcıya sun ve *"Ne yapmak istersiniz?"* diye sor.

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
