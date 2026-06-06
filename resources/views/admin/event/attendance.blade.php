@extends('admin.layout.app')

@section('content')
<div class="p-6 md:p-8">
    
    <div class="flex items-center space-x-3 mb-6">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Attendance for "{{ $event->name }}"</h2>
    </div>
    
    {{-- Top Actions --}}
    <div class="mb-6 flex justify-between items-center print:hidden">
        <a href="{{ route('admin.event.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to Events
        </a>
        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out" onclick="window.print()">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m0 0v2a2 2 0 002 2h4a2 2 0 002-2v-2m-4-10H9a2 2 0 00-2 2v4h10v-4a2 2 0 00-2-2z"></path></svg>
            Print Report
        </button>
    </div>
    
    {{-- Event Information Card --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-3">Event Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-6 text-gray-600">
            <p><strong>Name:</strong> {{ $event->name }}</p>
            <p><strong>Date:</strong> {{ $event->posted_date ? $event->posted_date->format('M d, Y') : 'N/A' }}</p>
            <p><strong>Status:</strong> 
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                    @if($event->status == 'active') 
                        bg-green-100 text-green-800 
                    @else 
                        bg-yellow-100 text-yellow-800 
                    @endif">
                    {{ ucfirst($event->status) }}
                </span>
            </p>
            <p><strong>Total Attendees:</strong> <span class="text-lg font-bold text-blue-600">{{ $rsvps->total() }}</span></p>
        </div>
    </div>
    
    {{-- Attendance Table --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:text-black print:border print:border-black">Customer Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:text-black print:border print:border-black">Mobile</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:text-black print:border print:border-black">Adults</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:text-black print:border print:border-black">Children</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider print:text-black print:border print:border-black">Attendance Date & Time</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($rsvps as $rsvp)
                        <tr class="hover:bg-gray-50 print:bg-white">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 print:text-black print:border print:border-black">
                                {{ $rsvp->customer->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 print:text-black print:border print:border-black">
                                {{ $rsvp->customer->mobile }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 print:text-black print:border print:border-black">
                                {{ $rsvp->adults_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 print:text-black print:border print:border-black">
                                {{ $rsvp->children_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 print:text-black print:border print:border-black">
                                {{ $rsvp->attendance_timestamp ? $rsvp->attendance_timestamp->setTimezone('Asia/Kolkata')->format('M d, Y H:i:s') : ($rsvp->created_at ? $rsvp->created_at->setTimezone('Asia/Kolkata')->format('M d, Y H:i:s') : 'N/A') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 print:text-black">
                                No attendance records found for this event.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 print:hidden">
            {{ $rsvps->links() }}
        </div>
    </div>
</div>
@endsection