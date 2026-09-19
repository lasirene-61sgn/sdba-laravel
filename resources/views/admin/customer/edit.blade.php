@extends('admin.layout.app')

@section('content')
<style>
    /* Tailwind-based Print Styles (replacing the original <style> block) */
    @media print {

        /* Hiding elements marked as 'no-print' */
        .no-print {
            display: none !important;
        }

        /* Setting base font size for print */
        body {
            font-size: 12px;
        }

        /* Making form labels bold */
        .form-label {
            font-weight: bold;
        }

        /* Adding a black border to form controls (inputs/selects/textareas) */
        /* This targets elements that would typically have the Tailwind 'border' class */
        .form-input-print {
            border: 1px solid #000;
        }

        /* Setting font size for the main heading */
        h2 {
            font-size: 18px;
        }

        /* Ensure no background color on print */
        .bg-white {
            background-color: transparent !important;
        }

        /* Ensure text is black on print */
        .text-gray-800,
        .text-gray-700 {
            color: #000 !important;
        }
    }
</style>

<div class="p-6 md:p-8">

    <div class="flex items-center space-x-3 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">✍️ Edit Member: {{ $customer->name }}</h2>
    </div>

    {{-- Print Button (No Print) --}}
    <div class="mb-6 no-print">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
            🖨️ Print Customer Details
        </button>
    </div>
    <div class="card bg-light mb-4 border-info">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1 text-info">Family Structure</h5>
                <p class="card-text small text-muted mb-0">Manage household relationships, dependencies, and matrimony statuses for this customer profile.</p>
            </div>
            <!-- <a href="{{ route('admin.customer.family.index', $customer->id) }}" class="btn btn-outline-info">
            Manage Family Members →
        </a> -->
        </div>
    </div>
    @php
    $fieldPermissions = Auth::guard('admin')->user()->customer_field_permissions ?? [];
    @endphp

    {{-- Main Form Container --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 md:p-8">
        <form action="{{ route('admin.customer.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="previous_url" value="{{ old('previous_url', url()->previous()) }}">

            {{-- Personal Details Row 1 (Name, Image, Father Name, Gotra) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

                @if(empty($fieldPermissions) || in_array('name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1 form-label">Name <span class="text-red-500">*</span></label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                        id="name"
                        name="name"
                        value="{{ old('name', $customer->name) }}"
                        required>
                    @error('name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('image', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-1 form-label">Image</label>
                    <input type="file"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('image') border-red-500 @enderror"
                        id="image"
                        name="image">
                    @if($customer->image)
                    <div class="mt-2">
                        <img src="{{ asset(strpos($customer->image, 'uploads/') === 0 ? $customer->image : 'storage/' . $customer->image) }}" alt="Customer Image" class="w-24 h-24 object-cover rounded-md border border-gray-200">
                    </div>
                    @endif
                    @error('image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
                
                @if(empty($fieldPermissions) || in_array('background_image', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="background_image" class="block text-sm font-medium text-gray-700 mb-1 form-label">Background Image</label>
                    <input type="file"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer @error('background_image') border-red-500 @enderror"
                        id="background_image"
                        name="background_image">
                    @if($customer->background_image)
                    <div class="mt-2">
                        <img src="{{ asset(strpos($customer->background_image, 'uploads/') === 0 ? $customer->background_image : 'storage/' . $customer->background_image) }}" alt="Customer Background Image" class="w-24 h-24 object-cover rounded-md border border-gray-200">
                    </div>
                    @endif
                    @error('background_image')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('father_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1 form-label">Father Name</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('father_name') border-red-500 @enderror"
                        id="father_name"
                        name="father_name"
                        value="{{ old('father_name', $customer->father_name) }}">
                    @error('father_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('gotra', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="gotra" class="block text-sm font-medium text-gray-700 mb-1 form-label">Gotra</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('gotra') border-red-500 @enderror"
                        id="gotra"
                        name="gotra"
                        value="{{ old('gotra', $customer->gotra) }}">
                    @error('gotra')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            <hr class="my-6 border-gray-200">

            {{-- Location/Business Details Row 2 (Label Name, Village, District) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                @if(empty($fieldPermissions) || in_array('label_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="label_name" class="block text-sm font-medium text-gray-700 mb-1 form-label">Label Name</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('label_name') border-red-500 @enderror"
                        id="label_name"
                        name="label_name"
                        value="{{ old('label_name', $customer->label_name) }}">
                    @error('label_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                <div class="mb-3 md:mb-0">
                    <label for="village_id" class="block text-sm font-medium text-gray-700 mb-1 form-label">Village</label>
                    <select class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('village_id') border-red-500 @enderror"
                        id="village_id"
                        name="village_id">
                        <option value="">Select Village...</option>
                        @foreach($villages as $id => $name)
                        <option value="{{ $id }}" {{ (old('village_id', $customer->village_id) == $id) ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                    @error('village_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                @if(empty($fieldPermissions) || in_array('district', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="district" class="block text-sm font-medium text-gray-700 mb-1 form-label">District</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('district') border-red-500 @enderror"
                        id="district"
                        name="district"
                        value="{{ old('district', $customer->district) }}">
                    @error('district')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Area Field --}}
            <div class="mb-6">
                <label for="area" class="block text-sm font-medium text-gray-700 mb-1 form-label">Area Name</label>
                <input type="text"
                    class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('area') border-red-500 @enderror"
                    id="area"
                    name="area"
                    value="{{ old('area', $customer->area) }}"
                    placeholder="Enter area name">
                <p class="mt-1 text-sm text-gray-500">Enter the customer's living area name.</p>
                @error('area')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Firm Name & Door No. / H. No. --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('ms_firm_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="ms_firm_name" class="block text-sm font-medium text-gray-700 mb-1 form-label">M/S Firm Name</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('ms_firm_name') border-red-500 @enderror"
                        id="ms_firm_name"
                        name="ms_firm_name"
                        value="{{ old('ms_firm_name', $customer->ms_firm_name) }}">
                    @error('ms_firm_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('dno', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="dno" class="block text-sm font-medium text-gray-700 mb-1 form-label">Door No. / H. No.</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('dno') border-red-500 @enderror"
                        id="dno"
                        name="dno"
                        value="{{ old('dno', $customer->dno) }}">
                    @error('dno')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Street / Road --}}
            @if(empty($fieldPermissions) || in_array('street_road', $fieldPermissions))
            <div class="mb-6">
                <label for="street_road" class="block text-sm font-medium text-gray-700 mb-1 form-label">Street / Road</label>
                <input type="text"
                    class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('street_road') border-red-500 @enderror"
                    id="street_road"
                    name="street_road"
                    value="{{ old('street_road', $customer->street_road) }}">
                @error('street_road')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif

            {{-- Address Line 2 --}}
            @if(empty($fieldPermissions) || in_array('address2', $fieldPermissions))
            <div class="mb-6">
                <label for="address2" class="block text-sm font-medium text-gray-700 mb-1 form-label">Address Line 2 (Optional)</label>
                <input type="text"
                    class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('address2') border-red-500 @enderror"
                    id="address2"
                    name="address2"
                    value="{{ old('address2', $customer->address2) }}">
                @error('address2')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif

            {{-- City & Pincode --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('city', $fieldPermissions))
                <div class="col-span-2 mb-3 md:mb-0">
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1 form-label">City</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('city') border-red-500 @enderror"
                        id="city"
                        name="city"
                        value="{{ old('city', $customer->city) }}">
                    @error('city')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('pincode', $fieldPermissions))
                <div class="col-span-2 mb-3 md:mb-0">
                    <label for="pincode" class="block text-sm font-medium text-gray-700 mb-1 form-label">Pincode</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('pincode') border-red-500 @enderror"
                        id="pincode"
                        name="pincode"
                        value="{{ old('pincode', $customer->pincode) }}">
                    @error('pincode')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            <div class="col-span-2 mb-3 md:mb-0">
                <label for="state" class="block text-sm font-medium text-gray-700 mb-1 form-label">State</label>
                <input type="text"
                    class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('pincode') border-red-500 @enderror"
                    id="state"
                    name="state"
                    value="{{ old('state', $customer->state) }}">
                @error('state')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <hr class="my-6 border-gray-200">

            {{-- Contact & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('mobile', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="mobile" class="block text-sm font-medium text-gray-700 mb-1 form-label">Mobile Number</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('mobile') border-red-500 @enderror"
                        id="mobile"
                        name="mobile"
                        value="{{ old('mobile', $customer->mobile) }}">
                    @error('mobile')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('whatsapp', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-1 form-label">WhatsApp Number</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('whatsapp') border-red-500 @enderror"
                        id="whatsapp"
                        name="whatsapp"
                        value="{{ old('whatsapp', $customer->whatsapp) }}">
                    @error('whatsapp')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('email', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1 form-label">Email</label>
                    <input type="email"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-500 @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email', $customer->email) }}">
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Superadmin fields row 1 (Email, Age, Gender) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('age', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="age" class="block text-sm font-medium text-gray-700 mb-1 form-label">Age</label>
                    <input type="number"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('age') border-red-500 @enderror"
                        id="age"
                        name="age"
                        value="{{ old('age', $customer->age) }}">
                    @error('age')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif

                @if(empty($fieldPermissions) || in_array('gender', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1 form-label">Gender</label>
                    <select class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('gender') border-red-500 @enderror"
                        id="gender"
                        name="gender">
                        <option value="">Select Gender...</option>
                        <option value="male" {{ old('gender', $customer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $customer->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $customer->gender) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Superadmin fields row 2 (Business Type, Business Name, Product/Service) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                @if(empty($fieldPermissions) || in_array('category_id', $fieldPermissions))
<div class="mb-3 md:mb-0 mt-4">
    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1 form-label">Category (Product/Service)</label>
    
    <select 
        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('category_id') border-red-500 @enderror"
        id="category_id"
        name="category_id"
        onchange="fetchSubcategories(this.value); toggleOtherInput('category_id', 'category_other_container')">
        <option value="">Select Category</option>
        @php
            $currentCategory = old('category_id', $customer->category_id ?? '');
            $isOtherCategory = $currentCategory && !$categories->contains('id', $currentCategory) && $currentCategory != 'Others';
        @endphp

        @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ $currentCategory == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
        <option value="Others" {{ $isOtherCategory || $currentCategory == 'Others' ? 'selected' : '' }}>Others</option>
    </select>

    <div id="category_other_container" class="mt-2 {{ $isOtherCategory || $currentCategory == 'Others' ? '' : 'hidden' }}">
        <input type="text" 
            class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            id="category_other"
            name="category_id"
            placeholder="Please enter custom category"
            value="{{ $isOtherCategory ? $currentCategory : old('category_id') }}"
            {{ $isOtherCategory || $currentCategory == 'Others' ? '' : 'disabled' }}>
    </div>

    @error('category_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
@endif

@if(empty($fieldPermissions) || in_array('subcategory_id', $fieldPermissions))
<div class="mb-3 md:mb-0 mt-4">
    <label for="subcategory_id" class="block text-sm font-medium text-gray-700 mb-1 form-label">Subcategory (Business Type)</label>
    
    <select 
        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('subcategory_id') border-red-500 @enderror"
        id="subcategory_id"
        name="subcategory_id"
        onchange="toggleOtherInput('subcategory_id', 'subcategory_other_container')">
        <option value="">Select Subcategory</option>
        @php
            $currentSubcategory = old('subcategory_id', $customer->subcategory_id ?? '');
            // We'll populate the options via AJAX below, but we can set the selected value.
        @endphp
        @if($currentSubcategory && $currentSubcategory != 'Others' && is_numeric($currentSubcategory))
            <option value="{{ $currentSubcategory }}" selected>{{ $customer->subcategory->name ?? 'Selected' }}</option>
        @endif
        <option value="Others" {{ (!is_numeric($currentSubcategory) && $currentSubcategory) || $currentSubcategory == 'Others' ? 'selected' : '' }}>Others</option>
    </select>

    <div id="subcategory_other_container" class="mt-2 {{ (!is_numeric($currentSubcategory) && $currentSubcategory) || $currentSubcategory == 'Others' ? '' : 'hidden' }}">
        <input type="text" 
            class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
            id="subcategory_other"
            name="subcategory_id"
            placeholder="Please enter custom subcategory"
            value="{{ (!is_numeric($currentSubcategory) && $currentSubcategory) ? $currentSubcategory : '' }}"
            {{ (!is_numeric($currentSubcategory) && $currentSubcategory) || $currentSubcategory == 'Others' ? '' : 'disabled' }}>
    </div>

    @error('subcategory_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
@endif 

<script>
function fetchSubcategories(categoryId) {
    const subcategorySelect = document.getElementById('subcategory_id');
    const subcategoryOtherContainer = document.getElementById('subcategory_other_container');
    const subcategoryOtherInput = document.getElementById('subcategory_other');
    
    // Clear options except first
    subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
    
    if (categoryId === 'Others') {
        subcategorySelect.innerHTML += '<option value="Others" selected>Others</option>';
        subcategoryOtherContainer.classList.remove('hidden');
        subcategoryOtherInput.disabled = false;
        return;
    }

    if (!categoryId) return;

    fetch(`/admin/api/categories/${categoryId}/subcategories`)
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Select Subcategory</option>';
            
            // Common Subcategories
            const commonSubs = ['Retail', 'Manufacturer', 'WholeSale', 'Services', 'Professional'];
            commonSubs.forEach(sub => {
                const selected = (sub == '{{ $customer->subcategory_id ?? "" }}') ? 'selected' : '';
                options += `<option value="${sub}" ${selected}>${sub}</option>`;
            });

            data.forEach(sub => {
                const selected = (sub.id == '{{ $customer->subcategory_id ?? "" }}') ? 'selected' : '';
                options += `<option value="${sub.id}" ${selected}>${sub.name}</option>`;
            });
            options += '<option value="Others">Others</option>';
            subcategorySelect.innerHTML = options;
        });
}

function toggleOtherInput(selectId, containerId) {
    const selectElement = document.getElementById(selectId);
    const containerElement = document.getElementById(containerId);
    const inputElement = document.getElementById(selectId === 'category_id' ? 'category_other' : 'subcategory_other');
    
    if (selectElement.value === 'Others') {
        containerElement.classList.remove('hidden');
        inputElement.disabled = false;
        selectElement.name = ''; // Prevent sending 'Others'
    } else {
        containerElement.classList.add('hidden');
        inputElement.disabled = true;
        selectElement.name = selectId;
        if (inputElement) inputElement.value = '';
    }
}

// Initial fetch if category is already selected
document.addEventListener('DOMContentLoaded', function() {
    const categoryId = document.getElementById('category_id').value;
    if (categoryId && categoryId !== 'Others') {
        fetchSubcategories(categoryId);
    }
});
</script>

                @if(empty($fieldPermissions) || in_array('business_name', $fieldPermissions))
                <div class="mb-3 md:mb-0">
                    <label for="business_name" class="block text-sm font-medium text-gray-700 mb-1 form-label">Business Name</label>
                    <input type="text"
                        class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('business_name') border-red-500 @enderror"
                        id="business_name"
                        name="business_name"
                        value="{{ old('business_name', $customer->business_name) }}">
                    @error('business_name')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
            </div>

            {{-- Superadmin field (Office Address) --}}
            @if(empty($fieldPermissions) || in_array('office_address', $fieldPermissions))
            <div class="mb-6">
                <label for="office_address" class="block text-sm font-medium text-gray-700 mb-1 form-label">Office Address</label>
                <textarea class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('office_address') border-red-500 @enderror"
                    id="office_address"
                    name="office_address"
                    rows="3">{{ old('office_address', $customer->office_address) }}</textarea>
                @error('office_address')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @endif

            @if(empty($fieldPermissions) || in_array('status', $fieldPermissions))
            <div class="mb-3 md:mb-0">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1 form-label">Status <span class="text-red-500">*</span></label>
                <select class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('status') border-red-500 @enderror"
                    id="status"
                    name="status"
                    required>
                    <option value="active" {{ old('status', $customer->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $customer->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1 form-label">Date of Birth</label>
            <input type="date"
                class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('date_of_birth') border-red-500 @enderror"
                id="date_of_birth"
                name="date_of_birth"
                value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}">
            @error('date_of_birth')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('anniversary_date', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="anniversary_date" class="block text-sm font-medium text-gray-700 mb-1 form-label">Anniversary Date</label>
            <input type="date"
                class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('anniversary_date') border-red-500 @enderror"
                id="anniversary_date"
                name="anniversary_date"
                value="{{ old('anniversary_date', $customer->anniversary_date ? $customer->anniversary_date->format('Y-m-d') : '') }}">
            @error('anniversary_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>

    {{-- Additional Customer Fields (Education, Occupation, Blood Group) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @if(empty($fieldPermissions) || in_array('education', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="education" class="block text-sm font-medium text-gray-700 mb-1 form-label">Education</label>
            <input type="text"
                class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('education') border-red-500 @enderror"
                id="education"
                name="education"
                value="{{ old('education', $customer->education) }}">
            @error('education')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('occupation', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1 form-label">Occupation</label>
            <input type="text"
                class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('occupation') border-red-500 @enderror"
                id="occupation"
                name="occupation"
                value="{{ old('occupation', $customer->occupation) }}">
            @error('occupation')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('blood_group', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="blood_group" class="block text-sm font-medium text-gray-700 mb-1 form-label">Blood Group</label>
            <input type="text"
                class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('blood_group') border-red-500 @enderror"
                id="blood_group"
                name="blood_group"
                value="{{ old('blood_group', $customer->blood_group) }}">
            @error('blood_group')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>

    {{-- Hobbies & Native Place --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @if(empty($fieldPermissions) || in_array('hobbies', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="hobbies" class="block text-sm font-medium text-gray-700 mb-1 form-label">Hobbies</label>
            <textarea class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('hobbies') border-red-500 @enderror"
                id="hobbies"
                name="hobbies"
                rows="3">{{ old('hobbies', $customer->hobbies) }}</textarea>
            @error('hobbies')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif

        @if(empty($fieldPermissions) || in_array('native_place', $fieldPermissions))
        <div class="mb-3 md:mb-0">
            <label for="native_place" class="block text-sm font-medium text-gray-700 mb-1 form-label">Native Place</label>
            <input type="text"
                class="form-input-print w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 @error('native_place') border-red-500 @enderror"
                id="native_place"
                name="native_place"
                value="{{ old('native_place', $customer->native_place) }}">
            @error('native_place')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        @endif
    </div>


    {{-- Submission Buttons --}}
    <div class="pt-4 border-t border-gray-100 flex gap-3">
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out no-print">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Update Customer
        </button>
        <a href="{{ route('admin.customer.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out no-print">
            Cancel
        </a>
    </div>
    </form>
</div>
</div>
@endsection