<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $roles = [
            'department', // Role for department users
            'ppic', // Role for production planning and inventory control
            'factory-manager', // Role for factory managers
            'procurement', // Role for procurement (pengadaan)
            'bod', // Role for Board of Directors (Direksi)
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create permissions
        $permissions = [
            'add purchase request', // Permission to add purchase requests
            'follow up purchase request', // Permission to follow up on purchase requests
            'update status purchase request', // Permission to update status of purchase requests
            'view offers', // Permission to view offers
            'view vendor history', // Permission to view vendor history
            'create new po', // Permission to create new PO'
            'add to existing po', // Permission to add to existing PO
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $departmentRole = Role::findByName('department');
        $ppicRole = Role::findByName('ppic');
        $factoryManagerRole = Role::findByName('factory-manager');
        $procurementRole = Role::findByName('procurement');
        $bodRole = Role::findByName('bod');

        // Assign permissions to roles
        // Example: You can customize which permissions each role should have
        $departmentRole->givePermissionTo([
            'add purchase request',
        ]);

        $ppicRole->givePermissionTo([
            'add purchase request',
            'follow up purchase request',
        ]);

        $factoryManagerRole->givePermissionTo([
            'update status purchase request',
        ]);

        $procurementRole->givePermissionTo([
            'create new po',
            'add to existing po',
        ]);

        $bodRole->givePermissionTo([
            'view offers',
            'view vendor history',
        ]);
    }
}
