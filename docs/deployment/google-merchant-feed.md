# Google Merchant Feed Deployment Guide

## Overview
- Feed generator lives under `app/Services/Feed/GoogleMerchant`.
- `php artisan merchant:generate-feed` creates `feeds/google-merchant.xml` on the configured disk.
- Daily cron (default `0 3 * * *`) is registered in `app/Console/Kernel.php`.

## Configuration
Set the following environment keys per environment (see `config/feeds.php` for defaults):

```
GOOGLE_MERCHANT_FEED_ENABLED=true
GOOGLE_MERCHANT_FEED_DISK=public            # or s3
GOOGLE_MERCHANT_FEED_PATH=feeds/google-merchant.xml
GOOGLE_MERCHANT_PRODUCT_URL_BASE=https://www.kocmax.tr
GOOGLE_MERCHANT_PRODUCT_URL_PREFIX=urun     # update to storefront pattern
GOOGLE_MERCHANT_ASSET_URL_BASE=https://cdn.kocmax.tr (opsiyonel)
GOOGLE_MERCHANT_DEFAULT_CATEGORY=Home & Garden > Kitchen & Dining
GOOGLE_MERCHANT_BRAND=KOCMAX
GOOGLE_MERCHANT_SCHEDULE_ENABLED=true
GOOGLE_MERCHANT_SCHEDULE="0 3 * * *"
```

Additional knobs:
- `GOOGLE_MERCHANT_MAX_IMAGES` limits additional images per item (default 10).
- `GOOGLE_MERCHANT_ITEM_LIMIT` caps the number of items for dry runs.
- `GOOGLE_MERCHANT_CHANNEL_*` overrides RSS channel metadata.
- `config('feeds.google_merchant.category_slug_map')` dizisine slug → Google taxonomy eşlemelerini girerek `g:google_product_category` değerlerini parametrik yönetebilirsiniz.

## Manual Generation
```
php artisan merchant:generate-feed --disk=public --path=feeds/google-merchant.xml
```
- `--disk` / `--path` override configuration for ad-hoc exports.
- Command output logs success/skip counts; details also written to `storage/logs/google_merchant_feed.log` by the scheduler.

## Validation
Use the lightweight validator to verify the generated XML before uploading:
```
php artisan merchant:validate-feed --disk=public --path=feeds/google-merchant.xml
```
The command checks well-formedness and ensures the Google namespace exists. For schema-level validation use `xmllint` or Merchant Center diagnostics.

## Storage & CDN
- Default disk is `public`, resulting file path: `storage/app/public/feeds/google-merchant.xml`.
- When using S3, set `GOOGLE_MERCHANT_FEED_DISK=s3` and ensure bucket policy allows public reads.
- Existing feed is backed up as `<filename>.<timestamp>.xml` before each overwrite.

## Scheduling
- Scheduler runs on the app server; ensure `php artisan schedule:work` or system cron is active.
- Toggle scheduling per environment with `GOOGLE_MERCHANT_SCHEDULE_ENABLED`.
- Cron output appended to `storage/logs/google_merchant_feed.log` for quick troubleshooting.

## Monitoring & Alerting
- Failures bubble up through `Log::error`; integrate with existing logging channels (e.g. papertrail/slack) via `config/logging.php`.
- Review Merchant Center Diagnostics after first deploy and whenever product schema changes.
- Consider adding health-check alerts if feed timestamp (`Storage::lastModified`) is older than 24 hours.

## Data Quality Checklist
- Ensure primary images resolve to HTTPS URLs accessible by Googlebot.
- Populate `google_taxonomy_id` for categories or maintain mapping to avoid diagnostics warnings.
- Confirm GTIN/MPN completeness; products missing both identifiers will be exported with `identifier_exists = no` and may require manual approval.
- Validate price figures reflect VAT-inclusive amounts per Google policy.

## Pilot Launch Steps
1. Run manual generation in staging, validate XML with `merchant:validate-feed` and spot-check sample items.
2. Upload feed to Merchant Center (test environment) and capture diagnostics.
3. Enable scheduler in production once diagnostics return clean.
4. Document Merchant Center feed name + fetch frequency in shared operations runbook.
