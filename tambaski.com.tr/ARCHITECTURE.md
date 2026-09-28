# 🏗️ TAMBASKI.COM.TR - MİMARİ VE CANLI ŞEMA

```mermaid
flowchart TD
    subgraph Frontend ["🎨 Kullanıcı Arayüzü"]
        Home["🏠 Anasayfa"]
        Products["📦 Baskı Ürünleri"]
        Customizer["🖌️ Kişiye Özel Tasarım / Baskı Editörü"]
        Cart["🛒 Sepet & Sipariş"]
    end

    subgraph Backend ["⚙️ API & İş Mantığı"]
        OrderAPI["📋 Sipariş & Dosya Yükleme API"]
        PaymentAPI["💳 Ödeme API (PayTR / İyzico)"]
    end

    subgraph Storage ["🗄️ Veri & Dosya Depolama"]
        DB[("🗄️ Veritabanı (MySQL)")]
        Uploads[("📁 Yüklenen Tasarım Dosyaları")]
    end

    Frontend --> Backend
    Backend --> Storage
```
