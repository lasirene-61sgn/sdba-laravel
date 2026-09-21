@extends('admin.layout.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white px-6 py-5 border-b border-gray-100">
            <div>
                <h5 class="text-xl font-bold text-gray-900">Social Media Settings</h5>
                <p class="text-sm text-gray-500 mt-1">Manage external platform URLs linked to this admin workspace profile.</p>
            </div>
            <div>
                @if(!$link)
                    <a href="{{ route('admin.links.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Social Links
                    </a>
                @else
                    <a href="{{ route('admin.links.edit', $link->id) }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-amber-900 bg-amber-100 hover:bg-amber-200 transition-colors duration-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        Edit Links
                    </a>
                @endif
            </div>
        </div>

        <div class="p-6">
            @if(session('success'))
                <div id="success-alert" class="flex items-center justify-between p-4 mb-6 text-sm text-green-800 bg-green-50 border border-green-100 rounded-xl transition-all duration-300" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2.5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="document.getElementById('success-alert').remove()" class="text-green-500 hover:text-green-700 p-1 rounded-lg hover:bg-green-100 transition-colors focus:outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"/></svg>
                    </button>
                </div>
            @endif

            @if($link)
                <div class="overflow-x-auto border border-gray-200 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-200 align-middle">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-1/4">Platform</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Configured Link URL</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">WhatsApp</td>
                                <td class="px-6 py-4 text-sm break-all">
                                    @if($link->whatsapp_link)
                                        <a href="{{ $link->whatsapp_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 font-medium">
                                            {{ $link->whatsapp_link }}
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Not Set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Facebook</td>
                                <td class="px-6 py-4 text-sm break-all">
                                    @if($link->facebook_link)
                                        <a href="{{ $link->facebook_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 font-medium">
                                            {{ $link->facebook_link }}
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Not Set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Email Contact</td>
                                <td class="px-6 py-4 text-sm break-all">
                                    @if($link->email_link)
                                        <a href="mailto:{{ $link->email_link }}" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 font-medium">
                                            {{ $link->email_link }}
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Not Set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Twitter (X)</td>
                                <td class="px-6 py-4 text-sm break-all">
                                    @if($link->twitter_link)
                                        <a href="{{ $link->twitter_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 font-medium">
                                            {{ $link->twitter_link }}
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Not Set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">Instagram</td>
                                <td class="px-6 py-4 text-sm break-all">
                                    @if($link->instagram_link)
                                        <a href="{{ $link->instagram_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 font-medium">
                                            {{ $link->instagram_link }}
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Not Set</span>
                                    @endif
                                </td>
                            </tr>
                            <tr class="hover:bg-gray-50/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">LinkedIn</td>
                                <td class="px-6 py-4 text-sm break-all">
                                    @if($link->linkedin_link)
                                        <a href="{{ $link->linkedin_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 font-medium">
                                            {{ $link->linkedin_link }}
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">Not Set</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <form action="{{ route('admin.links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all configuration links? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center justify-center px-3.5 py-2 text-xs font-semibold text-red-600 hover:text-white border border-red-200 hover:border-red-600 hover:bg-red-600 transition-all duration-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Reset All Links
                        </button>
                    </form>
                </div>
            @else
                <div class="text-center py-16 px-4 border-2 border-dashed border-gray-200 rounded-xl max-w-md mx-auto my-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">No profile links configured</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-xs mx-auto">Configure your platform profiles so that app members can connect with your workspace listings easily.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection