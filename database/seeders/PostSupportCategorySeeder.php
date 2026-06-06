<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\SupportCategory;

class PostSupportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample posts
        $posts = [
            ['name' => 'Software Engineer', 'status' => 'active'],
            ['name' => 'Product Manager', 'status' => 'active'],
            ['name' => 'Designer', 'status' => 'active'],
            ['name' => 'Data Analyst', 'status' => 'active'],
            ['name' => 'DevOps Engineer', 'status' => 'active'],
        ];

        foreach ($posts as $postData) {
            Post::firstOrCreate(
                ['name' => $postData['name']],
                $postData
            );
        }

        // Create sample support categories
        $categories = [
            ['name' => 'Technical Issue', 'status' => 'active'],
            ['name' => 'Billing Inquiry', 'status' => 'active'],
            ['name' => 'Feature Request', 'status' => 'active'],
            ['name' => 'Account Access', 'status' => 'active'],
            ['name' => 'General Inquiry', 'status' => 'active'],
        ];

        foreach ($categories as $categoryData) {
            SupportCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );
        }
    }
}