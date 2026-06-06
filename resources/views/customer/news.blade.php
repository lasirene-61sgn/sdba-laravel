@extends('customer.layout')

@section('title', 'News')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">News</h2>
    
    @if($newsItems->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">📰</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No News</h3>
            <p class="text-gray-600">
                There are no news items available at the moment.
            </p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($newsItems as $news)
                <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                    @if($news->image_path)
                        <img src="{{ asset('storage/' . $news->image_path) }}" alt="{{ $news->title }}" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $news->title }}</h3>
                        @if($news->summary)
                            <p class="text-gray-600 mt-2">{{ $news->summary }}</p>
                        @endif
                        <div class="mt-3 flex flex-wrap items-center justify-between">
                            <div class="text-sm text-gray-500">
                                By {{ $news->author }} • {{ $news->posted_date->format('M d, Y') }}
                            </div>
                            @if($news->keywords)
                                <div class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded mt-2 md:mt-0">
                                    {{ $news->keywords }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection