@extends('admin.layout.app')

@section('title', 'Bill Details - ' . $bill->bill_number)

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    @if(request()->has('receipt'))
        <!-- Receipt View -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Payment Receipt</h2>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Print Receipt
            </button>
        </div>
        
        <div class="border-2 border-gray-300 rounded-lg p-6 mb-6">
            <div class="text-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">PAYMENT RECEIPT</h1>
                <p class="text-gray-600 mt-2">Official Receipt for Payment Received</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Receipt Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Receipt Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Receipt Number:</span>
                            <span class="font-medium">{{ $bill->bill_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Receipt Date:</span>
                            <span class="font-medium">{{ $bill->created_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Billing Period:</span>
                            <span class="font-medium">{{ $bill->billing_period_start->format('d M Y') }} - {{ $bill->billing_period_end->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Due Date:</span>
                            <span class="font-medium">{{ $bill->due_date->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Customer Details</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Customer Name:</span>
                            <span class="font-medium">{{ $bill->customer->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Mobile:</span>
                            <span class="font-medium">{{ $bill->customer->mobile ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Plan Type:</span>
                            <span class="font-medium">
                                @if($bill->customer->activeCustomerPlan)
                                    {{ ucfirst($bill->customer->activeCustomerPlan->plan_type) }}
                                @else
                                    No Active Plan
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Plan Status:</span>
                            <span class="font-medium">
                                @if($bill->customer->activeCustomerPlan)
                                    <span class="px-2 py-1 rounded text-xs font-medium 
                                        @if($bill->customer->activeCustomerPlan->status === 'active') bg-green-100 text-green-800
                                        @elseif($bill->customer->activeCustomerPlan->status === 'inactive') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($bill->customer->activeCustomerPlan->status) }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-300 pt-4">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-gray-600">Payment Amount:</span>
                    </div>
                    <div class="text-2xl font-bold text-green-600">₹{{ number_format($bill->amount, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Thank you for your payment. This is an official receipt.</p>
                <p class="mt-2">If you have any questions, please contact our support team.</p>
            </div>
        </div>
        
        <div class="text-center">
            <a href="{{ route('admin.bills.show', $bill) }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Bill Details
            </a>
        </div>
    @else
        <!-- Regular Bill Details View -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Bill Details</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.bills.show', $bill) }}?receipt=true" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    View Receipt
                </a>
                <a href="{{ route('admin.bills.edit', $bill) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Edit Bill
                </a>
                <a href="{{ route('admin.bills.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    ← Back to Bills
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Bill Information -->
            <div class="md:col-span-2 bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Bill Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Bill Number</p>
                        <p class="font-medium">{{ $bill->bill_number }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Billing Type</p>
                        <p class="font-medium">
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $bill->billing_type === 'monthly' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($bill->billing_type) }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Amount</p>
                        <p class="font-medium text-lg">₹{{ number_format($bill->amount, 2) }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p class="font-medium">
                            <span class="px-2 py-1 rounded text-xs font-medium 
                                @if($bill->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($bill->status === 'paid') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Billing Period</p>
                        <p class="font-medium">
                            {{ $bill->billing_period_start->format('d M Y') }} - {{ $bill->billing_period_end->format('d M Y') }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Due Date</p>
                        <p class="font-medium {{ $bill->due_date < now() && $bill->status !== 'paid' ? 'text-red-600 font-bold' : '' }}">
                            {{ $bill->due_date->format('d M Y') }}
                        </p>
                    </div>
                </div>
                
                @if($bill->notes)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600">Notes</p>
                        <p class="font-medium">{{ $bill->notes }}</p>
                    </div>
                @endif
            </div>
            
            <!-- Customer Information -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h3>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">{{ $bill->customer->name }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Mobile</p>
                        <p class="font-medium">{{ $bill->customer->mobile ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">WhatsApp</p>
                        <p class="font-medium">{{ $bill->customer->whatsapp ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $bill->customer->email ?? 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-600">Address</p>
                        <p class="font-medium">
                            @if($bill->customer->address2)
                                {{ $bill->customer->address2 }}, {{ $bill->customer->city }}, {{ $bill->customer->district }} - {{ $bill->customer->pincode }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    
                    <!-- Customer Plan Information -->
                    <div class="pt-3 border-t border-gray-300">
                        <p class="text-sm text-gray-600">Current Plan</p>
                        <p class="font-medium">
                            @if($bill->customer->activeCustomerPlan)
                                <span class="px-2 py-1 rounded text-xs font-medium 
                                    @if($bill->customer->activeCustomerPlan->plan_type === 'monthly') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($bill->customer->activeCustomerPlan->plan_type) }}
                                </span>
                                <span class="px-2 py-1 rounded text-xs font-medium 
                                    @if($bill->customer->activeCustomerPlan->status === 'active') bg-green-100 text-green-800
                                    @elseif($bill->customer->activeCustomerPlan->status === 'inactive') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($bill->customer->activeCustomerPlan->status) }}
                                </span>
                            @else
                                <span class="text-gray-500">No Active Plan</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 pt-4">
            <div class="text-sm text-gray-500">
                Created on {{ $bill->created_at->format('F d, Y \a\t h:i A') }}
                @if($bill->updated_at != $bill->created_at)
                    | Last updated on {{ $bill->updated_at->format('F d, Y \a\t h:i A') }}
                @endif
            </div>
        </div>
    @endif
</div>
@endsection