<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeederForAdminRole::class,
            PermissionSeeder::class,
            UserSeeder::class,
            CurrencySeeder::class,
            AttributeTypeSeeder::class,
            SkuConfigurationSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
