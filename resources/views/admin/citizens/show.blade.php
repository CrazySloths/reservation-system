@extends('layouts.admin')

@section('page-title', 'Citizen Details')
@section('page-subtitle', 'View citizen profile and activity history')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Back Button --}}
    <div class="flex items-center gap-gr-sm">
        <a href="{{ route('admin.citizens.index') }}" class="p-gr-xs rounded-lg hover:bg-gray-100">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline">{{ $citizen->full_name }}</h1>
            <p class="text-sm text-lgu-paragraph">Citizen ID: #{{ str_pad($citizen->id, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    {{-- Citizen Info & Statistics --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-md">
        {{-- Profile Info --}}
        <div class="bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-stroke">
            <h2 class="text-h3 font-bold text-lgu-headline mb-gr-md">Profile Information</h2>
            <div class="space-y-gr-sm">
                <div>
                    <p class="text-sm text-lgu-paragraph">Email</p>
                    <p class="font-semibold text-lgu-headline">{{ $citizen->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-lgu-paragraph">Phone</p>
                    <p class="font-semibold text-lgu-headline">{{ $citizen->mobile_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-lgu-paragraph">City</p>
                    <p class="font-semibold text-lgu-headline">{{ $citizen->city_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-lgu-paragraph">Status</p>
                    <span class="inline-block px-gr-sm py-gr-xs rounded-full text-xs font-semibold
                        {{ $citizen->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($citizen->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-lgu-paragraph">Registered</p>
                    <p class="font-semibold text-lgu-headline">{{ \Carbon\Carbon::parse($citizen->created_at)->format('F d, Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-3 gap-gr-md">
            <div class="bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-green">
                <p class="text-sm text-lgu-paragraph mb-gr-xs">Total Bookings</p>
                <p class="text-3xl font-bold text-lgu-green">{{ $stats['total_bookings'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-stroke">
                <p class="text-sm text-lgu-paragraph mb-gr-xs">Completed</p>
                <p class="text-3xl font-bold text-lgu-headline">{{ $stats['completed'] }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-stroke">
                <p class="text-sm text-lgu-paragraph mb-gr-xs">Cancelled</p>
                <p class="text-3xl font-bold text-lgu-headline">{{ $stats['cancelled'] }}</p>
            </div>
            <div class="md:col-span-3 bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-highlight">
                <p class="text-sm text-lgu-paragraph mb-gr-xs">Total Spent</p>
                <p class="text-3xl font-bold text-lgu-green">₱{{ number_format($stats['total_spent'], 2) }}</p>
            </div>
        </div>
    </div>

    {{-- Recent Bookings --}}
    <div class="bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-stroke">
        <div class="flex justify-between items-center mb-gr-md">
            <h2 class="text-h3 font-bold text-lgu-headline">Recent Bookings</h2>
            <a href="{{ route('admin.citizens.bookings', $citizen->id) }}" class="text-sm text-lgu-green hover:underline">
                View All →
            </a>
        </div>
        <div class="space-y-gr-sm">
            @forelse($recentBookings as $booking)
                <div class="flex justify-between items-center p-gr-sm bg-lgu-background-light rounded-lg">
                    <div>
                        <p class="font-semibold text-lgu-headline">{{ $booking->facility_name }}</p>
                        <p class="text-sm text-lgu-paragraph">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, Y') }}</p>
                    </div>
                    <span class="px-gr-sm py-gr-xs rounded-full text-xs font-semibold
                        @if($booking->status === 'confirmed') bg-green-100 text-green-800
                        @elseif($booking->status === 'completed') bg-blue-100 text-blue-800
                        @elseif($booking->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
            @empty
                <p class="text-center text-lgu-paragraph py-gr-md">No bookings yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Recent Reviews --}}
    <div class="bg-white rounded-xl shadow-md p-gr-lg border-2 border-lgu-stroke">
        <h2 class="text-h3 font-bold text-lgu-headline mb-gr-md">Recent Reviews</h2>
        <div class="space-y-gr-sm">
            @forelse($reviews as $review)
                <div class="p-gr-sm bg-lgu-background-light rounded-lg">
                    <div class="flex justify-between items-start mb-gr-xs">
                        <p class="font-semibold text-lgu-headline">{{ $review->facility_name }}</p>
                        <div class="flex gap-gr-xs text-yellow-500">
                            @for($i = 0; $i < $review->rating; $i++)
                                <i data-lucide="star" class="w-4 h-4 fill-current"></i>
                            @endfor
                        </div>
                    </div>
                    <p class="text-sm text-lgu-paragraph">{{ Str::limit($review->review, 100) }}</p>
                    <p class="text-xs text-lgu-paragraph mt-gr-xs">{{ \Carbon\Carbon::parse($review->created_at)->format('M d, Y') }}</p>
                </div>
            @empty
                <p class="text-center text-lgu-paragraph py-gr-md">No reviews yet.</p>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

