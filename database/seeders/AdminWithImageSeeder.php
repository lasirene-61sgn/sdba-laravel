<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminWithImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'company_name' => 'Test Company',
            'password' => Hash::make('password'),
            'image' => null, // Will be updated manually or through the profile page
            'sidebar_permissions' => [],
            'customer_field_permissions' => [],
        ]);
    }
}
