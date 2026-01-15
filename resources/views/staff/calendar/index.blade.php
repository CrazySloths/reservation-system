@extends('layouts.staff')

@section('page-title', 'Facility Calendar')
@section('page-subtitle', 'View booking schedule and check for conflicts')

@section('page-content')
<div class="space-y-gr-md">
    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md">
        <div class="flex flex-wrap items-end gap-gr-sm">
            <!-- Facility Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="facility-filter" class="block text-small font-medium text-lgu-headline mb-2">
                    Filter by Facility
                </label>
                <select id="facility-filter" 
                    class="w-full px-4 py-2.5 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none text-lgu-paragraph">
                    <option value="all">All Facilities</option>
                    @foreach($facilities as $facility)
                        <option value="{{ $facility->facility_id }}">{{ $facility->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div class="flex-1 min-w-[200px]">
                <label for="status-filter" class="block text-small font-medium text-lgu-headline mb-2">
                    Filter by Status
                </label>
                <select id="status-filter" 
                    class="w-full px-4 py-2.5 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none text-lgu-paragraph">
                    <option value="all" selected>All Confirmed Bookings</option>
                    <option value="staff_verified">Approved Only (Awaiting Payment)</option>
                    <option value="paid">Paid Only</option>
                    <option value="confirmed">Confirmed Only</option>
                    <option value="pending">Pending Verification</option>
                </select>
            </div>

            <!-- Apply Button -->
            <div>
                <button id="apply-filters" 
                    class="px-6 py-2.5 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:opacity-90 transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-filter">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                    </svg>
                    Apply Filters
                </button>
            </div>

            <!-- Reset Button -->
            <div>
                <button id="reset-filters" 
                    class="px-6 py-2.5 border-2 border-lgu-stroke text-lgu-headline font-semibold rounded-lg hover:bg-lgu-bg transition flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw">
                        <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                        <path d="M3 3v5h5"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md">
        <h3 class="text-body font-bold text-lgu-headline mb-gr-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4"/>
                <path d="M12 8h.01"/>
            </svg>
            Booking Status Legend
        </h3>
        <div class="flex flex-wrap gap-gr-sm">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-[#34d399] border-2 border-[#10b981] rounded"></div>
                <span class="text-small text-lgu-paragraph">Approved (Awaiting Payment)</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-[#60a5fa] border-2 border-[#3b82f6] rounded"></div>
                <span class="text-small text-lgu-paragraph">Paid & Confirmed</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-[#fbbf24] border-2 border-[#f59e0b] rounded"></div>
                <span class="text-small text-lgu-paragraph">Pending Verification</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-[#f87171] border-2 border-[#ef4444] rounded"></div>
                <span class="text-small text-lgu-paragraph">Rejected/Cancelled</span>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-md">
        <div id="calendar" class="calendar-container"></div>
    </div>
</div>

<!-- Event Details Modal -->
<div id="event-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" style="display: none;">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-lgu-headline p-gr-md flex items-center justify-between">
            <h3 class="text-h4 font-bold text-white flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check">
                    <path d="M8 2v4"/>
                    <path d="M16 2v4"/>
                    <rect width="18" height="18" x="3" y="4" rx="2"/>
                    <path d="M3 10h18"/>
                    <path d="m9 16 2 2 4-4"/>
                </svg>
                Booking Details
            </h3>
            <button id="close-modal" class="text-white hover:text-gray-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div id="modal-content" class="p-gr-md space-y-gr-sm">
            <!-- Content will be injected here by JavaScript -->
        </div>

        <!-- Modal Footer -->
        <div class="bg-lgu-bg p-gr-md flex justify-end gap-gr-sm border-t border-lgu-stroke">
            <button id="view-details-btn" class="px-6 py-2.5 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:opacity-90 transition">
                View Full Details
            </button>
            <button id="close-modal-btn" class="px-6 py-2.5 border-2 border-lgu-stroke text-lgu-headline font-semibold rounded-lg hover:bg-lgu-bg transition">
                Close
            </button>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    /* FullCalendar Customization for LGU Theme */
    .fc {
        font-family: 'Poppins', sans-serif;
    }

    .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #00473e;
    }

    .fc-button {
        background-color: #faae2b !important;
        border-color: #faae2b !important;
        color: #00473e !important;
        font-weight: 600 !important;
        text-transform: capitalize !important;
        padding: 0.5rem 1rem !important;
        border-radius: 0.5rem !important;
        margin: 0 0.382rem !important; /* Golden Ratio spacing */
    }

    .fc-toolbar-chunk {
        display: flex !important;
        gap: 0.618rem !important; /* Golden Ratio gap between button groups */
    }

    .fc-button:hover {
        opacity: 0.9 !important;
    }

    .fc-button:disabled {
        opacity: 0.5 !important;
    }

    .fc-button-active {
        background-color: #00473e !important;
        border-color: #00473e !important;
        color: #faae2b !important;
    }

    .fc-daygrid-day-number {
        color: #00473e;
        font-weight: 500;
    }

    .fc-col-header-cell-cushion {
        color: #00473e;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .fc-event {
        cursor: pointer;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .fc-event-title {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: block;
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

    .fc-event:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .fc-daygrid-day.fc-day-today {
        background-color: #f2f7f5 !important;
    }

    .calendar-container {
        min-height: 600px;
    }
</style>
@endpush

@push('scripts')
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('calendar');
    const eventModal = document.getElementById('event-modal');
    const modalContent = document.getElementById('modal-content');
    const closeModalBtn = document.getElementById('close-modal-btn');
    const closeModalX = document.getElementById('close-modal');
    const viewDetailsBtn = document.getElementById('view-details-btn');
    let currentBookingId = null;

    // Initialize FullCalendar
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        height: 'auto',
        editable: false,
        selectable: false,
        selectMirror: true,
        dayMaxEvents: 4,
        moreLinkText: 'more',
        eventDisplay: 'block',
        displayEventTime: false,
        displayEventEnd: false,
        eventContent: function(arg) {
            // Custom event rendering with correct time (no timezone conversion)
            const startTime = arg.event.extendedProps.startTime || arg.event.start;
            if (!startTime) return { html: arg.event.title };
            
            // Extract hour from ISO or datetime format
            let hour;
            if (typeof startTime === 'string' && startTime.includes('T')) {
                const timePart = startTime.split('T')[1].split(/[+Z]/)[0];
                hour = parseInt(timePart.split(':')[0]);
            } else {
                const date = new Date(startTime);
                hour = date.getHours();
            }
            
            const ampm = hour >= 12 ? 'p' : 'a';
            const displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
            
            return { html: `${displayHour}${ampm} ${arg.event.title}` };
        },
        events: function(info, successCallback, failureCallback) {
            // Get filter values
            const facilityId = document.getElementById('facility-filter').value;
            const status = document.getElementById('status-filter').value;
            
            const url = `{{ route('staff.calendar.events') }}?facility_id=${facilityId}&status=${status}&start=${info.startStr}&end=${info.endStr}`;
            console.log('Fetching calendar events:', url);

            // Fetch events from server with proper headers
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    
                    // Check if response is OK
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    // Get the response as text first to check what we're receiving
                    return response.text();
                })
                .then(text => {
                    console.log('Raw response:', text.substring(0, 500)); // Log first 500 chars
                    
                    // Try to parse as JSON
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);
                    
                    // Check if it's an error response
                    if (data.error) {
                        console.error('Server error:', data);
                        alert('Server Error: ' + data.message);
                        failureCallback(data);
                        return;
                    }
                    
                    // Check if data is an array
                    if (!Array.isArray(data)) {
                        console.error('Data is not an array:', typeof data, data);
                        failureCallback(new Error('Invalid data format'));
                        return;
                    }
                    
                    console.log('Events received:', data.length, 'event(s)');
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    alert('Error loading calendar events: ' + error.message);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            // Show event details in modal
            const event = info.event;
            const props = event.extendedProps;
            
            currentBookingId = event.id;

            // Check if this is a city event
            if (props.event_type === 'city_event') {
                // City Event modal content
                modalContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Event Type</p>
                                <p class="text-body font-bold text-lgu-button">City Event</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color: ${event.backgroundColor}; color: ${event.textColor};">
                                    ${props.status.toUpperCase()}
                                </span>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Event Title</p>
                            <p class="text-body font-semibold text-gray-900">${event.title.replace('[CITY EVENT] ', '')}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Facility</p>
                            <p class="text-body font-semibold text-gray-900">${props.facility_name}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Date & Time</p>
                                <p class="text-small text-gray-900">
                                    ${new Date(event.start).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                </p>
                                <p class="text-small text-gray-600">
                                    ${formatTime(props.startTime)} - ${formatTime(props.endTime)}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Category</p>
                                <p class="text-body font-bold text-lgu-headline">${props.category}</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Description</p>
                            <p class="text-small text-gray-900">${props.event_description || 'No description provided'}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Affected Bookings</p>
                            <p class="text-small text-gray-900">${props.affected_bookings} citizen booking(s) affected</p>
                        </div>
                    </div>
                `;
            } else {
                // Build modal content for regular bookings
                modalContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Booking Reference</p>
                                <p class="text-body font-bold text-lgu-headline">${props.bookingId}</p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Status</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium" style="background-color: ${event.backgroundColor}; color: ${event.textColor};">
                                    ${props.statusLabel}
                                </span>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Facility</p>
                            <p class="text-body font-semibold text-gray-900">${props.facilityName}</p>
                            <p class="text-small text-gray-600">${props.cityName}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Date & Time</p>
                                <p class="text-small text-gray-900">
                                    ${new Date(event.start).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                </p>
                                <p class="text-small text-gray-600">
                                    ${formatTime(props.startTime)} - ${formatTime(props.endTime)}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Expected Attendees</p>
                                <p class="text-body font-bold text-lgu-headline">${props.attendees.toLocaleString()}</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Purpose/Event</p>
                            <p class="text-small text-gray-900">${props.purpose}</p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Booked By</p>
                            <p class="text-small text-gray-900">${props.userName}</p>
                        </div>
                    </div>
                `;
            }

            // Show modal
            eventModal.style.display = 'flex';
        }
    });

    calendar.render();

    // Filter functionality
    document.getElementById('apply-filters').addEventListener('click', function() {
        calendar.refetchEvents();
    });

    document.getElementById('reset-filters').addEventListener('click', function() {
        document.getElementById('facility-filter').value = 'all';
        document.getElementById('status-filter').value = 'staff_verified';
        calendar.refetchEvents();
    });

    // Modal controls
    closeModalBtn.addEventListener('click', function() {
        eventModal.style.display = 'none';
    });

    closeModalX.addEventListener('click', function() {
        eventModal.style.display = 'none';
    });

    viewDetailsBtn.addEventListener('click', function() {
        if (currentBookingId) {
            window.location.href = `/staff/bookings/${currentBookingId}/review`;
        }
    });

    // Close modal when clicking outside
    eventModal.addEventListener('click', function(e) {
        if (e.target === eventModal) {
            eventModal.style.display = 'none';
        }
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
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && eventModal.style.display === 'flex') {
            eventModal.style.display = 'none';
        }
    });
});
</script>
@endpush

