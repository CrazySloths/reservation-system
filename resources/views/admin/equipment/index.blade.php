@extends('layouts.admin')

@section('title', 'Equipment Inventory')
@section('page-title', 'Equipment Inventory')
@section('page-subtitle', 'Manage equipment items and availability')

@section('page-content')
<div class="container mx-auto px-gr-md py-gr-lg">
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-gr-lg">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Equipment Inventory</h1>
            <p class="text-body text-lgu-paragraph">Manage rental equipment for facilities</p>
        </div>
        <a href="{{ route('admin.equipment.create') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
            <i data-lucide="plus" class="w-5 h-5 mr-gr-xs"></i>
            Add Equipment
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div id="success-message" class="bg-green-50 border-2 border-green-200 rounded-lg p-gr-md mb-gr-lg flex items-start gap-gr-sm">
            <i data-lucide="check-circle" class="w-6 h-6 text-green-600 flex-shrink-0 mt-1"></i>
            <div class="flex-1">
                <p class="text-body font-semibold text-green-900">{{ session('success') }}</p>
            </div>
            <button onclick="document.getElementById('success-message').remove()" class="text-green-600 hover:text-green-800">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div id="error-message" class="bg-red-50 border-2 border-red-200 rounded-lg p-gr-md mb-gr-lg flex items-start gap-gr-sm">
            <i data-lucide="alert-circle" class="w-6 h-6 text-red-600 flex-shrink-0 mt-1"></i>
            <div class="flex-1">
                <p class="text-body font-semibold text-red-900">{{ session('error') }}</p>
            </div>
            <button onclick="document.getElementById('error-message').remove()" class="text-red-600 hover:text-red-800">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    @endif

    {{-- Tabs for Active/Archived --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-gr-lg">
        <div style="display: flex;">
            <a href="{{ route('admin.equipment.index', request()->except('show_deleted')) }}" 
                style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.2s; border-bottom: 4px solid {{ !$showDeleted ? '#00473e' : 'transparent' }}; background-color: {{ !$showDeleted ? '#00473e' : '#f9fafb' }}; color: {{ !$showDeleted ? 'white' : '#4b5563' }};">
                <i data-lucide="package" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
                <span>Active Equipment</span>
                <span style="margin-left: 0.5rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: {{ !$showDeleted ? 'white' : '#e5e7eb' }}; color: {{ !$showDeleted ? '#00473e' : '#374151' }};">
                    {{ $activeEquipmentCount ?? 0 }}
                </span>
            </a>
            <a href="{{ route('admin.equipment.index', array_merge(request()->except('show_deleted'), ['show_deleted' => '1'])) }}" 
                style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.2s; border-bottom: 4px solid {{ $showDeleted ? '#f59e0b' : 'transparent' }}; background-color: {{ $showDeleted ? '#f59e0b' : '#f9fafb' }}; color: {{ $showDeleted ? 'white' : '#4b5563' }};">
                <i data-lucide="archive" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
                <span>Archived Equipment</span>
                <span style="margin-left: 0.5rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: {{ $showDeleted ? 'white' : '#e5e7eb' }}; color: {{ $showDeleted ? '#f59e0b' : '#374151' }};">
                    {{ $archivedEquipmentCount ?? 0 }}
                </span>
            </a>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg mb-gr-lg">
        <form method="GET" action="{{ route('admin.equipment.index') }}" class="space-y-gr-md">
            <input type="hidden" name="show_deleted" value="{{ $showDeleted ? '1' : '' }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-gr-md">
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

                {{-- Availability Filter --}}
                <div>
                    <label for="availability" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Availability</label>
                    <select id="availability" name="availability" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All</option>
                        <option value="1" {{ $availability === '1' ? 'selected' : '' }}>Available</option>
                        <option value="0" {{ $availability === '0' ? 'selected' : '' }}>Unavailable</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-gr-sm">
                <button type="submit" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                    <i data-lucide="filter" class="w-5 h-5 mr-gr-xs"></i>
                    Apply Filters
                </button>
                <a href="{{ route('admin.equipment.index', $showDeleted ? ['show_deleted' => '1'] : []) }}" class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
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
                        <th class="px-gr-md py-gr-sm text-center text-small font-semibold uppercase tracking-wider">Quantity</th>
                        <th class="px-gr-md py-gr-sm text-center text-small font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-gr-md py-gr-sm text-right text-small font-semibold uppercase tracking-wider">Actions</th>
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
                            <td class="px-gr-md py-gr-sm text-center">
                                @if($item->deleted_at)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <i data-lucide="archive" class="w-3 h-3 mr-1"></i>
                                        Archived
                                    </span>
                                @elseif($item->is_available)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i>
                                        Available
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                        Unavailable
                                    </span>
                                @endif
                            </td>
                            <td class="px-gr-md py-gr-sm text-right">
                                <div class="flex items-center justify-end gap-gr-xs">
                                    @if($item->deleted_at)
                                        <button onclick="restoreEquipment({{ $item->id }})" 
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                            title="Restore">
                                            <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                                        </button>
                                    @else
                                        <button onclick="toggleAvailability({{ $item->id }})" 
                                            class="p-2 {{ $item->is_available ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition-colors duration-200"
                                            title="{{ $item->is_available ? 'Disable' : 'Enable' }}">
                                            <i data-lucide="{{ $item->is_available ? 'eye-off' : 'eye' }}" class="w-5 h-5"></i>
                                        </button>
                                        <a href="{{ route('admin.equipment.edit', $item->id) }}" 
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                            title="Edit">
                                            <i data-lucide="edit" class="w-5 h-5"></i>
                                        </a>
                                        <button onclick="archiveEquipment({{ $item->id }})" 
                                            class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors duration-200"
                                            title="Archive">
                                            <i data-lucide="archive" class="w-5 h-5"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-gr-md py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    @if($showDeleted)
                                        <i data-lucide="archive" class="w-16 h-16 text-amber-300 mb-gr-md"></i>
                                        <p class="text-body font-semibold text-gray-600 mb-gr-xs">No archived equipment</p>
                                        <p class="text-small text-gray-500">Archived equipment will appear here</p>
                                    @else
                                        <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mb-gr-md"></i>
                                        <p class="text-body font-semibold text-gray-600 mb-gr-xs">No equipment found</p>
                                        <p class="text-small text-gray-500 mb-gr-md">Start by adding your first equipment item</p>
                                        <a href="{{ route('admin.equipment.create') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                                            <i data-lucide="plus" class="w-5 h-5 mr-gr-xs"></i>
                                            Add Equipment
                                        </a>
                                    @endif
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

{{-- Delete Form (hidden) --}}
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

{{-- Toggle Form (hidden) --}}
<form id="toggle-form" method="POST" style="display: none;">
    @csrf
</form>

{{-- Restore Form (hidden) --}}
<form id="restore-form" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
function archiveEquipment(id) {
    Swal.fire({
        title: 'Archive Equipment?',
        text: "This will archive the equipment and hide it from active listings. You can restore it anytime from the archived equipment list.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#475d5b',
        confirmButtonText: 'Yes, Archive',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/equipment/${id}`;
            form.submit();
        }
    });
}

function restoreEquipment(id) {
    Swal.fire({
        title: 'Restore Equipment?',
        text: "This will restore the equipment and make it available again.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#00473e',
        cancelButtonColor: '#475d5b',
        confirmButtonText: 'Yes, Restore',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('restore-form');
            form.action = `/admin/equipment/${id}/restore`;
            form.submit();
        }
    });
}

function toggleAvailability(id) {
    const form = document.getElementById('toggle-form');
    form.action = `/admin/equipment/${id}/toggle`;
    form.submit();
}

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

