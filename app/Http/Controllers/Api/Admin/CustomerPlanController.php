<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerPlan;
use App\Models\Customer;

class CustomerPlanController extends Controller
{
    /**
     * Display a listing of customer plans
     */
    public function index(Request $request)
    {
        $adminId = $request->user()->id;
        
        $customerPlans = CustomerPlan::with('customer')
                                    ->where('admin_id', $adminId)
                                    ->latest()
                                    ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $customerPlans,
        ]);
    }

    /**
     * Store a newly created customer plan
     */
    public function store(Request $request)
    {
        $adminId = $request->user()->id;
        
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'next_due_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive',
        ]);

        // Verify customer belongs to admin
        $customer = Customer::where('admin_id', $adminId)
                           ->where('id', $validatedData['customer_id'])
                           ->firstOrFail();

        $validatedData['admin_id'] = $adminId;
        $customerPlan = CustomerPlan::create($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Customer plan created successfully',
            'data' => $customerPlan,
        ], 201);
    }

    /**
     * Display the specified customer plan
     */
    public function show(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $customerPlan = CustomerPlan::with('customer')
                                   ->where('admin_id', $adminId)
                                   ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $customerPlan,
        ]);
    }

    /**
     * Update the specified customer plan
     */
    public function update(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $customerPlan = CustomerPlan::where('admin_id', $adminId)
                                   ->findOrFail($id);

        $validatedData = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'plan_type' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'next_due_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:active,inactive',
        ]);

        // If customer_id is being updated, verify customer belongs to admin
        if (isset($validatedData['customer_id'])) {
            $customer = Customer::where('admin_id', $adminId)
                               ->where('id', $validatedData['customer_id'])
                               ->firstOrFail();
        }

        $customerPlan->update($validatedData);

        return response()->json([
            'status' => 'success',
            'message' => 'Customer plan updated successfully',
            'data' => $customerPlan,
        ]);
    }

    /**
     * Remove the specified customer plan
     */
    public function destroy(Request $request, $id)
    {
        $adminId = $request->user()->id;
        
        $customerPlan = CustomerPlan::where('admin_id', $adminId)
                                   ->findOrFail($id);

        $customerPlan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Customer plan deleted successfully',
        ]);
    }
}