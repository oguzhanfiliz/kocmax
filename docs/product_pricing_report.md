# Product API Fiyat Raporu

## Rotalar
- `GET /api/v1/products`
- `GET /api/v1/products/{product}`
- `GET /api/v1/products/{product}/pricing`
- `GET /api/v1/products/filters`
- `GET /api/v1/products/search-suggestions`
- `GET /api/v1/products/variant-types`
- `GET /api/v1/newproducts`

## Örnek JSON Yanıtı — `GET /api/v1/products/101`
```json
{
  "data": {
    "id": 101,
    "name": "BX-6000 Kışlık-Gri",
    "slug": "bx-6000-kislik-gri",
    "description": null,
    "short_description": "TABCAP / KIŞLIK\n",
    "sku": "PRD-250902-GAG",
    "brand": null,
    "gender": "unisex",
    "safety_standard": null,
    "is_featured": false,
    "is_bestseller": false,
    "is_active": true,
    "sort_order": 0,
    "pricing": {
      "base_price": 161,
      "your_price": 104.650000000000005684341886080801486968994140625,
      "your_price_formatted": "104,65 ₺",
      "base_price_formatted": "161,00 ₺",
      "currency": "TRY",
      "price_type": "👤 Bireysel Fiyat",
      "customer_type": "B2C",
      "discount_percentage": 35,
      "discount_amount": 56.349999999999994315658113919198513031005859375,
      "savings_amount": 56.349999999999994315658113919198513031005859375,
      "smart_pricing_enabled": true,
      "is_dealer_price": false,
      "pricing_tier": null,
      "quantity": 1,
      "price_excl_tax": 104.650000000000005684341886080801486968994140625,
      "price_excl_tax_formatted": "104,65 ₺",
      "price_incl_tax": 115.1200000000000045474735088646411895751953125,
      "price_incl_tax_formatted": "115,12 ₺",
      "tax_rate": 10,
      "tax_amount": 10.4700000000000006394884621840901672840118408203125,
      "tax_amount_formatted": "10,47 ₺",
      "total_price_excl_tax": 104.650000000000005684341886080801486968994140625,
      "total_price_incl_tax": 115.1200000000000045474735088646411895751953125,
      "total_tax_amount": 10.4700000000000006394884621840901672840118408203125
    },
    "seo": {
      "meta_title": null,
      "meta_description": null,
      "meta_keywords": null
    },
    "price": {
      "original": 161,
      "converted": 104.650000000000005684341886080801486968994140625,
      "currency": "TRY",
      "formatted": "104,65 ₺"
    },
    "images": [
      {
        "id": 89,
        "image_url": "http://localhost/storage/products/01K45E87Z7J43N9DG64Q0A2J1R.png",
        "alt_text": null,
        "is_primary": true
      }
    ],
    "categories": [
      {
        "id": 18,
        "name": "Kafa ve Yüz Koruyucuları",
        "slug": "kafa-ve-yuz-koruyuculari"
      },
      {
        "id": 37,
        "name": "TABCAP HELMET",
        "slug": "tabcap-helmet"
      }
    ],
    "variants": [
      {
        "id": 390,
        "sku": "PRD-250902-GAG-DEFAULT",
        "color": "Gri",
        "size": null,
        "stock": 100,
        "price": {
          "original": 161,
          "converted": 161,
          "currency": "TRY",
          "formatted": "161,00 ₺"
        },
        "images": [
          {
            "id": 439,
            "image_url": "https://admin.kocmax.tr/storage/variant-images/01K45E94BN4DTE086A2G49TSDC.webp",
            "alt_text": "Gri - Görsel 1",
            "is_primary": true,
            "sort_order": 0
          }
        ],
        "pricing": {
          "base_price": 161,
          "your_price": 104.650000000000005684341886080801486968994140625,
          "your_price_formatted": "104,65 ₺",
          "base_price_formatted": "161,00 ₺",
          "currency": "TRY",
          "price_type": "👤 Bireysel Fiyat",
          "customer_type": "B2C",
          "discount_percentage": 35,
          "discount_amount": 56.349999999999994315658113919198513031005859375,
          "savings_amount": 56.349999999999994315658113919198513031005859375,
          "smart_pricing_enabled": true,
          "is_dealer_price": false,
          "pricing_tier": null,
          "quantity": 1,
          "price_excl_tax": 104.650000000000005684341886080801486968994140625,
          "price_excl_tax_formatted": "104,65 ₺",
          "price_incl_tax": 115.1200000000000045474735088646411895751953125,
          "price_incl_tax_formatted": "115,12 ₺",
          "tax_rate": 10,
          "tax_amount": 10.4700000000000006394884621840901672840118408203125,
          "tax_amount_formatted": "10,47 ₺",
          "total_price_excl_tax": 104.650000000000005684341886080801486968994140625,
          "total_price_incl_tax": 115.1200000000000045474735088646411895751953125,
          "total_tax_amount": 10.4700000000000006394884621840901672840118408203125
        },
        "package_dimensions": {
          "box_quantity": {
            "value": null,
            "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M21 16V8C21 5.79086 19.2091 4 17 4H7C4.79086 4 3 5.79086 3 8V16C3 18.2091 4.79086 20 7 20H17C19.2091 20 21 18.2091 21 16Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M3 10H21\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 4V2\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M17 4V2\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
            "label": "Kutu Adeti",
            "formatted": null
          },
          "product_weight": {
            "value": null,
            "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M19 15L20.09 21.26L27 22L20.09 22.74L19 29L17.91 22.74L11 22L17.91 21.26L19 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M5 15L6.09 21.26L13 22L6.09 22.74L5 29L3.91 22.74L-3 22L3.91 21.26L5 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
            "label": "Ürün Ağırlığı",
            "formatted": null
          },
          "package_quantity": {
            "value": 50,
            "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M3 7H21V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M3 7L12 2L21 7\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M17 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 11H17\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
            "label": "Koli Adeti",
            "formatted": "50 Adet"
          },
          "package_weight": {
            "value": null,
            "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M19 15L20.09 21.26L27 22L20.09 22.74L19 29L17.91 22.74L11 22L17.91 21.26L19 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M5 15L6.09 21.26L13 22L6.09 22.74L5 29L3.91 22.74L-3 22L3.91 21.26L5 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
            "label": "Koli Ağırlığı",
            "formatted": null
          },
          "package_size": {
            "value": null,
            "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M3 7H21V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M3 7L12 2L21 7\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M17 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 11H17\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M9 7L15 7\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
            "label": "Koli Ölçüsü",
            "formatted": null
          }
        },
        "variant_types": [
          {
            "id": 1,
            "name": "Color",
            "display_name": "Renk",
            "slug": "color",
            "input_type": "color",
            "is_required": false,
            "options": [
              {
                "id": 8,
                "name": "Gray",
                "value": "Gri",
                "display_value": "Gri",
                "slug": "gray",
                "hex_color": "#808080",
                "image_url": null,
                "sort_order": 7,
                "is_selected": true
              }
            ]
          }
        ]
      }
    ],
    "variant_types": [
      {
        "id": 1,
        "name": "Color",
        "display_name": "Renk",
        "slug": "color",
        "input_type": "color",
        "is_required": false,
        "options": [
          {
            "id": 8,
            "name": "Gray",
            "value": "Gri",
            "display_value": "Gri",
            "slug": "gray",
            "hex_color": "#808080",
            "image_url": null,
            "sort_order": 7
          }
        ]
      }
    ],
    "in_stock": true,
    "certificates": [],
    "package_dimensions": {
      "box_quantity": {
        "value": null,
        "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M21 16V8C21 5.79086 19.2091 4 17 4H7C4.79086 4 3 5.79086 3 8V16C3 18.2091 4.79086 20 7 20H17C19.2091 20 21 18.2091 21 16Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M3 10H21\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 4V2\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M17 4V2\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
        "label": "Kutu Adeti",
        "formatted": null
      },
      "product_weight": {
        "value": null,
        "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M19 15L20.09 21.26L27 22L20.09 22.74L19 29L17.91 22.74L11 22L17.91 21.26L19 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M5 15L6.09 21.26L13 22L6.09 22.74L5 29L3.91 22.74L-3 22L3.91 21.26L5 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
        "label": "Ürün Ağırlığı",
        "formatted": null
      },
      "package_quantity": {
        "value": 50,
        "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M3 7H21V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M3 7L12 2L21 7\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M17 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 11H17\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
        "label": "Koli Adeti",
        "formatted": "50 Adet"
      },
      "package_weight": {
        "value": null,
        "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M12 2L13.09 8.26L20 9L13.09 9.74L12 16L10.91 9.74L4 9L10.91 8.26L12 2Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M19 15L20.09 21.26L27 22L20.09 22.74L19 29L17.91 22.74L11 22L17.91 21.26L19 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M5 15L6.09 21.26L13 22L6.09 22.74L5 29L3.91 22.74L-3 22L3.91 21.26L5 15Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
        "label": "Koli Ağırlığı",
        "formatted": null
      },
      "package_size": {
        "value": null,
        "icon": "<svg width=\"24\" height=\"24\" viewBox=\"0 0 24 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path d=\"M3 7H21V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7Z\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M3 7L12 2L21 7\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M17 21V11\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M7 11H17\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n            <path d=\"M9 7L15 7\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"/>\n        </svg>",
        "label": "Koli Ölçüsü",
        "formatted": null
      }
    },
    "created_at": "2025-09-02T14:45:29.000000Z",
    "updated_at": "2025-09-02T14:45:29.000000Z",
    "reviews": {
      "average_rating": 0,
      "total_reviews": 0,
      "rating_distribution": [
        0,
        0,
        0,
        0,
        0
      ],
      "recent_reviews": []
    },
    "specifications": {},
    "meta": {
      "total_stock": 100,
      "lowest_price": 161,
      "highest_price": 161
    }
  },
  "message": "Ürün detayları başarıyla getirildi",
  "pricing_info": {
    "customer_type": "B2C",
    "type_label": "👤 Bireysel Fiyat",
    "is_authenticated": false,
    "is_dealer": false,
    "discount_percentage": 35,
    "pricing_tier": null,
    "smart_pricing_enabled": true
  }
}
```

## Fiyat Bilgisi Analizi

### Gerekli Alanlar
| Alan | Konum | Gerekçe |
| --- | --- | --- |
| `base_price` | `data.pricing.base_price` | Liste fiyatını sağlar; indirim hesaplarının başlangıç noktası. |
| `your_price` | `data.pricing.your_price` | Müşteriye gösterilecek indirimli fiyat. |
| `*_formatted` | `data.pricing.{base_price,your_price,price_excl_tax,price_incl_tax,tax_amount}_formatted` | Yerel para formatı hazır döner; frontend tarafında ek formatlama ihtiyacını azaltır. |
| `currency` | `data.pricing.currency` | Para birimi bilgisini taşır; çoklu para senaryoları için zorunlu. |
| `price_type` | `data.pricing.price_type` | Akıllı fiyatlandırmada hangi label'ın gösterileceğini belirler. |
| `customer_type` | `data.pricing.customer_type` | B2B/B2C ayrımı için ihtiyaç duyulan temel bilgi. |
| `discount_percentage` | `data.pricing.discount_percentage` | Yüzdesel indirim rozetleri ve kontrol hesapları için kullanılır. |
| `discount_amount` | `data.pricing.discount_amount` | Kullanıcıya kazancı parasal olarak göstermek için gerekli. |
| `smart_pricing_enabled` | `data.pricing.smart_pricing_enabled` | Fiyatların dinamik hesaplandığını tespit etmek için. |
| `is_dealer_price` | `data.pricing.is_dealer_price` | Bayi fiyatı mı sorusunu yanıtlar; frontende farklı uyarılar için. |
| `pricing_tier` | `data.pricing.pricing_tier` | B2B katman bilgisini taşır; henüz `null`, fakat fiyat kademeleri için zorunlu hale gelecek. |
| `quantity` | `data.pricing.quantity` | Toplam fiyat alanlarının hangi adet üzerinden hesaplandığını belirtir. |
| `price_excl_tax` | `data.pricing.price_excl_tax` | Net fiyat gösterimleri ve vergi hesaplarının tabanı. |
| `price_incl_tax` | `data.pricing.price_incl_tax` | KDV dahil etiket fiyatı. |
| `tax_rate` | `data.pricing.tax_rate` | Vergi gösterimleri ve doğrulama için gerekli. |
| `tax_amount` | `data.pricing.tax_amount` | Vergi tutarı; fiyat kırılımlarında kullanılır. |
| `total_price_excl_tax` | `data.pricing.total_price_excl_tax` | Adet >1 olduğunda net toplamı hazır verir. |
| `total_price_incl_tax` | `data.pricing.total_price_incl_tax` | Adet >1 olduğunda brüt toplamı hazır verir. |
| `total_tax_amount` | `data.pricing.total_tax_amount` | Toplam vergi değerini (adetli) sunar. |
| `variants[].pricing.*` | `data.variants[].pricing` | Her varyant için yukarıdaki alanların kopyasını sağlar; varyant seçimi için zorunlu. |
| `pricing_info.type_label` | `pricing_info.type_label` | Kullanıcıya gösterilecek fiyat etiketi metni. |
| `pricing_info.is_authenticated` | `pricing_info.is_authenticated` | Kullanıcının giriş durumuna göre fiyat mesajı üretmek için. |
| `pricing_info.is_dealer` | `pricing_info.is_dealer` | Bayi kullanıcı senaryoları için kontrol. |
| `pricing_info.smart_pricing_enabled` | `pricing_info.smart_pricing_enabled` | Arayüzde smart pricing badge'lerini kontrol etmek için. |

### Gereksiz veya Sadeleştirilebilir Alanlar
| Alan | Konum | Gerekçe |
| --- | --- | --- |
| `savings_amount` | `data.pricing.savings_amount` | `discount_amount` ile birebir aynı değeri döndürüyor; tek alan tutulabilir. |
| `price` | `data.price.*` | `pricing` nesnesinin sadeleştirilmiş kopyası; kod içinde “legacy” yorumlanmış, yeni tüketicilerde kaldırılabilir. |
| `variants[].price.*` | `data.variants[].price` | Varyant `pricing` objesinde aynı bilgiler zaten mevcut. |
| `pricing_info.customer_type` | `pricing_info.customer_type` | `data.pricing.customer_type` ile birebir aynı; tek kaynaktan okunabilir. |
| `pricing_info.discount_percentage` | `pricing_info.discount_percentage` | Üstteki `data.pricing.discount_percentage` ile aynı; yinelenen bilgi. |
| `pricing_info.pricing_tier` | `pricing_info.pricing_tier` | `data.pricing.pricing_tier` ile aynı değeri taşıyor; tek alan yeterli. |

> Not: JSON yanıtındaki yüksek hassasiyetli ondalıklar PHP'nin `float` dönüş türünden kaynaklanıyor. Ekranda 2 basamaklı gösterim için `your_price_formatted` gibi alanların kullanılması önerilir.
