@extends('admin.layout.app')

@section('title', 'Edit Support Entry: ' . $support->name)

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Edit Support Entry: {{ $support->name }}</h2>
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
        <form method="POST" action="{{ route('admin.supports.update', $support) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Use the PUT method for updates --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name Field --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $support->name) }}" 
                           required 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone Field --}}
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $support->phone) }}" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Current Image Preview --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Image:</label>
                @if ($support->image)
                    <img src="{{ asset('storage/' . $support->image) }}" 
                         alt="Current Image" 
                         class="w-40 h-40 object-cover rounded-lg shadow-md border border-gray-200 mt-2">
                @else
                    <p class="text-gray-500 text-sm mt-2">No image uploaded.</p>
                @endif
            </div>

            {{-- New Image Upload Field --}}
            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Upload New Image (Optional):</label>
                <input type="file" 
                       id="image" 
                       name="image" 
                       accept="image/*" 
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 cursor-pointer
                              @error('image') border-red-500 @enderror">
                <small class="mt-1 text-xs text-gray-500">Leave blank to keep the current image.</small>
                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-200">

            {{-- Support Type Selection --}}
            <div class="mb-5">
                <label for="support_type_id" class="block text-sm font-medium text-gray-700 mb-1">Support Type <span class="text-red-500">*</span></label>
                <select id="support_type_id" name="support_type_id" required 
                        class="form-select w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('support_type_id') border-red-500 @enderror">
                    <option value="">Select Support Type</option>
                    @foreach ($supportTypes as $type)
                        <option value="{{ $type->id }}" 
                            {{ old('support_type_id', $support->support_type_id) == $type->id ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
                @error('support_type_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Support Category Selection --}}
            <div class="mb-6">
                <label for="support_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <select id="support_category_id" name="support_category_id" required 
                        class="form-select w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('support_category_id') border-red-500 @enderror">
                    <option value="">Select Category</option>
                    @foreach ($supportCategories as $category)
                        <option value="{{ $category->id }}" 
                            {{ old('support_category_id', $support->support_category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('support_category_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-200">
            
            {{-- Buttons --}}
            <div class="flex gap-3 pt-4">
                <button type="submit" class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 18h15.356M4 12v5h.582m15.356-2A8.001 8.001 0 0118.418 6h-15.356"></path></svg>
                    Update Support Entry
                </button>
                <a href="{{ route('admin.supports.index') }}" class="inline-flex justify-center items-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection