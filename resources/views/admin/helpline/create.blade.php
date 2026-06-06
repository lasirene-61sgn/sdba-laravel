@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center space-x-3 mb-6">
            <a href="{{ route('admin.helpline.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Add New Helpline</h2>
        </div>

        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.helpline.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Enter helpline name (e.g. Police, Ambulance)">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="heading_name" class="block text-sm font-semibold text-gray-700 mb-2">Heading Name</label>
                    <input type="text" name="heading_name" id="heading_name" value="{{ old('heading_name') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Enter heading name (e.g. Apollo Hospital, City Police Station)">
                    @error('heading_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>


                <!-- Multiple Inputs Section -->
                @php
                    $fields = [
                        'mobile_numbers' => 'Mobile Numbers',
                        'whatsapp_numbers' => 'WhatsApp Numbers',
                        'emails' => 'Emails',
                        'locations' => 'Locations'
                    ];
                @endphp

                @foreach($fields as $name => $label)
                <div class="field-group" data-name="{{ $name }}">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $label }}</label>
                    <div class="inputs-container space-y-2">
                        <div class="flex space-x-2">
                            <input type="{{ $name == 'emails' ? 'email' : 'text' }}" name="{{ $name }}[]" 
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                   placeholder="Enter {{ strtolower($label) }}">
                            <button type="button" class="add-field bg-blue-50 text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="pt-4 border-t border-gray-100 flex justify-end space-x-3">
                    <a href="{{ route('admin.helpline.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">Cancel</a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 shadow-md transition">Create Helpline</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.add-field').forEach(button => {
            button.addEventListener('click', function() {
                const container = this.closest('.field-group').querySelector('.inputs-container');
                const fieldName = this.closest('.field-group').dataset.name;
                const fieldType = fieldName === 'emails' ? 'email' : 'text';
                
                const div = document.createElement('div');
                div.className = 'flex space-x-2';
                div.innerHTML = `
                    <input type="${fieldType}" name="${fieldName}[]" 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                           placeholder="Enter ${fieldName.replace('_', ' ')}">
                    <button type="button" class="remove-field bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                `;
                container.appendChild(div);
            });
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.remove-field')) {
                e.target.closest('.flex').remove();
            }
        });
    });
</script>
@endsection
@endsection
