<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'department', // Role for department users
            'ppic', // Role for production planning and inventory control
            'factory-manager', // Role for factory managers
            'procurement', // Role for procurement (pengadaan)
            'bod', // Role for Board of Directors (Direksi)
        ];

        foreach ($roles as $role) {
            // Create a user for each role
            $user = User::firstOrCreate(
                ['email' => "{$role}@example.com"], // Ensure unique emails
                [
                    'name' => ucfirst(str_replace('-', ' ', $role)) . ' User',
                    'password' => bcrypt('password'), // Default password
                ]
            );

            // Assign the role to the user
            $user->assignRole($role);

            $this->command->info("User '{$user->email}' created and assigned role '{$role}'.");
        }
    }
}
