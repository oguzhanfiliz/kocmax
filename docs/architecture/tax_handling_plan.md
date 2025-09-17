# 📑 KDV Entegrasyon Planı

Bu doküman, ürün/ kategori bazlı KDV (% vergi) oranlarının sisteme eklenmesi ve fiyat hesaplama/ödeme akışlarına entegre edilmesi için atılması gereken adımları özetler.

---

## 🎯 Amaçlar

- Admin panelinden **genel**, **kategori** ve **ürün** seviyelerinde KDV oranı tanımlayabilmek.
- KDV tutarının **indirimsiz fiyattan değil, indirim uygulanmış net tutardan** hesaplanmasını sağlamak.
- PayTR'ye gönderilen sepet/basket verilerinde KDV dahil fiyatları iletebilmek.
- API cevaplarında KDV hariç ve KDV dahil fiyatları ayrı alanlar olarak döndürmek; böylece frontend ihtiyaca göre `indirimli + KDV` veya doğrudan `KDV dahil` fiyatı gösterebilsin.
- Sipariş/raporlama tarafında KDV tutarını ayrıca saklayıp görüntülemek.

---

## 🧱 Veri Modeli / Migration Adımları

1. **Products & Categories**
   - `products.tax_rate` ve `categories.tax_rate` alanları nullable decimal olarak eklendi.
   - Öncelik sırası: `ürün > kategori > global varsayılan`.
2. **Settings tablosu**
   - Varsayılan KDV için `pricing.default_tax_rate` kaydı oluştur/ güncelle (örn. %20).
3. **Orders / Order Items**
   - Sipariş kalemlerinde `tax_rate` ve `tax_amount` alanlarının mevcut olduğundan emin ol; yoksa migration ile ekle.

**Not:** Şimdilik ürün grubu / kampanya bazlı oran gereksinimi yok; ileride ihtiyaç olursa ek tablolar düşünülebilir.

---

## ⚙️ Backend / Domain Değişiklikleri

### 1. Ayarlar & Admin Paneli
- Mevcut ürün ve kategori formlarına `tax_rate` (0-100 arası) alanı ekle.
- Genel ayarlar ekranına `Varsayılan KDV (%)` alanı ekle (şimdiden `pricing.default_tax_rate` kaydı mevcut).
- Ek bir “KDV yönetimi” sekmesi açılmayacak; kullanıcı aynı form üzerinde oranı yönetebilecek.

### 2. Pricing Engine
- `PriceResult` veya `PricingService` hesaplamasına yeni bir katman ekle:
  - İndirimler uygulandıktan sonra `net_total` üzerinden KDV hesapla.
  - `PriceResult` içinde `taxAmount`, `taxRate`, `grossPrice` gibi alanlar barındırılmalı.
  - Müşteri tipine göre KDV oranı değişmeyecek; hesap yalnızca ürün/kategori/global fallback’ine göre yapılacak.
- Cache (PriceEngine) invalidation stratejisini güncelle; KDV değişiklikleri cache'i geçersiz kılmalı.

### 3. Campaign & Discount Entegrasyonu
- Kampanyalar net fiyatı değiştirdiği için KDV hesaplaması kampanya sonrasında koşulmalı.
- Ücretsiz kargo / kampanya KDV’yi etkilemez, ancak `CampaignResult` çıktıları KDV hariç net toplamı döndürdüğünden emin ol.

### 4. Sipariş Oluşturma Akışı
- OrderController `createOrderFromCart` içinde `tax_amount` artık hesaplanmış değer olmalı.
- `OrderItem` kayıtlarında `tax_rate` ve `tax_amount` sakla.
- KDV toplamı sipariş düzeyinde (`orders.tax_amount`) güncellenmeli.

### 5. Payment / PayTR Entegrasyonu
- `PayTrTokenService::prepareBasketData` kurgusu ürün satırlarını `KDV dahil` olacak şekilde güncellemeli.
- PayTR documents: `paytr_token` hesaplanırken kullanılan miktar (payment_amount) KDV dahil olmalı.
- KDV oranını PayTR destekliyorsa ilave metadata ile ilet (gerekirse belgeyi kontrol et).

---

## 🧾 API Değişiklikleri

### Ürün Listeleri & Detayları
- `ProductListResource`, `ProductDetailResource` gibi resource'lara aşağıdaki alanları ekle:
  - `price_excl_tax`
  - `price_incl_tax`
  - `tax_rate`
  - `tax_amount`
- Kampanya sonrası indirimli fiyatların hem KDV hariç hem KDV dahil versiyonları döndürülmeli.

### Sepet / Checkout API
- `processCheckoutPayment` ve ilgili response'larda `tax_amount` ve `tax_rate` alanları ekle.
- Frontend, ödeme sayfası öncesinde KDV dahil toplamları görebilmeli.

### Admin API
- KDV ayarlarının listelenmesi/ güncellenmesi için endpointler (varsa Filament API’si). Gerekmiyorsa sadece panel UI yeterli.

---

## 🧮 Hesaplama Sırası Önerisi

1. Birim baz fiyatı al (vergisiz).
2. İndirimleri uygula (kampanya, müşteri indirimi, kupon vs).
3. Net satır tutarı = `indirim sonrası fiyat * miktar`.
4. Uygulanacak KDV oranını belirle:
   - Ürün özel > kategori > global ayar.
5. `tax_amount = net satır tutarı * (tax_rate / 100)`.
6. `gross_total = net satır + tax_amount`.
7. Sipariş genelinde KDV toplamını topla, `orders.tax_amount` ve `orders.total_amount = subtotal + tax + shipping` olacak şekilde güncelle.

---

## ✅ Test / Doğrulama Planı

- **Unit Testler**: PricingService için farklı kombinasyonlarda (ürün özel KDV, kategori KDV, global default) vergi hesabı doğrulansın.
- **Feature Testleri**: Checkout sürecinde API response’larının KDV alanları içerdiği doğrulansın.
- **Integration**: PayTR token oluşturma isteğinde gönderilen `payment_amount` ve `user_basket` değerleri KDV dahil olmalı.
- **Regression**: Ücretsiz kargo, farklı müşteri tipleri, ürün kampanyaları gibi senaryolarda net/gross tutarlar tutarlı mı?

---

## 📝 Açık Sorular / Netleştirilmesi Gerekenler

- B2B veya müşteri tipine göre farklı KDV oranı gereksinimi bulunmuyor.
- Ürün varyantı özelinde farklı KDV oranına ihtiyaç var mı?
- PayTR tarafında satır bazında KDV oranı gönderme zorunluluğu var mı? Dokümantasyon teyit edilmeli.
- İade / iade edilen siparişlerde KDV hesaplaması nasıl ele alınacak?

---

## 📌 Yol Haritası (Öneri)

1. Migration + veri modeli (tax rate alanları).
2. Admin panel/ayar ekranları.
3. Pricing engine güncellemesi + unit testler.
4. Order/payments entegrasyonu (PayTR dahil).
5. API resource güncellemeleri.
6. Dokümantasyon + QA testleri + rollout.
