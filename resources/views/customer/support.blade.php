@extends('customer.layout')

@section('title', 'Support')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Support</h2>
    
    @if($supports->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">❓</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Support Items</h3>
            <p class="text-gray-600">
                There are no support items available at the moment.
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($supports as $support)
                <a href="{{ route('customer.support.show', $support->id) }}" class="block">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-lg text-gray-800">{{ $support->name }}</h3>
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                {{ $support->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        @if($support->phone)
                            <p class="text-gray-600 mt-2">Phone: {{ $support->phone }}</p>
                        @endif
                        <div class="mt-3 text-sm text-gray-500">
                            Added on {{ $support->created_at->format('M d, Y') }}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection