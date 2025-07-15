# 📋 B2B-B2C Proje Yönetim Rehberi

## 🎯 Proje Takip Sistemi

Bu proje tek kişilik bir geliştirme sürecidir ve aşağıdaki metodoloji kullanılmaktadır:

### 📊 GitHub Issues Organizasyonu

#### Issue Types & Labels:
- **🚀 Epic** (`epic`): Büyük özellik grupları (FAZ 1, FAZ 2, etc.)
- **📋 User Story** (`user-story`): Geliştirme hikayeleri
- **🐛 Bug** (`bug`): Hata raporları
- **🔧 Task** (`task`): Teknik görevler
- **📚 Documentation** (`documentation`): Dokümantasyon

#### Priority Labels:
- `critical` - Kritik (sistem kırıcı)
- `high-priority` - Yüksek öncelik
- `medium-priority` - Orta öncelik
- `low-priority` - Düşük öncelik

#### Phase Labels:
- `faz-1` - Temel Altyapı
- `faz-2` - Ürün Yönetimi
- `faz-3` - Fiyatlandırma
- `faz-4` - Frontend
- `faz-5` - Ödeme
- `faz-6` - Admin Panel

#### Week Labels:
- `hafta-1`, `hafta-2`, `hafta-3`, `hafta-4`

## 🔄 Sprint Workflow

### 1. Sprint Planning (Hafta başı)
- [ ] Sprint goal belirle
- [ ] User story'leri priority'e göre sırala
- [ ] Story point'leri estimate et
- [ ] Sprint backlog oluştur

### 2. Daily Development
- [ ] Günlük hedefleri belirle
- [ ] Progress'i issue'larda güncelle
- [ ] Blocker'ları dokümante et

### 3. Sprint Review (Hafta sonu)
- [ ] Tamamlanan story'leri review et
- [ ] Demo hazırla (varsa)
- [ ] Velocity hesapla

### 4. Sprint Retrospective
- [ ] Ne iyi gitti?
- [ ] Ne geliştirilebilir?
- [ ] Action item'ları belirle

## 📝 Issue Template'leri

### User Story Template:
```markdown
# 📋 [Feature Name]

## User Story
**As a** [user type]
**I want to** [functionality]
**So that** [benefit]

## Acceptance Criteria
- [ ] Criteria 1
- [ ] Criteria 2

## Technical Tasks
- [ ] Task 1
- [ ] Task 2

## Estimation
- **Story Points:** X
- **Time Estimate:** X hours
- **Priority:** High/Medium/Low

## Dependencies
- [ ] Issue #X

## Definition of Done
- [ ] All acceptance criteria met
- [ ] Code reviewed
- [ ] Tests written and passing
- [ ] Documentation updated
```

### Bug Template:
```markdown
# 🐛 [Bug Title]

## Bug Description
[Clear description of the bug]

## Steps to Reproduce
1. Step 1
2. Step 2
3. Step 3

## Expected Behavior
[What should happen]

## Actual Behavior
[What actually happens]

## Environment
- OS: [e.g., macOS, Windows, Linux]
- Browser: [e.g., Chrome, Firefox]
- Version: [e.g., 1.0.0]

## Screenshots
[If applicable]
```

## 🌿 Git Branch Strategy

### Branch Naming Convention:
- `feature/US-XXX-short-description` - User story implementation
- `bugfix/BUG-XXX-short-description` - Bug fixes
- `hotfix/CRITICAL-short-description` - Critical fixes
- `docs/DOC-XXX-description` - Documentation updates

### Commit Message Convention:
```
type(scope): description (#issue)

feat: add user authentication system (#3)
fix: resolve login validation bug (#15)
docs: update API documentation (#22)
test: add unit tests for product service (#8)
refactor: improve database query performance (#12)
```

### Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `test`: Testing
- `refactor`: Code refactoring
- `perf`: Performance improvement
- `chore`: Maintenance

## 📊 Progress Tracking

### Story Points Estimation:
- **1 Point:** Very Simple (1-2 hours)
- **3 Points:** Simple (3-6 hours)
- **5 Points:** Medium (1-2 days)
- **8 Points:** Complex (3-5 days)
- **13 Points:** Very Complex (1+ week)

### Velocity Calculation:
```
Velocity = Completed Story Points / Sprint Duration (weeks)
```

### Burndown Tracking:
Her gün `documents/sprint-planning/FAZ1-Sprint-Plan.md` dosyasını güncelle:
- Tamamlanan story points
- Kalan story points
- Daily notes

## 🛠️ Development Workflow

### 1. Issue'yu Al
```bash
# Issue'yu assign et
# GitHub'da issue'yu kendine assign et
```

### 2. Branch Oluştur
```bash
git checkout main
git pull origin main
git checkout -b feature/US-001-laravel-setup
```

### 3. Development
```bash
# Kod geliştir
# Test yaz
# Commit et
git add .
git commit -m "feat: Laravel 11 kurulumu tamamlandı (#3)"
```

### 4. Code Review (Self)
- [ ] Code quality check
- [ ] Test coverage check
- [ ] Performance check
- [ ] Security check

### 5. Merge & Deploy
```bash
git checkout main
git merge feature/US-001-laravel-setup
git push origin main

# Issue'yu close et
```

## 📈 Reporting & Metrics

### Weekly Reports
Her hafta sonu oluştur:
- Completed stories
- Velocity
- Blockers
- Next week plan

### Monthly Reports
Her ay sonu:
- Phase progress
- Overall velocity
- Risks & mitigation
- Timeline updates

## 🔧 Tools & Environment

### Development Tools:
- **IDE:** VS Code
- **Version Control:** Git + GitHub
- **Database:** MySQL + phpMyAdmin
- **Testing:** PHPUnit + Pest
- **Documentation:** Markdown

### Project Management:
- **Issues:** GitHub Issues
- **Projects:** GitHub Projects (Kanban)
- **Documentation:** README + /documents
- **Communication:** Issue comments

## 📋 Quality Gates

### Definition of Ready (DoR):
- [ ] User story well defined
- [ ] Acceptance criteria clear
- [ ] Dependencies identified
- [ ] Estimation completed

### Definition of Done (DoD):
- [ ] All acceptance criteria met
- [ ] Code reviewed
- [ ] Tests written and passing
- [ ] Documentation updated
- [ ] No critical bugs
- [ ] Performance acceptable

---

**Proje Başlangıcı:** 15 Temmuz 2025  
**Son Güncelleme:** 15 Temmuz 2025  
**Maintainer:** @oguzhanfiliz
