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
            PermissionSeeder::class,
            PermissionSeederForAdminRole::class, // Admin izinlerini ekle
            UserSeeder::class,
            CategorySeeder::class,
            CurrencySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
