<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GalleryItem;

class MigrateGalleryImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all gallery items that have an old image_path
        $galleryItems = GalleryItem::whereNotNull('image_path_old')->get();
        
        foreach ($galleryItems as $item) {
            // Convert the single image path to an array
            $imagePaths = [$item->image_path_old];
            
            // Update the item with the new image_paths array
            $item->image_paths = $imagePaths;
            $item->save();
        }
        
        $this->command->info('Migrated ' . $galleryItems->count() . ' gallery items to support multiple images.');
    }
}