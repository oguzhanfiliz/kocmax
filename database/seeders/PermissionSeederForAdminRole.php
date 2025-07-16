<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        $models = [
            'user',
            'shield::role',
            'product_attribute',
            'attribute_type',
            'sku_configuration',
        ];

        $actions = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force_delete',
            'replicate',
            'reorder',
            'delete_any',
            'restore_any',
            'force_delete_any',
            'publish' // for pages
        ];

        $permissionsToCreate = [];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                 // shield::role için format farklı
                if ($model === 'shield::role') {
                    $permissionsToCreate[] = $action . '_' . $model;
                } else {
                    $permissionsToCreate[] = $action . '_' . $model;
                }
            }
        }
        
        // Ekstra izinler
        $extra_permissions = [
            'publish_pages'
        ];

        foreach (array_merge($permissionsToCreate, $extra_permissions) as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Admin rolüne tüm izinleri senkronize et
        $allPermissions = Permission::all();
        $role->syncPermissions($allPermissions);
    }
}
