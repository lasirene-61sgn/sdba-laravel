@extends('customer.layout')

@section('title', 'About Us')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">About Us</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Left Column - Image and Description -->
        <div>
            @if($aboutUs->image_path)
                <div class="mb-6">
                    <img src="{{ asset('storage/' . $aboutUs->image_path) }}" alt="About Us" class="w-full h-auto rounded-lg shadow-md">
                </div>
            @endif
            
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Our Story</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $aboutUs->description ?? 'No information available.' }}
                </p>
            </div>
        </div>
        
        <!-- Right Column - Vision and Mission -->
        <div>
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Our Vision</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $aboutUs->vision ?? 'No information available.' }}
                </p>
            </div>
            
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-3">Our Mission</h3>
                <p class="text-gray-600 leading-relaxed">
                    {{ $aboutUs->mission ?? 'No information available.' }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection