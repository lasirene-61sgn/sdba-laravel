@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Edit Gallery Item: {{ $galleryItem->title }}</h2>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-8">
        <form action="{{ route('admin.gallery.update', $galleryItem) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- Title --}}
            <div class="mb-5">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="form-input w-full px-4 py-2 border rounded-lg focus:ring-blue-500 @error('title') border-red-500 @enderror" value="{{ old('title', $galleryItem->title) }}" required>
                @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" id="description" rows="3" class="form-textarea w-full px-4 py-2 border rounded-lg focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $galleryItem->description) }}</textarea>
                @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>
            
            {{-- Image Section --}}
            <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-3">Manage Images</label>
                
                {{-- Upload New --}}
                <input type="file" name="images[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-4">

                {{-- Current Images with Delete Checkbox --}}
                @if ($galleryItem->image_paths)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        @foreach ($galleryItem->image_paths as $imagePath)
                            <div class="relative group border rounded-lg overflow-hidden bg-white">
                                <img src="{{ asset('storage/' . $imagePath) }}" class="w-full h-24 object-cover">
                                <div class="p-2 bg-white flex items-center justify-between">
                                    <label class="inline-flex items-center text-xs text-red-600 cursor-pointer">
                                        <input type="checkbox" name="remove_images[]" value="{{ $imagePath }}" class="mr-1 rounded text-red-600 focus:ring-red-500">
                                        Remove
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Video Section --}}
            <div class="mb-8 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <label class="block text-sm font-bold text-gray-700 mb-3">Manage Videos</label>
                
                {{-- Upload New --}}
                <input type="file" name="videos[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 mb-4">

                {{-- Current Videos with Delete Checkbox --}}
                @if ($galleryItem->video_paths)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($galleryItem->video_paths as $videoPath)
                            <div class="flex items-center p-2 border rounded-lg bg-white">
                                <video class="w-20 h-12 bg-black rounded" muted>
                                    <source src="{{ asset('storage/' . $videoPath) }}">
                                </video>
                                <div class="ml-3">
                                    <label class="inline-flex items-center text-xs text-red-600 cursor-pointer">
                                        <input type="checkbox" name="remove_videos[]" value="{{ $videoPath }}" class="mr-1 rounded text-red-600 focus:ring-red-500">
                                        Remove Video
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Status --}}
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-select w-full px-4 py-2 border rounded-lg focus:ring-blue-500" required>
                    <option value="active" {{ old('status', $galleryItem->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $galleryItem->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center items-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Update Gallery Item
                </button>
                <a href="{{ route('admin.gallery.index') }}" class="inline-flex justify-center items-center py-2 px-4 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection