@extends('layouts.staff')

@section('title', 'Staff Dashboard')
@section('page-title', 'Staff Dashboard')
@section('page-subtitle', 'Booking Verification & Management')

@section('page-content')
<div class="space-y-gr-lg">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gr-md">
        <!-- Pending Verification Card -->
        <div class="bg-amber-50 rounded-xl p-gr-md shadow-md border-2 border-amber-300 hover:shadow-lg hover:scale-105 transition-all">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="flex-1">
                    <p class="text-small text-amber-700 font-semibold mb-1">Pending Verification</p>
                    <h3 class="text-h1 font-bold text-amber-900">{{ $stats['pending_verification'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-amber-200/60 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-amber-700">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <a href="{{ route('staff.verification-queue') }}" class="text-small text-amber-800 hover:text-amber-900 font-bold flex items-center group">
                View Queue
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right ml-2 group-hover:translate-x-1 transition-transform">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>
            </a>
        </div>

        <!-- Verified Today Card -->
        <div class="bg-green-50 rounded-xl p-gr-md shadow-md border-2 border-green-300 hover:shadow-lg hover:scale-105 transition-all">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="flex-1">
                    <p class="text-small text-green-700 font-semibold mb-1">Verified Today</p>
                    <h3 class="text-h1 font-bold text-green-900">{{ $stats['verified_today'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-green-200/60 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle text-green-700">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <path d="m9 11 3 3L22 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-small text-green-700 font-semibold">Great work!</p>
        </div>

        <!-- Rejected Today Card -->
        <div class="bg-red-50 rounded-xl p-gr-md shadow-md border-2 border-red-300 hover:shadow-lg hover:scale-105 transition-all">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="flex-1">
                    <p class="text-small text-red-700 font-semibold mb-1">Rejected Today</p>
                    <h3 class="text-h1 font-bold text-red-900">{{ $stats['rejected_today'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-red-200/60 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle text-red-700">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m15 9-6 6"/>
                        <path d="m9 9 6 6"/>
                    </svg>
                </div>
            </div>
            <p class="text-small text-red-700 font-semibold">With valid reasons</p>
        </div>

        <!-- Total Processed Card -->
        <div class="bg-blue-50 rounded-xl p-gr-md shadow-md border-2 border-blue-300 hover:shadow-lg hover:scale-105 transition-all">
            <div class="flex items-center justify-between mb-gr-sm">
                <div class="flex-1">
                    <p class="text-small text-blue-700 font-semibold mb-1">Total Processed Today</p>
                    <h3 class="text-h1 font-bold text-blue-900">{{ $stats['total_processed'] }}</h3>
                </div>
                <div class="w-16 h-16 bg-blue-200/60 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check text-blue-700">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                        <path d="m9 15 2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <p class="text-small text-blue-700 font-semibold">Verified + Rejected</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke p-gr-lg">
        <h2 class="text-h3 font-bold text-lgu-headline mb-gr-md">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-md">
            <!-- View Verification Queue -->
            <a href="{{ route('staff.verification-queue') }}" class="flex items-center gap-gr-sm p-gr-md rounded-xl border-2 border-gray-200 hover:border-lgu-button hover:bg-amber-50 transition-all group">
                <div class="w-16 h-16 bg-lgu-button rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-check text-white">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <path d="m9 14 2 2 4-4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-body font-bold text-lgu-headline mb-0.5 leading-tight">Verification Queue</h3>
                    <p class="text-small text-lgu-paragraph leading-tight">Review pending bookings</p>
                </div>
            </a>

            <!-- Calendar -->
            <a href="{{ route('staff.calendar') }}" class="flex items-center gap-gr-sm p-gr-md rounded-xl border-2 border-purple-200 hover:border-purple-400 hover:shadow-md transition-all duration-200 bg-white group">
                <div class="w-16 h-16 bg-purple-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm group-hover:shadow-md transition-shadow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days text-white">
                        <path d="M8 2v4"/>
                        <path d="M16 2v4"/>
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <path d="M3 10h18"/>
                        <path d="M8 14h.01"/>
                        <path d="M12 14h.01"/>
                        <path d="M16 14h.01"/>
                        <path d="M8 18h.01"/>
                        <path d="M12 18h.01"/>
                        <path d="M16 18h.01"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-body font-bold text-lgu-headline mb-0.5 leading-tight">Calendar View</h3>
                    <p class="text-small text-lgu-paragraph leading-tight">Check booking schedule</p>
                </div>
            </a>

            <!-- Reports -->
            <div class="flex items-center gap-gr-sm p-gr-md rounded-xl border-2 border-gray-200 bg-gray-50 opacity-60 cursor-not-allowed">
                <div class="w-16 h-16 bg-gray-400 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-bar-chart-3 text-white">
                        <path d="M3 3v18h18"/>
                        <path d="M18 17V9"/>
                        <path d="M13 17V5"/>
                        <path d="M8 17v-3"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-body font-bold text-gray-600 mb-0.5 leading-tight">Reports</h3>
                    <p class="text-small text-gray-500 leading-tight">Coming soon</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Required Alert -->
    @if($stats['pending_verification'] > 0)
    <div class="bg-amber-50 border-l-4 border-amber-500 p-gr-md rounded-xl shadow-sm">
        <div class="flex items-start">
            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mr-gr-sm flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-circle text-amber-600">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" x2="12" y1="8" y2="12"/>
                    <line x1="12" x2="12.01" y1="16" y2="16"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-body font-bold text-amber-900 mb-1">Action Required</p>
                <p class="text-small text-amber-800">
                    You have <strong class="font-bold">{{ $stats['pending_verification'] }}</strong> booking(s) waiting for verification. 
                    <a href="{{ route('staff.verification-queue') }}" class="underline font-semibold hover:text-amber-900">Review now</a>
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
