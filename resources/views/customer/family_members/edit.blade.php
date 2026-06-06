@extends('customer.layout')

@section('title', 'Edit Family Member')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Family Member</h2>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('customer.family.members.update', $familyMember) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $familyMember->name) }}" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Image</label>
                    <input type="file" name="image" id="image"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    @if($familyMember->image)
                        <div class="mt-2">
                            <p class="text-gray-600 text-sm">Current Image:</p>
                            <img src="{{ asset('storage/' . $familyMember->image) }}" alt="{{ $familyMember->name }}" class="w-16 h-16 object-cover rounded">
                        </div>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="relationship" class="block text-gray-700 text-sm font-bold mb-2">Relationship</label>
                    <input type="text" name="relationship" id="relationship" value="{{ old('relationship', $familyMember->relationship) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="mobile" class="block text-gray-700 text-sm font-bold mb-2">Mobile</label>
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile', $familyMember->mobile) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="date_of_birth" class="block text-gray-700 text-sm font-bold mb-2">Date of Birth</label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $familyMember->date_of_birth ? $familyMember->date_of_birth->format('Y-m-d') : '') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="anniversary_date" class="block text-gray-700 text-sm font-bold mb-2">Anniversary Date</label>
                    <input type="date" name="anniversary_date" id="anniversary_date" value="{{ old('anniversary_date', $familyMember->anniversary_date ? $familyMember->anniversary_date->format('Y-m-d') : '') }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <!-- Matrimony Field -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Matrimony</label>
                    <div class="flex items-center">
                        <input type="radio" name="matrimony" id="matrimony_yes" value="1" {{ (old('matrimony', $familyMember->matrimony) == '1' || old('matrimony', $familyMember->matrimony) == true) ? 'checked' : '' }}
                            class="mr-2">
                        <label for="matrimony_yes" class="mr-4">Yes</label>
                        
                        <input type="radio" name="matrimony" id="matrimony_no" value="0" {{ (old('matrimony', $familyMember->matrimony) == '0' || old('matrimony', $familyMember->matrimony) == false || old('matrimony', $familyMember->matrimony) == null) ? 'checked' : '' }}
                            class="mr-2">
                        <label for="matrimony_no">No</label>
                    </div>
                </div>
            </div>

            <div>
                <div class="mb-4">
                    <label for="gotra" class="block text-gray-700 text-sm font-bold mb-2">Gotra</label>
                    <input type="text" name="gotra" id="gotra" value="{{ old('gotra', $familyMember->gotra) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="occupation" class="block text-gray-700 text-sm font-bold mb-2">Occupation</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $familyMember->occupation) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="education" class="block text-gray-700 text-sm font-bold mb-2">Education</label>
                    <input type="text" name="education" id="education" value="{{ old('education', $familyMember->education) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="blood_group" class="block text-gray-700 text-sm font-bold mb-2">Blood Group</label>
                    <input type="text" name="blood_group" id="blood_group" value="{{ old('blood_group', $familyMember->blood_group) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="hobbies" class="block text-gray-700 text-sm font-bold mb-2">Hobbies</label>
                    <input type="text" name="hobbies" id="hobbies" value="{{ old('hobbies', $familyMember->hobbies) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div class="mb-4">
                    <label for="native_place" class="block text-gray-700 text-sm font-bold mb-2">Native Place</label>
                    <input type="text" name="native_place" id="native_place" value="{{ old('native_place', $familyMember->native_place) }}"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                
                <!-- Gender Field -->
                <div class="mb-4">
                    <label for="gender" class="block text-gray-700 text-sm font-bold mb-2">Gender</label>
                    <select name="gender" id="gender"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $familyMember->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $familyMember->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $familyMember->gender) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes', $familyMember->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-6">
            <a href="{{ route('customer.family.members.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Update Family Member
            </button>
        </div>
    </form>
</div>
@endsection