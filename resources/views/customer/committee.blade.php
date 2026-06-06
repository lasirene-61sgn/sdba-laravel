@extends('customer.layout')

@section('title', 'Committee')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Committee Members</h2>
    
    @if($committeeMembers->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">👥</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Committee Members</h3>
            <p class="text-gray-600">
                There are no committee members available at the moment.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($committeeMembers as $member)
                <div class="bg-gray-50 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow text-center">
                    @if($member->image_path)
                        <img src="{{ asset('storage/' . $member->image_path) }}" alt="{{ $member->name }}" class="w-full h-48 object-cover">
                    @else
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-48 flex items-center justify-center">
                            <span class="text-gray-500">No Image</span>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-800">{{ $member->name }}</h3>
                        <p class="text-gray-600 mt-1">{{ $member->post_name }}</p>
                        <p class="text-gray-500 text-sm mt-2">{{ $member->phone }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection