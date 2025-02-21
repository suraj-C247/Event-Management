<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@mailinator.com'], // Ensure no duplicate admins
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin@123'), // Change 'password' as needed
                'role' => 'admin',
                'is_active' => true,
            ]
        );
    }
}
