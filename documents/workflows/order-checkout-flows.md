## Order & Checkout Flows (Sipariş ve Checkout Akışları)

Kısa özet: Sepet doğrulaması sonrasında `CheckoutCoordinator` sipariş oluşturmayı koordine eder; ödeme, kargo ve bildirim süreçleri `Order` servisleriyle entegredir. Guest checkout ve sipariş takibi desteklidir.

- [Özet Akış](#özet-akış)
- [Detaylı Adımlar](#detaylı-adımlar)
- [Mimari ve Dosya Yapısı](#mimari-ve-dosya-yapısı)
- [Senaryolar](#senaryolar)

---

## Özet Akış

1) Sepet checkout validasyonu (stok, min tutar, bayi kuralları).
2) `CheckoutContext` oluşturma (özet, kalemler, müşteri tipi).
3) Sipariş oluşturma → ödeme başlatma → bildirim.
4) Başarılıysa sepet temizleme.

---

## Detaylı Adımlar

- `CartService->prepareCheckout()` → `CheckoutContext` üretir.
- `OrderCreationService` context’ten order/kalemleri oluşturur.
- `OrderPaymentService` ödeme sürecini başlatır (provider entegrasyonuna hazır).
- `OrderNotificationService` e-posta/olay bildirimi gönderir.

---

## Mimari ve Dosya Yapısı

```text
app/
  Services/Checkout/
    CheckoutCoordinator.php          # Checkout orkestrasyonu
    CheckoutValidationService.php    # Ek validasyon adımları
  Services/Order/
    OrderCreationService.php         # Sipariş oluşturma
    OrderPaymentService.php          # Ödeme akışı
    OrderNotificationService.php     # Bildirimler
    States/                          # Sipariş durumu state pattern
```

---

## Senaryolar

- Guest checkout: adres ve iletişim bilgisi ile sipariş; e-posta ile takip linki.
- Auth checkout: kayıtlı adres/cihaz bazlı token ile.
- Ödeme başarısız: sipariş pending/cancel akışı; tekrar deneme.


