@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16m0 0l-3-3m3 3l3-3m-3-13h6a2 2 0 012 2v10a2 2 0 01-2 2H9a2 2 0 01-2-2V3a2 2 0 012-2z" /></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Customer Plans List</h2>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        
        {{-- Controls Header --}}
        <div class="p-4 md:p-5 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('admin.customer-plan.create') }}" 
               class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Assign New Plan</span>
            </a>
        </div>
        
        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 hidden md:table-header-group">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Customer Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Plan Type</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Start Date</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Next Due Date</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($plans as $plan)
                    
                    {{-- Mobile View: Stacked Card --}}
                    <tr class="md:hidden block border-b border-gray-200 last:border-b-0">
                        <td class="p-4 block">
                            <div class="flex flex-col space-y-2">
                                <div class="flex justify-between items-start">
                                    <strong class="text-lg font-semibold text-gray-800">{{ $plan->customer->name ?? 'Customer Deleted' }} (#{{ $plan->id }})</strong>
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $plan->status == 'active' ? 'bg-green-100 text-green-800' : ($plan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">Mobile: {{ $plan->customer->mobile ?? 'N/A' }}</p>

                                <div class="grid grid-cols-2 gap-y-1 text-sm pt-2 border-t border-gray-100">
                                    <div><span class="font-medium text-gray-700">Plan:</span> {{ ucfirst($plan->plan_type) }}</div>
                                    <div><span class="font-medium text-gray-700">Start:</span> {{ $plan->start_date?->format('Y-m-d') }}</div>
                                    <div class="col-span-2">
                                        <span class="font-medium text-gray-700">Due Date:</span> 
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full 
                                            {{ $plan->next_due_date?->isPast() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $plan->next_due_date?->format('Y-m-d') ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                
                                {{-- Actions on Mobile --}}
                                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end space-x-3">
                                    <a href="{{ route('admin.customer-plan.edit', $plan) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                    <form action="{{ route('admin.customer-plan.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- Desktop View: Table Row --}}
                    <tr class="hover:bg-gray-50 hidden md:table-row">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $plan->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <strong class="text-gray-800 block">{{ $plan->customer->name ?? 'Customer Deleted' }}</strong>
                            <small class="text-xs text-gray-500">Mobile: {{ $plan->customer->mobile ?? 'N/A' }}</small>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ ucfirst($plan->plan_type) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">{{ $plan->start_date?->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $plan->next_due_date?->isPast() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $plan->next_due_date?->format('Y-m-d') ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $plan->status == 'active' ? 'bg-green-100 text-green-800' : ($plan->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex space-x-2 justify-center">
                                <a href="{{ route('admin.customer-plan.edit', $plan) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <form action="{{ route('admin.customer-plan.destroy', $plan) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1l2-2m-1 7h14a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Customer Plans Found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by assigning a plan to a customer.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.customer-plan.create') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Assign New Plan
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection