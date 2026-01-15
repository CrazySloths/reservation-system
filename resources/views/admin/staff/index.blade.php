@extends('layouts.admin')

@section('page-title', 'Staff Accounts')
@section('page-subtitle', 'Manage staff members who verify booking applications')

@section('page-content')
<div class="space-y-gr-lg">
    {{-- Header with Actions --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-h1 font-bold text-lgu-headline mb-gr-xs">Staff Accounts</h1>
            <p class="text-body text-lgu-paragraph">Total: {{ $staff->total() }} staff members</p>
        </div>
        <a href="{{ route('admin.staff.create') }}" class="btn-primary">
            <i data-lucide="user-plus" class="w-5 h-5"></i>
            Add Staff Member
        </a>
    </div>

    {{-- Search and Filters --}}
    <div class="bg-white rounded-xl shadow-md p-gr-md border-2 border-lgu-stroke">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-gr-sm">
            {{-- Search --}}
            <div>
                <label for="search" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="Name, email, phone..."
                       class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0">
            </div>

            {{-- City Filter --}}
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

            {{-- Status Filter --}}
            <div>
                <label for="status" class="block text-sm font-semibold mb-gr-xs text-lgu-headline">Status</label>
                <select name="status" id="status" class="w-full px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke focus:border-lgu-green focus:ring-0">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex items-end gap-gr-xs">
                <button type="submit" class="flex-1 btn-secondary">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    Filter
                </button>
                <a href="{{ route('admin.staff.index') }}" class="px-gr-sm py-gr-xs rounded-lg border-2 border-lgu-stroke hover:bg-gray-50">
                    <i data-lucide="x" class="w-5 h-5 text-lgu-paragraph"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Staff Table --}}
    <div class="bg-white rounded-xl shadow-md border-2 border-lgu-stroke overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-lgu-green text-white">
                    <tr>
                        <th class="px-gr-md py-gr-sm text-left">Staff Member</th>
                        <th class="px-gr-md py-gr-sm text-left">Contact</th>
                        <th class="px-gr-md py-gr-sm text-left">City Assignment</th>
                        <th class="px-gr-md py-gr-sm text-center">Performance</th>
                        <th class="px-gr-md py-gr-sm text-center">Status</th>
                        <th class="px-gr-md py-gr-sm text-center">Joined</th>
                        <th class="px-gr-md py-gr-sm text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y-2 divide-lgu-stroke">
                    @forelse($staff as $member)
                        <tr class="hover:bg-lgu-background-light">
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center gap-gr-xs">
                                    <div class="w-10 h-10 bg-lgu-green rounded-full flex items-center justify-center text-white font-bold">
                                        {{ substr($member->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-lgu-headline">{{ $member->full_name }}</p>
                                        <p class="text-sm text-lgu-paragraph">ID: #{{ str_pad($member->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <p class="text-sm text-lgu-paragraph">{{ $member->email }}</p>
                                <p class="text-sm text-lgu-paragraph">{{ $member->mobile_number }}</p>
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center gap-gr-xs">
                                    <span class="text-sm text-lgu-paragraph">{{ $member->city_name }}</span>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center">
                                <div class="text-sm">
                                    <span class="font-semibold text-lgu-green">{{ $stats[$member->id]['total_verifications'] }}</span>
                                    <p class="text-xs text-lgu-paragraph">verifications</p>
                                </div>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center">
                                <span class="px-gr-sm py-gr-xs rounded-full text-xs font-semibold
                                    {{ $member->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($member->status) }}
                                </span>
                            </td>
                            <td class="px-gr-md py-gr-sm text-center text-sm text-lgu-paragraph">
                                {{ \Carbon\Carbon::parse($member->created_at)->format('M d, Y') }}
                            </td>
                            <td class="px-gr-md py-gr-sm">
                                <div class="flex items-center justify-center gap-gr-xs">
                                    {{-- Edit Button --}}
                                    <a href="{{ route('admin.staff.edit', $member->id) }}" 
                                       class="p-gr-xs rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition"
                                       title="Edit Staff">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>

                                    {{-- Toggle Status Button --}}
                                    <button onclick="toggleStatus({{ $member->id }}, '{{ $member->status }}')" 
                                            class="p-gr-xs rounded-lg transition
                                                {{ $member->status === 'active' ? 'bg-amber-500 text-white hover:bg-amber-600' : 'bg-green-500 text-white hover:bg-green-600' }}"
                                            title="{{ $member->status === 'active' ? 'Deactivate' : 'Activate' }}">
                                        <i data-lucide="{{ $member->status === 'active' ? 'user-x' : 'user-check' }}" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-gr-md py-gr-xl text-center text-lgu-paragraph">
                                <i data-lucide="users" class="w-12 h-12 mx-auto mb-gr-sm text-gray-300"></i>
                                <p>No staff members found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($staff->hasPages())
            <div class="px-gr-md py-gr-sm border-t-2 border-lgu-stroke">
                {{ $staff->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleStatus(staffId, currentStatus) {
    const action = currentStatus === 'active' ? 'deactivate' : 'activate';
    const icon = currentStatus === 'active' ? 'warning' : 'info';
    
    Swal.fire({
        title: `${action.charAt(0).toUpperCase() + action.slice(1)} Staff Member?`,
        text: `Are you sure you want to ${action} this staff member?`,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#2C5E3F',
        cancelButtonColor: '#6B7280',
        confirmButtonText: `Yes, ${action}!`,
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the toggle status request
            fetch(`/admin/staff/${staffId}/toggle-status`, {
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

// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}
</script>
@endpush
@endsection

