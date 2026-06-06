<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Village;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $customers = Customer::with('village')
                            ->where('admin_id', $adminId)
                            ->latest()
                            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'father_name' => 'nullable|string|max:100',
            'gotra' => 'nullable|string|max:100',
            'label_name' => 'nullable|string|max:100',
            'village_id' => 'nullable|exists:villages,id',
            'district' => 'nullable|string|max:100',
            'ms_firm_name' => 'nullable|string|max:100',
            'dno' => 'nullable|string|max:50',
            'street_road' => 'nullable|string|max:150',
            'address2' => 'nullable|string|max:150',
            'area' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'business_type' => 'nullable|string|max:100',
            'product_service' => 'nullable|string|max:100',
            'office_address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        // Safety check: Ensure the selected village belongs to the current admin
        if ($request->filled('village_id')) {
            $village = Village::find($request->village_id);
            if (!$village || $village->admin_id !== $adminId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid village selection.',
                ], 400);
            }
        }

        $customer = Customer::create(array_merge($validatedData, [
            'admin_id' => $adminId,
        ]));

        return response()->json([
            'status' => 'success',
            'message' => 'Customer created successfully!',
            'data' => $customer,
        ], 201);
    }

    /**
     * Display the specified customer
     */
    public function show(Request $request, Customer $customer)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($customer->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access: You can only view customers you created.',
            ], 403);
        }

        $customer->load('village');

        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ]);
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($customer->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only update customers you created.',
            ], 403);
        }
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:100',
            'father_name' => 'nullable|string|max:100',
            'gotra' => 'nullable|string|max:100',
            'label_name' => 'nullable|string|max:100',
            'village_id' => 'nullable|exists:villages,id',
            'district' => 'nullable|string|max:100',
            'ms_firm_name' => 'nullable|string|max:100',
            'dno' => 'nullable|string|max:50',
            'street_road' => 'nullable|string|max:150',
            'address2' => 'nullable|string|max:150',
            'area' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'gender' => 'nullable|in:male,female,other',
            'business_type' => 'nullable|string|max:100',
            'product_service' => 'nullable|string|max:100',
            'office_address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'anniversary_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Safety check: Ensure the selected village is either owned by the admin or the existing saved one
        if ($request->filled('village_id')) {
            $village = Village::find($request->village_id);
            if (!$village || ($village->admin_id !== $adminId && $village->id !== $customer->village_id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid village selection or permission denied.',
                ], 400);
            }
        }

        $customer->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Customer updated successfully!',
            'data' => $customer,
        ]);
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Request $request, Customer $customer)
    {
        $adminId = $request->user()->id;
        
        // Enforce ownership check
        if ($customer->admin_id !== $adminId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action: You can only delete customers you created.',
            ], 403);
        }
        
        $customer->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Customer deleted successfully!',
        ]);
    }
}