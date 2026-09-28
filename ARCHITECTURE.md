# 🏗️ SİSTEM MİMARİSİ VE CANLI ŞEMA (ARCHITECTURE)

> **Antigravity Kuralı:** Projeye yeni bir sayfa, API, veritabanı tablosu veya modül eklendiğinde aşağıdaki Mermaid şemalarını **güncelle**. Böylece yeni sohbette yapay zeka projeyi tek bakışta tam olarak kavrar.

---

## 1. Genel Sistem & Çalışma Akış Şeması

```mermaid
flowchart TD
    subgraph Geliştiriciler ["👥 Geliştirici Ekibi (Farklı Şehirler)"]
        Arif["💻 Arif (Antigravity)"]
        Memo["💻 Memo (Antigravity)"]
    end

    subgraph CanliYonetim ["🛡️ Kilit & Dokümantasyon Katmanı"]
        Lock["🔒 PROJECT_STATE.md (Kilit / Çakışma Önleme)"]
        Log["📜 ACTIVITY_LOG.md (Kim Ne Yaptı Kaydı)"]
        Arch["🏗️ ARCHITECTURE.md (Canlı Şemalar)"]
    end

    subgraph BulutRepo ["☁️ Merkezi Senkronizasyon (GitHub / cPanel)"]
        Repo[("📦 Git Deposu (Private)")]
        Server["🌐 cPanel / Web Sunucusu (Canlı Test)"]
    end

    subgraph ProjeModulleri ["🚀 Uygulama Modülleri (Örnek E-Ticaret/Web)"]
        Frontend["🎨 Frontend (Kullanıcı Arayüzü)"]
        API["⚙️ Backend / API Servisleri"]
        DB[("🗄️ Veritabanı (MySQL / PostgreSQL)")]
    end

    Arif -->|1. Kilit Kontrolü & Çek| Lock
    Memo -->|1. Kilit Kontrolü & Çek| Lock
    Lock -->|2. Görev Başlat & Koru| ProjeModulleri
    ProjeModulleri -->|3. Şema & Log Güncelle| Arch
    ProjeModulleri -->|3. Şema & Log Güncelle| Log
    Log -->|4. Otomatik Eşitle| Repo
    Repo -->|5. Otomatik Canlıya Al| Server
```

---

## 2. Modül ve Veri Akış Şeması

```mermaid
flowchart LR
    User(["👤 Müşteri / Ziyaretçi"]) --> UI["🖥️ Web Arayüzü"]
    UI --> Auth["🔐 Kimlik Doğrulama"]
    UI --> Catalog["📦 Ürün Kataloğu"]
    UI --> Cart["🛒 Sepet & Sipariş"]
    
    Auth --> API["🔌 REST API / Controller"]
    Catalog --> API
    Cart --> API
    
    API --> Payment["💳 Ödeme Geçidi (PayTR/İyzico vb.)"]
    API --> Database[("🗄️ Veritabanı")]
```

---

## 3. Bileşen Haritası (Component Map)

| Modül Adı | Açıklama | Ana Dosya / Dizin | Bağımlılıklar |
| :--- | :--- | :--- | :--- |
| **Ortak Altyapı** | Kilit, Log, Şema ve Kurallar | `/`, `.gemini/rules/` | Antigravity AI |
| **Arayüz (Frontend)** | Tasarım ve Sayfalar | `src/` (veya `public/`) | Backend API |
| **Backend & API** | Sunucu tarafı işlemler, Veritabanı | `api/` (veya `server/`) | cPanel MySQL |

---
*(Proje geliştikçe yeni modüller ve tablolar yukarıdaki şemaya eklenecektir)*
