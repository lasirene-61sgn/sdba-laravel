<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use App\Models\SupportCategory;

class TestPostCreation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:post-creation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test post and category creation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing post and category creation...');
        
        // Count before creation
        $postsBefore = Post::count();
        $categoriesBefore = SupportCategory::count();
        
        $this->info("Before creation - Posts: $postsBefore, Categories: $categoriesBefore");
        
        // Create a new post
        $post = Post::create([
            'name' => 'Test Post ' . time(),
            'status' => 'active'
        ]);
        
        $this->info("Created post with ID: {$post->id}, Name: {$post->name}");
        
        // Create a new category
        $category = SupportCategory::create([
            'name' => 'Test Category ' . time(),
            'status' => 'active'
        ]);
        
        $this->info("Created category with ID: {$category->id}, Name: {$category->name}");
        
        // Count after creation
        $postsAfter = Post::count();
        $categoriesAfter = SupportCategory::count();
        
        $this->info("After creation - Posts: $postsAfter, Categories: $categoriesAfter");
        
        // Test getActive method
        $activePosts = Post::getActive();
        $activeCategories = SupportCategory::getActive();
        
        $this->info("Active posts count: " . count($activePosts));
        $this->info("Active categories count: " . count($activeCategories));
        
        $this->info('Test completed successfully!');
    }
}