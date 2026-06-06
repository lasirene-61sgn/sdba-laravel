<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SuperAdmin;
use Illuminate\Support\Facades\Hash;

class SuperAdminWithImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SuperAdmin::create([
            'name' => 'Test Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('password'),
            'image' => null, // Will be updated manually or through the profile page
        ]);
    }
}
