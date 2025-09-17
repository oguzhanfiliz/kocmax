<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->after('base_currency');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->after('parent_id');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'tax_rate')) {
                $table->decimal('tax_rate', 5, 2)->nullable()->after('discount_amount');
            }
        });

        // Varsayılan KDV oranı ayarını oluştur (varsa güncelle)
        $defaultTaxSetting = DB::table('settings')->where('key', 'pricing.default_tax_rate')->first();
        if ($defaultTaxSetting) {
            DB::table('settings')->where('key', 'pricing.default_tax_rate')->update([
                'value' => '20',
                'type' => 'float',
                'group' => 'pricing',
                'label' => 'Varsayılan KDV Oranı (%)',
                'description' => 'Ürün veya kategoride oran belirtilmemişse kullanılacak varsayılan KDV yüzdesi.',
                'is_public' => false,
                'updated_by' => null,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('settings')->insert([
                'key' => 'pricing.default_tax_rate',
                'value' => '20',
                'type' => 'float',
                'group' => 'pricing',
                'label' => 'Varsayılan KDV Oranı (%)',
                'description' => 'Ürün veya kategoride oran belirtilmemişse kullanılacak varsayılan KDV yüzdesi.',
                'is_public' => false,
                'created_by' => null,
                'updated_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'tax_rate')) {
                $table->dropColumn('tax_rate');
            }
        });

        DB::table('settings')->where('key', 'pricing.default_tax_rate')->delete();
    }
};
