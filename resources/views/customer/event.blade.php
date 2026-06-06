@extends('customer.layout')

@section('title', 'Events')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Events</h2>
    
    @if(session('success'))
        <div class="alert alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    @if($events->isEmpty())
        <div class="text-center py-12">
            <div class="text-5xl text-gray-300 mb-4">🎉</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Events</h3>
            <p class="text-gray-600">
                There are no events available at the moment.
            </p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($events as $event)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="font-semibold text-lg text-gray-800">{{ $event->name }}</h3>
                            @if($event->date)
                                <p class="text-gray-600 mt-1">
                                    <span class="font-medium">Date:</span> {{ $event->date ? $event->date->format('M d, Y') : 'N/A' }}
                                </p>
                            @endif
                        </div>
                        <div class="mt-2 md:mt-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $event->status === 'active' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    {{-- Define customer RSVP variable here --}}
                    @php
                        $customerRsvp = $event->rsvps->where('customer_id', $customer->id)->first();
                    @endphp

                    <!-- RSVP Section -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h4 class="text-md font-medium text-gray-800 mb-2">Event RSVP</h4>
                        
                        @if($customerRsvp)
                            <div class="mb-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                    @if($customerRsvp->status == 'accepted') bg-green-100 text-green-800
                                    @elseif($customerRsvp->status == 'declined') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    Your Response: {{ ucfirst($customerRsvp->status) }}
                                    @if($customerRsvp->updated_at)
                                        <span class="ml-2">(Updated: {{ $customerRsvp->updated_at ? $customerRsvp->updated_at->setTimezone('Asia/Kolkata')->format('M d, Y H:i:s') : 'N/A' }})</span>
                                    @endif
                                    @if($customerRsvp->note)
                                        <span class="ml-2">({{ $customerRsvp->note }})</span>
                                    @endif
                                </span>
                                
                                @if($customerRsvp->status == 'accepted')
                                    <div class="mt-2 text-sm text-gray-600">
                                        <span>Adults: {{ $customerRsvp->adults_count }}</span>
                                        <span class="mx-2">•</span>
                                        <span>Children: {{ $customerRsvp->children_count }}</span>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <p class="text-sm text-gray-600 mb-3">Please RSVP to confirm your intention to attend this event:</p>
                                
                                <div class="flex flex-wrap gap-2">
                                    <form method="POST" action="{{ route('customer.event.rsvp', $event->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="accepted">
                                        <div class="mb-3">
                                            <label for="adults_count_{{ $event->id }}" class="form-label text-sm">Adults Count</label>
                                            <input type="number" class="form-control text-sm" id="adults_count_{{ $event->id }}" name="adults_count" min="0" value="1" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="children_count_{{ $event->id }}" class="form-label text-sm">Children Count</label>
                                            <input type="number" class="form-control text-sm" id="children_count_{{ $event->id }}" name="children_count" min="0" value="0" required>
                                        </div>
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm">
                                            Accept & RSVP
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('customer.event.rsvp', $event->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="declined">
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors text-sm">
                                            Decline
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('customer.event.rsvp', $event->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="status" value="maybe">
                                        <button type="submit" class="px-3 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition-colors text-sm">
                                            Maybe
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Attendance Section -->
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <h4 class="text-md font-medium text-gray-800 mb-2">Event Attendance</h4>
                        
                        @php
                            $hasAttended = $customerRsvp && $customerRsvp->attended;
                        @endphp
                        
                        @if($hasAttended)
                            <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-green-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-green-800">Attendance Confirmed</h4>
                                        <p class="text-sm text-green-700 mt-1">
                                            You have successfully marked your attendance for this event on {{ $customerRsvp->attendance_timestamp ? $customerRsvp->attendance_timestamp->setTimezone('Asia/Kolkata')->format('M d, Y H:i:s') : 'N/A' }}.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 001 1zm0 10h2a1 1 0 001-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-blue-800">Scan QR Code for Attendance</h4>
                                        <p class="text-sm text-blue-700 mt-1">
                                            Point your device's camera at the event QR code to mark your attendance.
                                        </p>
                                        <button data-event-id="{{ $event->id }}" class="scan-qr-btn mt-2 inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            Scan QR Code
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if($event->description)
                        <p class="text-gray-600 mt-3">{{ $event->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- QR Scanner Modal -->
<div id="qr-scanner-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black bg-opacity-75"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Scan QR Code</h3>
                    <button onclick="closeQrScanner()" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-4">
                <div id="scanner-container" class="relative w-full h-64 bg-gray-100 rounded-lg overflow-hidden">
                    <video id="qr-video" class="w-full h-full object-cover"></video>
                    <canvas id="qr-canvas" class="hidden"></canvas>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="border-2 border-blue-500 border-dashed rounded-lg w-48 h-48"></div>
                    </div>
                </div>
                <p class="mt-2 text-sm text-gray-500 text-center">Position the QR code within the frame to scan</p>
                <div class="mt-4 flex justify-center">
                    <button onclick="startCamera()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Start Camera
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Load jsQR library -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

<!-- JavaScript for QR Code Scanning -->
<script>
let currentEventId = null;
let stream = null;
let scanningInterval = null;

// Add event listeners to all scan buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.scan-qr-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            openQrScanner(eventId);
        });
    });
});

function openQrScanner(eventId) {
    currentEventId = eventId;
    document.getElementById('qr-scanner-modal').classList.remove('hidden');
}

function closeQrScanner() {
    document.getElementById('qr-scanner-modal').classList.add('hidden');
    stopCamera();
}

function startCamera() {
    const video = document.getElementById('qr-video');
    
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert('Your browser does not support camera access. Please use a modern browser or scan the QR code manually.');
        return;
    }
    
    navigator.mediaDevices.getUserMedia({ 
        video: { facingMode: 'environment' } 
    })
    .then(function(mediaStream) {
        stream = mediaStream;
        video.srcObject = mediaStream;
        video.play();
        
        // Start scanning for QR codes
        startQrScanning();
    })
    .catch(function(err) {
        console.error('Camera error:', err);
        alert('Could not access the camera. Please ensure you have granted camera permissions.');
    });
}

function stopCamera() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    
    if (scanningInterval) {
        clearInterval(scanningInterval);
        scanningInterval = null;
    }
}

function startQrScanning() {
    const video = document.getElementById('qr-video');
    const canvas = document.getElementById('qr-canvas');
    const canvasContext = canvas.getContext('2d');
    
    // Scan every 500ms
    scanningInterval = setInterval(function() {
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvasContext.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = canvasContext.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            
            if (code) {
                // QR code detected
                handleQrCodeResult(code.data);
            }
        }
    }, 500);
}

function handleQrCodeResult(data) {
    // Debug information
    console.log('Scanned QR Code Data:', data);
    console.log('Current Event ID:', currentEventId);
    console.log('Window location origin:', window.location.origin);
    
    // Check if the scanned QR code is for the current event
    // Be more flexible with URL comparison to handle potential trailing slashes or other minor differences
    const baseUrl = window.location.origin + '/customer/event/qr-attend/';
    const expectedUrl = baseUrl + currentEventId;
    
    console.log('Expected URL:', expectedUrl);
    console.log('Base URL for comparison:', baseUrl);
    
    // Normalize URLs for comparison by removing trailing slashes and query parameters
    const normalizeUrl = function(url) {
        // Remove query parameters
        const urlWithoutParams = url.split('?')[0];
        // Remove trailing slashes
        return urlWithoutParams.replace(/\/$/, '');
    };
    
    const normalizedData = normalizeUrl(data);
    const normalizedExpected = normalizeUrl(expectedUrl);
    
    console.log('Normalized Scanned Data:', normalizedData);
    console.log('Normalized Expected URL:', normalizedExpected);
    
    // Also check if the scanned data contains the base URL
    console.log('Does scanned data start with base URL?', data.startsWith(baseUrl));
    console.log('Does normalized data start with normalized base URL?', normalizedData.startsWith(normalizeUrl(baseUrl)));
    
    // Extract just the path and ID from both URLs for comparison
    // This handles cases where the domain/protocol might be different
    const extractPathAndId = function(url) {
        try {
            // Create a temporary anchor element to parse the URL
            const a = document.createElement('a');
            a.href = url;
            // Return just the path part
            return a.pathname;
        } catch (e) {
            // If URL parsing fails, return the original URL
            return url;
        }
    };
    
    const dataPath = extractPathAndId(data);
    const expectedPath = extractPathAndId(expectedUrl);
    
    console.log('Data path:', dataPath);
    console.log('Expected path:', expectedPath);
    
    // Check if paths match
    if (normalizedData === normalizedExpected) {
        // Correct QR code for this event
        // Make a request to mark attendance
        markAttendance(currentEventId);
    } else {
        // Check if it's a valid event QR code by checking if it starts with the expected path pattern
        const basePath = '/customer/event/qr-attend/';
        
        if (dataPath.startsWith(basePath)) {
            // It's a valid event QR code, but for a different event
            // Extract the event ID by removing the base path
            const eventId = dataPath.replace(basePath, '').replace(/^\//, '');
            console.log('Extracted event ID:', eventId);
            
            // Check if the extracted ID is a valid number
            if (eventId && !isNaN(eventId)) {
                if (eventId === currentEventId) {
                    // This is actually the correct event, but the domain was different
                    markAttendance(eventId);
                } else {
                    // Different event
                    alert('This QR code is for a different event (ID: ' + eventId + '). Please scan the correct QR code for this event.');
                }
            } else {
                // Invalid ID format
                alert('Invalid QR code. Please scan a valid event QR code.');
            }
        } else {
            // Not a valid event QR code
            console.log('QR code does not match expected pattern');
            alert('Invalid QR code. Please scan a valid event QR code.');
        }
    }
}

function markAttendance(eventId) {
    // Close the scanner modal
    closeQrScanner();
    
    // Show loading message
    alert('Marking your attendance...');
    
    // Redirect to the QR attendance route
    window.location.href = '/customer/event/qr-attend/' + eventId;
}

// Close modal when clicking outside
document.getElementById('qr-scanner-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQrScanner();
    }
});
</script>
@endsection