@extends('admin.layout.app')

@section('content')
<div class="container mx-auto p-6 lg:p-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">✍️ Edit Plan for: {{ $customerPlan->customer->name ?? 'N/A' }}</h2>
    
    <form action="{{ route('admin.customer-plan.update', $customerPlan) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- CUSTOMER DROPDOWN (Readonly for Edit) --}}
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer Name</label>
                <select class="w-full px-4 py-2 border rounded-lg shadow-sm bg-gray-100 cursor-not-allowed" 
                        id="customer_id" 
                        name="customer_id" 
                        disabled>
                    {{-- Only the current customer is in the $customers array --}}
                    @foreach($customers as $id => $name)
                        <option value="{{ $id }}" selected>{{ $name }}</option>
                    @endforeach
                </select>
                {{-- Hidden field to pass the actual ID to the controller --}}
                <input type="hidden" name="customer_id" value="{{ $customerPlan->customer_id }}">
            </div>
            
            {{-- PLAN TYPE (Monthly/Yearly) --}}
            <div>
                <label for="plan_type" class="block text-sm font-medium text-gray-700 mb-1">Plan Type</label>
                <select class="w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('plan_type') border-red-500 @enderror" 
                        id="plan_type" 
                        name="plan_type" 
                        required>
                    <option value="monthly" {{ old('plan_type', $customerPlan->plan_type) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ old('plan_type', $customerPlan->plan_type) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
                @error('plan_type')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
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
                       value="{{ old('start_date', $customerPlan->start_date ? $customerPlan->start_date->toDateString() : '') }}" 
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
                    <option value="active" {{ old('status', $customerPlan->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $customerPlan->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="pending" {{ old('status', $customerPlan->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
                @error('status')<div class="mt-2 text-sm text-red-600">{{ $message }}</div>@enderror
            </div>
        </div>
        
        {{-- Next Due Date Information --}}
        <p class="mt-3 text-sm text-gray-700 p-4 border border-blue-200 bg-blue-50 rounded-lg mb-6">
            <span class="font-semibold">Next Due Date:</span> {{ $customerPlan->next_due_date ? $customerPlan->next_due_date->format('Y-m-d') : 'Calculated on save' }}
        </p>
        
        {{-- Submission Buttons --}}
        <div class="flex space-x-3 pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                Update Plan
            </button>
            <a href="{{ route('admin.customer-plan.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection