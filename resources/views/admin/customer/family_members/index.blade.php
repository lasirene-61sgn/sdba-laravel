@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    {{-- Header Navigation Wrapper --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">
                Family Members for: <span class="text-indigo-600">{{ $customer->name }}</span>
            </h2>
            <a href="{{ route('admin.customer.index') }}" class="text-sm text-indigo-500 hover:text-indigo-700 font-medium inline-flex items-center gap-1 mt-1">
                ← Back to Customers List
            </a>
        </div>
        
        {{-- Add Family Member Button --}}
        <a href="{{ route('admin.customer.family.create', $customer->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition shadow-sm mt-4 md:mt-0">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Family Member
        </a>
    </div>

    {{-- System Success Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm mb-6">
            {{ session('success') }}
        </div>
    @endif

    {{-- Main Data List Grid Box --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Photo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Relationship</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Matrimony</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%]">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($familyMembers as $member)
                        <tr class="hover:bg-gray-50">
                            {{-- Profile Avatar Image column --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($member->image)
                                    <img src="{{ asset($member->image) }}" alt="Family Image" class="w-10 h-10 object-cover rounded-full mx-auto border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-10 h-10 bg-indigo-50 rounded-full mx-auto flex items-center justify-center text-indigo-600 text-xs font-bold uppercase">
                                        {{ substr($member->name, 0, 2) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Name and Birth Metadata --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-semibold text-gray-900 block">{{ $member->name }}</span>
                                @if($member->date_of_birth)
                                    <span class="text-xs text-gray-500 block">DOB: {{ $member->date_of_birth }}</span>
                                @endif
                            </td>

                            {{-- Relationship --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 uppercase tracking-wider">
                                    {{ $member->relationship ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Phone connection info --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $member->mobile ?? '—' }}
                            </td>

                            {{-- Gender identifier --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 capitalize">
                                {{ $member->gender ?? '—' }}
                            </td>

                            {{-- Matrimonial Registry status match flags --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ in_array($member->matrimony, [1, true, '1', 'true']) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ in_array($member->matrimony, [1, true, '1', 'true']) ? 'Yes' : 'No' }}
                                </span>
                            </td>

                            {{-- Actions Column --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex space-x-3 justify-center items-center">
                                    {{-- Edit Link --}}
                                    <a href="{{ route('admin.customer.family.edit', [$customer->id, $member->id]) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    {{-- Delete Form Component --}}
                                    <form action="{{ route('admin.customer.family.destroy', [$customer->id, $member->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this family member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <h3 class="text-sm font-medium text-gray-900">No Family Members Saved</h3>
                                <p class="text-xs text-gray-500 mt-0.5">There are no family members saved for this customer profile yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection