@extends('customer.layout')

@section('title', 'My Profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">My Profile</h2>
        @if($customer->image)
            <div>
                <img src="{{ asset('storage/' . $customer->image) }}" alt="Profile Image" class="rounded-full w-16 h-16 object-cover">
            </div>
        @endif
        <div class="flex space-x-2">
            <a href="{{ route('customer.family.members.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Family Members
            </a>
            <a href="{{ route('customer.edit.profile') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Edit Profile
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Name</label>
                    <p class="text-gray-800">{{ $customer->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Father's Name</label>
                    <p class="text-gray-800">{{ $customer->father_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Gotra</label>
                    <p class="text-gray-800">{{ $customer->gotra ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Label Name</label>
                    <p class="text-gray-800">{{ $customer->label_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Date of Birth</label>
                    <p class="text-gray-800">{{ $customer->date_of_birth ? $customer->date_of_birth->format('d M Y') : 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Anniversary Date</label>
                    <p class="text-gray-800">{{ $customer->anniversary_date ? $customer->anniversary_date->format('d M Y') : 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div>
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Mobile</label>
                    <p class="text-gray-800">{{ $customer->mobile }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">WhatsApp</label>
                    <p class="text-gray-800">{{ $customer->whatsapp ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Address</label>
                    <p class="text-gray-800">
                        {{ $customer->dno ?? '' }}
                        {{ $customer->street_road ?? '' }}
                        {{ $customer->address2 ?? '' }}
                        {{ $customer->city ?? '' }}
                        {{ $customer->district ?? '' }}
                        {{ $customer->pincode ?? '' }}
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Village</label>
                    <p class="text-gray-800">{{ $customer->village->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">MS/Firm Name</label>
                    <p class="text-gray-800">{{ $customer->ms_firm_name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Status</h3>
        <p>
            <span class="px-3 py-1 rounded text-sm {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ ucfirst($customer->status) }}
            </span>
        </p>
    </div>
</div>
@endsection