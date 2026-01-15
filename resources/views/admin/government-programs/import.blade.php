@extends('layouts.admin')

@section('title', 'Import from Energy Efficiency System')

@section('content')
<div class="container mx-auto px-4 py-8">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-lgu-headline mb-2">Import Government Programs</h1>
                <p class="text-lgu-paragraph">Sync seminars from Energy Efficiency & Conservation System</p>
            </div>
            
            @if($connectionStatus === 'connected')
                <div class="flex items-center gap-2 bg-green-50 px-4 py-2 rounded-lg">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    <span class="text-green-700 font-medium">Connected to ener_nova_capri</span>
                </div>
            @else
                <div class="flex items-center gap-2 bg-red-50 px-4 py-2 rounded-lg">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                    <span class="text-red-700 font-medium">Connection Failed</span>
                </div>
            @endif
        </div>
    </div>

    @if($connectionStatus === 'failed')
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded-lg mb-8">
            <div class="flex items-start gap-3">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600 mt-0.5"></i>
                <div>
                    <h3 class="text-lg font-semibold text-red-800 mb-2">Database Connection Error</h3>
                    <p class="text-red-700 mb-4">{{ $error }}</p>
                    <div class="bg-white p-4 rounded border border-red-200">
                        <p class="text-sm font-mono text-gray-700">Make sure you've added to .env:</p>
                        <pre class="text-sm mt-2 text-gray-600">
EE_DB_HOST=127.0.0.1
EE_DB_PORT=3306
EE_DB_DATABASE=ener_nova_capri
EE_DB_USERNAME=root
EE_DB_PASSWORD=</pre>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
            <div class="flex items-center gap-2">
                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Available Seminars to Import --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-lgu-highlight rounded-lg flex items-center justify-center">
                    <i data-lucide="download" class="w-6 h-6 text-lgu-button-text"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-lgu-headline">Available Seminars</h2>
                    <p class="text-lgu-paragraph text-sm">{{ $notImported->count() }} new seminars ready to import</p>
                </div>
            </div>

            @if($notImported->count() > 0)
                <form action="{{ route('admin.government-programs.import-bulk') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:opacity-90 transition flex items-center gap-2">
                        <i data-lucide="download-cloud" class="w-5 h-5"></i>
                        Import All ({{ $notImported->count() }})
                    </button>
                </form>
            @endif
        </div>

        @if($notImported->count() === 0)
            <div class="text-center py-12">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mx-auto mb-4"></i>
                <p class="text-gray-500 text-lg">No new seminars to import</p>
                <p class="text-gray-400 text-sm">All available seminars have been imported</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach($notImported as $seminar)
                    <div class="border border-gray-200 rounded-lg p-6 hover:border-lgu-highlight transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
                                        #{{ $seminar->seminar_id }}
                                    </span>
                                    <h3 class="text-xl font-bold text-lgu-headline">{{ $seminar->seminar_title }}</h3>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="flex items-center gap-2 text-lgu-paragraph">
                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                        <span>{{ date('F j, Y', strtotime($seminar->seminar_date)) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-lgu-paragraph">
                                        <i data-lucide="clock" class="w-4 h-4"></i>
                                        <span>{{ date('g:i A', strtotime($seminar->start_time)) }} - {{ date('g:i A', strtotime($seminar->end_time)) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-lgu-paragraph">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                        <span>{{ $seminar->location ?: 'Location TBD' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-lgu-paragraph">
                                        <i data-lucide="users" class="w-4 h-4"></i>
                                        <span>Target Area: {{ $seminar->target_area ?: 'All Areas' }}</span>
                                    </div>
                                </div>

                                @if($seminar->description)
                                    <p class="text-gray-600 text-sm line-clamp-2">{{ $seminar->description }}</p>
                                @endif
                            </div>

                            <form action="{{ route('admin.government-programs.import-single', $seminar->seminar_id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-lgu-button text-lgu-button-text font-medium rounded-lg hover:opacity-90 transition flex items-center gap-2">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    Import
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Already Imported Seminars --}}
    @if($alreadyImported->count() > 0)
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-square" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-lgu-headline">Already Imported</h2>
                    <p class="text-lgu-paragraph text-sm">{{ $alreadyImported->count() }} seminars already in system</p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($alreadyImported as $seminar)
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 flex-1">
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                    <i data-lucide="check" class="w-3 h-3 inline"></i>
                                    Imported
                                </span>
                                <h4 class="font-semibold text-lgu-headline">{{ $seminar->seminar_title }}</h4>
                                <span class="text-gray-500 text-sm">{{ date('M j, Y', strtotime($seminar->seminar_date)) }}</span>
                            </div>
                            <a href="{{ route('admin.government-programs.index') }}" class="text-lgu-button-text hover:underline text-sm font-medium">
                                View Details →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
// Initialize Lucide icons
lucide.createIcons();
</script>
@endsection

