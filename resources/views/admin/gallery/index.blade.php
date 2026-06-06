@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Gallery Management</h2>
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

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        
        {{-- Controls Header --}}
        <div class="p-4 md:p-5 border-b border-gray-100 flex items-center justify-between flex-wrap">
            <a href="{{ route('admin.gallery.create') }}" 
               class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 text-sm mb-2 sm:mb-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span>Add New Gallery Item</span>
            </a>
            
            <div class="flex space-x-3 mt-2 sm:mt-0">
                <button id="bulk-delete-btn" class="inline-flex items-center justify-center space-x-2 bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 text-sm disabled:opacity-50" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    <span>Bulk Delete</span>
                </button>
                <button id="bulk-print-btn" class="inline-flex items-center justify-center space-x-2 bg-blue-400 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 text-sm disabled:opacity-50" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6m-3-3v3" /></svg>
                    <span>Bulk Print</span>
                </button>
            </div>
        </div>

        {{-- Table Content --}}
        <form id="bulk-delete-form" action="#" method="POST">
            @csrf
            @method('DELETE')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 hidden md:table-header-group">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Image(s)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Images Count</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 hidden lg:table-cell">Created At</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32 hidden lg:table-cell">Last Updated</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($galleryItems as $item)
                        {{-- Mobile view: Stacked card --}}
                        <tr class="md:hidden block border-b border-gray-200 last:border-b-0">
                            <td class="p-4 block">
                                <div class="flex items-start space-x-3">
                                    <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="item-checkbox mt-1 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    
                                    {{-- Image & Title --}}
                                    <div class="flex-shrink-0">
                                        @if($item->image_paths)
                                            <img src="{{ asset('storage/' . $item->image_paths[0]) }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover rounded-md border border-gray-200 shadow-sm">
                                            @if(count($item->image_paths) > 1)
                                                <span class="text-xs text-gray-500 mt-1 block">+{{ count($item->image_paths) - 1 }} more</span>
                                            @endif
                                        @else
                                            <div class="flex items-center justify-center w-16 h-16 bg-gray-100 border-2 border-dashed border-gray-300 rounded-md text-gray-400">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="text-lg font-semibold text-gray-800">{{ $item->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($item->description, 50) }}</div>
                                        <div class="mt-2 space-y-1">
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-700">Count:</span> 
                                                <span class="text-gray-600">{{ $item->image_paths ? count($item->image_paths) : 0 }}</span>
                                            </div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-700">Status:</span> 
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $item->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions on Mobile --}}
                                <div class="mt-4 flex space-x-3 justify-end">
                                    <a href="{{ route('admin.gallery.edit', $item) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">Edit</a>
                                    <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Desktop View: Table Row --}}
                        <tr class="hover:bg-gray-50 hidden md:table-row">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="ids[]" value="{{ $item->id }}" class="item-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->image_paths)
                                    <div class="flex flex-col items-center">
                                        @foreach (array_slice($item->image_paths, 0, 1) as $imagePath)
                                            <img src="{{ asset('storage/' . $imagePath) }}" alt="{{ $item->title }}" class="w-16 h-16 object-cover rounded-md border border-gray-200 shadow-sm">
                                        @endforeach
                                        @if(count($item->image_paths) > 1)
                                            <span class="text-xs text-gray-500 mt-1 block">+{{ count($item->image_paths) - 1 }} more</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center justify-center w-16 h-16 bg-gray-100 border-2 border-dashed border-gray-300 rounded-md text-gray-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 max-w-sm">
                                <strong class="text-gray-800 font-semibold">{{ $item->title }}</strong>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($item->description, 70) }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-center">
                                {{ $item->image_paths ? count($item->image_paths) : 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $item->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">{{ $item->updated_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex space-x-2 justify-center">
                                    <a href="{{ route('admin.gallery.edit', $item) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        {{-- Mobile & Desktop Empty State --}}
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1l2-2m-1 7h14a2 2 0 002-2V5a2 2 0 00-2-2H4a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No Gallery Items</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Get started by adding a new gallery item.
                                </p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.gallery.create') }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                        Add New Gallery Item
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    
    {{-- Pagination --}}
    <div class="mt-5">
        {{ $galleryItems->links() }}
    </div>
</div>

<script>
    // The JavaScript remains the same as it manages functionality, not styling.
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.item-checkbox');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const bulkPrintBtn = document.getElementById('bulk-print-btn');
        const bulkDeleteForm = document.getElementById('bulk-delete-form');

        function toggleBulkButtons() {
            const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
            const hasChecked = checkedBoxes.length > 0;
            
            bulkDeleteBtn.disabled = !hasChecked;
            bulkPrintBtn.disabled = !hasChecked;
            
            if (hasChecked) {
                bulkDeleteForm.action = "{{ route('admin.gallery.bulk-delete') }}";
            } else {
                bulkDeleteForm.action = "#";
            }
            
            const allChecked = checkedBoxes.length === checkboxes.length && checkboxes.length > 0;
            selectAll.checked = allChecked;
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
            toggleBulkButtons();
        });

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkButtons);
        });

        bulkDeleteBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete the selected items? This action cannot be undone.')) {
                bulkDeleteForm.submit();
            }
        });

        bulkPrintBtn.addEventListener('click', function() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Gallery Items Print</title>');
            printWindow.document.write('<style>body { font-family: sans-serif; padding: 20px; } .item { border-bottom: 1px solid #eee; padding: 15px 0; } .item h3 { margin-top: 0; font-size: 1.2em; } .item img { max-width: 150px; height: 150px; object-fit: cover; margin-right: 10px; display: inline-block; border-radius: 4px; }</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h1>Selected Gallery Items</h1>');

            document.querySelectorAll('.item-checkbox:checked').forEach(cb => {
                const row = cb.closest('tr');
                // Check if it's the mobile stacked view or desktop view to find content
                let title, imageContainer;
                if (row.classList.contains('md:hidden')) {
                    title = row.querySelector('.text-lg').textContent;
                    imageContainer = row.querySelector('.flex-shrink-0');
                } else {
                    title = row.querySelector('td:nth-child(3) strong').textContent;
                    imageContainer = row.querySelector('td:nth-child(2)');
                }
                
                let imagesHtml = '';
                imageContainer.querySelectorAll('img').forEach(img => {
                     imagesHtml += `<img src="${img.src}" alt="${title}">`;
                });
                
                printWindow.document.write(`<div class="item"><h3>${title}</h3><div class="images">${imagesHtml}</div></div>`);
            });
            
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });

        toggleBulkButtons();
    });
</script>
@endsection