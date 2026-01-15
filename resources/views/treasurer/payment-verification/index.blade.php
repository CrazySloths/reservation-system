@extends('layouts.treasurer')

@section('title', 'Payment Verification')
@section('page-title', 'Payment Verification')
@section('page-subtitle', 'Verify Cash Payments at CTO')

@section('page-content')

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="mb-gr-md bg-green-50 border-l-4 border-green-500 p-gr-sm rounded-lg shadow-sm">
        <div class="flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-gr-xs flex-shrink-0"></i>
            <p class="text-body font-semibold text-green-800">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-gr-md bg-red-50 border-l-4 border-red-500 p-gr-sm rounded-lg shadow-sm">
        <div class="flex items-center">
            <i data-lucide="x-circle" class="w-5 h-5 text-red-600 mr-gr-xs flex-shrink-0"></i>
            <p class="text-body font-semibold text-red-800">{{ session('error') }}</p>
        </div>
    </div>
@endif

<!-- Filters and Search -->
<div class="bg-white rounded-xl shadow-md p-gr-md mb-gr-md">
    <form method="GET" action="{{ route('treasurer.payment-verification') }}" class="space-y-gr-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-sm">
            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-caption font-semibold text-gray-700 mb-gr-xs">Status</label>
                <select name="status" id="status" class="w-full px-gr-sm py-gr-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent text-body transition-all">
                    <option value="unpaid" {{ request('status', 'unpaid') === 'unpaid' ? 'selected' : '' }}>Unpaid (Pending)</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid (Verified)</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>
            
            <!-- Search -->
            <div class="md:col-span-2">
                <label for="search" class="block text-caption font-semibold text-gray-700 mb-gr-xs">Search</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           id="search" 
                           value="{{ request('search') }}"
                           placeholder="Search by slip number, name, or email..."
                           class="w-full px-gr-sm py-gr-xs pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-lgu-button focus:border-transparent text-body transition-all">
                    <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-gr-sm top-1/2 transform -translate-y-1/2"></i>
                </div>
            </div>
        </div>
        
        <div class="flex gap-gr-xs pt-gr-xs">
            <button type="submit" class="px-gr-md py-gr-xs bg-lgu-button hover:bg-lgu-highlight text-white font-semibold rounded-lg transition-all shadow-sm hover:shadow-md text-body">
                Apply Filters
            </button>
            <a href="{{ route('treasurer.payment-verification') }}" class="px-gr-md py-gr-xs bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-all text-body">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Payment Slips Table -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="px-gr-md py-gr-sm border-b border-gray-200 flex items-center justify-between">
        <div>
            <h3 class="text-h3 font-bold text-gray-900">Payment Slips</h3>
            <p class="text-small text-gray-600 mt-gr-xs">{{ $paymentSlips->total() }} total payment slip(s)</p>
        </div>
        <div class="flex items-center gap-gr-xs">
            @php
                $statusCounts = [
                    'unpaid' => DB::connection('facilities_db')->table('payment_slips')->where('status', 'unpaid')->count(),
                    'paid' => DB::connection('facilities_db')->table('payment_slips')->where('status', 'paid')->count(),
                ];
            @endphp
            <span class="px-gr-sm py-gr-xs bg-orange-100 text-orange-800 text-caption font-bold rounded-full">
                {{ $statusCounts['unpaid'] }} Pending
            </span>
            <span class="px-gr-sm py-gr-xs bg-green-100 text-green-800 text-caption font-bold rounded-full">
                {{ $statusCounts['paid'] }} Verified
            </span>
        </div>
    </div>
    
    @if($paymentSlips->count() > 0)
        <table class="w-full table-fixed divide-y divide-gray-200">
                <colgroup>
                    <col class="w-[13%]"><!-- Slip # -->
                    <col class="w-[22%]"><!-- Citizen -->
                    <col class="w-[13%]"><!-- Facility -->
                    <col class="w-[12%]"><!-- Amount -->
                    <col class="w-[14%]"><!-- Deadline -->
                    <col class="w-[13%]"><!-- Status -->
                    <col class="w-[13%]"><!-- Actions -->
                </colgroup>
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-gr-sm py-gr-sm text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Slip #</th>
                        <th class="px-gr-sm py-gr-sm text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Citizen</th>
                        <th class="px-gr-sm py-gr-sm text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Facility</th>
                        <th class="px-gr-sm py-gr-sm text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                        <th class="px-gr-sm py-gr-sm text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Deadline</th>
                        <th class="px-gr-sm py-gr-sm text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-gr-sm py-gr-sm text-left text-caption font-bold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($paymentSlips as $slip)
                        @php
                            $deadline = \Carbon\Carbon::parse($slip->payment_deadline);
                            $isOverdue = $slip->status === 'unpaid' && $deadline->isPast();
                            $isUrgent = $slip->status === 'unpaid' && $deadline->diffInHours(now(), false) <= 24 && !$isOverdue;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer {{ $isOverdue ? 'bg-red-50/50' : '' }}" onclick="window.location='{{ route('treasurer.payment-slips.show', $slip->id) }}'">
                            <td class="px-gr-sm py-gr-sm whitespace-nowrap">
                                <span class="text-body font-bold text-lgu-button">{{ $slip->slip_number }}</span>
                            </td>
                            <td class="px-gr-sm py-gr-sm">
                                <div class="text-body font-semibold text-gray-900 truncate">{{ $slip->applicant_name }}</div>
                                <div class="text-small text-gray-500 mt-gr-xs truncate">{{ $slip->applicant_email }}</div>
                            </td>
                            <td class="px-gr-sm py-gr-sm">
                                <span class="text-body text-gray-700 block truncate">{{ $slip->facility_name }}</span>
                            </td>
                            <td class="px-gr-sm py-gr-sm whitespace-nowrap">
                                <span class="text-body font-bold text-gray-900">₱{{ number_format($slip->amount_due, 2) }}</span>
                            </td>
                            <td class="px-gr-sm py-gr-sm whitespace-nowrap">
                                <div class="text-body font-medium text-gray-900">{{ $deadline->format('M d, Y') }}</div>
                                <div class="text-caption font-semibold mt-gr-xs {{ $isOverdue ? 'text-red-600' : ($isUrgent ? 'text-orange-600' : 'text-gray-500') }}">
                                    {{ $isOverdue ? 'OVERDUE' : $deadline->diffForHumans() }}
                                </div>
                            </td>
                            <td class="px-gr-sm py-gr-sm whitespace-nowrap">
                                @if($slip->status === 'paid')
                                    <span class="inline-flex items-center px-gr-sm py-gr-xs rounded-full text-caption font-bold bg-green-100 text-green-800">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-gr-xs"></i>
                                        Verified
                                    </span>
                                @elseif($slip->status === 'expired')
                                    <span class="inline-flex items-center px-gr-sm py-gr-xs rounded-full text-caption font-bold bg-red-100 text-red-800">
                                        <i data-lucide="x-circle" class="w-3 h-3 mr-gr-xs"></i>
                                        Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-gr-sm py-gr-xs rounded-full text-caption font-bold {{ $isUrgent ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        <i data-lucide="clock" class="w-3 h-3 mr-gr-xs"></i>
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-gr-sm py-gr-sm whitespace-nowrap">
                                <a href="{{ route('treasurer.payment-slips.show', $slip->id) }}" class="inline-flex items-center text-body font-semibold text-lgu-button hover:text-lgu-highlight transition-colors" onclick="event.stopPropagation()">
                                    {{ $slip->status === 'unpaid' ? 'Verify' : 'View' }}
                                    <i data-lucide="arrow-right" class="w-4 h-4 ml-gr-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        
        <!-- Pagination -->
        <div class="px-gr-md py-gr-sm border-t border-gray-200">
            {{ $paymentSlips->links() }}
        </div>
    @else
        <div class="px-gr-md py-gr-xl text-center">
            <i data-lucide="inbox" class="w-16 h-16 mx-auto text-gray-300 mb-gr-sm"></i>
            <p class="text-body font-semibold text-gray-600 mb-gr-xs">No payment slips found</p>
            <p class="text-small text-gray-400">Try adjusting your filters</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush

@endsection

