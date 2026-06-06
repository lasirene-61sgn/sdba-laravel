@extends('customer.layout')

@section('title', 'My Plans')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">My Plans</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($plans->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="py-2 px-4 text-left border-b">Plan Type</th>
                        <th class="py-2 px-4 text-left border-b">Start Date</th>
                        <th class="py-2 px-4 text-left border-b">Next Due</th>
                        <th class="py-2 px-4 text-left border-b">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                        <tr>
                            <td class="py-2 px-4 border-b">{{ ucfirst($plan->plan_type) }}</td>
                            <td class="py-2 px-4 border-b">{{ $plan->start_date->format('d M Y') }}</td>
                            <td class="py-2 px-4 border-b">{{ $plan->next_due_date->format('d M Y') }}</td>
                            <td class="py-2 px-4 border-b">
                                <span class="px-2 py-1 rounded text-xs {{ $plan->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-600 mb-4">You don't have any plans yet.</p>
            <p class="text-gray-500 text-sm">Contact your administrator to assign a plan.</p>
        </div>
    @endif
</div>
@endsection