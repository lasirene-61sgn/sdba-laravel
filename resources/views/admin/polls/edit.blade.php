@extends('admin.layout.app')

@section('title', 'Edit Poll')

@section('content')
<div class="page-header">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Poll</h1>
        <p class="text-gray-600 mt-1">Modify your poll details</p>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Poll Details</h2>
    </div>
    
    <form action="{{ route('admin.polls.update', $poll) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Poll Question</label>
            <textarea 
                id="description" 
                name="description" 
                rows="4" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                placeholder="Enter your poll question here...">{{ old('description', $poll->description) }}</textarea>
            @error('description')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="mb-6">
            <div class="flex items-center">
                <input 
                    id="active" 
                    name="active" 
                    type="checkbox" 
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    {{ old('active', $poll->active) ? 'checked' : '' }}>
                <label for="active" class="ml-2 block text-sm text-gray-900">
                    Active
                </label>
            </div>
            <p class="mt-1 text-sm text-gray-500">Only active polls will be visible to customers.</p>
        </div>
        
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.polls.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                ← Back to Polls
            </a>
            <div class="flex space-x-3">
                <button type="reset" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Reset
                </button>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Poll
                </button>
            </div>
        </div>
    </form>
</div>
@endsection