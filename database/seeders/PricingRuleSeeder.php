<?php

namespace Database\Seeders;

use App\Models\PricingRule;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // B2B Fiyatlandırma Kuralları
        
        // 1. Miktar Bazlı İndirimler
        PricingRule::firstOrCreate(
            [
                'slug' => 'b2b-toplu-alim-indirimi-10-urun',
            ],
            [
                'name' => 'B2B Toplu Alım İndirimi - 10+ Ürün',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'min_quantity' => 10
                ],
                'actions' => [
                    'discount_percentage' => 5
                ],
                'description' => '10 ve üzeri ürün alımında %5 ek indirim',
                'priority' => 10,
                'is_active' => true,
            ]
        );

        PricingRule::firstOrCreate(
            [
                'slug' => 'b2b-buyuk-miktar-indirimi-25-urun',
            ],
            [
                'name' => 'B2B Büyük Miktar İndirimi - 25+ Ürün',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'min_quantity' => 25
                ],
                'actions' => [
                    'discount_percentage' => 8
                ],
                'description' => '25 ve üzeri ürün alımında %8 ek indirim',
                'priority' => 15,
                'is_active' => true,
            ]
        );

        PricingRule::firstOrCreate(
            [
                'slug' => 'b2b-super-toplu-alim-50-urun',
            ],
            [
                'name' => 'B2B Süper Toplu Alım - 50+ Ürün',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'min_quantity' => 50
                ],
                'actions' => [
                    'discount_percentage' => 12
                ],
                'description' => '50 ve üzeri ürün alımında %12 ek indirim',
                'priority' => 20,
                'is_active' => true,
            ]
        );

        // 2. Tutar Bazlı İndirimler
        PricingRule::firstOrCreate(
            [
                'slug' => 'b2b-yuksek-tutarli-siparis-2000',
            ],
            [
                'name' => 'B2B Yüksek Tutarlı Sipariş - 2000₺+',
                'type' => 'fixed_amount',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'min_order_amount' => 2000
                ],
                'actions' => [
                    'discount_amount' => 100
                ],
                'description' => '2000₺ ve üzeri siparişlerde 100₺ ek indirim',
                'priority' => 12,
                'is_active' => true,
            ]
        );

        PricingRule::firstOrCreate(
            [
                'slug' => 'b2b-premium-siparis-5000',
            ],
            [
                'name' => 'B2B Premium Sipariş - 5000₺+',
                'type' => 'fixed_amount',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'min_order_amount' => 5000
                ],
                'actions' => [
                    'discount_amount' => 300
                ],
                'description' => '5000₺ ve üzeri siparişlerde 300₺ ek indirim',
                'priority' => 18,
                'is_active' => true,
            ]
        );

        // 3. B2C Fiyatlandırma Kuralları
        PricingRule::firstOrCreate(
            [
                'slug' => 'ilk-alisveris-indirimi',
            ],
            [
                'name' => 'İlk Alışveriş İndirimi',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2c',
                    'is_first_time' => true
                ],
                'actions' => [
                    'discount_percentage' => 10
                ],
                'description' => 'İlk kez alışveriş yapan müşterilere %10 hoş geldin indirimi',
                'priority' => 25,
                'is_active' => true,
            ]
        );

        PricingRule::firstOrCreate(
            [
                'slug' => 'b2c-toplu-alim-5-urun',
            ],
            [
                'name' => 'B2C Toplu Alım - 5+ Ürün',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2c',
                    'min_quantity' => 5
                ],
                'actions' => [
                    'discount_percentage' => 6
                ],
                'description' => 'Bireysel müşteriler için 5+ ürün alımında %6 indirim',
                'priority' => 8,
                'is_active' => true,
            ]
        );

        PricingRule::firstOrCreate(
            [
                'slug' => 'b2c-yuksek-tutar-indirimi-500',
            ],
            [
                'name' => 'B2C Yüksek Tutar İndirimi - 500₺+',
                'type' => 'fixed_amount',
                'conditions' => [
                    'customer_type' => 'b2c',
                    'min_order_amount' => 500
                ],
                'actions' => [
                    'discount_amount' => 25
                ],
                'description' => '500₺ ve üzeri bireysel siparişlerde 25₺ indirim',
                'priority' => 10,
                'is_active' => true,
            ]
        );

        // 4. Misafir Kullanıcı Kuralları
        PricingRule::firstOrCreate(
            [
                'slug' => 'misafir-kullanici-tesvik-indirimi',
            ],
            [
                'name' => 'Misafir Kullanıcı Teşvik İndirimi',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'guest',
                    'is_first_visit' => true
                ],
                'actions' => [
                    'discount_percentage' => 5
                ],
                'description' => 'İlk kez siteyi ziyaret eden misafirlere %5 indirim',
                'priority' => 5,
                'is_active' => true,
            ]
        );

        PricingRule::firstOrCreate(
            [
                'slug' => 'misafir-toplu-alim-10-urun',
            ],
            [
                'name' => 'Misafir Toplu Alım - 10+ Ürün',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'guest',
                    'min_quantity' => 10
                ],
                'actions' => [
                    'discount_percentage' => 8
                ],
                'description' => 'Misafir kullanıcılar için 10+ ürün alımında %8 indirim',
                'priority' => 12,
                'is_active' => true,
            ]
        );

        // 5. Özel Kampanyalar
        PricingRule::firstOrCreate(
            [
                'slug' => 'kis-guvenlik-kampanyasi',
            ],
            [
                'name' => 'Kış Güvenlik Kampanyası',
                'type' => 'percentage',
                'conditions' => [
                    'campaign_code' => 'KIS2024',
                    'min_order_amount' => 300
                ],
                'actions' => [
                    'discount_percentage' => 15
                ],
                'description' => 'Kış güvenlik ürünleri için özel kampanya - 300₺ üzeri %15 indirim',
                'priority' => 30,
                'is_active' => true,
                'starts_at' => now()->subDays(10),
                'ends_at' => now()->addDays(30),
            ]
        );

        PricingRule::firstOrCreate(
            [
                'slug' => 'yilbasi-ozel-indirimi',
            ],
            [
                'name' => 'Yılbaşı Özel İndirimi',
                'type' => 'fixed_amount',
                'conditions' => [
                    'coupon_code' => 'YILBASI2024',
                    'min_order_amount' => 200
                ],
                'actions' => [
                    'discount_amount' => 50
                ],
                'description' => 'Yılbaşına özel kupon kodu ile 200₺ üzeri siparişlerde 50₺ indirim',
                'priority' => 35,
                'is_active' => true,
                'starts_at' => now()->subDays(5),
                'ends_at' => now()->addDays(15),
            ]
        );

        // 6. Öğrenci İndirimi
        PricingRule::firstOrCreate(
            [
                'slug' => 'ogrenci-indirimi',
            ],
            [
                'name' => 'Öğrenci İndirimi',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2c',
                    'is_student' => true
                ],
                'actions' => [
                    'discount_percentage' => 12
                ],
                'description' => 'Geçerli öğrenci belgesi olan müşteriler için %12 indirim',
                'priority' => 20,
                'is_active' => true,
            ]
        );

        // 7. Sezonsal Kampanyalar
        PricingRule::firstOrCreate(
            [
                'slug' => 'yaz-guvenlik-urunleri-kampanyasi',
            ],
            [
                'name' => 'Yaz Güvenlik Ürünleri Kampanyası',
                'type' => 'percentage',
                'conditions' => [
                    'season' => 'summer',
                    'min_quantity' => 3
                ],
                'actions' => [
                    'discount_percentage' => 10
                ],
                'description' => 'Yaz aylarında güvenlik ürünlerinde 3+ alımda %10 indirim',
                'priority' => 15,
                'is_active' => false, // Mevsim dışı
            ]
        );

        // 8. Sadakat Programı
        PricingRule::firstOrCreate(
            [
                'slug' => 'sadik-musteri-bonusu',
            ],
            [
                'name' => 'Sadık Müşteri Bonusu',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2c',
                    'loyalty_points' => 1000
                ],
                'actions' => [
                    'discount_percentage' => 7
                ],
                'description' => '1000+ sadakat puanı olan müşteriler için %7 ek indirim',
                'priority' => 22,
                'is_active' => true,
            ]
        );

        // 9. Referans İndirimi
        PricingRule::firstOrCreate(
            [
                'slug' => 'arkadasini-getir-indirimi',
            ],
            [
                'name' => 'Arkadaşını Getir İndirimi',
                'type' => 'fixed_amount',
                'conditions' => [
                    'has_referral' => true,
                    'min_order_amount' => 150
                ],
                'actions' => [
                    'discount_amount' => 20
                ],
                'description' => 'Referans ile gelen müşterilere 150₺ üzeri siparişlerde 20₺ indirim',
                'priority' => 18,
                'is_active' => true,
            ]
        );

        // 10. Hızlı Teslimat Kampanyası
        PricingRule::firstOrCreate(
            [
                'slug' => 'hizli-teslimat-bonusu',
            ],
            [
                'name' => 'Hızlı Teslimat Bonusu',
                'type' => 'percentage',
                'conditions' => [
                    'delivery_type' => 'express',
                    'min_order_amount' => 400
                ],
                'actions' => [
                    'discount_percentage' => 3
                ],
                'description' => 'Hızlı teslimat seçen ve 400₺ üzeri sipariş veren müşterilere %3 ek indirim',
                'priority' => 8,
                'is_active' => true,
            ]
        );

        // 11. B2B Erken Ödeme İndirimi
        PricingRule::firstOrCreate(
            [
                'slug' => 'b2b-erken-odeme-indirimi',
            ],
            [
                'name' => 'B2B Erken Ödeme İndirimi',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2b',
                    'payment_method' => 'advance_payment',
                    'min_order_amount' => 1000
                ],
                'actions' => [
                    'discount_percentage' => 4
                ],
                'description' => 'Peşin ödeme yapan B2B müşterilerine 1000₺ üzeri siparişlerde %4 ek indirim',
                'priority' => 12,
                'is_active' => true,
            ]
        );

        // 12. İnaktif Kural (Test için)
        PricingRule::firstOrCreate(
            [
                'slug' => 'eski-kampanya-inaktif',
            ],
            [
                'name' => 'Eski Kampanya - İnaktif',
                'type' => 'percentage',
                'conditions' => [
                    'customer_type' => 'b2c',
                    'old_campaign' => true
                ],
                'actions' => [
                    'discount_percentage' => 25
                ],
                'description' => 'Süresi biten eski kampanya - test amaçlı inaktif',
                'priority' => 5,
                'is_active' => false,
            ]
        );

        // Kategori İlişkilerini ekle
        $this->createCategoryRules();

        $this->command->info('✅ ' . PricingRule::count() . ' fiyatlandırma kuralı oluşturuldu.');
        $this->command->info('🎯 Aktif kurallar: ' . PricingRule::where('is_active', true)->count());
        $this->command->info('⏸️  İnaktif kurallar: ' . PricingRule::where('is_active', false)->count());
        $this->command->info('📈 B2B kuralları: ' . PricingRule::whereJsonContains('conditions->customer_type', 'b2b')->count());
        $this->command->info('👤 B2C kuralları: ' . PricingRule::whereJsonContains('conditions->customer_type', 'b2c')->count());
        $this->command->info('👥 Guest kuralları: ' . PricingRule::whereJsonContains('conditions->customer_type', 'guest')->count());
    }

    private function createCategoryRules(): void
    {
        // Kategori varsa kategori özel kuralları ekle
        $categories = Category::limit(3)->get();

        if ($categories->count() > 0) {
            foreach ($categories as $index => $category) {
                $rule = PricingRule::firstOrCreate(
                    [
                        'slug' => strtolower(str_replace(' ', '-', $category->name)) . '-kategori-ozel-indirimi',
                    ],
                    [
                        'name' => "{$category->name} Kategori Özel İndirimi",
                        'type' => 'percentage',
                        'conditions' => [
                            'category_id' => $category->id,
                            'min_quantity' => 3
                        ],
                        'actions' => [
                            'discount_percentage' => 6 + $index
                        ],
                        'description' => "{$category->name} kategorisinde 3+ ürün alımında %" . (6 + $index) . " özel indirim",
                        'priority' => 15,
                        'is_active' => true,
                    ]
                );

                // Pivot tablo ilişkisi
                DB::table('pricing_rule_categories')->updateOrInsert([
                    'pricing_rule_id' => $rule->id,
                    'category_id' => $category->id
                ]);
            }

            $this->command->info('📂 ' . $categories->count() . ' kategori özel kuralı eklendi.');
        }
    }
}