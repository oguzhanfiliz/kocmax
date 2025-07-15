# FAZ 1: Core Models & Infrastructure

**Amaç:** B2B e-ticaret platformunun temel veri modellerini, veritabanı yapısını ve bu yapıları yönetecek temel admin panel arayüzlerini oluşturmak.

---

## Sprint 1: B2B User System (Bitiş Tarihi: 29.07.2025)

### Görev 1: [FEATURE] Add B2B Dealer Fields to User Model (Issue #1)

**Senaryo:** Mevcut `User` modelini, bir kullanıcıyı "Bayi" olarak tanımlayabilecek ve bayi özel bilgilerini (şirket adı, vergi no, iskonto oranı vb.) tutabilecek şekilde genişletmek.

**Teknik Adımlar:**
1.  **Migration:** `php artisan make:migration add_dealer_fields_to_users_table --table=users` komutu ile migration oluştur.
    -   Eklenecek Alanlar: `dealer_code` (string, nullable, unique), `company_name` (string, nullable), `tax_number` (string, nullable), `dealer_discount_percentage` (decimal), `is_approved_dealer` (boolean, default: false), `dealer_application_date` (timestamp, nullable), `approved_at` (timestamp, nullable).
2.  **Model Güncellemesi (`app/Models/User.php`):**
    -   Yeni alanları `$fillable` dizisine ekle.
    -   Cast'leri tanımla: `is_approved_dealer` -> `boolean`, `dealer_discount_percentage` -> `float`.
    -   Yardımcı metodlar ekle: `public function isDealer(): bool { return $this->is_approved_dealer; }`
3.  **Filament Resource Güncellemesi (`app/Filament/Resources/UserResource.php`):**
    -   Form'a yeni alanları ekle (`TextInput` for `company_name`, `tax_number`; `Toggle` for `is_approved_dealer`).
    -   Alanları, sadece "Admin" rolüne sahip kullanıcıların görebileceği/düzenleyebileceği şekilde ayarla.
    -   Tabloya (Table) önemli bayi alanlarını (örn: `company_name`, `is_approved_dealer`) ekle.
4.  **Testler:** Bu alanların doğru şekilde kaydedildiğini ve `isDealer()` metodunun çalıştığını doğrulayan birim (Unit) ve özellik (Feature) testleri yaz.

### Görev 2: [FEATURE] Create Dealer Application System (Issue #2)

**Senaryo:** Potansiyel bayilerin sistem üzerinden başvuru yapabilmesini ve yöneticilerin bu başvuruları inceleyip onaylayabilmesini sağlamak.

**Teknik Adımlar:**
1.  **Model ve Migration:** `php artisan make:model DealerApplication -m` komutuyla model ve migration oluştur.
    -   Alanlar: `user_id` (ilişki), `status` (enum: pending, approved, rejected), `notes` (text), `documents` (json).
2.  **Dosya Yükleme Ayarları:** `config/filesystems.php` dosyasında `public` diski ayarla. Bayi belgeleri (vergi levhası vb.) için güvenli bir depolama stratejisi belirle.
3.  **Filament Resource:** `php artisan make:filament-resource DealerApplication` ile başvuru yönetim paneli oluştur.
    -   Listede başvuruları durumuyla göster.
    -   Detay sayfasında başvuranın bilgilerini ve yüklediği belgeleri göster.
4.  **Onay/Red Aksiyonları (Command & Observer Patterns):**
    -   `DealerApplicationResource` içine "Onayla" ve "Reddet" `Action`'ları ekle.
    -   **Onay Aksiyonu:**
        -   `DealerApplication` `status`'unu 'approved' yapar.
        -   İlişkili `User` modelinin `is_approved_dealer` alanını `true` yapar, `approved_at` tarihini doldurur ve benzersiz bir `dealer_code` atar.
        -   **Observer Tetikleme:** Başvurunun onaylandığına dair kullanıcıya e-posta gönderen bir `Notification` tetikler.
    -   **Reddet Aksiyonu:**
        -   `DealerApplication` `status`'unu 'rejected' yapar.
        -   **Observer Tetikleme:** Reddedilme nedenini içeren bir e-postayı kullanıcıya gönderir.
5.  **Testler:** Başvuru oluşturma, onaylama ve reddetme akışını uçtan uca test eden Feature testleri yaz. E-posta bildirimlerinin gönderildiğini doğrula.

---

## Sprint 2: Product Management (Bitiş Tarihi: 05.08.2025)

### Görev 3: [FEATURE] Implement Product & Category Models (Issue #3)

**Senaryo:** Ürünleri ve bu ürünleri organize edecek, "İş Kıyafetleri > Montlar" gibi iç içe (nested) kategori yapısını yönetecek altyapıyı kurmak.

**Teknik Adımlar:**
1.  **Nested Set Paketi:** `kalnoy/nestedset` paketini kur: `composer require kalnoy/nestedset`.
2.  **Modeller ve Migration'lar:**
    -   `Category`: `name`, `slug` ve nested set için gerekli `_lft`, `_rgt`, `parent_id` alanlarını ekle. Modelde `NodeTrait`'i kullan.
    -   `Product`: `category_id` (ilişki), `name`, `slug`, `description`, `base_price` vb. alanları ekle.
    -   `ProductVariant`: `product_id` (ilişki), `attributes` (json, örn: `{"color": "red", "size": "XL"}`), `sku`, `price`, `stock` alanlarını ekle.
3.  **Filament Resource'ları:**
    -   `CategoryResource`: Kategorileri hiyerarşik bir yapıda listelemek için bir eklenti (`filament-treenode`) kullan.
    -   `ProductResource`: Ürün formunda `RelationshipManager` veya `Repeater` kullanarak varyantlarını ve görsellerini yönet.
4.  **Seeder'lar:** Test verisi oluşturmak için `CategorySeeder` ve `ProductSeeder` dosyalarını oluştur.

### Görev 4: [FEATURE] Currency & Exchange Rate Management (Issue #4)

**Senaryo:** Sistemde birden fazla para birimini (TRY, USD, EUR) desteklemek ve yöneticinin döviz kurlarını manuel olarak yönetebilmesini sağlamak.

**Teknik Adımlar:**
1.  **Modeller ve Migration'lar:**
    -   `Currency`: `name` (Türk Lirası), `code` (TRY), `symbol` (₺).
    -   `ExchangeRate`: `from_currency_id`, `to_currency_id`, `rate` (decimal), `date` (date).
2.  **Filament Resource'ları:** `CurrencyResource` ve `ExchangeRateResource` oluştur. Yöneticinin kur girişi yapmasını ve geçmiş kurları görmesini sağla.
3.  **Dönüşüm Servisi:**
    -   `app/Services/CurrencyConverter.php` adında bir servis sınıfı oluştur.
    -   `public function convert(float $amount, string $from, string $to): float` metodu içersin. Bu metod, `ExchangeRate` tablosundan en güncel kuru bularak dönüşümü yapsın.
4.  **Helper Fonksiyonu:** `app/Helpers/currency.php` içinde `convert_price(...)` gibi global bir yardımcı fonksiyon oluşturarak servisin kullanımını kolaylaştır.

---

## Sprint 3: Admin Panel (Bitiş Tarihi: 12.08.2025)

**Senaryo:** Faz 1'de oluşturulan tüm modeller için Filament'te tam fonksiyonel CRUD arayüzleri oluşturmak ve paneli kullanıma hazır hale getirmek.

**Teknik Adımlar:**
1.  **Gözden Geçirme:** Önceki adımlarda oluşturulan tüm Filament Resource'larını (User, DealerApplication, Product, Category, Currency, ExchangeRate) gözden geçir.
2.  **İlişki Yönetimi:** `ProductResource` içinden varyantları, `UserResource` içinden bayilik başvurusunu yönetmek gibi ilişkileri `RelationshipManager`'lar ile kur.
3.  **Dashboard:** Filament dashboard'una yeni başvuruları, son eklenen ürünleri vb. gösteren widget'lar ekle.
4.  **Yetkilendirme:** Tüm resource ve action'ların `spatie/laravel-permission` ile tanımlanan rollere göre doğru şekilde yetkilendirildiğinden emin ol.