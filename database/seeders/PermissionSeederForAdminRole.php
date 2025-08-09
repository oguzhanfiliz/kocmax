<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeederForAdminRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin rolü oluştur
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Önce mevcut izinleri kontrol et
        $allPermissions = Permission::all();
        
        // Eğer hiç permission yoksa önce PermissionSeeder'ı çalıştır
        if ($allPermissions->isEmpty()) {
            $this->command->info('ℹ️ Henüz permission tanımlanmamış, temel izinler oluşturuluyor...');
            $this->call(PermissionSeeder::class);
            $allPermissions = Permission::all();
        }

        // SÜPER OTOMATİK YAKLAŞIM: Admin rolüne sistemdeki TÜM mevcut izinleri ver
        // Bu sayede yeni kaynak eklendiğinde o kaynağın shield:generate çalıştırılması yeterli
        $role->syncPermissions($allPermissions);

        $this->command->info('🚀 Admin rolü TAMAMEN OTOMATİK olarak ' . $allPermissions->count() . ' izin ile güncellendi!');
        $this->command->info('📋 YENİ KAYNAK EKLEDİĞİNİZDE:');
        $this->command->info('   1. php artisan shield:generate --all (yeni izinler oluşur)');
        $this->command->info('   2. php artisan db:seed --class=PermissionSeederForAdminRole (admin otomatik alır)');
        $this->command->info('   VEYA: php artisan db:seed (hepsini birden çalıştırır)');
    }
}
