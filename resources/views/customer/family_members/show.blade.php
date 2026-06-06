@extends('customer.layout')

@section('title', 'Family Member Details')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Family Member Details</h2>
        <a href="{{ route('customer.family.members.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Back to Family Members
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-1">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                @if($familyMember->image)
                    <img src="{{ asset('storage/' . $familyMember->image) }}" alt="{{ $familyMember->name }}" class="rounded w-full object-cover">
                @else
                    <div class="bg-gray-200 border-2 border-dashed rounded-xl w-full h-64 flex items-center justify-center text-gray-500">
                        No Image
                    </div>
                @endif
                <h3 class="text-xl font-semibold text-gray-800 mt-4">{{ $familyMember->name }}</h3>
                @if($familyMember->relationship)
                    <p class="text-gray-600">{{ $familyMember->relationship }}</p>
                @endif
                <!-- Matrimony Status -->
                @if($familyMember->matrimony)
                    <div class="mt-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                            <svg class="mr-1.5 h-2 w-2 text-pink-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Matrimony
                        </span>
                    </div>
                @endif
            </div>
        </div>
        
        <div class="md:col-span-2">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($familyMember->mobile)
                        <div>
                            <p class="text-gray-600 text-sm">Mobile</p>
                            <p class="font-medium">{{ $familyMember->mobile }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->date_of_birth)
                        <div>
                            <p class="text-gray-600 text-sm">Date of Birth</p>
                            <p class="font-medium">{{ $familyMember->date_of_birth->format('F j, Y') }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->anniversary_date)
                        <div>
                            <p class="text-gray-600 text-sm">Anniversary Date</p>
                            <p class="font-medium">{{ $familyMember->anniversary_date->format('F j, Y') }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->gotra)
                        <div>
                            <p class="text-gray-600 text-sm">Gotra</p>
                            <p class="font-medium">{{ $familyMember->gotra }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->occupation)
                        <div>
                            <p class="text-gray-600 text-sm">Occupation</p>
                            <p class="font-medium">{{ $familyMember->occupation }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->education)
                        <div>
                            <p class="text-gray-600 text-sm">Education</p>
                            <p class="font-medium">{{ $familyMember->education }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->blood_group)
                        <div>
                            <p class="text-gray-600 text-sm">Blood Group</p>
                            <p class="font-medium">{{ $familyMember->blood_group }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->gender)
                        <div>
                            <p class="text-gray-600 text-sm">Gender</p>
                            <p class="font-medium">{{ ucfirst($familyMember->gender) }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->hobbies)
                        <div>
                            <p class="text-gray-600 text-sm">Hobbies</p>
                            <p class="font-medium">{{ $familyMember->hobbies }}</p>
                        </div>
                    @endif
                    
                    @if($familyMember->native_place)
                        <div>
                            <p class="text-gray-600 text-sm">Native Place</p>
                            <p class="font-medium">{{ $familyMember->native_place }}</p>
                        </div>
                    @endif
                </div>
                
                @if($familyMember->notes)
                    <div class="mt-4">
                        <p class="text-gray-600 text-sm">Notes</p>
                        <p class="font-medium">{{ $familyMember->notes }}</p>
                    </div>
                @endif
                
                <div class="flex space-x-2 mt-6">
                    <a href="{{ route('customer.family.members.edit', $familyMember) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Edit
                    </a>
                    
                    <form action="{{ route('customer.family.members.destroy', $familyMember) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this family member?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection