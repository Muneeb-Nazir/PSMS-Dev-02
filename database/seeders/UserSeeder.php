<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@psms.com',
            'password' => Hash::make('admin123'),
            'phone' => '+92-300-1234567',
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Operator user
        User::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@elitefuelstation.com',
            'password' => Hash::make('password123'),
            'phone' => '+92-321-9876543',
            'role' => 'operator',
            'status' => 'active',
        ]);
    }
}
