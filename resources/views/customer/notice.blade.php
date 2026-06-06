@extends('customer.layout')

@section('title', 'Notices')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Notices</h2>
    
    @if($notices->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">📝</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Notices</h3>
            <p class="text-gray-600">
                There are no notices available at the moment.
            </p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($notices as $notice)
                <a href="{{ route('customer.notice.show', $notice->id) }}" class="block">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <h3 class="font-semibold text-lg text-gray-800">{{ $notice->name }}</h3>
                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                {{ $notice->created_at->format('M d, Y') }}
                            </span>
                        </div>
                        @if($notice->description)
                            <p class="text-gray-600 mt-2">{{ Str::limit($notice->description, 100) }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection