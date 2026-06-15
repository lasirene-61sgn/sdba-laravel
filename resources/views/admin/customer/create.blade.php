@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">

    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight"> Add New Member</h2>
    </div>

    @php
    // Assuming $fieldPermissions is defined in the controller or Auth setup
    $fieldPermissions = Auth::guard('admin')->user()->customer_field_permissions ?? [];
    @endphp
    <!-- ================= FAMILY MEMBERS SECTION ================= -->
    <!-- <div class="mt-8 border-t border-gray-200 pt-6">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Family Members Details</h3>
                <p class="text-sm text-gray-500">Add household dependencies or relatives linked to this main profile.</p>
            </div>
            <button type="button" id="add-family-btn" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Family Member
            </button>
        </div>

        Container where dynamic family blocks will be injected -->
    <!-- <div id="family-members-container" class="space-y-4"></div>
    </div> -->
    <!-- ========================================================== -->
    {{-- Main Form Container --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-8">
        <form action="{{ route('admin.customer.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Personal Details Row 1 (Name, Image, Father Name, Gotra) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

                @if(empty($fieldPermissions) || in_array('name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('image', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                    <input type="file"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('image') border-red-500 @enderror"
                        id="image"
                        name="image">
                    @error('image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('father_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1">Father Name</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('father_name') border-red-500 @enderror"
                        id="father_name"
                        name="father_name"
                        value="{{ old('father_name') }}">
                    @error('father_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('gotra', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="gotra" class="block text-sm font-medium text-gray-700 mb-1">Gotra</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('gotra') border-red-500 @enderror"
                        id="gotra"
                        name="gotra"
                        value="{{ old('gotra') }}">
                    @error('gotra')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            <hr class="my-6 border-gray-200">

            {{-- Location/Business Details Row 2 (Label Name, Village, District) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                @if(empty($fieldPermissions) || in_array('label_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="label_name" class="block text-sm font-medium text-gray-700 mb-1">Label Name</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('label_name') border-red-500 @enderror"
                        id="label_name"
                        name="label_name"
                        value="{{ old('label_name') }}">
                    @error('label_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                <div class="mb-3 md:mb-0">
                    <label for="village_id" class="block text-sm font-medium text-gray-700 mb-1">Village</label>
                    <select class="form-select w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('village_id') border-red-500 @enderror"
                        id="village_id"
                        name="village_id">
                        <option value="">Select Village...</option>
                        @foreach($villages as $id => $name)
                        <option value="{{ $id }}" {{ old('village_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('village_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                @if(empty($fieldPermissions) || in_array('district', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="district" class="block text-sm font-medium text-gray-700 mb-1">District</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('district') border-red-500 @enderror"
                        id="district"
                        name="district"
                        value="{{ old('district') }}">
                    @error('district')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Area Field --}}
            <div class="mb-6">
                <label for="area" class="block text-sm font-medium text-gray-700 mb-1">Area Name</label>
                <input type="text"
                    class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('area') border-red-500 @enderror"
                    id="area"
                    name="area"
                    value="{{ old('area') }}"
                    placeholder="Enter area name">
                <p class="mt-1 text-sm text-gray-500">Enter the customer's living area name.</p>
                @error('area')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Firm Name & Door No. / H. No. --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('ms_firm_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="ms_firm_name" class="block text-sm font-medium text-gray-700 mb-1">M/S Firm Name</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('ms_firm_name') border-red-500 @enderror"
                        id="ms_firm_name"
                        name="ms_firm_name"
                        value="{{ old('ms_firm_name') }}">
                    @error('ms_firm_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('dno', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="dno" class="block text-sm font-medium text-gray-700 mb-1">Door No. / H. No.</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('dno') border-red-500 @enderror"
                        id="dno"
                        name="dno"
                        value="{{ old('dno') }}">
                    @error('dno')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Street / Road --}}
            @if(empty($fieldPermissions) || in_array('street_road', $fieldPermissions))
            <div class="mb-6">
                <label for="street_road" class="block text-sm font-medium text-gray-700 mb-1">Street / Road</label>
                <input type="text"
                    class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('street_road') border-red-500 @enderror"
                    id="street_road"
                    name="street_road"
                    value="{{ old('street_road') }}">
                @error('street_road')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif

            {{-- Address Line 2 --}}
            @if(empty($fieldPermissions) || in_array('address2', $fieldPermissions))
            <div class="mb-6">
                <label for="address2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                <input type="text"
                    class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('address2') border-red-500 @enderror"
                    id="address2"
                    name="address2"
                    value="{{ old('address2') }}">
                @error('address2')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif

            {{-- City & Pincode --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('city', $fieldPermissions))
                <div class="col-span-2 mb-3 md:mb-0">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('city') border-red-500 @enderror"
                        id="city"
                        name="city"
                        value="{{ old('city') }}">
                    @error('city')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('pincode', $fieldPermissions))
                <div class="col-span-2 mb-3 md:mb-0">
                    <label for="pincode" class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('pincode') border-red-500 @enderror"
                        id="pincode"
                        name="pincode"
                        value="{{ old('pincode') }}">
                    @error('pincode')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            <div class="col-span-2 mb-3 md:mb-0">
                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                <input type="text"
                    class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('state') border-red-500 @enderror"
                    id="state"
                    name="state"
                    value="{{ old('state') }}">
                @error('state')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <hr class="my-6 border-gray-200">

            {{-- Contact & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('mobile', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1">Mobile Number</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('mobile') border-red-500 @enderror"
                        id="mobile"
                        name="mobile"
                        value="{{ old('mobile') }}">
                    @error('mobile')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('whatsapp', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp') border-red-500 @enderror"
                        id="whatsapp"
                        name="whatsapp"
                        value="{{ old('whatsapp') }}">
                    @error('whatsapp')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('email', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}">
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Superadmin fields row 1 (Email, Age, Gender) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('age', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                    <input type="number"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('age') border-red-500 @enderror"
                        id="age"
                        name="age"
                        value="{{ old('age') }}">
                    @error('age')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('gender', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('gender') border-red-500 @enderror"
                        id="gender"
                        name="gender">
                        <option value="">Select Gender...</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Superadmin fields row 2 (Business Type, Business Name, Product/Service) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('business_type', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="business_type" class="block text-sm font-medium text-gray-700 mb-1">Business Type</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('business_type') border-red-500 @enderror"
                        id="business_type"
                        name="business_type"
                        value="{{ old('business_type') }}">
                    @error('business_type')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('business_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('business_name') border-red-500 @enderror"
                        id="business_name"
                        name="business_name"
                        value="{{ old('business_name') }}">
                    @error('business_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('product_service', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="product_service" class="block text-sm font-medium text-gray-700 mb-1">Product/Service</label>
                    <input type="text"
                        class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('product_service') border-red-500 @enderror"
                        id="product_service"
                        name="product_service"
                        value="{{ old('product_service') }}">
                    @error('product_service')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Superadmin field (Office Address) --}}
            @if(empty($fieldPermissions) || in_array('office_address', $fieldPermissions))
            <div class="mb-6">
                <label for="office_address" class="block text-sm font-medium text-gray-700 mb-1">Office Address</label>
                <textarea class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('office_address') border-red-500 @enderror"
                    id="office_address"
                    name="office_address"
                    rows="3">{{ old('office_address') }}</textarea>
                @error('office_address')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif

            @if(empty($fieldPermissions) || in_array('status', $fieldPermissions))
            <div class="mb-3 md:mb-0">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                <select class="form-select w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror"
                    id="status"
                    name="status"
                    required>
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif
    </div>

    <hr class="my-6 border-gray-200">

    {{-- Date of Birth and Anniversary Date Fields --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        @if(empty($fieldPermissions) || in_array('date_of_birth', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
            <input type="date"
                class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('date_of_birth') border-red-500 @enderror"
                id="date_of_birth"
                name="date_of_birth"
                value="{{ old('date_of_birth') }}">
            @error('date_of_birth')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('anniversary_date', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="anniversary_date" class="block text-sm font-medium text-gray-700 mb-1">Anniversary Date</label>
            <input type="date"
                class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('anniversary_date') border-red-500 @enderror"
                id="anniversary_date"
                name="anniversary_date"
                value="{{ old('anniversary_date') }}">
            @error('anniversary_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>

    {{-- Additional Customer Fields (Education, Occupation, Blood Group) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @if(empty($fieldPermissions) || in_array('education', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="education" class="block text-sm font-medium text-gray-700 mb-1">Education</label>
            <input type="text"
                class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('education') border-red-500 @enderror"
                id="education"
                name="education"
                value="{{ old('education') }}">
            @error('education')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('occupation', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1">Occupation</label>
            <input type="text"
                class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('occupation') border-red-500 @enderror"
                id="occupation"
                name="occupation"
                value="{{ old('occupation') }}">
            @error('occupation')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('blood_group', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-1">Blood Group</label>
            <input type="text"
                class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('blood_group') border-red-500 @enderror"
                id="blood_group"
                name="blood_group"
                value="{{ old('blood_group') }}">
            @error('blood_group')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>

    {{-- Hobbies & Native Place --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if(empty($fieldPermissions) || in_array('hobbies', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="hobbies" class="block text-sm font-medium text-gray-700 mb-1">Hobbies</label>
            <textarea class="form-textarea w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('hobbies') border-red-500 @enderror"
                id="hobbies"
                name="hobbies"
                rows="3">{{ old('hobbies') }}</textarea>
            @error('hobbies')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('native_place', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="native_place" class="block text-sm font-medium text-gray-700 mb-1">Native Place</label>
            <input type="text"
                class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('native_place') border-red-500 @enderror"
                id="native_place"
                name="native_place"
                value="{{ old('native_place') }}">
            @error('native_place')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>
    <div class="mb-3 md:mb-0">
        <label for="background_image" class="block text-sm font-medium text-gray-700 mb-1">background_image</label>
        <input type="file"
            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('background_image') border-red-500 @enderror"
            id="background_image"
            name="background_image">
        @error('background_image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    {{-- Submission Buttons --}}
    <div class="pt-4 border-t border-gray-100 flex gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Save Customer
        </button>
        <a href="{{ route('admin.customer.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
            Cancel
        </a>
    </div>
    </form>
</div>
</div>

<!-- <script>
    (function() {
        // Core initialization function
        function initFamilyFactory() {
            const container = document.getElementById('family-members-container');
            const addButton = document.getElementById('add-family-btn');

            if (!addButton || !container) {
                console.error("Family elements not found in DOM! Check your IDs.");
                return;
            }

            let familyIndex = 0;

            addButton.onclick = function(e) {
                e.preventDefault(); // Stop any form submission attempts

                const familyBlock = document.createElement('div');
                familyBlock.className = 'family-member-row bg-gray-50 p-4 rounded-xl border border-gray-200 relative shadow-sm mb-4';
                familyBlock.style.position = 'relative';
                familyBlock.style.backgroundColor = '#f9fafb';
                familyBlock.style.padding = '1rem';
                familyBlock.style.borderRadius = '0.75rem';
                familyBlock.style.border = '1px solid #e5e7eb';
                familyBlock.style.marginBottom = '1rem';

                familyBlock.innerHTML = `
                    <button type="button" class="remove-family-btn" style="position: absolute; top: 0.75rem; right: 0.75rem; color: #9ca3af; background: none; border: none; font-size: 1.25rem; cursor: pointer;">
                        &times;
                    </button>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; padding-right: 1.5rem;">
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; text-transform: uppercase; margin-bottom: 0.25rem;">Full Name <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="family[${familyIndex}][name]" style="width: 100%; rounded-md; border-color: #d1d5db; border-radius: 0.375rem; padding: 0.4rem;" placeholder="Name" required>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; text-transform: uppercase; margin-bottom: 0.25rem;">Relationship</label>
                            <input type="text" name="family[${familyIndex}][relationship]" style="width: 100%; border-radius: 0.375rem; border-color: #d1d5db; padding: 0.4rem;" placeholder="e.g. Son, Spouse">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; text-transform: uppercase; margin-bottom: 0.25rem;">Gender</label>
                            <select name="family[${familyIndex}][gender]" style="width: 100%; border-radius: 0.375rem; border-color: #d1d5db; padding: 0.4rem;">
                                <option value="">Select</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; text-transform: uppercase; margin-bottom: 0.25rem;">Mobile</label>
                            <input type="text" name="family[${familyIndex}][mobile]" style="width: 100%; border-radius: 0.375rem; border-color: #d1d5db; padding: 0.4rem;" placeholder="Mobile Number">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; text-transform: uppercase; margin-bottom: 0.25rem;">Date of Birth</label>
                            <input type="date" name="family[${familyIndex}][date_of_birth]" style="width: 100%; border-radius: 0.375rem; border-color: #d1d5db; padding: 0.4rem;">
                        </div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <label style="display: block; font-size: 0.75rem; font-weight: 600; color: #374151; text-transform: uppercase; margin-bottom: 0.25rem;">Profile Photo</label>
                        <input type="file" name="family[${familyIndex}][image]">
                    </div>
                `;

                container.appendChild(familyBlock);

                // Handle item deletion row click event
                familyBlock.querySelector('.remove-family-btn').onclick = function() {
                    familyBlock.remove();
                };

                familyIndex++;
                console.log("Successfully generated family row index: " + familyIndex);
            };
        }

        // Run immediately, and fallback on standard load listener triggers
        if (document.readyState === "complete" || document.readyState === "interactive") {
            initFamilyFactory();
        } else {
            document.addEventListener('DOMContentLoaded', initFamilyFactory);
        }
    })();
</script> -->
@endsection