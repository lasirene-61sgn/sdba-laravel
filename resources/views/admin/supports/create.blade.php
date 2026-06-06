@extends('admin.layout.app')

@section('title', 'Create New Support Entry')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Create New Support Entry</h2>
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
        <form method="POST" action="{{ route('admin.supports.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Basic Fields --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name Field --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}" 
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
                           value="{{ old('phone') }}" 
                           class="form-input w-full px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Image Field --}}
            <div class="mb-6">
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image (Required) <span class="text-red-500">*</span></label>
                <input type="file" 
                       id="image" 
                       name="image" 
                       accept="image/*" 
                       required 
                       class="block w-full text-sm text-gray-500
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-semibold
                              file:bg-blue-50 file:text-blue-700
                              hover:file:bg-blue-100 cursor-pointer
                              @error('image') border-red-500 @enderror">
                @error('image')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <hr class="my-6 border-gray-200">

            {{-- Support Type Selection and Dynamic Creation --}}
            <div class="mb-5">
                <label for="support_type_id" class="block text-sm font-medium text-gray-700 mb-1">Support Type <span class="text-red-500">*</span></label>
                <div class="flex items-center space-x-3">
                    <select id="support_type_id" name="support_type_id" required 
                            class="form-select px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('support_type_id') border-red-500 @enderror">
                        <option value="">Select Support Type</option>
                        @foreach ($supportTypes as $type)
                            <option value="{{ $type->id }}" {{ old('support_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="add-support-type-btn" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        + Add New
                    </button>
                    {{-- Test button --}}
                    <!-- <button type="button" id="test-ajax-btn" 
                            class="inline-flex items-center px-3 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        Test AJAX
                    </button> -->
                </div>
            </div>

            {{-- New Support Type Form (Hidden by default) --}}
            <div id="new-support-type-form" class="hidden-form p-4 my-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50" style="display: none;">
                <label for="new_support_type_name" class="block text-sm font-medium text-gray-700 mb-2">New Type Name:</label>
                <div class="flex space-x-3">
                    <input type="text" id="new_support_type_name" placeholder="e.g., Blog, News" 
                           class="form-input flex-grow px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button type="button" id="save-support-type-btn" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        Save & Select
                    </button>
                </div>
            </div>

            {{-- Support Category Selection and Dynamic Creation --}}
            <div class="mb-6">
                <label for="support_category_id" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                <div class="flex items-center space-x-3">
                    <select id="support_category_id" name="support_category_id" required 
                            class="form-select px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('support_category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach ($supportCategories as $category)
                            <option value="{{ $category->id }}" {{ old('support_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="add-support-category-btn" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        + Add New
                    </button>
                </div>
            </div>
            
            {{-- New Support Category Form (Hidden by default) --}}
            <div id="new-support-category-form" class="hidden-form p-4 my-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50" style="display: none;">
                <label for="new_support_category_name" class="block text-sm font-medium text-gray-700 mb-2">New Category Name:</label>
                <div class="flex space-x-3">
                    <input type="text" id="new_support_category_name" placeholder="e.g., Tech, Sports" 
                           class="form-input flex-grow px-4 py-2 border rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <button type="button" id="save-support-category-btn" 
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                        Save & Select
                    </button>
                </div>
            </div>

            <hr class="my-6 border-gray-200">
            
            {{-- Submit Button --}}
            <div class="flex justify-start pt-4">
                <button type="submit" 
                        class="inline-flex items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Create Support Entry
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    console.log("Document ready - initializing support creation forms");
    
    // Check if jQuery is loaded
    if (typeof $ === 'undefined') {
        console.error("jQuery is not loaded!");
        return;
    }
    
    // 💡 FIX: Inject the CSRF token into a JS variable once.
    // NOTE: Ensure your layout file ('admin.layout.app') contains:
    // <meta name="csrf-token" content="{{ csrf_token() }}">
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    console.log("CSRF Token:", csrfToken);

    /**
     * Handles the AJAX creation of a new Support Type or Category.
     * @param {string} addButtonId - ID of the "+ Add New" button.
     * @param {string} hiddenFormId - ID of the hidden input form container.
     * @param {string} inputId - ID of the text input field for the new name.
     * @param {string} saveButtonId - ID of the "Save & Select" button.
     * @param {string} selectId - ID of the main dropdown select element.
     * @param {string} routeName - Laravel route for storing the new item.
     * @param {string} entityName - Friendly name of the entity (e.g., 'Support Type').
     */
    function handleAjaxCreation(addButtonId, hiddenFormId, inputId, saveButtonId, selectId, routeName, entityName) {
        console.log("Initializing handler for", entityName);
        
        // Toggle the hidden form
        $(addButtonId).on('click', function() {
            console.log("Add button clicked for", entityName);
            $(hiddenFormId).slideToggle(200); // Use slideToggle for smoother transition
        });

        // Handle the Save button click
        $(saveButtonId).on('click', function() {
            console.log("Save button clicked for", entityName);
            const newItemName = $(inputId).val().trim();
            if (newItemName === '') {
                alert(`Please enter a ${entityName} name.`);
                return;
            }

            // Temporarily disable button to prevent multiple submissions
            $(saveButtonId).prop('disabled', true).text('Saving...'); 

            $.ajax({
                url: routeName,
                method: 'POST',
                data: {
                    // Use the JS variable holding the token.
                    _token: csrfToken, 
                    name: newItemName
                },
                success: function(response) {
                    console.log(entityName + " created successfully:", response);
                    
                    // Keys are 'support_type' and 'support_category' from the controllers
                    const key = entityName.toLowerCase().replace(' ', '_');
                    const newItem = response[key]; 
                    
                    // 1. Add new option to the main dropdown
                    const newOption = `<option value="${newItem.id}">${newItem.name}</option>`;
                    $(selectId).append(newOption);

                    // 2. Select the newly created option
                    $(selectId).val(newItem.id).change(); 
                    
                    // 3. Reset and hide the 'Add New' form
                    $(inputId).val('');
                    $(hiddenFormId).slideUp(200);

                    alert(response.message);
                },
                error: function(xhr) {
                    console.error("Error creating " + entityName + ":", xhr);
                    let errors = 'An unknown error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errors = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errors = xhr.responseJSON.message;
                    }
                    alert('Error creating ' + entityName + ':\n' + errors);
                },
                complete: function() {
                    // Re-enable button
                    $(saveButtonId).prop('disabled', false).text('Save & Select'); 
                }
            });
        });
    }

    // Initialize for Support Type
    handleAjaxCreation(
        '#add-support-type-btn', 
        '#new-support-type-form', 
        '#new_support_type_name', 
        '#save-support-type-btn', 
        '#support_type_id', 
        "{{ route('admin.support_types.store') }}", 
        'Support Type'
    );

    // Initialize for Support Category
    handleAjaxCreation(
        '#add-support-category-btn', 
        '#new-support-category-form', 
        '#new_support_category_name', 
        '#save-support-category-btn', 
        '#support_category_id', 
        "{{ route('admin.support_categories.store') }}", 
        'Support Category'
    );
    
    // Test AJAX button (kept commented out as in original for deployment readiness)
    /*
    $('#test-ajax-btn').on('click', function() {
        console.log("Test AJAX button clicked");
        $.ajax({
            url: "{{ route('admin.support_types.store') }}", // Using one of the existing routes for a test POST
            method: 'POST',
            data: {
                _token: csrfToken,
                name: 'Test Type ' + Math.floor(Math.random() * 100) // Dummy data
            },
            success: function(response) {
                console.log("Test AJAX successful:", response);
                alert("Test AJAX successful! Check console for details.");
            },
            error: function(xhr) {
                console.error("Test AJAX failed:", xhr);
                alert("Test AJAX failed! Check console for details.");
            }
        });
    });
    */
    
    console.log("All handlers initialized");
});
</script>
@endsection