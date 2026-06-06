<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BillController extends Controller
{
    /**
     * Display a listing of the bills.
     */
    public function index()
    {
        $admin = Auth::guard('admin')->user();
        $bills = Bill::where('admin_id', $admin->id)
            ->with('customer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.bills.index', compact('bills'));
    }

    /**
     * Show the form for creating a new bill.
     */
    public function create()
    {
        $admin = Auth::guard('admin')->user();
        // Get customers with paid plans
        $customers = Customer::where('admin_id', $admin->id)
            ->whereHas('customerPlans', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        return view('admin.bills.create', compact('customers'));
    }

    /**
     * Store a newly created bill in storage.
     */
    public function store(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id,admin_id,'.$admin->id,
            'billing_type' => 'required|in:monthly,yearly',
            'amount' => 'required|numeric|min:0',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after_or_equal:billing_period_start',
            'due_date' => 'required|date|after_or_equal:billing_period_end',
            'notes' => 'nullable|string',
        ]);

        // Generate unique bill number
        $billNumber = 'BILL-' . strtoupper(Str::random(8));
        while (Bill::where('bill_number', $billNumber)->exists()) {
            $billNumber = 'BILL-' . strtoupper(Str::random(8));
        }

        $bill = new Bill($validatedData);
        $bill->admin_id = $admin->id;
        $bill->bill_number = $billNumber;
        $bill->status = 'pending';
        $bill->save();

        return redirect()->route('admin.bills.index')->with('success', 'Bill created successfully!');
    }

    /**
     * Display the specified bill.
     */
    public function show(Bill $bill)
    {
        // Ensure the bill belongs to the authenticated admin
        if ($bill->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        return view('admin.bills.show', compact('bill'));
    }

    /**
     * Show the form for editing the specified bill.
     */
    public function edit(Bill $bill)
    {
        // Ensure the bill belongs to the authenticated admin
        if ($bill->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $admin = Auth::guard('admin')->user();
        // Get customers with paid plans
        $customers = Customer::where('admin_id', $admin->id)
            ->whereHas('customerPlans', function ($query) {
                $query->where('status', 'active');
            })
            ->orderBy('name')
            ->get();

        return view('admin.bills.edit', compact('bill', 'customers'));
    }

    /**
     * Update the specified bill in storage.
     */
    public function update(Request $request, Bill $bill)
    {
        // Ensure the bill belongs to the authenticated admin
        if ($bill->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $validatedData = $request->validate([
            'customer_id' => 'required|exists:customers,id,admin_id,'.$bill->admin_id,
            'billing_type' => 'required|in:monthly,yearly',
            'amount' => 'required|numeric|min:0',
            'billing_period_start' => 'required|date',
            'billing_period_end' => 'required|date|after_or_equal:billing_period_start',
            'due_date' => 'required|date|after_or_equal:billing_period_end',
            'status' => 'required|in:pending,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $bill->update($validatedData);

        return redirect()->route('admin.bills.index')->with('success', 'Bill updated successfully!');
    }

    /**
     * Remove the specified bill from storage.
     */
    public function destroy(Bill $bill)
    {
        // Ensure the bill belongs to the authenticated admin
        if ($bill->admin_id !== Auth::guard('admin')->id()) {
            abort(403);
        }

        $bill->delete();

        return redirect()->route('admin.bills.index')->with('success', 'Bill deleted successfully!');
    }

    /**
     * Get customer details for AJAX request
     */
    public function getCustomerDetails($customerId)
    {
        $admin = Auth::guard('admin')->user();
        $customer = Customer::where('id', $customerId)
            ->where('admin_id', $admin->id)
            ->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }

        return response()->json([
            'name' => $customer->name,
            'mobile' => $customer->mobile,
            'whatsapp' => $customer->whatsapp,
            'email' => $customer->email ?? 'N/A',
            'address' => $customer->address2 ? $customer->address2 . ', ' . $customer->city . ', ' . $customer->district . ' - ' . $customer->pincode : 'N/A'
        ]);
    }
}