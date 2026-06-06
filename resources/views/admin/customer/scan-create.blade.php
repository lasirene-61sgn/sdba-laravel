@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8 max-w-[1600px] mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div class="flex items-center space-x-3 mb-4 md:mb-0">
            <a href="{{ route('admin.customer.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">AI Smart Registration</h2>
                <p class="text-xs text-gray-500 mt-0.5">Upload a customer record slip to automatically pull and populate data fields.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <div class="lg:col-span-5 space-y-4 lg:sticky lg:top-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002-2z" /></svg>
                    Source Document Slip
                </h3>

                <div id="dropzone" class="border-2 border-dashed border-gray-300 hover:border-purple-500 rounded-xl p-8 text-center cursor-pointer transition bg-gray-50 flex flex-col items-center justify-center min-h-[220px]">
                    <input type="file" id="scanFileInp" accept="image/*" class="hidden">
                    <svg class="h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm font-semibold text-gray-700">Click to select or drag slip image</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG or JPEG up to 3MB</p>
                </div>

                <div id="previewContainer" class="hidden border border-gray-200 rounded-xl overflow-hidden bg-gray-900 flex items-center justify-center max-h-[500px] p-2 relative group shadow-inner">
                    <img id="scannedImagePreview" src="" alt="Slip Document Viewport" class="max-h-[480px] w-full object-contain rounded">
                    <button type="button" id="changeImageBtn" class="absolute top-3 right-3 bg-red-600 hover:bg-red-700 text-white p-2 rounded-lg shadow transition opacity-0 group-hover:opacity-100 duration-200" title="Remove & Scan Different Image">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>

            <div id="loadingState" class="hidden bg-purple-50 border border-purple-200 rounded-xl p-6 text-center shadow-sm">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-purple-600 border-t-transparent mb-3"></div>
                <h4 class="text-sm font-bold text-purple-900">Gemini AI Engine Reading Document</h4>
                <p class="text-xs text-purple-700 mt-0.5">Parsing handwritten details into fields automatically...</p>
            </div>
        </div>

        <div class="lg:col-span-7">
            <form id="customerForm" action="{{ route('admin.customer.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                @csrf
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Customer Registration Details</h3>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="field_name" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Father's Name</label>
                            <input type="text" name="father_name" id="field_father_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Gotra</label>
                            <input type="text" name="gotra" id="field_gotra" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Label Name</label>
                        <input type="text" name="label_name" id="field_label_name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">City</label>
                            <input type="text" name="city" id="field_city" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Pincode</label>
                            <input type="text" name="pincode" id="field_pincode" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Mobile / Phone Number</label>
                        <input type="text" name="mobile" id="field_mobile" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Address Line 2</label>
                        <input type="text" name="address2" id="field_address2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2.5">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Office / Firm Address</label>
                        <textarea name="office_address" id="field_office_address" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm"></textarea>
                    </div>

                    <input type="hidden" name="status" value="active">
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
                    <a href="{{ route('admin.customer.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 shadow-sm transition">Save Customer Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropzone = document.getElementById('dropzone');
    const fileInp = document.getElementById('scanFileInp');
    const previewContainer = document.getElementById('previewContainer');
    const imgPreview = document.getElementById('scannedImagePreview');
    const changeImageBtn = document.getElementById('changeImageBtn');
    const loadingState = document.getElementById('loadingState');

    // Trigger input click
    if (dropzone) {
        dropzone.addEventListener('click', () => fileInp.click());
    }

    // File dropped or selected listener
    if (fileInp) {
        fileInp.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                uploadAndParseSlip(e.target.files[0]);
            }
        });
    }

    // Reset layout view
    if (changeImageBtn) {
        changeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (fileInp) fileInp.value = '';
            if (imgPreview) imgPreview.src = '';
            if (previewContainer) previewContainer.classList.add('hidden');
            if (dropzone) dropzone.classList.remove('hidden');
            if (loadingState) loadingState.classList.add('hidden');
        });
    }

    function uploadAndParseSlip(file) {
        // Toggle view containers safely
        if (dropzone) dropzone.classList.add('hidden');
        if (previewContainer) previewContainer.classList.remove('hidden');
        if (loadingState) loadingState.classList.remove('hidden');

        // Draw local image preview immediately
        const reader = new FileReader();
        reader.onload = (e) => {
            if (imgPreview) imgPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Prep Multi-part Form Stream Data
        const formData = new FormData();
        formData.append('scanned_image', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Execute Request safely
        fetch('{{ route("admin.customer.scan-card") }}', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => {
            // If the server crashes with a real 500 error, we capture its actual text body instead of breaking
            if (!response.ok) {
                return response.text().then(textBody => {
                    throw new Error(`Server Status ${response.status}: ${textBody.substring(0, 200)}`);
                });
            }
            return response.json();
        })
        .then(result => {
            if (loadingState) loadingState.classList.add('hidden');
            
            console.log("Server Payload response:", result);

            if (result.success && result.data) {
                let aiData = result.data;
                
                if (typeof aiData === 'string') {
                    try { aiData = JSON.parse(aiData); } catch(e) { console.error("JSON parse error:", e); }
                }

                // Field Mapper Utility Engine
                const mapField = (selector, val) => {
                    const inputElement = document.getElementById(selector) || 
                                         document.querySelector(`[name="${selector}"]`) ||
                                         document.querySelector(`[name="${selector.toLowerCase()}"]`);
                    if (inputElement) {
                        inputElement.value = val || '';
                        inputElement.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                };

                // Apply mapped data to fields
                mapField('name', aiData.name);
                mapField('father_name', aiData.father_name);
                mapField('gotra', aiData.gotra);
                mapField('label_name', aiData.label_name);
                mapField('city', aiData.city);
                mapField('pincode', aiData.pincode);
                mapField('mobile', aiData.mobile);
                mapField('address2', aiData.address2);
                mapField('office_address', aiData.office_address);

                alert('Slip scanned and fields mapped successfully!');
            } else {
                alert('Mapping failed: ' + (result.message || 'Unknown structure error matching keys.'));
            }
        })
        .catch(err => {
            if (loadingState) loadingState.classList.add('hidden');
            console.error("REAL CRASH DETAILS:", err);
            
            // CRITICAL FIX: Show the REAL JavaScript error message so you can read what failed!
            alert('JavaScript Processing Error:\n' + err.message);
        });
    }
});
</script>
@endsection