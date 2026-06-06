@extends('customer.layout')

@section('title', 'Family Members')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Family Members</h2>
        <a href="{{ route('customer.family.members.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Add Family Member
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if($familyMembers->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($familyMembers as $member)
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex justify-between items-start">
                        <div>
                            @if($member->image)
                                <img src="{{ asset('storage/' . $member->image) }}" alt="{{ $member->name }}" class="rounded w-16 h-16 object-cover mb-2">
                            @else
                                <div class="bg-gray-200 border-2 border-dashed rounded-xl w-16 h-16 mb-2 flex items-center justify-center text-gray-500">
                                    No Image
                                </div>
                            @endif
                            <h4 class="font-semibold text-gray-800">{{ $member->name }}</h4>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('customer.family.members.edit', $member) }}" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('customer.family.members.destroy', $member) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this family member?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @if($member->relationship)
                        <p class="text-gray-600 text-sm">Relationship: {{ $member->relationship }}</p>
                    @endif
                    @if($member->mobile)
                        <p class="text-gray-600 text-sm">Mobile: {{ $member->mobile }}</p>
                    @endif
                    @if($member->date_of_birth)
                        <p class="text-gray-600 text-sm">DOB: {{ $member->date_of_birth->format('d M Y') }}</p>
                    @endif
                    @if($member->anniversary_date)
                        <p class="text-gray-600 text-sm">Anniversary: {{ $member->anniversary_date->format('d M Y') }}</p>
                    @endif
                    <!-- Matrimony Status -->
                    @if($member->matrimony)
                        <div class="mt-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                <svg class="mr-1.5 h-2 w-2 text-pink-400" fill="currentColor" viewBox="0 0 8 8">
                                    <circle cx="4" cy="4" r="3" />
                                </svg>
                                Matrimony
                            </span>
                        </div>
                    @endif
                    @if($member->gender)
                        <p class="text-gray-600 text-sm mt-1">Gender: {{ ucfirst($member->gender) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-600 mb-4">You haven't added any family members yet.</p>
            <a href="{{ route('customer.family.members.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Add Your First Family Member
            </a>
        </div>
    @endif
</div>
@endsection