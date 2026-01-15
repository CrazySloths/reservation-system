@extends('layouts.citizen')

@section('page-title', 'Cashless Payment')

@section('page-content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('citizen.payment-slips.show', $paymentSlip->id) }}" class="text-gray-500 hover:text-gray-700">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Cashless Payment</h1>
        </div>
        <p class="text-gray-600">Choose your preferred payment method and complete the payment</p>
    </div>

    <!-- Payment Slip Summary -->
    <div class="bg-lgu-button rounded-xl p-6 mb-8 text-lgu-button-text shadow-lg">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm opacity-90 mb-1">Payment Slip Number</p>
                <p class="text-2xl font-bold">{{ $paymentSlip->slip_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm opacity-90 mb-1">Amount to Pay</p>
                <p class="text-3xl font-bold">₱{{ number_format($paymentSlip->amount_due, 2) }}</p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-white/20">
            <p class="text-sm opacity-90">{{ $paymentSlip->applicant_name }}</p>
            <p class="text-sm opacity-90">{{ $booking->facility_name }} - {{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</p>
        </div>
    </div>

    @if(config('payment.test_mode'))
    <!-- Test Mode Banner -->
    <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-3">
            <i data-lucide="alert-triangle" class="w-6 h-6 text-yellow-600"></i>
            <div>
                <p class="font-bold text-yellow-900">TEST MODE ENABLED</p>
                <p class="text-sm text-yellow-700">
                    For testing: Use reference numbers starting with <code class="bg-yellow-200 px-2 py-1 rounded font-mono">TEST-</code> (e.g., TEST-123456789)
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Channel Selection -->
    <div class="bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Select Payment Method</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            @foreach(config('payment.channels') as $key => $channel)
                @if($channel['enabled'])
                <button type="button" 
                        onclick="selectChannel('{{ $key }}')"
                        id="channel-{{ $key }}"
                        class="channel-btn border-2 border-gray-300 rounded-xl p-6 hover:border-lgu-button hover:bg-blue-50 transition-all text-left">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center">
                            <i data-lucide="{{ $channel['icon'] }}" class="w-6 h-6 text-gray-600"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900">{{ $channel['name'] }}</h3>
                            <p class="text-sm text-gray-600">{{ $channel['account_number'] }}</p>
                        </div>
                        <div class="channel-check hidden">
                            <i data-lucide="check-circle" class="w-6 h-6 text-green-500"></i>
                        </div>
                    </div>
                </button>
                @endif
            @endforeach
        </div>

        <!-- Payment Instructions (shown after channel selection) -->
        <div id="payment-instructions" class="hidden">
            <div class="bg-blue-50 border-2 border-blue-300 rounded-xl p-6 mb-6">
                <h3 class="font-bold text-xl text-blue-900 mb-4">Payment Instructions</h3>
                
                <!-- Dynamic content based on selected channel -->
                <div id="channel-details">
                    <!-- Filled by JavaScript -->
                </div>

                <div class="mt-6 p-4 bg-white rounded-lg border-2 border-blue-200">
                    <h4 class="font-bold text-gray-900 mb-3">Steps to Complete Payment:</h4>
                    <ol class="list-decimal list-inside space-y-2 text-sm text-gray-700">
                        <li>Open your <span id="app-name" class="font-semibold"></span> app</li>
                        <li>Send <strong class="text-green-600">₱{{ number_format($paymentSlip->amount_due, 2) }}</strong> to the account number above</li>
                        <li>Copy the <strong>Reference Number</strong> from your transaction receipt</li>
                        <li>Enter the reference number below and submit</li>
                        <li>Our Treasurer will verify your payment within 24 hours</li>
                    </ol>
                </div>
            </div>

            <!-- Reference Number Form -->
            <form action="{{ route('citizen.payment-slips.submit-cashless', $paymentSlip->id) }}" method="POST" id="payment-form">
                @csrf
                <input type="hidden" name="payment_channel" id="payment_channel" value="">
                
                <div class="mb-6">
                    <label for="reference_number" class="block text-sm font-bold text-gray-900 mb-2">
                        Transaction Reference Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="reference_number" 
                           id="reference_number" 
                           required
                           maxlength="20"
                           placeholder="Enter your reference number"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-lgu-button focus:ring-2 focus:ring-lgu-button/20 transition-all font-mono text-lg">
                    <p class="text-sm text-gray-600 mt-2">
                        <i data-lucide="info" class="w-4 h-4 inline"></i>
                        This is the unique number provided in your transaction receipt
                    </p>
                </div>

                <div class="mb-6">
                    <label for="account_name" class="block text-sm font-bold text-gray-900 mb-2">
                        Account Name (Optional)
                    </label>
                    <input type="text" 
                           name="account_name" 
                           id="account_name" 
                           maxlength="100"
                           placeholder="Name on your account"
                           class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-lgu-button focus:ring-2 focus:ring-lgu-button/20 transition-all">
                </div>

                <div class="flex gap-4">
                    <button type="button" 
                            onclick="window.location.href='{{ route('citizen.payment-slips.show', $paymentSlip->id) }}'"
                            class="flex-1 bg-gray-200 text-gray-700 font-bold py-4 rounded-lg hover:bg-gray-300 transition-all">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="flex-1 bg-lgu-button text-white font-bold py-4 rounded-lg hover:bg-lgu-highlight transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const channels = @json(config('payment.channels'));
    let selectedChannel = null;

    function selectChannel(channelKey) {
        selectedChannel = channelKey;
        const channel = channels[channelKey];
        
        // Update UI
        document.querySelectorAll('.channel-btn').forEach(btn => {
            btn.classList.remove('border-lgu-button', 'bg-blue-50');
            btn.classList.add('border-gray-300');
            btn.querySelector('.channel-check').classList.add('hidden');
        });
        
        const selectedBtn = document.getElementById(`channel-${channelKey}`);
        selectedBtn.classList.remove('border-gray-300');
        selectedBtn.classList.add('border-lgu-button', 'bg-blue-50');
        selectedBtn.querySelector('.channel-check').classList.remove('hidden');
        
        // Show instructions
        document.getElementById('payment-instructions').classList.remove('hidden');
        document.getElementById('payment_channel').value = channelKey;
        document.getElementById('app-name').textContent = channel.name;
        
        // Update channel details
        const channelDetails = `
            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-lucide="${channel.icon}" class="w-6 h-6 text-white"></i>
                </div>
                <div class="flex-1">
                    <h4 class="font-bold text-lg text-blue-900 mb-2">${channel.name}</h4>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-blue-700 font-semibold">Account Number:</span>
                            <code class="bg-blue-200 px-3 py-1 rounded font-mono text-blue-900 font-bold">${channel.account_number}</code>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-blue-700 font-semibold">Account Name:</span>
                            <span class="text-sm text-blue-900 font-bold">${channel.account_name}</span>
                        </div>
                    </div>
                    <p class="text-sm text-blue-700 mt-3">${channel.instructions}</p>
                </div>
            </div>
        `;
        
        document.getElementById('channel-details').innerHTML = channelDetails;
        
        // Reinitialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        // Scroll to instructions
        document.getElementById('payment-instructions').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Form validation
    document.getElementById('payment-form').addEventListener('submit', function(e) {
        const refNumber = document.getElementById('reference_number').value.trim();
        
        if (!selectedChannel) {
            e.preventDefault();
            alert('Please select a payment method first');
            return;
        }
        
        if (!refNumber) {
            e.preventDefault();
            alert('Please enter your transaction reference number');
            return;
        }
        
        // Show loading state
        const submitBtn = e.target.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i data-lucide="loader" class="w-5 h-5 animate-spin"></i> Processing...';
    });
</script>
@endpush
@endsection

