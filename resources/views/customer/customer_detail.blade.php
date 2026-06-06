@extends('customer.layout')

@section('title', 'Customer Details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Customer Details</h2>
        <a href="{{ route('customer.list') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            ← Back to All Customers
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Name</label>
                    <p class="text-gray-800">{{ $targetCustomer->name }}</p>
                </div>
                @if($targetCustomer->father_name)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Father's Name</label>
                    <p class="text-gray-800">{{ $targetCustomer->father_name }}</p>
                </div>
                @endif
                @if($targetCustomer->gotra)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Gotra</label>
                    <p class="text-gray-800">{{ $targetCustomer->gotra }}</p>
                </div>
                @endif
                @if($targetCustomer->label_name)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Label Name</label>
                    <p class="text-gray-800">{{ $targetCustomer->label_name }}</p>
                </div>
                @endif
                @if($targetCustomer->date_of_birth)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Date of Birth</label>
                    <p class="text-gray-800">{{ $targetCustomer->date_of_birth->format('d M Y') }}</p>
                </div>
                @endif
                @if($targetCustomer->anniversary_date)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Anniversary Date</label>
                    <p class="text-gray-800">{{ $targetCustomer->anniversary_date->format('d M Y') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
            <div class="space-y-3">
                @if($targetCustomer->mobile)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Mobile</label>
                    <p class="text-gray-800">{{ $targetCustomer->mobile }}</p>
                </div>
                @endif
                @if($targetCustomer->whatsapp)
                <div>
                    <label class="block text-sm font-medium text-gray-600">WhatsApp</label>
                    <p class="text-gray-800">{{ $targetCustomer->whatsapp }}</p>
                </div>
                @endif
                <div>
                    <label class="block text-sm font-medium text-gray-600">Status</label>
                    <p>
                        <span class="px-3 py-1 rounded text-sm {{ $targetCustomer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($targetCustomer->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Address Information</h3>
            <div class="space-y-3">
                @if($targetCustomer->village)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Village</label>
                    <p class="text-gray-800">{{ $targetCustomer->village->name }}</p>
                </div>
                @endif
                @if($targetCustomer->district)
                <div>
                    <label class="block text-sm font-medium text-gray-600">District</label>
                    <p class="text-gray-800">{{ $targetCustomer->district }}</p>
                </div>
                @endif
                @if($targetCustomer->city)
                <div>
                    <label class="block text-sm font-medium text-gray-600">City</label>
                    <p class="text-gray-800">{{ $targetCustomer->city }}</p>
                </div>
                @endif
                @if($targetCustomer->dno || $targetCustomer->street_road || $targetCustomer->address2)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Address</label>
                    <p class="text-gray-800">
                        {{ $targetCustomer->dno ?? '' }}
                        {{ $targetCustomer->street_road ?? '' }}
                        {{ $targetCustomer->address2 ?? '' }}
                    </p>
                </div>
                @endif
                @if($targetCustomer->pincode)
                <div>
                    <label class="block text-sm font-medium text-gray-600">Pincode</label>
                    <p class="text-gray-800">{{ $targetCustomer->pincode }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Business Information -->
        <div class="bg-gray-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Business Information</h3>
            <div class="space-y-3">
                @if($targetCustomer->ms_firm_name)
                <div>
                    <label class="block text-sm font-medium text-gray-600">MS/Firm Name</label>
                    <p class="text-gray-800">{{ $targetCustomer->ms_firm_name }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection