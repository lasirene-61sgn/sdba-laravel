@extends('customer.layout')

@section('title', 'Villages')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Villages</h2>
    
    @if($villages->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">🏘️</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Villages</h3>
            <p class="text-gray-600">
                There are no villages available at the moment.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($villages as $village)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg text-gray-800">{{ $village->name }}</h3>
                    <div class="mt-2 text-sm text-gray-500">
                        Added on {{ $village->created_at->format('M d, Y') }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection