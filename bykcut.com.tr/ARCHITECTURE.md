# 🏗️ BYKCUT.COM.TR - MİMARİ VE CANLI ŞEMA

```mermaid
flowchart TD
    subgraph Frontend ["🎨 Kullanıcı Arayüzü"]
        Home["🏠 Anasayfa"]
        Products["✂️ Ürün & Hizmet Kataloğu"]
        Booking["📅 Randevu / Sipariş Modülü"]
        Cart["🛒 Sepet & Ödeme"]
    end

    subgraph Backend ["⚙️ API & Servisler"]
        CoreAPI["🔌 Çekirdek API"]
        PaymentAPI["💳 Ödeme Geçidi"]
    end

    subgraph Database ["🗄️ Veritabanı"]
        DB[("🗄️ MySQL Veritabanı")]
    end

    Frontend --> Backend
    Backend --> Database
```
