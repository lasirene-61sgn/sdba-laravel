@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">

    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M12 10a3 3 0 100-6 3 3 0 000 6z" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Committee Members List</h2>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
        {{ session('error') }}
    </div>
    @endif

    {{-- Tailwind Styled Bulk Upload Error Box --}}
    @if(session('error_list'))
    <div class="bg-amber-50 border border-amber-200 text-amber-800 p-5 rounded-lg shadow-sm mb-6" role="alert">
        <div class="flex items-center space-x-2 text-amber-900 font-bold mb-2">
            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span>Some rows were skipped during bulk upload:</span>
        </div>
        <ul class="list-disc list-inside space-y-1 text-sm text-amber-800 max-h-40 overflow-y-auto pl-2">
            @foreach(session('error_list') as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">

        {{-- Controls Header --}}
        <div class="p-4 md:p-5 border-b border-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex flex-wrap gap-2">
                {{-- Add New Member Button --}}
                <a href="{{ route('admin.committee.create') }}"
                    class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add New Member</span>
                </a>

                {{-- BULK UPLOAD BUTTON --}}
                <a href="{{ route('committee.bulk_upload_form') }}"
                    class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-lg shadow-sm transition duration-200 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Bulk Upload CSV</span>
                </a>
            </div>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 hidden md:table-header-group">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Image</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Role / Phone</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Order</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12 hidden lg:table-cell">Created At</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12 hidden lg:table-cell">Last Updated</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($people as $person)

                    {{-- Mobile View: Stacked Card --}}
                    <tr class="md:hidden block border-b border-gray-200 last:border-b-0">
                        <td class="p-4 block">
                            <div class="flex items-start space-x-4">
                                {{-- Image --}}
                                <div class="flex-shrink-0">
                                    @if ($person->image_path)
                                    <img src="{{ Storage::url($person->image_path) }}" alt="{{ $person->name }}" class="w-16 h-16 object-cover rounded-full border border-gray-200 shadow-sm">
                                    @else
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 text-xs border border-dashed">No Image</div>
                                    @endif
                                </div>

                                {{-- Details --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <strong class="text-lg font-semibold text-gray-800">{{ $person->name }}</strong>
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $person->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ ucfirst($person->status) }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 mt-1">Role: <strong class="text-gray-700">{{ $person->post_name }}</strong></p>
                                    <p class="text-sm text-gray-600">Phone: {{ $person->phone }}</p>
                                    <p class="text-sm text-gray-600">Display Order: <strong class="text-gray-700">{{ $person->sort_order }}</strong></p>

                                    <div class="mt-2 text-xs text-gray-500 space-y-1">
                                        <p><span class="font-medium text-gray-700">Created:</span> {{ $person->created_at->format('Y-m-d H:i') }}</p>
                                        <p><span class="font-medium text-gray-700">Updated:</span> {{ $person->updated_at->format('Y-m-d H:i') }}</p>
                                    </div>

                                    {{-- Actions on Mobile --}}
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end space-x-3">
                                        <a href="{{ route('admin.committee.edit', $person->id) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                        <form action="{{ route('admin.committee.destroy', $person->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>

                    {{-- Desktop View: Table Row --}}
                    <tr class="hover:bg-gray-50 hidden md:table-row">
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if ($person->image_path)
                            <img src="{{ Storage::url($person->image_path) }}" alt="{{ $person->name }}" class="w-10 h-10 object-cover rounded-md mx-auto border border-gray-200">
                            @else
                            <div class="w-10 h-10 bg-gray-100 rounded-md mx-auto flex items-center justify-center text-gray-500 text-xs border border-dashed">N/A</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $person->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            <span class="block font-semibold text-gray-800">{{ $person->post_name }}</span>
                            <span class="block text-xs text-gray-500">{{ $person->phone }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $person->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($person->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700 font-medium">
                            {{ $person->sort_order }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $person->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $person->updated_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                            <div class="flex space-x-2 justify-center">
                                <a href="{{ route('admin.committee.edit', $person->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.committee.destroy', $person->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this member?');">
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
                        <td colspan="9" class="px-6 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1l2-2m-1 7h14a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Committee Members Found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Click "Add New Member" or upload a batch file to start.
                            </p>
                            <div class="mt-6 flex justify-center space-x-3">
                                <a href="{{ route('admin.committee.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add New Member
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Links --}}
        @if ($people->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $people->links() }}
        </div>
        @endif
    </div>
</div>
@endsection