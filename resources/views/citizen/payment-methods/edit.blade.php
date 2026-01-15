@extends('layouts.citizen')

@section('page-title', 'Edit Payment Method')
@section('page-subtitle', 'Update your payment method details')

@section('page-content')
<div class="pb-gr-2xl">
    <!-- Back Button -->
    <div class="mb-gr-md">
        <a href="{{ route('citizen.payment-methods.index') }}" class="inline-flex items-center text-small font-medium text-gray-600 hover:text-gray-900">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-gr-xs"></i>
            Back to Payment Methods
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg">
            <form method="POST" action="{{ route('citizen.payment-methods.update', $paymentMethod->id) }}">
                @csrf
                @method('PUT')

                <!-- Payment Type -->
                <div class="mb-gr-lg">
                    <label class="block text-body font-semibold text-gray-900 mb-gr-sm">Payment Type <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-md">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment_type" value="gcash" {{ old('payment_type', $paymentMethod->payment_type) == 'gcash' ? 'checked' : '' }} class="sr-only peer" required>
                            <div class="border-2 border-gray-200 rounded-lg p-gr-md text-center peer-checked:border-lgu-green peer-checked:bg-lgu-green peer-checked:bg-opacity-5 transition-all">
                                <div class="w-12 h-12 bg-blue-100 rounded-full mx-auto mb-gr-sm flex items-center justify-center">
                                    <span class="text-h5 font-bold text-blue-600">G</span>
                                </div>
                                <p class="font-semibold text-gray-900">GCash</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment_type" value="paymaya" {{ old('payment_type', $paymentMethod->payment_type) == 'paymaya' ? 'checked' : '' }} class="sr-only peer" required>
                            <div class="border-2 border-gray-200 rounded-lg p-gr-md text-center peer-checked:border-lgu-green peer-checked:bg-lgu-green peer-checked:bg-opacity-5 transition-all">
                                <div class="w-12 h-12 bg-green-100 rounded-full mx-auto mb-gr-sm flex items-center justify-center">
                                    <span class="text-h5 font-bold text-green-600">P</span>
                                </div>
                                <p class="font-semibold text-gray-900">PayMaya</p>
                            </div>
                        </label>

                        <label class="relative cursor-pointer">
                            <input type="radio" name="payment_type" value="bank_transfer" {{ old('payment_type', $paymentMethod->payment_type) == 'bank_transfer' ? 'checked' : '' }} class="sr-only peer" required>
                            <div class="border-2 border-gray-200 rounded-lg p-gr-md text-center peer-checked:border-lgu-green peer-checked:bg-lgu-green peer-checked:bg-opacity-5 transition-all">
                                <div class="w-12 h-12 bg-purple-100 rounded-full mx-auto mb-gr-sm flex items-center justify-center">
                                    <i data-lucide="building-2" class="w-6 h-6 text-purple-600"></i>
                                </div>
                                <p class="font-semibold text-gray-900">Bank Transfer</p>
                            </div>
                        </label>
                    </div>
                    @error('payment_type')
                    <p class="text-caption text-red-600 mt-gr-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div class="mb-gr-md">
                    <label for="account_name" class="block text-body font-semibold text-gray-900 mb-gr-sm">Account Name <span class="text-red-500">*</span></label>
                    <input type="text" id="account_name" name="account_name" value="{{ old('account_name', $paymentMethod->account_name) }}" 
                           class="input-field @error('account_name') border-red-500 @enderror" 
                           placeholder="Juan Dela Cruz" required>
                    @error('account_name')
                    <p class="text-caption text-red-600 mt-gr-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div class="mb-gr-md">
                    <label for="account_number" class="block text-body font-semibold text-gray-900 mb-gr-sm">Account/Mobile Number <span class="text-red-500">*</span></label>
                    <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $paymentMethod->account_number) }}" 
                           class="input-field @error('account_number') border-red-500 @enderror" 
                           placeholder="09171234567 or Account Number" required>
                    @error('account_number')
                    <p class="text-caption text-red-600 mt-gr-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Set as Default -->
                <div class="mb-gr-lg">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_default" value="1" {{ old('is_default', $paymentMethod->is_default) ? 'checked' : '' }} class="w-4 h-4 text-lgu-green border-gray-300 rounded focus:ring-lgu-green">
                        <span class="ml-gr-xs text-body text-gray-900">Set as default payment method</span>
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-gr-sm pt-gr-md border-t border-gray-200">
                    <button type="submit" class="btn-primary flex-1">
                        <i data-lucide="save" class="w-4 h-4 mr-gr-xs"></i>
                        Update Payment Method
                    </button>
                    <a href="{{ route('citizen.payment-methods.index') }}" class="btn-secondary flex-1 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Initialize Lucide icons
lucide.createIcons();
</script>
@endsection

