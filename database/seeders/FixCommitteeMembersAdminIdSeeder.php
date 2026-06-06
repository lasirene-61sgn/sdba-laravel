<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CommitteePerson;
use App\Models\Admin;

class FixCommitteeMembersAdminIdSeeder extends Seeder
{
    /**
     * Run the database seeds to fix committee members with incorrect admin_id values.
     */
    public function run(): void
    {
        // Get all admins
        $admins = Admin::all();
        
        if ($admins->isEmpty()) {
            echo "No admins found. Skipping committee member admin_id fix.\n";
            return;
        }
        
        // Get the first admin as the default admin for fixing committee members
        $defaultAdmin = $admins->first();
        
        // Get all committee members with null or invalid admin_id
        $committeeMembers = CommitteePerson::whereNull('admin_id')
            ->orWhereNotIn('admin_id', $admins->pluck('id')->toArray())
            ->get();
            
        foreach ($committeeMembers as $member) {
            // Assign the default admin to these committee members
            $member->admin_id = $defaultAdmin->id;
            $member->save();
            
            echo "Fixed committee member ID {$member->id}: assigned to admin ID {$defaultAdmin->id}\n";
        }
        
        echo "Fixed " . $committeeMembers->count() . " committee members with incorrect admin_id values.\n";
    }
}