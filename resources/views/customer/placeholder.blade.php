@extends('customer.layout')

@section('title', $title ?? 'Feature')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ $title ?? 'Feature' }}</h2>
    
    <div class="text-center py-12">
        <div class="text-5xl text-gray-300 mb-4">🚧</div>
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Feature Coming Soon</h3>
        <p class="text-gray-600">
            This feature is currently under development. Please check back later.
        </p>
    </div>
</div>
@endsection