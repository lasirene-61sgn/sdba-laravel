@extends('customer.layout')

@section('title', 'Gallery')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Gallery</h2>
    
    @if($galleryItems->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">🖼️</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Gallery Items</h3>
            <p class="text-gray-600">
                There are no gallery items available at the moment.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($galleryItems as $item)
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    <a href="{{ route('customer.gallery.show', $item->id) }}">
                        @if($item->image_paths && !empty($item->image_paths))
                            <!-- Show first image as the main image -->
                            <div class="relative">
                                <img src="{{ asset('storage/' . $item->image_paths[0]) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                                <!-- Show indicator if there are more images -->
                                @if(count($item->image_paths) > 1)
                                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                        +{{ count($item->image_paths) - 1 }} more
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-48 flex items-center justify-center">
                                <span class="text-gray-500">No Image</span>
                            </div>
                        @endif
                    </a>
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $item->title }}</h3>
                        @if($item->description)
                            <p class="text-gray-600 mt-2 text-sm">{{ Str::limit($item->description, 100) }}</p>
                        @endif
                        <div class="mt-3 text-xs text-gray-500">
                            Added on {{ $item->created_at->format('M d, Y') }}
                        </div>
                        <!-- Show total image count -->
                        @if($item->image_paths && !empty($item->image_paths))
                            <div class="mt-2 text-xs text-gray-600">
                                {{ count($item->image_paths) }} image(s)
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection