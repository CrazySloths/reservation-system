@extends('layouts.treasurer')

@section('title', 'Official Receipt Details')
@section('page-title', 'Official Receipt')
@section('page-subtitle', $paymentSlip->transaction_reference)

@push('styles')
<style>
@media print {
    body * {
        visibility: hidden;
    }
    #receipt-print-area, #receipt-print-area * {
        visibility: visible;
    }
    #receipt-print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        padding: 40px;
    }
}
#receipt-print-area {
    display: none;
}
@media print {
    #receipt-print-area {
        display: block;
    }
}
</style>
@endpush

@section('page-content')

<!-- PRINT-ONLY RECEIPT DESIGN -->
<div id="receipt-print-area">
    <div style="max-width: 600px; margin: 0 auto; font-family: 'Poppins', sans-serif; color: #1f2937;">
        
        <!-- Header with Success Icon -->
        <div style="text-align: center; margin-bottom: 30px;">
            <div style="width: 80px; height: 80px; background: #d1fae5; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h1 style="font-size: 28px; font-weight: bold; color: #1f2937; margin: 0 0 10px 0;">Payment Success!</h1>
            <p style="color: #6b7280; font-size: 14px; margin: 0;">Your payment has been successfully processed.</p>
        </div>

        <!-- Total Amount -->
        <div style="text-align: center; background: #f9fafb; padding: 20px; border-radius: 12px; margin-bottom: 30px;">
            <p style="color: #6b7280; font-size: 14px; margin: 0 0 8px 0;">Total Payment</p>
            <p style="font-size: 36px; font-weight: bold; color: #0f5b3a; margin: 0;">₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
        </div>

        <!-- Receipt Details Grid -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
            <!-- Ref Number -->
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                <p style="color: #6b7280; font-size: 12px; margin: 0 0 5px 0;">Ref Number</p>
                <p style="color: #1f2937; font-weight: 600; font-size: 14px; margin: 0;">{{ $paymentSlip->transaction_reference }}</p>
            </div>

            <!-- Payment Time -->
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                <p style="color: #6b7280; font-size: 12px; margin: 0 0 5px 0;">Payment Time</p>
                <p style="color: #1f2937; font-weight: 600; font-size: 14px; margin: 0;">{{ \Carbon\Carbon::parse($paymentSlip->paid_at)->format('d M Y, H:i') }}</p>
            </div>

            <!-- Payment Method -->
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                <p style="color: #6b7280; font-size: 12px; margin: 0 0 5px 0;">Payment Method</p>
                <p style="color: #1f2937; font-weight: 600; font-size: 14px; margin: 0;">{{ ucfirst(str_replace('_', ' ', $paymentSlip->payment_method)) }}</p>
            </div>

            <!-- Payor Name -->
            <div style="background: #f9fafb; padding: 15px; border-radius: 8px;">
                <p style="color: #6b7280; font-size: 12px; margin: 0 0 5px 0;">Payor Name</p>
                <p style="color: #1f2937; font-weight: 600; font-size: 14px; margin: 0;">{{ $paymentSlip->applicant_name }}</p>
            </div>
        </div>

        <!-- Facility Details -->
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
            <p style="color: #6b7280; font-size: 12px; margin: 0 0 10px 0;">Facility Details</p>
            <p style="color: #1f2937; font-weight: 600; font-size: 14px; margin: 0 0 5px 0;">{{ $paymentSlip->facility_name }}</p>
            <p style="color: #6b7280; font-size: 12px; margin: 0;">{{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('F d, Y') }} • {{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($paymentSlip->end_time)->format('h:i A') }}</p>
        </div>

        <!-- Divider -->
        <div style="border-top: 2px dashed #e5e7eb; margin: 30px 0;"></div>

        <!-- Footer -->
        <div style="text-align: center;">
            <p style="color: #9ca3af; font-size: 12px; margin: 0;">Official Receipt</p>
            <p style="color: #6b7280; font-size: 13px; font-weight: 600; margin: 5px 0;">City Treasurer's Office</p>
            <p style="color: #9ca3af; font-size: 11px; margin: 5px 0;">LGU Facility Reservation System</p>
            <p style="color: #9ca3af; font-size: 11px; margin: 10px 0 0 0;">Payment Slip: {{ $paymentSlip->slip_number }}</p>
        </div>

    </div>
</div>

<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('treasurer.official-receipts') }}" 
       class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-medium">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Official Receipts
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- OR Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="file-text" class="w-5 h-5 mr-2 text-lgu-button"></i>
                Official Receipt Details
            </h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">OR Number</label>
                    <p class="mt-1 text-lg font-bold text-green-600">{{ $paymentSlip->transaction_reference }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Slip #</label>
                    <p class="mt-1 text-base font-semibold text-lgu-button">{{ $paymentSlip->slip_number }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Date Issued</label>
                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($paymentSlip->paid_at)->format('F d, Y h:i A') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Payment Method</label>
                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $paymentSlip->payment_method)) }}</p>
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-500">Amount Paid</label>
                    <p class="mt-1 text-3xl font-bold text-lgu-headline">₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Payor Information -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="user" class="w-5 h-5 mr-2 text-lgu-button"></i>
                Payor Information
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

        <!-- Facility Details -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <i data-lucide="calendar" class="w-5 h-5 mr-2 text-lgu-button"></i>
                Booking Details
            </h3>
            
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Facility</label>
                    <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $paymentSlip->facility_name }}</p>
                    <p class="text-xs text-gray-500">{{ $paymentSlip->facility_address }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Event Date</label>
                    <p class="mt-1 text-sm text-gray-900">{{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('F d, Y') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Time</label>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($paymentSlip->start_time)->format('h:i A') }} - 
                        {{ \Carbon\Carbon::parse($paymentSlip->end_time)->format('h:i A') }}
                    </p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-500">Purpose</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $paymentSlip->purpose }}</p>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
            
            <div class="space-y-3">
                <a href="{{ route('treasurer.official-receipts.print', $paymentSlip->id) }}" 
                   class="w-full bg-lgu-button text-lgu-button-text font-bold py-3 rounded-lg hover:bg-lgu-highlight transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    Download PDF
                </a>
                
                <button onclick="window.print()" 
                        class="w-full bg-gray-100 text-gray-700 font-semibold py-3 rounded-lg hover:bg-gray-200 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    Print Receipt
                </button>
                
                <a href="{{ route('treasurer.payment-slips.show', $paymentSlip->id) }}" 
                   class="w-full border-2 border-lgu-button text-lgu-button font-semibold py-3 rounded-lg hover:bg-lgu-bg transition-all flex items-center justify-center gap-2">
                    <i data-lucide="file-check" class="w-5 h-5"></i>
                    View Payment Slip
                </a>
            </div>
        </div>

        <!-- Receipt Status -->
        <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-green-800">Receipt Issued</h4>
                    <p class="text-sm text-green-600">Payment Complete</p>
                </div>
            </div>
            
            @if($paymentSlip->verified_by_name)
            <div class="mt-4 pt-4 border-t border-green-200">
                <p class="text-xs text-green-700">Verified by:</p>
                <p class="text-sm font-semibold text-green-800">{{ $paymentSlip->verified_by_name }}</p>
            </div>
            @endif
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endpush

