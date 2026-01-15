@extends('layouts.treasurer')

@section('title', 'Verify Payment')
@section('page-title', 'Payment Verification')
@section('page-subtitle', $paymentSlip->slip_number)

@section('page-content')

<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('treasurer.payment-verification') }}" 
       class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-medium">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Payment Queue
    </a>
</div>

<!-- Payment Status Alert -->
<div class="mb-6">
    @php
        $deadline = \Carbon\Carbon::parse($paymentSlip->payment_deadline);
        $isOverdue = $paymentSlip->status === 'unpaid' && $deadline->isPast();
        $isUrgent = $paymentSlip->status === 'unpaid' && $deadline->diffInHours(now(), false) <= 24 && !$isOverdue;
    @endphp
    
    @if($paymentSlip->status === 'paid')
        <div class="bg-green-50 border-l-8 border-green-500 p-6 rounded-xl shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                    <i data-lucide="check-circle" class="w-8 h-8 text-green-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-green-800 font-bold text-lg">Payment Verified</h3>
                    <p class="text-green-700 mt-1">This payment was verified on {{ \Carbon\Carbon::parse($paymentSlip->paid_at)->format('F d, Y \a\t h:i A') }}</p>
                    @if($paymentSlip->transaction_reference)
                        <p class="text-green-700 mt-1">Official Receipt: <span class="font-bold text-lg">{{ $paymentSlip->transaction_reference }}</span></p>
                    @endif
                    @if($paymentSlip->payment_method)
                        <p class="text-green-700 mt-1">Payment Method: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $paymentSlip->payment_method)) }}</span></p>
                    @endif
                </div>
            </div>
        </div>
    @elseif($paymentSlip->status === 'expired')
        <div class="bg-red-50 border-l-8 border-red-500 p-6 rounded-xl shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                    <i data-lucide="x-circle" class="w-8 h-8 text-red-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-red-800 font-bold text-lg">Payment Expired</h3>
                    <p class="text-red-700 mt-1">This payment slip has expired. The citizen must rebook the facility.</p>
                </div>
            </div>
        </div>
    @elseif($isOverdue)
        <div class="bg-red-50 border-l-8 border-red-500 p-6 rounded-xl shadow-lg animate-pulse">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                    <i data-lucide="alert-circle" class="w-8 h-8 text-red-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-red-800 font-bold text-lg">OVERDUE PAYMENT</h3>
                    <p class="text-red-700 mt-1">Deadline was {{ $deadline->diffForHumans() }}. Payment should have been received by {{ $deadline->format('F d, Y \a\t h:i A') }}</p>
                </div>
            </div>
        </div>
    @elseif($isUrgent)
        <div class="bg-orange-50 border-l-8 border-orange-500 p-6 rounded-xl shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                    <i data-lucide="clock" class="w-8 h-8 text-orange-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-orange-800 font-bold text-lg">Urgent - Payment Due Soon</h3>
                    <p class="text-orange-700 mt-1">Deadline: {{ $deadline->format('F d, Y \a\t h:i A') }} ({{ $deadline->diffForHumans() }})</p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-blue-50 border-l-8 border-blue-500 p-6 rounded-xl shadow-lg">
            <div class="flex items-start">
                <div class="flex-shrink-0 mt-1">
                    <i data-lucide="info" class="w-8 h-8 text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-blue-800 font-bold text-lg">Awaiting Payment</h3>
                    <p class="text-blue-700 mt-1">Payment deadline: {{ $deadline->format('F d, Y \a\t h:i A') }} ({{ $deadline->diffForHumans() }})</p>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content - Payment Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Payment Slip Information -->
        <div class="bg-white rounded-xl shadow-lg p-6" id="payment-slip-details">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center">
                    <i data-lucide="file-text" class="w-5 h-5 mr-2 text-lgu-button"></i>
                    Payment Slip Details
                </h3>
                <button onclick="printSlipDetails()" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                    Print Slip
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Slip Number</label>
                    <p class="mt-1 text-lg font-semibold text-lgu-button">{{ $paymentSlip->slip_number }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Amount Due</label>
                    <p class="mt-1 text-2xl font-bold text-gray-900">₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Status</label>
                    <p class="mt-1">
                        @if($paymentSlip->status === 'paid')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                                Verified
                            </span>
                        @elseif($paymentSlip->status === 'expired')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                                Expired
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                                Pending
                            </span>
                        @endif
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Deadline</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $deadline->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Citizen Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="user" class="w-5 h-5 mr-2 text-lgu-button"></i>
                Citizen Information
            </h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Full Name</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $paymentSlip->applicant_name }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Email Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $paymentSlip->applicant_email }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Phone Number</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $paymentSlip->applicant_phone }}</p>
                </div>
            </div>
        </div>

        <!-- Booking Details -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="calendar" class="w-5 h-5 mr-2 text-lgu-button"></i>
                Booking Details
            </h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Facility</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $paymentSlip->facility_name }}</p>
                    <p class="text-xs text-gray-600">{{ $paymentSlip->facility_address }}</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Date & Time</label>
                        <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('M d, Y') }}</p>
                        <p class="text-xs text-gray-600">
                            {{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('h:i A') }} - 
                            {{ \Carbon\Carbon::parse($paymentSlip->end_time)->format('h:i A') }}
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Attendees</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $paymentSlip->attendees }} people</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Purpose</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $paymentSlip->purpose }}</p>
                </div>
            </div>
        </div>

        <!-- Cashless Payment Details (If submitted) -->
        @if($paymentSlip->status === 'unpaid' && $paymentSlip->transaction_reference)
            <div class="bg-blue-50 border-2 border-blue-300 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center">
                    <i data-lucide="smartphone" class="w-5 h-5 mr-2"></i>
                    Cashless Payment Submitted
                </h3>
                
                @if($paymentSlip->is_test_transaction)
                <div class="bg-yellow-100 border-2 border-yellow-400 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-700 mr-2"></i>
                        <strong class="text-yellow-900">TEST MODE TRANSACTION</strong>
                    </div>
                </div>
                @endif
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-700">Payment Channel</label>
                        <p class="mt-1 text-lg font-bold text-blue-900">{{ strtoupper($paymentSlip->payment_channel ?? $paymentSlip->payment_method) }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-700">Reference Number</label>
                        <p class="mt-1 text-lg font-mono font-bold text-blue-900">{{ $paymentSlip->transaction_reference }}</p>
                    </div>
                    
                    @if($paymentSlip->account_name)
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-blue-700">Account Name</label>
                        <p class="mt-1 text-sm text-blue-900">{{ $paymentSlip->account_name }}</p>
                    </div>
                    @endif
                    
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-blue-700">Submitted At</label>
                        <p class="mt-1 text-sm text-blue-900">{{ \Carbon\Carbon::parse($paymentSlip->sent_to_treasurer_at)->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                
                <div class="mt-4 p-4 bg-white rounded-lg border border-blue-200">
                    <p class="text-sm text-gray-700">
                        <i data-lucide="info" class="w-4 h-4 inline mr-1 text-blue-600"></i>
                        <strong>Next Step:</strong> Verify this transaction in your {{ strtoupper($paymentSlip->payment_channel ?? 'payment') }} app, then click "Verify & Confirm Payment" below.
                    </p>
                </div>
            </div>
        @endif

        <!-- Payment Verification Form (Only if unpaid) -->
        @if($paymentSlip->status === 'unpaid')
            <div class="bg-white rounded-xl shadow-lg p-6 border-2 border-lgu-button">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="check-square" class="w-5 h-5 mr-2 text-lgu-button"></i>
                    Verify Payment
                </h3>
                
                <form id="verifyPaymentForm" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent">
                            <option value="">Select payment method</option>
                            <option value="cash" {{ ($paymentSlip->payment_method === 'cash') ? 'selected' : '' }}>Cash</option>
                            <option value="gcash" {{ ($paymentSlip->payment_channel === 'gcash' || $paymentSlip->payment_method === 'gcash') ? 'selected' : '' }}>GCash</option>
                            <option value="paymaya" {{ ($paymentSlip->payment_channel === 'maya' || $paymentSlip->payment_method === 'paymaya') ? 'selected' : '' }}>Maya (PayMaya)</option>
                            <option value="bank_transfer" {{ (in_array($paymentSlip->payment_channel, ['bpi', 'bdo', 'metrobank', 'unionbank', 'landbank']) || $paymentSlip->payment_method === 'bank_transfer') ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes (Optional)
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3"
                                  placeholder="Add any additional notes or observations..."
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent"></textarea>
                    </div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i data-lucide="info" class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5"></i>
                            <p class="text-sm text-blue-800">
                                Upon verification, an <strong>Official Receipt (OR) number</strong> will be automatically generated and issued to the citizen.
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition inline-flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i>
                            Verify & Confirm Payment
                        </button>
                        <a href="{{ route('treasurer.payment-verification') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        @endif
    </div>

    <!-- Sidebar - Quick Actions -->
    <div class="space-y-6">
        <!-- Important Dates -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="calendar-check" class="w-5 h-5 mr-2 text-lgu-button"></i>
                Important Dates
            </h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500">Payment Deadline</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ $deadline->format('M d, Y') }}</p>
                    <p class="text-xs text-gray-600">{{ $deadline->diffForHumans() }}</p>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-500">Event Date</label>
                    <p class="mt-1 text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('M d, Y') }}</p>
                </div>
                
                @if($paymentSlip->status === 'paid')
                    <div>
                        <label class="block text-xs font-medium text-gray-500">Payment Verified</label>
                        <p class="mt-1 text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($paymentSlip->paid_at)->format('M d, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
            
            <div class="space-y-2">
                <button onclick="window.print()" class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition inline-flex items-center justify-center">
                    <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
                    Print Slip
                </button>
                
                @if($paymentSlip->status === 'paid')
                    <a href="#" class="w-full px-4 py-2 bg-lgu-button hover:bg-lgu-highlight text-white font-medium rounded-lg transition inline-flex items-center justify-center">
                        <i data-lucide="file-text" class="w-4 h-4 mr-2"></i>
                        View Official Receipt
                    </a>
                @endif
                
                <a href="{{ route('treasurer.payment-verification') }}" class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition inline-flex items-center justify-center">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<style>
@media print {
    /* Hide everything by default */
    body * {
        visibility: hidden;
    }
    
    /* Show only the payment slip details */
    #payment-slip-details,
    #payment-slip-details * {
        visibility: visible;
    }
    
    /* Position the payment slip details */
    #payment-slip-details {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 40px;
    }
    
    /* Hide the print button in print view */
    #payment-slip-details button {
        display: none !important;
    }
    
    /* Center the heading */
    #payment-slip-details h3 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 30px;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('verifyPaymentForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const paymentMethod = formData.get('payment_method');
            
            if (!paymentMethod) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Information',
                    text: 'Please select a payment method.',
                    confirmButtonColor: '#0f5b3a'
                });
                return;
            }
            
            Swal.fire({
                title: 'Confirm Payment Verification',
                html: `
                    <p class="mb-2">Are you sure you want to verify this payment?</p>
                    <div class="bg-gray-100 p-4 rounded-lg mt-4">
                        <p class="text-sm"><strong>Slip #:</strong> {{ $paymentSlip->slip_number }}</p>
                        <p class="text-sm"><strong>Amount:</strong> ₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
                        <p class="text-sm"><strong>Method:</strong> <span id="confirmMethod"></span></p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, Verify Payment',
                cancelButtonText: 'Cancel',
                didOpen: () => {
                    document.getElementById('confirmMethod').textContent = 
                        paymentMethod.replace('_', ' ').toUpperCase();
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Verifying payment',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit via AJAX
                    fetch('{{ route("treasurer.payment-slips.verify", $paymentSlip->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Payment Verified!',
                                text: data.message,
                                confirmButtonColor: '#0f5b3a'
                            }).then(() => {
                                if (data.redirect) {
                                    window.location.href = data.redirect;
                                } else {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Verification Failed',
                                text: data.message,
                                confirmButtonColor: '#0f5b3a'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while verifying the payment.',
                            confirmButtonColor: '#0f5b3a'
                        });
                    });
                }
            });
        });
    }
    
    // Simplified print function - just use window.print(), CSS handles the rest
    function printSlipDetails() {
        window.print();
    }
    
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush

@endsection

