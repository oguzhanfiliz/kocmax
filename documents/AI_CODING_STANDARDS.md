
# AI Development and Coding Standards for Laravel Filament CMS

This document outlines the development standards, conventions, and architectural patterns for this project. All AI-assisted code generation, refactoring, and modifications **must** strictly adhere to these rules to ensure code quality, consistency, security, and maintainability.

## 1. General Principles

### 1.1. Security First
- **Never Trust User Input:** All data from external sources (requests, forms, APIs) must be validated using Laravel's Form Requests or the Validator facade.
- **Prevent Mass Assignment:** All Eloquent models **must** use the `$fillable` property to explicitly define which attributes are mass-assignable. Avoid using `$guarded = []`.
- **Prevent SQL Injection:** Use the Eloquent ORM and query builder for all database interactions. Avoid raw SQL queries (`DB::raw`) unless absolutely necessary, and if so, use parameter binding.
- **Prevent XSS:** Blade's `{{ }}` syntax escapes output by default. Always use it. Never use `{!! !!}` with user-provided content.
- **CSRF Protection:** All `POST`, `PUT`, `PATCH`, and `DELETE` routes in `routes/web.php` must be protected by the `web` middleware group, which includes CSRF protection.

### 1.2. Clean Code & SOLID
- **Readability:** Write clear, self-documenting code. Use meaningful names for variables, functions, classes, and methods.
- **DRY (Don't Repeat Yourself):** Abstract reusable logic into helper functions, traits, or service classes.
- **KISS (Keep It Simple, Stupid):** Prefer simple, straightforward solutions over complex and overly-engineered ones.
- **SOLID Principles:**
    - **Single Responsibility:** A class or method should have only one reason to change. (e.g., Controllers handle HTTP requests, Services handle business logic, Models handle data).
    - **Open/Closed:** Software entities should be open for extension but closed for modification. Use interfaces and abstract classes.
    - **Liskov Substitution:** Subtypes must be substitutable for their base types.
    - **Interface Segregation:** Clients should not be forced to depend on interfaces they do not use.
    - **Dependency Inversion:** Depend on abstractions, not on concretions. Use Dependency Injection.

### 1.3. Follow Existing Patterns
Before writing any new code, analyze the existing codebase (`app/` directory) to understand and replicate its architectural patterns, naming conventions, and style. Consistency is key.

## 2. Technology Stack & Standards

- **Backend:** Laravel 10 (PHP 8.2+)
- **Admin Panel:** Filament 3.x
- **Frontend:** Vite, Tailwind CSS, Alpine.js
- **PHP Standard:** All PHP code **must** adhere to the **PSR-12** coding standard.

## 3. Backend (Laravel) Guidelines

### 3.1. Naming Conventions
- **Controllers:** `SingularPascalCase` followed by `Controller` (e.g., `ProjectController`).
- **Models:** `SingularPascalCase` (e.g., `ProjectCategory`).
- **Migrations:** Default Laravel naming (`create_plural_snake_case_table`).
- **Database Tables:** `plural_snake_case` (e.g., `project_categories`).
- **Table Columns & Model Properties:** `snake_case` (e.g., `created_at`, `full_name`).
- **Routes:** `plural.kebab-case` for resource routes (e.g., `projects.show`).

### 3.2. Architecture
- **Thin Controllers:** Controllers should be lean. Their primary role is to handle the HTTP request and response. Delegate business logic to Service classes.
- **Service Layer:** For complex business logic, create service classes (e.g., `app/Services/ProjectService.php`). Inject these services into controllers.
- **Repository Pattern:** For complex database queries that are reused, consider a Repository pattern (e.g., `app/Repositories/ProjectRepository.php`). For standard CRUD, Eloquent is sufficient.
- **Form Requests:** Use dedicated Form Request classes for validating all incoming data for `store` and `update` operations. (e.g., `app/Http/Requests/StoreProjectRequest.php`).

### 3.3. Eloquent ORM
- **Relationships:** Define all model relationships clearly (e.g., `hasMany`, `belongsTo`).
- **Eager Loading:** To prevent N+1 query problems, always use eager loading (`with()`) when retrieving models with their relationships.
- **Scopes:** Use local query scopes in models to encapsulate reusable query logic.

## 4. Admin Panel (Filament) Guidelines

### 4.1. Resources
- **Structure:** Keep Filament Resources organized. The static `form()` and `table()` methods are the source of truth for the resource's schema.
- **Modularity:** Use `Tabs` and `Sections` within forms to group related fields and improve user experience for complex models.
- **Actions & Filters:** Use table actions for operations on a single record and bulk actions for multiple records. Implement filters for all searchable or filterable columns.

### 4.2. Livewire & Alpine.js
- **Livewire:** Remember that Filament components are built on Livewire. Use Livewire's features for server-side reactivity.
- **Alpine.js:** Use Alpine.js for trivial client-side interactions that don't require a server roundtrip. Keep this to a minimum.

## 5. Frontend Guidelines

### 5.1. Tailwind CSS
- **Utility-First:** Use Tailwind's utility classes directly in the Blade templates.
- **Avoid Custom CSS:** Do not write custom CSS files unless absolutely necessary for a complex component that cannot be built with utilities.
- **Theme Configuration:** If custom colors, fonts, or spacing are needed, extend the theme via `tailwind.config.js`.

### 5.2. Vite
- **Asset Bundling:** All CSS and JS assets must be compiled through Vite. Reference them in Blade templates using the `@vite()` directive.

## 6. Database

- **Migrations:** All database schema changes **must** be performed through migration files. The `down()` method of a migration must correctly reverse the changes made in the `up()` method.
- **Seeders:** Use seeder files to populate the database with initial or test data.

## 7. Testing

- **Feature Tests:** Write feature tests (using Pest or PHPUnit) for all new functionality to cover the main use cases, including validation and authorization logic.
- **Unit Tests:** Write unit tests for complex service classes or helper functions with pure logic.
