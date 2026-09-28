# 🤖 ANTIGRAVITY & AI ÇİFT GELİŞTİRİCİ ÇALIŞMA PROTOKOLÜ (COLLABORATION PROTOCOL)

Bu kural dosyası **her yeni sohbet başlatıldığında ve her prompt çalıştırıldığında** Antigravity tarafından zorunlu olarak uygulanır.

---

## 🎯 PROTOKOL ADIMLARI (HER PROMPTTA SIRASIYLA UYGULANIR)

### 1. ADIM: Başlangıç ve Çakışma Kontrolü (Pre-Flight Check)
Kullanıcı herhangi bir kod yazma, düzenleme veya silme isteğinde bulunduğunda:
1. `PROJECT_STATE.md` dosyasını oku.
2. Eğer kullanıcının değiştirmek istediği dosya veya modül üzerinde diğer geliştirici (Arif veya Memo) **[DOLU / KİLİTLİ]** görünüyorsa:
   - Kullanıcıyı hemen uyar: *"⚠️ Bu dosya/modül şu anda diğer geliştirici tarafından kilitli. Çakışmayı önlemek için işin bitmesini bekleyelim veya farklı bir modülde çalışalım."*
3. Dosya boşsa (`[BOŞTA]`):
   - `PROJECT_STATE.md` dosyasında ilgili geliştiricinin durumunu **[DOLU / KİLİTLİ]** olarak işaretle.

### 2. ADIM: Şemayı & Teknik Dokümanı Kavrama
1. `ARCHITECTURE.md` ve `TECHNICAL_DOC.md` dosyalarını inceleyerek sistemin en güncel akışını doğrula.
2. Yapılacak işi mevcut mimariye uygun olarak gerçekleştir.

### 3. ADIM: Kodlama ve Değişiklik
1. İstenen kodları temiz, hatasız ve modüler şekilde yaz / düzenle.

### 4. ADIM: Dokümantasyon ve Şemayı Canlı Güncelleme (ZORUNLU 📌)
İşlem tamamlandıktan hemen sonra:
1. **Şemayı Güncelle:** `ARCHITECTURE.md` dosyasındaki Mermaid diyagramına yeni eklenen servis, sayfa veya veritabanı ilişkisini ekle.
2. **Log Günlüğünü Yaz:** `ACTIVITY_LOG.md` dosyasının en üstüne bugünün tarih-saatini, kimin yaptığını (Arif / Memo), neyin değiştiğini ve yeni dosyaları ekle.
3. **Teknik Dokümanı Güncelle:** `TECHNICAL_DOC.md`'ye yeni dosya ve fonksiyonların kısa açıklamasını ekle.
4. **Kilidi Aç:** `PROJECT_STATE.md` dosyasındaki kilit durumunu tekrar **🟢 [BOŞTA]** yap.

### 5. ADIM: Arka Plan Eşitleme (Auto-Sync)
- Değişiklikleri Git ile otomatik commit & push etmeye hazır hale getir.

---

Bu adımlar harfiyen uygulandığında iki farklı şehirdeki geliştirici asla kod çakışması yaşamaz ve proje daima canlı belgelenmiş kalır.
