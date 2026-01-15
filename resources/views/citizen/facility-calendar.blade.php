@extends('layouts.citizen')

@section('title', 'Facility Calendar')
@section('page-title', 'Facility Calendar')
@section('page-subtitle', 'View available dates and existing reservations')

@section('page-content')
<div class="space-y-6">
    <!-- Calendar Header with Filters -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <!-- Month/Year Navigation -->
            <div class="flex items-center space-x-4">
                <a href="{{ route('citizen.facility-calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year, 'facility_id' => $selectedFacilityId]) }}" 
                   class="p-2 text-gray-600 hover:text-lgu-headline hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $currentDate->format('F Y') }}
                </h2>
                
                <a href="{{ route('citizen.facility-calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year, 'facility_id' => $selectedFacilityId]) }}" 
                   class="p-2 text-gray-600 hover:text-lgu-headline hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
                
                <a href="{{ route('citizen.facility-calendar') }}" 
                   class="px-4 py-2 text-sm text-lgu-button-text bg-lgu-button hover:bg-lgu-highlight rounded-lg transition font-medium">
                    Today
                </a>
            </div>
            
            <!-- Facility Filter -->
            <div class="flex items-center space-x-3">
                <label for="facility_filter" class="text-sm font-medium text-gray-700">Filter by Facility:</label>
                <select id="facility_filter" 
                        onchange="window.location.href='{{ route('citizen.facility-calendar', ['month' => $currentDate->month, 'year' => $currentDate->year]) }}&facility_id=' + this.value"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button">
                    <option value="">All Facilities</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->facility_id }}" {{ $selectedFacilityId == $facility->facility_id ? 'selected' : '' }}>
                            {{ $facility->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="mt-6 pt-4 border-t border-gray-200">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Status Legend:</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-green-500 rounded"></div>
                    <span class="text-sm text-gray-600">Available</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-blue-500 rounded"></div>
                    <span class="text-sm text-gray-600">Reserved (Paid)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-yellow-500 rounded"></div>
                    <span class="text-sm text-gray-600">Tentative (Unpaid)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-red-500 rounded"></div>
                    <span class="text-sm text-gray-600">Confirmed</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-purple-500 rounded"></div>
                    <span class="text-sm text-gray-600">Under Verification</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-gradient-to-br from-lgu-button to-lgu-highlight rounded"></div>
                    <span class="text-sm text-gray-600 font-semibold">City Event</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-4 h-4 bg-lgu-green rounded"></div>
                    <span class="text-sm text-gray-600 font-semibold">Government Program</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Grid -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Day Headers -->
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Sunday</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Monday</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Tuesday</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Wednesday</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Thursday</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Friday</div>
            <div class="p-3 text-center text-sm font-semibold text-gray-700">Saturday</div>
        </div>
        
        <!-- Calendar Days -->
        <div class="grid grid-cols-7">
            @foreach($calendarData as $day)
                @if($day['date'])
                    <div class="min-h-[120px] p-2 border border-gray-200 {{ $day['isToday'] ? 'bg-blue-50' : ($day['isPast'] ? 'bg-gray-50' : 'bg-white') }} hover:bg-gray-50 transition-colors cursor-pointer"
                         onclick="showDayDetails('{{ $day['dateString'] }}')">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-sm font-semibold {{ $day['isToday'] ? 'text-blue-600' : ($day['isPast'] ? 'text-gray-400' : 'text-gray-900') }}">
                                {{ $day['date']->format('j') }}
                            </span>
                            @if($day['hasBookings'])
                                <span class="px-2 py-1 text-xs font-bold bg-lgu-button text-lgu-button-text rounded-full">
                                    {{ $day['bookingCount'] }}
                                </span>
                            @endif
                        </div>
                        
                        @if($day['hasCityEvents'] || $day['hasBookings'] || $day['hasGovPrograms'])
                            <div class="space-y-1">
                                {{-- City Events First (Higher Priority) --}}
                                @if($day['hasCityEvents'])
                                    @foreach($day['cityEvents']->take(1) as $event)
                                        <div class="p-1.5 bg-yellow-100 text-lgu-headline border-2 border-lgu-highlight rounded text-xs">
                                            <div class="font-bold truncate">[CITY EVENT]</div>
                                            <div class="truncate text-xs font-semibold">{{ $event->event_title }}</div>
                                        </div>
                                    @endforeach
                                @endif
                                
                                {{-- Government Programs (High Priority) --}}
                                @if($day['hasGovPrograms'])
                                    @php
                                        $govDisplayLimit = $day['hasCityEvents'] ? 0 : 1;
                                    @endphp
                                    @foreach($day['govPrograms']->take($govDisplayLimit) as $program)
                                        <div class="p-1.5 bg-green-100 text-green-900 border-2 border-green-600 rounded text-xs">
                                            <div class="font-bold truncate">[GOV PROGRAM]</div>
                                            <div class="truncate text-xs font-semibold">{{ $program->program_title }}</div>
                                        </div>
                                    @endforeach
                                @endif
                                
                                {{-- Regular Bookings --}}
                                @if($day['hasBookings'])
                                    @php
                                        $usedSlots = ($day['hasCityEvents'] ? 1 : 0) + ($day['hasGovPrograms'] && !$day['hasCityEvents'] ? 1 : 0);
                                        $displayLimit = max(0, 2 - $usedSlots);
                                    @endphp
                                    @foreach($day['bookings']->take($displayLimit) as $booking)
                                        @php
                                            $statusColors = [
                                                'reserved' => 'bg-blue-100 text-blue-800 border-blue-300',
                                                'tentative' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                                'confirmed' => 'bg-red-100 text-red-800 border-red-300',
                                                'staff_verified' => 'bg-purple-100 text-purple-800 border-purple-300',
                                                'payment_pending' => 'bg-orange-100 text-orange-800 border-orange-300',
                                            ];
                                            $colorClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800 border-gray-300';
                                        @endphp
                                        <div class="p-1.5 {{ $colorClass }} border rounded text-xs">
                                            <div class="font-semibold truncate">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }}</div>
                                            <div class="truncate text-xs">{{ $booking->facility_name }}</div>
                                        </div>
                                    @endforeach
                                    @if($day['bookingCount'] > $displayLimit)
                                        <div class="text-xs text-gray-500 text-center mt-1">
                                            +{{ $day['bookingCount'] - $displayLimit }} more
                                        </div>
                                    @endif
                                @endif
                                
                                @php
                                    $moreCount = ($day['cityEventCount'] > 1 ? $day['cityEventCount'] - 1 : 0) 
                                                + ($day['hasGovPrograms'] && $day['hasCityEvents'] ? $day['govProgramCount'] : 0);
                                @endphp
                                @if($moreCount > 0)
                                    <div class="text-xs text-gray-500 text-center mt-1">
                                        +{{ $moreCount }} more
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="min-h-[120px] p-2 border border-gray-200 bg-gray-50"></div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- All Reservations List (Highly Visible) -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">
                {{ $currentDate->format('F Y') }} Events & Reservations
            </h2>
            <span class="px-4 py-2 bg-lgu-button text-lgu-button-text font-bold rounded-lg text-lg">
                {{ $bookings->count() + $govPrograms->count() }} Total
            </span>
        </div>

        @if($bookings->isEmpty() && $govPrograms->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Events Found</h3>
                <p class="text-gray-600">There are no reservations or government programs for this month{{ $selectedFacilityId ? ' and facility' : '' }}.</p>
            </div>
        @else
            <div class="space-y-3">
                {{-- Government Programs First --}}
                @foreach($govPrograms as $program)
                    <div class="flex items-center justify-between p-4 border-2 border-green-600 rounded-lg hover:shadow-md transition-shadow bg-green-50">
                        <div class="flex items-center space-x-4 flex-1">
                            <!-- Date Badge -->
                            <div class="flex-shrink-0 text-center rounded-lg p-3 min-w-[80px] shadow-md border-2 border-green-600" style="background-color: #15803d;">
                                <div class="text-2xl font-bold text-white">{{ \Carbon\Carbon::parse($program->event_date)->format('d') }}</div>
                                <div class="text-xs uppercase text-white">{{ \Carbon\Carbon::parse($program->event_date)->format('M') }}</div>
                            </div>
                            
                            <!-- Program Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-3 py-1 bg-green-100 text-green-900 text-xs font-bold rounded border-2 border-green-600">GOV PROGRAM</span>
                                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $program->program_title }}</h3>
                                </div>
                                <div class="flex items-center space-x-4 mt-1 text-sm text-gray-700">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-semibold">{{ \Carbon\Carbon::parse($program->event_date . ' ' . $program->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($program->event_date . ' ' . $program->end_time)->format('g:i A') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span>{{ $program->facility_name }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-green-100 text-green-800 border-2 border-green-300">
                                    Confirmed
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                {{-- Regular Bookings --}}
            <div class="space-y-3">
                @foreach($bookings as $booking)
                    @php
                        $statusConfig = [
                            'reserved' => ['color' => 'blue', 'label' => 'Reserved'],
                            'tentative' => ['color' => 'yellow', 'label' => 'Tentative'],
                            'confirmed' => ['color' => 'green', 'label' => 'Confirmed'],
                            'staff_verified' => ['color' => 'purple', 'label' => 'Verified'],
                            'payment_pending' => ['color' => 'orange', 'label' => 'Payment Pending'],
                        ];
                        $status = $statusConfig[$booking->status] ?? ['color' => 'gray', 'label' => ucfirst($booking->status)];
                    @endphp
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow bg-gray-50">
                        <div class="flex items-center space-x-4 flex-1">
                            <!-- Date Badge -->
                            <div class="flex-shrink-0 text-center bg-lgu-headline text-white rounded-lg p-3 min-w-[80px]">
                                <div class="text-2xl font-bold">{{ \Carbon\Carbon::parse($booking->start_time)->format('d') }}</div>
                                <div class="text-xs uppercase">{{ \Carbon\Carbon::parse($booking->start_time)->format('M') }}</div>
                            </div>
                            
                            <!-- Booking Details -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-bold text-gray-900 truncate">{{ $booking->facility_name }}</h3>
                                <div class="flex items-center space-x-4 mt-1 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-semibold">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span>{{ $booking->facility_location }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Status Badge -->
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-{{ $status['color'] }}-100 text-{{ $status['color'] }}-800 border-2 border-{{ $status['color'] }}-300">
                                    {{ $status['label'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
</div>

<!-- Day Details Modal -->
<div id="dayDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between">
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900"></h3>
            <button onclick="closeDayDetails()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="modalContent" class="p-6">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDayDetails(dateString) {
    const modal = document.getElementById('dayDetailsModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    // Format date for display
    const date = new Date(dateString + 'T00:00:00');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    modalTitle.textContent = date.toLocaleDateString('en-US', options);
    
    // Show loading
    modalContent.innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-b-2 border-lgu-headline mx-auto"></div></div>';
    modal.classList.remove('hidden');
    
    // Fetch bookings for this day
    const facilityId = document.getElementById('facility_filter').value;
    fetch(`{{ route('citizen.facility-calendar.bookings') }}?date=${dateString}&facility_id=${facilityId}`)
        .then(response => response.json())
        .then(data => {
            const hasAnyEvents = data.bookings.length > 0 || 
                                (data.cityEvents && data.cityEvents.length > 0) || 
                                (data.govPrograms && data.govPrograms.length > 0);
                                
            if (!hasAnyEvents) {
                modalContent.innerHTML = `
                    <div class="text-center py-8">
                        <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Events</h3>
                        <p class="text-gray-600">There are no reservations, city events, or government programs for this date.</p>
                    </div>
                `;
            } else {
                let html = '<div class="space-y-3">';
                
                // Display city events first
                if (data.cityEvents && data.cityEvents.length > 0) {
                    data.cityEvents.forEach(event => {
                        const startTime = new Date(event.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                        const endTime = new Date(event.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                        
                        html += `
                            <div class="p-4 border-4 border-lgu-highlight bg-yellow-50 rounded-lg shadow-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-lg text-lgu-headline">[CITY EVENT] ${event.event_title}</h4>
                                    <span class="px-3 py-1 bg-lgu-button text-white rounded-full text-xs font-bold uppercase">${event.status}</span>
                                </div>
                                <div class="text-sm space-y-2 text-gray-700">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-lgu-headline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-semibold">${startTime} - ${endTime}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-lgu-headline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span class="font-semibold">${event.facility_name}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                // Display government programs
                if (data.govPrograms && data.govPrograms.length > 0) {
                    data.govPrograms.forEach(program => {
                        const eventTime = new Date(program.event_date + ' ' + program.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                        const endTime = new Date(program.event_date + ' ' + program.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                        
                        html += `
                            <div class="p-4 border-4 border-green-600 bg-green-50 rounded-lg shadow-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-lg text-green-800">[GOV PROGRAM] ${program.program_title}</h4>
                                    <span class="px-3 py-1 bg-green-100 text-green-900 rounded-full text-xs font-bold uppercase border-2 border-green-600">${program.coordination_status}</span>
                                </div>
                                <div class="text-sm space-y-2 text-gray-700">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-semibold">${eventTime} - ${endTime}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        </svg>
                                        <span class="font-semibold">${program.facility_name}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Organizer: ${program.organizer_name}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                }
                
                // Display regular bookings
                data.bookings.forEach(booking => {
                    const statusColors = {
                        'reserved': 'bg-blue-100 text-blue-800 border-blue-300',
                        'tentative': 'bg-yellow-100 text-yellow-800 border-yellow-300',
                        'confirmed': 'bg-red-100 text-red-800 border-red-300',
                        'staff_verified': 'bg-purple-100 text-purple-800 border-purple-300',
                        'payment_pending': 'bg-orange-100 text-orange-800 border-orange-300',
                    };
                    const colorClass = statusColors[booking.status] || 'bg-gray-100 text-gray-800 border-gray-300';
                    const startTime = new Date(booking.start_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    const endTime = new Date(booking.end_time).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                    
                    html += `
                        <div class="p-4 border-2 ${colorClass} rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-bold text-lg">${booking.facility_name}</h4>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase">${booking.status}</span>
                            </div>
                            <div class="text-sm space-y-1">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-semibold">${startTime} - ${endTime}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    </svg>
                                    <span>${booking.facility_location}</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                modalContent.innerHTML = html;
            }
        })
        .catch(error => {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-600">Error loading bookings. Please try again.</p>
                </div>
            `;
        });
}

function closeDayDetails() {
    document.getElementById('dayDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('dayDetailsModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDayDetails();
    }
});
</script>
@endpush
@endsection

