# 🛠️ KDV Entegrasyonu – Geliştirme Görevleri

Bu dosya, KDV (tax) planının hayata geçirilmesi için takip edilmesi gereken geliştirme görevlerini aşamalara ayırır.

---

## 1. Veri Modeli ve Altyapı
- [x] `products.tax_rate` ve `categories.tax_rate` sütunlarını ekleyen migration'ı oluştur (tamamlandı: `2025_09_20_120000_add_tax_rates_to_products_and_categories.php`).
- [x] Migration'ı çalıştırıp mevcut verinin tutarlılığını doğrula (staging/prod planı hazırlansın).
- [x] `orders` ve `order_items` tablolarında KDV için gerekli alanlar mevcut mu kontrol et; eksikse migration ekle (`tax_rate`, `tax_amount`).
- [x] Varsayılan KDV oranı (`pricing.default_tax_rate`) için seed/migration sonrası veritabanı kaydını doğrula.

## 2. Admin Panel & Ayarlar
- [x] Filament ürün formuna `tax_rate` alanı ekle (0-100 arası, yüzde formatı, yardımla metin).
- [x] Filament kategori formuna `tax_rate` alanı ekle ve yardım metniyle ürün fallback mantığını açıkla.
- [x] Genel ayarlar (pricing) ekranına `Varsayılan KDV (%)` alanını ekle veya mevcut alanı `pricing.default_tax_rate` kaydı ile bağla.
- [x] KDV alanlarında validation kurallarını ve yetki kontrollerini ekle (örn. sadece yetkili roller düzenleyebilsin).

## 3. Pricing Motoru
- [ ] `PricingService` / `PriceEngine` kapsamına KDV hesaplamasını entegre et: indirim sonrası net tutardan KDV oranını uygula.
- [ ] `PriceResult` VO'suna `tax_rate`, `tax_amount`, `total_with_tax` (veya benzeri) alanlarını ekle.
- [ ] KDV fallback mantığını (ürün > kategori > global) kapsayan yardımcı metot yaz ve fiyat hesaplarında kullan.
- [ ] Cache mekanizmasında (PriceEngine) KDV değişiklikleri sonrası invalidation stratejisini güncelle.

## 4. Kampanya ve Sepet Entegrasyonu
- [ ] `CampaignPricingService` çıktılarını güncelleyerek kampanya sonrası net tutarlar üzerinden KDV hesaplandığından emin ol.
- [ ] Ücretsiz kargo, paket indirimi vb. kampanyalarda KDV hesaplamasının etkilenmediği senaryoları doğrula.
- [ ] Sepet / checkout sürecinde KDV dahil toplamın ve KDV tutarının sipariş verisine aktarıldığını kontrol et.

## 5. Sipariş Oluşturma & Ödeme
- [ ] `OrderController::createOrderFromCart` içinde satır bazında ve toplamda KDV hesapla/ata.
- [ ] `OrderItem` creation sürecinde `tax_rate` ve `tax_amount` değerlerini kaydet.
- [ ] `orders.total_amount` hesabını KDV ve kargo dahil olacak şekilde güncelle.
- [ ] PayTR entegrasyonunda (`PayTrTokenService::prepareBasketData`) satır fiyatlarını KDV dahil hale getir ve `payment_amount`u brüt tutar üzerinden hesapla.

## 6. API & Resource Güncellemeleri
- [ ] `ProductListResource`, `ProductDetailResource` gibi kaynaklara aşağıdaki alanları ekle: `price_excl_tax`, `price_incl_tax`, `tax_rate`, `tax_amount`.
- [ ] Checkout/Order API response’larında KDV tutarı ve oranını dahil et (örn. `processCheckoutPayment`, `OrderResource`).
- [ ] Swagger/OpenAPI dokümantasyonunu yeni alanlarla güncelle.

## 7. Test ve Doğrulama
- [ ] Unit test: farklı KDV kaynakları (ürün, kategori, global) için fiyat hesaplamasını doğrula.
- [ ] Feature test: Checkout akışında API’nin KDV alanlarını döndürdüğünü doğrula.
- [ ] Integration test: PayTR token isteğinde gönderilen `user_basket` ve `payment_amount` değerlerinin KDV dahil olduğunu doğrula.
- [ ] Manuel test senaryoları: indirimli ürün, kampanya, ücretsiz kargo, farklı kategoriler gibi kombinasyonlarda sonuçları kontrol et.

## 8. Yayına Hazırlık
- [ ] Tax ayarlarının yönetimi için dokümantasyon ve iç eğitim notu hazırla.
- [ ] Deployment sonrası KDV değerlerini doğrulamak için checklist oluştur.
- [ ] Monitoring/alerting: KDV hesaplarına dair logging veya dashboard ihtiyacını değerlendir.

---

> Not: Görevler sıralı ilerleyişi temsil eder; bağımlılıkları göz önünde bulundurarak sprint planlaması yapılmalıdır.
