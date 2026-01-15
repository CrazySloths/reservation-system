@extends('layouts.citizen')

@section('title', 'Reservation History')
@section('page-title', 'Reservation History')
@section('page-subtitle', 'View past and cancelled bookings')

@section('page-content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('citizen.reservations') }}" 
           class="inline-flex items-center text-lgu-button hover:text-lgu-highlight font-medium">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                <path d="m12 19-7-7 7-7"/><path d="M19 12H5"/>
            </svg>
            Back to My Reservations
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white shadow rounded-lg p-6">
        <form method="GET" action="{{ route('citizen.reservation.history') }}">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search History</label>
            <div class="flex gap-3">
                <input type="text" name="search" id="search" value="{{ $search }}" 
                       placeholder="Facility name, reference #, or purpose..." 
                       class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-lgu-button focus:border-lgu-button">
                <button type="submit" 
                        class="px-6 py-2 bg-lgu-button text-lgu-button-text font-medium rounded-lg hover:bg-lgu-highlight transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-1">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- History List -->
    @if($bookings->isEmpty())
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <div class="mx-auto w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">No History Found</h3>
            <p class="text-gray-600">You don't have any completed or cancelled bookings{{ $search ? ' matching your search' : '' }}.</p>
        </div>
    @else
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Facility</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            @php
                                $statusConfig = [
                                    'completed' => ['color' => 'blue', 'label' => 'Completed'],
                                    'cancelled' => ['color' => 'gray', 'label' => 'Cancelled'],
                                    'rejected' => ['color' => 'red', 'label' => 'Rejected'],
                                ];
                                $status = $statusConfig[$booking->status] ?? ['color' => 'gray', 'label' => ucfirst($booking->status)];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">#{{ $booking->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->facility_name }}</div>
                                    @if($booking->city_code)
                                        <div class="text-sm text-gray-500">{{ $booking->city_code }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($booking->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('g:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-lgu-headline">₱{{ number_format($booking->total_amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $status['color'] }}-100 text-{{ $status['color'] }}-800">
                                        {{ $status['label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('citizen.reservations.show', $booking->id) }}" 
                                       class="text-lgu-button hover:text-lgu-highlight">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $bookings->links() }}
            </div>
        </div>
    @endif
</div>
@endsection

