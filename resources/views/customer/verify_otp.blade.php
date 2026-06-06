@extends('customer.layout')

@section('title', 'Verify OTP')

@section('content')
<div class="container mx-auto max-w-md">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Verify OTP</h2>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('customer.verify.otp') }}">
            @csrf
            <input type="hidden" name="mobile" value="{{ $mobile }}">
            
            <div class="mb-4">
                <label for="otp" class="block text-gray-700 text-sm font-bold mb-2">Enter OTP</label>
                <input type="text" name="otp" id="otp" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-gray-600 text-xs mt-1">Enter the OTP sent to your mobile number</p>
            </div>

            <div class="flex items-center justify-between">
                <a href="{{ route('customer.login') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Back
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Verify OTP
                </button>
            </div>
        </form>
    </div>
</div>
@endsection