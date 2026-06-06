@extends('customer.layout')

@section('title', 'Polls')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Community Polls</h2>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif
    
    @if($polls->count() > 0)
        <div class="space-y-6">
            @foreach($polls as $poll)
                <div class="bg-gray-50 rounded-lg p-5 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ $poll->description }}</h3>
                    
                    @php
                        $hasVoted = $poll->responses->contains('customer_id', Auth::guard('customer')->id());
                    @endphp
                    
                    @if($hasVoted)
                        <div class="mt-4">
                            <p class="text-green-600 font-medium mb-3">Thank you for voting!</p>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white p-4 rounded-lg border border-green-200">
                                    <div class="text-2xl font-bold text-green-600">{{ $poll->yes_count }}</div>
                                    <div class="text-sm text-gray-600">Yes Votes</div>
                                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500" style="width: {{ $poll->total_responses > 0 ? ($poll->yes_count / $poll->total_responses) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg border border-red-200">
                                    <div class="text-2xl font-bold text-red-600">{{ $poll->no_count }}</div>
                                    <div class="text-sm text-gray-600">No Votes</div>
                                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-500" style="width: {{ $poll->total_responses > 0 ? ($poll->no_count / $poll->total_responses) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg border border-yellow-200">
                                    <div class="text-2xl font-bold text-yellow-600">{{ $poll->maybe_count }}</div>
                                    <div class="text-sm text-gray-600">Maybe Votes</div>
                                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-yellow-500" style="width: {{ $poll->total_responses > 0 ? ($poll->maybe_count / $poll->total_responses) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('customer.polls.vote', $poll) }}" method="POST" class="mt-4">
                            @csrf
                            <div class="flex flex-wrap gap-3">
                                <button type="submit" name="response" value="yes" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                                    Yes
                                </button>
                                <button type="submit" name="response" value="no" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                                    No
                                </button>
                                <button type="submit" name="response" value="maybe" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition">
                                    Maybe
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endforeach
            
            @if($polls->hasPages())
                <div class="mt-6">
                    {{ $polls->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No polls available</h3>
            <p class="mt-1 text-sm text-gray-500">Check back later for new polls.</p>
        </div>
    @endif
</div>
@endsection