@extends('customer.layout')

@section('title', 'Customer Login')

@section('content')
<div class="container mx-auto max-w-md px-4 mt-10">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="p-8">
            <h2 class="text-3xl font-extrabold text-center text-gray-900 mb-2">Welcome Back</h2>
            <p class="text-center text-gray-500 mb-8 text-sm">Login via WhatsApp OTP</p>

            {{-- Error Alerts --}}
            <div id="alertContainer" class="hidden mb-6">
                <div class="bg-red-50 border-l-4 border-red-500 p-4">
                    <p id="errorMessage" class="text-red-700 text-sm font-medium"></p>
                </div>
            </div>

            <form id="loginForm" class="space-y-6">
                @csrf
                <div>
                    <label for="mobile" class="block text-sm font-semibold text-gray-700 mb-1">Mobile Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-phone-alt"></i>
                        </span>
                        <input type="text" name="mobile" id="mobile" required placeholder="Enter mobile with country code (e.g., 91...)"
                            class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out sm:text-sm">
                    </div>
                </div>

                <div>
                    <button type="submit" id="submitBtn" 
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <span id="btnText">Send OTP</span>
                        <svg id="loaderIcon" class="hidden animate-spin h-5 w-5 ml-3 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <div class="mt-8 border-t border-gray-200 pt-6">
                <div class="flex flex-col space-y-4 text-center">
                    <a href="{{ route('customer.password.login') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition duration-150">
                        Login with Password instead
                    </a>
                    <a href="{{ route('customer.forgot.password') }}" class="text-sm text-gray-500 hover:text-gray-700 transition duration-150">
                        Forgot Password?
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const mobile = document.getElementById('mobile').value;
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const loaderIcon = document.getElementById('loaderIcon');
        const alertContainer = document.getElementById('alertContainer');
        const errorMessage = document.getElementById('errorMessage');

        // Route Setup
        const sendOtpUrl = "{{ route('customer.send.otp') }}";
        const verifyOtpUrl = "{{ route('customer.verify.otp.form') }}";
        const csrfToken = "{{ csrf_token() }}";

        // Start Loading State
        submitBtn.disabled = true;
        btnText.innerText = 'Sending OTP...';
        loaderIcon.classList.remove('hidden');
        alertContainer.classList.add('hidden');

        fetch(sendOtpUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ mobile: mobile })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                // Success: Redirect to verify page with the mobile parameter
                window.location.href = verifyOtpUrl + "?mobile=" + encodeURIComponent(mobile);
            } else {
                // Show API or Validation Error
                throw new Error(result.message || 'Mobile number not found or service unavailable.');
            }
        })
        .catch(error => {
            console.error('Login Error:', error);
            // Re-enable UI
            submitBtn.disabled = false;
            btnText.innerText = 'Send OTP';
            loaderIcon.classList.add('hidden');
            
            // Show Alert
            alertContainer.classList.remove('hidden');
            errorMessage.innerText = error.message;
        });
    });
</script>
@endsection