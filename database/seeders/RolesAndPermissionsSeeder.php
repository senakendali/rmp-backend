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
            'purchase-request' => [
                'add purchase request',        // Permission to add purchase requests
                'follow up purchase request', // Permission to follow up on purchase requests
                'update status purchase request', // Permission to update status of purchase requests
                'view purchase history',      // Permission to view purchase history
                'create new po',           // Permission to create new PO
                'add to existing po',      // Permission to add to existing PO
            ],
            'purchase-order' => [
                'follow up purchase order', // Permission to follow up on purchase orders
                'delete purchase order',   // Permission to delete purchase orders
                'view detail purchase order', // Permission to view details of purchase orders
                'edit purchase order',     // Permission to edit purchase orders
                'view offers',             // Permission to view offers
                'view vendor history',     // Permission to view vendor history
            ],
            'vendor-management' => [
                'add vendor',               // Permission to add vendors
                'edit vendor',              // Permission to edit vendors
                'vendor verification',      // Permission to verify vendor
                'vendor approval',          // Permission to approve vendor
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
                'purchase-request.add purchase request',
                'vendor-management.add vendor',
                'vendor-management.edit vendor',
            ],
            'ppic' => [
                'purchase-request.add purchase request',
                'purchase-request.follow up purchase request',
            ],
            'factory-manager' => [
                'purchase-request.update status purchase request',
            ],
            'procurement' => [
                'procurement.create new po',
                'procurement.add to existing po',
                'vendor-management.add vendor',
                'vendor-management.edit vendor',
                'vendor-management.vendor verification', 
                'purchase-order.follow up purchase order', 
                'purchase-order.delete purchase order',
                'purchase-order.view detail purchase order',
                'purchase-order.edit purchase order',
                'purchase-order.view offers',
                'purchase-order.view vendor history',
            ],
            'bod' => [
                'purchase-order.view offers',
                'purchase-order.view vendor history',
                'vendor-management.vendor approval',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::findByName($roleName);
            $role->givePermissionTo($permissions);
        }
    }
}
