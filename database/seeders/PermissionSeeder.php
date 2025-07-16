<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for users
        Permission::firstOrCreate(['name' => 'view users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete users', 'guard_name' => 'web']);

        // Create permissions for ProductAttribute
        Permission::firstOrCreate(['name' => 'view_product_attribute', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create_product_attribute', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit_product_attribute', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete_product_attribute', 'guard_name' => 'web']);

        // Create permissions for AttributeType
        Permission::firstOrCreate(['name' => 'view_attribute_type', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create_attribute_type', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit_attribute_type', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete_attribute_type', 'guard_name' => 'web']);

        // Create permissions for SkuConfiguration
        Permission::firstOrCreate(['name' => 'view_sku_configuration', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create_sku_configuration', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit_sku_configuration', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete_sku_configuration', 'guard_name' => 'web']);


        // RoleResource için gerekli olabilecek izinler (RolePolicy.php dosyasında kullanıldığı gibi)
        // Bu izinlerin Filament resource'larınızda gerçekten kullanıldığından emin olun.
        Permission::firstOrCreate(['name' => 'view_shield::role', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'create_shield::role', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'edit_shield::role', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'delete_shield::role', 'guard_name' => 'web']);

        // Create roles and assign permissions
        // Admin rolünü oluştur veya bul ve tüm izinleri senkronize et
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        // Editor rolünü oluştur veya bul
        $editorRole = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorPermissions = [
            'view users',
        ];
        // Sadece var olan izinleri ata
        $existingEditorPermissions = Permission::whereIn('name', $editorPermissions)->where('guard_name', 'web')->get();
        $editorRole->syncPermissions($existingEditorPermissions);

        // Author rolünü oluştur veya bul
        $authorRole = Role::firstOrCreate(['name' => 'author', 'guard_name' => 'web']);
        $authorPermissions = [];
        // Sadece var olan izinleri ata
        $existingAuthorPermissions = Permission::whereIn('name', $authorPermissions)->where('guard_name', 'web')->get();
        $authorRole->syncPermissions($existingAuthorPermissions);

        $this->command->info('Permissions and roles seeded successfully.');
    }
}
