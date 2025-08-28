🏁 PROJE GENEL BİLGİLERİ

Proje Adı: İş Sağlığı Güvenliği Kıyafetleri E-Ticaret Platformu

Süre: 10-12 Hafta

Teknoloji: Laravel 11 + Filament 3 + React + shadcn/ui + TypeScript

Hedef: B2B (Bayiler) + B2C (Müşteriler) Hibrit Platform

🎆 ANA HEDEFLER

- Çift kanal satış (Bayi + Perakende)
- Karmaşık ürün varyantları (Beden, renk, standart, özel özellikler)
- Çok seviyeli fiyatlandırma (Bayi tiers + Müşteri özel fiyatlar)
- Esnek ödeme sistemi (Iyzico/PayTR interface pattern)
- Performanslı loglama (Global handler + selective logging)
- Modern UI/UX (shadcn/ui + React components)

📅 FAZ PLANI

- FAZ 1: Temel Altyapı (3-4 Hafta) - Laravel + Filament + React + Database [FAZ 1: Temel Altyapı (3-4 Hafta)](https://www.notion.so/FAZ-1-Temel-Altyap-3-4-Hafta-22f3c2c3d54f812d84b3fcdc0f67d4d4?pvs=21)
- FAZ 2: Ürün Yönetimi (2-3 Hafta) - Varyant Builder + Kategoriler [FAZ 2: Ürün Yönetim Sistemi (2-3 Hafta)](https://www.notion.so/FAZ-2-r-n-Y-netim-Sistemi-2-3-Hafta-22f3c2c3d54f81d98d92e678c9362969?pvs=21)
- FAZ 3: Fiyatlandırma (2-3 Hafta) - Bayi/Müşteri Tiers + Kampanyalar [FAZ 3: Fiyatlandırma & Kullanıcı Sistemi (2-3 Hafta)](https://www.notion.so/FAZ-3-Fiyatland-rma-Kullan-c-Sistemi-2-3-Hafta-22f3c2c3d54f81ce9e81f6fe74ef641d?pvs=21)
- FAZ 4: Frontend (3 Hafta) - React + shadcn/ui + Responsive Design [FAZ 4: React + shadcn/ui Frontend (3 Hafta)](https://www.notion.so/FAZ-4-React-shadcn-ui-Frontend-3-Hafta-22f3c2c3d54f8139ab79fc030fb8bb57?pvs=21)
- FAZ 5: Ödeme (1-2 Hafta) - Iyzico/PayTR Interface [FAZ 5: Ödeme & Entegrasyonlar (1-2 Hafta)](https://www.notion.so/FAZ-5-deme-Entegrasyonlar-1-2-Hafta-22f3c2c3d54f81c4a96cd12827507abe?pvs=21)
- FAZ 6: Admin Panel (1-2 Hafta) - Filament + Raporlama + Analytics [FAZ 6: Admin Panel & Raporlama (1-2 Hafta)](https://www.notion.so/FAZ-6-Admin-Panel-Raporlama-1-2-Hafta-22f3c2c3d54f81db919be61323b334a3?pvs=21)

🚀 TEKNİK STACK (GÜNCELLENMİŞ)

- Backend: Laravel 11 + Filament 3
- Frontend: React 18 + Inertia.js + shadcn/ui + TypeScript
- Styling: Tailwind CSS + Responsive Design
- Database: MySQL + Redis/Memcached Cache
- Payment: Interface Pattern (Iyzico/PayTR)
- Logging: Global Handler + Selective Business Logging

🎆 GÜNCELLENMİŞ ÖZELLİKLER (Anlaşma Dosyasına Göre)

- ✅ Multi-Currency Support (TRY, USD, EUR) + Günlük kur girişi
- ✅ Adet Bazlı Fiyatlandırma (X üründe 10 tane alırsa Y fiyat)
- ✅ Hediye Ürün Kampanyaları (Şu 3 ürünü alana şu ürün hediye)
- ✅ İndirim Kuponu Sistemi (Tek/çoklu kullanım)
- ✅ Bayi Fiyat Görünürlüğü (Perakende + Bayi fiyatı aynı anda)
- ✅ Bireysel Bayi İndirim Oranları (A bayiye %5, B bayiye %7)
- ✅ Bayi Özel Ürün Fiyatları (A ürün B bayiye 100 TL)

🕰️ REVİZE EDİLMİŞ SÜRE TAHMİNİ

Orijinal: 10-12 hafta

Revize: 11-13 hafta

Eklenen süre sebepleri:

- + Multi-currency sistemi (+1 hafta)
- + Gelişmiş kampanya sistemi (+1 hafta)
- - Kargo entegrasyonu (-1 hafta)

---

## 🚀 KURULUM

### Gereksinimler
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0
- Docker & Docker Compose (isteğe bağlı)

### Hızlı Başlangıç

#### 1. Projeyi İndir
```bash
git clone <repository-url>
cd B2B-B2C-main
```

#### 2. Bağımlılıkları Yükle
```bash
composer install
npm install
```

#### 3. Ortam Dosyasını Hazırla
```bash
cp .env.example .env
php artisan key:generate
```

#### 4. Docker ile Veritabanı (Önerilen)
```bash
docker-compose up -d
```

Bu komut başlatır:
- MySQL 8.0 (Port: 3306)
- phpMyAdmin (http://localhost:8081)

#### 5. Veritabanını Hazırla
```bash
# MySQL için .env dosyasını güncelle:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=password

# Migrationları çalıştır ve test verilerini yükle
php artisan migrate:fresh --seed
```

#### 6. Frontend Derle
```bash
npm run dev
```

#### 7. Sunucuyu Başlat
```bash
php artisan serve
```

Uygulama http://localhost:8000 adresinde çalışacak.

### Admin Panel Erişimi
- URL: http://localhost:8000/admin
- Test kullanıcısı: `php artisan make:test-user` komutuyla oluşturulabilir

### Sıfırdan Klon Sonrası Hızlı Yetkilendirme Kurulumu

Proje sıfırdan klonlandıktan sonra, rol/izin (RBAC) altyapısının hazır hale gelmesi için aşağıdaki komutları sırasıyla çalıştırın:

```bash
# 1) PHP bağımlılıklarını yükle
composer install

# 2) Filament Shield ile tüm izin ve rollerin oluşturulması
php artisan shield:generate --all

# 3) Admin rolü için gerekli izinlerin tohumlanması
php artisan db:seed --class=PermissionSeederForAdminRole
```

Notlar:
- Bu adımlardan önce `.env` dosyanızı hazırlayıp veritabanı bağlantınızı sağlamış olmanız gerekir.
- Gerekirse migration ve seed işlemlerini (`php artisan migrate --seed`) önce çalıştırın.

### Önemli Komutlar

#### Geliştirme
```bash
php artisan serve                    # Geliştirme sunucusu
npm run dev                         # Frontend geliştirme modu
php artisan migrate:fresh --seed    # Veritabanını sıfırla ve test verisi yükle
php artisan test                    # Testleri çalıştır
```

#### Fiyatlandırma Sistemi
```bash
php artisan exchange:update         # Döviz kurlarını güncelle
php artisan make:test-user          # Test kullanıcıları oluştur
```

#### Kod Kalitesi
```bash
./vendor/bin/pint                   # Kod formatla (Laravel Pint)
php artisan test --coverage        # Test kapsamı raporu
```

### Docker Olmadan Kurulum

MySQL'i manuel kurulum:
```bash
# MySQL 8.0 kur ve çalıştır
# Veritabanı oluştur: laravel
# .env dosyasında DB bilgilerini güncelle

# Devam et:
php artisan migrate:fresh --seed
php artisan serve
```

### Sorun Giderme

#### Cache Temizleme
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Composer Sorunları
```bash
composer dump-autoload
composer install --no-cache
```

#### Node.js Sorunları
```bash
npm cache clean --force
rm -rf node_modules package-lock.json
npm install
```