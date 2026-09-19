<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Jewellery', 'Manufacturing', 'Electrical', 'Hardware', 'Provisions',
            'Finance', 'Home Needs', 'Interior', 'Construction Product', 'Builders',
            'Automobiles', 'Electronics', 'IT Products', 'Plastics', 'Steel',
            'Stationery', 'Dry Fruits', 'pharmaceutical', 'Chemical', 'Wellness',
            'Reckzin', 'Consultancy', 'Real Estate', 'Professionals'
        ];

        foreach ($categories as $c) {
            \App\Models\Category::firstOrCreate(['name' => $c]);
        }

    }
}
