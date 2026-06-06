@extends('customer.layout')

@section('title', 'Notice - ' . $noticeItem->name)

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">{{ $noticeItem->name }}</h2>
        <a href="{{ route('customer.notice') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            ← Back to Notices
        </a>
    </div>
    
    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
        <div class="prose max-w-none">
            <p class="text-gray-700 whitespace-pre-line">{{ $noticeItem->description }}</p>
        </div>
    </div>
    
    <div class="border-t border-gray-200 pt-4">
        <div class="text-sm text-gray-500">
            Published on {{ $noticeItem->created_at->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
</div>
@endsection