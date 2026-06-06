<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Run the support data seeder
        $this->call(SupportDataSeeder::class);
        
        // Run the test customer seeder
        $this->call(TestCustomerSeeder::class);
        
        // Run the admin customer ID seeder
        $this->call(PopulateAdminCustomerIdSeeder::class);
        
        // Run the committee members admin ID fix seeder
        $this->call(FixCommitteeMembersAdminIdSeeder::class);
    }
}