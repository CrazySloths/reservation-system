@extends('layouts.staff')

@section('title', 'Equipment List')
@section('page-title', 'Equipment List')
@section('page-subtitle', 'View available equipment and inventory')

@section('page-content')
<div class="container mx-auto px-gr-md py-gr-lg">
    {{-- Page Header --}}
    <div class="mb-gr-lg">
        <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Equipment Directory</h1>
        <p class="text-body text-lgu-paragraph">Browse available rental equipment</p>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg mb-gr-lg">
        <form method="GET" action="{{ route('staff.equipment.index') }}" class="space-y-gr-md">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-gr-md">
                {{-- Search --}}
                <div>
                    <label for="search" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Search</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" id="search" name="search" value="{{ $search }}" 
                            placeholder="Search equipment..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                    </div>
                </div>

                {{-- Category Filter --}}
                <div>
                    <label for="category" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Category</label>
                    <select id="category" name="category" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-gr-sm">
                <button type="submit" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                    Apply Filters
                </button>
                <a href="{{ route('staff.equipment.index') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i data-lucide="x" class="w-5 h-5 mr-gr-xs"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Equipment Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-lgu-headline text-white">
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Equipment</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Category</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Price/Unit</th>
                        <th class="px-gr-md py-gr-sm text-center text-small font-semibold uppercase tracking-wider">Available</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($equipment as $item)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center gap-gr-sm">
                                    @if($item->image_path)
                                        <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" 
                                            class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i data-lucide="package" class="w-6 h-6 text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-lgu-headline">{{ $item->name }}</p>
                                        @if($item->description)
                                            <p class="text-small text-gray-600">{{ Str::limit($item->description, 40) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <span class="text-body text-lgu-paragraph">{{ $categories[$item->category] ?? $item->category }}</span>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <span class="font-semibold text-lgu-headline">₱{{ number_format($item->price_per_unit, 2) }}</span>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-small font-semibold {{ $item->quantity_available > 10 ? 'bg-green-100 text-green-800' : ($item->quantity_available > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $item->quantity_available }} units
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-gr-md py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mb-gr-md"></i>
                                    <p class="text-body font-semibold text-gray-600 mb-gr-xs">No equipment found</p>
                                    <p class="text-small text-gray-500">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($equipment->hasPages())
            <div class="px-gr-md py-gr-sm border-t border-gray-200">
                {{ $equipment->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

