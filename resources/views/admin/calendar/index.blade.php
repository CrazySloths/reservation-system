@extends('layouts.admin')

@section('page-title', 'Admin Calendar')
@section('page-subtitle', 'View all bookings and schedules')

@section('page-content')
<div class="space-y-gr-xl">
    <!-- Page Header -->
    <div class="bg-purple-600 rounded-2xl p-gr-xl text-white shadow-lg">
        <div class="flex items-center gap-gr-md">
            <div class="w-16 h-16 bg-purple-700 rounded-xl flex items-center justify-center">
                <i data-lucide="calendar-days" class="w-8 h-8 text-white"></i>
            </div>
            <div>
                <h1 class="text-h2 font-bold mb-gr-xs text-white">Admin Calendar</h1>
                <p class="text-body text-purple-100">View all bookings across all facilities and statuses</p>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white rounded-xl shadow-lg border border-lgu-stroke overflow-hidden">
        <!-- Calendar Controls -->
        <div class="bg-lgu-headline p-gr-lg border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-gr-md">
                <!-- Filters -->
                <div class="flex flex-wrap gap-gr-sm">
                    <!-- Facility Filter -->
                    <select id="facilityFilter" class="px-gr-md py-gr-sm bg-white border border-gray-300 rounded-lg text-small font-medium text-lgu-headline focus:outline-none focus:ring-2 focus:ring-lgu-button">
                        <option value="">All Facilities</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->facility_id }}">{{ $facility->name }}</option>
                        @endforeach
                    </select>

                    <!-- Status Filter -->
                    <select id="statusFilter" class="px-gr-md py-gr-sm bg-white border border-gray-300 rounded-lg text-small font-medium text-lgu-headline focus:outline-none focus:ring-2 focus:ring-lgu-button">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending Verification</option>
                        <option value="staff_verified">Awaiting Payment</option>
                        <option value="paid">Payment Verified</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="rejected">Rejected</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap items-center gap-gr-sm text-caption text-white">
                    <span class="font-semibold text-white">Legend:</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-500 rounded"></span> <span class="text-white">Pending</span></span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-500 rounded"></span> <span class="text-white">Awaiting Payment</span></span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-500 rounded"></span> <span class="text-white">Paid</span></span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-purple-500 rounded"></span> <span class="text-white">Confirmed</span></span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-500 rounded"></span> <span class="text-white">Rejected/Cancelled</span></span>
                </div>
            </div>
        </div>

        <!-- FullCalendar -->
        <div class="p-gr-lg">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="eventModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
        <div class="bg-white border-b border-gray-200 p-gr-lg flex items-center justify-between flex-shrink-0">
            <h3 class="text-h3 font-bold text-lgu-headline">Booking Details</h3>
            <button onclick="closeEventModal()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        <div id="modalContent" class="p-gr-lg space-y-gr-md overflow-y-auto flex-1 sidebar-scrollbar">
            <!-- Content will be populated by JavaScript -->
        </div>
        <div class="bg-gray-50 border-t border-gray-200 p-gr-lg flex-shrink-0">
            <a id="viewDetailsButton" href="#" class="block w-full px-gr-lg py-gr-md bg-lgu-button hover:bg-lgu-highlight text-lgu-button-text font-bold rounded-lg text-center transition-colors">
                View Full Details
            </a>
        </div>
    </div>
</div>

@push('styles')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for FullCalendar to load
    if (typeof FullCalendar === 'undefined') {
        console.error('FullCalendar library not loaded');
        return;
    }
    
    const calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) {
        console.error('Calendar element not found');
        return;
    }
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            day: 'Day'
        },
        height: 'auto',
        eventDisplay: 'block',
        displayEventTime: false,
        displayEventEnd: false,
        dayMaxEvents: 4,
        moreLinkText: 'more',
        eventContent: function(arg) {
            // Custom event rendering with correct time (no timezone conversion)
            const startTime = arg.event.extendedProps.start_time;
            if (!startTime) return { html: arg.event.title };
            
            // Extract hour from ISO format directly
            const timePart = startTime.split('T')[1].split(/[+Z]/)[0];
            const hour = parseInt(timePart.split(':')[0]);
            const ampm = hour >= 12 ? 'p' : 'a';
            const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
            
            return { html: `${displayHour}${ampm} ${arg.event.title}` };
        },
        events: function(info, successCallback, failureCallback) {
            const facilityId = document.getElementById('facilityFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            const params = new URLSearchParams({
                start: info.startStr,
                end: info.endStr
            });
            
            if (facilityId) params.append('facility_id', facilityId);
            if (status) params.append('status', status);
            
            fetch(`{{ route('admin.calendar.events') }}?${params.toString()}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => {
                    console.error('Error loading events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            showEventDetails(info.event);
        },
        eventDidMount: function(info) {
            info.el.style.cursor = 'pointer';
        }
    });
    
    calendar.render();
    
    // Reload calendar when filters change
    document.getElementById('facilityFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    document.getElementById('statusFilter').addEventListener('change', function() {
        calendar.refetchEvents();
    });
    
    // Helper function to format time correctly (without timezone conversion)
    function formatTime(datetime) {
        if (!datetime) return 'N/A';
        
        // Extract time from ISO8601 format directly (e.g., "2026-01-05T08:00:00+00:00")
        // Split by 'T' to get the time part, then split by '+' or 'Z' to remove timezone
        const timePart = datetime.split('T')[1].split(/[+Z]/)[0];
        const [hours, minutes] = timePart.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
        
        return `${displayHour}:${minutes} ${ampm}`;
    }
    
    // Event details modal
    window.showEventDetails = function(event) {
        const props = event.extendedProps;
        
        // Check if it's a government program
        if (props.event_type === 'government_program') {
            showGovernmentProgramDetails(event, props);
            return;
        }
        
        // Check if it's a city event
        if (props.event_type === 'city_event') {
            showCityEventDetails(event, props);
            return;
        }
        
        // Regular booking
        const booking = props;
        
        const statusConfig = {
            'pending': { bg: 'bg-yellow-100', text: 'text-yellow-800', label: 'Pending Verification' },
            'staff_verified': { bg: 'bg-blue-100', text: 'text-blue-800', label: 'Awaiting Payment' },
            'paid': { bg: 'bg-green-100', text: 'text-green-800', label: 'Payment Verified' },
            'confirmed': { bg: 'bg-purple-100', text: 'text-purple-800', label: 'Confirmed' },
            'rejected': { bg: 'bg-red-100', text: 'text-red-800', label: 'Rejected' },
            'cancelled': { bg: 'bg-gray-100', text: 'text-gray-800', label: 'Cancelled' }
        };
        
        const status = statusConfig[booking.status] || statusConfig['pending'];
        
        const modalContent = `
            <div class="space-y-gr-md">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-h4 font-bold text-lgu-headline">${event.title}</h4>
                        <p class="text-small text-lgu-paragraph">${booking.facility_location}</p>
                    </div>
                    <span class="${status.bg} ${status.text} px-3 py-1 rounded-lg text-small font-semibold">${status.label}</span>
                </div>
                
                <div class="bg-lgu-bg rounded-lg p-gr-md space-y-gr-sm">
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="calendar" class="w-4 h-4 text-lgu-paragraph"></i>
                        <span class="text-lgu-paragraph">Date:</span>
                        <span class="font-semibold text-lgu-headline">${new Date(booking.start_time).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="clock" class="w-4 h-4 text-lgu-paragraph"></i>
                        <span class="text-lgu-paragraph">Time:</span>
                        <span class="font-semibold text-lgu-headline">${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="user" class="w-4 h-4 text-lgu-paragraph"></i>
                        <span class="text-lgu-paragraph">Citizen:</span>
                        <span class="font-semibold text-lgu-headline">${booking.citizen_name}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="users" class="w-4 h-4 text-lgu-paragraph"></i>
                        <span class="text-lgu-paragraph">Attendees:</span>
                        <span class="font-semibold text-lgu-headline">${booking.num_attendees} people</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <span class="w-4 h-4 flex items-center justify-center text-lgu-paragraph font-bold text-body">₱</span>
                        <span class="text-lgu-paragraph">Total Amount:</span>
                        <span class="font-semibold text-green-600">₱${parseFloat(booking.total_amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                    </div>
                </div>
                
                ${booking.purpose ? `
                <div>
                    <p class="text-caption text-gray-500 mb-1">Event Purpose</p>
                    <p class="text-small text-lgu-paragraph">${booking.purpose}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('viewDetailsButton').href = `/admin/bookings/${booking.booking_id}/review`;
        document.getElementById('eventModal').classList.remove('hidden');
        
        // Reinitialize Lucide icons
        lucide.createIcons();
    };
    
    // Show government program details
    window.showGovernmentProgramDetails = function(event, props) {
        const statusConfig = {
            'confirmed': { bg: 'bg-green-100', text: 'text-green-800', label: 'Confirmed' },
            'completed': { bg: 'bg-purple-100', text: 'text-purple-800', label: 'Completed' }
        };
        
        const status = statusConfig[props.status] || statusConfig['confirmed'];
        
        const modalContent = `
            <div class="space-y-gr-md">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-h4 font-bold text-lgu-headline">${event.title.replace('[GOV PROGRAM] ', '')}</h4>
                        <p class="text-small text-lgu-paragraph">${props.facility_location}</p>
                    </div>
                    <span class="${status.bg} ${status.text} px-3 py-1 rounded-lg text-small font-semibold">${status.label}</span>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-gr-md space-y-gr-sm">
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="calendar" class="w-4 h-4 text-green-700"></i>
                        <span class="text-green-700">Date:</span>
                        <span class="font-semibold text-green-900">${new Date(props.startTime).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="clock" class="w-4 h-4 text-green-700"></i>
                        <span class="text-green-700">Time:</span>
                        <span class="font-semibold text-green-900">${formatTime(props.startTime)} - ${formatTime(props.endTime)}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="building" class="w-4 h-4 text-green-700"></i>
                        <span class="text-green-700">Source:</span>
                        <span class="font-semibold text-green-900">${props.source_system}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="user" class="w-4 h-4 text-green-700"></i>
                        <span class="text-green-700">Organizer:</span>
                        <span class="font-semibold text-green-900">${props.organizer_name}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="users" class="w-4 h-4 text-green-700"></i>
                        <span class="text-green-700">Expected Attendees:</span>
                        <span class="font-semibold text-green-900">${props.expected_attendees} people</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="mic" class="w-4 h-4 text-green-700"></i>
                        <span class="text-green-700">Speakers:</span>
                        <span class="font-semibold text-green-900">${props.speakers_count} speaker(s)</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <span class="w-4 h-4 flex items-center justify-center text-green-700 font-bold text-body">₱</span>
                        <span class="text-green-700">Approved Budget:</span>
                        <span class="font-semibold text-green-900">₱${parseFloat(props.approved_budget).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
                    </div>
                </div>
                
                ${props.program_description ? `
                <div>
                    <p class="text-caption text-gray-500 mb-1">Program Description</p>
                    <p class="text-small text-lgu-paragraph">${props.program_description}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('viewDetailsButton').href = `/admin/government-programs/${props.program_id}`;
        document.getElementById('eventModal').classList.remove('hidden');
        
        // Reinitialize Lucide icons
        lucide.createIcons();
    };
    
    // Show city event details
    window.showCityEventDetails = function(event, props) {
        const modalContent = `
            <div class="space-y-gr-md">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-h4 font-bold text-lgu-headline">${event.title.replace('[CITY EVENT] ', '')}</h4>
                        <p class="text-small text-lgu-paragraph">${props.facility_name}</p>
                    </div>
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-lg text-small font-semibold">${props.category}</span>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-gr-md space-y-gr-sm">
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="calendar" class="w-4 h-4 text-yellow-700"></i>
                        <span class="text-yellow-700">Date:</span>
                        <span class="font-semibold text-yellow-900">${new Date(props.startTime).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                    </div>
                    <div class="flex items-center gap-2 text-small">
                        <i data-lucide="clock" class="w-4 h-4 text-yellow-700"></i>
                        <span class="text-yellow-700">Time:</span>
                        <span class="font-semibold text-yellow-900">${formatTime(props.startTime)} - ${formatTime(props.endTime)}</span>
                    </div>
                </div>
                
                ${props.event_description ? `
                <div>
                    <p class="text-caption text-gray-500 mb-1">Event Description</p>
                    <p class="text-small text-lgu-paragraph">${props.event_description}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = modalContent;
        document.getElementById('viewDetailsButton').href = `/admin/city-events/${props.event_id}`;
        document.getElementById('eventModal').classList.remove('hidden');
        
        // Reinitialize Lucide icons
        lucide.createIcons();
    };
    
    window.closeEventModal = function() {
        document.getElementById('eventModal').classList.add('hidden');
    };
    
    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEventModal();
        }
    });
});
</script>

<style>
/* Golden Ratio spacing for calendar buttons */
.fc .fc-button {
    padding: 8px 16px !important;
    margin: 0 4px !important;
    border-radius: 8px !important;
    font-weight: 600 !important;
    transition: all 0.2s !important;
}

.fc .fc-button-primary {
    background-color: #faae2b !important;
    border-color: #faae2b !important;
    color: #00473e !important;
}

.fc .fc-button-primary:hover {
    background-color: #e09900 !important;
    border-color: #e09900 !important;
}

.fc .fc-button-primary:not(:disabled):active,
.fc .fc-button-primary:not(:disabled).fc-button-active {
    background-color: #00473e !important;
    border-color: #00473e !important;
    color: #faae2b !important;
}

.fc .fc-toolbar-chunk {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

.fc .fc-toolbar {
    margin-bottom: 24px !important;
}

.fc-event {
    border-radius: 6px !important;
    border: none !important;
    padding: 4px 8px !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

.fc-event-title {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
    display: block !important;
}

/* Additional truncation for all FullCalendar event types */
.fc-daygrid-event {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

.fc-event-main {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

.fc-event-title-container {
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

.fc-h-event {
    overflow: hidden !important;
}

.fc-daygrid-event-harness {
    overflow: hidden !important;
}
</style>
@endpush

@endsection

