@extends('admin.layout.app')

@section('title', 'Bills')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Bills</h2>
        <a href="{{ route('admin.bills.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Create New Bill
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($bills->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 border-b text-left">Bill Number</th>
                        <th class="py-2 px-4 border-b text-left">Customer</th>
                        <th class="py-2 px-4 border-b text-left">Type</th>
                        <th class="py-2 px-4 border-b text-left">Amount</th>
                        <th class="py-2 px-4 border-b text-left">Period</th>
                        <th class="py-2 px-4 border-b text-left">Due Date</th>
                        <th class="py-2 px-4 border-b text-left">Status</th>
                        <th class="py-2 px-4 border-b text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bills as $bill)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b">{{ $bill->bill_number }}</td>
                            <td class="py-2 px-4 border-b">
                                {{ $bill->customer->name }}
                                <div class="text-sm text-gray-500">{{ $bill->customer->mobile }}</div>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $bill->billing_type === 'monthly' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($bill->billing_type) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b">₹{{ number_format($bill->amount, 2) }}</td>
                            <td class="py-2 px-4 border-b">
                                {{ $bill->billing_period_start->format('d M Y') }} - {{ $bill->billing_period_end->format('d M Y') }}
                            </td>
                            <td class="py-2 px-4 border-b {{ $bill->due_date < now() && $bill->status !== 'paid' ? 'text-red-600 font-bold' : '' }}">
                                {{ $bill->due_date->format('d M Y') }}
                            </td>
                            <td class="py-2 px-4 border-b">
                                <span class="px-2 py-1 rounded text-xs font-medium 
                                    @if($bill->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($bill->status === 'paid') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ ucfirst($bill->status) }}
                                </span>
                            </td>
                            <td class="py-2 px-4 border-b">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.bills.show', $bill) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.bills.show', $bill) }}?receipt=true" class="text-green-600 hover:text-green-900" title="View Receipt">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.bills.edit', $bill) }}" class="text-green-600 hover:text-green-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.bills.destroy', $bill) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this bill?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">📋</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Bills Found</h3>
            <p class="text-gray-600 mb-4">
                You haven't created any bills yet.
            </p>
            <a href="{{ route('admin.bills.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Create Your First Bill
            </a>
        </div>
    @endif
</div>
@endsection