@extends('layouts.admin')

@section('page-title', 'Review Seminar Request')
@section('page-subtitle', 'Review and process facility request from Energy Efficiency')

@section('page-content')
<div class="space-y-gr-xl">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.government-programs.index') }}" 
           class="inline-flex items-center gap-2 text-lgu-paragraph hover:text-lgu-headline transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            <span class="font-medium">Back to Requests</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="bg-lgu-headline rounded-2xl p-gr-xl shadow-lg text-white">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-gr-sm">
                    <div class="bg-white/20 rounded-xl p-3">
                        <i data-lucide="calendar-check" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-400 text-yellow-900 mb-2">
                            <i data-lucide="clock" class="w-3.5 h-3.5 mr-1.5"></i>
                            Pending Review
                        </span>
                        <h1 class="text-h2 font-bold">{{ $seminar->seminar_title }}</h1>
                    </div>
                </div>
                <p class="text-white/90 text-body ml-16">
                    Seminar request from Energy Efficiency & Conservation system
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-lg">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-gr-lg">
            <!-- Seminar Details Card -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                    <h2 class="text-h3 font-bold text-lgu-headline flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-lgu-headline"></i>
                        Seminar Details
                    </h2>
                </div>
                <div class="p-gr-lg space-y-gr-md">
                    <!-- Description -->
                    <div>
                        <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Description</label>
                        <p class="text-body text-lgu-paragraph leading-relaxed">{{ $seminar->description }}</p>
                    </div>

                    <!-- Date and Time -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-md">
                        <div>
                            <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Date</label>
                            <div class="flex items-center gap-2 text-body text-lgu-paragraph">
                                <i data-lucide="calendar" class="w-4 h-4 text-lgu-headline"></i>
                                {{ \Carbon\Carbon::parse($seminar->seminar_date)->format('l, F d, Y') }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Time</label>
                            <div class="flex items-center gap-2 text-body text-lgu-paragraph">
                                <i data-lucide="clock" class="w-4 h-4 text-lgu-headline"></i>
                                {{ \Carbon\Carbon::parse($seminar->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($seminar->end_time)->format('g:i A') }}
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-md">
                        <div>
                            <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Preferred Location</label>
                            <div class="flex items-center gap-2 text-body text-lgu-paragraph">
                                <i data-lucide="map-pin" class="w-4 h-4 text-lgu-headline"></i>
                                {{ $seminar->location }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Target Area</label>
                            <div class="flex items-center gap-2 text-body text-lgu-paragraph">
                                <i data-lucide="target" class="w-4 h-4 text-lgu-headline"></i>
                                {{ $seminar->target_area }}
                            </div>
                        </div>
                    </div>

                    <!-- Expected Attendees -->
                    <div>
                        <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Expected Attendees</label>
                        <div class="flex items-center gap-2 text-body text-lgu-paragraph">
                            <i data-lucide="users" class="w-4 h-4 text-lgu-headline"></i>
                            {{ $attendees->count() }} registered participants
                        </div>
                    </div>

                    <!-- Seminar Image -->
                    @if(isset($seminar->seminar_image_url) && $seminar->seminar_image_url)
                    <div>
                        <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Seminar Image</label>
                        <div class="bg-blue-50 rounded-lg p-gr-md border border-blue-200">
                            <div class="flex items-start gap-gr-sm">
                                <div class="bg-blue-100 rounded-lg p-2 flex-shrink-0">
                                    <i data-lucide="image" class="w-6 h-6 text-blue-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-small font-semibold text-lgu-headline truncate">
                                        {{ basename($seminar->seminar_image_url) }}
                                    </p>
                                    <p class="text-caption text-blue-700">
                                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                        Hosted on Energy Efficiency system
                                    </p>
                                    <p class="text-caption text-lgu-paragraph mt-1 font-mono text-xs truncate">
                                        {{ $seminar->seminar_image_url }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Attachments -->
                    @if(isset($seminar->attachments_path) && $seminar->attachments_path)
                    <div>
                        <label class="block text-small font-semibold text-lgu-headline mb-gr-xs">Attachments</label>
                        <div class="bg-orange-50 rounded-lg p-gr-md border border-orange-200">
                            <div class="flex items-start gap-gr-sm">
                                <div class="bg-orange-100 rounded-lg p-2 flex-shrink-0">
                                    <i data-lucide="file-text" class="w-6 h-6 text-orange-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-small font-semibold text-lgu-headline truncate">
                                        {{ basename($seminar->attachments_path) }}
                                    </p>
                                    <p class="text-caption text-orange-700">
                                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                                        PDF Document - Hosted on Energy Efficiency system
                                    </p>
                                    <p class="text-caption text-lgu-paragraph mt-1 font-mono text-xs truncate">
                                        {{ $seminar->attachments_path }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Organizer Information -->
            @if($organizer)
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                    <h2 class="text-h3 font-bold text-lgu-headline flex items-center gap-2">
                        <i data-lucide="user-circle" class="w-5 h-5 text-lgu-headline"></i>
                        Organizer Information
                    </h2>
                </div>
                <div class="p-gr-lg">
                    <div class="flex items-start gap-gr-md">
                        <div class="w-16 h-16 bg-lgu-button rounded-full flex items-center justify-center text-white text-h3 font-bold flex-shrink-0">
                            {{ strtoupper(substr($organizer->first_name, 0, 1)) }}{{ strtoupper(substr($organizer->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-1 space-y-gr-sm">
                            <div>
                                <p class="text-h4 font-bold text-lgu-headline">
                                    {{ $organizer->first_name }} {{ $organizer->last_name }}
                                </p>
                                <p class="text-small text-lgu-paragraph capitalize">{{ $organizer->user_role }}</p>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-sm text-small">
                                <div class="flex items-center gap-2 text-lgu-paragraph">
                                    <i data-lucide="mail" class="w-4 h-4 text-lgu-headline"></i>
                                    {{ $organizer->email }}
                                </div>
                                <div class="flex items-center gap-2 text-lgu-paragraph">
                                    <i data-lucide="phone" class="w-4 h-4 text-lgu-headline"></i>
                                    {{ $organizer->cellphone_number }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Registered Attendees -->
            @if($attendees->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                    <h2 class="text-h3 font-bold text-lgu-headline flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-lgu-headline"></i>
                        Registered Attendees ({{ $attendees->count() }})
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-gr-md py-gr-sm text-left text-caption font-semibold text-gray-600 uppercase">Name</th>
                                <th class="px-gr-md py-gr-sm text-left text-caption font-semibold text-gray-600 uppercase">Area</th>
                                <th class="px-gr-md py-gr-sm text-left text-caption font-semibold text-gray-600 uppercase">Contact</th>
                                <th class="px-gr-md py-gr-sm text-left text-caption font-semibold text-gray-600 uppercase">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($attendees as $attendee)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-gr-md py-gr-sm">
                                    <div class="flex items-center gap-gr-xs">
                                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 text-caption font-semibold">
                                            {{ strtoupper(substr($attendee->name, 0, 1)) }}
                                        </div>
                                        <span class="text-small font-medium text-lgu-headline">{{ $attendee->name }}</span>
                                    </div>
                                </td>
                                <td class="px-gr-md py-gr-sm text-small text-lgu-paragraph">{{ $attendee->area }}</td>
                                <td class="px-gr-md py-gr-sm">
                                    <div class="space-y-0.5 text-caption text-lgu-paragraph">
                                        <div>{{ $attendee->email }}</div>
                                        <div>{{ $attendee->cellphone_number }}</div>
                                    </div>
                                </td>
                                <td class="px-gr-md py-gr-sm text-caption text-lgu-paragraph">
                                    {{ \Carbon\Carbon::parse($attendee->joined_at)->format('M d, Y') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-gr-lg">
            <!-- Action Card -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden sticky top-gr-lg">
                <div class="bg-lgu-headline px-gr-lg py-gr-md">
                    <h3 class="text-h4 font-bold text-white flex items-center gap-2">
                        <i data-lucide="check-square" class="w-5 h-5"></i>
                        Actions
                    </h3>
                </div>
                <div class="p-gr-lg space-y-gr-md">
                    <p class="text-small text-lgu-paragraph">
                        Review the seminar details and decide whether to accept this facility request.
                    </p>
                    
                    <div class="space-y-gr-sm">
                        <a href="{{ route('admin.government-programs.accept-form', $seminar->seminar_id) }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-gr-lg py-gr-md bg-lgu-button hover:bg-lgu-highlight text-lgu-button-text font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Accept & Assign Facility
                        </a>
                        
                        <button onclick="rejectRequest()" 
                                class="w-full inline-flex items-center justify-center gap-2 px-gr-lg py-gr-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all duration-200">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            Decline Request
                        </button>
                    </div>
                </div>
            </div>

            <!-- Available Facilities -->
            @if($availableFacilities->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                    <h3 class="text-h4 font-bold text-lgu-headline flex items-center gap-2">
                        <i data-lucide="building" class="w-5 h-5 text-lgu-headline"></i>
                        Available Facilities
                    </h3>
                    <p class="text-caption text-lgu-paragraph mt-1">
                        Facilities that can accommodate {{ $attendees->count() }} attendees
                    </p>
                </div>
                <div class="p-gr-md space-y-gr-sm">
                    @foreach($availableFacilities as $facility)
                    <div class="p-gr-sm border border-gray-200 rounded-lg hover:border-lgu-green hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-gr-xs">
                            <i data-lucide="building-2" class="w-4 h-4 text-lgu-headline mt-1 flex-shrink-0"></i>
                            <div class="flex-1">
                                <p class="text-small font-semibold text-lgu-headline">{{ $facility->name }}</p>
                                <p class="text-caption text-lgu-paragraph">Capacity: {{ $facility->capacity }} pax</p>
                                <p class="text-caption text-lgu-paragraph">{{ $facility->address }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-gr-md">
                <div class="flex items-start gap-gr-sm">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-small font-semibold text-yellow-900">No Available Facilities</p>
                        <p class="text-caption text-yellow-800 mt-1">
                            No facilities can currently accommodate {{ $attendees->count() }} attendees.
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });

    function acceptRequest() {
        @if($availableFacilities->count() === 0)
            Swal.fire({
                title: 'No Available Facilities',
                text: 'There are no facilities available that can accommodate {{ $attendees->count() }} attendees.',
                icon: 'error',
                confirmButtonColor: '#00473e',
                confirmButtonText: 'OK'
            });
            return;
        @endif

        // Build facility options HTML
        const facilitiesHTML = `
            <div class="text-left">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Select Facility</label>
                <select id="facility-select" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-headline text-sm">
                    <option value="">-- Choose a facility --</option>
                    @foreach($availableFacilities as $facility)
                    <option value="{{ $facility->facility_id }}" data-name="{{ $facility->name }}">
                        {{ $facility->name }} ({{ $facility->capacity }} pax) - {{ $facility->address }}
                    </option>
                    @endforeach
                </select>
            </div>
        `;

        Swal.fire({
            title: 'Accept & Assign Facility',
            html: facilitiesHTML,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#00473e',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Accept & Assign',
            cancelButtonText: 'Cancel',
            preConfirm: () => {
                const facilityId = document.getElementById('facility-select').value;
                if (!facilityId) {
                    Swal.showValidationMessage('Please select a facility');
                    return false;
                }
                return facilityId;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('admin.government-programs.accept', $seminar->seminar_id) }}';
                
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);
                
                const facilityInput = document.createElement('input');
                facilityInput.type = 'hidden';
                facilityInput.name = 'facility_id';
                facilityInput.value = result.value;
                form.appendChild(facilityInput);
                
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function rejectRequest() {
        Swal.fire({
            title: 'Decline Request?',
            text: 'This will notify the Energy Efficiency system that this facility request cannot be accommodated.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Decline',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // TODO: Implement reject logic
                Swal.fire({
                    title: 'Under Development',
                    text: 'The decline functionality is under development.',
                    icon: 'info',
                    confirmButtonColor: '#00473e'
                });
            }
        });
    }
</script>
@endpush

@endsection

