@extends('layouts.admin')

@section('page-title', 'Citizens')
@section('page-subtitle', 'Manage registered citizens and view their activity')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header --}}
    <div>
        <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Registered Citizens</h1>
        <p class="text-body text-lgu-paragraph">Total: {{ $citizens->total() }} citizens</p>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white rounded-xl shadow-md p-gr-md border-2 border-lgu-stroke">
        <form method="GET" action="{{ route('admin.citizens.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-gr-sm">
            <div>
                <label for="search" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Name, email, phone..."
                       class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0">
            </div>

            <div>
                <label for="city_id" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">City</label>
                <select name="city_id" id="city_id" class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0">
                    <option value="">All Cities</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                            {{ $city->city_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">Status</label>
                <select name="status" id="status" class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="flex items-end gap-gr-xs">
                <button type="submit" class="flex-1 btn-secondary">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Filter
                </button>
                <a href="{{ route('admin.citizens.index') }}" class="px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke hover:bg-gray-50">
                    <i data-lucide="x" class="w-5 h-5 text-lgu-paragraph"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Citizens Table --}}
    <div class="bg-white rounded-xl shadow-md border-2 border-lgu-stroke overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-lgu-green text-white">
                    <tr>
                        <th class="px-gr-md py-gr-sm text-left">Citizen</th>
                        <th class="px-gr-md py-gr-sm text-left">Contact</th>
                        <th class="px-gr-md py-gr-sm text-left">City</th>
                        <th class="px-gr-md py-gr-sm text-center">Bookings</th>
                        <th class="px-gr-md py-gr-sm text-center">Total Spent</th>
                        <th class="px-gr-md py-gr-sm text-center">Status</th>
                        <th class="px-gr-md py-gr-sm text-center">Registered</th>
                        <th class="px-gr-md py-gr-sm text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-lgu-stroke">
                    @forelse($citizens as $citizen)
                        <tr class="hover:bg-lgu-background-light">
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center gap-gr-xs">
                                    <div class="w-10 h-10 bg-lgu-highlight rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($citizen->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-lgu-headline">{{ $citizen->full_name }}</p>
                                        <p class="text-sm text-lgu-paragraph">ID: #{{ str_pad($citizen->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <p class="text-sm text-lgu-paragraph">{{ $citizen->email }}</p>
                                <p class="text-sm text-lgu-paragraph">{{ $citizen->mobile_number }}</p>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center gap-gr-xs">
                                    <span class="text-sm text-lgu-paragraph">{{ $citizen->city_name }}</span>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center">
                                <div class="text-sm">
                                    <span class="font-semibold text-lgu-green">{{ $stats[$citizen->id]['total_bookings'] }}</span>
                                    <p class="text-xs text-lgu-paragraph">({{ $stats[$citizen->id]['completed_bookings'] }} completed)</p>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center">
                                <span class="font-semibold text-lgu-headline">₱{{ number_format($stats[$citizen->id]['total_spent'], 2) }}</span>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center">
                                <span class="px-gr-sm py-gr-xs rounded-full text-xs font-semibold
                                    {{ $citizen->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($citizen->status) }}
                                </span>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center text-sm text-lgu-paragraph">
                                {{ \Carbon\Carbon::parse($citizen->created_at)->format('M d, Y') }}
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center justify-center gap-gr-xs">
                                    <a href="{{ route('admin.citizens.show', $citizen->id) }}" 
                                       class="p-gr-xs rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition"
                                       title="View Details">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>

                                    <button onclick="toggleStatus({{ $citizen->id }}, '{{ $citizen->status }}')" 
                                            class="p-gr-xs rounded-lg transition
                                                {{ $citizen->status === 'active' ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-green-500 text-white hover:bg-green-600' }}"
                                            title="{{ $citizen->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i data-lucide="{{ $citizen->status === 'active' ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-gr-md py-gr-xl text-center text-lgu-paragraph">
                                <i data-lucide="users" class="w-12 h-12 mx-auto mb-gr-sm text-gray-300"></i>
                                <p>No citizens found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($citizens->hasPages())
            <div class="px-gr-md py-gr-sm border-t-2 border-lgu-stroke">
                {{ $citizens->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleStatus(citizenId, currentStatus) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    const icon = currentStatus === 'active' ? 'warning' : 'info';
    
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Citizen Account?`,
        text: `Are you sure you want to ${action} this citizen's account?`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#2C5E3F',
        cancelButtonColor: '#6B7280',
        confirmButtonText: `Yes, ${action}!`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/citizens/${citizenId}/toggle-status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#2C5E3F'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#2C5E3F'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update status. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#2C5E3F'
                });
            });
        }
    });
}

if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

