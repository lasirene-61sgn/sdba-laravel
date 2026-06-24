@extends('admin.layout.app')

@section('content')
<style>
    /* --- STYLES FOR PRINT (@media print) --- */

    @media print {

        @page {
            margin: 0.5cm;
            /* Small margin for browser headers, but margin:0 is better to hide them completely */
        }

        /* Hide UI elements */
        .no-print,
        .sidebar,
        .main-header,
        .main-footer,
        .mb-6,
        .print-view-controls,
        button {
            display: none !important;
        }

        /* **MENU HIDE FIX** */
        /* Hide common layout elements like sidebars, headers, footers */
        .print-hide-layout,
        .sidebar,
        .main-header,
        .main-footer,
        .breadcrumb,
        .menu,
        #menu,
        .admin-dashboard-header {
            display: none !important;
        }

        /* Ensure the main content takes full width and remove page margins for print */
        .content-wrapper,
        .container-fluid,
        .p-6,
        .md\:p-8 {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        body {
            font-size: 12px;
        }

        /* Hide the original table when printing */
        .table-responsive,
        .pagination,
        .print-view-controls {
            display: none !important;
        }

        /* Show the new contact-card layout only when printing */
        .print-only-directory-container {
            display: flex !important;
            /* Make sure it's visible */
            flex-wrap: wrap;
            width: 100%;
            margin-top: -5px;
            /* Adjust if needed to fit layout */
        }

        /* Define the two-column layout */
        .print-directory-entry {
            width: 50%;
            padding: 8px 15px;
            /* Adjust spacing */
            box-sizing: border-box;
            page-break-inside: avoid;
            /* Important: keeps entries from splitting across pages/columns */
        }

        .print-directory-entry strong {
            font-size: 14px;
            /* Slightly smaller for print */
            display: block;
            margin-bottom: 2px;
            color: #333;
            /* Dark text for print clarity */
        }

        .print-directory-entry span {
            display: block;
            line-height: 1.2;
            color: #555;
        }

        .print-directory-entry .entry-tag {
            float: right;
            font-style: italic;
            font-size: 10px;
            margin-top: -30px;
            /* Adjust position to match the image */
            margin-right: 5px;
            color: #777;
        }
    }
</style>
<div class="p-6 md:p-8">
    {{-- Header Bar and Buttons (no-print) --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 no-print print-view-controls">
        <div class="flex items-center space-x-3 mb-4 md:mb-0">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M12 10a3 3 0 100-6 3 3 0 000 6z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Members List</h2>
        </div>

        <div class="flex flex-wrap gap-2">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 transition duration-150 ease-in-out">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m0 0v2a2 2 0 002 2h2a2 2 0 002-2v-2m4-12V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v4m7 12H9"></path>
                </svg>
                Print Directory
            </button>
            <a href="{{ route('admin.customer.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add New
            </a>
            <a href="{{ route('admin.customers.bulk-upload-form') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 transition duration-150 ease-in-out">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                Bulk Upload
            </a>
            
            <a href="{{ route('admin.customer.scan-create-form') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 transition duration-150 ease-in-out shadow-sm no-print">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h.01M16 20a2 2 0 01-2 2H8a2 2 0 01-2-2V6a2 2 0 012-2h6a2 2 0 012 2v14z" />
                </svg>
                Scan & Create Customer
            </a>
        </div>
    </div>

    @if (session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-lg shadow-sm mb-6 no-print">{{ session('success') }}</div>
    @endif

    @php
    // Assuming $fieldPermissions is defined in the controller or Auth setup
    $fieldPermissions = Auth::guard('admin')->user()->customer_field_permissions ?? [];

    // Calculate colspan properly
    $baseColumns = ['id', 'village', 'area', 'created_at', 'updated_at'];
    $conditionalColumns = [
    'name' => empty($fieldPermissions) || in_array('name', $fieldPermissions),
    'image' => empty($fieldPermissions) || in_array('image', $fieldPermissions),
    'mobile' => empty($fieldPermissions) || in_array('mobile', $fieldPermissions),
    'status' => empty($fieldPermissions) || in_array('status', $fieldPermissions)
    ];

    // Count visible columns
    $visibleBaseColumns = count($baseColumns);
    $visibleConditionalColumns = count(array_filter($conditionalColumns));
    $totalColumns = $visibleBaseColumns + $visibleConditionalColumns;
    @endphp


    {{-- Main Table View (Hidden in Print) --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden no-print">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[5%]">Admin ID</th>
                        @if(empty($fieldPermissions) || in_array('name', $fieldPermissions))
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[20%]">Name</th>
                        @endif
                        @if(empty($fieldPermissions) || in_array('image', $fieldPermissions))
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-[5%]">Image</th>
                        @endif
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Village</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Area</th>
                        @if(empty($fieldPermissions) || in_array('mobile', $fieldPermissions))
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Mobile</th>
                        @endif
                        @if(empty($fieldPermissions) || in_array('status', $fieldPermissions))
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%]">Status</th>
                        @endif
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%] hidden lg:table-cell">Created At</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[15%] hidden lg:table-cell">Last Updated</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-[10%] no-print">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer->admin_customer_id }}</td>
                        @if(empty($fieldPermissions) || in_array('name', $fieldPermissions))
                        <td class="px-6 py-4 whitespace-nowrap">
                            <strong class="text-gray-800 block">{{ $customer->name }}</strong>
                            @if(empty($fieldPermissions) || in_array('father_name', $fieldPermissions))
                            <small class="text-xs text-gray-500">{{ $customer->father_name }} (S/o)</small>
                            @endif
                        </td>
                        @endif
                        @if(empty($fieldPermissions) || in_array('image', $fieldPermissions))
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($customer->image)
                            <img src="{{ asset('storage/' . $customer->image) }}" alt="Customer Image" class="w-10 h-10 object-cover rounded-md mx-auto border border-gray-200 shadow-sm">
                            @else
                            <div class="w-10 h-10 bg-gray-100 rounded-md mx-auto flex items-center justify-center text-gray-500 text-xs">N/A</div>
                            @endif
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $customer->village->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $customer->area ?? 'N/A' }}</td>
                        @if(empty($fieldPermissions) || in_array('mobile', $fieldPermissions))
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $customer->mobile }}</td>
                        @endif
                        @if(empty($fieldPermissions) || in_array('status', $fieldPermissions))
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $customer->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($customer->status) }}
                            </span>
                        </td>
                        @endif
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $customer->updated_at->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center no-print">
                            <div class="flex space-x-2 justify-center items-center">
                                <a href="{{ route('admin.customer.show', $customer->id) }}" class="text-indigo-600 hover:text-indigo-900" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.customer.edit', $customer->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.customer.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                                
                                {{-- CLEAN FIXED FAMILY MEMBERS ACTION LINK --}}
                                <a href="{{ route('admin.customer.family.index', $customer->id) }}" class="inline-flex items-center px-2 py-1 bg-teal-600 text-white text-xs font-medium rounded hover:bg-teal-700 transition" title="Family Members">
                                    Family
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $totalColumns }}" class="px-6 py-10 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1l2-2m-1 7h14a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Customers Found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Get started by adding a new customer or bulk uploading.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('admin.customer.create') }}"
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add New Customer
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination Links --}}
    <div class="flex justify-center mt-4 pagination no-print">
        {{ $customers->links() }}
    </div>


    {{-- Print Directory View (Hidden in Normal View) --}}
    <div class="print-only-directory-container" style="display: none;">
        @if(isset($allCustomers) && $allCustomers->count() >= 5000)
        <div class="p-4 bg-yellow-100 text-yellow-800 w-full mb-4">
            Note: Only the first 5000 customers are shown in this print directory due to volume. You have more customers in the system.
        </div>
        @endif
        @forelse ($allCustomers ?? $customers as $customer)
        <div class="print-directory-entry">
            <strong>
                Sha {{ $customer->name }}
                @if($customer->father_name)
                S/o {{ $customer->father_name }}
                @endif
            </strong>

            <span>Firm: {{ $customer->ms_firm_name ?? 'N/A' }}</span>

            <span>
                @if($customer->dno){{ $customer->dno }}, @endif
                {{ $customer->street_road ?? '' }}
            </span>

            <span>
                {{ $customer->city ?? 'N/A' }} - {{ $customer->pincode ?? '' }}
            </span>

            <span style="margin-top: 5px;">
                PH: {{ $customer->mobile ?? 'N/A' }}
                @if($customer->whatsapp)
                , W: {{ $customer->whatsapp }}
                @endif
            </span>

            <span>
                Edu: {{ $customer->education ?? 'N/A' }} |
                Occ: {{ $customer->occupation ?? 'N/A' }} |
                BG: {{ $customer->blood_group ?? 'N/A' }}
            </span>
            <span>
                Hobbies: {{ $customer->hobbies ?? 'N/A' }}
            </span>

            @if ($customer->village)
            <span class="entry-tag">({{ $customer->village->name }})</span>
            @elseif($customer->area)
            <span class="entry-tag">({{ $customer->area }})</span>
            @endif
        </div>
        @empty
        <div class="print-directory-entry w-full">
            No customers found for the print directory.
        </div>
        @endforelse
    </div>
</div>
@endsection