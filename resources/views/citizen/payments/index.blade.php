@extends('layouts.citizen')

@section('title', 'Payment Slips')
@section('page-title', 'Payment Slips')
@section('page-subtitle', 'Manage your facility booking payments')

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
                   placeholder="Search by slip number, facility name, or purpose..."
                   class="w-full pl-12 pr-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent transition-all"
                   oninput="liveSearch(this.value)">
        </div>
    </div>

    <!-- Status Filters -->
    <div class="bg-white shadow-lg rounded-xl p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-lgu-button">
                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
            </svg>
            Filter by Status
        </h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('citizen.payment-slips', ['status' => 'all']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $status === 'all' ? 'bg-lgu-button text-lgu-button-text shadow-lg scale-105' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                </svg>
                All <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $status === 'all' ? 'bg-white/20' : 'bg-gray-200' }}">{{ $statusCounts['all'] }}</span>
            </a>
            <a href="{{ route('citizen.payment-slips', ['status' => 'unpaid']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $status === 'unpaid' ? 'bg-orange-500 text-white shadow-lg scale-105' : 'bg-orange-100 text-orange-700 hover:bg-orange-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Unpaid <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $status === 'unpaid' ? 'bg-white/20' : 'bg-orange-200' }}">{{ $statusCounts['unpaid'] }}</span>
            </a>
            <a href="{{ route('citizen.payment-slips', ['status' => 'paid']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $status === 'paid' ? 'bg-green-500 text-white shadow-lg scale-105' : 'bg-green-100 text-green-700 hover:bg-green-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                </svg>
                Paid <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $status === 'paid' ? 'bg-white/20' : 'bg-green-200' }}">{{ $statusCounts['paid'] }}</span>
            </a>
            <a href="{{ route('citizen.payment-slips', ['status' => 'expired']) }}" 
               class="group px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 cursor-pointer flex items-center gap-2 {{ $status === 'expired' ? 'bg-red-500 text-white shadow-lg scale-105' : 'bg-red-100 text-red-700 hover:bg-red-200 hover:scale-105' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>
                </svg>
                Expired <span class="ml-1 px-2 py-0.5 rounded-full text-xs {{ $status === 'expired' ? 'bg-white/20' : 'bg-red-200' }}">{{ $statusCounts['expired'] }}</span>
            </a>
        </div>
    </div>

    <!-- Payment Slips List -->
    @if($paymentSlips->isEmpty())
        <div class="bg-gray-50 shadow-lg rounded-xl p-16 text-center">
            <div class="mx-auto w-24 h-24 bg-white shadow-lg rounded-full flex items-center justify-center mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                    <path d="M12 11h4"/><path d="M12 16h4"/><path d="M8 11h.01"/><path d="M8 16h.01"/>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-900 mb-3">No Payment Slips Found</h3>
            <p class="text-gray-600 mb-8 text-lg">You don't have any payment slips{{ $status !== 'all' ? ' with this status' : '' }} yet.</p>
            <a href="{{ route('citizen.browse-facilities') }}" 
               class="inline-flex items-center px-8 py-4 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M5 12h14"/><path d="M12 5v14"/>
                </svg>
                Browse Facilities
            </a>
        </div>
    @else
        <div class="space-y-5">
            @foreach($paymentSlips as $slip)
                @php
                    // Use match for fixed Tailwind classes
                    $statusBadge = match($slip->status) {
                        'unpaid' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-300', 'label' => 'Awaiting Payment'],
                        'paid' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300', 'label' => 'Paid'],
                        'expired' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300', 'label' => 'Expired'],
                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'label' => ucfirst($slip->status)]
                    };
                    $dueDate = \Carbon\Carbon::parse($slip->payment_deadline);
                    $isOverdue = $slip->status === 'unpaid' && $dueDate->isPast();
                    $daysUntilDue = $isOverdue ? abs($dueDate->diffInDays(now(), false)) : $dueDate->diffInDays(now(), false);
                @endphp
                
                <div class="bg-white shadow-lg rounded-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-gray-100 hover:border-lgu-button/30 transform hover:-translate-y-1 {{ $isOverdue ? 'border-l-8 border-l-red-500' : '' }}">
                    <div class="p-6">
                        <!-- Header Section -->
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-5 gap-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <h3 class="text-xl md:text-2xl font-bold text-gray-900">{{ $slip->facility_name }}</h3>
                                    @if($isOverdue)
                                        <span class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded-full shadow-lg inline-flex items-center gap-1">
                                            <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                                            OVERDUE
                                        </span>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold text-gray-900">Slip #:</span> 
                                        <span class="font-mono bg-gray-100 px-2 py-0.5 rounded">{{ $slip->slip_number }}</span>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <span class="font-semibold text-gray-900">Purpose:</span> {{ $slip->purpose }}
                                    </p>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <span class="px-5 py-2.5 rounded-lg text-sm font-bold {{ $statusBadge['bg'] }} {{ $statusBadge['text'] }} border-2 {{ $statusBadge['border'] }} whitespace-nowrap shadow-sm">
                                {{ $statusBadge['label'] }}
                            </span>
                        </div>

                        <!-- Payment Info Grid with Countdown -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5 p-5 bg-gray-50 rounded-xl">
                            <!-- Amount Due -->
                            <div class="text-center border-r border-gray-200 last:border-r-0">
                                <p class="text-xs text-gray-600 mb-1 font-semibold uppercase tracking-wide">Amount Due</p>
                                <div class="flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lgu-button">
                                        <line x1="12" x2="12" y1="2" y2="22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                    <p class="text-2xl md:text-3xl font-bold text-lgu-headline">₱{{ number_format($slip->amount_due, 2) }}</p>
                                </div>
                            </div>
                            
                            <!-- Due Date with Countdown -->
                            <div class="text-center border-r border-gray-200 last:border-r-0">
                                <p class="text-xs text-gray-600 mb-1 font-semibold uppercase tracking-wide">Due Date</p>
                                <p class="text-base font-bold text-gray-900">{{ $dueDate->format('M d, Y') }}</p>
                                @if($slip->status === 'unpaid')
                                    @if($isOverdue)
                                        <div class="mt-1 px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><path d="M12 8v4"/><path d="M12 16h.01"/>
                                            </svg>
                                            {{ $daysUntilDue }} {{ $daysUntilDue == 1 ? 'day' : 'days' }} overdue
                                        </div>
                                    @else
                                        <div class="mt-1 px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full inline-flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            {{ $daysUntilDue }} {{ $daysUntilDue == 1 ? 'day' : 'days' }} left
                                        </div>
                                    @endif
                                @endif
                            </div>
                            
                            <!-- Booking Date -->
                            <div class="text-center border-r border-gray-200 last:border-r-0">
                                <p class="text-xs text-gray-600 mb-1 font-semibold uppercase tracking-wide">Booking Date</p>
                                <p class="text-base font-bold text-gray-900">{{ \Carbon\Carbon::parse($slip->start_time)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ \Carbon\Carbon::parse($slip->start_time)->format('g:i A') }}</p>
                            </div>
                            
                            <!-- Payment Method -->
                            <div class="text-center">
                                <p class="text-xs text-gray-600 mb-1 font-semibold uppercase tracking-wide">Payment Method</p>
                                <p class="text-base font-bold text-gray-900">{{ $slip->payment_method ? ucfirst(str_replace('_', ' ', $slip->payment_method)) : 'Not set' }}</p>
                                @if($slip->transaction_reference)
                                    <p class="text-xs text-gray-600 mt-1 font-mono">{{ $slip->transaction_reference }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('citizen.payment-slips.show', $slip->id) }}" 
                               class="flex-1 min-w-[200px] px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition-all duration-200 text-center shadow-md hover:shadow-lg cursor-pointer flex items-center justify-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                                View Details & Pay
                            </a>

                            <a href="{{ route('citizen.reservations.show', $slip->booking_id) }}" 
                               class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-all duration-200 shadow-md hover:shadow-lg cursor-pointer flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/>
                                </svg>
                                View Booking
                            </a>

                            @if($slip->status === 'paid' && isset($slip->or_number) && $slip->or_number)
                                <div class="px-5 py-3 bg-green-50 border-2 border-green-200 text-green-700 font-semibold rounded-lg shadow-sm flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                                    </svg>
                                    OR #<span class="font-mono">{{ $slip->or_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="bg-white shadow-lg rounded-xl p-6">
            {{ $paymentSlips->links() }}
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
        const currentStatus = '{{ $status }}';
        window.location.href = `{{ route('citizen.payment-slips') }}?status=${currentStatus}&search=${encodeURIComponent(query)}`;
    }, 500);
}

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

