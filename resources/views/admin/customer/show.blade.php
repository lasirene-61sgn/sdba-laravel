@extends('admin.layout.app')

@section('content')
<style>
/* Custom Print Styles (Converted to be compatible with Tailwind structure) */
@media print {
    /* Hiding elements marked as 'no-print' */
    .no-print {
        display: none !important;
    }
    
    /* Setting base font size for print */
    body {
        font-size: 12px;
    }
    
    /* Applying border to cards for print (replacing box-shadow: none and adding a border) */
    .card-print {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
    }
    
    /* Setting font size for headings on print */
    h2, h5, h6 {
        font-size: 16px;
    }
    
    /* Adjusting padding for table cells on print */
    .table-print td, .table-print th {
        padding: 4px;
    }
    
    /* Ensure background is white for tables and badges on print */
    .bg-success, .bg-warning {
        background-color: transparent !important;
        color: #000 !important;
        border: 1px solid #000;
        padding: 2px 4px;
    }
    
    /* Ensure text color is black for print visibility */
    .text-gray-700, .text-muted {
        color: #000 !important;
    }
}
</style>

<div class="container-fluid p-6 lg:p-10">
    
    {{-- Header and Action Buttons --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 space-y-3 md:space-y-0">
        <h2 class="text-3xl font-bold text-gray-800">👤 Customer Details</h2>
        <div class="flex space-x-3 no-print">
            <button onclick="window.print()" class="flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                Print
            </button>
            <a href="{{ route('admin.customer.edit', $customer->id) }}" class="flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-gray-900 bg-yellow-400 hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-400 transition duration-150 ease-in-out">
                Edit
            </a>
            <a href="{{ route('admin.customer.index') }}" class="flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                Back to Customers
            </a>
        </div>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
            {{ session('success') }}
        </div>
    @endif

    {{-- Customer Card --}}
    <div class="card-print bg-white shadow-xl rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h5 class="text-xl font-semibold text-gray-800">{{ $customer->name }}</h5>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                {{-- Personal Information Column --}}
                <div>
                    <h6 class="text-lg font-medium text-gray-700 mb-4">👤 Personal Information</h6>
                    
                    @if($customer->image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $customer->image) }}" alt="Customer Image" class="w-48 h-auto object-cover rounded-lg border border-gray-200 shadow-sm">
                        </div>
                    @endif
                    
                    <table class="w-full table-auto table-print text-sm text-gray-700">
                        <tbody>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold w-1/3">ID:</td>
                                <td class="py-2">{{ $customer->admin_customer_id }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold w-1/3">Name:</td>
                                <td class="py-2">{{ $customer->name }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Father's Name:</td>
                                <td class="py-2">{{ $customer->father_name ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Gotra:</td>
                                <td class="py-2">{{ $customer->gotra ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Label Name:</td>
                                <td class="py-2">{{ $customer->label_name ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Date of Birth:</td>
                                <td class="py-2">{{ $customer->date_of_birth ? date('d M Y', strtotime($customer->date_of_birth)) : 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Anniversary Date:</td>
                                <td class="py-2">{{ $customer->anniversary_date ? date('d M Y', strtotime($customer->anniversary_date)) : 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Education:</td>
                                <td class="py-2">{{ $customer->education ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Occupation:</td>
                                <td class="py-2">{{ $customer->occupation ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Blood Group:</td>
                                <td class="py-2">{{ $customer->blood_group ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Hobbies:</td>
                                <td class="py-2">{{ $customer->hobbies ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Native Place:</td>
                                <td class="py-2">{{ $customer->native_place ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Status:</td>
                                <td class="py-2">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $customer->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($customer->status) }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Contact and Address Column --}}
                <div>
                    {{-- Contact Information --}}
                    <h6 class="text-lg font-medium text-gray-700 mb-4">📞 Contact Information</h6>
                    <table class="w-full table-auto table-print text-sm text-gray-700 mb-6">
                        <tbody>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold w-1/3">Mobile:</td>
                                <td class="py-2">{{ $customer->mobile ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">WhatsApp:</td>
                                <td class="py-2">{{ $customer->whatsapp ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Address Information --}}
                    <h6 class="text-lg font-medium text-gray-700 mb-4">📍 Address Information</h6>
                    <table class="w-full table-auto table-print text-sm text-gray-700">
                        <tbody>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold w-1/3">District:</td>
                                <td class="py-2">{{ $customer->district ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">MS/Firm Name:</td>
                                <td class="py-2">{{ $customer->ms_firm_name ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">DNO:</td>
                                <td class="py-2">{{ $customer->dno ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Street/Road:</td>
                                <td class="py-2">{{ $customer->street_road ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Address Line 2:</td>
                                <td class="py-2">{{ $customer->address2 ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">City:</td>
                                <td class="py-2">{{ $customer->city ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Pincode:</td>
                                <td class="py-2">{{ $customer->pincode ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Area:</td>
                                <td class="py-2">{{ $customer->area ?? 'N/A' }}</td>
                            </tr>
                            <tr class="border-t border-gray-100">
                                <td class="py-2 pr-4 font-bold">Village:</td>
                                <td class="py-2">{{ $customer->village->name ?? 'N/A' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        {{-- Card Footer --}}
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            <small class="text-xs text-gray-500">
                Created: {{ $customer->created_at->format('d M Y, H:i') }} | 
                Last Updated: {{ $customer->updated_at->format('d M Y, H:i') }}
            </small>
        </div>
    </div>
</div>
@endsection