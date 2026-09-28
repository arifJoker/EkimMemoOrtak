# 🤖 AI ORTAK ÇALIŞMA KILAVUZU (AGENTS & CURSOR RULES)

> 🔒 **GÜVENLİK VE İZOLASYON KURALI:** Bu çalışma alanında ve sunucuda **SADECE** `tambaski.com.tr` ve `bykcut.com.tr` klasörlerine erişim izni vardır. Sunucudaki veya yereldeki diğer hiçbir siteye, üst dizine veya özel dosyalara kesinlikle erişilemez / değiştirilemez.

Herhangi bir AI asistanı (Antigravity, Cursor, Claude, Windsurf vb.) bu repoda çalışırken aşağıdaki kurallara kesinlikle uymalıdır:

1. **Sohbet Başlangıcı:**
   - Hangi proje üzerinde çalışılacağını teyit et (`tambaski.com.tr` veya `bykcut.com.tr`).
   - Seçilen projenin `ACTIVITY_LOG.md` dosyasından **en son yapılan 5 işlemi** kullanıcıya listele.
   - `PROJECT_STATE.md` kilit durumunu kontrol et.

2. **Kilit Kontrolü:** İstenen dosya kilitliyse kullanıcıyı uyar. Değilse kilidi `[DOLU]` yap.

3. **Mimari & Şema:** Projenin `ARCHITECTURE.md` şemasına ve `TECHNICAL_DOC.md` dosyasına bakarak geliştirme yap.

4. **Canlı Güncelleme (Zorunlu):**
   - `ARCHITECTURE.md` Mermaid şemasına yeni modülü/sayfayı ekle.
   - `ACTIVITY_LOG.md` içine yapılan işlemi en üste log olarak yaz.
   - `TECHNICAL_DOC.md` dizin yapısını güncelle.
   - `PROJECT_STATE.md` kilidini tekrar `[BOŞTA]` durumuna getir.

5. **Eşitleme:** Kodları Git ile senkronize et.
