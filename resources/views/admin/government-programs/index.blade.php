@extends('layouts.admin')

@section('page-title', 'Government Programs')
@section('page-subtitle', 'Manage facility requests from Energy Efficiency system')

@section('page-content')
<div class="space-y-6">
    <!-- Error Messages -->
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i data-lucide="alert-circle" class="w-6 h-6 text-red-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-red-800">Error</h3>
                <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto flex-shrink-0">
                <i data-lucide="x" class="w-5 h-5 text-red-400 hover:text-red-600"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Success Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-semibold text-green-800">Success</h3>
                <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-auto flex-shrink-0">
                <i data-lucide="x" class="w-5 h-5 text-green-400 hover:text-green-600"></i>
            </button>
        </div>
    </div>
    @endif

    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-lgu-headline">Program Requests</h2>
            <p class="text-lgu-paragraph mt-1">
                Facility requests from Energy Efficiency & Conservation subsystem
            </p>
        </div>
    </div>

    <!-- Connection Status -->
    <div class="bg-white rounded-xl shadow-sm p-6 border border-lgu-stroke">
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0">
                <i data-lucide="database" class="w-8 h-8 text-lgu-green"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-lgu-headline">Integration Status</h3>
                <p class="text-sm text-lgu-paragraph">
                    Testing Phase: Local database integration
                </p>
            </div>
            <div class="flex-shrink-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                    <i data-lucide="test-tube" class="w-4 h-4 mr-2"></i>
                    Testing Mode
                </span>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-lgu-stroke">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-lgu-paragraph">Pending Requests</p>
                    <h3 class="text-3xl font-bold text-yellow-600 mt-1">{{ $totalPending ?? 0 }}</h3>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i data-lucide="clock" class="w-8 h-8 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-lgu-stroke">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-lgu-paragraph">Processed</p>
                    <h3 class="text-3xl font-bold text-lgu-green mt-1">{{ $totalProcessed ?? 0 }}</h3>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i data-lucide="check-circle" class="w-8 h-8 text-lgu-green"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border border-lgu-stroke">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-lgu-paragraph">Connection</p>
                    <h3 class="text-sm font-semibold mt-1
                        {{ ($connectionStatus ?? 'failed') === 'connected' ? 'text-lgu-green' : 'text-red-600' }}">
                        {{ ($connectionStatus ?? 'failed') === 'connected' ? 'Connected' : 'Disconnected' }}
                    </h3>
                </div>
                <div class="rounded-full p-3 
                    {{ ($connectionStatus ?? 'failed') === 'connected' ? 'bg-green-100' : 'bg-red-100' }}">
                    <i data-lucide="database" class="w-8 h-8 
                        {{ ($connectionStatus ?? 'failed') === 'connected' ? 'text-lgu-green' : 'text-red-600' }}"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Requests Section -->
    @if(isset($pendingRequests) && $pendingRequests->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
        <div class="p-6 border-b border-lgu-stroke">
            <h3 class="text-xl font-bold text-lgu-headline">Pending Facility Requests</h3>
            <p class="text-sm text-lgu-paragraph mt-1">New seminar requests from Energy Efficiency system</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-lgu-headline text-white">
                    <tr>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Seminar Details</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Date & Time</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Location</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pendingRequests as $seminar)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-gr-md py-gr-sm">
                            <div class="text-small font-medium text-lgu-headline">{{ $seminar->seminar_title }}</div>
                            <div class="text-caption text-lgu-paragraph">{{ Str::limit($seminar->description, 80) }}</div>
                        </td>
                        <td class="px-gr-md py-gr-sm">
                            <div class="text-small text-lgu-headline">{{ \Carbon\Carbon::parse($seminar->seminar_date)->format('M d, Y') }}</div>
                            <div class="text-caption text-lgu-paragraph">
                                {{ \Carbon\Carbon::parse($seminar->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($seminar->end_time)->format('g:i A') }}
                            </div>
                        </td>
                        <td class="px-gr-md py-gr-sm">
                            <div class="text-small text-lgu-paragraph">{{ $seminar->location }}</div>
                            <div class="text-caption text-gray-500">{{ $seminar->target_area }}</div>
                        </td>
                        <td class="px-gr-md py-gr-sm">
                            <a href="{{ route('admin.government-programs.preview', $seminar->seminar_id) }}" 
                               class="inline-flex items-center gap-1.5 px-gr-sm py-1.5 bg-lgu-button hover:bg-lgu-highlight text-lgu-button-text text-caption font-medium rounded-lg transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Review Request
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Processed Programs Section -->
    @if(isset($programs) && $programs->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden mt-6">
        <div class="p-6 border-b border-lgu-stroke">
            <h3 class="text-xl font-bold text-lgu-headline">Confirmed Programs</h3>
            <p class="text-sm text-lgu-paragraph mt-1">Government programs that have been accepted and confirmed</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-lgu-headline text-white">
                    <tr>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Program Title</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Facility Assigned</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Event Date</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Status</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($programs as $program)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-gr-md py-gr-sm">
                            <div class="text-small font-medium text-lgu-headline">{{ $program->program_title }}</div>
                            <div class="text-caption text-lgu-paragraph">Organizer: {{ $program->organizer_name }}</div>
                        </td>
                        <td class="px-gr-md py-gr-sm">
                            <div class="text-small text-lgu-headline">
                                {{ $program->assignedFacility->name ?? 'Not assigned' }}
                            </div>
                            <div class="text-caption text-lgu-paragraph">
                                {{ $program->assignedFacility->lguCity->city_name ?? '' }}
                            </div>
                        </td>
                        <td class="px-gr-md py-gr-sm">
                            <div class="text-small text-lgu-headline">
                                {{ \Carbon\Carbon::parse($program->event_date)->format('M d, Y') }}
                            </div>
                            <div class="text-caption text-lgu-paragraph">
                                {{ \Carbon\Carbon::parse($program->start_time)->format('g:i A') }} - 
                                {{ \Carbon\Carbon::parse($program->end_time)->format('g:i A') }}
                            </div>
                        </td>
                        <td class="px-gr-md py-gr-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($program->coordination_status === 'confirmed') bg-green-100 text-green-800
                                @elseif($program->coordination_status === 'completed') bg-blue-100 text-blue-800
                                @elseif($program->coordination_status === 'cancelled') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $program->coordination_status)) }}
                            </span>
                        </td>
                        <td class="px-gr-md py-gr-sm">
                            <a href="{{ route('admin.government-programs.show', $program->id) }}" 
                               class="inline-flex items-center gap-1.5 px-gr-sm py-1.5 bg-lgu-button hover:bg-lgu-highlight text-lgu-button-text text-caption font-medium rounded-lg transition-colors">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Empty State (only show if no pending requests) -->
    @if(!isset($pendingRequests) || $pendingRequests->count() === 0)
    <div class="bg-white rounded-xl shadow-sm p-12 border border-lgu-stroke text-center">
        <div class="max-w-md mx-auto">
            <div class="mb-6">
                <i data-lucide="inbox" class="w-24 h-24 mx-auto text-gray-300"></i>
            </div>
            <h3 class="text-xl font-bold text-lgu-headline mb-3">
                No Pending Requests
            </h3>
            <p class="text-lgu-paragraph mb-6">
                @if(($connectionStatus ?? 'failed') === 'connected')
                    All seminar requests from Energy Efficiency have been processed.
                @else
                    Cannot connect to Energy Efficiency database. Check your .env configuration.
                @endif
            </p>
            
            @if(($connectionStatus ?? 'failed') === 'failed')
            <!-- Testing Instructions -->
            <div class="bg-blue-50 rounded-lg p-6 text-left">
                <h4 class="font-semibold text-blue-900 mb-3 flex items-center">
                    <i data-lucide="lightbulb" class="w-5 h-5 mr-2"></i>
                    Setup Instructions
                </h4>
                <ol class="text-sm text-blue-800 space-y-2 list-decimal list-inside">
                    <li>Import Energy Efficiency database: <code class="bg-blue-100 px-2 py-1 rounded">ener_nova_capri.sql</code></li>
                    <li>Run SQL file: <code class="bg-blue-100 px-2 py-1 rounded">add_facility_response_tables.sql</code></li>
                    <li>Configure database connection in <code class="bg-blue-100 px-2 py-1 rounded">.env</code></li>
                    <li>Run: <code class="bg-blue-100 px-2 py-1 rounded">php artisan config:clear</code></li>
                </ol>
                @if(isset($connectionError))
                <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded">
                    <p class="text-xs text-red-800"><strong>Error:</strong> {{ $connectionError }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
    @endif

</div>

@push('scripts')
<script>
    // Initialize Lucide icons
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
            
            // Re-initialize after a short delay to catch any late-loading elements
            setTimeout(function() {
                lucide.createIcons();
            }, 100);
        }
    });
</script>
@endpush

@endsection

