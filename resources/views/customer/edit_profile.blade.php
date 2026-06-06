@extends('customer.layout')

@section('title', 'Edit Profile')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Profile</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('customer.update.profile') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Personal Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                
                <div class="mb-4">
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Profile Image</label>
                    <input type="file" name="image" id="image"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @if($customer->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $customer->image) }}" alt="Current Image" class="rounded-full w-16 h-16 object-cover">
                        </div>
                    @endif
                </div>
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $customer->name) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="father_name" class="block text-gray-700 text-sm font-bold mb-2">Father's Name</label>
                    <input type="text" name="father_name" id="father_name" value="{{ old('father_name', $customer->father_name) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="gotra" class="block text-gray-700 text-sm font-bold mb-2">Gotra</label>
                    <input type="text" name="gotra" id="gotra" value="{{ old('gotra', $customer->gotra) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="label_name" class="block text-gray-700 text-sm font-bold mb-2">Label Name</label>
                    <input type="text" name="label_name" id="label_name" value="{{ old('label_name', $customer->label_name) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="date_of_birth" class="block text-gray-700 text-sm font-bold mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $customer->date_of_birth ? $customer->date_of_birth->format('Y-m-d') : '') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="anniversary_date" class="block text-gray-700 text-sm font-bold mb-2">Anniversary Date</label>
                    <input type="date" name="anniversary_date" id="anniversary_date" value="{{ old('anniversary_date', $customer->anniversary_date ? $customer->anniversary_date->format('Y-m-d') : '') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <!-- Contact Information -->
            <div>
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                
                <div class="mb-4">
                    <label for="mobile" class="block text-gray-700 text-sm font-bold mb-2">Mobile</label>
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $customer->mobile) }}" readonly
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">
                    <p class="text-gray-600 text-xs mt-1">Mobile number cannot be changed</p>
                </div>

                <div class="mb-4">
                    <label for="whatsapp" class="block text-gray-700 text-sm font-bold mb-2">WhatsApp</label>
                    <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $customer->whatsapp) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="dno" class="block text-gray-700 text-sm font-bold mb-2">DNO</label>
                    <input type="text" name="dno" id="dno" value="{{ old('dno', $customer->dno) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="street_road" class="block text-gray-700 text-sm font-bold mb-2">Street/Road</label>
                    <input type="text" name="street_road" id="street_road" value="{{ old('street_road', $customer->street_road) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="address2" class="block text-gray-700 text-sm font-bold mb-2">Address Line 2</label>
                    <input type="text" name="address2" id="address2" value="{{ old('address2', $customer->address2) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="city" class="block text-gray-700 text-sm font-bold mb-2">City</label>
                    <input type="text" name="city" id="city" value="{{ old('city', $customer->city) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="district" class="block text-gray-700 text-sm font-bold mb-2">District</label>
                    <input type="text" name="district" id="district" value="{{ old('district', $customer->district) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="pincode" class="block text-gray-700 text-sm font-bold mb-2">Pincode</label>
                    <input type="text" name="pincode" id="pincode" value="{{ old('pincode', $customer->pincode) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="ms_firm_name" class="block text-gray-700 text-sm font-bold mb-2">MS/Firm Name</label>
                    <input type="text" name="ms_firm_name" id="ms_firm_name" value="{{ old('ms_firm_name', $customer->ms_firm_name) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('customer.profile') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Profile
            </button>
        </div>
    </form>
</div>
@endsection