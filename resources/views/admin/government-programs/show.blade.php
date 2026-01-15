@extends('layouts.admin')

@section('page-title', 'Government Program Details')
@section('page-subtitle', 'View and manage government program coordination')

@section('page-content')
<div class="space-y-gr-lg">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-gr-sm mb-gr-xs">
                <a href="{{ route('admin.government-programs.index') }}" 
                   class="text-lgu-paragraph hover:text-lgu-headline transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-display-sm font-bold text-lgu-headline">{{ $program->program_title }}</h1>
            </div>
            <p class="text-small text-lgu-paragraph">Government program details and coordination status</p>
        </div>
        
        <div class="flex items-center gap-gr-sm">
            <span class="inline-flex items-center px-gr-sm py-gr-xs rounded-lg text-small font-medium
                @if($program->coordination_status === 'confirmed') bg-green-100 text-green-800
                @elseif($program->coordination_status === 'completed') bg-blue-100 text-blue-800
                @elseif($program->coordination_status === 'cancelled') bg-red-100 text-red-800
                @else bg-yellow-100 text-yellow-800
                @endif">
                <i data-lucide="check-circle" class="w-4 h-4 mr-1.5"></i>
                {{ ucfirst(str_replace('_', ' ', $program->coordination_status)) }}
            </span>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-lg">
        <!-- Left Column: Main Details -->
        <div class="lg:col-span-2 space-y-gr-lg">
            
            <!-- Program Information -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-lgu-headline text-white p-gr-md">
                    <h2 class="text-lg font-bold flex items-center">
                        <i data-lucide="calendar" class="w-5 h-5 mr-gr-xs"></i>
                        Program Information
                    </h2>
                </div>
                <div class="p-gr-md space-y-gr-md">
                    <div>
                        <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Description</label>
                        <p class="text-small text-lgu-headline">{{ $program->program_description ?? 'No description provided' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-gr-md">
                        <div>
                            <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Event Date</label>
                            <p class="text-small text-lgu-headline">{{ \Carbon\Carbon::parse($program->event_date)->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Time</label>
                            <p class="text-small text-lgu-headline">
                                {{ \Carbon\Carbon::parse($program->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($program->end_time)->format('g:i A') }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-gr-md">
                        <div>
                            <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Expected Attendees</label>
                            <p class="text-small text-lgu-headline">{{ $program->expected_attendees }} participants</p>
                        </div>
                        <div>
                            <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Program Type</label>
                            <p class="text-small text-lgu-headline">{{ ucfirst($program->program_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assigned Facility -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-lgu-headline text-white p-gr-md">
                    <h2 class="text-lg font-bold flex items-center">
                        <i data-lucide="building-2" class="w-5 h-5 mr-gr-xs"></i>
                        Assigned Facility
                    </h2>
                </div>
                <div class="p-gr-md">
                    @if($program->assignedFacility)
                    <div class="flex items-start gap-gr-md">
                        <div class="flex-shrink-0 w-12 h-12 bg-lgu-button rounded-lg flex items-center justify-center">
                            <i data-lucide="map-pin" class="w-6 h-6 text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-small font-bold text-lgu-headline">{{ $program->assignedFacility->name }}</h3>
                            <p class="text-caption text-lgu-paragraph mt-1">{{ $program->assignedFacility->address }}</p>
                            <p class="text-caption text-lgu-paragraph">{{ $program->assignedFacility->lguCity->city_name ?? '' }}</p>
                            <div class="mt-gr-sm flex items-center gap-gr-md text-caption text-lgu-paragraph">
                                <span><strong>Capacity:</strong> {{ $program->assignedFacility->capacity }} people</span>
                                <span><strong>Type:</strong> {{ $program->assignedFacility->type }}</span>
                            </div>
                        </div>
                    </div>
                    @else
                    <p class="text-small text-gray-500 italic">No facility assigned yet</p>
                    @endif
                </div>
            </div>

            <!-- Speakers -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-lgu-headline text-white p-gr-md">
                    <h2 class="text-lg font-bold flex items-center">
                        <i data-lucide="users" class="w-5 h-5 mr-gr-xs"></i>
                        Speakers ({{ $program->number_of_speakers }})
                    </h2>
                </div>
                <div class="p-gr-md">
                    @if($program->speaker_details && is_array($program->speaker_details) && count($program->speaker_details) > 0)
                        <div class="space-y-gr-sm">
                            @foreach($program->speaker_details as $index => $speaker)
                            <div class="flex items-start gap-gr-sm p-gr-sm bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-shrink-0 w-10 h-10 bg-lgu-button rounded-full flex items-center justify-center text-white font-bold">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-small font-semibold text-lgu-headline">{{ $speaker['name'] ?? 'N/A' }}</h4>
                                    <p class="text-caption text-lgu-paragraph">{{ $speaker['topic'] ?? 'No topic specified' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-small text-gray-500 italic">No speakers assigned yet</p>
                    @endif
                </div>
            </div>

            <!-- Budget Information -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-lgu-headline text-white p-gr-md">
                    <h2 class="text-lg font-bold flex items-center">
                        <i data-lucide="dollar-sign" class="w-5 h-5 mr-gr-xs"></i>
                        Budget & Fund Transparency
                    </h2>
                </div>
                <div class="p-gr-md space-y-gr-md">
                    <div class="flex items-center justify-between p-gr-sm bg-blue-50 rounded-lg border border-blue-200">
                        <span class="text-small font-semibold text-blue-900">Total Approved Budget</span>
                        <span class="text-lg font-bold text-blue-900">₱{{ number_format($program->approved_amount, 2) }}</span>
                    </div>

                    @if($program->fund_breakdown && is_array($program->fund_breakdown) && count($program->fund_breakdown) > 0)
                    <div>
                        <h4 class="text-small font-semibold text-lgu-headline mb-gr-sm">Itemized Budget Breakdown</h4>
                        <div class="space-y-gr-xs">
                            @foreach($program->fund_breakdown as $item)
                            <div class="flex items-center justify-between p-gr-xs border-b border-gray-200 last:border-0">
                                <span class="text-small text-lgu-paragraph">{{ $item['item'] ?? 'N/A' }}</span>
                                <span class="text-small font-semibold text-lgu-headline">₱{{ number_format($item['amount'] ?? 0, 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-gr-lg">
            
            <!-- Organizer Information -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-lgu-headline text-white p-gr-md">
                    <h2 class="text-small font-bold flex items-center">
                        <i data-lucide="user" class="w-4 h-4 mr-gr-xs"></i>
                        Organizer
                    </h2>
                </div>
                <div class="p-gr-md space-y-gr-sm">
                    <div>
                        <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Name</label>
                        <p class="text-small text-lgu-headline">{{ $program->organizer_name }}</p>
                    </div>
                    <div>
                        <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Email</label>
                        <p class="text-small text-lgu-headline">{{ $program->organizer_email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Contact</label>
                        <p class="text-small text-lgu-headline">{{ $program->organizer_contact }}</p>
                    </div>
                    @if($program->organizer_area)
                    <div>
                        <label class="text-caption font-semibold text-lgu-paragraph block mb-1">Area</label>
                        <p class="text-small text-lgu-headline">{{ $program->organizer_area }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Source System Info -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-lgu-headline text-white p-gr-md">
                    <h2 class="text-small font-bold flex items-center">
                        <i data-lucide="link" class="w-4 h-4 mr-gr-xs"></i>
                        Integration Details
                    </h2>
                </div>
                <div class="p-gr-md space-y-gr-sm text-caption">
                    <div class="flex items-center justify-between">
                        <span class="text-lgu-paragraph">Source System</span>
                        <span class="font-semibold text-lgu-headline">{{ ucfirst($program->source_system) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lgu-paragraph">Source ID</span>
                        <span class="font-semibold text-lgu-headline">{{ $program->source_seminar_id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lgu-paragraph">Assigned By</span>
                        <span class="font-semibold text-lgu-headline">{{ $program->assignedAdmin->name ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-lgu-paragraph">Assigned At</span>
                        <span class="font-semibold text-lgu-headline">{{ $program->assigned_at ? \Carbon\Carbon::parse($program->assigned_at)->format('M d, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                <div class="bg-lgu-headline text-white p-gr-md">
                    <h2 class="text-small font-bold">Quick Actions</h2>
                </div>
                <div class="p-gr-md space-y-gr-xs">
                    <button class="w-full inline-flex items-center justify-center gap-gr-xs px-gr-sm py-gr-xs bg-lgu-button hover:bg-lgu-highlight text-white text-small font-medium rounded-lg transition-colors">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Print Details
                    </button>
                    <button class="w-full inline-flex items-center justify-center gap-gr-xs px-gr-sm py-gr-xs bg-gray-500 hover:bg-gray-600 text-white text-small font-medium rounded-lg transition-colors">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Export PDF
                    </button>
                </div>
            </div>
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
</script>
@endpush

@endsection

