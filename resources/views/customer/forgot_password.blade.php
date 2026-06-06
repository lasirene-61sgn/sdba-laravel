@extends('customer.layout')

@section('title', 'Forgot Password')

@section('content')
<div class="container mx-auto max-w-md">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Forgot Password</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form id="forgotPasswordForm">
            @csrf
            <div class="mb-4">
                <label for="mobile" class="block text-gray-700 text-sm font-bold mb-2">Mobile Number</label>
                <input type="text" name="mobile" id="mobile" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('customer.login') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Back
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Send OTP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const mobile = document.getElementById('mobile').value;
        
        const sendOtpUrl = "{{ route('customer.forgot.password.otp') }}";
        const resetPasswordUrl = "{{ route('customer.reset.password.form') }}";
        const csrfToken = "{{ csrf_token() }}";
        
        fetch(sendOtpUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ mobile: mobile })
        })
        .then(response => {
            // Even if response is not ok (like 422), we still want to parse the JSON
            return response.json().then(data => ({
                status: response.status,
                data: data,
                ok: response.ok
            }));
        })
        .then(result => {
            if (result.ok) {
                // Success response
                if (result.data.success) {
                    // Show success message to user
                    alert('OTP sent successfully to your WhatsApp. Please check your WhatsApp and enter the OTP.');
                    window.location.href = resetPasswordUrl + '?mobile=' + encodeURIComponent(mobile);
                } else {
                    alert('Error: ' + result.data.message);
                }
            } else {
                // Error response (like 422 validation error)
                if (result.data.message) {
                    alert('Error: ' + result.data.message);
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('A network error occurred. Please try again.');
        });
    });
</script>
@endsection