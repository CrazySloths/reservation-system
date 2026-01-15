@extends('layouts.citizen')

@section('page-title', 'Resolve Booking Conflict')
@section('page-subtitle', 'Choose to reschedule or request a refund')

@section('page-content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('citizen.conflicts.index') }}" class="p-2 hover:bg-lgu-bg rounded-lg transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5 text-lgu-headline"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-lgu-headline">Resolve Booking Conflict</h2>
            <p class="text-sm text-lgu-paragraph mt-1">Choose your preferred resolution</p>
        </div>
    </div>

    <!-- Conflict Details -->
    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">
        <div class="flex items-start gap-3 mb-4">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-orange-600 flex-shrink-0"></i>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-lgu-headline mb-1">{{ $conflict->cityEvent->event_title }}</h3>
                <p class="text-sm text-lgu-paragraph">{{ $conflict->cityEvent->event_description }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 p-4 bg-lgu-bg rounded-lg">
            <div>
                <p class="text-xs text-lgu-paragraph uppercase tracking-wide mb-1">Your Booking</p>
                <p class="text-sm font-semibold text-lgu-headline">{{ $conflict->facilityDetails->name }}</p>
                <p class="text-sm text-lgu-paragraph">{{ \Carbon\Carbon::parse($conflict->bookingDetails->start_time)->format('M d, Y') }}</p>
                <p class="text-xs text-lgu-paragraph">{{ \Carbon\Carbon::parse($conflict->bookingDetails->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($conflict->bookingDetails->end_time)->format('g:i A') }}</p>
            </div>
            <div>
                <p class="text-xs text-lgu-paragraph uppercase tracking-wide mb-1">City Event</p>
                <p class="text-sm font-semibold text-lgu-headline">{{ ucfirst($conflict->cityEvent->event_type) }}</p>
                <p class="text-sm text-lgu-paragraph">{{ $conflict->cityEvent->start_time->format('M d, Y') }}</p>
                <p class="text-xs text-lgu-paragraph">{{ $conflict->cityEvent->start_time->format('g:i A') }} - {{ $conflict->cityEvent->end_time->format('g:i A') }}</p>
            </div>
            <div>
                <p class="text-xs text-lgu-paragraph uppercase tracking-wide mb-1">Response Deadline</p>
                <p class="text-sm font-semibold text-orange-700">{{ $conflict->response_deadline->format('M d, Y') }}</p>
                <p class="text-xs text-lgu-paragraph">{{ $conflict->response_deadline->format('g:i A') }}</p>
                <p class="text-xs text-orange-600 mt-1">{{ $conflict->response_deadline->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <!-- Resolution Options -->
    <form action="{{ route('citizen.conflicts.resolve', $conflict) }}" method="POST" id="resolutionForm">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Option 1: Reschedule -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-lgu-headline">Reschedule</h3>
                            <p class="text-sm text-lgu-paragraph">Pick a new date (no extra charge)</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-lgu-headline mb-2">New Start Date & Time</label>
                            <input 
                                type="datetime-local" 
                                name="new_start_time" 
                                id="new_start_time"
                                class="w-full px-4 py-2 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-lgu-headline mb-2">New End Date & Time</label>
                            <input 
                                type="datetime-local" 
                                name="new_end_time" 
                                id="new_end_time"
                                class="w-full px-4 py-2 border-2 border-lgu-stroke rounded-lg focus:border-lgu-highlight focus:outline-none"
                            />
                        </div>
                    </div>

                    <button type="button" onclick="selectOption('reschedule')" class="w-full mt-6 btn-primary flex items-center justify-center gap-2">
                        <i data-lucide="calendar-check" class="w-5 h-5"></i>
                        <span>Choose Reschedule</span>
                    </button>
                </div>
            </div>

            <!-- Option 2: Refund -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                            <i data-lucide="circle-dollar-sign" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-lgu-headline">Request Refund</h3>
                            <p class="text-sm text-lgu-paragraph">Full refund within 3-7 business days</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-2 text-sm text-lgu-paragraph">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                            <span>100% refund guaranteed</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-lgu-paragraph">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                            <span>Processed within 3-7 business days</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-lgu-paragraph">
                            <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                            <span>No cancellation fees</span>
                        </div>
                    </div>

                    <button type="button" onclick="selectOption('refund')" class="w-full mt-6 btn-secondary flex items-center justify-center gap-2">
                        <i data-lucide="coins" class="w-5 h-5"></i>
                        <span>Request Refund</span>
                    </button>
                </div>
            </div>
        </div>

        <input type="hidden" name="choice" id="choice" value="">
        <input type="hidden" name="refund_method" id="refund_method" value="">
        <input type="hidden" name="refund_account_name" id="refund_account_name" value="">
        <input type="hidden" name="refund_account_number" id="refund_account_number" value="">
        <input type="hidden" name="refund_bank_name" id="refund_bank_name" value="">
    </form>

    <!-- Important Notice -->
    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <i data-lucide="info" class="w-5 h-5 text-yellow-700 flex-shrink-0 mt-0.5"></i>
            <div class="text-sm text-yellow-800">
                <p class="font-semibold mb-1">Important Notice</p>
                <p>If you don't respond by <strong>{{ $conflict->response_deadline->format('M d, Y g:i A') }}</strong>, your booking will be automatically refunded.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

function selectOption(choice) {
    if (choice === 'reschedule') {
        const newStartTime = document.getElementById('new_start_time').value;
        const newEndTime = document.getElementById('new_end_time').value;

        if (!newStartTime || !newEndTime) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please select both start and end times for your new booking.',
                confirmButtonColor: '#faae2b'
            });
            return;
        }
    }

    Swal.fire({
        title: choice === 'reschedule' ? 'Confirm Reschedule?' : 'Confirm Refund Request?',
        text: choice === 'reschedule' 
            ? 'Your booking will be rescheduled to the new date and time.' 
            : 'You will receive a full refund within 3-7 business days.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#faae2b',
        cancelButtonColor: '#fa5246',
        confirmButtonText: 'Yes, proceed',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            if (choice === 'refund') {
                // Show refund method selection modal
                showRefundMethodModal();
            } else {
                document.getElementById('choice').value = choice;
                document.getElementById('resolutionForm').submit();
            }
        }
    });
}

function showRefundMethodModal() {
    Swal.fire({
        title: 'Select Refund Method',
        html: `
            <div class="text-left space-y-4">
                <p class="text-sm text-gray-600 mb-4">How would you like to receive your refund?</p>
                
                <div class="space-y-3">
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-lgu-highlight transition-colors">
                        <input type="radio" name="refund_method" value="cash" class="mt-0 mr-3" onchange="toggleRefundFields('cash')">
                        <i data-lucide="banknote" class="w-5 h-5 text-lgu-headline mr-3"></i>
                        <div class="flex-1">
                            <div class="font-semibold text-lgu-headline">Cash</div>
                            <div class="text-sm text-gray-600">Pick up at City Treasurer's Office</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-lgu-highlight transition-colors">
                        <input type="radio" name="refund_method" value="gcash" class="mt-0 mr-3" onchange="toggleRefundFields('gcash')">
                        <i data-lucide="smartphone" class="w-5 h-5 text-lgu-headline mr-3"></i>
                        <div class="flex-1">
                            <div class="font-semibold text-lgu-headline">GCash</div>
                            <div class="text-sm text-gray-600">Direct transfer to GCash</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-lgu-highlight transition-colors">
                        <input type="radio" name="refund_method" value="paymaya" class="mt-0 mr-3" onchange="toggleRefundFields('paymaya')">
                        <i data-lucide="smartphone" class="w-5 h-5 text-lgu-headline mr-3"></i>
                        <div class="flex-1">
                            <div class="font-semibold text-lgu-headline">PayMaya</div>
                            <div class="text-sm text-gray-600">Direct transfer to PayMaya</div>
                        </div>
                    </label>
                    
                    <label class="flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-lgu-highlight transition-colors">
                        <input type="radio" name="refund_method" value="bank_transfer" class="mt-0 mr-3" onchange="toggleRefundFields('bank_transfer')">
                        <i data-lucide="building-2" class="w-5 h-5 text-lgu-headline mr-3"></i>
                        <div class="flex-1">
                            <div class="font-semibold text-lgu-headline">Bank Transfer</div>
                            <div class="text-sm text-gray-600">Direct bank deposit</div>
                        </div>
                    </label>
                </div>
                
                <div id="refund_details_fields" class="hidden mt-4 p-4 bg-gray-50 rounded-lg space-y-3">
                    <div id="account_name_field">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                        <input type="text" id="refund_account_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-transparent" placeholder="Enter account name">
                    </div>
                    
                    <div id="account_number_field">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <span id="account_number_label">Account Number</span>
                        </label>
                        <input type="text" id="refund_account_number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-transparent" placeholder="Enter account number">
                    </div>
                    
                    <div id="bank_name_field" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" id="refund_bank_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-transparent" placeholder="e.g., BDO, BPI, Metrobank">
                    </div>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonColor: '#faae2b',
        cancelButtonColor: '#fa5246',
        confirmButtonText: 'Submit Refund Request',
        cancelButtonText: 'Cancel',
        width: '600px',
        didOpen: () => {
            // Initialize Lucide icons in the modal
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        },
        preConfirm: () => {
            const method = document.querySelector('input[name="refund_method"]:checked');
            if (!method) {
                Swal.showValidationMessage('Please select a refund method');
                return false;
            }
            
            const methodValue = method.value;
            
            if (methodValue !== 'cash') {
                const accountName = document.getElementById('refund_account_name').value.trim();
                const accountNumber = document.getElementById('refund_account_number').value.trim();
                
                if (!accountName || !accountNumber) {
                    Swal.showValidationMessage('Please fill in all required fields');
                    return false;
                }
                
                if (methodValue === 'bank_transfer') {
                    const bankName = document.getElementById('refund_bank_name').value.trim();
                    if (!bankName) {
                        Swal.showValidationMessage('Please enter the bank name');
                        return false;
                    }
                    return { method: methodValue, accountName, accountNumber, bankName };
                }
                
                return { method: methodValue, accountName, accountNumber };
            }
            
            return { method: methodValue };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const refundData = result.value;
            
            // Set form values
            console.log('Setting form values...', refundData);
            document.getElementById('choice').value = 'refund';
            document.getElementById('refund_method').value = refundData.method;
            
            if (refundData.accountName) {
                document.getElementById('refund_account_name').value = refundData.accountName;
            } else {
                document.getElementById('refund_account_name').value = '';
            }
            if (refundData.accountNumber) {
                document.getElementById('refund_account_number').value = refundData.accountNumber;
            } else {
                document.getElementById('refund_account_number').value = '';
            }
            if (refundData.bankName) {
                document.getElementById('refund_bank_name').value = refundData.bankName;
            } else {
                document.getElementById('refund_bank_name').value = '';
            }
            
            // Debug: Log form data
            const formData = new FormData(document.getElementById('resolutionForm'));
            console.log('Form data before submit:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Submit form
            console.log('Submitting form...');
            document.getElementById('resolutionForm').submit();
        }
    });
}

function toggleRefundFields(method) {
    const detailsDiv = document.getElementById('refund_details_fields');
    const bankNameField = document.getElementById('bank_name_field');
    const accountNumberLabel = document.getElementById('account_number_label');
    
    if (method === 'cash') {
        detailsDiv.classList.add('hidden');
    } else {
        detailsDiv.classList.remove('hidden');
        
        if (method === 'gcash') {
            accountNumberLabel.textContent = 'GCash Mobile Number';
            document.getElementById('refund_account_number').placeholder = '09XXXXXXXXX';
            bankNameField.classList.add('hidden');
        } else if (method === 'paymaya') {
            accountNumberLabel.textContent = 'PayMaya Mobile Number';
            document.getElementById('refund_account_number').placeholder = '09XXXXXXXXX';
            bankNameField.classList.add('hidden');
        } else if (method === 'bank_transfer') {
            accountNumberLabel.textContent = 'Account Number';
            document.getElementById('refund_account_number').placeholder = 'Enter account number';
            bankNameField.classList.remove('hidden');
        }
    }
}
</script>
@endpush
@endsection

