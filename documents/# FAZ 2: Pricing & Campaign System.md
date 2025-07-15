# FAZ 2: Pricing & Campaign System

**Amaç:** Bayilere özel iskontolar ve fiyat listeleri tanımlamak. "3 al 2 öde" gibi esnek pazarlama kampanyaları ve kupon kodları oluşturacak altyapıyı kurmak.

---

## Sprint 1: Pricing Strategy (Bitiş Tarihi: 19.08.2025)

### Görev: [FEATURE] Implement Pricing Strategy System

**Senaryo:** Farklı bayi gruplarına veya doğrudan bayinin kendisine özel fiyat listeleri ve iskontolar tanımlayabilmek. Örneğin, "Altın Bayi" grubuna tüm ürünlerde %20 iskonto, "X Bayisi"ne ise sadece "Montlar" kategorisinde %25 iskonto tanımlamak.

**Teknik Adımlar (Strategy Pattern):**
1.  **Modeller:** `PriceList` (fiyat listesi adı), `PriceRule` (kural: hangi ürüne/kategoriye ne kadar iskonto/sabit fiyat uygulanacağı) modellerini oluştur.
2.  **İlişkiler:** `PriceList` modelini `User` veya `Role` modeline bağla.
3.  **Fiyat Hesaplama Servisi:** `ProductPriceCalculator` adında bir servis oluştur. Bu servis, bir kullanıcı ve ürün aldığında, o kullanıcıya uygulanacak tüm kuralları (genel kampanya, bayi grubu iskontosu, bayiye özel iskonto) sırayla işleyerek nihai fiyatı döndürür. Strategy Pattern, farklı kural tiplerini (yüzdesel, sabit fiyat vb.) uygulamak için kullanılacaktır.
4.  **Filament Arayüzü:** Yöneticinin fiyat listeleri ve kuralları oluşturabileceği bir arayüz tasarla.

---

## Sprint 2: Campaign System (Bitiş Tarihi: 26.08.2025)

### Görev: [FEATURE] Implement Campaign & Discount System

**Senaryo:** Sepet bazında veya ürün bazında dinamik kampanyalar oluşturmak. Örnek: "1000 TL ve üzeri alışverişe %10 indirim", "A ürününden 3 alana 1 bedava", "XYZ-2025" kupon kodu ile 50 TL indirim.

**Teknik Adımlar (Decorator Pattern):**
1.  **Modeller:** `Campaign` (kampanya kuralları), `Coupon` (kupon kodları) modellerini oluştur.
2.  **Sepet Altyapısı:** Henüz yoksa, temel bir sepet (Cart) sistemi kur.
3.  **İndirim Hesaplayıcı:** Sepet tutarını hesaplayan bir `CartTotalCalculator` sınıfı oluştur. Decorator Pattern kullanarak, bu hesaplayıcının üzerine kampanyaları ve kuponları "giydirerek" indirimleri uygula. Her kampanya, sepetin toplamını değiştiren bir `Decorator` olur.
4.  **Filament Arayüzü:** Yöneticinin farklı türlerde (sepette X TL indirim, X al Y öde vb.) kampanyalar ve kuponlar oluşturabileceği bir panel tasarla.