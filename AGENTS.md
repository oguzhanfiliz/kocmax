# Repository Guidelines

## Project Structure & Module Organization
- `app/` holds Laravel domain logic grouped by responsibility (`Models`, `Services`, `Filament`, `ValueObjects`) to keep concerns isolated.
- HTTP fronts live in `routes/`, Blade views in `resources/views`, and the Inertia bundle in `resources/js/app.js`; Vite ships assets to `public/build`.
- Database migrations, factories, and seeders stay in `database/`, while long-form knowledge lives under `docs/`; reuse the catalog utilities in `scripts/` instead of ad-hoc fixes.

## Build, Test, and Development Commands
- `composer install && npm install` syncs dependencies after each branch update.
- `php artisan serve` starts the API/admin stack at `http://localhost:8000`; pair with `npm run dev` for live asset rebuilding.
- `php artisan migrate:fresh --seed` resets schema and demo data; add `php artisan shield:generate --all` when permissions change.
- `npm run build` produces production assets; run it before deployment tags or preview builds.

## Coding Style & Naming Conventions
- Follow PSR-12 with four-space indentation and one PHP class per file named after the class (`PricingService.php`).
- Format PHP with `./vendor/bin/pint`; keep controller logic in `app/Http/Controllers`, queued jobs in `app/Jobs`, Filament resources in `app/Filament`.
- Use StudlyCase for PHP classes, camelCase for helpers, and PascalCase for frontend components; keep utility modules in camelCase (`usePricing.ts`).

## Testing Guidelines
- Store feature tests in `tests/Feature` and unit coverage in `tests/Unit`; name suites `SomethingTest.php` for PHPUnit discovery.
- Run `php artisan test` against a freshly migrated database and capture results in the PR description.
- Use `php artisan test --coverage` to monitor critical modules (pricing, campaigns, RBAC) and aim for ≥80% statement coverage using factories instead of hard-coded IDs.

## Commit & Pull Request Guidelines
- Adopt Conventional Commits (`feat:`, `fix:`, `chore:`) as seen in the log for easier release notes.
- Keep commits focused—separate backend logic, migrations, and frontend assets when possible.
- Pull requests should outline changes, affected modules, evidence of testing, linked Notion/GitHub issues, and screenshots for UI updates.

## Security & Configuration Tips
- Keep `.env` out of version control; bootstrap with `cp .env.example .env` and record secrets in secure channels only.
- After configuration updates, run `php artisan config:cache` in staging/production to lock settings.
- When adjusting authorisation, regenerate shields via `php artisan shield:generate --all` and reseed with `php artisan db:seed --class=PermissionSeederForAdminRole`.
