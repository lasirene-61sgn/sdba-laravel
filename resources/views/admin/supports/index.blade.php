@extends('admin.layout.app')

@section('title', 'All Support Entries')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.466 9.597 5 8.25 5c-4.418 0-8 3.582-8 8s3.582 8 8 8c1.347 0 2.582-.466 3.75-1.253M12 6.253C13.168 5.466 14.403 5 15.75 5c4.418 0 8 3.582 8 8s-3.582 8-8 8c-1.347 0-2.582-.466-3.75-1.253" /></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">All Support Entries</h2>
    </div>
    
    {{-- Updated Tab-Style Category Filter --}}
    <div class="mb-6 border-b border-gray-200">
        <div class="flex flex-wrap -mb-px text-sm font-medium text-center">
            {{-- "All" Tab --}}
            <a href="{{ route('admin.supports.index') }}" 
               class="inline-block p-4 border-b-2 rounded-t-lg transition-colors duration-200 {{ !request('category_id') ? 'text-blue-600 border-blue-600 active bg-white' : 'border-transparent hover:text-gray-600 hover:border-gray-300 text-gray-500' }}">
               All
            </a>

            {{-- Category Tabs --}}
            @foreach($supportCategories as $category)
                <a href="{{ route('admin.supports.index', ['category_id' => $category->id]) }}" 
                   class="inline-block p-4 border-b-2 rounded-t-lg transition-colors duration-200 {{ request('category_id') == $category->id ? 'text-blue-600 border-blue-600 active bg-white' : 'border-transparent hover:text-gray-600 hover:border-gray-300 text-gray-500' }}">
                   {{ $category->name }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm mb-6" role="alert">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        
        {{-- Controls Header --}}
        <div class="p-4 md:p-5 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('admin.supports.create') }}" 
               class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Add New Support Entry</span>
            </a>
        </div>
        
        {{-- Table Content --}}
        @if ($supports->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1l2-2m-1 7h14a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No Support Entries Found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Click "Add New Support Entry" to start.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 hidden md:table-header-group">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">ID</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">Image</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Name & Phone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Type / Category</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-2/12">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($supports as $support)
                        
                        {{-- Mobile View --}}
                        <tr class="md:hidden block border-b border-gray-200 last:border-b-0">
                            <td class="p-4 block">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        @if ($support->image)
                                            <img src="{{ asset('storage/' . $support->image) }}" alt="{{ $support->name }}" class="w-12 h-12 object-cover rounded-full border border-gray-200 shadow-sm">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-xs">N/A</div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 min-w-0">
                                        <div class="text-base font-semibold text-gray-800">{{ $support->name }} (#{{ $support->id }})</div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $support->phone }}</p>
                                        
                                        <div class="mt-2 text-sm space-y-1">
                                            <p><span class="font-medium text-gray-700">Type:</span> {{ $support->supportType->name ?? 'N/A' }}</p>
                                            <p><span class="font-medium text-gray-700">Category:</span> {{ $support->supportCategory->name ?? 'N/A' }}</p>
                                        </div>
                                        
                                        <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end space-x-3">
                                            <a href="{{ route('admin.supports.edit', $support) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">Edit</a>
                                            <form action="{{ route('admin.supports.destroy', $support) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- Desktop View --}}
                        <tr class="hover:bg-gray-50 hidden md:table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">{{ $support->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($support->image)
                                    <img src="{{ asset('storage/' . $support->image) }}" alt="{{ $support->name }}" class="w-10 h-10 object-cover rounded-full mx-auto">
                                @else
                                    <div class="w-10 h-10 bg-gray-100 rounded-full mx-auto flex items-center justify-center text-gray-500 text-xs">N/A</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <strong class="text-gray-800 block">{{ $support->name }}</strong>
                                <span class="text-sm text-gray-500">{{ $support->phone }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <span class="block text-xs font-semibold text-gray-600">Type: {{ $support->supportType->name ?? 'N/A' }}</span>
                                <span class="block text-xs text-gray-500">Cat: {{ $support->supportCategory->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex space-x-2 justify-center">
                                    <a href="{{ route('admin.supports.edit', $support) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.supports.destroy', $support) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if ($supports->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $supports->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection