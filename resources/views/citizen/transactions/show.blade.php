@extends('layouts.citizen')

@section('page-title', 'Transaction Details')
@section('page-subtitle', 'View your payment transaction details')

@section('page-content')
<div class="pb-gr-2xl">
    <!-- Back Button -->
    <div class="mb-gr-md">
        <a href="{{ route('citizen.transactions.index') }}" class="inline-flex items-center text-small font-medium text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-gr-xs"></i>
            Back to Transactions
        </a>
    </div>

    <!-- Page Header -->
    <div class="flex justify-between items-start mb-gr-lg">
        <div>
            <h2 class="text-h4 font-bold text-gray-900 mb-gr-xs">Payment Receipt</h2>
            <p class="text-small text-gray-600">Reference: <span class="font-mono font-semibold">{{ $transaction->slip_number ?? $transaction->or_number ?? 'N/A' }}</span></p>
        </div>
        <span class="inline-flex items-center px-gr-sm py-gr-xs rounded-full text-small font-medium
            @if($transaction->status == 'paid') bg-green-100 text-green-700
            @elseif($transaction->status == 'pending') bg-orange-100 text-orange-700
            @elseif($transaction->status == 'cancelled') bg-red-100 text-red-700
            @else bg-gray-100 text-gray-700
            @endif">
            <i data-lucide="{{ $transaction->status == 'paid' ? 'circle-check' : ($transaction->status == 'pending' ? 'clock' : 'x-circle') }}" class="w-4 h-4 mr-gr-xs"></i>
            {{ ucfirst($transaction->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-lg">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-gr-lg">
            <!-- Payment Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
                <h3 class="text-h5 font-bold text-gray-900 mb-gr-md flex items-center">
                    <i data-lucide="credit-card" class="w-5 h-5 mr-gr-xs text-lgu-green"></i>
                    Payment Information
                </h3>

                <div class="grid grid-cols-2 gap-gr-md">
                    <div>
                        <p class="text-caption text-gray-600 mb-gr-3xs">Amount Due</p>
                        <p class="text-h4 font-bold text-gray-900">₱{{ number_format($transaction->amount_due, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-caption text-gray-600 mb-gr-3xs">Payment Method</p>
                        <p class="text-body font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $transaction->payment_method ?? 'Cash')) }}</p>
                    </div>
                    <div>
                        <p class="text-caption text-gray-600 mb-gr-3xs">Transaction Date</p>
                        <p class="text-body font-medium text-gray-900">{{ \Carbon\Carbon::parse($transaction->created_at)->format('F d, Y h:i A') }}</p>
                    </div>
                    @if($transaction->paid_at)
                    <div>
                        <p class="text-caption text-gray-600 mb-gr-3xs">Paid At</p>
                        <p class="text-body font-medium text-gray-900">{{ \Carbon\Carbon::parse($transaction->paid_at)->format('F d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>

                @if($transaction->proof_of_payment)
                <div class="mt-gr-md pt-gr-md border-t border-gray-200">
                    <p class="text-caption text-gray-600 mb-gr-sm">Proof of Payment</p>
                    <a href="{{ asset('storage/' . $transaction->proof_of_payment) }}" target="_blank" class="inline-flex items-center text-lgu-green hover:text-lgu-green-dark font-medium">
                        <i data-lucide="image" class="w-4 h-4 mr-gr-xs"></i>
                        View Uploaded Proof
                    </a>
                </div>
                @endif
            </div>

            <!-- Booking Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
                <h3 class="text-h5 font-bold text-gray-900 mb-gr-md flex items-center">
                    <i data-lucide="calendar" class="w-5 h-5 mr-gr-xs text-lgu-green"></i>
                    Booking Information
                </h3>

                <div class="space-y-gr-sm">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-caption text-gray-600">Facility</p>
                            <p class="text-body font-semibold text-gray-900">{{ $booking->facility_name }}</p>
                            @if($booking->facility_address)
                            <p class="text-caption text-gray-500">{{ $booking->facility_address }}</p>
                            @endif
                        </div>
                        <a href="{{ route('citizen.reservations.show', $booking->id) }}" class="text-lgu-green hover:text-lgu-green-dark font-medium text-small">
                            View Booking
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-gr-md pt-gr-sm border-t border-gray-200">
                        <div>
                            <p class="text-caption text-gray-600 mb-gr-3xs">Event Name</p>
                            <p class="text-body font-medium text-gray-900">{{ $booking->event_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-gray-600 mb-gr-3xs">Event Type</p>
                            <p class="text-body font-medium text-gray-900">{{ ucfirst($booking->event_type ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-gray-600 mb-gr-3xs">Event Date</p>
                            <p class="text-body font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->start_time)->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-gray-600 mb-gr-3xs">Time</p>
                            <p class="text-body font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                            </p>
                        </div>
                        @if($booking->num_attendees)
                        <div>
                            <p class="text-caption text-gray-600 mb-gr-3xs">Attendees</p>
                            <p class="text-body font-medium text-gray-900">{{ number_format($booking->num_attendees) }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-caption text-gray-600 mb-gr-3xs">Booking Status</p>
                            <span class="inline-flex items-center px-gr-xs py-gr-3xs rounded-full text-caption font-medium
                                @if($booking->status == 'confirmed') bg-green-100 text-green-700
                                @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-700
                                @elseif($booking->status == 'rejected') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-700
                                @endif">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-gr-lg">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
                <h3 class="text-h6 font-bold text-gray-900 mb-gr-md">Actions</h3>
                <div class="space-y-gr-sm">
                    <button onclick="window.print()" class="w-full btn-secondary justify-center">
                        <i data-lucide="printer" class="w-4 h-4 mr-gr-xs"></i>
                        Print Receipt
                    </button>
                    <button onclick="downloadReceipt()" class="w-full btn-secondary justify-center">
                        <i data-lucide="download" class="w-4 h-4 mr-gr-xs"></i>
                        Download PDF
                    </button>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-lgu-green bg-opacity-10 rounded-xl border border-lgu-green p-gr-lg">
                <h3 class="text-h6 font-bold text-gray-900 mb-gr-md">Payment Summary</h3>
                <div class="space-y-gr-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-small text-gray-600">Facility Fee</span>
                        <span class="text-small font-semibold text-gray-900">₱{{ number_format($booking->total_cost ?? $transaction->amount_due, 2) }}</span>
                    </div>
                    <div class="pt-gr-sm border-t border-lgu-green">
                        <div class="flex justify-between items-center">
                            <span class="text-body font-bold text-gray-900">Total</span>
                            <span class="text-h5 font-bold text-lgu-green">₱{{ number_format($transaction->amount_due, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-gr-lg">
                <div class="flex items-start">
                    <i data-lucide="help-circle" class="w-5 h-5 text-blue-600 mr-gr-sm flex-shrink-0 mt-1"></i>
                    <div>
                        <h4 class="text-small font-bold text-blue-900 mb-gr-xs">Need Help?</h4>
                        <p class="text-caption text-blue-800 mb-gr-sm">Contact us if you have questions about this transaction.</p>
                        <a href="#" class="text-caption font-semibold text-blue-600 hover:text-blue-700">Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadReceipt() {
    Swal.fire({
        title: 'Download Receipt',
        text: 'PDF download feature coming soon!',
        icon: 'info',
        confirmButtonColor: '#047857'
    });
}

// Initialize Lucide icons
lucide.createIcons();
</script>
@endsection

