@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.586a2 2 0 012.828 0L20.5 5.5V9m-8-8v8m0 0h8"></path></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Edit Committee Member: {{ $committee->name }}</h2>
    </div>
    
    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
            <strong class="font-bold">Validation Errors:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main Form Container (Wide Layout) --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-8">
        <form action="{{ route('admin.committee.update', $committee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') 
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- NAME FIELD --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $committee->name) }}" 
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-5">
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State <span class="text-red-500"></span></label>
                    <input type="text" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('state') border-red-500 @enderror" 
                           id="state" 
                           name="state" 
                           value="{{ old('state', $committee->state) }}" 
                           >
                    @error('state')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- PHONE FIELD --}}
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="text" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $committee->phone) }}" 
                           required>
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            {{-- PASSWORD FIELDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password (Optional)</label>
                    <input type="password" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" 
                           id="password" 
                           name="password">
                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep the current password</p>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input type="password" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                           id="password_confirmation" 
                           name="password_confirmation">
                </div>
            </div>
            
            {{-- POST NAME / ROLE FIELD --}}
            <div class="mb-5">
                <label for="post_name" class="block text-sm font-medium text-gray-700 mb-1">Committee Role/Designation <span class="text-red-500"></span></label>
                <input type="text" 
                       class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('post_name') border-red-500 @enderror" 
                       id="post_name" 
                       name="post_name" 
                       value="{{ old('post_name', $committee->post_name) }}" 
                       >
                @error('post_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- SORT ORDER FIELD --}}
            <div class="mb-5">
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Display Order <span class="text-red-500"></span></label>
                <input type="number" 
                       class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('sort_order') border-red-500 @enderror" 
                       id="sort_order" 
                       name="sort_order" 
                       value="{{ old('sort_order', $committee->sort_order) }}" 
                       min="0"
                       >
                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first. Use 0 for President, 1 for Vice President, etc.</p>
                @error('sort_order')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-200">

            {{-- IMAGE FIELD (Shows Current Image & Upload) --}}
            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Profile Image</label>
                
                {{-- Current Image Preview --}}
                @if ($committee->image_path)
                    <div class="mb-4">
                        <span class="text-xs font-semibold text-gray-600">Current Image:</span>
                        {{-- NOTE: Using Storage::url() means this route must be accessible via a symbolic link/public disk. --}}
                        <img src="{{ Storage::url($committee->image_path) }}" 
                             alt="Current Member Image" 
                             class="w-36 h-36 object-cover rounded-lg shadow-md border border-gray-200 mt-2">
                    </div>
                @endif

                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Upload New Image (Optional):</label>
                <input class="block w-full text-sm text-gray-500
                             file:mr-4 file:py-2 file:px-4
                             file:rounded-lg file:border-0
                             file:text-sm file:font-semibold
                             file:bg-blue-50 file:text-blue-700
                             hover:file:bg-blue-100 cursor-pointer
                             @error('image') border-red-500 @enderror" 
                       type="file" 
                       id="image" 
                       name="image">
                <small class="mt-1 text-xs text-gray-500">Leave blank to keep the current image.</small>
                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-200">

            {{-- STATUS FIELD --}}
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select class="form-select w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" 
                         id="status" 
                         name="status" 
                         required>
                    <option value="active" {{ old('status', $committee->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $committee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 18h15.356M4 12v5h.582m15.356-2A8.001 8.001 0 0118.418 6h-15.356"></path></svg>
                    Update Member
                </button>
                <a href="{{ route('admin.committee.index') }}" class="inline-flex justify-center items-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection