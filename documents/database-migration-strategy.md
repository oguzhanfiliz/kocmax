# Database Migration Strategy

## Amaç
Bu doküman, veritabanı şema değişikliklerinin güvenli, tekrarlanabilir ve CI (SQLite) ile prod (MySQL 8) ortamlarında sorunsuz çalışacak şekilde tasarlanması için standartları tanımlar.

## İsimlendirme ve Standartlar
- Tablolar: snake_case, çoğul (ör. `products`, `product_variants`).
- Yabancı anahtarlar: `{table}_id` (ör. `product_id`).
- Para alanları: `decimal(10,2)` (tüm para birimi alanları için zorunlu).
- Pivot tablolar: alfabetik sıralama (ör. `category_product`).
- Önemli varlıklar: soft deletes (ör. `deleted_at`).
- Indexler: FK alanları ve sık sorgulanan sütunlar için eklenir.

## SQLite ↔ MySQL Uyumluluk Kuralları (CI Uyumlu)
SQLite, ALTER TABLE desteğinde sınırlıdır. CI GitHub Actions’ta SQLite kullanıldığı için tüm migrasyonlar aşağıdaki kurallara uymalıdır:

- Bir tabloda birden fazla sütun silerken tek bir `dropColumn([..])` çağrısı kullanın.
  ```php
  Schema::table('products', function (Blueprint $table) {
      $toDrop = [];
      foreach (['col_a', 'col_b', 'col_c'] as $c) {
          if (Schema::hasColumn('products', $c)) { $toDrop[] = $c; }
      }
      if ($toDrop) { $table->dropColumn($toDrop); }
  });
  ```
- Aynı migrasyon içinde çoklu `renameColumn` veya ardışık `dropColumn` çağrılarından kaçının.
- Gerekirse değişiklikleri ayrı `Schema::table` bloklarına bölün; her blok tek bir mantıksal değişiklik yapsın.
- Enum alanları: MySQL ENUM yerine `string` + uygulama düzeyi `enum` (PHP/ValueObject) tercih edin; SQLite’ta kontrol kısıtı olmadığından uyum kolaylaşır.
- JSON alanları: SQLite’ta JSON gerçek tip değildir; `json` tanımı dahi altta `TEXT` olabilir. Uygulama doğrulaması yapın.

## Sürümleme ve Sıralama
- Tarihli adlandırma (Laravel varsayılanı) sürüm sırasını belirler.
- Kırıcı değişiklikleri parçalayın: ekle → doldur (backfill) → tüketen kodu değiştir → eskiyi kaldır.
- Veri taşıma (backfill) gerektiren durumlarda ayrı “veri migrasyonu” sınıfı veya `artisan` komutu kullanın; tabloyu kilitleyen büyük `UPDATE`’lerden kaçının.

## Para ve Kur Alanları
- Para: `decimal(10,2)`; kur: `decimal(10,4)` (örn. `exchange_rate`).
- Para birimi: `currency_id` (FK) veya `currency_code` (3 harf) için index ekleyin.

## Yabancı Anahtarlar ve Indexleme
- FK sütunları `unsignedBigInteger` + `constrained()` veya açık `foreign()` ile tanımlanır.
- Sorgu desenlerine göre birleşik indexler ekleyin (örn. `is_active, created_at`).
- FK alanlarına tekil index koyun (JOIN performansı için).

## CI / GitHub Actions Uyum
- CI SQLite yapılandırması:
  ```bash
  php artisan migrate:fresh --seed --force
  php artisan test
  ```
- Ortam değişkenleri (Actions’da): `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`, `DB_FOREIGN_KEYS=true`.
- Tests/TestCase hizası: Testlerin SQLite bellek/dosya moduyla çakışmaması için `database.default` ayarları gözden geçirilmelidir.

## Seed Stratejisi
- Başlangıç verileri: Para birimleri (`CurrencySeeder`), roller/izinler (Spatie + Shield), kategoriler/örnek ürünler.
- Admin rolü tam yetki:
  ```bash
  php artisan db:seed --class=PermissionSeederForAdminRole
  ```
- Seed’ler idempotent olmalı (`firstOrCreate`, `updateOrCreate`).

## Geri Alma (Rollback) ve Sıfır Kesinti (Zero-Downtime) İlkeleri
- Additive-first: önce yeni sütun/tabloları ekleyin; uygulama yeni alanı okumaya geçtikten sonra eskiyi kaldırın.
- Büyük tablo güncellemelerinde parçalı işleyin (chunk), işlem (transaction) sınırlarını küçük tutun.
- Eski sütunları kaldırmayı ayrı, geç bir migrasyona bırakın (özellikle prod’da).

## Büyük Değişikliklerde Örnek Plan
1) `new_col` ekle (nullable + varsayılan).
2) Backfill job ile eski veriden `new_col` doldur.
3) Uygulama kodunu `new_col` kullanacak şekilde değiştir.
4) Eski sütunu yalnızca tüm örnekler güncellendikten sonra kaldır (ayrı migrasyon).

## Örnek Desenler
- Birden çok sütun silme (SQLite uyumlu):
  ```php
  Schema::table('product_variants', function (Blueprint $table) {
      $drop = array_values(array_filter([
          Schema::hasColumn('product_variants', 'legacy_attr') ? 'legacy_attr' : null,
          Schema::hasColumn('product_variants', 'legacy_code') ? 'legacy_code' : null,
      ]));
      if ($drop) { $table->dropColumn($drop); }
  });
  ```
- Sütun yeniden adlandırma:
  ```php
  Schema::table('products', function (Blueprint $table) {
      if (Schema::hasColumn('products', 'price')) {
          $table->renameColumn('price', 'base_price');
      }
  });
  ```

## Kontrol Listesi (PR Öncesi)
- [ ] SQLite ve MySQL’de çalışır (CI yeşil).
- [ ] Para alanları `decimal(10,2)`.
- [ ] FK’lar ve indexler eklendi.
- [ ] Silme/yeniden adlandırma işlemleri SQLite’a uygun tek çağrı/ayrı bloklarla yazıldı.
- [ ] Seed’ler idempotent.
- [ ] Geri dönüş planı (rollback) tanımlı.
- [ ] Büyük veri değişiklikleri için backfill/queue planı hazır.

## İlgili Dosyalar
- `.github/workflows/laravel.yml` (CI ayarları)
- `database/migrations/*` (tüm migrasyonlar)
- `database/seeders/*` (seed stratejisi)
- `memorybank/database-schema.md` (şema referansı)
