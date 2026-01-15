@extends('layouts.citizen')

@section('title', 'New Booking - Select Date & Time')
@section('page-title', 'New Booking')
@section('page-subtitle', 'Step 1 of 3: Select Facility, Date & Time')

@php
    // Get session data for form restoration
    $sessionData = session('booking_step1', []);
@endphp

@section('page-content')
<div class="max-w-4xl mx-auto">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 bg-lgu-button text-lgu-button-text rounded-full flex items-center justify-center font-bold">
                    1
                </div>
                <span class="mt-2 text-sm font-medium text-lgu-button">Date & Time</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300"></div>
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold">
                    2
                </div>
                <span class="mt-2 text-sm text-gray-500">Equipment</span>
            </div>
            <div class="flex-1 h-1 bg-gray-300"></div>
            <div class="flex flex-col items-center flex-1">
                <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold">
                    3
                </div>
                <span class="mt-2 text-sm text-gray-500">Review</span>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="bg-white shadow-lg rounded-lg p-8">
        <form action="{{ route('citizen.booking.step2') }}" method="POST" id="bookingStep1Form">
            @csrf

            <!-- Facility Selection -->
            <div class="mb-6">
                <label for="facility_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building-2 inline-block mr-1">
                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                        <path d="M10 6h4"/>
                        <path d="M10 10h4"/>
                        <path d="M10 14h4"/>
                        <path d="M10 18h4"/>
                    </svg>
                    Select Facility <span class="text-red-500">*</span>
                </label>
                
                @if($facility)
                    <!-- Read-only facility display when pre-selected -->
                    <div class="relative">
                        <input type="text" value="{{ $facility->name }}" disabled
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed">
                        <input type="hidden" name="facility_id" value="{{ $facility->facility_id }}">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock text-gray-400">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info inline-block mr-1">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        Facility locked. Click "Cancel" below to go back and select a different facility.
                    </p>
                @else
                    <!-- Editable dropdown when no facility pre-selected -->
                    <select name="facility_id" id="facility_id" required
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button">
                        <option value="">-- Choose a facility --</option>
                        @foreach($facilities as $fac)
                            <option value="{{ $fac->facility_id }}">{{ $fac->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">Select the facility you want to reserve</p>
                @endif
                
                @error('facility_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Selection -->
            <div class="mb-6">
                <label for="booking_date_display" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar inline-block mr-1">
                        <path d="M8 2v4"/>
                        <path d="M16 2v4"/>
                        <rect width="18" height="18" x="3" y="4" rx="2"/>
                        <path d="M3 10h18"/>
                    </svg>
                    Booking Date <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text" id="booking_date_display" readonly required
                           placeholder="Click to select a date"
                           value="{{ old('booking_date') ? date('F j, Y', strtotime(old('booking_date'))) : '' }}"
                           class="block w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg cursor-pointer hover:border-lgu-button focus:ring-lgu-button focus:border-lgu-button bg-white transition">
                    <input type="hidden" name="booking_date" id="booking_date" value="{{ old('booking_date') }}">
                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days text-gray-400">
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
                </div>
                <p class="mt-1 text-xs text-gray-500">Book at least 7 business days in advance</p>
                @error('booking_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Calendar Modal -->
            <div id="calendarModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-20 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-opacity duration-300">
                <div id="calendarModalContent" class="rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95 opacity-0 overflow-hidden">
                    <!-- Modal Header -->
                    <div class="bg-lgu-headline p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar mr-2">
                                    <path d="M8 2v4"/>
                                    <path d="M16 2v4"/>
                                    <rect width="18" height="18" x="3" y="4" rx="2"/>
                                    <path d="M3 10h18"/>
                                </svg>
                                Select Booking Date
                            </h3>
                            <button type="button" id="closeCalendarModal" class="text-white hover:text-gray-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                                    <path d="M18 6 6 18"/>
                                    <path d="m6 6 12 12"/>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Month/Year Navigation -->
                        <div class="flex items-center justify-between">
                            <button type="button" id="prevMonth" class="p-2 hover:bg-lgu-stroke rounded-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left text-white">
                                    <path d="m15 18-6-6 6-6"/>
                                </svg>
                            </button>
                            <div class="text-center">
                                <div id="currentMonthYear" class="text-xl font-bold text-white"></div>
                                <div class="text-xs text-lgu-highlight mt-1">Minimum 7 business days advance booking</div>
                            </div>
                            <button type="button" id="nextMonth" class="p-2 hover:bg-lgu-stroke rounded-lg transition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right text-white">
                                    <path d="m9 18 6-6-6-6"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Body -->
                    <div class="bg-white p-6">
                        <!-- Weekday Headers -->
                        <div class="grid grid-cols-7 gap-2 mb-3">
                            <div class="text-center text-xs font-bold text-gray-600 py-2">Sun</div>
                            <div class="text-center text-xs font-bold text-gray-600 py-2">Mon</div>
                            <div class="text-center text-xs font-bold text-gray-600 py-2">Tue</div>
                            <div class="text-center text-xs font-bold text-gray-600 py-2">Wed</div>
                            <div class="text-center text-xs font-bold text-gray-600 py-2">Thu</div>
                            <div class="text-center text-xs font-bold text-gray-600 py-2">Fri</div>
                            <div class="text-center text-xs font-bold text-gray-600 py-2">Sat</div>
                        </div>

                        <!-- Calendar Days Grid -->
                        <div id="calendarDays" class="grid grid-cols-7 gap-2"></div>

                        <!-- Legend -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex flex-wrap gap-4 text-xs">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-lgu-button rounded mr-2"></div>
                                    <span class="text-gray-600">Selected</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-white border-2 border-lgu-button rounded mr-2"></div>
                                    <span class="text-gray-600">Available (Mon-Sat)</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                                    <span class="text-gray-600">Fully Booked</span>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-gray-100 rounded mr-2 flex items-center justify-center">
                                        <span class="text-gray-400 text-xs font-bold">×</span>
                                    </div>
                                    <span class="text-gray-600">Closed (Sundays)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-white px-6 pb-6 flex justify-end space-x-3">
                        <button type="button" id="cancelCalendarBtn" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition font-medium">
                            Cancel
                        </button>
                        <button type="button" id="clearDateBtn" class="px-6 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 transition font-medium">
                            Clear Date
                        </button>
                    </div>
                </div>
            </div>

            <!-- Time Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_time_display" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock inline-block mr-1">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Start Time <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="start_time_display" readonly required
                               placeholder="Click to select time"
                               value="{{ old('start_time_display', '08:00 AM') }}"
                               style="-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;"
                               class="block w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg cursor-pointer hover:border-lgu-button focus:ring-lgu-button focus:border-lgu-button bg-white transition font-semibold text-gray-700">
                        <input type="hidden" name="start_time" id="start_time" value="{{ old('start_time', $sessionData['start_time'] ?? '08:00') }}">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-gray-400">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                    </div>
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time_display" class="block text-sm font-medium text-gray-700 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock inline-block mr-1">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        End Time <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" id="end_time_display" readonly required
                               placeholder="Click to select time"
                               value="{{ old('end_time_display', '11:00 AM') }}"
                               style="-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;"
                               class="block w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg cursor-pointer hover:border-lgu-button focus:ring-lgu-button focus:border-lgu-button bg-white transition font-semibold text-gray-700">
                        <input type="hidden" name="end_time" id="end_time" value="{{ old('end_time', $sessionData['end_time'] ?? '11:00') }}">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock text-gray-400">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                    </div>
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Time Picker Modal -->
            <div id="timePickerModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-20 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-opacity duration-300">
                <div id="timePickerModalContent" class="rounded-xl shadow-2xl w-full max-w-lg transform transition-all duration-300 scale-95 opacity-0 bg-white">
                    <!-- Modal Header -->
                    <div class="bg-lgu-headline px-4 py-3 sticky top-0 z-10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                <span id="timePickerTitle">Select Time</span>
                            </h3>
                            <button type="button" id="closeTimePickerModal" class="text-white hover:text-gray-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x">
                                    <path d="M18 6 6 18"/>
                                    <path d="m6 6 12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Time Picker Body -->
                    <div class="bg-white p-5">
                        <!-- Current Time Display -->
                        <div class="text-center mb-4 py-4 bg-lgu-headline rounded-lg">
                            <div id="currentTimeDisplay" class="text-4xl font-black text-white tracking-wider antialiased" style="-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;">08:00 AM</div>
                        </div>

                        <!-- Hours Grid -->
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 mb-2">HOUR</label>
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;" id="hourSelector">
                                <button type="button" data-hour="01" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">01</button>
                                <button type="button" data-hour="02" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">02</button>
                                <button type="button" data-hour="03" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">03</button>
                                <button type="button" data-hour="04" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">04</button>
                                <button type="button" data-hour="05" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">05</button>
                                <button type="button" data-hour="06" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">06</button>
                                <button type="button" data-hour="07" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">07</button>
                                <button type="button" data-hour="08" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">08</button>
                                <button type="button" data-hour="09" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">09</button>
                                <button type="button" data-hour="10" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">10</button>
                                <button type="button" data-hour="11" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">11</button>
                                <button type="button" data-hour="12" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">12</button>
                            </div>
                        </div>

                        <!-- Minutes Grid -->
                        <div class="mb-4">
                            <label class="block text-xs font-bold text-gray-700 mb-2">MINUTE</label>
                            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;" id="minuteSelector">
                                <button type="button" data-minute="00" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">00</button>
                                <button type="button" data-minute="15" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">15</button>
                                <button type="button" data-minute="30" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">30</button>
                                <button type="button" data-minute="45" class="time-option py-2.5 text-sm text-center hover:bg-lgu-highlight hover:text-white transition border border-gray-300 rounded-lg font-medium bg-white text-gray-700">45</button>
                            </div>
                        </div>

                        <!-- AM/PM Grid -->
                        <div class="mb-1">
                            <label class="block text-xs font-bold text-gray-700 mb-2">PERIOD</label>
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                                <button type="button" data-period="AM" class="time-option py-3 text-base text-center hover:bg-lgu-highlight hover:text-white transition border-2 border-gray-300 rounded-lg font-bold bg-white text-gray-700">AM</button>
                                <button type="button" data-period="PM" class="time-option py-3 text-base text-center hover:bg-lgu-highlight hover:text-white transition border-2 border-gray-300 rounded-lg font-bold bg-white text-gray-700">PM</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-5 py-3 flex justify-end space-x-2 border-t border-gray-200 sticky bottom-0 z-10">
                        <button type="button" id="cancelTimePickerBtn" class="px-5 py-2 text-sm border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition font-medium">
                            Cancel
                        </button>
                        <button type="button" id="confirmTimeBtn" class="px-5 py-2 text-sm bg-lgu-button text-white rounded-lg hover:bg-lgu-highlight transition font-medium shadow">
                            Confirm
                        </button>
                    </div>
                </div>
            </div>

            <!-- Availability Check Result -->
            <div id="availabilityResult" class="hidden mb-6"></div>

            <!-- Purpose -->
            <div class="mb-6">
                <label for="purpose_type" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text inline-block mr-1">
                        <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                        <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                        <path d="M10 9H8"/>
                        <path d="M16 13H8"/>
                        <path d="M16 17H8"/>
                    </svg>
                    Event Purpose <span class="text-red-500">*</span>
                </label>
                
                <!-- Predefined Event Types Dropdown -->
                <select id="purpose_type" 
                        class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button mb-3">
                    <option value="">-- Select Event Type --</option>
                    <option value="Birthday Celebration">Birthday Celebration</option>
                    <option value="Wedding Reception">Wedding Reception</option>
                    <option value="Company Seminar/Training">Company Seminar/Training</option>
                    <option value="Community Meeting">Community Meeting</option>
                    <option value="Sports Event">Sports Event</option>
                    <option value="Cultural Event">Cultural Event</option>
                    <option value="Religious Gathering">Religious Gathering</option>
                    <option value="Educational Workshop">Educational Workshop</option>
                    <option value="Government Program">Government Program</option>
                    <option value="Charity Event">Charity Event</option>
                    <option value="Corporate Event">Corporate Event</option>
                    <option value="Family Reunion">Family Reunion</option>
                    <option value="Other">Other (Customize)</option>
                </select>

                <!-- Hidden Custom Purpose Input (shown when "Other" is selected) -->
                <div id="custom_purpose_wrapper" class="hidden">
                    <label for="custom_purpose" class="block text-sm font-medium text-gray-600 mb-2">
                        Please specify your event purpose:
                    </label>
                    <textarea id="custom_purpose" rows="3"
                              placeholder="Please describe your event purpose in detail..."
                              class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button"></textarea>
                </div>

                <!-- Hidden input to store the final purpose value for form submission -->
                <input type="hidden" name="purpose" id="purpose" required>
                
                @error('purpose')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Expected Attendees -->
            <div class="mb-8">
                <label for="expected_attendees" class="block text-sm font-medium text-gray-700 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users inline-block mr-1">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Expected Number of Attendees <span class="text-red-500">*</span>
                </label>
                <input type="number" name="expected_attendees" id="expected_attendees" required 
                       min="{{ $facility->min_capacity ?? 1 }}" 
                       max="{{ $facility->capacity ?? 1000 }}"
                       value="{{ old('expected_attendees', session('booking_step1.expected_attendees')) }}"
                       placeholder="Minimum {{ $facility->min_capacity ?? 1 }} people"
                       class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button">
                <p class="mt-1 text-xs text-gray-500">
                    <span class="font-semibold text-lgu-headline">Minimum: {{ number_format($facility->min_capacity ?? 1) }} people</span> • 
                    Maximum: {{ number_format($facility->capacity ?? 1000) }} people
                </p>
                @error('expected_attendees')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Real-time Pricing Breakdown -->
            <div id="pricingBreakdown" class="mb-8 bg-blue-50 border-2 border-lgu-stroke rounded-xl p-6 shadow-sm" style="display: none;">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-lgu-headline flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calculator mr-2">
                            <rect width="16" height="20" x="4" y="2" rx="2"/>
                            <line x1="8" x2="16" y1="6" y2="6"/>
                            <line x1="16" x2="16" y1="14" y2="14"/>
                            <path d="M16 10h.01"/>
                            <path d="M12 10h.01"/>
                            <path d="M8 10h.01"/>
                            <path d="M12 14h.01"/>
                            <path d="M8 14h.01"/>
                            <path d="M12 18h.01"/>
                            <path d="M8 18h.01"/>
                        </svg>
                        Estimated Pricing
                    </h3>
                    <span class="text-xs text-gray-500 italic">Live estimate</span>
                </div>
                
                <div class="space-y-3 mb-4 pb-4 border-b border-gray-300">
                    <div id="durationDisplay" class="text-sm font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Duration: <span id="totalDuration">0</span> hours
                    </div>
                    
                    <div class="bg-white rounded-lg p-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-600">Base Rate (<span id="baseHoursDisplay">3</span> hours)</span>
                            <span class="font-semibold text-gray-900" id="baseRateDisplay">₱0.00</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1" id="baseRateDetails">
                            <!-- Will be populated by JS -->
                        </div>
                    </div>
                    
                    <div id="extensionRateSection" class="bg-white rounded-lg p-3" style="display: none;">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-amber-600 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-zap mr-1">
                                    <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>
                                </svg>
                                Time Extension (<span id="extensionHoursDisplay">0</span> hours)
                            </span>
                            <span class="font-semibold text-amber-600" id="extensionRateDisplay">₱0.00</span>
                        </div>
                        <div class="text-xs text-gray-500 mt-1" id="extensionRateDetails">
                            <!-- Will be populated by JS -->
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-between items-center pt-2">
                    <span class="text-base font-bold text-gray-700">Estimated Subtotal:</span>
                    <span class="text-xl font-bold text-lgu-button" id="estimatedSubtotal">₱0.00</span>
                </div>
                
                <div class="mt-3 text-xs text-gray-600 italic flex items-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info mr-1.5 mt-0.5 flex-shrink-0">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 16v-4"/>
                        <path d="M12 8h.01"/>
                    </svg>
                    <span>Note: Equipment charges and discounts will be applied in the next steps</span>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between pt-6 border-t">
                <a href="{{ route('citizen.browse-facilities') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition cursor-pointer">
                    Cancel
                </a>
                <button type="submit" id="nextStepBtn"
                        class="px-8 py-3 bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-lgu-highlight transition shadow cursor-pointer">
                    Next Step: Select Equipment
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right inline-block ml-2">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const facilitySelect = document.getElementById('facility_id');
    const facilityHiddenInput = document.querySelector('input[name="facility_id"][type="hidden"]');
    const dateInput = document.getElementById('booking_date');
    const dateDisplayInput = document.getElementById('booking_date_display');
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    const availabilityResult = document.getElementById('availabilityResult');
    const nextStepBtn = document.getElementById('nextStepBtn');

    // Calendar Modal Elements
    const calendarModal = document.getElementById('calendarModal');
    const calendarModalContent = document.getElementById('calendarModalContent');
    const closeCalendarModal = document.getElementById('closeCalendarModal');
    const cancelCalendarBtn = document.getElementById('cancelCalendarBtn');
    const clearDateBtn = document.getElementById('clearDateBtn');
    const calendarDays = document.getElementById('calendarDays');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const prevMonthBtn = document.getElementById('prevMonth');
    const nextMonthBtn = document.getElementById('nextMonth');

    let currentDate = new Date();
    let selectedDate = null;
    const minDate = new Date();
    minDate.setDate(minDate.getDate() + 7); // Minimum 7 days advance

    // Open Calendar Modal
    dateDisplayInput.addEventListener('click', function() {
        openCalendarModal();
    });

    function openCalendarModal() {
        // Set current date to the minimum date month to show available dates
        if (!selectedDate) {
            currentDate = new Date(minDate);
        }
        
        calendarModal.classList.remove('hidden');
        setTimeout(() => {
            calendarModal.classList.add('opacity-100');
            calendarModalContent.classList.remove('scale-95', 'opacity-0');
            calendarModalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
        renderCalendar();
    }

    function closeModal() {
        calendarModalContent.classList.remove('scale-100', 'opacity-100');
        calendarModalContent.classList.add('scale-95', 'opacity-0');
        calendarModal.classList.remove('opacity-100');
        setTimeout(() => {
            calendarModal.classList.add('hidden');
        }, 300);
    }

    // Close modal handlers
    closeCalendarModal.addEventListener('click', closeModal);
    cancelCalendarBtn.addEventListener('click', closeModal);
    
    calendarModal.addEventListener('click', function(e) {
        if (e.target === calendarModal) {
            closeModal();
        }
    });

    // Clear date
    clearDateBtn.addEventListener('click', function() {
        selectedDate = null;
        dateInput.value = '';
        dateDisplayInput.value = '';
        closeModal();
        checkAvailability();
    });

    // Month navigation
    prevMonthBtn.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });

    nextMonthBtn.addEventListener('click', function() {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // Render Calendar
    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        
        // Update header
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];
        currentMonthYear.textContent = `${monthNames[month]} ${year}`;

        // Calculate first day and days in month
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // Clear calendar
        calendarDays.innerHTML = '';

        // Add empty cells for days before month starts
        for (let i = 0; i < firstDay; i++) {
            const emptyCell = document.createElement('div');
            calendarDays.appendChild(emptyCell);
        }

        // Add day cells
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            date.setHours(0, 0, 0, 0);
            const dayCell = document.createElement('button');
            dayCell.type = 'button';
            dayCell.textContent = day;
            dayCell.className = 'aspect-square p-2 text-sm rounded-lg font-medium transition-all duration-200';

            const isToday = date.getTime() === today.getTime();
            const isPast = date < minDate;
            const isSelected = selectedDate && date.getTime() === new Date(selectedDate).getTime();
            const isSunday = date.getDay() === 0; // Sunday = 0
            
            // TODO: Check if date is fully booked from backend
            const isFullyBooked = false; // This will be checked via API later

            if (isSelected) {
                dayCell.className += ' bg-lgu-button text-white font-bold shadow-lg scale-105';
            } else if (isPast) {
                dayCell.className += ' bg-gray-100 text-gray-400 cursor-not-allowed';
                dayCell.disabled = true;
            } else if (isSunday) {
                dayCell.className += ' bg-gray-100 text-gray-400 cursor-not-allowed line-through';
                dayCell.disabled = true;
                dayCell.title = 'LGU Office closed on Sundays';
            } else if (isFullyBooked) {
                dayCell.className += ' bg-red-500 text-white cursor-not-allowed font-bold';
                dayCell.disabled = true;
            } else {
                // Available business days (Monday-Saturday)
                dayCell.className += ' bg-white hover:bg-lgu-highlight hover:scale-110 text-gray-700 border-2 border-lgu-button';
            }

            if (!isPast && !isSunday && !isFullyBooked) {
                dayCell.addEventListener('click', function() {
                    selectDate(date);
                });
            }

            calendarDays.appendChild(dayCell);
        }
    }

    // Select Date
    function selectDate(date) {
        selectedDate = date;
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        
        // Update inputs
        dateInput.value = `${year}-${month}-${day}`;
        
        // Format display date
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                           'July', 'August', 'September', 'October', 'November', 'December'];
        dateDisplayInput.value = `${monthNames[date.getMonth()]} ${day}, ${year}`;
        
        // Close modal with animation
        closeModal();
        
        // Check availability
        checkAvailability();
    }

    // Check availability when inputs change
    async function checkAvailability() {
        // Get facility ID from either select or hidden input
        const facilityId = facilitySelect ? facilitySelect.value : (facilityHiddenInput ? facilityHiddenInput.value : null);
        const bookingDate = dateInput.value;
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;

        if (!facilityId || !bookingDate || !startTime || !endTime) {
            availabilityResult.classList.add('hidden');
            return;
        }

        try {
            const response = await fetch('{{ route("citizen.booking.check-availability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    facility_id: facilityId,
                    booking_date: bookingDate,
                    start_time: startTime,
                    end_time: endTime
                })
            });

            const data = await response.json();
            
            availabilityResult.classList.remove('hidden');
            
            if (data.available) {
                availabilityResult.className = 'p-4 bg-green-50 border border-green-200 rounded-lg mb-6';
                availabilityResult.innerHTML = `
                    <div class="flex items-center text-green-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle mr-2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <path d="m9 11 3 3L22 4"/>
                        </svg>
                        <span class="font-medium">Facility is available for the selected date and time!</span>
                    </div>
                `;
                nextStepBtn.disabled = false;
            } else {
                // Build conflicts list HTML
                let conflictsHtml = '';
                if (data.conflicts && data.conflicts.length > 0) {
                    conflictsHtml = `<div class="mt-3">
                        <p class="text-sm font-medium text-red-700 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock mr-1.5">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            Already Booked:
                        </p>
                        <ul class="text-sm text-red-600 space-y-1">`;
                    data.conflicts.forEach(conflict => {
                        conflictsHtml += `<li class="flex items-start">
                            <span class="inline-block w-1.5 h-1.5 bg-red-500 rounded-full mr-2 mt-1.5"></span>
                            <div class="flex-1">
                                <div>${conflict.start} - ${conflict.end}</div>
                                <div class="text-xs text-red-500 mt-0.5">+ 2-hour buffer until ${conflict.buffer_end} (cleanup & inspection)</div>
                            </div>
                        </li>`;
                    });
                    conflictsHtml += '</ul></div>';
                }

                // Build available slots HTML
                let slotsHtml = '';
                if (data.available_slots && data.available_slots.length > 0) {
                    slotsHtml = `<div class="mt-4 pt-4 border-t border-red-200">
                        <p class="text-sm font-medium text-green-700 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check-circle mr-1.5">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <path d="m9 11 3 3L22 4"/>
                            </svg>
                            Available Time Slots Today:
                        </p>
                        <div class="grid grid-cols-1 gap-2">`;
                    data.available_slots.forEach((slot, index) => {
                        slotsHtml += `
                            <button type="button" 
                                    onclick="selectTimeSlot('${slot.start_24h}', '${slot.end_24h}', '${slot.start}', '${slot.end}')"
                                    class="text-left px-3 py-2 bg-green-50 hover:bg-green-100 border border-green-300 rounded-lg text-sm text-green-800 transition-colors flex items-center justify-between">
                                <span class="font-medium">${slot.start} - ${slot.end}</span>
                                <span class="text-xs text-green-600 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mouse-pointer-click mr-1">
                                        <path d="m9 9 5 12 1.774-5.226L21 14 9 9z"/>
                                        <path d="m16.071 16.071 4.243 4.243"/>
                                        <path d="m7.188 2.239.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656-2.12 2.122"/>
                                    </svg>
                                    Click to use
                                </span>
                            </button>
                        `;
                    });
                    slotsHtml += '</div></div>';
                } else {
                    slotsHtml = `<div class="mt-4 pt-4 border-t border-red-200">
                        <p class="text-sm text-gray-600 flex items-start">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info mr-1.5 mt-0.5 flex-shrink-0">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 16v-4"/>
                                <path d="M12 8h.01"/>
                            </svg>
                            <span><strong>Tip:</strong> Try a different date - this facility is fully booked today.</span>
                        </p>
                    </div>`;
                }

                availabilityResult.className = 'p-4 bg-red-50 border border-red-200 rounded-lg mb-6';
                availabilityResult.innerHTML = `
                    <div class="flex items-start text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-circle mr-2 mt-0.5 flex-shrink-0">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="m15 9-6 6"/>
                            <path d="m9 9 6 6"/>
                        </svg>
                        <div class="flex-1">
                            <span class="font-medium">Time slot not available</span>
                            ${conflictsHtml}
                            ${slotsHtml}
                        </div>
                    </div>
                `;
                nextStepBtn.disabled = true;
            }
        } catch (error) {
            console.error('Error checking availability:', error);
        }
    }

    // Function to select an available time slot (called from HTML buttons)
    window.selectTimeSlot = function(start24h, end24h, start12h, end12h) {
        // Update the time inputs
        startTimeInput.value = start24h;
        startTimeDisplayInput.value = start12h;
        endTimeInput.value = end24h;
        endTimeDisplayInput.value = end12h;
        
        // Recheck availability (should now be green)
        checkAvailability();
        
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Time Updated!',
            text: `New time: ${start12h} - ${end12h}`,
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    };

    // Add event listeners (only for elements that exist)
    if (facilitySelect) {
        facilitySelect.addEventListener('change', checkAvailability);
    }
    startTimeInput.addEventListener('change', checkAvailability);
    endTimeInput.addEventListener('change', checkAvailability);

    // ============================================
    // TIME PICKER MODAL FUNCTIONALITY
    // ============================================
    
    const timePickerModal = document.getElementById('timePickerModal');
    const timePickerModalContent = document.getElementById('timePickerModalContent');
    const closeTimePickerModal = document.getElementById('closeTimePickerModal');
    const cancelTimePickerBtn = document.getElementById('cancelTimePickerBtn');
    const confirmTimeBtn = document.getElementById('confirmTimeBtn');
    const timePickerTitle = document.getElementById('timePickerTitle');
    const currentTimeDisplay = document.getElementById('currentTimeDisplay');
    
    const startTimeDisplayInput = document.getElementById('start_time_display');
    const endTimeDisplayInput = document.getElementById('end_time_display');
    
    let currentTimeField = null; // 'start' or 'end'
    let selectedHour = '08';
    let selectedMinute = '00';
    let selectedPeriod = 'AM';
    
    // Open time picker for start time
    startTimeDisplayInput.addEventListener('click', function() {
        currentTimeField = 'start';
        const currentValue = startTimeInput.value || '08:00';
        parseAndSetTime(currentValue);
        timePickerTitle.textContent = 'Select Start Time';
        openTimePicker();
    });
    
    // Open time picker for end time
    endTimeDisplayInput.addEventListener('click', function() {
        currentTimeField = 'end';
        const currentValue = endTimeInput.value || '11:00';
        parseAndSetTime(currentValue);
        timePickerTitle.textContent = 'Select End Time';
        openTimePicker();
    });
    
    function parseAndSetTime(time24) {
        // Convert 24-hour format to 12-hour format
        const [hours, minutes] = time24.split(':');
        let hour = parseInt(hours);
        selectedMinute = minutes;
        
        if (hour >= 12) {
            selectedPeriod = 'PM';
            if (hour > 12) hour -= 12;
        } else {
            selectedPeriod = 'AM';
            if (hour === 0) hour = 12;
        }
        
        selectedHour = hour.toString().padStart(2, '0');
        updateTimeDisplay();
        highlightSelectedOptions();
    }
    
    function openTimePicker() {
        timePickerModal.classList.remove('hidden');
        setTimeout(() => {
            timePickerModal.classList.add('opacity-100');
            timePickerModalContent.classList.remove('scale-95', 'opacity-0');
            timePickerModalContent.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
    
    function closeTimePicker() {
        timePickerModalContent.classList.remove('scale-100', 'opacity-100');
        timePickerModalContent.classList.add('scale-95', 'opacity-0');
        timePickerModal.classList.remove('opacity-100');
        setTimeout(() => {
            timePickerModal.classList.add('hidden');
        }, 300);
    }
    
    function updateTimeDisplay() {
        currentTimeDisplay.textContent = `${selectedHour}:${selectedMinute} ${selectedPeriod}`;
    }
    
    function highlightSelectedOptions() {
        // Remove all previous selections
        document.querySelectorAll('.time-option').forEach(btn => {
            btn.classList.remove('bg-lgu-button', 'bg-lgu-highlight', 'text-white', 'font-bold', 'shadow-md');
            btn.classList.add('bg-white', 'text-gray-700');
        });
        
        // Highlight selected options
        const hourBtn = document.querySelector(`[data-hour="${selectedHour}"]`);
        const minuteBtn = document.querySelector(`[data-minute="${selectedMinute}"]`);
        const periodBtn = document.querySelector(`[data-period="${selectedPeriod}"]`);
        
        if (hourBtn) {
            hourBtn.classList.remove('bg-white', 'text-gray-700');
            hourBtn.classList.add('bg-lgu-button', 'text-white', 'font-bold', 'shadow-md');
        }
        if (minuteBtn) {
            minuteBtn.classList.remove('bg-white', 'text-gray-700');
            minuteBtn.classList.add('bg-lgu-button', 'text-white', 'font-bold', 'shadow-md');
        }
        if (periodBtn) {
            periodBtn.classList.remove('bg-white', 'text-gray-700');
            periodBtn.classList.add('bg-lgu-button', 'text-white', 'font-bold', 'shadow-md');
        }
    }
    
    // Handle hour selection
    document.querySelectorAll('[data-hour]').forEach(button => {
        button.addEventListener('click', function() {
            selectedHour = this.getAttribute('data-hour');
            updateTimeDisplay();
            highlightSelectedOptions();
        });
    });
    
    // Handle minute selection
    document.querySelectorAll('[data-minute]').forEach(button => {
        button.addEventListener('click', function() {
            selectedMinute = this.getAttribute('data-minute');
            updateTimeDisplay();
            highlightSelectedOptions();
        });
    });
    
    // Handle period selection
    document.querySelectorAll('[data-period]').forEach(button => {
        button.addEventListener('click', function() {
            selectedPeriod = this.getAttribute('data-period');
            updateTimeDisplay();
            highlightSelectedOptions();
        });
    });
    
    // Helper function to format time in 12-hour format
    function formatTime12Hour(hour24, minute) {
        let hour12 = hour24;
        let period = 'AM';
        
        if (hour24 >= 12) {
            period = 'PM';
            if (hour24 > 12) hour12 = hour24 - 12;
        } else if (hour24 === 0) {
            hour12 = 12;
        }
        
        return `${hour12.toString().padStart(2, '0')}:${minute} ${period}`;
    }
    
    // Show time restriction error modal
    function showTimeError(title, message) {
        try {
            console.log('showTimeError called with:', title, message);
            
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4';
            modal.style.zIndex = '9999';
            
            console.log('Modal element created');
            
            modal.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all duration-300">
                    <div class="bg-red-600 px-6 py-4 rounded-t-xl">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white mr-3">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                            <h3 class="text-lg font-bold text-white">${title}</h3>
                        </div>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 leading-relaxed">${message}</p>
                        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-800 mr-2">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                                </svg>
                                <p class="text-sm text-blue-800 font-medium">Booking Guidelines:</p>
                            </div>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• <strong>Earliest Start:</strong> 8:00 AM</li>
                                <li>• <strong>Latest End:</strong> 10:00 PM</li>
                                <li>• <strong>Minimum Duration:</strong> 3 hours</li>
                                <li>• <strong>Maintenance Gap:</strong> 2 hours between bookings</li>
                            </ul>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 rounded-b-xl flex justify-end">
                        <button class="close-error-btn px-6 py-2 bg-yellow-500 text-gray-800 rounded-lg font-semibold hover:bg-yellow-400 transition">
                            I Understand
                        </button>
                    </div>
                </div>
            `;
            
            console.log('Modal HTML set');
            
            document.body.appendChild(modal);
            console.log('Modal appended to body');
            
            // Add close button handler
            const closeBtn = modal.querySelector('.close-error-btn');
            closeBtn.addEventListener('click', function() {
                console.log('Close button clicked');
                modal.remove();
            });
            
            // Close on background click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    console.log('Background clicked');
                    modal.remove();
                }
            });
            
            console.log('Modal should be visible now!');
        } catch (error) {
            console.error('Error in showTimeError:', error);
            alert(title + '\n\n' + message);
        }
    }
    
    // Confirm time selection
    confirmTimeBtn.addEventListener('click', function() {
        console.log('Confirm button clicked!');
        console.log('Selected hour:', selectedHour);
        console.log('Selected minute:', selectedMinute);
        console.log('Selected period:', selectedPeriod);
        console.log('Current time field:', currentTimeField);
        
        // Convert to 24-hour format for the hidden input
        let hour24 = parseInt(selectedHour);
        if (selectedPeriod === 'PM' && hour24 !== 12) {
            hour24 += 12;
        } else if (selectedPeriod === 'AM' && hour24 === 12) {
            hour24 = 0;
        }
        
        console.log('Converted hour24:', hour24);
        
        const time24 = `${hour24.toString().padStart(2, '0')}:${selectedMinute}`;
        const time12 = `${selectedHour}:${selectedMinute} ${selectedPeriod}`;
        
        console.log('Time24:', time24);
        console.log('Time12:', time12);
        
        // Validate time restrictions
        if (currentTimeField === 'start') {
            console.log('Validating START time...');
            // START TIME VALIDATION: Must be >= 8:00 AM (08:00)
            if (hour24 < 8) {
                console.log('Time is before 8 AM! hour24 =', hour24);
                console.log('Calling showTimeError...');
                showTimeError(
                    'Start Time Too Early',
                    'Events cannot start before 8:00 AM. This allows setup time starting at 6:00 AM without disturbing residents in the community. Please select 8:00 AM or later.'
                );
                return;
            }
            console.log('Start time validation passed!');
            
            // Calculate end time (3 hours later)
            let endHour24 = hour24 + 3;
            let endMinute = selectedMinute;
            
            // END TIME VALIDATION: Must be <= 10:00 PM (22:00)
            if (endHour24 > 22 || (endHour24 === 22 && endMinute !== '00')) {
                showTimeError(
                    'End Time Exceeds Limit',
                    `Starting at ${time12} would end at ${formatTime12Hour(endHour24, endMinute)}, which exceeds the 10:00 PM limit. Events must end by 10:00 PM out of respect for the community. Please select an earlier start time.`
                );
                return;
            }
            
            // All validations passed - update start time
            startTimeInput.value = time24;
            startTimeDisplayInput.value = time12;
            
            // Handle day overflow (if it goes past 24 hours, cap at 22:00)
            if (endHour24 >= 24) {
                endHour24 = 22;
                endMinute = '00';
            }
            
            // Convert end time to 12-hour format for display
            let endHour12 = endHour24;
            let endPeriod = 'AM';
            
            if (endHour24 >= 12) {
                endPeriod = 'PM';
                if (endHour24 > 12) endHour12 = endHour24 - 12;
            } else if (endHour24 === 0) {
                endHour12 = 12;
            }
            
            const endTime24 = `${endHour24.toString().padStart(2, '0')}:${endMinute}`;
            const endTime12 = `${endHour12.toString().padStart(2, '0')}:${endMinute} ${endPeriod}`;
            
            endTimeInput.value = endTime24;
            endTimeDisplayInput.value = endTime12;
            
        } else if (currentTimeField === 'end') {
            // END TIME VALIDATION: Must be <= 10:00 PM (22:00)
            if (hour24 > 22 || (hour24 === 22 && parseInt(selectedMinute) > 0)) {
                showTimeError(
                    'End Time Exceeds Limit',
                    'Events must end by 10:00 PM out of respect for residents living near the facility. Please select 10:00 PM or earlier.'
                );
                return;
            }
            
            // Check if duration is ONLY 3 or 5 hours
            const startTime = startTimeInput.value;
            if (startTime) {
                const [startHour, startMin] = startTime.split(':').map(Number);
                const startMinutes = startHour * 60 + startMin;
                const endMinutes = hour24 * 60 + parseInt(selectedMinute);
                const durationHours = (endMinutes - startMinutes) / 60;
                
                // Only allow 3 hours or 5 hours
                if (durationHours !== 3 && durationHours !== 5) {
                    showTimeError(
                        'Invalid Duration',
                        `You selected ${durationHours} hours.\n\nFacility bookings must be:\n• 3 hours (standard duration), OR\n• 5 hours (3 hours + 2-hour extension)\n\nOnly ONE 2-hour extension is allowed.\n\nPlease select a valid end time.`
                    );
                    return;
                }
            }
            
            endTimeInput.value = time24;
            endTimeDisplayInput.value = time12;
        }
        
        closeTimePicker();
        checkAvailability();
        calculateRealTimePricing(); // Trigger pricing recalculation immediately
        saveFormData(); // Save updated time to localStorage and session
    });
    
    // Close modal handlers
    closeTimePickerModal.addEventListener('click', closeTimePicker);
    cancelTimePickerBtn.addEventListener('click', closeTimePicker);
    
    timePickerModal.addEventListener('click', function(e) {
        if (e.target === timePickerModal) {
            closeTimePicker();
        }
    });
    
    // Keyboard support - ESC to close modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            if (!calendarModal.classList.contains('hidden')) {
                closeModal();
            }
            if (!timePickerModal.classList.contains('hidden')) {
                closeTimePicker();
            }
        }
    });

    // Event Purpose Dropdown Handler
    const purposeTypeSelect = document.getElementById('purpose_type');
    const customPurposeWrapper = document.getElementById('custom_purpose_wrapper');
    const customPurposeInput = document.getElementById('custom_purpose');
    const purposeHiddenInput = document.getElementById('purpose');

    // Function to update the hidden purpose input
    function updatePurposeValue() {
        const selectedType = purposeTypeSelect.value;
        
        if (selectedType === 'Other') {
            // Show custom input and make it required
            customPurposeWrapper.classList.remove('hidden');
            customPurposeInput.setAttribute('required', 'required');
            // Use custom input value
            purposeHiddenInput.value = customPurposeInput.value;
        } else if (selectedType) {
            // Hide custom input and remove required
            customPurposeWrapper.classList.add('hidden');
            customPurposeInput.removeAttribute('required');
            customPurposeInput.value = ''; // Clear custom input
            // Use predefined value
            purposeHiddenInput.value = selectedType;
        } else {
            // No selection
            customPurposeWrapper.classList.add('hidden');
            customPurposeInput.removeAttribute('required');
            customPurposeInput.value = ''; // Clear custom input
            purposeHiddenInput.value = '';
        }
    }

    // Listen for changes on the dropdown
    purposeTypeSelect.addEventListener('change', updatePurposeValue);

    // Listen for input on the custom purpose field
    customPurposeInput.addEventListener('input', function() {
        if (purposeTypeSelect.value === 'Other') {
            purposeHiddenInput.value = customPurposeInput.value;
        }
    });

    // Auto-save form data to localStorage
    const formId = 'bookingStep1Form';
    const form = document.getElementById(formId);
    
    // Restore saved data on page load
    function restoreFormData() {
        const savedData = localStorage.getItem('booking_step1_data');
        if (savedData) {
            const data = JSON.parse(savedData);
            
            // Restore facility selection
            if (data.facility_id) {
                const facilitySelect = document.getElementById('facility_id');
                if (facilitySelect) facilitySelect.value = data.facility_id;
            }
            
            // Restore date
            if (data.booking_date) {
                document.getElementById('booking_date').value = data.booking_date;
                document.getElementById('booking_date_display').value = data.booking_date_display || data.booking_date;
            }
            
            // Restore times
            if (data.start_time) {
                document.getElementById('start_time').value = data.start_time;
                document.getElementById('start_time_display').value = data.start_time_display || data.start_time;
            }
            if (data.end_time) {
                document.getElementById('end_time').value = data.end_time;
                document.getElementById('end_time_display').value = data.end_time_display || data.end_time;
            }
            
            // Restore purpose and attendees
            if (data.purpose_type) {
                document.getElementById('purpose_type').value = data.purpose_type;
                // Trigger change event to show/hide custom input
                document.getElementById('purpose_type').dispatchEvent(new Event('change'));
            }
            if (data.custom_purpose) {
                document.getElementById('custom_purpose').value = data.custom_purpose;
            }
            if (data.purpose) document.getElementById('purpose').value = data.purpose;
            if (data.expected_attendees) document.getElementById('expected_attendees').value = data.expected_attendees;
        }
    }
    
    // Save form data on input
    function saveFormData() {
        const data = {
            facility_id: document.getElementById('facility_id')?.value,
            booking_date: document.getElementById('booking_date')?.value,
            booking_date_display: document.getElementById('booking_date_display')?.value,
            start_time: document.getElementById('start_time')?.value,
            start_time_display: document.getElementById('start_time_display')?.value,
            end_time: document.getElementById('end_time')?.value,
            end_time_display: document.getElementById('end_time_display')?.value,
            purpose_type: document.getElementById('purpose_type')?.value,
            custom_purpose: document.getElementById('custom_purpose')?.value,
            purpose: document.getElementById('purpose')?.value,
            expected_attendees: document.getElementById('expected_attendees')?.value
        };
        localStorage.setItem('booking_step1_data', JSON.stringify(data));
    }
    
    // Attach save listeners to all form inputs
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.addEventListener('input', saveFormData);
        input.addEventListener('change', saveFormData);
    });
    
    // Restore data on page load
    restoreFormData();
    
    // Real-time Pricing Calculation
    const pricingBreakdown = document.getElementById('pricingBreakdown');
    const expectedAttendeesInput = document.getElementById('expected_attendees');
    
    // Facility pricing data (from backend)
    const facilityPricing = {
        per_person_rate: {{ $facility->per_person_rate ?? 130 }},
        per_person_extension_rate: {{ $facility->per_person_extension_rate ?? 30 }},
        base_hours: {{ $facility->base_hours ?? 3 }},
        min_capacity: {{ $facility->min_capacity ?? 1 }},
        max_capacity: {{ $facility->capacity ?? 1000 }}
    };
    
    // Auto-enforce minimum and maximum capacity (validate on blur, not every keystroke)
    expectedAttendeesInput.addEventListener('blur', function() {
        const minCapacity = facilityPricing.min_capacity;
        const maxCapacity = facilityPricing.max_capacity;
        let value = parseInt(this.value);
        
        if (value < minCapacity && value !== '' && !isNaN(value)) {
            this.value = minCapacity;
            
            // Show minimum capacity notification
            Swal.fire({
                icon: 'warning',
                title: 'Minimum Capacity Required',
                html: `This facility requires a <strong>minimum of ${minCapacity.toLocaleString()} attendees</strong> to ensure cost-effectiveness.<br><br>The number has been adjusted automatically.`,
                confirmButtonColor: '#2C5F2D',
                timer: 4000,
                timerProgressBar: true
            });
            
            // Recalculate price with adjusted value
            calculateRealTimePricing();
        }
        
        if (value > maxCapacity) {
            this.value = maxCapacity;
            
            // Show maximum capacity notification
            Swal.fire({
                icon: 'info',
                title: 'Maximum Capacity Reached',
                text: `This facility can accommodate a maximum of ${maxCapacity.toLocaleString()} people. The number has been adjusted automatically.`,
                confirmButtonColor: '#2C5F2D',
                timer: 3000,
                timerProgressBar: true
            });
            
            // Recalculate price with adjusted value
            calculateRealTimePricing();
        }
    });
    
    function calculateRealTimePricing() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        const attendees = parseInt(expectedAttendeesInput.value) || 0;
        
        // Only show pricing if all required fields are filled
        if (!startTime || !endTime || attendees <= 0) {
            pricingBreakdown.style.display = 'none';
            return;
        }
        
        // Calculate duration in hours
        const start = new Date('2000-01-01 ' + startTime);
        const end = new Date('2000-01-01 ' + endTime);
        let totalHours = (end - start) / (1000 * 60 * 60);
        
        if (totalHours <= 0) {
            pricingBreakdown.style.display = 'none';
            return;
        }
        
        // Calculate pricing
        const baseHours = facilityPricing.base_hours;
        const extensionHours = Math.max(0, totalHours - baseHours);
        const extensionBlocks = Math.ceil(extensionHours / 2); // 2-hour blocks, rounded up
        const baseRate = facilityPricing.per_person_rate * attendees;
        const extensionRate = extensionHours > 0 ? (facilityPricing.per_person_extension_rate * attendees * extensionBlocks) : 0;
        const subtotal = baseRate + extensionRate;
        
        // Update display
        document.getElementById('totalDuration').textContent = totalHours.toFixed(1);
        document.getElementById('baseHoursDisplay').textContent = baseHours;
        document.getElementById('baseRateDisplay').textContent = '₱' + baseRate.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('baseRateDetails').innerHTML = '₱' + facilityPricing.per_person_rate.toFixed(2) + ' per person × ' + attendees.toLocaleString() + ' people';
        
        // Show/hide extension section
        const extensionSection = document.getElementById('extensionRateSection');
        if (extensionHours > 0) {
            extensionSection.style.display = 'block';
            document.getElementById('extensionHoursDisplay').textContent = extensionHours.toFixed(1);
            document.getElementById('extensionRateDisplay').textContent = '₱' + extensionRate.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('extensionRateDetails').innerHTML = '₱' + facilityPricing.per_person_extension_rate.toFixed(2) + ' per person per 2-hour block × ' + attendees.toLocaleString() + ' people × ' + extensionBlocks + (extensionBlocks === 1 ? ' block' : ' blocks');
        } else {
            extensionSection.style.display = 'none';
        }
        
        document.getElementById('estimatedSubtotal').textContent = '₱' + subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Show pricing breakdown
        pricingBreakdown.style.display = 'block';
    }
    
    // Add event listeners for real-time calculation
    if (startTimeInput && endTimeInput && expectedAttendeesInput) {
        startTimeInput.addEventListener('change', calculateRealTimePricing);
        startTimeInput.addEventListener('input', calculateRealTimePricing);
        endTimeInput.addEventListener('change', calculateRealTimePricing);
        endTimeInput.addEventListener('input', calculateRealTimePricing);
        expectedAttendeesInput.addEventListener('input', calculateRealTimePricing);
        
        // Calculate on page load if data is available
        calculateRealTimePricing();
    }
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        // Validate purpose selection
        const purposeType = purposeTypeSelect.value;
        const purposeValue = purposeHiddenInput.value;
        
        if (!purposeType) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Event Purpose Required',
                text: 'Please select an event purpose before continuing.',
                confirmButtonColor: '#2C5F2D'
            });
            purposeTypeSelect.focus();
            return false;
        }
        
        if (purposeType === 'Other' && !purposeValue.trim()) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Custom Purpose Required',
                text: 'Please describe your event purpose in the custom field.',
                confirmButtonColor: '#2C5F2D'
            });
            customPurposeInput.focus();
            return false;
        }
        
        // Update the hidden purpose value one more time before submission
        updatePurposeValue();
        
        // Don't clear immediately - wait for successful submission
        // Data will be cleared when moving to step 2
    });
});
</script>
@endpush
@endsection

