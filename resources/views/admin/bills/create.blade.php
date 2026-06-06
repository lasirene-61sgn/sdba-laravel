@extends('admin.layout.app')

@section('title', 'Create Bill')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Create New Bill</h2>
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

    <form method="POST" action="{{ route('admin.bills.store') }}" id="billForm">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <label for="customer_id" class="block text-gray-700 text-sm font-bold mb-2">Customer *</label>
                    <select name="customer_id" id="customer_id" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select a Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->name }} ({{ $customer->mobile }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Details Section (Hidden by default) -->
                <div id="customerDetails" class="mb-4 bg-gray-50 p-4 rounded-lg border border-gray-200 hidden">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Customer Details</h3>
                    <div id="customerInfo">
                        <!-- Customer details will be loaded here via AJAX -->
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Billing Type *</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="billing_type" value="monthly" {{ old('billing_type') == 'monthly' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Monthly</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="billing_type" value="yearly" {{ old('billing_type') == 'yearly' ? 'checked' : '' }} class="form-radio h-4 w-4 text-blue-600">
                            <span class="ml-2">Yearly</span>
                        </label>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Amount (₹) *</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div>
                <div class="mb-4">
                    <label for="billing_period_start" class="block text-gray-700 text-sm font-bold mb-2">Billing Period Start *</label>
                    <input type="date" name="billing_period_start" id="billing_period_start" value="{{ old('billing_period_start') }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="billing_period_end" class="block text-gray-700 text-sm font-bold mb-2">Billing Period End *</label>
                    <input type="date" name="billing_period_end" id="billing_period_end" value="{{ old('billing_period_end') }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="due_date" class="block text-gray-700 text-sm font-bold mb-2">Due Date *</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('admin.bills.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Bill
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerSelect = document.getElementById('customer_id');
    const customerDetails = document.getElementById('customerDetails');
    const customerInfo = document.getElementById('customerInfo');
    
    customerSelect.addEventListener('change', function() {
        const customerId = this.value;
        
        if (customerId) {
            // Show loading state
            customerInfo.innerHTML = '<p>Loading customer details...</p>';
            customerDetails.classList.remove('hidden');
            
            // Fetch customer details via AJAX
            fetch(`/admin/bills/customer/${customerId}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        customerInfo.innerHTML = '<p class="text-red-600">Error loading customer details</p>';
                    } else {
                        customerInfo.innerHTML = `
                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm text-gray-600">Name</p>
                                    <p class="font-medium">${data.name}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Mobile</p>
                                    <p class="font-medium">${data.mobile || 'N/A'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">WhatsApp</p>
                                    <p class="font-medium">${data.whatsapp || 'N/A'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Email</p>
                                    <p class="font-medium">${data.email || 'N/A'}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Address</p>
                                    <p class="font-medium">${data.address || 'N/A'}</p>
                                </div>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    customerInfo.innerHTML = '<p class="text-red-600">Error loading customer details</p>';
                });
        } else {
            customerDetails.classList.add('hidden');
        }
    });
    
    // Trigger change event if there's a previously selected customer (for validation errors)
    if (customerSelect.value) {
        customerSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection