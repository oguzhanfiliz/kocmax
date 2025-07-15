# 🚀 FAZ-1 GitHub Issues Organizasyonu

## 📋 Mevcut Görev Durumları (Notion'dan Sync)

### ✅ Issue #1: [FEATURE] Add B2B Dealer Fields to User Model - TAMAMLANDI
**Status:** CLOSED ✅  
**Assignee:** @oguzhanfiliz  
**Labels:** `enhancement`, `faz-1`, `priority-high`, `backend`, `b2b-feature`, `dealer-system`  
**Milestone:** 📋 FAZ-1-Sprint-1: B2B User System  

#### 📝 Description
User model'ine B2B dealer için gerekli alanları eklemek ve dealer management sistemi oluşturmak.

#### ✅ Completed Features:
- [x] Migration: `add_dealer_fields_to_users_table` oluşturuldu
- [x] User model'e dealer methods eklendi (`isDealer()`)
- [x] Factory ve seeder güncellendi
- [x] Filament UserResource güncellendi
- [x] Branch: `faz-1-dealer-fields` merged to `main`
- [x] Commit: `6d82460`

#### 🔧 Technical Implementation:
```php
// Migration fields added:
'dealer_code' => 'string nullable unique',
'dealer_discount_percentage' => 'decimal(5,2) nullable', 
'company_name' => 'string nullable',
'tax_number' => 'string nullable',
'is_approved_dealer' => 'boolean default false',
'dealer_application_date' => 'timestamp nullable',
'approved_at' => 'timestamp nullable',
```

#### 📋 Definition of Done - COMPLETED ✅
- [x] Migration çalışır ve deploy edilebilir
- [x] Model methods test edildi
- [x] Admin panel'den dealer oluşturulabiliyor
- [x] Code review tamamlandı

---

### 🔄 Issue #2: [FEATURE] Create Dealer Application System - DEVAM EDİYOR
**Status:** IN PROGRESS 🟡  
**Assignee:** @oguzhanfiliz  
**Labels:** `feature`, `faz-1`, `priority-high`, `backend`, `b2b-feature`, `dealer-system`  
**Milestone:** 📋 FAZ-1-Sprint-1: B2B User System  

#### 📝 Description
Bayi başvuru formu ve onay sistemi - Command pattern ile approval workflow

#### 🎯 Acceptance Criteria
- [ ] DealerApplication model oluştur
- [ ] Migration ve relationships kurulumu
- [ ] Filament DealerApplicationResource
- [ ] Approval workflow (onay/red)
- [ ] Email notifications (Observer pattern)
- [ ] File upload (ticaret sicil, vergi levhası)
- [ ] Dealer code auto-generation

#### 🔧 Technical Details
- Command pattern kullanarak approval process
- Observer pattern ile email notifications
- File storage konfigürasyonu (S3/local)
- Queue system ile async email sending

#### 📋 Definition of Done
- [ ] Başvuru formu frontend'de çalışır
- [ ] Admin onay/red yapabiliyor
- [ ] Email gönderimi çalışır ve queue'da
- [ ] Dosya upload çalışır ve güvenli
- [ ] Integration testler geçer

#### 🚀 Next Steps:
1. `php artisan make:model DealerApplication -m` komutu ile model oluştur
2. Observer pattern ile email notifications
3. File storage konfigürasyonu
4. Filament Resource oluştur
5. Approval workflow implement et

---

### 📋 Issue #3: [FEATURE] Implement Product & Category Models - GELECEK
**Status:** TODO 📋  
**Assignee:** @oguzhanfiliz  
**Labels:** `feature`, `faz-1`, `priority-medium`, `backend`, `database`  
**Milestone:** 📦 FAZ-1-Sprint-2: Product Management  

#### 📝 Description
Ürün ve kategori yönetim sistemi - İSG kategorileri için nested structure

#### 🎯 Acceptance Criteria
- [ ] Category model (nested set pattern)
- [ ] Product model + variants
- [ ] ProductVariant model (beden, renk, standart)
- [ ] ProductImage model
- [ ] Migrations ve relationships
- [ ] Filament Resources (Category, Product)

#### 🔧 Technical Details
```php
// İSG Category structure:
İş Kıyafetleri
├── Montlar  
├── Pantolonlar
└── Yelekler
İş Ayakkabıları
├── S1 Standart
├── S2 Standart  
└── S3 Standart
Koruyucu Ekipmanlar
├── Baretler
├── Eldivenler
└── Gözlükler
```

#### 📋 Definition of Done
- [ ] Nested kategoriler çalışır (parent/child)
- [ ] Ürün variants sistemi aktif
- [ ] Image upload çalışır (multiple images)
- [ ] Admin panel CRUD işlemleri functional
- [ ] Seeder'lar ile test data

#### 🏷️ Dependencies: 
- Depends on: Issue #2 (Dealer Application System)

---

### 💱 Issue #4: [FEATURE] Currency & Exchange Rate Management - GELECEK
**Status:** TODO 📋  
**Assignee:** @oguzhanfiliz  
**Labels:** `feature`, `faz-1`, `priority-medium`, `backend`, `b2b-feature`  
**Milestone:** 📦 FAZ-1-Sprint-2: Product Management  

#### 📝 Description
Multi-currency support (TRY, USD, EUR) ve günlük kur sistemi

#### 🎯 Acceptance Criteria
- [ ] Currency model
- [ ] ExchangeRate model  
- [ ] Daily rate input system (admin panel)
- [ ] Rate conversion service
- [ ] Filament Resources (Currency, ExchangeRate)
- [ ] Rate history tracking

#### 🔧 Technical Details
- Strategy pattern kullanarak currency conversion
- Manual rate input sistemi (admin panelden)
- Rate history için separate table
- Conversion calculations için service layer

#### 📋 Definition of Done
- [ ] Currency CRUD çalışır
- [ ] Exchange rate sistemi aktif
- [ ] Conversion hesaplamaları doğru
- [ ] Admin panel'den rate girişi functional
- [ ] Rate history takibi çalışır

#### 🏷️ Dependencies: 
- Depends on: Issue #3 (Product & Category Models)

---

### 🎛️ Issue #5: [FEATURE] Admin Panel Resources - GELECEK
**Status:** TODO 📋  
**Assignee:** @oguzhanfiliz  
**Labels:** `feature`, `faz-1`, `priority-medium`, `frontend`, `backend`  
**Milestone:** 🎛️ FAZ-1-Sprint-3: Admin Panel  

#### 📝 Description
Tüm CRUD işlemleri, resource'lar, dashboard widgets

#### 🎯 Acceptance Criteria
- [ ] Tüm Filament Resources gözden geçirme
- [ ] İlişki yönetimi (RelationshipManager)
- [ ] Dashboard widgets
- [ ] Yetkilendirme (spatie/laravel-permission)
- [ ] User experience optimizasyonu

#### 🔧 Technical Details
- ProductResource içinden varyantları
- UserResource içinden bayilik başvurusunu
- Dashboard'a yeni başvuruları gösteren widgets
- Rol bazlı yetkilendirme

#### 📋 Definition of Done
- [ ] Tüm CRUD işlemleri functional
- [ ] İlişkiler RelationshipManager ile kuruldu
- [ ] Dashboard widgets çalışır
- [ ] Yetkilendirme testleri geçer
- [ ] UX optimizasyonu tamamlandı

#### 🏷️ Dependencies: 
- Depends on: Issue #4 (Currency & Exchange Rate Management)

---

## 📊 Sprint Progress Summary

### FAZ-1 Sprint 1: B2B User System (Bitiş: 29.07.2025)
- ✅ **Issue #1:** TAMAMLANDI (100%)
- 🔄 **Issue #2:** DEVAM EDİYOR (0%)

### FAZ-1 Sprint 2: Product Management (Bitiş: 05.08.2025)
- 📋 **Issue #3:** PENDING (0%)
- 💱 **Issue #4:** PENDING (0%)

### FAZ-1 Sprint 3: Admin Panel (Bitiş: 12.08.2025)
- 🎛️ **Issue #5:** PENDING (0%)

## 🎯 Next Actions

1. **Immediate:** Issue #2'ye başla (Dealer Application System)
2. **This Week:** DealerApplication model oluştur
3. **Next Week:** Product & Category Models (Issue #3)

---

**Last Updated:** 15.07.2025  
**Sync with Notion:** ✅ COMPLETED  
**GitHub Issues Created:** Ready for manual creation  
**Total Issues:** 5 (1 Completed, 1 In Progress, 3 Pending) 