@extends('admin.layout.app')

@section('content')
{{-- 
Note: The styling for this file is handled by the Tailwind CSS classes 
applied directly to the elements (e.g., text-3xl, bg-white, grid-cols-2).
No custom CSS is required in this section, assuming Tailwind is linked 
in the base layout ('admin.layout.app'). 
--}}

<div class="container mx-auto p-6 lg:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">➕ Assign Customer Plan</h2>
    
    <form action="{{ route('admin.customer-plan.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            {{-- CUSTOMER DROPDOWN (Filtered by Admin) --}}
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                <select class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('customer_id') border-red-500 @enderror" 
                        id="customer_id" 
                        name="customer_id" 
                        required>
                    <option value="">Select Customer...</option>
                    @foreach($customers as $id => $name)
                        <option value="{{ $id }}" {{ old('customer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @error('customer_id')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
            
            {{-- PLAN TYPE (Monthly/Yearly) --}}
            <div>
                <label for="plan_type" class="block text-sm font-medium text-gray-700 mb-1">Plan Type</label>
                <select class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('plan_type') border-red-500 @enderror" 
                        id="plan_type" 
                        name="plan_type" 
                        required>
                    <option value="monthly" {{ old('plan_type') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ old('plan_type') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
                @error('plan_type')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
        </div>
        
        {{-- CUSTOMER DETAILS FETCHED VIA AJAX --}}
        <div id="customer_details_card" class="bg-white shadow-md rounded-lg border border-gray-200 mb-6" style="display:none;">
            <div class="px-6 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg font-semibold text-gray-800">Customer Details</div>
            <div class="p-6 text-sm text-gray-700">
                <p class="mb-2"><strong>Father Name:</strong> <span id="detail_father_name"></span></p>
                <p class="mb-2"><strong>Mobile:</strong> <span id="detail_mobile"></span></p>
                <p class="mb-2"><strong>Village:</strong> <span id="detail_village"></span></p>
                <p><strong>Address:</strong> <span id="detail_address"></span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- START DATE --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" 
                       class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('start_date') border-red-500 @enderror" 
                       id="start_date" 
                       name="start_date" 
                       value="{{ old('start_date', now()->toDateString()) }}" 
                       required>
                @error('start_date')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>

            {{-- STATUS --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Plan Status</label>
                <select class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror" 
                        id="status" 
                        name="status" 
                        required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                @error('status')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
        </div>
        
        {{-- Submission Buttons --}}
        <div class="flex space-x-3 pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                Save Plan
            </button>
            <a href="{{ route('admin.customer-plan.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                Cancel
            </a>
        </div>
    </form>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customerSelect = document.getElementById('customer_id');
        const detailsCard = document.getElementById('customer_details_card');

        customerSelect.addEventListener('change', function () {
            const customerId = this.value;

            if (customerId) {
                // Ensure the route name and structure matches your Laravel setup
                fetch(`{{ route('admin.customer-plans.get-customer-details', ['customerId' => 'CUSTOMER_ID']) }}`.replace('CUSTOMER_ID', customerId))
                    .then(response => {
                        if (!response.ok) {
                            // If the response is not 200 OK
                            throw new Error('Customer not found or access denied (Status: ' + response.status + ')');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Populate details
                        document.getElementById('detail_father_name').textContent = data.father_name || 'N/A';
                        document.getElementById('detail_mobile').textContent = data.mobile || 'N/A';
                        document.getElementById('detail_village').textContent = data.village_name || 'N/A';
                        
                        // Clean up the address string (removing leading/trailing commas and double spaces)
                        const address = data.address ? data.address.trim().replace(/^,+|,+$/g, '').replace(/, ,/g, ', ') : 'N/A';
                        document.getElementById('detail_address').textContent = address;
                        
                        detailsCard.style.display = 'block';
                    })
                    .catch(error => {
                        console.error('Error fetching details:', error);
                        detailsCard.style.display = 'none';
                        // Use a more Tailwind-like way to display dynamic messages if needed, 
                        // but for simplicity, keeping the alert.
                        alert('Could not fetch customer details. Check console for details.');
                    });
            } else {
                detailsCard.style.display = 'none';
            }
        });
        
        // Trigger change on load if an old customer_id value exists
        if (customerSelect.value) {
            customerSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
@endsection