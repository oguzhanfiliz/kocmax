# Filament Admin Paneli Kapsamlı Kullanım Kılavuzu

Bu kılavuz, Filament admin panelinin tüm özelliklerini, özellikle de ürün yönetimi mekanizmalarını, hiç teknik bilgisi olmayan bir kişinin bile anlayabileceği şekilde açıklamaktadır.

## Temel Kavramlar

Başlamadan önce, bazı temel kavramları anlamak önemlidir:

- **Ürün (Product):** Sattığınız ana ürün. Örneğin, "İş Ayakkabısı".
- **Nitelik (Attribute):** Bir ürünün sahip olabileceği özellikler. Örneğin, "Renk" veya "Beden".
- **Değer (Value):** Bir niteliğin alabileceği değer. Örneğin, "Renk" niteliği için "Siyah", "Beden" niteliği için "42".
- **Varyant (Variant):** Bir ürünün farklı nitelik ve değer kombinasyonlarından oluşan alt versiyonu. Örneğin, "Siyah Renk, 42 Beden İş Ayakkabısı".
- **SKU (Stock Keeping Unit):** Her bir varyant için benzersiz olan ve stok takibini kolaylaştıran kod. Örneğin, "AYK-SYH-42".

## Ürün Yönetimi

### 1. Adım: Özellik Türleri Oluşturma

Ürünlerinize özellik (renk, beden, malzeme vb.) ekleyebilmek için önce bu özelliklerin türlerini tanımlamanız gerekir.

**Amaç:** Ürünlerinizi hangi özelliklere göre çeşitlendireceğinizi sisteme öğretmek.

**Nasıl Yapılır:**
1. Sol menüden **Ürün Yönetimi** altında **Özellik Türleri**'ne gidin.
2. **Yeni Özellik Türü**'ne tıklayın.
3. **Tip Adı** alanına özellik türünü girin (Örnek: "select").
4. **Görünen Ad** alanına kullanıcının göreceği ismi girin (Örnek: "Seçim Listesi").
5. **Filament Component** alanına "Select" yazın.
6. **Kaydet**'e tıklayın.
7. Aynı işlemi "text", "number", "checkbox" gibi diğer türler için de tekrarlayın.

### 2. Adım: Ürün Özellikleri ve Değerleri Oluşturma

Özellik türlerini oluşturduktan sonra, kategoriler için özel özellikler tanımlamanız gerekir.

**Amaç:** Her kategori için hangi özelliklerin kullanılacağını ve bu özelliklerin seçeneklerini belirlemek.

**Nasıl Yapılır:**
1. Sol menüden **Ürün Yönetimi** altında **Ürün Özellikleri**'ne gidin.
2. **Yeni Ürün Özelliği**'ne tıklayın.
3. **Ad** alanına özelliğin ismini girin (Örnek: "Beden").
4. **Özellik Türü** olarak uygun türü seçin (Örnek: "Seçim Listesi").
5. **Kategoriler** alanından bu özelliğin hangi kategorilerde kullanılacağını seçin.
6. **Seçenekler** alanına değerleri girin (Örnek: 36, 37, 38, 39, 40, 41, 42, 43).
7. **Varyant İçin Kullan** seçeneğini işaretleyin (eğer bu özellik ile varyant oluşturmak istiyorsanız).
8. **Kaydet**'e tıklayın.

### 3. Adım: Stok Kodu Yapılandırması (Otomatik Kod Üretimi)

Her bir ürün varyantı için otomatik olarak benzersiz bir kod (SKU) üretilmesini sağlayabilirsiniz.

**Amaç:** Stok takibini otomatikleştirmek ve her bir ürün çeşidini kolayca ayırt etmek.

**Nasıl Yapılır:**
1. Sol menüden **Ürün Yönetimi** altında **Stok Kodu Yapılandırması**'na gidin.
2. **Yeni Stok Kodu Yapılandırması**'na tıklayın.
3. **Yapılandırma Adı** girin (Örnek: "İş Ayakkabısı SKU").
4. **SKU Deseni** girin (Örnek: "{CATEGORY}-{PRODUCT}-{NUMBER}").
5. **Ayırıcı** belirleyin (Örnek: "-").
6. **Sayı Uzunluğu** girin (Örnek: 3).
7. **Varsayılan** olarak işaretleyin (eğer ana yapılandırma olacaksa).
8. **Kaydet**'e tıklayın.

### 4. Adım: Kategori Oluşturma

Ürünlerinizi gruplamak için kategoriler oluşturun.

**Amaç:** Ürünleri düzenli bir şekilde sınıflandırmak ve kullanıcıların aradıklarını kolayca bulmasını sağlamak.

**Nasıl Yapılır:**
1. Sol menüden **Ürün Yönetimi** altında **Kategoriler**'e gidin.
1. Sol menüden **Kategoriler**'e gidin.
2. **Kategori Oluştur**'a tıklayın.
3. Kategori adını girin (Örnek: "İş Ayakkabıları").
4. Kaydedin.

### 5. Adım: Ürün Oluşturma ve Varyantları Ekleme

Tüm altyapıyı hazırladığınıza göre ana ürünü ve varyantlarını oluşturabilirsiniz.

**Amaç:** Satılacak ana ürünü ve onun tüm çeşitlerini (renk, beden vb.) sisteme tanımlamak.

**Nasıl Yapılır:**

1. Sol menüden **Ürün Yönetimi** altında **Ürünler**'e gidin.
2. **Yeni Ürün**'e tıklayın.
3. **Ürün Adı** girin (Örnek: "Profesyonel İş Ayakkabısı").
4. **URL Slug** otomatik olarak oluşacaktır.
5. **Kategoriler** alanında ürünün ait olduğu kategoriyi seçin ("İş Ayakkabıları").
6. **Açıklama** alanına ürün detaylarını yazın.
7. **Fiyat** ve **Stok Miktarı** bilgilerini girin.
8. **Kaydet**'e tıklayın.

### 6. Adım: Varyantları Oluşturma ve Yönetme

Ürünü kaydettikten sonra ürün düzenleme sayfasına giderek varyantları oluşturabilirsiniz.

**Amaç:** Ürünün farklı özellik kombinasyonlarını (varyantlarını) oluşturmak, fiyatlarını ve stoklarını belirlemek.

**Nasıl Yapılır:**

1. Ürün listesinden düzenlemek istediğiniz ürünün yanındaki **Düzenle** butonuna tıklayın.
2. **Varyant Yönetimi** bölümünde **Varyant Oluştur** butonuna tıklayın.
3. Açılan formda, bu ürün kategorisine ait özellikleri göreceksiniz.
4. Her özellik için istediğiniz değerleri girin (Örnek: Beden için "41, 42, 43").
5. **Kaydet** butonuna tıklayın.
6. Sistem seçtiğiniz değerlerin tüm kombinasyonlarını otomatik varyant olarak oluşturacaktır.
7. **Varyantlar** sekmesinden her varyantın fiyat ve stok bilgilerini ayrı ayrı düzenleyebilirsiniz.

Bu adımları takip ederek, en karmaşık ürünlerinizi bile kolayca yönetebilir, stok takibini otomatikleştirebilir ve müşterilerinize aradıkları tüm seçenekleri sunabilirsiniz.

## Ürün Özellik Sistemi - Detaylı Açıklama

Sistemimizde ürünlere çeşitli özellikler ekleyebilmeniz için gelişmiş bir özellik sistemi bulunmaktadır. Bu sistem, farklı veri türlerini destekler ve her bir özellik türünün kendine özgü kullanım alanları vardır.

### Özellik Türleri (Attribute Types) 

Sistem 7 farklı özellik türünü desteklemektedir:

#### 1. **TEXT (Metin Girişi)**
**Ne İşe Yarar:** Serbest metin yazmanız gereken durumlarda kullanılır.
**Örnekler:**
- Model Numarası: "AYK-2024-PRO"
- Sertifika Numarası: "CE-EN20345-2024"
- Ürün Açıklaması: "Profesyonel çelik burunlu iş ayakkabısı"
- Marka Kodu: "SAFETY-PRO-001"

**Nasıl Kullanılır:** Kullanıcı klavye ile istediği metni yazar.

#### 2. **SELECT (Açılır Liste - Tekli Seçim)**
**Ne İşe Yarar:** Önceden belirlenmiş seçenekler arasından sadece bir tanesini seçmek için kullanılır.
**Örnekler:**
- **Marka:** Nike, Adidas, Safety Pro, WorkMax (Sadece bir marka seçilebilir)
- **Renk:** Siyah, Kahverengi, Gri (Sadece bir renk seçilebilir)
- **Güvenlik Sınıfı:** S1, S1P, S2, S3 (Sadece bir sınıf seçilebilir)
- **Malzeme:** Deri, Süet, Sentetik (Sadece bir malzeme seçilebilir)

**Nasıl Kullanılır:** Kullanıcı açılır listeden bir seçenek tıklar.

#### 3. **CHECKBOX (Çoklu Seçim Kutucukları)**
**Ne İşe Yarar:** Birden fazla özelliği aynı anda seçmek için kullanılır.
**Örnekler:**
- **Özellikler:** 
  ☑ Su Geçirmez
  ☑ Nefes Alabilir
  ☑ Antistatik
  ☑ Asit Dayanımlı
  (Hepsini veya hiçbirini seçebilirsiniz)
- **Sertifikalar:**
  ☑ CE Belgeli
  ☑ ISO 20345
  ☑ ASTM F2413
  ☑ EN ISO 20347

**Nasıl Kullanılır:** Her kutucuğa ayrı ayrı tıklayarak seçim yapılır.

#### 4. **RADIO (Tekli Seçim Butonları)**
**Ne İşe Yarar:** Seçenekler arasından sadece bir tanesini seçmek için kullanılır. SELECT'e benzer ama seçeneklerin hepsi görünür durumda olur.
**Örnekler:**
- **Beden:**
  ○ 36  ○ 37  ○ 38  ● 39  ○ 40  ○ 41  ○ 42  ○ 43
  (Sadece bir beden seçilebilir)
- **Taban Tipi:**
  ○ PU  ● Kauçuk  ○ EVA  ○ Nitrile
  (Sadece bir taban tipi seçilebilir)

**Nasıl Kullanılır:** Bir seçeneğe tıklandığında diğerleri otomatik olarak iptal olur.

#### 5. **COLOR (Renk Seçici)**
**Ne İşe Yarar:** Görsel olarak renk seçmek için kullanılır. Renk paleti açılır.
**Örnekler:**
- **Ana Renk:** Kullanıcı renk paletinden istediği rengi tıklar
- **Aksesuar Rengi:** Bağcık, dikişler için renk seçimi
- **Logo Rengi:** Ürün üzerindeki logo rengi

**Nasıl Kullanılır:** Renk kutusuna tıklandığında renk paleti açılır, istenen renk seçilir.

#### 6. **NUMBER (Sayı Girişi)**
**Ne İşe Yarar:** Sadece sayısal değerlerin girilmesi gereken durumlarda kullanılır.
**Örnekler:**
- **Ağırlık:** 850 (gram cinsinden)
- **Yükseklik:** 15 (cm cinsinden)
- **Maksimum Sıcaklık:** 150 (derece cinsinden)
- **Kalınlık:** 12 (mm cinsinden)
- **Koruma Seviyesi:** 4 (1-5 arası)

**Nasıl Kullanılır:** Sadece rakam girişi yapılabilir, harf yazılamaz.

#### 7. **DATE (Tarih Seçici)**
**Ne İşe Yarar:** Tarih bilgisi girmek için kullanılır.
**Örnekler:**
- **Üretim Tarihi:** 15.07.2024
- **Son Kullanma Tarihi:** 15.07.2027
- **Sertifika Geçerlilik Tarihi:** 31.12.2025
- **Garanti Bitiş Tarihi:** 15.07.2025

**Nasıl Kullanılır:** Takvim açılır, istenen tarih tıklanır.

### Gerçek Hayat Örnekleri

#### İş Ayakkabısı İçin Özellik Seti:
1. **Model Numarası** (TEXT): "SAFETY-PRO-2024-XL"
2. **Marka** (SELECT): Safety Pro, WorkMax, DeWalt
3. **Renk** (COLOR): Siyah, kahverengi, gri renk paleti
4. **Beden** (RADIO): 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46
5. **Özellikler** (CHECKBOX): Su geçirmez, Nefes alabilir, Antistatik
6. **Ağırlık** (NUMBER): 850 gram
7. **Üretim Tarihi** (DATE): 15.07.2024

#### İş Eldiveni İçin Özellik Seti:
1. **Ürün Kodu** (TEXT): "ELD-NTR-001"
2. **Malzeme** (SELECT): Deri, Nitrile, Latex, PVC
3. **Beden** (RADIO): XS, S, M, L, XL, XXL
4. **Kalınlık** (NUMBER): 0.15 mm
5. **Koruma Özellikleri** (CHECKBOX): Kimyasal dayanım, Kesilme direnci, Aşınma direnci
6. **Sertifika Tarihi** (DATE): 01.01.2024

### Sistemin Avantajları

1. **Esneklik:** Her ürün türü için farklı özellik kombinasyonları oluşturabilirsiniz.
2. **Tutarlılık:** Aynı türdeki ürünler için standart özellik setleri kullanabilirsiniz.
3. **Filtreleme:** Müşteriler özelliklere göre ürünleri filtreleyebilir.
4. **Karşılaştırma:** Ürünler özellikleri üzerinden karşılaştırılabilir.
5. **Stok Yönetimi:** Özellik kombinasyonları ile stok takibi yapılabilir.

Bu sistem sayesinde, en karmaşık ürün kataloglarını bile düzenli ve kullanıcı dostu bir şekilde yönetebilirsiniz.
