<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\SupportCategory;

class SupportDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample posts
        $posts = [
            ['name' => 'Manager'],
            ['name' => 'Supervisor'],
            ['name' => 'Team Lead'],
            ['name' => 'Specialist'],
        ];

        foreach ($posts as $postData) {
            Post::firstOrCreate(
                ['name' => $postData['name']],
                $postData
            );
        }

        // Create sample categories
        $categories = [
            ['name' => 'Technical Support'],
            ['name' => 'Customer Service'],
            ['name' => 'Sales Support'],
            ['name' => 'Administrative'],
        ];

        foreach ($categories as $categoryData) {
            SupportCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }
    }
}