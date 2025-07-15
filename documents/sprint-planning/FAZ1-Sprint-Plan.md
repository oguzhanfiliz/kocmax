# 🚀 FAZ 1: Temel Altyapı Sprint Planı

## 📋 Sprint Özeti
- **Sprint Adı:** FAZ 1 - Temel Altyapı
- **Süre:** 3-4 hafta (15 Temmuz - 12 Ağustos 2025)
- **Sprint Goal:** Laravel + Filament + React temel altyapısını kurarak geliştirme ortamını hazırlamak
- **Team:** Solo Developer (Oğuzhan Filiz)

## 🎯 Sprint Backlog

### Epic: [#2 FAZ 1 Temel Altyapı Sprint Planı](https://github.com/B2B-B2C/B2B-B2C-main/issues/2)

### User Stories:
| ID | Story | Story Points | Priority | Status | Assignee |
|----|-------|--------------|----------|--------|----------|
| [#3](https://github.com/B2B-B2C/B2B-B2C-main/issues/3) | Laravel 11 Backend Kurulumu | 3 | High | 📋 To Do | @oguzhanfiliz |
| [#4](https://github.com/B2B-B2C/B2B-B2C-main/issues/4) | Filament 3 Admin Panel Kurulumu | 5 | High | 📋 To Do | @oguzhanfiliz |
| [#5](https://github.com/B2B-B2C/B2B-B2C-main/issues/5) | Database Tasarımı ve Migration'lar | 8 | High | 📋 To Do | @oguzhanfiliz |
| TBD | React + Inertia.js Frontend Kurulumu | 5 | High | 📋 To Do | @oguzhanfiliz |
| TBD | Authentication Sistemi | 5 | High | 📋 To Do | @oguzhanfiliz |
| TBD | Rol & Yetki Sistemi | 3 | Medium | 📋 To Do | @oguzhanfiliz |
| TBD | Docker Development Environment | 3 | Medium | 📋 To Do | @oguzhanfiliz |
| TBD | Testing Environment Setup | 3 | Medium | 📋 To Do | @oguzhanfiliz |

**Toplam Story Points:** 35

## 📅 Haftalık Plan

### HAFTA 1 (15-21 Temmuz): Backend Foundation
- ✅ Repository organizasyonu (#2)
- 🔄 Laravel 11 kurulumu (#3) - **Şu an aktif**
- 📋 Filament 3 kurulumu (#4)
- 📋 Database tasarımı (#5)

**Target:** 16 story points

### HAFTA 2 (22-28 Temmuz): Frontend & Auth
- 📋 React + Inertia.js kurulumu
- 📋 shadcn/ui component library
- 📋 Authentication sistemi
- 📋 Rol ve yetki sistemi

**Target:** 13 story points

### HAFTA 3 (29 Temmuz - 4 Ağustos): Models & Services
- 📋 Model'lerin oluşturulması
- 📋 Relationship'lerin tanımlanması
- 📋 Repository pattern
- 📋 Service layer

**Target:** 6 story points

### HAFTA 4 (5-12 Ağustos): DevOps & Testing
- 📋 Docker production setup
- 📋 Testing environment
- 📋 CI/CD pipeline
- 📋 Code quality tools

**Target:** Kalan work items

## 📊 Sprint Metrikleri

### Daily Tracking (Günlük güncelle)
| Tarih | Tamamlanan SP | Kalan SP | Notes |
|-------|---------------|----------|-------|
| 15 Tem | 0 | 35 | Sprint başlangıcı, repo organize edildi |
| 16 Tem | - | - | - |
| 17 Tem | - | - | - |
| 18 Tem | - | - | - |

### Velocity Tracking
- **Hedef Velocity:** 35 SP / 4 hafta = ~9 SP/hafta
- **Gerçekleşen Velocity:** TBD

## 🔄 Daily Stand-up Template

### Günlük Sorular:
1. **Dün ne yaptım?**
2. **Bugün ne yapacağım?**
3. **Herhangi bir engel var mı?**

### Daily Log:
```markdown
## [Tarih] Daily Log

### Dün Tamamladıklarım:
- [ ] Task 1
- [ ] Task 2

### Bugün Planlananlar:
- [ ] Task 1
- [ ] Task 2

### Blocker'lar:
- None / [Açıklama]

### Notes:
[Günlük notlar]
```

## ✅ Definition of Done

Her user story için:
- [ ] Tüm acceptance criteria karşılandı
- [ ] Code review yapıldı (self-review)
- [ ] Unit testler yazıldı ve geçiyor
- [ ] Integration testler geçiyor
- [ ] Dokümantasyon güncellendi
- [ ] Performance kabul edilebilir seviyede
- [ ] Security check yapıldı
- [ ] Mobile responsive (frontend tasks için)

## 🔧 Sprint Tools & Workflow

### Tools:
- **Project Management:** GitHub Issues + Project Boards
- **Code:** VS Code + Laravel/React extensions
- **Database:** MySQL + phpMyAdmin (Docker)
- **Testing:** PHPUnit + Pest
- **Communication:** Self-documentation

### Git Workflow:
```bash
# Feature branch oluştur
git checkout -b feature/US-001-laravel-setup

# Geliştirme yap
git add .
git commit -m "feat: Laravel 11 kurulumu tamamlandı (#3)"

# Main branch'e merge
git checkout main
git merge feature/US-001-laravel-setup

# Issue'yu kapat
# GitHub'da issue'yu close et
```

### Branch Naming:
- `feature/US-XXX-short-description`
- `bugfix/BUG-XXX-short-description`
- `hotfix/CRITICAL-short-description`

## 📝 Sprint Retrospective (Sprint sonunda doldur)

### What went well?
- [ ] [Retrospective sonunda doldurulacak]

### What could be improved?
- [ ] [Retrospective sonunda doldurulacak]

### Action items for next sprint:
- [ ] [Retrospective sonunda doldurulacak]

---

**Last Updated:** 15 Temmuz 2025
**Next Review:** 22 Temmuz 2025 (Hafta 2 başlangıcı)
