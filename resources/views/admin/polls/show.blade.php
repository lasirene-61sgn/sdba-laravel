@extends('admin.layout.app')

@section('title', 'Poll Results')

@section('content')
<div class="page-header">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Poll Results</h1>
            <p class="text-gray-600 mt-1">View responses for this poll</p>
        </div>
        <a href="{{ route('admin.polls.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
            ← Back to Polls
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-900">Poll Question</h2>
    </div>
    <div class="p-6">
        <p class="text-gray-800">{{ $poll->description }}</p>
        <div class="mt-4 flex items-center">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                {{ $poll->total_responses }} Responses
            </span>
            <span class="ml-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                {{ $poll->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $poll->active ? 'Active' : 'Inactive' }}
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Yes Votes</h3>
        </div>
        <div class="p-6">
            <div class="text-3xl font-bold text-green-600">{{ $poll->yes_count }}</div>
            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-green-500" style="width: {{ $poll->total_responses > 0 ? ($poll->yes_count / $poll->total_responses) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">No Votes</h3>
        </div>
        <div class="p-6">
            <div class="text-3xl font-bold text-red-600">{{ $poll->no_count }}</div>
            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-red-500" style="width: {{ $poll->total_responses > 0 ? ($poll->no_count / $poll->total_responses) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Maybe Votes</h3>
        </div>
        <div class="p-6">
            <div class="text-3xl font-bold text-yellow-600">{{ $poll->maybe_count }}</div>
            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-yellow-500" style="width: {{ $poll->total_responses > 0 ? ($poll->maybe_count / $poll->total_responses) * 100 : 0 }}%"></div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-medium text-gray-900">Individual Responses</h2>
        <span class="text-sm text-gray-500">{{ $poll->responses->count() }} responses</span>
    </div>
    
    @if($poll->responses->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($poll->responses as $response)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $response->customer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $response->customer->mobile }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if($response->response === 'yes')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Yes
                                    </span>
                                @elseif($response->response === 'no')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        No
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Maybe
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $response->created_at->format('M d, Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No responses yet</h3>
            <p class="mt-1 text-sm text-gray-500">Customers haven't voted on this poll yet.</p>
        </div>
    @endif
</div>
@endsection