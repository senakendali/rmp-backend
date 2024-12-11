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
        // Define roles
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

        // Define permissions by page
        $pagePermissions = [
            'purchase_request' => [
                'add purchase request',        // Permission to add purchase requests
                'follow up purchase request', // Permission to follow up on purchase requests
                'update status purchase request', // Permission to update status of purchase requests
            ],
            'procurement' => [
                'view offers',             // Permission to view offers
                'view vendor history',     // Permission to view vendor history
                'create new po',           // Permission to create new PO
                'add to existing po',      // Permission to add to existing PO
            ],
        ];

        // Create permissions
        foreach ($pagePermissions as $page => $permissions) {
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['name' => "{$page}.{$permission}"]);
            }
        }

        // Assign permissions to roles
        $rolePermissions = [
            'department' => [
                'purchase_request.add purchase request',
            ],
            'ppic' => [
                'purchase_request.add purchase request',
                'purchase_request.follow up purchase request',
            ],
            'factory-manager' => [
                'purchase_request.update status purchase request',
            ],
            'procurement' => [
                'procurement.create new po',
                'procurement.add to existing po',
            ],
            'bod' => [
                'procurement.view offers',
                'procurement.view vendor history',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::findByName($roleName);
            $role->givePermissionTo($permissions);
        }
    }
}
