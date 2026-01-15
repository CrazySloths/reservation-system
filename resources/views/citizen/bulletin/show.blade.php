@extends('layouts.citizen')

@section('title', $announcement->title)
@section('page-title', 'Announcement Details')
@section('page-subtitle', $announcement->title)

@section('page-content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('citizen.bulletin') }}" 
           class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-semibold transition-colors cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
            </svg>
            Back to Bulletin Board
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Announcement Card -->
            <div class="bg-white shadow-lg rounded-xl p-8">
                @php
                    // Type badge configuration
                    $typeBadge = match($announcement->type) {
                        'general' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'label' => 'General', 'icon' => 'text-blue-600'],
                        'maintenance' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'label' => 'Maintenance', 'icon' => 'text-yellow-600'],
                        'event' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-300', 'label' => 'Event', 'icon' => 'text-purple-600'],
                        'urgent' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'label' => 'Urgent', 'icon' => 'text-red-600'],
                        'facility_update' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'label' => 'Facility Update', 'icon' => 'text-green-600'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'label' => ucfirst($announcement->type), 'icon' => 'text-gray-600']
                    };

                    // Priority configuration
                    $priorityBadge = match($announcement->priority) {
                        'urgent' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'label' => 'Urgent Priority'],
                        'high' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-300', 'label' => 'High Priority'],
                        'medium' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'label' => 'Medium Priority'],
                        'low' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'label' => 'Low Priority'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'label' => ucfirst($announcement->priority)]
                    };
                @endphp

                <!-- Header -->
                <div class="mb-6 pb-6 border-b-2 border-gray-200">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                <span class="px-4 py-2 rounded-lg text-sm font-bold {{ $typeBadge['bg'] }} {{ $typeBadge['text'] }} border-2 {{ $typeBadge['border'] }}">
                                    {{ $typeBadge['label'] }}
                                </span>
                                <span class="px-4 py-2 rounded-lg text-xs font-bold {{ $priorityBadge['bg'] }} {{ $priorityBadge['text'] }} border {{ $priorityBadge['border'] }}">
                                    {{ $priorityBadge['label'] }}
                                </span>
                                @if($announcement->is_pinned)
                                    <span class="px-4 py-2 bg-yellow-400 text-yellow-900 rounded-lg text-xs font-bold shadow-md flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 17v5"/><path d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z"/>
                                        </svg>
                                        Pinned Announcement
                                    </span>
                                @endif
                            </div>
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">{{ $announcement->title }}</h1>
                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button">
                                        <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($announcement->created_at)->format('F d, Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button">
                                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                    <span>{{ \Carbon\Carbon::parse($announcement->created_at)->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="prose max-w-none mb-8">
                    <div class="text-gray-800 leading-relaxed text-lg whitespace-pre-wrap">{{ $announcement->content }}</div>
                </div>

                <!-- Additional Info -->
                @if($announcement->additional_info)
                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 flex-shrink-0 mt-1">
                                <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                            </svg>
                            <div class="flex-1">
                                <h4 class="font-bold text-blue-900 mb-2">Additional Information</h4>
                                <p class="text-blue-800 leading-relaxed">{{ $announcement->additional_info }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Attachment -->
                @if($announcement->attachment_path)
                    <div class="bg-green-50 border-2 border-green-300 rounded-xl p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                                        <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-green-900 text-lg">Attachment Available</h4>
                                    <p class="text-sm text-green-700">Click to download the attached file</p>
                                </div>
                            </div>
                            <a href="{{ route('citizen.bulletin.download', $announcement->id) }}" 
                               class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-lg hover:shadow-xl cursor-pointer flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="space-y-6">
                <!-- Date Info Card -->
                <div class="bg-white shadow-lg rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Important Dates</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex items-start gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mt-0.5">
                                <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-900">Start Date</p>
                                <p class="text-gray-600">{{ \Carbon\Carbon::parse($announcement->start_date)->format('F d, Y') }}</p>
                            </div>
                        </div>
                        @if($announcement->end_date)
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button mt-0.5">
                                    <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-gray-900">End Date</p>
                                    <p class="text-gray-600">{{ \Carbon\Carbon::parse($announcement->end_date)->format('F d, Y') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Related Announcements -->
                @if($relatedAnnouncements->isNotEmpty())
                    <div class="bg-white shadow-lg rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Related Announcements</h3>
                        <div class="space-y-4">
                            @foreach($relatedAnnouncements as $related)
                                <a href="{{ route('citizen.bulletin.show', $related->id) }}" 
                                   class="block p-4 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors cursor-pointer border border-gray-200 hover:border-lgu-button">
                                    <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $related->title }}</h4>
                                    <p class="text-xs text-gray-600 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                        </svg>
                                        {{ \Carbon\Carbon::parse($related->created_at)->format('M d, Y') }}
                                    </p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Actions -->
                <div class="bg-white shadow-lg rounded-xl p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('citizen.bulletin') }}" 
                           class="block w-full px-4 py-3 bg-lgu-button text-lgu-button-text text-center font-semibold rounded-lg hover:bg-lgu-highlight transition-all duration-200 shadow-md hover:shadow-lg cursor-pointer flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
                            </svg>
                            Back to Board
                        </a>
                        <button onclick="window.print()" 
                                class="block w-full px-4 py-3 bg-gray-100 border-2 border-gray-300 text-gray-700 text-center font-semibold rounded-lg hover:bg-gray-200 transition-all duration-200 shadow-sm hover:shadow-md cursor-pointer flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

