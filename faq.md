# Sıkça Sorulan Sorular (SSS) - Fiyatlandırma Sistemi

Bu doküman, gelişmiş fiyatlandırma sistemimiz hakkında sıkça sorulan soruları ve cevaplarını içerir.

---

### Soru 1: Bu gelişmiş fiyatlandırma sisteminin amacı nedir? Neden bu kadar çok tasarım deseni kullanıldı ve kritik noktaları nelerdir?

**Cevap:**

Bu sistemin temel amacı, eski dağınık ve hataya açık fiyat hesaplama mantığını, merkezi, esnek ve kolay yönetilebilir **"akıllı bir kasa"** ile değiştirmektir. Bu "akıllı kasa", her müşteriye (B2B, B2C, misafir) ve her senaryoya (toplu alım, kupon, sezon indirimi) göre her zaman doğru ve en avantajlı fiyatı saniyeler içinde hesaplar.

Bu sistemi bu kadar güçlü kılan "tasarım desenleri" ve arkasındaki mantık şudur:

1.  **"Strateji Defterleri" (Strategy Pattern):** Her müşteri tipi (B2B, B2C) için ayrı bir hesaplama yöntemi kullanırız. Yeni bir müşteri tipi (örneğin, "VIP Üyeler") eklemek, mevcut sistemi hiç bozmadan sadece yeni bir "defter" eklemek kadar kolaydır.

2.  **"İndirim Uzmanları Zinciri" (Chain of Responsibility):** Bir ürünün fiyatı hesaplanırken, sırayla bir dizi "indirim uzmanından" geçer. Her uzman sadece kendi alanına bakar (biri toplu alıma, diğeri kupon koduna bakar). Bu, indirim kurallarının birbiriyle karışmasını engeller ve yeni bir indirim türü (örneğin, "Kara Cuma İndirimi") eklemeyi çok basit hale getirir.

3.  **"Fiyat Süsleyicileri" (Decorator Pattern):** İndirimler bittikten sonra, son fiyat sunuma hazırlanır. Bir "süsleyici" KDV'yi ekler, bir diğeri para birimi simgesini (₺) koyar. Bu, vergi gibi konuları ana fiyat hesaplama mantığından ayırarak sistemi temiz ve yönetilebilir tutar.

**Kritik Noktalar ve Felsefesi:**
*   **Her Parça Tek İş Yapar:** Vergi hesaplayan kod sadece vergiyle, indirim yöneten kod sadece indirimle ilgilenir. Bu, hata bulmayı kolaylaştırır.
*   **Binayı Yıkmadan Oda Ekleme:** Sistem, yeni kurallar eklemeye **AÇIK**, ama bu sırada mevcut çalışan yapıyı bozmaya **KAPALI**'dır.
*   **Esneklik ve Kontrol:** Bu yapı, bize fiyatın her bir bileşenini (net tutar, indirimler, vergiler) ayrı ayrı yönetme kontrolü verir. Bu bir karmaşa değil, tam tersine düzen ve güç demektir.

Daha detaylı, analojilerle zenginleştirilmiş açıklamayı `documents/fiyatlandirma_sisteminin_kalbi.md` dosyasında bulabilirsiniz.

---

### Soru 2: Eğer kullanıcı, admin panelinde ürün fiyatını KDV dahil olarak girmek isterse, bu kadar detaylı bir sistemle onu gereksiz detaya boğmuş olmuyor muyuz?

**Cevap:**

**Hayır, kesinlikle olmuyoruz.** Tam tersine, bu mimari tam da bu tür pratik ihtiyaçları zarafetle karşılamak için tasarlanmıştır.

Bu ihtiyacı **"Fiyat Süsleyicileri" (Decorator Pattern)** sayesinde çözeriz. Normalde `TaxDecorator` (Vergi Süsleyicisi), hesaplanan net fiyata KDV **ekler**.

Ancak, kullanıcıya panelde **"Bu Fiyat KDV Dahil mi?"** diye bir seçenek sunarak bu davranışı akıllıca yönetebiliriz:

*   **Eğer kullanıcı "Evet" derse:** `TaxDecorator` bu sefer fiyata KDV eklemek yerine, girilen brüt fiyattan KDV'yi **ayıklayarak** net fiyatı bulur ve veritabanına net fiyatı doğru bir şekilde kaydeder.
*   **Eğer kullanıcı "Hayır" derse:** Sistem normal çalışır ve girilen net fiyata KDV ekler.

**Bu yaklaşımın faydaları:**

1.  **Veri Bütünlüğü:** Her zaman ürünün **net fiyatını** biliriz. Bu, doğru raporlama ve muhasebe için hayatidir.
2.  **Esneklik:** Kullanıcıyı tek bir yönteme zorlamayız. İsteyen net, isteyen brüt fiyat girebilir.
3.  **Geleceğe Hazırlık:** KDV oranı değiştiğinde, sadece tek bir ayarı güncelleriz ve tüm sistem yeni orana göre doğru çalışmaya devam eder. Veritabanındaki tüm fiyatları tek tek değiştirmemiz gerekmez.

Kısacası, bu "detay" aslında bir karmaşa değil, kullanıcıya esneklik sunan ve veri tutarlılığını koruyan bir **kontrol mekanizmasıdır**.

---

### Soru 3: Ürün veya varyant eklerken girdiğim bir fiyat var. Eğer hiçbir fiyatlandırma kuralı (indirim, kampanya vb.) eklemezsem o fiyat mı geçerli olur?

**Cevap:**

**Evet, kesinlikle.** Bu, sistemin en temel ve en önemli çalışma prensibidir.

Ürün veya varyant için girdiğiniz fiyat, o ürünün **"etiket fiyatıdır"** (taban fiyat). Bu, tüm hesaplamaların başlangıç noktasıdır.

Oluşturduğunuz tüm fiyatlandırma kuralları, bu temel etiket fiyatının üzerine uygulanan birer **istisnadır**. Sistem bir fiyatı hesaplarken:

1.  Önce ürünün etiket fiyatını okur.
2.  Sonra, o anki müşteri ve ürün için geçerli bir kural veya indirim olup olmadığını kontrol eder.
3.  **Eğer hiçbir kural bulamazsa,** fiyata hiçbir değişiklik yapmaz.

Dolayısıyla, ürünün kendi fiyatı, sistemin **"güvenli limanıdır"**. Her koşulda bir fiyatın olmasını sağlar. Fiyatlandırma kuralları ise bu temel fiyatı daha dinamik ve akıllı hale getiren güçlü bir katmandır. O katman boşsa, her zaman temel etiket fiyatı geçerli olur.

---

### Soru 4: Peki şu an sistemimizde bir `TaxDecorator` var mı? Çünkü burada, Türkiye'de, KDV yüzdeleri ürüne göre değişir.

**Cevap:**

**Hayır, henüz fiziksel olarak kodlanmış bir `TaxDecorator.php` dosyası yok.**

Mevcut mimari dokümanlarında `TaxDecorator`, **planlanan mimarinin bir parçası** olarak tanımlanmıştır. Yani, bu işi yapacak olan "uzmanın" yeri ayrılmış ve görevi belirlenmiştir, ancak henüz inşa edilmemiştir. Şu ana kadar geliştirme, sistemin çekirdek altyapısına (fiyatlandırma stratejileri, veritabanı yapısı vb.) odaklanmıştır.

**Peki, ürüne göre değişen KDV oranlarını bu mimari nasıl karşılayacak?**

Mimari, bu ihtiyacı karşılamak için **mükemmel bir şekilde tasarlanmıştır.** `TaxDecorator` kodlandığında, tam olarak bu senaryoya göre çalışacaktır:

1.  **Veritabanı Ayarlaması:** `products` (ürünler) tablosuna her ürünün kendi KDV oranını tutacak bir alan (`tax_percentage` gibi) eklenir. Bu, her ürünün farklı bir KDV oranına sahip olabilmesini sağlar.

2.  **Akıllı `TaxDecorator` Mantığı:** `TaxDecorator` oluşturulduğunda, görevi fiyata körü körüne bir KDV eklemek olmayacak. Bunun yerine:
    *   Fiyatı hesaplanacak olan **ürünün kendisini** de parametre olarak alacak.
    *   Ürünün veritabanındaki kaydından o ürüne özel KDV oranını okuyacak.
    *   Hesaplamayı bu **dinamik orana** göre yapacak.

**Sonuç olarak:** Sistemde henüz fiziksel bir `TaxDecorator` dosyası olmasa da, mimari, Türkiye'deki gibi ürüne göre değişen vergi sistemlerini destekleyecek şekilde **önceden düşünülerek** tasarlanmıştır. Bu ihtiyacı, veritabanına küçük bir ekleme yaparak ve `TaxDecorator`'ı planlandığı gibi akıllıca kodlayarak zarif bir şekilde çözeceğiz.

---

### Soru 5: Peki müşteri ürün sorguladığında, sistem ürünün resimlerini ve fiyat hesaplamalarını yaparken zorlanmayacak mı?

**Cevap:**

Bu çok önemli bir performans sorusu ve mimari, bu sorunu önlemek için özel olarak tasarlanmıştır. **Sistem bu işlem sırasında zorlanmayacak.** İşte nedenleri:

1.  **Görevler Ayrı Çalışır:** Ürün bilgilerini (resim, açıklama, başlık) getirmek ile o ürünün fiyatını hesaplamak tamamen **ayrı ve bağımsız** işlemlerdir. Fiyat hesaplaması, resimlerin yüklenmesini beklemez; resimler de fiyatın hesaplanmasını beklemez. Bu sayede biri diğerini yavaşlatmaz.

2.  **Fiyat Hesaplama Zaten Çok Hızlı:** Fiyat hesaplama süreci, büyük dosyalarla (resim gibi) çalışmaz. Çoğunlukla sayılarla yapılan matematiksel işlemlerden ve veritabanından sadece ilgili kuralları çeken birkaç küçük ve hızlı sorgudan oluşur. Bu işlemler milisaniyeler içinde tamamlanır.

3.  **Performans Optimizasyonları Planlandı:** Mimari dokümanında da belirtildiği gibi, sistemin hızlı kalması için birkaç katmanlı bir optimizasyon planımız var:
    *   **Önbellekleme (Caching):** Sık sorgulanan bir ürünün fiyatı, her seferinde yeniden hesaplanmak yerine **Redis gibi süper hızlı bir önbellekte** saklanabilir. Sistem önce önbelleğe bakar, eğer fiyat oradaysa anında geri döner. Bu, özellikle popüler ürünlerde performansı kat kat artırır.
    *   **Verimli Sorgulama (Eager Loading):** Bir liste sayfasında 20 ürün gösterilirken, bu 20 ürünün fiyatlandırma kurallarını tek tek 20 ayrı sorguyla değil, **tek bir verimli sorguyla** topluca çekeriz. Bu, "N+1 sorgu problemi" denilen ve sistemleri yavaşlatan yaygın bir sorunu engeller.
    *   **Ön Hesaplama (Pre-calculation):** Gerekirse, belirli müşteri grupları için fiyat listeleri geceleri veya belirli aralıklarla **önceden hesaplanıp** hazır tutulabilir.

4.  **Resimler Ayrı Optimize Edilir:** Ürün resimlerinin hızlı yüklenmesi, fiyatlandırma sisteminden bağımsız olarak, modern web geliştirme teknikleriyle (CDN kullanımı, resim sıkıştırma, lazy loading vb.) zaten ayrıca optimize edilir.

**Özetle:** Sistem, performans göz önünde bulundurularak tasarlandı. Fiyat hesaplama ve ürün bilgilerini gösterme işlemleri birbirinden ayrılarak ve önbellekleme gibi güçlü optimizasyon teknikleri planlanarak, kullanıcının hızlı ve akıcı bir deneyim yaşaması hedeflenmiştir.

---

### Soru 6: Sistemde `B2B`, `B2C`, `Toptan Satış (Wholesale)` ve `Perakende Satış (Retail)` gibi müşteri tipleri tanımlı. Bayi başvurusu kabul edilenlerin `B2B`, normal kayıt olanların ise `B2C` olacağını anladık. Peki, `Toptan Satış` ve `Perakende Satış` tiplerini nasıl tespit edeceğiz veya atayacağız? Bunun için ek bir panel ayarı gerekir mi?

**Cevap:**

Bu çok yerinde bir soru. `Toptan Satış` ve `Perakende Satış` tiplerinin nasıl atanacağı şu anki akışta net değil ve bu durum için **evet, ek bir yönetim mekanizması gereklidir.**

**1. Bu Tiplerin Stratejik Amacı Nedir?**

Bu tipler, sisteme daha esnek bir müşteri segmentasyonu yeteneği kazandırmak için vardır:

*   **`Toptan Satış (Wholesale)`:** Resmi bir bayi olmasa da, tek seferde çok yüksek miktarda alım yapan bir müşteri olabilir (örneğin, bir etkinlik için 1000 adet tişört alan bir şirket). Bu müşteriye, o alıma özel toptan satış iskontoları sunmak isteyebilirsiniz.
*   **`Perakende Satış (Retail)`:** `B2C`'nin bir alt segmenti olarak düşünülebilir. Örneğin, fiziksel mağazanızdan alışveriş yapan veya sadakat programınıza üye olan "sadık perakende müşterilerinizi" bu gruba dahil ederek onlara özel küçük indirimler tanımlayabilirsiniz.

**2. Nasıl Atanacaklar? (Pratik Çözümler)**

Bu atamayı yapmak için iki temel yöntemimiz var:

*   **Yöntem A: Manuel Atama (Admin Kontrolü):** Bu en basit çözümdür. Admin panelindeki **Kullanıcılar** sayfasında, her kullanıcının düzenleme ekranına **"Müşteri Tipi"** adında bir açılır menü eklenir. Bir yönetici, müşterinin davranışlarına göre (örneğin yüksek bir sipariş geçtiğini görerek) tipini manuel olarak `Toptan Satış`'a çekebilir. Bu, özel durumları yönetmek için her zaman gereklidir.

*   **Yöntem B: Otomatik Atama (Kural Motoru):** Bu daha gelişmiş bir çözümdür. Belirli koşullar sağlandığında, sistem bir müşterinin tipini otomatik olarak güncelleyebilir. Örneğin: "Eğer bir `B2C` müşterisinin son 3 aydaki toplam harcaması 50.000 TL'yi geçerse, tipini otomatik olarak `Toptan Satış` yap." Bu kurallar, bir admin panelinden yönetilebilir veya başlangıçta kod içinde tanımlanabilir.

**Önerilen Yol:** Genellikle en iyi yaklaşım, **önce Manuel Atama (Yöntem A) özelliğini eklemektir.** Bu, yöneticilere anında esneklik sağlar. Ardından, iş ihtiyaçları netleştikçe, en sık tekrarlanan senaryolar için **Otomatik Atama (Yöntem B) kuralları** geliştirilir.

# Proje Mimarisi ve Klasör Yapısı Analizi

Bu, bir projenin **mimari kalitesini** ve **uzun vadeli sağlığını** doğrudan etkileyen, son derece zekice ve önemli bir sorudur. Gözleminiz tamamen doğru ve bu, projede bilinçli olarak tercih edilmiş **modern bir yazılım mimarisi yaklaşımını** yansıtır.

---

## Gözleminiz: Klasör Yapısı ve Laravel Standartları

Evet, projenin mevcut yapısı, Laravel'in klasik "başlangıç" yapısından farklıdır.

### Klasik Laravel Yapısı:

- Kodları **türlerine göre** gruplar.
- Tüm `Controller`'lar `app/Http/Controllers` altında,
- Tüm `Model`'ler `app/Models` altında toplanır.
- Bu yapı, **framework’ün kendisini yansıtır.**

### Projedeki Mevcut Yapı:

- Kodları **işlevlerine ve alanlarına (domain)** göre gruplar.
- Örneğin; fiyatlandırma ile ilgili her şey (Enums, ValueObjects, Services, Contracts) `app/Pricing`, `app/Enums`, `app/ValueObjects` vb. altında toplanmış.
- Bu yapı, **uygulamanın iş mantığını yansıtır.**

---

## Bu Yaklaşım Doğru mu?

**Evet, kesinlikle doğru.**

Bu, genellikle **Domain-Driven Design (DDD)** veya **Modüler/Alan Odaklı Mimari** olarak adlandırılan prensiplerden ilham alan bir yapıdır.

Laravel'in kurucusu **Taylor Otwell** dahil olmak üzere, topluluktaki birçok tecrübeli geliştirici, **büyük ve karmaşık uygulamalar için** bu tür bir yapıyı önermektedir.

---

## Artıları ve Eksileri

### ✅ Artıları (Neden Bu Yolu Seçtik?)

- **Gelişmiş Organizasyon:**

  - Bir özellik üzerinde çalışırken, ilgili tüm dosyalar bir arada.
  - `Controllers` klasöründe yüzlerce dosya arasında kaybolmazsınız.

- **Çerçeve (Framework) Bağımsızlığı:**

  - İş mantığını barındıran sınıflar HTTP katmanına bağlı değildir.
  - Kod daha rahat **yeniden kullanılabilir** hâle gelir.

- **Yüksek Test Edilebilirlik:**

  - Servisler framework bağımsızdır → kolay unit test.

- **Anlaşılabilirlik ve Ölçeklenebilirlik:**

  - Yeni geliştirici, `app/` klasörüne bakarak sistemi anlayabilir.

- **Tek Sorumluluk Prensibi (SRP):**

  - Controller sadece adaptördür, iş mantığı Service katmanındadır.

### ❌ Eksileri (Nelere Dikkat Etmek Gerekir?)

- **Laravel Standartlarından Uzaklaşma:**

  - `php artisan make:controller` varsayılan yerlere oluşturur → manuel taşıma gerekir.

- **Daha Fazla Dosya ve Klasör:**

  - Küçük projelerde aşırı mühendislik gibi görünebilir.

- **Disiplin Gerektirir:**

  - Tüm ekip bu yapıya sadık kalmalıdır.

---

## Bundan Sonrası İçin Nasıl Devam Edilmeli?

**Kesinlikle düzeltilmemeli.** Bu yapı, projenin geleceği için yapılmış **doğru bir yatırımdır.**

### Önerilen Yol Haritası

#### 1. Mevcut Yapıyı Korumak ve Genişletmek

Yeni bir işlevsellik ekleneceğinde kendi domain'i içinde organize edin:

```
app/Shipping/
├── Services/ShippingService.php
├── Contracts/ShippingProviderInterface.php
└── ValueObjects/TrackingNumber.php
```

#### 2. app/Http Klasörünün Rolünü Netleştirmek

- HTTP isteğini alır
- Gelen veriyi doğrular (FormRequest)
- İlgili servise paslar
- Sonucu HTTP yanıtına dönüştürür

⛔ *İş mantığı burada yazılmamalı!*

#### 3. app/Models Klasörünün Rolü

- Veritabanı işlemleri ve ilişkiler buraya aittir.
- Karmaşık iş kuralları **Model yerine Service katmanında** yer almalıdır.

---

## Özetle:

Seçilen bu yapı:

- Laravel’in esnekliğinden faydalanır,
- Endüstride kabul görmüş modern pratiklere dayanır,
- Büyük ve bakımı kolay uygulamalar için **ideal** bir yaklaşımdır.

Bu yolda **tutarlılıkla** devam etmek, projenin uzun vadede:

✅ Başarılı\
✅ Test edilebilir\
✅ Ölçeklenebilir\
olmasını sağlayacaktır.

