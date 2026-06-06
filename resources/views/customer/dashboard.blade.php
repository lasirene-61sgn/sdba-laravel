@extends('customer.layout')

@section('title', 'Customer Dashboard')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Welcome, {{ $customer->name }}</h2>

    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Banner Section -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Featured Banner</h3>
        @if(isset($banners) && $banners->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($banners as $banner)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        @if($banner->image_path)
                            <img src="{{ Storage::url($banner->image_path) }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover rounded-md mb-3">
                        @endif
                        <h4 class="font-semibold text-gray-800">{{ $banner->title }}</h4>
                        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($banner->description, 100) }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No banners available.</p>
        @endif
    </div>

    <!-- Birthdays Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">🎂 Upcoming Birthdays</h3>
        @if($birthdays->count() > 0)
            <ul class="space-y-2">
                @foreach($birthdays as $birthday)
                    <li class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium">{{ $birthday['name'] }}</span>
                            @if($birthday['type'] === 'family')
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">
                                    Family
                                </span>
                                @if(isset($birthday['relationship']))
                                    <span class="text-xs text-gray-500 ml-1">
                                        ({{ $birthday['relationship'] }})
                                    </span>
                                @endif
                            @endif
                            <div class="text-sm text-gray-600">
                                {{ $birthday['date']->format('F j') }}
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($birthday['mobile'])
                                <a href="tel:{{ $birthday['mobile'] }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </a>
                            @endif
                            @if($birthday['whatsapp'])
                                <a href="https://wa.me/{{ $birthday['whatsapp'] }}" target="_blank" class="text-green-600 hover:text-green-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-600">No birthdays this month.</p>
        @endif
    </div>

    <!-- Anniversaries Section -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">💍 Upcoming Anniversaries</h3>
        @if($anniversaries->count() > 0)
            <ul class="space-y-2">
                @foreach($anniversaries as $anniversary)
                    <li class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                        <div>
                            <span class="font-medium">{{ $anniversary['name'] }}</span>
                            @if($anniversary['type'] === 'family')
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">
                                    Family
                                </span>
                                @if(isset($anniversary['relationship']))
                                    <span class="text-xs text-gray-500 ml-1">
                                        ({{ $anniversary['relationship'] }})
                                    </span>
                                @endif
                            @endif
                            <div class="text-sm text-gray-600">
                                {{ $anniversary['date']->format('F j') }}
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($anniversary['mobile'])
                                <a href="tel:{{ $anniversary['mobile'] }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </a>
                            @endif
                            @if($anniversary['whatsapp'])
                                <a href="https://wa.me/{{ $anniversary['whatsapp'] }}" target="_blank" class="text-green-600 hover:text-green-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-gray-600">No anniversaries this month.</p>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Customer Info Card -->
        <!-- <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
            <h3 class="text-lg font-semibold text-blue-800 mb-2">Your Information</h3>
            <p><strong>Name:</strong> {{ $customer->name }}</p>
            <p><strong>Mobile:</strong> {{ $customer->mobile }}</p>
            <p><strong>Status:</strong> 
                <span class="px-2 py-1 rounded text-xs {{ $customer->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($customer->status) }}
                </span>
            </p>
        </div> -->

        <!-- Quick Stats Card -->
        <!-- <div class="bg-green-50 rounded-lg p-4 border border-green-200">
            <h3 class="text-lg font-semibold text-green-800 mb-2">Quick Stats</h3>
            <p><strong>Total Plans:</strong> {{ $plans->count() }}</p>
            <p><strong>Family Members:</strong> {{ $familyMembers->count() }}</p>
        </div> -->
    </div>

    <!-- Quick Actions Section -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('customer.profile') }}" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-800 font-medium py-3 px-4 rounded-lg text-center transition duration-150">
                <div class="flex flex-col items-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span>Profile</span>
                </div>
            </a>
            <a href="{{ route('customer.plans') }}" class="bg-green-100 hover:bg-green-200 text-green-800 font-medium py-3 px-4 rounded-lg text-center transition duration-150">
                <div class="flex flex-col items-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    <span>Plans</span>
                </div>
            </a>
            <a href="{{ route('customer.family.members.index') }}" class="bg-blue-100 hover:bg-blue-200 text-blue-800 font-medium py-3 px-4 rounded-lg text-center transition duration-150">
                <div class="flex flex-col items-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span>Family</span>
                </div>
            </a>
            <a href="{{ route('customer.event') }}" class="bg-purple-100 hover:bg-purple-200 text-purple-800 font-medium py-3 px-4 rounded-lg text-center transition duration-150">
                <div class="flex flex-col items-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span>Events</span>
                </div>
            </a>
            <a href="{{ route('customer.polls') }}" class="bg-teal-100 hover:bg-teal-200 text-teal-800 font-medium py-3 px-4 rounded-lg text-center transition duration-150">
                <div class="flex flex-col items-center">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span>Polls</span>
                </div>
            </a>
        </div>
    </div>

    <!-- Upcoming Events Section -->
    @if(isset($upcomingEvents) && $upcomingEvents->count() > 0)
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Upcoming Events</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingEvents as $event)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="font-semibold text-gray-800">{{ $event->name }}</h4>
                        @if($event->posted_date)
                            <p class="text-gray-600 text-sm mt-1">{{ $event->posted_date->format('F j, Y') }}</p>
                        @endif
                        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($event->description, 80) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Committee Members Section -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Our Leaders</h3>
        @if(isset($committeeMembers) && $committeeMembers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($committeeMembers as $member)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        @if($member->image_path)
                            <img src="{{ Storage::url($member->image_path) }}" alt="{{ $member->name }}" class="w-16 h-16 object-cover rounded-full mx-auto mb-3">
                        @endif
                        <h4 class="font-semibold text-gray-800 text-center">{{ $member->name }}</h4>
                        <p class="text-gray-600 text-sm text-center">{{ $member->post_name }}</p>
                        @if($member->phone)
                            <p class="text-gray-600 text-sm text-center mt-1">{{ $member->phone }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No committee members available.</p>
        @endif
    </div>

    <!-- Gallery Section -->
    <!-- <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Gallery</h3>
        @if(isset($galleryItems) && $galleryItems->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($galleryItems as $item)
                    <div class="bg-gray-50 rounded-lg overflow-hidden border border-gray-200">
                        @if($item->image_path)
                            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->title }}" class="w-full h-32 object-cover">
                        @endif
                        <div class="p-3">
                            <h4 class="font-medium text-gray-800 text-sm">{{ Str::limit($item->title, 30) }}</h4>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No gallery items available.</p>
        @endif
    </div> -->

    <!-- News Section -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Latest News</h3>
        @if(isset($newsItems) && $newsItems->count() > 0)
            <div class="space-y-4">
                @foreach($newsItems as $news)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="font-semibold text-gray-800">{{ $news->title }}</h4>
                        @if($news->posted_date)
                            <p class="text-gray-600 text-sm mt-1">{{ $news->posted_date->format('F j, Y') }}</p>
                        @endif
                        <p class="text-gray-600 text-sm mt-2">{{ Str::limit($news->content, 100) }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600">No news available.</p>
        @endif
    </div>

    <!-- Plans Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Your Plans</h3>
            <a href="{{ route('customer.plans') }}" class="text-blue-600 hover:text-blue-800">View All</a>
        </div>

        @if($plans->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-2 px-4 text-left border-b">Plan Type</th>
                            <th class="py-2 px-4 text-left border-b">Start Date</th>
                            <th class="py-2 px-4 text-left border-b">Next Due</th>
                            <th class="py-2 px-4 text-left border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($plans as $plan)
                            <tr>
                                <td class="py-2 px-4 border-b">{{ ucfirst($plan->plan_type) }}</td>
                                <td class="py-2 px-4 border-b">{{ $plan->start_date->format('d M Y') }}</td>
                                <td class="py-2 px-4 border-b">{{ $plan->next_due_date->format('d M Y') }}</td>
                                <td class="py-2 px-4 border-b">
                                    <span class="px-2 py-1 rounded text-xs {{ $plan->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($plan->status) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600">You don't have any plans yet.</p>
        @endif
    </div>

    <!-- Family Members Section -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Family Members</h3>
            <a href="{{ route('customer.family.members.index') }}" class="text-blue-600 hover:text-blue-800">Manage Family Members</a>
        </div>

        <!-- Matrimony Filter Button -->
        @if($matrimonyMembers->count() > 0)
            <div class="mb-4">
                <button id="toggleMatrimony" class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-pink-700 bg-pink-100 hover:bg-pink-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500">
                    <svg class="mr-1.5 h-4 w-4 text-pink-400" fill="currentColor" viewBox="0 0 8 8">
                        <circle cx="4" cy="4" r="3" />
                    </svg>
                    Show Matrimony Candidates ({{ $matrimonyMembers->count() }})
                </button>
            </div>
        @endif

        @if($familyMembers->count() > 0)
            <!-- All Family Members (Default View) -->
            <div id="allFamilyMembers" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($familyMembers as $familyMember)
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex justify-between items-start">
                            <h4 class="font-semibold text-gray-800">{{ $familyMember->name }}</h4>
                            <!-- Matrimony Icon -->
                            @if($familyMember->matrimony)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    <svg class="mr-1 h-3 w-3 text-pink-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    M
                                </span>
                            @endif
                        </div>
                        @if($familyMember->relationship)
                            <p class="text-gray-600 text-sm">Relationship: {{ $familyMember->relationship }}</p>
                        @endif
                        @if($familyMember->mobile)
                            <p class="text-gray-600 text-sm">Mobile: {{ $familyMember->mobile }}</p>
                        @endif
                        <p class="text-gray-600 text-sm mt-2">
                            Added on {{ $familyMember->created_at->format('M d, Y') }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Matrimony Members Only (Hidden by Default) -->
            @if($matrimonyMembers->count() > 0)
                <div id="matrimonyFamilyMembers" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 hidden">
                    @foreach($matrimonyMembers as $familyMember)
                        <div class="bg-pink-50 rounded-lg p-4 border border-pink-200">
                            <div class="flex justify-between items-start">
                                <h4 class="font-semibold text-gray-800">{{ $familyMember->name }}</h4>
                                <!-- Matrimony Icon -->
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    <svg class="mr-1 h-3 w-3 text-pink-400" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    M
                                </span>
                            </div>
                            @if($familyMember->relationship)
                                <p class="text-gray-600 text-sm">Relationship: {{ $familyMember->relationship }}</p>
                            @endif
                            @if($familyMember->mobile)
                                <p class="text-gray-600 text-sm">Mobile: {{ $familyMember->mobile }}</p>
                            @endif
                            @if($familyMember->gender)
                                <p class="text-gray-600 text-sm">Gender: {{ ucfirst($familyMember->gender) }}</p>
                            @endif
                            <p class="text-gray-600 text-sm mt-2">
                                Added on {{ $familyMember->created_at->format('M d, Y') }}
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <p class="text-gray-600">You don't have any family members registered yet.</p>
        @endif
    </div>

    <!-- Add JavaScript for toggling between views -->
    @if($matrimonyMembers->count() > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleButton = document.getElementById('toggleMatrimony');
                const allFamilyMembers = document.getElementById('allFamilyMembers');
                const matrimonyFamilyMembers = document.getElementById('matrimonyFamilyMembers');
                let showingAll = true;

                toggleButton.addEventListener('click', function() {
                    if (showingAll) {
                        // Show matrimony members only
                        allFamilyMembers.classList.add('hidden');
                        matrimonyFamilyMembers.classList.remove('hidden');
                        toggleButton.innerHTML = `
                            <svg class="mr-1.5 h-4 w-4 text-pink-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Show All Family Members
                        `;
                        showingAll = false;
                    } else {
                        // Show all family members
                        matrimonyFamilyMembers.classList.add('hidden');
                        allFamilyMembers.classList.remove('hidden');
                        toggleButton.innerHTML = `
                            <svg class="mr-1.5 h-4 w-4 text-pink-400" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Show Matrimony Candidates ({{ $matrimonyMembers->count() }})
                        `;
                        showingAll = true;
                    }
                });
            });
        </script>
    @endif

</div>
@endsection