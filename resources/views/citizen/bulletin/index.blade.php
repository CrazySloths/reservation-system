@extends('layouts.citizen')

@section('title', 'Bulletin Board')
@section('page-title', 'Bulletin Board')
@section('page-subtitle', 'Stay updated with the latest announcements and notifications')

@section('page-content')
<div class="space-y-6">
    <!-- Search Bar -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input type="text" 
                   id="searchInput" 
                   placeholder="Search announcements..."
                   value="{{ request('search') }}"
                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent transition-all"
                   oninput="liveSearch(this.value)">
        </div>
    </div>

    <!-- Type Filters -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-lgu-button">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            Filter by Type
        </h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('citizen.bulletin', ['type' => 'all']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $type === 'all' ? 'bg-lgu-button text-lgu-button-text shadow-lg scale-105' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/>
                </svg>
                All <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $type === 'all' ? 'bg-white/20' : 'bg-gray-200' }}">{{ $typeCounts['all'] }}</span>
            </a>
            <a href="{{ route('citizen.bulletin', ['type' => 'general']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $type === 'general' ? 'bg-blue-500 text-white shadow-lg scale-105' : 'bg-blue-100 text-blue-700 hover:bg-blue-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/>
                </svg>
                General <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $type === 'general' ? 'bg-white/20' : 'bg-blue-200' }}">{{ $typeCounts['general'] }}</span>
            </a>
            <a href="{{ route('citizen.bulletin', ['type' => 'maintenance']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $type === 'maintenance' ? 'bg-yellow-500 text-white shadow-lg scale-105' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                </svg>
                Maintenance <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $type === 'maintenance' ? 'bg-white/20' : 'bg-yellow-200' }}">{{ $typeCounts['maintenance'] }}</span>
            </a>
            <a href="{{ route('citizen.bulletin', ['type' => 'event']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $type === 'event' ? 'bg-purple-500 text-white shadow-lg scale-105' : 'bg-purple-100 text-purple-700 hover:bg-purple-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                </svg>
                Events <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $type === 'event' ? 'bg-white/20' : 'bg-purple-200' }}">{{ $typeCounts['event'] }}</span>
            </a>
            <a href="{{ route('citizen.bulletin', ['type' => 'urgent']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $type === 'urgent' ? 'bg-red-500 text-white shadow-lg scale-105 animate-pulse' : 'bg-red-100 text-red-700 hover:bg-red-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/>
                </svg>
                Urgent <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $type === 'urgent' ? 'bg-white/20' : 'bg-red-200' }}">{{ $typeCounts['urgent'] }}</span>
            </a>
        </div>
    </div>

    <!-- Announcements List -->
    @if($announcements->isEmpty())
        <div class="bg-gray-50 shadow-lg rounded-xl p-16 text-center">
            <div class="mx-auto w-24 h-24 bg-white shadow-lg rounded-full flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                    <path d="M3 7V5c0-1.1.9-2 2-2h2"/><path d="M17 3h2c1.1 0 2 .9 2 2v2"/><path d="M21 17v2c0 1.1-.9 2-2 2h-2"/><path d="M7 21H5c-1.1 0-2-.9-2-2v-2"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Announcements Found</h3>
            <p class="text-gray-600 text-lg">{{ request('search') ? 'Try adjusting your search terms.' : 'There are no announcements at this time.' }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($announcements as $announcement)
                @php
                    // Type badge configuration
                    $typeBadge = match($announcement->type) {
                        'general' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300', 'label' => 'General'],
                        'maintenance' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300', 'label' => 'Maintenance'],
                        'event' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'border' => 'border-purple-300', 'label' => 'Event'],
                        'urgent' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'label' => 'Urgent'],
                        'facility_update' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'label' => 'Facility Update'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'label' => ucfirst($announcement->type)]
                    };

                    // Priority indicator
                    $priorityColor = match($announcement->priority) {
                        'urgent' => 'bg-red-500',
                        'high' => 'bg-orange-500',
                        'medium' => 'bg-yellow-500',
                        'low' => 'bg-green-500',
                        default => 'bg-gray-500'
                    };
                @endphp

                <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-lgu-button/30 transform hover:-translate-y-1 relative">
                    <!-- Priority Indicator -->
                    <div class="absolute top-0 left-0 w-full h-2 {{ $priorityColor }}"></div>

                    <!-- Pinned Badge -->
                    @if($announcement->is_pinned)
                        <div class="absolute top-4 right-4 px-3 py-1.5 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full shadow-lg flex items-center gap-1 z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 17v5"/><path d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z"/>
                            </svg>
                            Pinned
                        </div>
                    @endif

                    <div class="p-6">
                        <!-- Type Badge -->
                        <div class="mb-4">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold {{ $typeBadge['bg'] }} {{ $typeBadge['text'] }} border {{ $typeBadge['border'] }}">
                                {{ $typeBadge['label'] }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 min-h-[3.5rem]">{{ $announcement->title }}</h3>

                        <!-- Content Preview -->
                        <p class="text-gray-600 mb-4 line-clamp-3 min-h-[4.5rem]">{{ Str::limit(strip_tags($announcement->content), 120) }}</p>

                        <!-- Meta Info -->
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4 pb-4 border-b border-gray-200">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                </svg>
                                <span>{{ \Carbon\Carbon::parse($announcement->created_at)->format('M d, Y') }}</span>
                            </div>
                            @if($announcement->attachment_path)
                                <div class="flex items-center gap-1 text-lgu-button">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/>
                                    </svg>
                                    <span class="text-xs font-semibold">File</span>
                                </div>
                            @endif
                        </div>

                        <!-- Read More Button -->
                        <a href="{{ route('citizen.bulletin.show', $announcement->id) }}" 
                           class="block w-full px-4 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-all duration-200 text-center shadow-md hover:shadow-lg cursor-pointer">
                            Read More
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            {{ $announcements->links() }}
        </div>
    @endif
</div>

@push('scripts')
<script>
// Live search functionality
let searchTimeout;
function liveSearch(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        const currentType = '{{ $type }}';
        window.location.href = `{{ route('citizen.bulletin') }}?type=${currentType}&search=${encodeURIComponent(query)}`;
    }, 500);
}
</script>
@endpush
@endsection

