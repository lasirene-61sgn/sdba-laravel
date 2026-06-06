@extends('admin.layout.app')

@section('title', 'Edit Bill')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Bill</h2>
        <a href="{{ route('admin.bills.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            ← Back to Bills
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bills.update', $bill) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <label for="customer_id" class="block text-gray-700 text-sm font-bold mb-2">Customer *</label>
                    <select name="customer_id" id="customer_id" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select a Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ (old('customer_id', $bill->customer_id) == $customer->id) ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->mobile }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Details Section (Hidden by default) -->
                <div id="customerDetails" class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Customer Details</h3>
                    <div class="space-y-2">
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
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Billing Type *</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="billing_type" value="monthly" {{ (old('billing_type', $bill->billing_type) == 'monthly') ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Monthly</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="billing_type" value="yearly" {{ (old('billing_type', $bill->billing_type) == 'yearly') ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Yearly</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Amount (₹) *</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', $bill->amount) }}" step="0.01" min="0" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div>
                <div class="mb-4">
                    <label for="billing_period_start" class="block text-gray-700 text-sm font-bold mb-2">Billing Period Start *</label>
                    <input type="date" name="billing_period_start" id="billing_period_start" value="{{ old('billing_period_start', $bill->billing_period_start->format('Y-m-d')) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="billing_period_end" class="block text-gray-700 text-sm font-bold mb-2">Billing Period End *</label>
                    <input type="date" name="billing_period_end" id="billing_period_end" value="{{ old('billing_period_end', $bill->billing_period_end->format('Y-m-d')) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="due_date" class="block text-gray-700 text-sm font-bold mb-2">Due Date *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', $bill->due_date->format('Y-m-d')) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status *</label>
                    <select name="status" id="status" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="pending" {{ (old('status', $bill->status) == 'pending') ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ (old('status', $bill->status) == 'paid') ? 'selected' : '' }}>Paid</option>
                        <option value="overdue" {{ (old('status', $bill->status) == 'overdue') ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes', $bill->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('admin.bills.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Bill
            </button>
        </div>
    </form>
</div>
@endsection