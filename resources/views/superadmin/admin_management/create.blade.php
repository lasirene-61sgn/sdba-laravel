@extends('superadmin.layout.app')

@section('title', 'Create Admin')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden p-5 md:p-6">
    <div class="border-b border-gray-100 pb-5 mb-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800 tracking-tight">Create New Admin</h2>
            <a href="{{ route('superadmin.admins.index') }}" 
                class="inline-flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-2 px-4 rounded-lg shadow-sm transition-all duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Admins</span>
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg flex items-start space-x-3 shadow-sm mb-6">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="font-medium">Please correct the following errors:</p>
                <ul class="list-disc pl-5 text-sm mt-1 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.admins.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="space-y-5 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <div>
                    <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500 transition duration-150">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500 transition duration-150">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="company_name" class="block text-gray-700 text-sm font-medium mb-2">Company Name</label>
                    <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" required
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500 transition duration-150">
                    @error('company_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="image" class="block text-gray-700 text-sm font-medium mb-2">Profile Image</label>
                    <input type="file" name="image" id="image" accept="image/*"
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500 transition duration-150">
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="helpline" class="block text-gray-700 text-sm font-medium mb-2">Helpline Details</label>
                <textarea name="helpline" id="helpline" rows="3"
                    class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500 transition duration-150">{{ old('helpline') }}</textarea>
                <p class="text-gray-500 text-xs mt-1">Enter helpline contact details that will be displayed to this admin only.</p>
                @error('helpline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500 transition duration-150">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-3 text-gray-700 placeholder-gray-400 focus:ring-primary-500 focus:border-primary-500 transition duration-150">
                </div>
                <div class="hidden md:block"></div> 
            </div>
        </div>

        <div class="mb-8 p-5 border border-gray-100 rounded-lg bg-gray-50">
            <label class="block text-gray-700 text-lg font-bold mb-3">Sidebar Permissions</label>
            <p class="text-gray-600 text-sm mb-4">Select which sidebar items this admin can access:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($sidebarItems as $key => $label)
                    <div class="flex items-center">
                        <input type="checkbox" 
                                name="sidebar_permissions[]" 
                                value="{{ $key }}" 
                                id="permission_{{ $key }}"
                                class="mr-3 h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                checked>
                        <label for="permission_{{ $key }}" class="text-gray-700 text-sm font-medium">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mb-8 p-5 border border-gray-100 rounded-lg bg-gray-50">
            <label class="block text-gray-700 text-lg font-bold mb-3">Customer Field Permissions</label>
            <p class="text-gray-600 text-sm mb-4">Select which customer fields this admin can view/edit:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($customerFields as $key => $label)
                    <div class="flex items-center">
                        <input type="checkbox" 
                                name="customer_field_permissions[]" 
                                value="{{ $key }}" 
                                id="customer_field_{{ $key }}"
                                class="mr-3 h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                checked>
                        <label for="customer_field_{{ $key }}" class="text-gray-700 text-sm font-medium">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-5 border-t border-gray-100 flex items-center justify-end space-x-3">
            <a href="{{ route('superadmin.admins.index') }}" 
                class="inline-flex items-center justify-center space-x-2 bg-gray-50 hover:bg-gray-100 text-gray-700 font-semibold py-2.5 px-5 rounded-lg transition-all duration-200 shadow-sm">
                Cancel
            </a>
            <button type="submit" 
                class="inline-flex items-center justify-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                Create Admin
            </button>
        </div>
    </form>
</div>
@endsection