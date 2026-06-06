@extends('superadmin.layout.app')

@section('title', 'Manage Admins')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Header Section -->
    <div class="p-5 md:p-6 border-b border-gray-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800 tracking-tight">Admin Management</h2>
                <p class="text-sm text-gray-500 mt-1">Manage all admin accounts and permissions</p>
            </div>
            <a href="{{ route('superadmin.admins.create') }}" 
               class="inline-flex items-center justify-center space-x-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2.5 px-5 rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>Add New Admin</span>
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('status'))
        <div class="mx-5 md:mx-6 mt-5">
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg flex items-start space-x-3 shadow-sm">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <p class="font-medium">Success!</p>
                    <p class="text-sm mt-1">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Table Section - Desktop View -->
    <div class="hidden lg:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-3.5 px-6 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        ID
                    </th>
                    <th class="py-3.5 px-6 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Admin Details
                    </th>
                    <th class="py-3.5 px-6 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Company
                    </th>
                    <th class="py-3.5 px-6 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Created Date
                    </th>
                    <th class="py-3.5 px-6 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($admins as $admin)
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="py-4 px-6 whitespace-nowrap">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-700 text-sm font-semibold">
                            {{ $admin->id }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $admin->name }}</p>
                                <p class="text-sm text-gray-500">{{ $admin->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $admin->company_name }}
                        </span>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-sm text-gray-600">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $admin->created_at->format('M d, Y') }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center space-x-3">
                            <a href="{{ route('superadmin.admins.edit', $admin) }}" 
                               class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-800 font-medium transition-colors"
                               title="Edit Admin">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('superadmin.admins.destroy', $admin) }}" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('Are you sure you want to delete this admin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center space-x-1 text-red-600 hover:text-red-800 font-medium transition-colors"
                                        title="Delete Admin">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-gray-500 font-medium">No admins found</p>
                        <p class="text-sm text-gray-400 mt-1">Get started by creating a new admin account</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Card View - Mobile & Tablet -->
    <div class="lg:hidden p-4 md:p-5 space-y-4">
        @forelse($admins as $admin)
        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow duration-200">
            <!-- Admin Header -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white font-bold flex-shrink-0">
                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $admin->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $admin->email }}</p>
                    </div>
                </div>
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary-100 text-primary-700 text-xs font-bold flex-shrink-0">
                    {{ $admin->id }}
                </span>
            </div>

            <!-- Admin Details -->
            <div class="space-y-2 mb-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Company:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $admin->company_name }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Created:</span>
                    <span class="text-gray-700 font-medium">{{ $admin->created_at->format('M d, Y') }}</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2 pt-3 border-t border-gray-100">
                <a href="{{ route('superadmin.admins.edit', $admin) }}" 
                   class="flex-1 inline-flex items-center justify-center space-x-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 px-4 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Edit</span>
                </a>
                <form action="{{ route('superadmin.admins.destroy', $admin) }}" 
                      method="POST" 
                      class="flex-1"
                      onsubmit="return confirm('Are you sure you want to delete this admin?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="w-full inline-flex items-center justify-center space-x-2 bg-red-50 hover:bg-red-100 text-red-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-gray-500 font-medium">No admins found</p>
            <p class="text-sm text-gray-400 mt-1">Get started by creating a new admin account</p>
        </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="px-5 md:px-6 py-4 border-t border-gray-100 bg-gray-50">
        <div class="flex items-center justify-between">
            <div class="hidden sm:block text-sm text-gray-600">
                Showing <span class="font-semibold">{{ $admins->firstItem() ?? 0 }}</span> to 
                <span class="font-semibold">{{ $admins->lastItem() ?? 0 }}</span> of 
                <span class="font-semibold">{{ $admins->total() }}</span> results
            </div>
            <div class="flex-1 sm:flex-none">
                {{ $admins->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Custom Pagination Styling -->
<style>
    /* Tailwind pagination styling */
    .pagination {
        display: flex;
        gap: 0.25rem;
        align-items: center;
    }
    
    .pagination a,
    .pagination span {
        padding: 0.5rem 0.75rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    
    .pagination a {
        color: #4B5563;
        background-color: white;
        border: 1px solid #E5E7EB;
    }
    
    .pagination a:hover {
        background-color: #F3F4F6;
        color: #1F2937;
    }
    
    .pagination .active span {
        background-color: #f97316;
        color: white;
        border: 1px solid #f97316;
    }
    
    .pagination .disabled span {
        color: #D1D5DB;
        cursor: not-allowed;
        background-color: #F9FAFB;
        border: 1px solid #E5E7EB;
    }
    
    @media (max-width: 640px) {
        .pagination a,
        .pagination span {
            padding: 0.375rem 0.625rem;
            font-size: 0.8125rem;
        }
    }
</style>
@endsection