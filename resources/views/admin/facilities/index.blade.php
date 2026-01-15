@extends('layouts.admin')

@section('title', 'Manage Facilities')
@section('page-title', 'Manage Facilities')
@section('page-subtitle', 'Add, edit, and manage all public facilities')

@section('page-content')
<div class="container mx-auto px-gr-md py-gr-lg">
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-gr-lg">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Manage Facilities</h1>
            <p class="text-body text-lgu-paragraph">Add, edit, and manage all public facilities</p>
        </div>
        <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
            <i data-lucide="plus" class="w-5 h-5 mr-gr-xs"></i>
            Add New Facility
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
        <div class="flex">
            <a href="{{ route('admin.facilities.index', request()->except('show_deleted')) }}" 
                style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.2s; border-bottom: 4px solid {{ !$showDeleted ? '#00473e' : 'transparent' }}; background-color: {{ !$showDeleted ? '#00473e' : '#f9fafb' }}; color: {{ !$showDeleted ? 'white' : '#4b5563' }};">
                <i data-lucide="building-2" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
                <span>Active Facilities</span>
                <span style="margin-left: 0.5rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: {{ !$showDeleted ? 'white' : '#e5e7eb' }}; color: {{ !$showDeleted ? '#00473e' : '#374151' }};">
                    {{ $activeFacilitiesCount ?? 0 }}
                </span>
            </a>
            <a href="{{ route('admin.facilities.index', array_merge(request()->except('show_deleted'), ['show_deleted' => '1'])) }}" 
                style="flex: 1; display: flex; align-items: center; justify-content: center; padding: 1rem 1.5rem; font-weight: 600; transition: all 0.2s; border-bottom: 4px solid {{ $showDeleted ? '#f59e0b' : 'transparent' }}; background-color: {{ $showDeleted ? '#f59e0b' : '#f9fafb' }}; color: {{ $showDeleted ? 'white' : '#4b5563' }};">
                <i data-lucide="archive" style="width: 20px; height: 20px; margin-right: 0.5rem;"></i>
                <span>Archived Facilities</span>
                <span style="margin-left: 0.5rem; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background-color: {{ $showDeleted ? 'white' : '#e5e7eb' }}; color: {{ $showDeleted ? '#f59e0b' : '#374151' }};">
                    {{ $archivedFacilitiesCount ?? 0 }}
                </span>
            </a>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-lg mb-gr-lg">
        <form method="GET" action="{{ route('admin.facilities.index') }}" class="space-y-gr-md">
            <input type="hidden" name="show_deleted" value="{{ $showDeleted ? '1' : '' }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gr-md">
                {{-- Search --}}
                <div>
                    <label for="search" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Search</label>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" id="search" name="search" value="{{ $search }}" 
                            placeholder="Search facilities..." 
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                    </div>
                </div>

                {{-- City Filter --}}
                <div>
                    <label for="city_id" class="block text-small font-semibold text-lgu-headline mb-gr-xs">City</label>
                    <select id="city_id" name="city_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Cities</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $cityId == $city->id ? 'selected' : '' }}>
                                {{ $city->city_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Facility Type Filter --}}
                <div>
                    <label for="facility_type" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Type</label>
                    <select id="facility_type" name="facility_type" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Types</option>
                        @foreach($facilityTypes as $key => $label)
                            <option value="{{ $key }}" {{ $facilityType == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-small font-semibold text-lgu-headline mb-gr-xs">Status</label>
                    <select id="status" name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-highlight focus:border-transparent">
                        <option value="">All Statuses</option>
                        @foreach($statusOptions as $key => $label)
                            <option value="{{ $key }}" {{ $status == $key ? 'selected' : '' }}>
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
                <a href="{{ route('admin.facilities.index', $showDeleted ? ['show_deleted' => '1'] : []) }}" class="inline-flex items-center px-gr-md py-gr-sm bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <i data-lucide="x" class="w-5 h-5 mr-gr-xs"></i>
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- Facilities Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-lgu-headline text-white">
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Facility</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">City</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Type</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Capacity</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Rate Per Person</th>
                        <th class="px-gr-md py-gr-sm text-left text-small font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-gr-md py-gr-sm text-right text-small font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($facilities as $facility)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $facility->deleted_at ? 'opacity-60' : '' }}">
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center gap-gr-sm">
                                    @if($facility->image_path)
                                        <img src="{{ Storage::url($facility->image_path) }}" alt="{{ $facility->name }}" 
                                            class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i data-lucide="building-2" class="w-6 h-6 text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-lgu-headline">{{ $facility->name }}</p>
                                        <p class="text-small text-gray-600">{{ Str::limit($facility->address, 40) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <span class="text-body text-lgu-paragraph">{{ $facility->city_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <span class="text-body text-lgu-paragraph">{{ $facility->name }}</span>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <span class="text-body text-lgu-paragraph">{{ number_format($facility->capacity) }} pax</span>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <span class="font-semibold text-lgu-headline">₱{{ number_format($facility->per_person_rate ?? 0, 2) }}</span>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                @if($facility->deleted_at)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">
                                        <i data-lucide="archive" class="w-3 h-3 mr-1"></i>
                                        Archived
                                    </span>
                                @elseif($facility->is_available)
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
                                    @if($facility->deleted_at)
                                        <button onclick="restoreFacility({{ $facility->facility_id }})" 
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                            title="Restore">
                                            <i data-lucide="rotate-ccw" class="w-5 h-5"></i>
                                        </button>
                                    @else
                                        <a href="{{ route('admin.facilities.edit', $facility->facility_id) }}" 
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                            title="Edit">
                                            <i data-lucide="edit" class="w-5 h-5"></i>
                                        </a>
                                        <button onclick="archiveFacility({{ $facility->facility_id }})" 
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
                            <td colspan="7" class="px-gr-md py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    @if($showDeleted)
                                        <i data-lucide="archive" class="w-16 h-16 text-amber-300 mb-gr-md"></i>
                                        <p class="text-body font-semibold text-gray-600 mb-gr-xs">No archived facilities</p>
                                        <p class="text-small text-gray-500">Archived facilities will appear here</p>
                                    @else
                                        <i data-lucide="inbox" class="w-16 h-16 text-gray-300 mb-gr-md"></i>
                                        <p class="text-body font-semibold text-gray-600 mb-gr-xs">No facilities found</p>
                                        <p class="text-small text-gray-500 mb-gr-md">Try adjusting your search or filters</p>
                                        <a href="{{ route('admin.facilities.create') }}" class="inline-flex items-center px-gr-md py-gr-sm bg-lgu-button text-lgu-button-text font-semibold rounded-lg hover:bg-opacity-90 transition-colors duration-200">
                                            <i data-lucide="plus" class="w-5 h-5 mr-gr-xs"></i>
                                            Add Your First Facility
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
        @if($facilities->hasPages())
            <div class="px-gr-md py-gr-sm border-t border-gray-200">
                {{ $facilities->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Delete Form (hidden) --}}
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

{{-- Restore Form (hidden) --}}
<form id="restore-form" method="POST" style="display: none;">
    @csrf
</form>

@push('scripts')
<script>
function archiveFacility(id) {
    Swal.fire({
        title: 'Archive Facility?',
        text: "This will archive the facility and hide it from active listings. You can restore it anytime from the archived facilities list.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#475d5b',
        confirmButtonText: 'Yes, Archive',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('delete-form');
            form.action = `/admin/facilities/${id}`;
            form.submit();
        }
    });
}

function restoreFacility(id) {
    Swal.fire({
        title: 'Restore Facility?',
        text: "This will restore the facility and make it available again.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#00473e',
        cancelButtonColor: '#475d5b',
        confirmButtonText: 'Yes, Restore',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('restore-form');
            form.action = `/admin/facilities/${id}/restore`;
            form.submit();
        }
    });
}

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

