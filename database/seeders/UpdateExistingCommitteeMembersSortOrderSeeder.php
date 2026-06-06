<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateExistingCommitteeMembersSortOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the order for common committee positions with exact matches
        $positionOrder = [
            'president' => 0,
            'vice - president' => 1,
            'secretary' => 2,
            'joint-secretary' => 3,
            'treasurer' => 4,
            'joint- treasurer' => 5,
        ];

        // Update existing committee members
        \App\Models\CommitteePerson::each(function ($member) use ($positionOrder) {
            $postName = strtolower($member->post_name);
            
            // Handle common variations - normalize spaces and hyphens
            $normalizedPostName = preg_replace('/[^a-z]/', '', $postName);
            
            // Set sort order based on position, default to 100 for unknown positions
            $sortOrder = $positionOrder[$postName] ?? $positionOrder[$normalizedPostName] ?? 100;
            
            $member->update(['sort_order' => $sortOrder]);
        });
    }
}
