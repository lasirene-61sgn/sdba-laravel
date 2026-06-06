@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Edit Event: {{ $event->name }}</h2>
    </div>
    
    {{-- Responsive styling applied: removed max-w-3xl mx-auto for wider layout --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-8">
        <form action="{{ route('admin.event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- Event Name Field --}}
            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Event Name <span class="text-red-500">*</span></label>
                <input type="text" 
                       class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $event->name) }}" 
                       required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Posted Date Field --}}
                <div class="mb-5">
                    <label for="posted_date" class="block text-sm font-medium text-gray-700 mb-1">Posted Date <span class="text-red-500">*</span></label>
                    <input type="date" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('posted_date') border-red-500 @enderror" 
                           id="posted_date" 
                           name="posted_date" 
                           value="{{ old('posted_date', $event->posted_date ? $event->posted_date->format('Y-m-d') : '') }}" 
                           required>
                    @error('posted_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Status Field --}}
                <div class="mb-5">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                    <select class="form-select w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" 
                             id="status" 
                             name="status" 
                             required>
                        <option value="active" {{ old('status', $event->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $event->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            {{-- Image Upload Field (Multiple) --}}
            <div class="mb-5">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-1">Image Upload (Leave blank to keep existing images)</label>
                <input class="block w-full text-sm text-gray-500
                             file:mr-4 file:py-2 file:px-4
                             file:rounded-lg file:border-0
                             file:text-sm file:font-semibold
                             file:bg-blue-50 file:text-blue-700
                             hover:file:bg-blue-100 cursor-pointer
                             @error('images') border-red-500 @enderror" 
                       type="file" 
                       id="images" 
                       name="images[]" 
                       multiple>
                
                @error('images')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('images.*')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Current Images Display --}}
                @if ($event->image_paths)
                    <p class="mt-4 text-sm font-medium text-gray-700">Current Images:</p>
                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($event->image_paths as $imagePath)
                            <div class="relative group">
                                <img src="{{ asset('storage/' . $imagePath) }}" 
                                     alt="Current Event Image" 
                                     class="w-full h-32 object-cover rounded-lg shadow-md border border-gray-200 transition duration-300 group-hover:opacity-80">
                                {{-- Optionally add a delete button here using AJAX or another form if needed --}}
                                <div class="absolute inset-0 bg-black bg-opacity-30 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition duration-300">
                                    <span class="text-white text-xs font-semibold">Existing Image</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- QR Code Section --}}
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Event Attendance QR Code</label>
                <p class="text-sm text-gray-500 mb-3">Customers can scan this QR code to attend the event</p>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 inline-block">
                    {!! $event->qr_code !!}
                </div>
                <p class="mt-2 text-xs text-gray-500">Scan this QR code to mark attendance for this event</p>
            </div>

            {{-- Description Field --}}
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea class="form-textarea w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                              id="description" 
                              name="description" 
                              rows="5">{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.21a2 2 0 010 2.83L16 16.5m-6.077-4.298a1 1 0 011.076-.11l3.056 1.746a1 1 0 010 1.733l-3.056 1.746a1 1 0 01-1.076-.11l-3.056-1.746a1 1 0 010-1.733l3.056-1.746zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Update Event
                </button>
                <a href="{{ route('admin.event.index') }}" class="inline-flex justify-center items-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection