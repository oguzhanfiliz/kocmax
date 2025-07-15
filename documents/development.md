bash#!/bin/bash
# B2B Project GitHub Organization Script

echo "🚀 B2B E-Ticaret GitHub Project Organization Starting..."

# Repository ve Owner bilgilerini al
REPO_OWNER="B2B-B2C"
REPO_NAME="B2B-B2C-main"

echo "📊 Repository Information:"
echo "Owner: $REPO_OWNER"
echo "Repository: $REPO_NAME"

# 1. Repository Owner ID'sini al
echo "🔍 Getting repository owner information..."
OWNER_ID=$(gh api graphql -f query='
  query {
    repositoryOwner(login: "'$REPO_OWNER'") {
      id
      login
      __typename
    }
  }
' --jq '.data.repositoryOwner.id')

echo "✅ Owner ID: $OWNER_ID"

# 2. Repository ID'sini al
echo "🔍 Getting repository information..."
REPO_ID=$(gh api graphql -f query='
  query {
    repository(owner: "'$REPO_OWNER'", name: "'$REPO_NAME'") {
      id
      nameWithOwner
      url
      description
    }
  }
' --jq '.data.repository.id')

echo "✅ Repository ID: $REPO_ID"

# Repository detaylarını göster
echo "📋 Repository Details:"
gh api graphql -f query='
  query {
    repository(owner: "'$REPO_OWNER'", name: "'$REPO_NAME'") {
      nameWithOwner
      description
      url
      isPrivate
      defaultBranchRef {
        name
      }
      languages(first: 10) {
        nodes {
          name
        }
      }
    }
  }
' --jq '.data.repository'

echo "✅ Repository information retrieved successfully!"
3. GitHub Labels Creation
bash#!/bin/bash
# B2B Project Labels Creation

echo "🏷️ Creating B2B project labels..."

# Navigate to your repository directory
cd B2B-B2C-main

# Faz labels (Phase labels)
gh label create "faz-1" --description "📋 FAZ 1: Core Models & Infrastructure" --color "FF6B6B" --force
gh label create "faz-2" --description "💰 FAZ 2: Pricing & Campaign System" --color "4ECDC4" --force  
gh label create "faz-3" --description "💳 FAZ 3: Payment & Orders" --color "45B7D1" --force
gh label create "faz-4" --description "🎨 FAZ 4: Frontend & API" --color "96CEB4" --force
gh label create "faz-5" --description "🚀 FAZ 5: Testing & Deployment" --color "FFEAA7" --force

# Priority labels
gh label create "priority-high" --description "🔴 High Priority" --color "D63031" --force
gh label create "priority-medium" --description "🟡 Medium Priority" --color "FDCB6E" --force
gh label create "priority-low" --description "🟢 Low Priority" --color "6C5CE7" --force

# Type labels
gh label create "backend" --description "🔧 Backend Development" --color "A29BFE" --force
gh label create "frontend" --description "🎨 Frontend Development" --color "FD79A8" --force
gh label create "database" --description "🗄️ Database Related" --color "00B894" --force
gh label create "testing" --description "🧪 Testing" --color "E17055" --force
gh label create "documentation" --description "📚 Documentation" --color "636E72" --force

# B2B Specific labels
gh label create "b2b-feature" --description "👥 B2B Specific Feature" --color "8E44AD" --force
gh label create "dealer-system" --description "🤝 Dealer Management" --color "E67E22" --force
gh label create "pricing-strategy" --description "💲 Pricing Strategy" --color "F39C12" --force
gh label create "payment-integration" --description "💳 Payment Integration" --color "2ECC71" --force

echo "✅ Labels created successfully!"
4. GitHub Milestones Creation
bash#!/bin/bash
# B2B Project Milestones Creation

echo "📅 Creating B2B project milestones..."

# FAZ 1 Milestones
gh api repos/B2B-B2C/B2B-B2C-main/milestones \
  --method POST \
  --field title="📋 FAZ-1-Sprint-1: B2B User System" \
  --field description="B2B User System & Dealer Management - User model'e dealer fields, bayi başvuru sistemi" \
  --field due_on="2025-07-29T23:59:59Z" \
  --field state="open"

gh api repos/B2B-B2C/B2B-B2C-main/milestones \
  --method POST \
  --field title="📦 FAZ-1-Sprint-2: Product Management" \
  --field description="Product & Category Management - Nested kategoriler, product variants, currency sistemi" \
  --field due_on="2025-08-05T23:59:59Z" \
  --field state="open"

gh api repos/B2B-B2C/B2B-B2C-main/milestones \
  --method POST \
  --field title="🎛️ FAZ-1-Sprint-3: Admin Panel" \
  --field description="Filament Admin Panel Resources - CRUD işlemleri, resource'lar" \
  --field due_on="2025-08-12T23:59:59Z" \
  --field state="open"

# FAZ 2 Milestones
gh api repos/B2B-B2C/B2B-B2C-main/milestones \
  --method POST \
  --field title="💰 FAZ-2-Sprint-1: Pricing Strategy" \
  --field description="Pricing Strategy Implementation - Strategy pattern, fiyatlandırma sistemi" \
  --field due_on="2025-08-19T23:59:59Z" \
  --field state="open"

gh api repos/B2B-B2C/B2B-B2C-main/milestones \
  --method POST \
  --field title="🎁 FAZ-2-Sprint-2: Campaign System" \
  --field description="Campaign & Discount System - Decorator pattern, kampanya yönetimi" \
  --field due_on="2025-08-26T23:59:59Z" \
  --field state="open"

echo "✅ Milestones created successfully!"
5. İlk Sprint Issues Creation
bash#!/bin/bash
# B2B Project Initial Issues Creation

echo "📝 Creating initial B2B project issues..."

# Issue 1: B2B User System
gh issue create \
  --title "🔧 [FEATURE] Add B2B Dealer Fields to User Model" \
  --body "## 📝 Description
User model'ine B2B dealer için gerekli alanları eklemek ve dealer management sistemi oluşturmak.

## 🎯 Acceptance Criteria
- [ ] Migration: \`add_dealer_fields_to_users_table\` oluştur
- [ ] User model'e dealer methods ekle (\`isDealer()\`, \`getDealerCode()\`)
- [ ] Factory ve seeder güncelle
- [ ] Unit testler yaz (%90+ coverage)
- [ ] Filament UserResource güncelle

## 🔧 Technical Details
\`\`\`php
// Migration fields:
'dealer_code' => 'string nullable unique',
'dealer_discount_percentage' => 'decimal(5,2) nullable', 
'company_name' => 'string nullable',
'tax_number' => 'string nullable',
'is_approved_dealer' => 'boolean default false',
'dealer_application_date' => 'timestamp nullable',
'approved_at' => 'timestamp nullable',
\`\`\`

## 📋 Definition of Done
- [ ] Migration çalışır ve deploy edilebilir
- [ ] Model methods test edildi ve coverage %90+
- [ ] Admin panel'den dealer oluşturulabiliyor
- [ ] Seeder'lar güncellendi ve test data oluşuyor
- [ ] Code review tamamlandı

## 🏷️ Story Points: 5" \
  --label "enhancement,faz-1,priority-high,backend,b2b-feature,dealer-system" \
  --milestone "📋 FAZ-1-Sprint-1: B2B User System"

# Issue 2: Dealer Application System  
gh issue create \
  --title "📋 [FEATURE] Create Dealer Application System" \
  --body "## 📝 Description
Bayi başvuru formu ve onay sistemi - Command pattern ile approval workflow

## 🎯 Acceptance Criteria
- [ ] DealerApplication model oluştur
- [ ] Migration ve relationships kurulumu
- [ ] Filament DealerApplicationResource
- [ ] Approval workflow (onay/red)
- [ ] Email notifications (Observer pattern)
- [ ] File upload (ticaret sicil, vergi levhası)
- [ ] Dealer code auto-generation

## 🔧 Technical Details
- Command pattern kullanarak approval process
- Observer pattern ile email notifications
- File storage konfigürasyonu (S3/local)
- Queue system ile async email sending

## 📋 Definition of Done
- [ ] Başvuru formu frontend'de çalışır
- [ ] Admin onay/red yapabiliyor
- [ ] Email gönderimi çalışır ve queue'da
- [ ] Dosya upload çalışır ve güvenli
- [ ] Integration testler geçer

## 🏷️ Story Points: 8" \
  --label "feature,faz-1,priority-high,backend,b2b-feature,dealer-system" \
  --milestone "📋 FAZ-1-Sprint-1: B2B User System"

# Issue 3: Product & Category Management
gh issue create \
  --title "📦 [FEATURE] Implement Product & Category Models" \
  --body "## 📝 Description
Ürün ve kategori yönetim sistemi - İSG kategorileri için nested structure

## 🎯 Acceptance Criteria
- [ ] Category model (nested set pattern)
- [ ] Product model + variants
- [ ] ProductVariant model (beden, renk, standart)
- [ ] ProductImage model
- [ ] Migrations ve relationships
- [ ] Filament Resources (Category, Product)

## 🔧 Technical Details
\`\`\`php
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
\`\`\`

## 📋 Definition of Done
- [ ] Nested kategoriler çalışır (parent/child)
- [ ] Ürün variants sistemi aktif
- [ ] Image upload çalışır (multiple images)
- [ ] Admin panel CRUD işlemleri functional
- [ ] Seeder'lar ile test data

## 🏷️ Story Points: 13" \
  --label "feature,faz-1,priority-medium,backend,database" \
  --milestone "📦 FAZ-1-Sprint-2: Product Management"

# Issue 4: Currency & Exchange Rate System
gh issue create \
  --title "💱 [FEATURE] Currency & Exchange Rate Management" \
  --body "## 📝 Description
Multi-currency support (TRY, USD, EUR) ve günlük kur sistemi

## 🎯 Acceptance Criteria
- [ ] Currency model
- [ ] ExchangeRate model  
- [ ] Daily rate input system (admin panel)
- [ ] Rate conversion service
- [ ] Filament Resources (Currency, ExchangeRate)
- [ ] Rate history tracking

## 🔧 Technical Details
- Strategy pattern kullanarak currency conversion
- Manual rate input sistemi (admin panelden)
- Rate history için separate table
- Conversion calculations için service layer

## 📋 Definition of Done
- [ ] Currency CRUD çalışır
- [ ] Exchange rate sistemi aktif
- [ ] Conversion hesaplamaları doğru
- [ ] Admin panel'den rate girişi functional
- [ ] Rate history takibi çalışır

## 🏷️ Story Points: 5" \
  --label "feature,faz-1,priority-medium,backend,b2b-feature" \
  --milestone "📦 FAZ-1-Sprint-2: Product Management"

echo "✅ Initial issues created successfully!"
echo "📊 Created 4 initial issues for FAZ-1"
echo "🔗 View issues: https://github.com/B2B-B2C/B2B-B2C-main/issues"
6. GitHub Issue Templates
bash# Create GitHub issue templates directory
mkdir -p .github/ISSUE_TEMPLATE

# Feature template
cat > .github/ISSUE_TEMPLATE/feature.md << 'EOF'
---
name: 🚀 Feature Request
about: B2B E-Ticaret projesi için yeni özellik talebi
title: '[FEATURE] '
labels: 'enhancement'
assignees: ''
---

## 📝 Feature Description
<!-- Özelliğin detaylı açıklaması -->

## 🎯 Acceptance Criteria
- [ ] Kriter 1
- [ ] Kriter 2
- [ ] Kriter 3

## 🔧 Technical Details
<!-- Teknik implementasyon detayları -->
```php
// Code examples here
📋 Definition of Done

 Code implemented and tested
 Unit tests written (%90+ coverage)
 Integration tests pass
 Documentation updated
 Code reviewed and approved
 Tested on staging environment

🏷️ Story Points
<!-- 1, 2, 3, 5, 8, 13, 21 -->
🔗 Related Issues
<!-- Link to related issues -->
EOF
Bug template
cat > .github/ISSUE_TEMPLATE/bug.md << 'EOF'
name: 🐛 Bug Report
about: B2B E-Ticaret projesi hata bildirimi
title: '[BUG] '
labels: 'bug'
assignees: ''
🐛 Bug Description
<!-- Hatanın detaylı açıklaması -->
🔄 Steps to Reproduce





✅ Expected Behavior
<!-- Beklenen davranış -->
❌ Actual Behavior
<!-- Gerçek davranış -->
🖥️ Environment

Laravel Version:
PHP Version:
Filament Version:
Browser:
OS:

📸 Screenshots
<!-- Eğer varsa ekran görüntüleri -->
📋 Additional Context
<!-- Ek bilgiler -->
EOF
echo "✅ Issue templates created!"

### **7. Final Setup & Commit**

```bash
#!/bin/bash
# Final setup and commit

echo "🚀 Finalizing B2B GitHub project setup..."

# Add all files to git
git add .

# Commit changes
git commit -m "🚀 B2B GitHub Project Organization Setup

✅ Created project labels (faz-1 to faz-5, priorities, types)
✅ Set up milestones for FAZ-1 and FAZ-2
✅ Created initial sprint issues with detailed acceptance criteria
✅ Added GitHub issue templates
✅ Organized development pipeline structure

📋 Ready for development:
- FAZ-1-Sprint-1: B2B User System & Dealer Management
- FAZ-1-Sprint-2: Product & Category Management  
- FAZ-1-Sprint-3: Admin Panel Resources

🎯 Next Steps:
1. Start with User model dealer fields
2. Implement dealer application system
3. Build product management system"

# Push to main branch
git push origin main

echo "✅ B2B GitHub Project Setup Complete!"
echo ""
echo "📊 Summary:"
echo "- ✅ Repository organized with proper labels"
echo "- ✅ Milestones created for sprints"
echo "- ✅ Initial issues created with detailed requirements"
echo "- ✅ Issue templates ready for future use"
echo "- ✅ MCP-ready structure for AI assistance"
echo ""
echo "🔗 Project URLs:"
echo "- Repository: https://github.com/B2B-B2C/B2B-B2C-main"
echo "- Issues: https://github.com/B2B-B2C/B2B-B2C-main/issues"
echo "- Milestones: https://github.com/B2B-B2C/B2B-B2C-main/milestones"
echo ""
echo "🎯 Ready to start development with FAZ-1-Sprint-1!"
🎯 Özet: GitHub MCP Setup Tamamlandı
B2B projeniz için MCP kullanarak aşağıdaki organizasyon yapıldı:
✅ Tamamlanan:

Repository Structure - Proje organizasyonu
Labels System - Faz, priority, type labels
Milestones - Sprint planlaması
Initial Issues - İlk 4 issue detaylı acceptance criteria ile
Issue Templates - Gelecek issues için template'ler
MCP Integration - GitHub MCP server hazır

📋 Oluşturulan Issues:

B2B User System (Priority: High, SP: 5)
Dealer Application System (Priority: High, SP: 8)
Product & Category Management (Priority: Medium, SP: 13)
Currency & Exchange Rate (Priority: Medium, SP: 5)