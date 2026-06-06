@extends('customer.layout')

@section('title', 'Support - ' . $supportItem->name)

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $supportItem->name }}</h2>
        <a href="{{ route('customer.support') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            ← Back to Support
        </a>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Contact Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
            
            @if($supportItem->phone)
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-600">Phone</label>
                    <p class="text-gray-800">{{ $supportItem->phone }}</p>
                </div>
            @endif
            
            @if($supportItem->supportType)
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-600">Support Type</label>
                    <p class="text-gray-800">{{ $supportItem->supportType->name }}</p>
                </div>
            @endif
            
            @if($supportItem->supportCategory)
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-600">Support Category</label>
                    <p class="text-gray-800">{{ $supportItem->supportCategory->name }}</p>
                </div>
            @endif
        </div>
        
        <!-- Image -->
        @if($supportItem->image)
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Image</h3>
                <img src="{{ asset('storage/' . $supportItem->image) }}" alt="{{ $supportItem->name }}" class="w-full h-64 object-contain rounded-lg">
            </div>
        @endif
    </div>
    
    <div class="border-t border-gray-200 pt-4">
        <div class="text-sm text-gray-500">
            Added on {{ $supportItem->created_at->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
</div>
@endsection