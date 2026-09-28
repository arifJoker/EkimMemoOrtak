# 🤖 AI ORTAK ÇALIŞMA KILAVUZU (AGENTS & CURSOR RULES)

Herhangi bir AI asistanı (Antigravity, Cursor, Claude, Windsurf vb.) bu projede işlem yaparken aşağıdaki 5 adımlı protokole uymak zorundadır:

1. **Kilit Kontrolü:** `PROJECT_STATE.md` dosyasını kontrol et. İstenen dosya kilitliyse kullanıcıyı uyar. Değilse kilidi al.
2. **Mimari & Şema İnceleme:** `ARCHITECTURE.md` içindeki Mermaid şemalarına ve `TECHNICAL_DOC.md` dosyasına bakarak projenin yapısını anla.
3. **Görevi Gerçekleştir:** İstenen geliştirmeyi yap.
4. **Canlı Güncelleme (Zorunlu):**
   - `ARCHITECTURE.md` içindeki şemaya yeni modülü/sayfayı ekle.
   - `ACTIVITY_LOG.md` içine yapılan işlemi (Tarih, Geliştirici, Değişiklikler) log olarak yaz.
   - `TECHNICAL_DOC.md` dizin yapısını güncelle.
   - `PROJECT_STATE.md` kilidini tekrar [BOŞTA] durumuna getir.
5. **Eşitleme:** Kodları senkronize et.
