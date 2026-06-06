@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m-10 12h10a2 2 0 002-2v-5m-14 0h14m-12 5h12"></path></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Add New News Item</h2>
    </div>
    
    {{-- Main Form Container (Wide Layout) --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-8">
        <form action="{{ route('admin.news.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title Field --}}
                <div class="mb-5">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                    <input type="text" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Slug Field --}}
                <div class="mb-5">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">Slug (Optional - auto-generated)</label>
                    <input type="text" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('slug') border-red-500 @enderror" 
                           id="slug" 
                           name="slug" 
                           value="{{ old('slug') }}">
                    @error('slug')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Author Field --}}
                <div class="mb-5">
                    <label for="author" class="block text-sm font-medium text-gray-700 mb-1">Author <span class="text-red-500">*</span></label>
                    <input type="text" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('author') border-red-500 @enderror" 
                           id="author" 
                           name="author" 
                           value="{{ old('author') }}" 
                           required>
                    @error('author')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Posted Date Field --}}
                <div class="mb-5">
                    <label for="posted_date" class="block text-sm font-medium text-gray-700 mb-1">Posted Date <span class="text-red-500">*</span></label>
                    <input type="date" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('posted_date') border-red-500 @enderror" 
                           id="posted_date" 
                           name="posted_date" 
                           value="{{ old('posted_date') }}" 
                           required>
                    @error('posted_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Feature Image Upload Field --}}
            <div class="mb-5">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Feature Image Upload <span class="text-red-500">*</span></label>
                <input class="block w-full text-sm text-gray-500
                             file:mr-4 file:py-2 file:px-4
                             file:rounded-lg file:border-0
                             file:text-sm file:font-semibold
                             file:bg-blue-50 file:text-blue-700
                             hover:file:bg-blue-100 cursor-pointer
                             @error('image') border-red-500 @enderror" 
                       type="file" 
                       id="image" 
                       name="image" 
                       required>
                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">Select a single feature image for the news item.</p>
            </div>
            
            {{-- Summary Field --}}
            <div class="mb-6">
                <label for="summary" class="block text-sm font-medium text-gray-700 mb-1">Summary (Short description)</label>
                <textarea class="form-textarea w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('summary') border-red-500 @enderror" 
                              id="summary" 
                              name="summary" 
                              rows="3">{{ old('summary') }}</textarea>
                @error('summary')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Keywords Field --}}
            <div class="mb-6">
                <label for="keywords" class="block text-sm font-medium text-gray-700 mb-1">Keywords (Comma separated for SEO/Tags)</label>
                <input type="text" 
                       class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('keywords') border-red-500 @enderror" 
                       id="keywords" 
                       name="keywords" 
                       value="{{ old('keywords') }}">
                @error('keywords')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Field --}}
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select class="form-select w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" 
                         id="status" 
                         name="status" 
                         required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save News Item
                </button>
                <a href="{{ route('admin.news.index') }}" class="inline-flex justify-center items-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection