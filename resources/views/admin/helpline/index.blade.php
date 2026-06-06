@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Helpline Management</h2>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        
        <div class="p-4 md:p-5 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('admin.helpline.create') }}" 
               class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Add New Helpline</span>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 hidden md:table-header-group">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mobile Numbers</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">WhatsApp</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Emails</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($helplines as $groupName => $groupItems)
                        {{-- Group Header --}}
                        <tr class="bg-gray-50">
                            <td colspan="5" class="px-6 py-3 text-sm font-bold text-gray-900 border-b border-gray-200">
                                {{ $groupName ?: 'No Name' }}
                            </td>
                        </tr>

                        @foreach($groupItems as $helpline)
                        {{-- Mobile View --}}
                        <tr class="md:hidden block border-b border-gray-100 last:border-b-0">
                            <td class="p-4 block">
                                <div class="flex items-start justify-between">
                                    <strong class="text-md font-semibold text-gray-700">{{ $helpline->heading_name }}</strong>
                                </div>
                                
                                <div class="mt-2 text-sm text-gray-600 space-y-1">
                                    <p><span class="font-medium text-gray-700">Mobile:</span> {{ implode(', ', $helpline->mobile_numbers ?? []) }}</p>
                                    <p><span class="font-medium text-gray-700">WhatsApp:</span> {{ implode(', ', $helpline->whatsapp_numbers ?? []) }}</p>
                                    <p><span class="font-medium text-gray-700">Emails:</span> {{ implode(', ', $helpline->emails ?? []) }}</p>
                                </div>

                                <div class="mt-4 flex space-x-3 justify-end border-t border-gray-100 pt-3">
                                    <a href="{{ route('admin.helpline.edit', $helpline->id) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</a>
                                    <form action="{{ route('admin.helpline.destroy', $helpline->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this helpline?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Desktop View --}}
                        <tr class="hover:bg-gray-50 hidden md:table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 pl-10">
                                {{ $helpline->heading_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ implode(', ', $helpline->mobile_numbers ?? []) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ implode(', ', $helpline->whatsapp_numbers ?? []) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ implode(', ', $helpline->emails ?? []) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex space-x-2 justify-center">
                                    <a href="{{ route('admin.helpline.edit', $helpline->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.helpline.destroy', $helpline->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this helpline?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1l2-2m-1 7h14a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Helpline Found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by adding a new helpline entry.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.helpline.create') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Add New Helpline
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
