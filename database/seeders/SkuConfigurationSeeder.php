<?php

namespace Database\Seeders;

use App\Models\SkuConfiguration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SkuConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SkuConfiguration::firstOrCreate(
            ['is_default' => true],
            [
                'name' => 'Varsayılan SKU Formatı',
                'pattern' => '{*}-{*}-{*}', // Category-Product-Number
                'separator' => '-',
                'number_length' => 3,
                'last_number' => 0,
                'is_default' => true,
            ]
        );
    }
}
