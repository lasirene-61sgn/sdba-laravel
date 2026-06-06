@extends('customer.layout')

@section('title', 'Banners')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Banners</h2>
    
    @if($banners->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">📢</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Banners</h3>
            <p class="text-gray-600">
                There are no banners available at the moment.
            </p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($banners as $banner)
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                    @if($banner->image_path)
                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="{{ $banner->name ?? 'Banner' }}" class="w-full h-64 object-cover">
                    @else
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-64 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                    @endif
                    @if($banner->name)
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-800">{{ $banner->name }}</h3>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection