@extends('customer.layout')

@section('title', 'All Customers')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">All Customers</h2>
        <div class="text-sm text-gray-600">
            Showing {{ $customers->count() }} of {{ $customers->total() }} customer(s)
        </div>
    </div>
    
    {{-- Debug information - uncomment to troubleshoot --}}
    <div class="mb-4 p-3 bg-blue-100 rounded hidden">
        <p><strong>Current Customer ID:</strong> {{ Auth::guard('customer')->id() }}</p>
        <p><strong>Current Customer Admin ID:</strong> {{ Auth::guard('customer')->user()->admin_id ?? 'N/A' }}</p>
        <p><strong>Total Customers Available:</strong> {{ $customers->total() }}</p>
    </div>

    @if($customers->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4 text-left border-b">Customer Details</th>
                        <th class="py-3 px-4 text-left border-b">Contact Info</th>
                        <th class="py-3 px-4 text-left border-b">Address</th>
                        <th class="py-3 px-4 text-left border-b">Business Info</th>
                        <th class="py-3 px-4 text-left border-b">Status</th>
                        <th class="py-3 px-4 text-left border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border-b">
                                <div class="font-semibold text-gray-800">{{ $customer->name }}</div>
                                @if($customer->father_name)
                                    <div class="text-sm text-gray-600">{{ $customer->father_name }} (S/o)</div>
                                @endif
                                @if($customer->gotra)
                                    <div class="text-sm text-gray-600">Gotra: {{ $customer->gotra }}</div>
                                @endif
                                @if($customer->label_name)
                                    <div class="text-sm text-gray-600">Label: {{ $customer->label_name }}</div>
                                @endif
                                @if($customer->date_of_birth)
                                    <div class="text-sm text-gray-600">DOB: {{ $customer->date_of_birth->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span>{{ $customer->mobile ?? 'N/A' }}</span>
                                </div>
                                @if($customer->whatsapp)
                                    <div class="flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                        <span>{{ $customer->whatsapp }}</span>
                                    </div>
                                @endif
                                @if($customer->anniversary_date)
                                    <div class="text-sm text-gray-600 mt-1">Anniversary: {{ $customer->anniversary_date->format('d M Y') }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b">
                                @if($customer->village || $customer->district || $customer->city || $customer->pincode)
                                    @if($customer->village)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            <span>{{ $customer->village->name }}</span>
                                        </div>
                                    @endif
                                    <div class="text-sm text-gray-600">
                                        @if($customer->dno)
                                            {{ $customer->dno }},
                                        @endif
                                        @if($customer->street_road)
                                            {{ $customer->street_road }},
                                        @endif
                                        @if($customer->address2)
                                            {{ $customer->address2 }},
                                        @endif
                                        @if($customer->city)
                                            {{ $customer->city }},
                                        @endif
                                        @if($customer->district)
                                            {{ $customer->district }},
                                        @endif
                                        @if($customer->pincode)
                                            {{ $customer->pincode }}
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">No address provided</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b">
                                @if($customer->ms_firm_name)
                                    <div class="font-medium text-gray-800">{{ $customer->ms_firm_name }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4 border-b">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($customer->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 border-b">
                                <a href="{{ route('customer.show', $customer->id) }}" class="text-blue-600 hover:text-blue-900 font-medium">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        <div class="mt-6">
            {{ $customers->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="mt-2 text-lg font-medium text-gray-900">No customers found</h3>
            <p class="mt-1 text-gray-500">There are no other customers registered under your admin.</p>
        </div>
    @endif
</div>

<script>
    // Toggle debug information
    document.addEventListener('DOMContentLoaded', function() {
        const debugToggle = document.createElement('button');
        debugToggle.textContent = 'Toggle Debug Info';
        debugToggle.className = 'mb-4 px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 text-sm';
        debugToggle.onclick = function() {
            const debugInfo = document.querySelector('.bg-blue-100');
            debugInfo.classList.toggle('hidden');
        };
        
        const container = document.querySelector('.bg-white.rounded-lg');
        container.insertBefore(debugToggle, container.firstChild);
    });
</script>
@endsection