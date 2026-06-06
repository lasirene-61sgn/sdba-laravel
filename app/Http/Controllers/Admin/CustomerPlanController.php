<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CustomerPlanController extends Controller
{
    private function getCurrentAdminId()
    {
        return Auth::guard('admin')->id();
    }
    
    /**
     * Display a listing of the resource (Index).
     */
    public function index()
    {
        $adminId = $this->getCurrentAdminId();
        // Fetch plans related to customers created by the current admin
        $plans = CustomerPlan::with('customer')
                            ->where('admin_id', $adminId)
                            ->latest()
                            ->get();

        return view('admin.customer_plan.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource (Create).
     */
    public function create()
    {
        $adminId = $this->getCurrentAdminId();
        
        // CRITICAL: Fetch ONLY customers created by the current admin for the dropdown
        $customers = Customer::where('admin_id', $adminId)->pluck('name', 'id');
        
        return view('admin.customer_plan.create', compact('customers'));
    }

    /**
     * Fetch customer details when selected in the form (AJAX Endpoint).
     */
    public function getCustomerDetails($customerId)
    {
        $customer = Customer::with('village')
                            ->where('id', $customerId)
                            ->where('admin_id', $this->getCurrentAdminId())
                            ->firstOrFail();

        return response()->json([
            'father_name' => $customer->father_name,
            'mobile' => $customer->mobile,
            'address' => $customer->dno . ', ' . $customer->street_road . ', ' . 
                         $customer->area . ', ' . $customer->city . ', ' . 
                         $customer->pincode,
            'village_name' => $customer->village->name ?? 'N/A',
        ]);
    }

    /**
     * Store a newly created resource in storage (Store).
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_type' => 'required|in:monthly,yearly',
            'start_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,pending',
        ]);
        
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now();
        
        $nextDueDate = $startDate->copy()->add(
            $validatedData['plan_type'] === 'monthly' ? '1 month' : '1 year'
        );

        CustomerPlan::create(array_merge($validatedData, [
            'admin_id' => $this->getCurrentAdminId(),
            'start_date' => $startDate->toDateString(),
            'next_due_date' => $nextDueDate->toDateString(),
        ]));

        return redirect()->route('admin.customer-plan.index')->with('success', 'Customer plan created successfully!');
    }

    /**
     * Show the form for editing the specified resource (Edit).
     */
    public function edit(CustomerPlan $customerPlan)
    {
        if ($customerPlan->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Fetch only the specific customer for the dropdown (since we don't need the whole list here)
        $customers = Customer::where('id', $customerPlan->customer_id)->pluck('name', 'id');
        
        return view('admin.customer_plan.edit', compact('customerPlan', 'customers'));
    }

    /**
     * Update the specified resource in storage (Update).
     */
    public function update(Request $request, CustomerPlan $customerPlan)
    {
        if ($customerPlan->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_type' => 'required|in:monthly,yearly',
            'start_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,pending',
        ]);

        $startDate = Carbon::parse($request->input('start_date'));
        $nextDueDate = $startDate->copy()->add(
            $validatedData['plan_type'] === 'monthly' ? '1 month' : '1 year'
        );

        $customerPlan->update(array_merge($validatedData, [
            'start_date' => $startDate->toDateString(),
            'next_due_date' => $nextDueDate->toDateString(),
        ]));

        return redirect()->route('admin.customer-plan.index')->with('success', 'Customer plan updated successfully!');
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerPlan $customerPlan)
    {
        if ($customerPlan->admin_id !== $this->getCurrentAdminId()) {
            abort(403, 'Unauthorized action.');
        }
        
        $customerPlan->delete();
        
        return redirect()->route('admin.customer-plan.index')->with('success', 'Customer plan deleted successfully!');
    }
}