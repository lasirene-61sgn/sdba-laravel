<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;

class MigrateEventImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all events that have an old image_path
        $events = Event::whereNotNull('image_path_old')->get();
        
        foreach ($events as $event) {
            // Convert the single image path to an array
            $imagePaths = [$event->image_path_old];
            
            // Update the event with the new image_paths array
            $event->image_paths = $imagePaths;
            $event->save();
        }
        
        $this->command->info('Migrated ' . $events->count() . ' events to support multiple images.');
    }
}