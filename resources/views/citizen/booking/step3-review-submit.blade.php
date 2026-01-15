@extends('layouts.citizen')

@section('title', 'New Booking - Review & Submit')
@section('page-title', 'New Booking')
@section('page-subtitle', 'Step 3 of 3: Review & Submit')

@section('page-content')
<div class="max-w-6xl mx-auto">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </div>
                <span class="mt-2 text-sm font-medium text-green-600">Date & Time</span>
            </div>
            <div class="flex-1 h-1 bg-lgu-highlight"></div>
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check">
                        <path d="M20 6 9 17l-5-5"/>
                    </svg>
                </div>
                <span class="mt-2 text-sm font-medium text-green-600">Equipment</span>
            </div>
            <div class="flex-1 h-1 bg-lgu-highlight"></div>
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 bg-lgu-button text-lgu-button-text rounded-full flex items-center justify-center font-bold">
                    3
                </div>
                <span class="mt-2 text-sm font-medium text-lgu-button">Review</span>
            </div>
        </div>
    </div>

    <form action="{{ route('citizen.booking.store') }}" method="POST" enctype="multipart/form-data" id="bookingSubmitForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Booking Details -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Booking Details</h2>

                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 text-lgu-button mt-0.5 mr-3">
                                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                                <path d="M10 6h4"/>
                                <path d="M10 10h4"/>
                                <path d="M10 14h4"/>
                                <path d="M10 18h4"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">{{ $facility->name }}</p>
                                <p class="text-sm text-gray-600">{{ $facility->address }}</p>
                                @if($facility->city_name)
                                    <p class="text-sm text-gray-600">{{ $facility->city_name }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar text-lgu-button mt-0.5 mr-3">
                                <path d="M8 2v4"/>
                                <path d="M16 2v4"/>
                                <rect width="18" height="18" x="3" y="4" rx="2"/>
                                <path d="M3 10h18"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($step1Data['booking_date'])->format('l, F j, Y') }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($step1Data['start_time'])->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($step1Data['end_time'])->format('h:i A') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text text-lgu-button mt-0.5 mr-3">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                <path d="M10 9H8"/>
                                <path d="M16 13H8"/>
                                <path d="M16 17H8"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Purpose</p>
                                <p class="text-sm text-gray-600">{{ $step1Data['purpose'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users text-lgu-button mt-0.5 mr-3">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <div>
                                <p class="font-medium text-gray-900">Expected Attendees</p>
                                <p class="text-sm text-gray-600">{{ number_format($step1Data['expected_attendees']) }} people</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Equipment Summary -->
                @if(!empty($equipmentDetails))
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Selected Equipment</h2>

                        <div class="space-y-3">
                            @foreach($equipmentDetails as $detail)
                                <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900">{{ $detail['item']->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $detail['quantity'] }} x ₱{{ number_format($detail['item']->price_per_unit, 2) }}</p>
                                    </div>
                                    <p class="font-medium text-gray-900">₱{{ number_format($detail['subtotal'], 2) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Discount Application -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Apply Discounts</h2>

                    <div class="space-y-6">
                        <!-- City of Residence (Auto-filled from Registration) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin inline-block mr-1">
                                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                City of Residence
                            </label>
                            <div class="relative">
                                <input type="text" value="{{ $user->city_name ?? 'Not specified' }}" disabled
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed font-semibold">
                                <input type="hidden" name="city_of_residence" value="{{ $user->city_name ?? '' }}">
                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock text-gray-400">
                                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                    </svg>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-blue-600 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info inline-block mr-1">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                                Automatically filled from your registration.
                                @if($user->city_name && $facility->city_name && $user->city_name === $facility->city_name)
                                    <span class="ml-1 text-green-600 font-semibold">30% resident discount will be applied!</span>
                                @endif
                            </p>
                        </div>

                        <!-- Valid ID Type -->
                        <div>
                            <label for="valid_id_type" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-id-card inline-block mr-1">
                                    <path d="M16 10h2"/>
                                    <path d="M16 14h2"/>
                                    <path d="M6.17 15a3 3 0 0 1 5.66 0"/>
                                    <circle cx="9" cy="11" r="2"/>
                                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                                </svg>
                                Valid ID Type <span class="text-red-500">*</span>
                            </label>
                            <select name="valid_id_type" id="valid_id_type" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button">
                                <option value="">Select Valid ID</option>
                                <option value="SSS ID">SSS ID</option>
                                <option value="UMID">UMID</option>
                                <option value="PhilHealth ID">PhilHealth ID</option>
                                <option value="TIN ID">TIN ID</option>
                                <option value="Passport">Passport</option>
                                <option value="Driver's License">Driver's License</option>
                                <option value="Voter's ID">Voter's ID</option>
                                <option value="PRC ID">PRC ID</option>
                                <option value="Postal ID">Postal ID</option>
                                <option value="Barangay ID">Barangay ID</option>
                                <option value="Senior Citizen ID">Senior Citizen ID</option>
                                <option value="PWD ID">PWD ID</option>
                                <option value="National ID (PhilSys)">National ID (PhilSys)</option>
                                <option value="School ID">School ID</option>
                                <option value="Company ID">Company ID</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Select the type of government-issued ID you will upload</p>
                        </div>

                        <!-- Special Discount (Optional) -->
                        <div>
                            <!-- Hidden input that submits the value (used by both manual and auto sections) -->
                            <input type="hidden" name="special_discount_type" id="special_discount_type" value="">
                            
                            <!-- Manual Selection (shown when no auto-discount) -->
                            <div id="manualDiscountSection">
                                <label for="special_discount_type_display" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-percent inline-block mr-1">
                                        <line x1="19" x2="5" y1="5" y2="19"/>
                                        <circle cx="6.5" cy="6.5" r="2.5"/>
                                        <circle cx="17.5" cy="17.5" r="2.5"/>
                                    </svg>
                                    Special Discount (Optional)
                                </label>
                                <select id="special_discount_type_display"
                                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button bg-gray-100 cursor-not-allowed" disabled>
                                    <option value="">-- No Discount Available --</option>
                                </select>
                                <p class="mt-1 text-xs text-gray-500" id="manualDiscountHelpText">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-1">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" x2="12" y1="8" y2="12"/>
                                        <line x1="12" x2="12.01" y1="16" y2="16"/>
                                    </svg>
                                    Your selected ID type does not qualify for special discounts. Please select Senior Citizen ID, PWD ID, or School ID to receive a discount.
                                </p>
                            </div>

                            <!-- Auto-Applied Discount Display (shown when auto-discount is active) -->
                            <div id="autoDiscountDisplay" class="hidden">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-percent inline-block mr-1">
                                        <line x1="19" x2="5" y1="5" y2="19"/>
                                        <circle cx="6.5" cy="6.5" r="2.5"/>
                                        <circle cx="17.5" cy="17.5" r="2.5"/>
                                    </svg>
                                    Special Discount
                                </label>
                                <div class="px-4 py-3 bg-green-50 border-2 border-green-500 rounded-lg">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 mr-2">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        <span class="text-green-800 font-semibold" id="autoDiscountText">20% Discount Auto-Applied</span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-green-600 font-medium" id="autoDiscountHelpText">
                                    Discount automatically applied based on your ID type. No need to upload separate ID.
                                </p>
                            </div>
                        </div>

                        <!-- Special Discount ID Upload -->
                        <div id="specialDiscountIdSection" class="hidden">
                            <label for="special_discount_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-alert-circle inline-block mr-1 text-blue-600">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" x2="12" y1="8" y2="12"/>
                                    <line x1="12" x2="12.01" y1="16" y2="16"/>
                                </svg>
                                Additional ID Upload Required <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="special_discount_id" id="special_discount_id" accept=".jpg,.jpeg,.png,.pdf"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button">
                            <p class="mt-1 text-xs text-blue-600 bg-blue-50 p-2 rounded">
                                <strong>Note:</strong> You already selected a discount-eligible ID above. Please upload it again here for verification purposes, or upload a different document if you have multiple qualifying IDs.
                            </p>
                            @error('special_discount_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Valid ID Upload (Required) -->
                        <div class="space-y-4">
                            <div class="flex items-center mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-upload text-lgu-button mr-2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" x2="12" y1="3" y2="15"/>
                                </svg>
                                <h3 class="text-sm font-semibold text-gray-700">Upload Valid ID <span class="text-red-500">*</span></h3>
                            </div>
                            <p class="text-xs text-gray-600 mb-4">Please upload clear photos of your ID as selected above. All three images are required for verification.</p>

                            <!-- Front of ID -->
                            <div>
                                <label for="valid_id_front" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card inline-block mr-1">
                                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                                        <line x1="2" x2="22" y1="10" y2="10"/>
                                    </svg>
                                    ID Front <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="valid_id_front" id="valid_id_front" required accept=".jpg,.jpeg,.png"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button"
                                       onchange="previewImage(this, 'preview_id_front')">
                                <div id="preview_id_front" class="mt-2 hidden">
                                    <img src="" alt="ID Front Preview" class="w-full max-w-md h-48 object-cover rounded-lg border-2 border-lgu-button">
                                </div>
                                @error('valid_id_front')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Back of ID -->
                            <div>
                                <label for="valid_id_back" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-credit-card inline-block mr-1">
                                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                                        <line x1="2" x2="22" y1="10" y2="10"/>
                                    </svg>
                                    ID Back <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="valid_id_back" id="valid_id_back" required accept=".jpg,.jpeg,.png"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button"
                                       onchange="previewImage(this, 'preview_id_back')">
                                <div id="preview_id_back" class="mt-2 hidden">
                                    <img src="" alt="ID Back Preview" class="w-full max-w-md h-48 object-cover rounded-lg border-2 border-lgu-button">
                                </div>
                                @error('valid_id_back')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Selfie with ID -->
                            <div>
                                <label for="valid_id_selfie" class="block text-sm font-medium text-gray-700 mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-check inline-block mr-1">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <polyline points="16 11 18 13 22 9"/>
                                    </svg>
                                    Selfie with ID <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="valid_id_selfie" id="valid_id_selfie" required accept=".jpg,.jpeg,.png"
                                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button"
                                       onchange="previewImage(this, 'preview_id_selfie')">
                                <div id="preview_id_selfie" class="mt-2 hidden">
                                    <img src="" alt="Selfie Preview" class="w-full max-w-md h-48 object-cover rounded-lg border-2 border-lgu-button">
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Take a photo of yourself holding your ID next to your face</p>
                                @error('valid_id_selfie')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div>
                            <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square inline-block mr-1">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                </svg>
                                Special Requests / Additional Notes (Optional)
                            </label>
                            <textarea name="special_requests" id="special_requests" rows="3"
                                      placeholder="Any special requirements or requests..."
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button">{{ old('special_requests') }}</textarea>
                            @error('special_requests')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <div class="flex items-start">
                        <input type="checkbox" id="terms" name="terms" required
                               class="mt-1 h-4 w-4 text-lgu-button focus:ring-lgu-button border-gray-300 rounded cursor-pointer">
                        <label for="terms" class="ml-3 text-sm text-gray-700 cursor-pointer">
                            I agree to the <span class="text-lgu-button font-semibold">Reservation Terms & Conditions</span> 
                            and confirm that all information provided is accurate. I understand that my booking is subject 
                            to staff verification and approval.
                        </label>
                    </div>
                </div>

                <!-- Mobile Back Button (visible on mobile only) -->
                <div class="lg:hidden">
                    <a href="{{ url()->previous() }}" 
                       class="block w-full px-6 py-3 border-2 border-lgu-stroke rounded-lg text-lgu-headline font-medium hover:bg-lgu-bg transition text-center">
                        ← Back to Equipment
                    </a>
                </div>
            </div>

            <!-- Pricing Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-lg rounded-lg p-6 sticky top-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Final Pricing</h3>

                    <div class="space-y-3 mb-4 pb-4 border-b">
                        <div class="text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Base Rate ({{ $pricing['base_hours'] }} hours)</span>
                                <span>₱{{ number_format($pricing['base_rate'], 2) }}</span>
                            </div>
                            @if(isset($pricing['pricing_model']) && $pricing['pricing_model'] === 'per_person')
                                <div class="text-xs text-gray-500 mt-1">
                                    ₱{{ number_format($pricing['per_person_rate'], 2) }} per person × {{ number_format($pricing['expected_attendees']) }} people
                                </div>
                            @endif
                        </div>

                        @if($pricing['extension_rate'] > 0 && isset($pricing['extension_hours']) && $pricing['extension_hours'] > 0)
                            <div class="text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Time Extension ({{ $pricing['extension_hours'] }} {{ $pricing['extension_hours'] == 1 ? 'hour' : 'hours' }})</span>
                                    <span>₱{{ number_format($pricing['extension_rate'], 2) }}</span>
                                </div>
                                @if(isset($pricing['pricing_model']) && $pricing['pricing_model'] === 'per_person' && isset($pricing['extension_rate_per_block']))
                                    <div class="text-xs text-gray-500 mt-1">
                                        ₱{{ number_format($pricing['extension_rate_per_block'], 2) }} per person per 2-hour block × {{ number_format($pricing['expected_attendees']) }} people × {{ $pricing['extension_blocks'] }} {{ $pricing['extension_blocks'] == 1 ? 'block' : 'blocks' }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if($pricing['equipment_total'] > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Equipment</span>
                                <span>₱{{ number_format($pricing['equipment_total'], 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-between font-medium text-gray-900 mb-4 pb-4 border-b">
                        <span>Subtotal</span>
                        <span id="displaySubtotal">₱{{ number_format($pricing['subtotal'], 2) }}</span>
                    </div>

                    <div class="space-y-2 mb-4 pb-4 border-b">
                        <div class="flex justify-between text-sm text-green-600" id="residentDiscountDisplay" style="display: none;">
                            <span>Resident Discount (30%)</span>
                            <span id="residentDiscountAmount">-₱0.00</span>
                        </div>

                        <div class="flex justify-between text-sm text-green-600" id="specialDiscountDisplay" style="display: none;">
                            <span id="specialDiscountLabel">Special Discount (20%)</span>
                            <span id="specialDiscountAmount">-₱0.00</span>
                        </div>
                    </div>

                    <div class="flex justify-between text-xl font-bold text-lgu-button mb-6">
                        <span>Total Amount</span>
                        <span id="displayTotal">₱{{ number_format($pricing['subtotal'], 2) }}</span>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full px-6 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition shadow-lg cursor-pointer">
                            Submit Booking
                        </button>
                        <a href="{{ url()->previous() }}" 
                           class="block w-full px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-center cursor-pointer">
                            Back to Equipment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const validIdTypeSelect = document.getElementById('valid_id_type');
    const specialDiscountSelect = document.getElementById('special_discount_type'); // Hidden input
    const specialDiscountDisplay = document.getElementById('special_discount_type_display'); // Display select
    const specialDiscountIdSection = document.getElementById('specialDiscountIdSection');
    const specialDiscountIdInput = document.getElementById('special_discount_id');
    
    const subtotal = {{ $pricing['subtotal'] }};
    const facilityCity = '{{ $facility->city_name ?? "" }}';

    // Auto-apply discount based on Valid ID Type
    let isAutoApplied = false;
    const manualDiscountSection = document.getElementById('manualDiscountSection');
    const autoDiscountDisplay = document.getElementById('autoDiscountDisplay');
    const autoDiscountText = document.getElementById('autoDiscountText');
    const autoDiscountHelpText = document.getElementById('autoDiscountHelpText');
    const manualDiscountHelpText = document.getElementById('manualDiscountHelpText');
    
    validIdTypeSelect.addEventListener('change', function() {
        const selectedIdType = this.value;
        
        // Map ID types to discount types
        if (selectedIdType === 'Senior Citizen ID') {
            specialDiscountSelect.value = 'senior';
            autoDiscountText.textContent = 'Senior Citizen (20% off)';
            autoDiscountHelpText.textContent = 'Senior Citizen discount automatically applied based on your ID type. No need to upload separate ID.';
            manualDiscountSection.classList.add('hidden');
            autoDiscountDisplay.classList.remove('hidden');
            isAutoApplied = true;
        } else if (selectedIdType === 'PWD ID') {
            specialDiscountSelect.value = 'pwd';
            autoDiscountText.textContent = 'PWD (20% off)';
            autoDiscountHelpText.textContent = 'PWD discount automatically applied based on your ID type. No need to upload separate ID.';
            manualDiscountSection.classList.add('hidden');
            autoDiscountDisplay.classList.remove('hidden');
            isAutoApplied = true;
        } else if (selectedIdType === 'School ID') {
            specialDiscountSelect.value = 'student';
            autoDiscountText.textContent = 'Student (20% off)';
            autoDiscountHelpText.textContent = 'Student discount automatically applied based on your ID type. No need to upload separate ID.';
            manualDiscountSection.classList.add('hidden');
            autoDiscountDisplay.classList.remove('hidden');
            isAutoApplied = true;
        } else {
            // Non-discount ID selected - show disabled dropdown with "no discount" message
            specialDiscountSelect.value = ''; // Hidden input value
            manualDiscountSection.classList.remove('hidden');
            autoDiscountDisplay.classList.add('hidden');
            isAutoApplied = false;
        }
        
        // Trigger change event to show/hide ID upload section and recalculate
        specialDiscountSelect.dispatchEvent(new Event('change'));
    });

    // Show/hide special discount ID upload
    specialDiscountSelect.addEventListener('change', function() {
        // If discount was auto-applied from ID type, don't require separate upload
        if (this.value && !isAutoApplied) {
            specialDiscountIdSection.classList.remove('hidden');
            specialDiscountIdInput.required = true;
        } else {
            specialDiscountIdSection.classList.add('hidden');
            specialDiscountIdInput.required = false;
        }
        calculateTotal();
    });

    // Calculate total on page load with user's registered city
    calculateTotal();

    function calculateTotal() {
        const selectedCity = '{{ $user->city_name ?? "" }}';
        const specialDiscountType = specialDiscountSelect.value;

        let residentDiscount = 0;
        let specialDiscount = 0;

        // Check if resident
        if (facilityCity && selectedCity.toLowerCase() === facilityCity.toLowerCase()) {
            residentDiscount = subtotal * 0.30;
            document.getElementById('residentDiscountDisplay').style.display = 'flex';
            document.getElementById('residentDiscountAmount').textContent = '-₱' + residentDiscount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        } else {
            document.getElementById('residentDiscountDisplay').style.display = 'none';
        }

        // Apply special discount
        if (specialDiscountType) {
            const afterResidentDiscount = subtotal - residentDiscount;
            specialDiscount = afterResidentDiscount * 0.20;
            document.getElementById('specialDiscountDisplay').style.display = 'flex';
            document.getElementById('specialDiscountAmount').textContent = '-₱' + specialDiscount.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
            let discountLabel = 'Special Discount (20%)';
            if (specialDiscountType === 'senior') discountLabel = 'Senior Citizen Discount (20%)';
            else if (specialDiscountType === 'pwd') discountLabel = 'PWD Discount (20%)';
            else if (specialDiscountType === 'student') discountLabel = 'Student Discount (20%)';
            
            document.getElementById('specialDiscountLabel').textContent = discountLabel;
        } else {
            document.getElementById('specialDiscountDisplay').style.display = 'none';
        }

        const total = subtotal - residentDiscount - specialDiscount;
        document.getElementById('displayTotal').textContent = '₱' + total.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
});

// Image preview function
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const img = preview.querySelector('img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
        img.src = '';
    }
}

// Terms and Conditions Modal with Timer
const termsCheckbox = document.getElementById('terms');
let termsAccepted = false;

termsCheckbox.addEventListener('click', function(e) {
    // If already accepted, allow normal behavior
    if (termsAccepted) {
        return;
    }
    
    // Prevent checkbox from being checked
    e.preventDefault();
    
    // Show SweetAlert2 modal with policies
    showTermsModal();
});

function showTermsModal() {
    const timerDuration = 5; // seconds
    let timerInterval;
    
    Swal.fire({
        title: '<div style="color: #0F5257;"><svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 10px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>Facility Reservation Terms & Conditions</div>',
        html: `
            <div style="text-align: left; max-height: 60vh; overflow-y: auto; padding: 20px; font-size: 14px; line-height: 1.6;">
                <div style="background: #E8F4F5; border-left: 4px solid #0F5257; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                    <p style="margin: 0; color: #0F5257; font-weight: 600;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 5px;">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" x2="12" y1="8" y2="12"/>
                            <line x1="12" x2="12.01" y1="16" y2="16"/>
                        </svg>
                        Please read these terms carefully before proceeding with your reservation.
                    </p>
                </div>

                <h3 style="color: #0F5257; font-weight: 600; margin-top: 20px; margin-bottom: 10px; font-size: 16px;">1. Reservation Policy</h3>
                <ul style="margin-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;">All reservations must be made at least <strong>7 business days</strong> in advance.</li>
                    <li style="margin-bottom: 8px;">Bookings are subject to <strong>staff verification and approval</strong> within 2-3 business days.</li>
                    <li style="margin-bottom: 8px;">Reservation status will be communicated via email or phone.</li>
                    <li style="margin-bottom: 8px;">Approved bookings require <strong>payment within 48 hours</strong> to confirm the reservation.</li>
                </ul>

                <h3 style="color: #0F5257; font-weight: 600; margin-top: 20px; margin-bottom: 10px; font-size: 16px;">2. City Event Priority</h3>
                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; margin-bottom: 15px; border-radius: 4px;">
                    <p style="margin: 0 0 10px 0; color: #92400E; font-weight: 600;">Important Notice:</p>
                    <p style="margin: 0; color: #92400E;">
                        <strong>Official city events and government functions have priority</strong> over private reservations. 
                        In the event that your booked facility and time slot are required for a city event, you will be notified 
                        at least 5 business days in advance (when possible) and offered the following options:
                    </p>
                </div>
                <ul style="margin-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;"><strong>Option A:</strong> Reschedule to a different date/time at the same facility (no additional charges)</li>
                    <li style="margin-bottom: 8px;"><strong>Option B:</strong> Book an alternative facility at the same date/time (if available)</li>
                    <li style="margin-bottom: 8px;"><strong>Option C:</strong> Request a full refund of all payments made</li>
                </ul>

                <h3 style="color: #0F5257; font-weight: 600; margin-top: 20px; margin-bottom: 10px; font-size: 16px;">3. Cancellation & Refund Policy</h3>
                <ul style="margin-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;"><strong>7+ days before event:</strong> 100% refund</li>
                    <li style="margin-bottom: 8px;"><strong>4-6 days before event:</strong> 50% refund</li>
                    <li style="margin-bottom: 8px;"><strong>Less than 3 days:</strong> No refund</li>
                    <li style="margin-bottom: 8px;">Refunds will be processed within 10-15 business days.</li>
                </ul>

                <h3 style="color: #0F5257; font-weight: 600; margin-top: 20px; margin-bottom: 10px; font-size: 16px;">4. Facility Usage Rules</h3>
                <ul style="margin-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;">Facilities must be used only for the stated purpose in the reservation.</li>
                    <li style="margin-bottom: 8px;">No smoking, drinking, or illegal activities are permitted on the premises.</li>
                    <li style="margin-bottom: 8px;">You are responsible for any damage to the facility or equipment during your reservation.</li>
                    <li style="margin-bottom: 8px;">Noise levels must be kept reasonable and within city ordinances.</li>
                    <li style="margin-bottom: 8px;">The facility must be vacated and cleaned before the end time.</li>
                </ul>

                <h3 style="color: #0F5257; font-weight: 600; margin-top: 20px; margin-bottom: 10px; font-size: 16px;">5. Liability & Responsibility</h3>
                <ul style="margin-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;">The LGU is not liable for any personal injury or property loss during your event.</li>
                    <li style="margin-bottom: 8px;">You assume full responsibility for the safety of your guests and attendees.</li>
                    <li style="margin-bottom: 8px;">Adequate security/supervision must be provided for events over 50 attendees.</li>
                </ul>

                <h3 style="color: #0F5257; font-weight: 600; margin-top: 20px; margin-bottom: 10px; font-size: 16px;">6. Payment Terms</h3>
                <ul style="margin-left: 20px; color: #374151;">
                    <li style="margin-bottom: 8px;">All discounts (resident, senior, PWD, student) are subject to verification.</li>
                    <li style="margin-bottom: 8px;">Payment must be made via approved payment methods only.</li>
                    <li style="margin-bottom: 8px;">Equipment rental fees are non-refundable unless the entire reservation is cancelled per the refund policy.</li>
                </ul>

                <div style="background: #E8F4F5; border: 2px solid #0F5257; padding: 15px; margin-top: 25px; border-radius: 8px; text-align: center;">
                    <p style="margin: 0; color: #0F5257; font-weight: 600; font-size: 15px;">
                        By clicking "I Agree", you acknowledge that you have read, understood, and agree to comply with all the terms and conditions stated above.
                    </p>
                </div>
            </div>
        `,
        width: '700px',
        showCancelButton: true,
        confirmButtonText: '<span id="agree-timer">Please read (5s)</span>',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#0F5257',
        cancelButtonColor: '#6B7280',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            const confirmButton = Swal.getConfirmButton();
            confirmButton.disabled = true;
            confirmButton.style.opacity = '0.5';
            confirmButton.style.cursor = 'not-allowed';
            
            let timeLeft = timerDuration;
            
            timerInterval = setInterval(() => {
                timeLeft--;
                
                if (timeLeft > 0) {
                    document.getElementById('agree-timer').textContent = `Please read (${timeLeft}s)`;
                } else {
                    clearInterval(timerInterval);
                    confirmButton.disabled = false;
                    confirmButton.style.opacity = '1';
                    confirmButton.style.cursor = 'pointer';
                    document.getElementById('agree-timer').innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 5px;"><polyline points="20 6 9 17 4 12"/></svg>I Agree';
                }
            }, 1000);
        },
        willClose: () => {
            if (timerInterval) {
                clearInterval(timerInterval);
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // User agreed - check the checkbox
            termsAccepted = true;
            termsCheckbox.checked = true;
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Terms Accepted',
                text: 'Thank you for reviewing the reservation terms and conditions.',
                timer: 2000,
                showConfirmButton: false,
                iconColor: '#0F5257'
            });
        } else {
            // User cancelled - uncheck the checkbox
            termsCheckbox.checked = false;
            termsAccepted = false;
        }
    });
}

// Auto-save Step 3 form data to localStorage
const bookingForm = document.getElementById('bookingSubmitForm');

// Restore saved Step 3 data
function restoreStep3Data() {
    const savedData = localStorage.getItem('booking_step3_data');
    if (savedData) {
        const data = JSON.parse(savedData);
        
        // Restore valid ID type
        if (data.valid_id_type) {
            const validIdSelect = document.getElementById('valid_id_type');
            if (validIdSelect) {
                validIdSelect.value = data.valid_id_type;
                // Trigger change event to apply auto-discount logic
                validIdSelect.dispatchEvent(new Event('change'));
            }
        }
        
        // Restore special requests
        if (data.special_requests) {
            const specialRequestsField = document.getElementById('special_requests');
            if (specialRequestsField) specialRequestsField.value = data.special_requests;
        }
        
        // Note: File uploads cannot be restored for security reasons
        // Users will need to re-upload files if they refresh
    }
}

// Save Step 3 form data
function saveStep3Data() {
    const data = {
        valid_id_type: document.getElementById('valid_id_type')?.value,
        special_requests: document.getElementById('special_requests')?.value
    };
    localStorage.setItem('booking_step3_data', JSON.stringify(data));
}

// Attach save listeners to Step 3 inputs
const step3Inputs = bookingForm.querySelectorAll('input:not([type="file"]):not([type="checkbox"]), textarea, select');
step3Inputs.forEach(input => {
    input.addEventListener('input', saveStep3Data);
    input.addEventListener('change', saveStep3Data);
});

// Restore Step 3 data on page load
restoreStep3Data();

// Clear ALL saved booking data when form is successfully submitted
bookingForm.addEventListener('submit', function(e) {
    // Only clear if terms are accepted (form will actually submit)
    if (termsAccepted) {
        // Clear all booking data from localStorage
        localStorage.removeItem('booking_step1_data');
        localStorage.removeItem('booking_step2_data');
        localStorage.removeItem('booking_step3_data');
    }
});

// Add a button to manually clear saved data (optional - for user convenience)
function addClearDataOption() {
    const hasSavedData = localStorage.getItem('booking_step1_data') || 
                        localStorage.getItem('booking_step2_data') || 
                        localStorage.getItem('booking_step3_data');
    
    if (hasSavedData) {
        // Show a subtle notification that data was restored
        const notification = document.createElement('div');
        notification.id = 'restored-data-notification';
        notification.className = 'fixed bottom-4 right-4 bg-green-50 border-2 border-green-500 rounded-lg p-4 shadow-lg z-50 max-w-sm';
        notification.innerHTML = `
            <div class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 mr-2 flex-shrink-0">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-green-800 mb-1">Form Data Restored</p>
                    <p class="text-xs text-green-700 mb-2">Your previous booking information has been automatically restored.</p>
                    <button onclick="clearAllBookingData()" class="text-xs text-green-600 hover:text-green-800 font-medium underline">
                        Start Fresh
                    </button>
                </div>
                <button onclick="document.getElementById('restored-data-notification').remove()" class="text-green-600 hover:text-green-800 ml-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" x2="6" y1="6" y2="18"/>
                        <line x1="6" x2="18" y1="6" y2="18"/>
                    </svg>
                </button>
            </div>
        `;
        document.body.appendChild(notification);
        
        // Auto-hide after 8 seconds
        setTimeout(() => {
            notification.remove();
        }, 8000);
    }
}

function clearAllBookingData() {
    Swal.fire({
        title: 'Clear Saved Data?',
        text: 'This will remove all your saved booking information and refresh the page.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Clear All',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#DC2626',
        cancelButtonColor: '#6B7280'
    }).then((result) => {
        if (result.isConfirmed) {
            localStorage.removeItem('booking_step1_data');
            localStorage.removeItem('booking_step2_data');
            localStorage.removeItem('booking_step3_data');
            
            Swal.fire({
                icon: 'success',
                title: 'Data Cleared',
                text: 'Redirecting to start fresh booking...',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '{{ route("citizen.booking.create") }}';
            });
        }
    });
}

// Show notification if data was restored
addClearDataOption();
</script>
@endpush
@endsection

