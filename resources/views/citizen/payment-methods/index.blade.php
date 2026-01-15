@extends('layouts.citizen')

@section('page-title', 'Payment Methods')
@section('page-subtitle', 'Manage your saved payment methods')

@section('page-content')
<div class="pb-gr-2xl">
    <!-- Header Actions -->
    <div class="flex justify-between items-center mb-gr-lg">
        <div>
            <p class="text-body text-gray-600">Save your preferred payment methods for faster checkout</p>
        </div>
        <a href="{{ route('citizen.payment-methods.create') }}" class="btn-primary">
            <i data-lucide="plus" class="w-4 h-4 mr-gr-xs"></i>
            Add Payment Method
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 px-gr-md py-gr-sm rounded-lg mb-gr-lg">
        <i data-lucide="circle-check" class="w-4 h-4 inline mr-gr-xs"></i>
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 px-gr-md py-gr-sm rounded-lg mb-gr-lg">
        <i data-lucide="alert-circle" class="w-4 h-4 inline mr-gr-xs"></i>
        {{ session('error') }}
    </div>
    @endif

    <!-- Payment Methods Grid -->
    @if($paymentMethods->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-gr-2xl text-center">
        <i data-lucide="credit-card" class="w-16 h-16 mx-auto mb-gr-md text-gray-400"></i>
        <h3 class="text-h5 font-bold text-gray-900 mb-gr-xs">No Payment Methods Yet</h3>
        <p class="text-small text-gray-600 mb-gr-lg">Add a payment method to make reservations faster and easier</p>
        <a href="{{ route('citizen.payment-methods.create') }}" class="btn-primary inline-flex items-center">
            <i data-lucide="plus" class="w-4 h-4 mr-gr-xs"></i>
            Add Your First Payment Method
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-gr-lg">
        @foreach($paymentMethods as $method)
        <div class="bg-white rounded-xl shadow-sm border-2 {{ $method->is_default ? 'border-lgu-green' : 'border-gray-200' }} p-gr-lg relative">
            @if($method->is_default)
            <span class="absolute top-gr-sm right-gr-sm bg-lgu-green text-white text-caption font-semibold px-gr-xs py-gr-3xs rounded-full">
                Default
            </span>
            @endif

            <div class="flex items-start justify-between mb-gr-md">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center
                    @if($method->payment_type == 'gcash') bg-blue-100
                    @elseif($method->payment_type == 'paymaya') bg-green-100
                    @else bg-purple-100
                    @endif">
                    @if($method->payment_type == 'gcash')
                    <span class="text-h5 font-bold text-blue-600">G</span>
                    @elseif($method->payment_type == 'paymaya')
                    <span class="text-h5 font-bold text-green-600">P</span>
                    @else
                    <i data-lucide="building-2" class="w-6 h-6 text-purple-600"></i>
                    @endif
                </div>
            </div>

            <h3 class="text-h6 font-bold text-gray-900 mb-gr-xs">
                {{ ucfirst(str_replace('_', ' ', $method->payment_type)) }}
            </h3>
            <p class="text-small text-gray-600 mb-gr-3xs">{{ $method->account_name }}</p>
            <p class="text-small font-mono text-gray-500 mb-gr-md">{{ substr($method->account_number, 0, -4) . str_repeat('*', 4) }}</p>

            <div class="pt-gr-md border-t border-gray-200 flex items-center space-x-gr-sm">
                @if(!$method->is_default)
                <button onclick="setAsDefault({{ $method->id }})" class="flex-1 text-lgu-green hover:text-lgu-green-dark font-medium text-small">
                    Set as Default
                </button>
                @endif
                <a href="{{ route('citizen.payment-methods.edit', $method->id) }}" class="text-gray-600 hover:text-gray-900">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                </a>
                <button onclick="confirmDelete({{ $method->id }})" class="text-red-600 hover:text-red-700">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<script>
function setAsDefault(methodId) {
    fetch(`/citizen/payment-methods/${methodId}/set-default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: data.message,
                icon: 'success',
                confirmButtonColor: '#047857'
            }).then(() => {
                window.location.reload();
            });
        }
    })
    .catch(error => {
        Swal.fire({
            title: 'Error',
            text: 'Failed to update default payment method',
            icon: 'error',
            confirmButtonColor: '#047857'
        });
    });
}

function confirmDelete(methodId) {
    Swal.fire({
        title: 'Delete Payment Method?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/citizen/payment-methods/${methodId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Initialize Lucide icons
lucide.createIcons();
</script>
@endsection

