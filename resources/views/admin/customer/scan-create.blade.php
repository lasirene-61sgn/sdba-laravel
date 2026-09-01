@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8 max-w-[1600px] mx-auto">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div class="flex items-center space-x-3 mb-4 md:mb-0">
            <a href="{{ route('admin.customer.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 tracking-tight">AI Smart Registration (Bulk)</h2>
                <p class="text-xs text-gray-500 mt-0.5">Upload multiple customer record slips to automatically pull and populate data fields.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <div class="lg:col-span-4 space-y-4 lg:sticky lg:top-6">
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-5">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002-2z" /></svg>
                    Source Document Slips
                </h3>

                <div id="dropzone" class="border-2 border-dashed border-gray-300 hover:border-purple-500 rounded-xl p-8 text-center cursor-pointer transition bg-gray-50 flex flex-col items-center justify-center min-h-[220px]">
                    <input type="file" id="scanFileInp" accept="image/*" multiple class="hidden">
                    <svg class="h-10 w-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <p class="text-sm font-semibold text-gray-700">Click to select or drag slip images</p>
                    <p class="text-xs text-gray-400 mt-1">PNG, JPG or JPEG up to 3MB</p>
                </div>

                <div id="cameraOptionContainer" class="mt-3">
                    <div class="flex items-center justify-center relative my-3">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs uppercase">
                            <span class="bg-white px-2 text-gray-500 font-semibold">Or</span>
                        </div>
                    </div>
                    <input type="file" id="scanCameraInp" accept="image/*" capture="environment" class="hidden">
                    <button type="button" id="startCameraBtn" class="w-full flex items-center justify-center space-x-2 py-3 px-4 bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold rounded-xl border border-purple-200 transition shadow-sm">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                         <span>Take Photo with Camera</span>
                    </button>
                </div>
            </div>

            <div id="loadingState" class="hidden bg-purple-50 border border-purple-200 rounded-xl p-6 text-center shadow-sm">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-purple-600 border-t-transparent mb-3"></div>
                <h4 class="text-sm font-bold text-purple-900">Gemini AI Engine Reading Documents</h4>
                <p class="text-xs text-purple-700 mt-0.5"><span id="processingCount">0</span> of <span id="totalCount">0</span> processed...</p>
            </div>
            
            <div id="resultsSummary" class="hidden bg-white border border-gray-200 rounded-xl p-6 text-center shadow-sm mt-4">
                <h4 class="text-sm font-bold text-gray-800 mb-2">Processing Complete</h4>
                <p class="text-sm text-gray-600">You can now review the extracted records and save them.</p>
                <button type="button" id="changeImageBtn" class="mt-4 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">Upload More</button>
            </div>
        </div>

        <div class="lg:col-span-8">
            <form id="bulkCustomerForm" class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Extracted Customers</h3>
                    <span id="extractedCountBadge" class="bg-purple-100 text-purple-800 text-xs font-semibold px-2.5 py-0.5 rounded-full">0</span>
                </div>

                <div class="p-6 space-y-6" id="customersContainer">
                    <div class="text-center py-10 text-gray-400 italic" id="emptyStateMsg">
                        No customers parsed yet. Upload images to begin.
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-end space-x-3">
                    <a href="{{ route('admin.customer.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">Cancel</a>
                    <button type="submit" id="saveAllBtn" class="px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed" disabled>Save All Customers</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Camera Capture Modal -->
<div id="cameraModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-75 p-4">
    <div class="bg-white rounded-2xl overflow-hidden max-w-lg w-full shadow-2xl border border-gray-100 flex flex-col animate-fade-in">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="text-base font-bold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Document Slip Camera
            </h3>
            <button type="button" id="closeCameraModalBtn" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6 flex flex-col items-center justify-center bg-black relative min-h-[320px]">
            <video id="modalVideo" autoplay playsinline class="w-full h-auto max-h-[360px] object-cover rounded-xl shadow-inner bg-black"></video>
            <div id="cameraLoadingSpinner" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 bg-opacity-95 rounded-xl z-20">
                <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-purple-500 border-t-transparent mb-3"></div>
                <p class="text-sm font-semibold text-gray-300">Initializing Camera...</p>
            </div>
            <canvas id="modalCanvas" class="hidden"></canvas>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
            <button type="button" id="cancelCameraBtn" class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition">Cancel</button>
            <button type="button" id="capturePhotoBtn" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-sm font-semibold shadow-md flex items-center space-x-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span>Capture Photo</span>
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropzone = document.getElementById('dropzone');
    const fileInp = document.getElementById('scanFileInp');
    const cameraInp = document.getElementById('scanCameraInp');
    const startCameraBtn = document.getElementById('startCameraBtn');
    const cameraOptionContainer = document.getElementById('cameraOptionContainer');
    const loadingState = document.getElementById('loadingState');
    const resultsSummary = document.getElementById('resultsSummary');
    const changeImageBtn = document.getElementById('changeImageBtn');
    
    const processingCountEl = document.getElementById('processingCount');
    const totalCountEl = document.getElementById('totalCount');
    const customersContainer = document.getElementById('customersContainer');
    const emptyStateMsg = document.getElementById('emptyStateMsg');
    const extractedCountBadge = document.getElementById('extractedCountBadge');
    const saveAllBtn = document.getElementById('saveAllBtn');
    const bulkCustomerForm = document.getElementById('bulkCustomerForm');

    // Camera Modal elements
    const cameraModal = document.getElementById('cameraModal');
    const modalVideo = document.getElementById('modalVideo');
    const modalCanvas = document.getElementById('modalCanvas');
    const capturePhotoBtn = document.getElementById('capturePhotoBtn');
    const cancelCameraBtn = document.getElementById('cancelCameraBtn');
    const closeCameraModalBtn = document.getElementById('closeCameraModalBtn');
    const cameraLoadingSpinner = document.getElementById('cameraLoadingSpinner');
    let videoStream = null;
    
    let customersData = []; // Array to hold all parsed customers

    // Device detection helper
    function isMobileDevice() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    // Camera Modal controls
    function openCameraModal() {
        if (!cameraModal || !modalVideo) return;
        cameraModal.classList.remove('hidden');
        if (cameraLoadingSpinner) cameraLoadingSpinner.classList.remove('hidden');

        navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } },
            audio: false
        })
        .then(stream => {
            videoStream = stream;
            modalVideo.srcObject = stream;
            modalVideo.onloadedmetadata = () => {
                modalVideo.play();
                if (cameraLoadingSpinner) cameraLoadingSpinner.classList.add('hidden');
            };
        })
        .catch(err => {
            alert("Could not access camera: " + err.message);
            closeCameraModal();
        });
    }

    function closeCameraModal() {
        if (cameraModal) cameraModal.classList.add('hidden');
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        if (modalVideo) modalVideo.srcObject = null;
    }

    // Modal capture click event
    if (capturePhotoBtn && modalCanvas && modalVideo) {
        capturePhotoBtn.addEventListener('click', function() {
            const context = modalCanvas.getContext('2d');
            modalCanvas.width = modalVideo.videoWidth || 640;
            modalCanvas.height = modalVideo.videoHeight || 480;
            context.drawImage(modalVideo, 0, 0, modalCanvas.width, modalCanvas.height);
            
            modalCanvas.toBlob(function(blob) {
                if (blob) {
                    const file = new File([blob], "camera_capture.jpg", { type: "image/jpeg" });
                    closeCameraModal();
                    processFiles([file]);
                }
            }, 'image/jpeg', 0.9);
        });
    }

    if (cancelCameraBtn) cancelCameraBtn.addEventListener('click', closeCameraModal);
    if (closeCameraModalBtn) closeCameraModalBtn.addEventListener('click', closeCameraModal);

    if (dropzone) dropzone.addEventListener('click', () => fileInp.click());

    if (startCameraBtn && cameraInp) {
        startCameraBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (isMobileDevice()) {
                cameraInp.click();
            } else {
                openCameraModal();
            }
        });
    }

    if (fileInp) {
        fileInp.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                processFiles(Array.from(e.target.files));
            }
        });
    }

    if (cameraInp) {
        cameraInp.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                processFiles(Array.from(e.target.files));
            }
        });
    }

    if (changeImageBtn) {
        changeImageBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (fileInp) fileInp.value = '';
            if (cameraInp) cameraInp.value = '';
            if (resultsSummary) resultsSummary.classList.add('hidden');
            if (dropzone) dropzone.classList.remove('hidden');
            if (cameraOptionContainer) cameraOptionContainer.classList.remove('hidden');
        });
    }

    async function processFiles(files) {
        if (dropzone) dropzone.classList.add('hidden');
        if (cameraOptionContainer) cameraOptionContainer.classList.add('hidden');
        if (resultsSummary) resultsSummary.classList.add('hidden');
        if (loadingState) loadingState.classList.remove('hidden');

        totalCountEl.innerText = files.length;
        let processedCount = 0;
        processingCountEl.innerText = processedCount;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const formData = new FormData();
            formData.append('scanned_image', file);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await fetch('{{ route("admin.customer.scan-card") }}', {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                
                if (!response.ok) {
                    console.error("Server Status:", response.status);
                    continue; // skip on error, proceed to next
                }

                const result = await response.json();
                if (result.success && result.data) {
                    let aiData = result.data;
                    if (typeof aiData === 'string') {
                        try { aiData = JSON.parse(aiData); } catch(e) {}
                    }
                    // Generate unique ID for this customer block
                    aiData._clientId = 'cust_' + Math.random().toString(36).substr(2, 9);
                    customersData.push(aiData);
                    renderCustomerCard(aiData);
                }
            } catch (err) {
                console.error("Processing error for file:", file.name, err);
            }

            processedCount++;
            processingCountEl.innerText = processedCount;
        }

        if (loadingState) loadingState.classList.add('hidden');
        if (resultsSummary) resultsSummary.classList.remove('hidden');
        
        updateSaveBtnState();
    }

    function renderCustomerCard(customer) {
        if (emptyStateMsg) emptyStateMsg.style.display = 'none';
        extractedCountBadge.innerText = customersData.length;

        const card = document.createElement('div');
        card.className = "bg-white border border-gray-200 rounded-xl overflow-hidden mb-6 shadow-sm";
        card.id = `card_${customer._clientId}`;
        
        // Escape helper
        const escapeHtml = (unsafe) => {
            return (unsafe || '').toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
        };

        let familyHtml = '';
        if (customer.family_members && Array.isArray(customer.family_members) && customer.family_members.length > 0) {
            familyHtml = `
                <div class="mt-5 border-t border-gray-200 pt-4">
                    <h4 class="text-xs font-bold text-gray-600 uppercase tracking-wider mb-3">Family Members</h4>
                    <div class="overflow-x-auto rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Name</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Father/Spouse Name</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Age/DOB</th>
                                    <th class="px-3 py-2 text-left font-semibold text-gray-600">Mobile No</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
            `;
            
            customer.family_members.forEach((member, fIdx) => {
                familyHtml += `
                    <tr>
                        <td class="px-3 py-2"><input type="text" data-field="name" data-client="${customer._clientId}" data-fidx="${fIdx}" value="${escapeHtml(member.name)}" class="w-full border-gray-300 rounded text-sm p-1 family-input"></td>
                        <td class="px-3 py-2"><input type="text" data-field="father_or_spouse" data-client="${customer._clientId}" data-fidx="${fIdx}" value="${escapeHtml(member.father_or_spouse)}" class="w-full border-gray-300 rounded text-sm p-1 family-input"></td>
                        <td class="px-3 py-2"><input type="text" data-field="age_or_dob" data-client="${customer._clientId}" data-fidx="${fIdx}" value="${escapeHtml(member.age_or_dob)}" class="w-full border-gray-300 rounded text-sm p-1 family-input"></td>
                        <td class="px-3 py-2"><input type="text" data-field="mobile" data-client="${customer._clientId}" data-fidx="${fIdx}" value="${escapeHtml(member.mobile)}" class="w-full border-gray-300 rounded text-sm p-1 family-input"></td>
                    </tr>
                `;
            });
            familyHtml += `</tbody></table></div></div>`;
        }

        card.innerHTML = `
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center cursor-pointer" onclick="document.getElementById('body_${customer._clientId}').classList.toggle('hidden')">
                <h4 class="font-bold text-gray-700 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="card-title">${escapeHtml(customer.name) || 'Unknown Customer'}</span>
                </h4>
                <button type="button" class="text-red-500 hover:text-red-700 text-sm font-semibold" onclick="removeCustomer(event, '${customer._clientId}')">Remove</button>
            </div>
            <div id="body_${customer._clientId}" class="p-4 bg-white">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Customer Name *</label>
                        <input type="text" data-field="name" data-client="${customer._clientId}" value="${escapeHtml(customer.name)}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2 cust-input" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Mobile / Phone</label>
                        <input type="text" data-field="mobile" data-client="${customer._clientId}" value="${escapeHtml(customer.mobile)}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2 cust-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Father's Name</label>
                        <input type="text" data-field="father_name" data-client="${customer._clientId}" value="${escapeHtml(customer.father_name)}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2 cust-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Gotra</label>
                        <input type="text" data-field="gotra" data-client="${customer._clientId}" value="${escapeHtml(customer.gotra)}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2 cust-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Label Name</label>
                        <input type="text" data-field="label_name" data-client="${customer._clientId}" value="${escapeHtml(customer.label_name)}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2 cust-input">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">City</label>
                        <input type="text" data-field="city" data-client="${customer._clientId}" value="${escapeHtml(customer.city)}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm py-2 cust-input">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1">Office / Firm Address</label>
                        <textarea data-field="office_address" data-client="${customer._clientId}" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm cust-input">${escapeHtml(customer.office_address)}</textarea>
                    </div>
                </div>
                ${familyHtml}
            </div>
        `;
        customersContainer.appendChild(card);
    }

    window.removeCustomer = function(e, clientId) {
        e.stopPropagation();
        customersData = customersData.filter(c => c._clientId !== clientId);
        const card = document.getElementById(`card_${clientId}`);
        if (card) card.remove();
        extractedCountBadge.innerText = customersData.length;
        if (customersData.length === 0 && emptyStateMsg) emptyStateMsg.style.display = 'block';
        updateSaveBtnState();
    };

    // Keep data array synced with manual edits
    customersContainer.addEventListener('input', function(e) {
        const target = e.target;
        const clientId = target.getAttribute('data-client');
        const field = target.getAttribute('data-field');
        if (!clientId || !field) return;

        const custObj = customersData.find(c => c._clientId === clientId);
        if (!custObj) return;

        if (target.classList.contains('family-input')) {
            const fIdx = parseInt(target.getAttribute('data-fidx'));
            if (custObj.family_members && custObj.family_members[fIdx]) {
                custObj.family_members[fIdx][field] = target.value;
            }
        } else if (target.classList.contains('cust-input')) {
            custObj[field] = target.value;
            // Update title
            if (field === 'name') {
                const cardTitle = document.querySelector(`#card_${clientId} .card-title`);
                if (cardTitle) cardTitle.innerText = target.value || 'Unknown Customer';
            }
        }
    });

    function updateSaveBtnState() {
        if (customersData.length > 0) {
            saveAllBtn.removeAttribute('disabled');
        } else {
            saveAllBtn.setAttribute('disabled', 'disabled');
        }
    }

    bulkCustomerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (customersData.length === 0) return;
        
        saveAllBtn.setAttribute('disabled', 'disabled');
        const originalText = saveAllBtn.innerText;
        saveAllBtn.innerText = 'Saving...';

        try {
            const response = await fetch('{{ route("admin.customer.store-multiple") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ customers: customersData })
            });

            const result = await response.json();
            
            if (result.success) {
                let msg = `Successfully saved ${result.saved_count} customers.\n`;
                if (result.skipped_count > 0) {
                    msg += `\nSkipped ${result.skipped_count} duplicates (Data Already Added):\n`;
                    msg += result.skipped_details.join('\n');
                }
                alert(msg);
                window.location.href = '{{ route("admin.customer.index") }}';
            } else {
                alert('Error saving customers: ' + (result.message || 'Unknown error'));
                saveAllBtn.removeAttribute('disabled');
                saveAllBtn.innerText = originalText;
            }
        } catch (err) {
            console.error(err);
            alert('Failed to submit form data.');
            saveAllBtn.removeAttribute('disabled');
            saveAllBtn.innerText = originalText;
        }
    });
});
</script>
@endsection
