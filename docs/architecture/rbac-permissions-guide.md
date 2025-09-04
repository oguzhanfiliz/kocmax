# RBAC & İzinler Kılavuzu (Spatie + Filament Shield)

## Amaç
Projedeki rol ve izin yönetimini standartlaştırmak, Spatie Laravel Permission ile Filament Shield akışını uyumlu şekilde işletmek ve yeni kaynak ekleme sürecini otomatikleştirmek.

## Bileşenler
- Spatie Laravel Permission (roller/izinler)
- Filament Shield (Filament kaynakları için izin üretimi ve panel yetkilendirmesi)
- Laravel Policies (kaynak bazlı yetkilendirme)
- Permission seed dosyaları (örn. `database/seeders/PermissionSeederForAdminRole.php`)

## Rol & İzin Taksonomisi
- Varsayılan roller: `admin`, `dealer` (B2B), `customer` (B2C)
- İzin adlandırma: `kaynak.eylem` biçimi
  - Örnekler: `product.view`, `product.create`, `product.update`, `product.delete`, `order.update-status`
- Filament kaynakları için Shield otomatik olarak aşağıdaki izinleri önerir:
  - `viewAny`, `view`, `create`, `update`, `delete`, `deleteAny`, `restore`, `forceDelete`

## Policy Standartları
- Her önemli model için Policy sınıfı tanımlanır ve `AuthServiceProvider` içinde bağlanır
- İzin kontrolü policy içinde yapılır:
```php
public function viewAny(User $user): bool { return $user->can('product.view'); }
public function create(User $user): bool { return $user->can('product.create'); }
```
- API tarafında kontrol:
  - Resource Controller aksiyonlarında `authorize()` / `Gate::authorize()`
  - Filament zaten policy + Shield izinlerine göre UI eylemlerini kısıtlar

## Filament Shield Akışı
- Kaynaklar oluşturulduktan sonra izinleri senkronize etmek için:
```bash
php artisan shield:generate       # Kaynaklara göre izinleri üretir
php artisan shield:install        # İlk kurulum (gerekirse)
php artisan shield:sync           # İzin/role eşleşmelerini günceller
```
- Shield, Filament menü görünürlüğü ve eylem butonlarını izinlere göre kontrol eder

## İzin Seed Süreci
- `PermissionSeederForAdminRole.php` ile admin rolüne tam yetki verilir ve izinler oluşturulur
- Önerilen çalışma sırası:
```bash
php artisan migrate --force
php artisan db:seed --class=PermissionSeederForAdminRole
# Filament kaynak/izin değişiminden sonra
php artisan shield:generate && php artisan shield:sync
```
- Admin kullanıcısının role atanması seed içinde veya ayrı seeder/komut ile yapılır (örn. `CreateTestUser`)

## Yeni Kaynak Ekleme Checklist
1) Model + Policy
   - Model oluşturun, Policy üretin (`php artisan make:policy ModelPolicy --model=Model`)
   - Policy’de `viewAny/view/create/update/delete` izin adlarıyla `can()` kontrollerini ekleyin
2) Filament Resource
   - Resource/RelationManager/Widget tanımlayın
   - Sorgularda eager loading kullanın (performans)
3) İzin Üretimi ve Atama
   - `php artisan shield:generate && php artisan shield:sync`
   - `PermissionSeederForAdminRole` içine yeni izinleri ekleyin veya dinamik hale getirin
4) Testler
   - Policy unit testleri (rolü olan/olmayan kullanıcı senaryoları)
   - Filament eylem görünürlüğü testleri (opsiyonel)

## API Yetkilendirme Desenleri
- Route düzeyinde: `auth:sanctum` ile kimlik doğrulama
- İşlem düzeyinde: Policy/permission kontrolü
```php
public function store(Request $request) {
    $this->authorize('create', Product::class); // veya Gate::authorize('product.create')
    // ...
}
```
- B2B/B2C farklılaştırma: Policy içinde kullanıcı tipine (dealer/customer) göre koşul eklenebilir

## En İyi Uygulamalar
- İzin adlarını küçük harf ve nokta ayırıcı ile tutarlı verin (örn. `order.manage-payments`)
- Geniş yetkiler için grup izinleri tanımlayın (örn. `product.manage` → create/update/delete kapsar)
- Seed’lerde idempotent yaklaşım: `firstOrCreate`/`sync` kullanın
- Filament dışında API tarafı için de aynı izin adlarını kullanın (tek kaynak gerçek)

## Test Stratejisi (Özet)
- Policy:
  - `admin` → tüm izinler true
  - `dealer` → kendi kayıtlarıyla sınırlı eylemler
  - `customer` → yalnızca okuma vs.
- Shield/Filament:
  - İzin yokken buton/menü görünmemeli
  - İzin varken görünmeli ve işlem başarılmalı

## Sorun Giderme
- “İzin var ama eylem görünmüyor” → `shield:sync` ve policy eşleşmesini doğrulayın
- “Admin yetkileri eksik” → Admin rolüne toplu izin atamasını seed ile güncelleyin
- “API 403 dönüyor” → Token sahibi kullanıcının rol/izin setini ve policy mantığını kontrol edin

## İlgili Dosyalar
- `database/seeders/PermissionSeederForAdminRole.php`
- `app/Policies/*`
- `app/Providers/AuthServiceProvider.php`
- `app/Filament/Resources/*` (Shield ile ilişkili izinler)
