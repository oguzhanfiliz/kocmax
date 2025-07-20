# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a hybrid B2B/B2C e-commerce platform for occupational health and safety clothing, built with Laravel 11 and Filament 3. The system serves both dealers (B2B) and individual customers (B2C) with distinct pricing and workflow models.

## Recent Development Updates

- Implemented comprehensive multi-currency pricing system with TCMB exchange rate integration
- Added dealer-specific pricing tiers and discount mechanisms
- Developed complex product variant generation service
- Created unified B2B/B2C user management with role-based access control
- Implemented Filament 3 admin panel with advanced resource management
- Added automated SKU generation for products
- Integrated product caching service for performance optimization

## Essential Development Commands

### Laravel Commands
```bash
php artisan serve                    # Start development server
php artisan migrate                  # Run database migrations
php artisan db:seed                  # Seed development data
php artisan test                     # Run test suite
php artisan queue:work               # Process background jobs
php artisan exchange:update          # Update TCMB exchange rates
php artisan make:test-user           # Create test users
./vendor/bin/pint                   # Format code (Laravel Pint)
```

[... rest of the existing content remains unchanged ...]