@extends('admin.layout.app')

@section('content')
{{-- Tailwind doesn't require a style block for the base utility classes, but if any custom styles were needed, they'd go here. --}}

<div class="container-fluid p-6 lg:p-10">
    
    {{-- Header and Back Button --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Bulk Upload Customers</h2>
        <a href="{{ route('admin.customer.index') }}" class="px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 transition duration-150 ease-in-out">
            Back to Customers
        </a>
    </div>

    {{-- Session Messages --}}
    @if (session('success'))
        <div class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
            {{ session('error') }}
        </div>
    @endif

    @if (session('errors'))
        <div class="p-4 mb-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg border-l-4 border-yellow-500 dark:bg-yellow-200 dark:text-yellow-800" role="alert">
            <strong class="font-semibold">Some rows had errors:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach(session('errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            @if(session('success_count'))
                <p class="mt-2 text-yellow-800">{{ session('success_count') }} records were imported successfully.</p>
            @endif
        </div>
    @endif

    {{-- Upload Card --}}
    <div class="bg-white shadow-xl rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <h5 class="text-xl font-semibold text-gray-800">Upload CSV File</h5>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.customers.bulk-upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-1">CSV File (.csv, .txt)</label>
                    <input type="file" 
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-gray-300 rounded-lg" 
                           id="excel_file" 
                           name="excel_file" 
                           accept=".csv,.txt" 
                           required>
                    <p class="mt-2 text-xs text-gray-500">Maximum file size: 2MB</p>
                    
                    <div class="mt-4">
                        <a href="{{ asset('templates/customer_template.csv') }}" class="inline-flex items-center px-3 py-2 border border-blue-500 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50 transition duration-150 ease-in-out" download>
                            📄 Download Sample Template (CSV)
                        </a>
                    </div>
                </div>
                
                
                <div class="p-4 mb-6 text-sm text-blue-700 bg-blue-100 rounded-lg dark:bg-blue-200 dark:text-blue-800" role="alert">
                    <strong class="font-semibold">Note:</strong> For now, only CSV files are supported. Please ensure your file follows the exact format shown above. The CSV template includes columns for both "Area" and "Village" fields. For the Village field, you can use existing village names or new villages will be automatically created.
                </div>
                
                {{-- Existing Villages --}}
                @if(isset($villages) && $villages->count() > 0)
                <div class="p-4 mb-6 text-sm bg-gray-100 rounded-lg dark:bg-gray-200" role="alert">
                    <strong class="font-semibold">Existing Villages:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($villages as $id => $name)
                            <li>{{ $name }}</li>
                        @endforeach
                    </ul>
                    <p class="mt-2">You can use these exact village names in the "Village" column of your CSV file, or enter new village names which will be automatically created. The "Area" column is for free-text area names.</p>
                </div>
                @endif
                
                {{-- Form Actions --}}
                <div class="flex justify-between mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.customer.index') }}" class="px-4 py-2 text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 transition duration-150 ease-in-out">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                        Upload Customers
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection