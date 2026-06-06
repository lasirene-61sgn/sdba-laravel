@extends('superadmin.layout.app')

@section('title', 'Edit Admin')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Admin: {{ $admin->name }}</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.admins.update', $admin) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $admin->name) }}" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $admin->email) }}" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="company_name" class="block text-gray-700 text-sm font-bold mb-2">Company Name</label>
            <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $admin->company_name) }}" required
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Profile Image</label>
            <input type="file" name="image" id="image" accept="image/*"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            @if($admin->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $admin->image) }}" alt="Current Profile Image" class="w-24 h-24 rounded-full object-cover">
                </div>
            @endif
        </div>

        <div class="mb-4">
            <label for="helpline" class="block text-gray-700 text-sm font-bold mb-2">Helpline Details</label>
            <textarea name="helpline" id="helpline" rows="3"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('helpline', $admin->helpline) }}</textarea>
            <p class="text-gray-600 text-xs mt-1">Enter helpline contact details that will be displayed to this admin only.</p>
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password (Leave blank to keep current)</label>
            <input type="password" name="password" id="password"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <!-- Sidebar Permissions Section -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Sidebar Permissions</label>
            <p class="text-gray-600 text-sm mb-3">Select which sidebar items this admin can access:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($sidebarItems as $key => $label)
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="sidebar_permissions[]" 
                               value="{{ $key }}" 
                               id="permission_{{ $key }}"
                               class="mr-2 h-4 w-4 text-blue-600"
                               {{ in_array($key, $admin->sidebar_permissions ?? array_keys($sidebarItems)) ? 'checked' : '' }}>
                        <label for="permission_{{ $key }}" class="text-gray-700">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Customer Field Permissions Section -->
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Customer Field Permissions</label>
            <p class="text-gray-600 text-sm mb-3">Select which customer fields this admin can view/edit:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($customerFields as $key => $label)
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="customer_field_permissions[]" 
                               value="{{ $key }}" 
                               id="customer_field_{{ $key }}"
                               class="mr-2 h-4 w-4 text-blue-600"
                               {{ in_array($key, $admin->customer_field_permissions ?? array_keys($customerFields)) ? 'checked' : '' }}>
                        <label for="customer_field_{{ $key }}" class="text-gray-700">{{ $label }}</label>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex items-center justify-between">
            <a href="{{ route('superadmin.admins.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Admin
            </button>
        </div>
    </form>
</div>
@endsection