# Database Schema - B2B-B2C E-Commerce Platform

## Schema Overview

The database is designed to support a hybrid B2B/B2C e-commerce platform with complex product variants, multi-currency pricing, and dealer management.

## Core Tables

### Users System
```sql
-- users: Main user table for both dealers and customers
users (
    id: bigint unsigned primary key,
    name: varchar(255),
    email: varchar(255) unique,
    email_verified_at: timestamp nullable,
    password: varchar(255),
    phone: varchar(20) nullable,
    address: text nullable,
    city: varchar(100) nullable,
    postal_code: varchar(20) nullable,
    tax_number: varchar(20) nullable,
    company_name: varchar(255) nullable,
    is_dealer: boolean default false,
    dealer_tier: enum('bronze', 'silver', 'gold') nullable,
    created_at: timestamp,
    updated_at: timestamp
)

-- dealer_applications: Dealer registration workflow
dealer_applications (
    id: bigint unsigned primary key,
    user_id: bigint unsigned foreign key,
    company_name: varchar(255),
    tax_number: varchar(20),
    business_address: text,
    contact_person: varchar(255),
    phone: varchar(20),
    status: enum('pending', 'approved', 'rejected') default 'pending',
    rejection_reason: text nullable,
    approved_at: timestamp nullable,
    created_at: timestamp,
    updated_at: timestamp
)
```

### Product Catalog
```sql
-- categories: Hierarchical product categories
categories (
    id: bigint unsigned primary key,
    parent_id: bigint unsigned nullable foreign key,
    name: varchar(255),
    slug: varchar(255) unique,
    description: text nullable,
    image: varchar(255) nullable,
    icon: varchar(100) nullable,
    sort_order: integer default 0,
    is_active: boolean default true,
    created_at: timestamp,
    updated_at: timestamp
)

-- products: Main product information
products (
    id: bigint unsigned primary key,
    name: varchar(255),
    slug: varchar(255) unique,
    description: text nullable,
    short_description: text nullable,
    sku_pattern: varchar(50) nullable,
    base_price: decimal(10,2),
    currency_id: bigint unsigned foreign key,
    stock_quantity: integer default 0,
    min_quantity: integer default 1,
    weight: decimal(8,2) nullable,
    dimensions: json nullable,
    is_active: boolean default true,
    is_featured: boolean default false,
    meta_title: varchar(255) nullable,
    meta_description: text nullable,
    created_at: timestamp,
    updated_at: timestamp
)

-- product_images: Product gallery
product_images (
    id: bigint unsigned primary key,
    product_id: bigint unsigned foreign key,
    image_path: varchar(255),
    alt_text: varchar(255) nullable,
    sort_order: integer default 0,
    is_primary: boolean default false,
    created_at: timestamp,
    updated_at: timestamp
)

-- product_variants: Product variations (size, color, etc.)
product_variants (
    id: bigint unsigned primary key,
    product_id: bigint unsigned foreign key,
    sku: varchar(100) unique,
    price: decimal(10,2),
    stock_quantity: integer default 0,
    weight: decimal(8,2) nullable,
    is_active: boolean default true,
    created_at: timestamp,
    updated_at: timestamp
)
```

### Attribute System
```sql
-- attribute_types: Defines attribute categories (size, color, etc.)
attribute_types (
    id: bigint unsigned primary key,
    name: varchar(100),
    slug: varchar(100) unique,
    input_type: enum('text', 'number', 'select', 'multiselect', 'boolean'),
    is_required: boolean default false,
    is_filterable: boolean default true,
    sort_order: integer default 0,
    created_at: timestamp,
    updated_at: timestamp
)

-- product_attributes: Available attribute values
product_attributes (
    id: bigint unsigned primary key,
    attribute_type_id: bigint unsigned foreign key,
    value: varchar(255),
    slug: varchar(255),
    hex_color: varchar(7) nullable,
    sort_order: integer default 0,
    created_at: timestamp,
    updated_at: timestamp
)

-- product_attribute_values: Links products to their attributes
product_attribute_values (
    id: bigint unsigned primary key,
    product_id: bigint unsigned foreign key,
    attribute_type_id: bigint unsigned foreign key,
    product_attribute_id: bigint unsigned foreign key,
    created_at: timestamp,
    updated_at: timestamp
)

-- product_variant_attributes: Links variants to specific attribute combinations
product_variant_attributes (
    id: bigint unsigned primary key,
    product_variant_id: bigint unsigned foreign key,
    product_attribute_id: bigint unsigned foreign key,
    created_at: timestamp,
    updated_at: timestamp
)

-- sku_configurations: SKU generation patterns
sku_configurations (
    id: bigint unsigned primary key,
    name: varchar(255),
    pattern: varchar(255), -- e.g., "{category_code}-{size}-{color}-{sequence}"
    separator: varchar(10) default '-',
    is_active: boolean default true,
    created_at: timestamp,
    updated_at: timestamp
)
```

### Pricing & Currency
```sql
-- currencies: Supported currencies
currencies (
    id: bigint unsigned primary key,
    code: varchar(3) unique, -- TRY, USD, EUR
    name: varchar(100),
    symbol: varchar(10),
    exchange_rate: decimal(10,4) default 1.0000,
    is_base: boolean default false,
    is_active: boolean default true,
    last_updated: timestamp nullable,
    created_at: timestamp,
    updated_at: timestamp
)

-- bulk_discounts: Quantity-based pricing
bulk_discounts (
    id: bigint unsigned primary key,
    product_id: bigint unsigned foreign key,
    min_quantity: integer,
    max_quantity: integer nullable,
    discount_type: enum('percentage', 'fixed_amount'),
    discount_value: decimal(10,2),
    is_active: boolean default true,
    starts_at: timestamp nullable,
    ends_at: timestamp nullable,
    created_at: timestamp,
    updated_at: timestamp
)

-- dealer_discounts: Dealer-specific pricing
dealer_discounts (
    id: bigint unsigned primary key,
    dealer_id: bigint unsigned foreign key,
    product_id: bigint unsigned nullable, -- null for global discount
    discount_type: enum('percentage', 'fixed_amount'),
    discount_value: decimal(10,2),
    is_active: boolean default true,
    starts_at: timestamp nullable,
    ends_at: timestamp nullable,
    created_at: timestamp,
    updated_at: timestamp
)

-- campaigns: Marketing campaigns
campaigns (
    id: bigint unsigned primary key,
    name: varchar(255),
    description: text nullable,
    type: enum('discount', 'gift', 'bundle'),
    discount_type: enum('percentage', 'fixed_amount') nullable,
    discount_value: decimal(10,2) nullable,
    min_amount: decimal(10,2) nullable,
    is_active: boolean default true,
    starts_at: timestamp,
    ends_at: timestamp,
    created_at: timestamp,
    updated_at: timestamp
)

-- campaign_products: Products included in campaigns
campaign_products (
    id: bigint unsigned primary key,
    campaign_id: bigint unsigned foreign key,
    product_id: bigint unsigned foreign key,
    is_gift: boolean default false,
    required_quantity: integer default 1,
    created_at: timestamp,
    updated_at: timestamp
)

-- discount_coupons: Promotional codes
discount_coupons (
    id: bigint unsigned primary key,
    code: varchar(50) unique,
    description: text nullable,
    discount_type: enum('percentage', 'fixed_amount'),
    discount_value: decimal(10,2),
    min_amount: decimal(10,2) nullable,
    usage_limit: integer nullable,
    used_count: integer default 0,
    is_active: boolean default true,
    starts_at: timestamp,
    ends_at: timestamp,
    created_at: timestamp,
    updated_at: timestamp
)
```

### Order Management
```sql
-- carts: Shopping cart sessions
carts (
    id: bigint unsigned primary key,
    user_id: bigint unsigned nullable foreign key,
    session_id: varchar(255) nullable,
    created_at: timestamp,
    updated_at: timestamp
)

-- cart_items: Items in shopping cart
cart_items (
    id: bigint unsigned primary key,
    cart_id: bigint unsigned foreign key,
    product_variant_id: bigint unsigned foreign key,
    quantity: integer,
    price: decimal(10,2),
    created_at: timestamp,
    updated_at: timestamp
)

-- orders: Customer orders
orders (
    id: bigint unsigned primary key,
    user_id: bigint unsigned foreign key,
    order_number: varchar(50) unique,
    status: enum('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'),
    currency_id: bigint unsigned foreign key,
    subtotal: decimal(10,2),
    tax_amount: decimal(10,2) default 0,
    shipping_amount: decimal(10,2) default 0,
    discount_amount: decimal(10,2) default 0,
    total_amount: decimal(10,2),
    payment_status: enum('pending', 'paid', 'failed', 'refunded'),
    payment_method: varchar(50) nullable,
    shipping_address: json,
    billing_address: json,
    notes: text nullable,
    shipped_at: timestamp nullable,
    delivered_at: timestamp nullable,
    created_at: timestamp,
    updated_at: timestamp
)

-- order_items: Items within orders
order_items (
    id: bigint unsigned primary key,
    order_id: bigint unsigned foreign key,
    product_variant_id: bigint unsigned foreign key,
    quantity: integer,
    unit_price: decimal(10,2),
    total_price: decimal(10,2),
    created_at: timestamp,
    updated_at: timestamp
)

-- product_reviews: Customer product reviews
product_reviews (
    id: bigint unsigned primary key,
    product_id: bigint unsigned foreign key,
    user_id: bigint unsigned foreign key,
    rating: integer check (rating >= 1 and rating <= 5),
    title: varchar(255) nullable,
    comment: text nullable,
    is_approved: boolean default false,
    created_at: timestamp,
    updated_at: timestamp
)
```

### Pivot Tables
```sql
-- category_product: Many-to-many relationship
category_product (
    id: bigint unsigned primary key,
    category_id: bigint unsigned foreign key,
    product_id: bigint unsigned foreign key,
    created_at: timestamp,
    updated_at: timestamp
)
```

### Authorization Tables (Spatie Laravel Permission)
```sql
-- roles: User roles
roles (
    id: bigint unsigned primary key,
    name: varchar(255),
    guard_name: varchar(255),
    created_at: timestamp,
    updated_at: timestamp
)

-- permissions: System permissions
permissions (
    id: bigint unsigned primary key,
    name: varchar(255),
    guard_name: varchar(255),
    created_at: timestamp,
    updated_at: timestamp
)

-- model_has_permissions: Direct user permissions
-- model_has_roles: User role assignments
-- role_has_permissions: Role permission assignments
```

## Key Indexes

### Performance Indexes
```sql
-- Products
INDEX idx_products_slug (slug)
INDEX idx_products_is_active (is_active)
INDEX idx_products_currency_id (currency_id)

-- Product Variants
INDEX idx_variants_product_id (product_id)
INDEX idx_variants_sku (sku)
INDEX idx_variants_is_active (is_active)

-- Categories
INDEX idx_categories_parent_id (parent_id)
INDEX idx_categories_slug (slug)
INDEX idx_categories_is_active (is_active)

-- Orders
INDEX idx_orders_user_id (user_id)
INDEX idx_orders_status (status)
INDEX idx_orders_created_at (created_at)

-- Users
INDEX idx_users_email (email)
INDEX idx_users_is_dealer (is_dealer)
```

## Business Rules

### Data Integrity
- Products must have at least one category
- Product variants inherit base product currency
- Orders must have valid shipping and billing addresses
- SKU must be unique across all product variants

### Pricing Logic
- Base currency (TRY) has exchange rate of 1.0000
- All prices stored in their original currency
- Exchange rates updated daily via TCMB API
- Dealer discounts take precedence over bulk discounts

### Stock Management
- Stock tracked at variant level
- Negative stock allowed for special orders
- Stock reserved during checkout process
- Automatic stock updates on order completion

### User Management
- Dealers must have approved dealer application
- Email verification required for all accounts
- Role assignment automatic upon dealer approval
- Soft delete for important user data

## Migration Strategy

### Phase 1 (Complete)
- Core user and authentication tables
- Basic product and category structure
- Currency and exchange rate management
- Admin authorization system

### Phase 2 (Planned)
- Enhanced attribute system
- Advanced pricing rules
- Campaign and promotion tables

### Phase 3 (Planned)
- Order processing tables
- Cart and checkout functionality
- Review and rating system