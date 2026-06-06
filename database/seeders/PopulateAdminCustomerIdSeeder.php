<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class PopulateAdminCustomerIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all customers grouped by admin_id
        $customersByAdmin = Customer::select('id', 'admin_id')
            ->orderBy('admin_id')
            ->orderBy('id')
            ->get()
            ->groupBy('admin_id');

        // Process each admin's customers
        foreach ($customersByAdmin as $adminId => $customers) {
            $counter = 1;
            foreach ($customers as $customer) {
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['admin_customer_id' => $counter]);
                
                $counter++;
            }
        }
    }
}