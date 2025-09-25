# Google Merchant Attribute Mapping

| Google Attribute | Required | Source | Transformation | Notes |
| --- | --- | --- | --- | --- |
| `g:id` | Yes | `product_variants.sku` (fallback: `product_variants.id`) | Trim, uppercase SKU; fallback uses `sprintf('VAR-%d', id)` | Ensures uniqueness per offer. Missing SKU logged and excluded by default. |
| `g:item_group_id` | Yes (for variants) | `products.id` | Cast to string | Groups variants of the same product. |
| `g:title` | Yes | `products.name` + (`product_variants.name` or color/size) | Strip tags, collapse whitespace, truncate to 150 chars | Variant details appended with ` - ` separator. |
| `g:description` | Yes | `products.description` fallback `products.short_description` | Strip HTML, decode entities, collapse whitespace, truncate 5000 chars | Markdown/HTML sanitized. |
| `g:link` | Yes | `config('feeds.google_merchant.product_url_base')` + slug | `rtrim(base, '/') . '/' . ltrim(path, '/')` | Base configurable via env (`FRONTEND_URL`). |
| `g:image_link` | Yes | Variant primary image -> `variant_images.image_url`; fallback product primary image | Ensure absolute HTTPS URL, replace spaces | Uses `Storage::disk('public')->url`. CDN base override via config. |
| `g:additional_image_link` | Optional | Product gallery images ordered by `sort_order` | Same normalization as image_link | Limit 10 entries per item. |
| `g:availability` | Yes | Variant `is_active`, `stock`, `min_stock_level` | `in stock` if stock > min, else `out of stock` `<0` -> `out of stock`, `0` -> `out of stock` | Configurable threshold for `limited availability`. |
| `g:price` | Yes | `MultiCurrencyPricingService::calculatePrice` (original) | Format `number_format(2) currency` | Uses default currency (`Currency::default()`). |
| `g:sale_price` | Conditional | `PriceResult::hasDiscounts()` -> final price | Same format as price | Omitted if original equals final. |
| `g:condition` | Yes | Constant `new` | — | Configurable constant for future use. |
| `g:brand` | Yes | `config('feeds.google_merchant.brand')` (varsayılan: `KOCMAX`) | Trimlenmiş sabit değer | Tüm ürünlerde aynı marka kullanılıyor. |
| `g:gtin` | Conditional | `product_variants.barcode` or `products.barcode` | Validate length (8/12/13/14), digits only | When invalid, send `g:identifier_exists` = `no`. |
| `g:mpn` | Conditional | `product_variants.sku` | Uppercase | Included when GTIN missing. |
| `g:identifier_exists` | Conditional | Derived | `no` if GTIN & MPN missing, else `yes` | Merchant requirement for missing IDs. |
| `g:google_product_category` | Conditional | `categories.google_taxonomy_id` | Cast to string, optional prefix `category:` removed | Requires data seeding. |
| `g:product_type` | Optional | Category breadcrumb | Join category path using ` > ` | Derived from primary category (lowest sort_order). |
| `g:shipping_weight` | Optional | Variant `package_weight` -> fallback product `package_weight` | Convert grams to kg with `number_format(2)` + `kg` suffix | Config flag to use net or gross weight. |
| `g:shipping_length` / `width` / `height` | Optional | Variant `package_length` etc. -> fallback product dims | Format `number_format(1)` + `cm` suffix | Provided when all three available. |
| `g:tax` | Optional | `products.tax_rate` -> fallback `settings('pricing.default_tax_rate')` | Format per Merchant (`country:rate:tax_ship`) | Omitted until business confirms. |
| `g:color` | Optional | `product_variants.color` | Title Case, remove trailing separators | Multiple colors split by `/`. |
| `g:size` | Optional | `product_variants.size` | Trim, uppercase for EU sizes | For multi-size combos, use slash separated. |
| `g:gender` | Optional | `products.gender` | Map to Merchant values (`male`, `female`, `unisex`) | Non-supported values skipped. |
| `g:age_group` | Optional | TBD | — | Requires new data; default omitted. |
| `g:custom_label_0` | Optional | `products.is_featured` etc. | Map to tokens (`featured`, `new`, `bestseller`) | Additional labels configured via config. |
| `g:expiration_date` | Optional | TBD (campaigns) | ISO 8601 | Only for limited-time offers. |
| `g:mobile_link` | Optional | `config('feeds.google_merchant.mobile_url_base')` + slug | Same as link | Fallback to `g:link` when config empty. |

## Validation Rules
- All URLs forced to HTTPS; if storage URL uses HTTP, configurable replacement via `feeds.google_merchant.asset_base_url`.
- Titles/descriptions sanitized with `strip_tags`, `html_entity_decode`, and normalized whitespace.
- Currency formatting uses default currency ISO code; multi-currency conversion performed via `CurrencyConversionService`.
- Items missing both price and availability are excluded from feed with error log.
- XML namespace prefix `g` resolves to `http://base.google.com/ns/1.0` with RSS 2.0 root.

## Outstanding Data Tasks
1. `config/feeds.google_merchant.category_slug_map` içine slug → Google taxonomy eşlemelerini doldurun.
2. Provide shipping configuration (flat rate or Merchant-managed) before enabling `<g:shipping>` nodes.
3. Decide on tax inclusion—if prices are VAT-inclusive, omit `<g:tax>` unless required for cross-border feeds.
