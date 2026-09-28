# 📖 TEKNİK DOKÜMANTASYON (TECHNICAL DOCS)

> Bu dosya projenin teknik rehberidir. Hangi dosyanın ne işe yaradığı, ortam değişkenleri ve API yapıları burada kayıt altında tutulur.

---

## 1. Dizin Yapısı ve Açıklamalar

```text
EkimMemoOrtak/
├── PROJECT_STATE.md      -> Çakışma önleme, kimin hangi dosyada çalıştığını belirten kilit tablosu
├── ACTIVITY_LOG.md       -> Kronolojik değişiklik günlüğü (Kim, ne zaman, ne yaptı)
├── ARCHITECTURE.md       -> Görsel mimari ve Mermaid şemaları
├── TECHNICAL_DOC.md      -> Teknik detaylar, modüller ve API rehberi
├── .gemini/rules/        -> Antigravity AI'ın uyacağı otomatik ortak çalışma kuralları
└── .gitignore            -> Gizli ve gereksiz dosyaları hariç tutma listesi
```

---

## 2. Ortam ve Sunucu Bilgileri (Environment)

| Parametre | Değer / Açıklama |
| :--- | :--- |
| **Geliştirme Ortamı** | Node.js / PHP / Python / HTML-CSS-JS |
| **Canlı Sunucu / Panel** | cPanel API Entegrasyonu |
| **Veritabanı** | MySQL / MariaDB (cPanel) |
| **Geliştiriciler** | Arif (Şehir 1), Memo (Şehir 2) |

---

## 3. Yeni Özellik / Sayfa Ekleme Standardı

Herhangi bir yeni sayfa veya özellik eklendiğinde:
1. `TECHNICAL_DOC.md` içindeki Dizin Yapısına ilgili dosya eklenir.
2. `ARCHITECTURE.md` içindeki şemaya yeni modül düğümü eklenir.
3. `ACTIVITY_LOG.md`'ye yapılan iş özetlenir.
4. `PROJECT_STATE.md` kilidi temizlenir.
