## Cart Flows (Sepet Akışları)

Bu doküman, sepet sisteminin uçtan uca akışlarını; API uçları, strateji seçimi (Guest vs Authenticated), validasyon kuralları, fiyatlandırma koordinasyonu, çoklu para birimi davranışı ve kenar durumlarıyla birlikte açıklar.

- [Genel Bakış](#genel-bakış)
- [Uç Noktalar ve Akışlar](#uç-noktalar-ve-akışlar)
- [Uçtan Uca Akış Diyagramı](#uçtan-uca-akış-diyagramı)
- [Strateji Seçimi (Guest vs Authenticated)](#strateji-seçimi-guest-vs-authenticated)
- [Validasyon Kuralları](#validasyon-kuralları)
- [Fiyatlandırma Koordinasyonu](#fiyatlandırma-koordinasyonu)
- [Çoklu Para Birimi Davranışı](#çoklu-para-birimi-davranışı)
- [Olaylar (Events)](#olaylar-events)
- [Kenar Durumları (Edge Cases)](#kenar-durumları-edge-cases)
- [İlgili Dosya ve Klasör Yapısı](#ilgili-dosya-ve-klasör-yapısı)
 - [Mimariler, Desenler, Prensipler](#mimariler-desenler-prensipler)

---

## Genel Bakış

- Sepetinizi görüntüleyebilir, ürün ekleyebilir, miktar güncelleyebilir, ürünü kaldırabilir veya sepeti tamamen temizleyebilirsiniz.
- Fiyatlar her işlemden sonra otomatik güncellenir; kupon/kampanya gibi indirimler sepete yansır.
- Giriş yaptıysanız sepetiniz hesabınıza bağlıdır; misafirseniz oturum bazında takip edilir ve giriş yaptığınızda sepetiniz hesabınıza taşınabilir.

---

## Uç Noktalar ve Akışlar

- GET `/api/v1/cart` — Sepeti görüntüle
  - `show()` metodu, `getOrCreateCart()` ile (kullanıcı girişliyse user cart, değilse session cart) sepeti getirir/oluşturur.
  - `currency` isteğe bağlı query paramı ile hedef para birimi alınır (varsayılan `TRY`).
  - `CartService->calculateSummary(...)` çağrılır; yanıt `CartResource` + `CartSummaryResource` ile döner.

- POST `/api/v1/cart/items` — Sepete ürün ekle
  - `AddItemRequest` doğrulaması sonrası `ProductVariant` bulunur.
  - `CartStrategyFactory->create($cart)` ile uygun strateji seçilir; `CartService->setStrategy(...)` uygulanır.
  - `CartService->addItem($cart, $variant, $quantity)` akışı:
    - `CartValidationService->validateAddItem(...)`
    - Strateji `addItem` (miktar birleştirme veya yeni kayıt)
    - `CartPriceCoordinator->updateCartPricing($cart)`
    - `CartItemAdded` olayı yayınlanır
  - Güncel sepet ve özet döner.

- PUT `/api/v1/cart/items/{item}` — Miktar güncelle
  - Akış: strateji seçimi → `CartService->updateQuantity(...)` → validasyon → fiyat güncellemesi → `CartItemUpdated` → yanıt.

- DELETE `/api/v1/cart/items/{item}` — Ürünü kaldır
  - Akış: strateji seçimi → `CartService->removeItem(...)` → `CartPriceCoordinator` güncellemesi → `CartItemRemoved` → yanıt.

- DELETE `/api/v1/cart` — Sepeti temizle
  - Akış: strateji seçimi → `CartService->clearCart(...)` → `CartCleared` → yanıt (boş sepet + özet).

- GET `/api/v1/cart/summary` — Sadece özet
  - Akış: `getOrCreateCart()` → `CartService->calculateSummary(...)` → `CartSummaryResource` yanıtı.

- PUT `/api/v1/cart/refresh-pricing` — Fiyatları yenile
  - Akış: strateji seçimi → `CartService->refreshPricing($cart)` → `CartService->calculateSummary(...)` → yanıt.

- POST `/api/v1/cart/migrate` — Misafir sepetini kullanıcıya taşı (auth gerektirir)
  - Giriş yapılmamışsa 401.
  - `CartService->migrateGuestCart($sessionId, $user)`
    - Eğer kullanıcı sepeti varsa: misafir sepetindeki kalemler kullanıcının sepetine birleştirilir (miktarlar toplanır), misafir sepeti silinir.
    - Yoksa: misafir sepeti kullanıcı sepetine dönüştürülür (user_id atanır, session_id null).
  - Ardından fiyatlar güncellenir ve güncel sepet + özet döner.

---

## Uçtan Uca Akış Diyagramı

```mermaid
flowchart TD
  A[GET /cart] -->|getOrCreateCart| B[Cart]
  B --> C{Auth?}
  C -- Yes --> D[AuthenticatedCartStrategy]
  C -- No --> E[GuestCartStrategy]
  A --> F[calculateSummary]
  F --> G[CartSummaryResource]

  H[POST /cart/items] --> I[AddItemRequest]
  I --> J[CartValidationService]
  J --> K[Strategy.addItem]
  K --> L[CartPriceCoordinator.update]

  M[PUT /cart/items/{id}] --> N[updateQuantity]
  N --> L

  O[DELETE /cart/items/{id}] --> P[removeItem]
  P --> L

  Q[PUT /cart/refresh-pricing] --> L

  R[POST /cart/migrate] --> S{Auth?}
  S -- No --> T[401]
  S -- Yes --> U[migrateGuestCart]
  U --> L
```

---

## Strateji Seçimi (Guest vs Authenticated)

- `CartStrategyFactory->create($cart)` karar verir:
  - `AuthenticatedCartStrategy`: `user_id` olan sepetler (DB tabanlı, kalıcı kayıtlar ve dokunuşlarla `updated_at` güncellenir).
  - `GuestCartStrategy`: `session_id` ile takip edilen misafir sepeti (oturum tabanlı davranış; proje içinde DB eşlemesiyle de desteklenebilir).

Stratejiler; ekleme, miktar güncelleme, kaldırma ve temizleme işlemlerini kendi bağlamlarında uygular.

---

## Validasyon Kuralları

- Miktar kuralları: > 0 ve ≤ 999.
- Stok kontrolü: istenen/toplam miktar stoktan büyük olamaz.
- Ürün/variant aktiflik kontrolü: pasif ürün/variant sepete alınamaz.
- Güncellenecek kalem mevcut olmalı; negatif miktar kaldırma anlamına gelir.
- Checkout validasyonları:
  - Sepet boş olamaz.
  - Her kalem için erişilebilirlik/stok ve fiyat güncelliği kontrolü.
  - B2B ise: bayi onayı, kredi limiti, minimum sipariş tutarı.

Kaynak: `CartValidationService` metodları (addItem, quantityUpdate, forCheckout).

---

## Fiyatlandırma Koordinasyonu

- `CartPriceCoordinator`:
  - Kalem bazında `PricingService->calculatePrice(variant, quantity, user)` ile fiyat hesaplar; birim/kalem indirimlerini ve `price_calculated_at` alanını günceller.
  - Sepet özeti (subtotal, total, discounts, item details) üretir ve sepet üst verilerini (`subtotal_amount`, `total_amount`, `applied_discounts`, `customer_type`) günceller.
  - Özet hesapları 300 sn cache’lenir (cache anahtarı sepet `updated_at` zaman damgasına bağlıdır).
  - `refresh-pricing` çağrısı tüm kalemleri yeniden hesaplayıp sepeti günceller ve özet cache’ini temizler.

---

## Çoklu Para Birimi Davranışı

- GET `/api/v1/cart` çağrısında `currency` (veya `_currency`) paramı ile hedef para birimi istenebilir.
- Yanıtta `available_currencies` listesi sağlanır.
- Varyant fiyatları sistemde TRY olarak normalize edilir; gösterim tarafında hedef para birimine dönüştürme yapılır (çoklu para birimi servisleri üzerinden).

Not: Fiyatlama hesapları sepet/kalem bazında TRY normalize edilerek saklanır; dönüşüm katmanı çıktı tarafında uygulanır.

---

## Olaylar (Events)

- `CartItemAdded($cart, $variant, $quantity)`
- `CartItemUpdated($cart, $item, $oldQuantity, $newQuantity)`
- `CartItemRemoved($cart, $item)`
- `CartCleared($cart, $removedCount)`

Bu olaylar analytics/izleme, bildirim veya harici sistem entegrasyonları için kullanılabilir.

---

## Kenar Durumları (Edge Cases)

- Negatif/sıfır miktar: reddedilir (güncellemede 0 → silme davranışı uygulanabilir bağlama göre).
- Stok yetersizliği: ekleme/güncelleme reddedilir; mesajda mevcut/yetersiz stok gösterilir.
- Pasif ürün/variant: sepete dahil edilemez; güncellemede de hata döner.
- Fiyat güncelliği: `price_calculated_at` 24 saati aşmışsa checkout öncesi uyarı/hata üretilebilir.
- Misafir sepeti birleştirme: aynı variant için miktarlar toplanır; çakışmalar tek kalemde birleştirilir.

---

## İlgili Dosya ve Klasör Yapısı

```text
app/
  Http/Controllers/Api/
    CartController.php                  # API uçları ve orchestration
  Services/Cart/
    CartService.php                     # Domain servis (transaction, events)
    CartPriceCoordinator.php            # Fiyat koordinasyonu ve özet
    CartValidationService.php           # İş kuralı validasyonları
    CartStrategyFactory.php             # Strateji seçimi
    AuthenticatedCartStrategy.php       # Kullanıcı sepeti davranışları
    GuestCartStrategy.php               # Misafir sepeti davranışları
  ValueObjects/Cart/
    CartSummary.php                     # Özet değeri (subtotal, total, indirimler)
    CartValidationResult.php            # Validasyon sonucu VO
    CheckoutContext.php                 # Order domain’e aktarım için bağlam
```

---

## Mimariler, Desenler, Prensipler

- Panel değil API-odaklı akış: Sepet işlemleri büyük oranda API üzerinden yürür; panel sadece gözlem/analitik.
- Servis/Strateji ayrımı: `CartService` (iş akışı) + Strategy (guest/auth) net ayrım.
- VO kullanımı: `CartSummary`, tutarlı dönüş ve kolay test.
- Performans: Özet caching (timestamp bazlı), eager loading.
- Hata yönetimi: Validasyon hataları kullanıcı-dostu JSON; transaction güvenliği.

### Notlar

- Sepet toplamları ve kalem fiyatları Pricing domain ile tam entegredir; kampanya/iskonto vb. hesaplar PricingService üzerinden yansır.
- B2B/B2C müşteri tipi tespiti `CustomerTypeDetector` ile yapılır; sepet üst verisine yazılır.


