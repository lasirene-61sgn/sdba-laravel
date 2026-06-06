@extends('customer.layout')

@section('title', 'Gallery - ' . $galleryItem->title)

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $galleryItem->title }}</h2>
        <a href="{{ route('customer.gallery') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            ← Back to Gallery
        </a>
    </div>
    
    @if($galleryItem->description)
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <p class="text-gray-700">{{ $galleryItem->description }}</p>
        </div>
    @endif
    
    @if($galleryItem->image_paths && !empty($galleryItem->image_paths))
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Images ({{ count($galleryItem->image_paths) }})</h3>
            
            <!-- Display all images in a grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($galleryItem->image_paths as $index => $imagePath)
                    <div class="rounded-lg overflow-hidden shadow-md">
                        <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $galleryItem->title }} - Image {{ $index + 1 }}" class="w-full h-64 object-cover">
                        <div class="p-3 bg-gray-50 text-center text-sm text-gray-600">
                            Image {{ $index + 1 }} of {{ count($galleryItem->image_paths) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">🖼️</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Images</h3>
            <p class="text-gray-600">
                This gallery item doesn't have any images.
            </p>
        </div>
    @endif
    
    <div class="border-t border-gray-200 pt-4">
        <div class="text-sm text-gray-500">
            Added on {{ $galleryItem->created_at->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
</div>
@endsection