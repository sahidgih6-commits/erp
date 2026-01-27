<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions (only if they don't exist)
        $permissions = [
            // User management
            'manage-owners',
            'manage-managers',
            'manage-salesmen',
            'view-all-users',
            
            // Product management
            'manage-products',
            'view-products',
            
            // Stock management
            'add-stock',
            'view-stock',
            
            // Sales management
            'create-sales',
            'view-own-sales',
            'view-all-sales',
            
            // Reports
            'view-reports',
            'view-all-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles if they don't exist
        $superAdmin = Role::firstOrCreate(['name' => 'superadmin']);
        $owner = Role::firstOrCreate(['name' => 'owner']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $salesman = Role::firstOrCreate(['name' => 'salesman']);
        $cashier = Role::firstOrCreate(['name' => 'cashier']);

        // Sync permissions for roles
        
        // Super Admin
        $superAdmin->syncPermissions([
            'manage-owners',
            'view-all-users',
            'manage-products',
            'view-products',
            'add-stock',
            'view-stock',
            'view-all-sales',
            'view-all-reports',
        ]);

        // Owner
        $owner->syncPermissions([
            'manage-managers',
            'view-all-users',
            'manage-products',
            'view-products',
            'add-stock',
            'view-stock',
            'create-sales',
            'view-all-sales',
            'view-all-reports',
        ]);

        // Manager
        $manager->syncPermissions([
            'manage-salesmen',
            'manage-products',
            'view-products',
            'add-stock',
            'view-stock',
            'create-sales',
            'view-all-sales',
            'view-reports',
        ]);

        // Salesman
        $salesman->syncPermissions([
            'view-products',
            'create-sales',
            'view-own-sales',
        ]);

        // Cashier (POS-only access)
        $cashier->syncPermissions([
            'view-products',
            'create-sales',
            'view-own-sales',
        ]);
    }
}
