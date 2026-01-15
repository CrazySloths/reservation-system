@extends('layouts.admin')

@section('page-title', 'Accept Seminar Request')
@section('page-subtitle', 'Assign facility, equipment, and budget for the program')

@section('page-content')
<div class="space-y-gr-xl">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.government-programs.preview', $seminar->seminar_id) }}" 
           class="inline-flex items-center gap-2 text-lgu-paragraph hover:text-lgu-headline transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            <span class="font-medium">Back to Preview</span>
        </a>
    </div>

    <!-- Page Header -->
    <div class="bg-lgu-headline rounded-2xl p-gr-xl shadow-lg text-white">
        <div class="flex items-start gap-4">
            <div class="bg-white/20 rounded-xl p-3 flex-shrink-0">
                <i data-lucide="check-square" class="w-8 h-8"></i>
            </div>
            <div class="flex-1">
                <h1 class="text-h2 font-bold mb-2">Accept Seminar Request</h1>
                <p class="text-white/90 text-body">{{ $seminar->seminar_title }}</p>
                <p class="text-white/80 text-small mt-1">
                    {{ \Carbon\Carbon::parse($seminar->seminar_date)->format('l, F d, Y') }} • 
                    {{ \Carbon\Carbon::parse($seminar->start_time)->format('g:i A') }} - 
                    {{ \Carbon\Carbon::parse($seminar->end_time)->format('g:i A') }}
                </p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-start">
            <i data-lucide="alert-circle" class="w-6 h-6 text-red-500 flex-shrink-0 mr-3"></i>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-red-800">Please correct the following errors:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.government-programs.accept', $seminar->seminar_id) }}" class="space-y-gr-lg">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-gr-lg">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-gr-lg">
                <!-- Facility Assignment -->
                <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                    <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                        <h2 class="text-h3 font-bold text-lgu-headline flex items-center gap-2">
                            <i data-lucide="building-2" class="w-5 h-5 text-lgu-headline"></i>
                            Facility Assignment
                        </h2>
                    </div>
                    <div class="p-gr-lg">
                        <label class="block text-small font-semibold text-lgu-headline mb-gr-sm">Select Facility *</label>
                        <select name="facility_id" required 
                                class="w-full px-gr-md py-gr-sm border border-lgu-stroke rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-headline">
                            <option value="">-- Choose a facility --</option>
                            @foreach($availableFacilities as $facility)
                            <option value="{{ $facility->facility_id }}" {{ old('facility_id') == $facility->facility_id ? 'selected' : '' }}>
                                {{ $facility->name }} ({{ $facility->capacity }} pax) - {{ $facility->address }}
                            </option>
                            @endforeach
                        </select>
                        <p class="text-caption text-lgu-paragraph mt-1">Expected attendees: {{ $attendees->count() }}</p>
                    </div>
                </div>

                <!-- Equipment Provision -->
                <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                    <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                        <h2 class="text-h3 font-bold text-lgu-headline flex items-center gap-2">
                            <i data-lucide="package" class="w-5 h-5 text-lgu-headline"></i>
                            Equipment Provision
                        </h2>
                    </div>
                    <div class="p-gr-lg">
                        <p class="text-small text-lgu-paragraph mb-gr-md">Specify any equipment that will be provided for this seminar (e.g., projector, microphones, laptops, etc.)</p>
                        <div id="equipment-container" class="space-y-gr-md">
                            <!-- Equipment templates will be added here -->
                        </div>
                        <button type="button" onclick="addEquipment()" 
                                class="mt-gr-md inline-flex items-center gap-2 px-gr-md py-gr-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add Equipment
                        </button>
                    </div>
                </div>

                <!-- Budget -->
                <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                    <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                        <h2 class="text-h3 font-bold text-lgu-headline flex items-center gap-2">
                            <i data-lucide="coins" class="w-5 h-5 text-lgu-headline"></i>
                            Budget Information
                        </h2>
                    </div>
                    <div class="p-gr-lg space-y-gr-md">
                        <div>
                            <label class="block text-small font-semibold text-lgu-headline mb-gr-sm">Total Budget (₱) *</label>
                            <input type="number" name="total_budget" id="total_budget" required step="0.01" min="0"
                                   value="{{ old('total_budget') }}"
                                   class="w-full px-gr-md py-gr-sm border border-lgu-stroke rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-headline">
                        </div>

                        <div>
                            <label class="block text-small font-semibold text-lgu-headline mb-gr-sm">Budget Breakdown</label>
                            <div id="budget-items-container" class="space-y-gr-sm">
                                <!-- Budget item templates will be added here -->
                            </div>
                            <button type="button" onclick="addBudgetItem()" 
                                    class="mt-gr-md inline-flex items-center gap-2 px-gr-md py-gr-sm bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Add Budget Item
                            </button>
                        </div>

                        <div class="bg-blue-50 rounded-lg p-gr-md border border-blue-200">
                            <div class="flex items-start gap-2">
                                <i data-lucide="calculator" class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <p class="text-small font-semibold text-blue-900">Calculated Total</p>
                                    <p class="text-h3 font-bold text-blue-700 mt-1">₱<span id="calculated-total">0.00</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="space-y-gr-lg">
                <!-- Seminar Info -->
                <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                    <div class="bg-gray-50 px-gr-lg py-gr-md border-b border-lgu-stroke">
                        <h3 class="text-h4 font-bold text-lgu-headline">Seminar Info</h3>
                    </div>
                    <div class="p-gr-md space-y-gr-sm text-small">
                        <div>
                            <p class="text-caption text-lgu-paragraph">Description</p>
                            <p class="font-medium text-lgu-headline">{{ Str::limit($seminar->description, 100) }}</p>
                        </div>
                        <div>
                            <p class="text-caption text-lgu-paragraph">Expected Attendees</p>
                            <p class="font-medium text-lgu-headline">{{ $attendees->count() }} participants</p>
                        </div>
                        <div>
                            <p class="text-caption text-lgu-paragraph">Preferred Location</p>
                            <p class="font-medium text-lgu-headline">{{ $seminar->location }}</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="bg-white rounded-xl shadow-sm border border-lgu-stroke overflow-hidden">
                    <div class="p-gr-lg space-y-gr-md">
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-gr-lg py-gr-md bg-lgu-button hover:bg-lgu-highlight text-lgu-button-text font-semibold rounded-lg transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Accept & Confirm
                        </button>
                        <a href="{{ route('admin.government-programs.preview', $seminar->seminar_id) }}" 
                           class="w-full inline-flex items-center justify-center gap-2 px-gr-lg py-gr-md bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let budgetItemCount = 0;
let equipmentCount = 0;

// Equipment inventory data from backend
const equipmentInventory = @json($equipmentInventory);

// Initialize Lucide icons
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
    
    // Add initial budget item
    addBudgetItem();
});

function addEquipment() {
    equipmentCount++;
    const container = document.getElementById('equipment-container');
    
    // Build equipment options from inventory
    let equipmentOptions = '<option value="">-- Select Equipment --</option>';
    let currentCategory = '';
    
    equipmentInventory.forEach(item => {
        if (item.category !== currentCategory) {
            if (currentCategory !== '') {
                equipmentOptions += '</optgroup>';
            }
            equipmentOptions += `<optgroup label="${item.category}">`;
            currentCategory = item.category;
        }
        equipmentOptions += `<option value="${item.equipment_name}" data-max="${item.available_quantity}">${item.equipment_name} (${item.available_quantity} available)</option>`;
    });
    if (currentCategory !== '') {
        equipmentOptions += '</optgroup>';
    }
    
    const equipmentHTML = `
        <div class="equipment-item flex items-end gap-gr-sm" data-equipment="${equipmentCount}">
            <div class="flex-1">
                <label class="block text-caption font-medium text-lgu-paragraph mb-1">Equipment Name</label>
                <select name="equipment[${equipmentCount}][name]" 
                        class="equipment-select w-full px-gr-sm py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-headline text-small"
                        onchange="updateQuantityMax(${equipmentCount})">
                    ${equipmentOptions}
                </select>
            </div>
            <div class="w-24">
                <label class="block text-caption font-medium text-lgu-paragraph mb-1">Quantity</label>
                <input type="number" name="equipment[${equipmentCount}][quantity]" min="1" value="1" max="999"
                       id="equipment-qty-${equipmentCount}"
                       class="w-full px-gr-sm py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-headline text-small">
            </div>
            <button type="button" onclick="removeEquipment(${equipmentCount})" 
                    class="p-2 text-red-600 hover:text-red-800 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', equipmentHTML);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function updateQuantityMax(equipmentId) {
    const selectElement = document.querySelector(`[data-equipment="${equipmentId}"] .equipment-select`);
    const qtyInput = document.getElementById(`equipment-qty-${equipmentId}`);
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const maxQty = selectedOption.getAttribute('data-max');
    
    if (maxQty) {
        qtyInput.setAttribute('max', maxQty);
        if (parseInt(qtyInput.value) > parseInt(maxQty)) {
            qtyInput.value = maxQty;
        }
    }
}

function removeEquipment(id) {
    const element = document.querySelector(`[data-equipment="${id}"]`);
    if (element) element.remove();
}

function addBudgetItem() {
    budgetItemCount++;
    const container = document.getElementById('budget-items-container');
    const itemHTML = `
        <div class="budget-item flex items-end gap-gr-sm" data-budget-item="${budgetItemCount}">
            <div class="flex-1">
                <label class="block text-caption font-medium text-lgu-paragraph mb-1">Item *</label>
                <input type="text" name="budget_items[${budgetItemCount}][item]" required 
                       class="w-full px-gr-sm py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-headline text-small"
                       placeholder="e.g., Catering Services">
            </div>
            <div class="w-32">
                <label class="block text-caption font-medium text-lgu-paragraph mb-1">Amount (₱) *</label>
                <input type="number" name="budget_items[${budgetItemCount}][amount]" required step="0.01" min="0"
                       class="budget-amount w-full px-gr-sm py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-lgu-headline text-small"
                       placeholder="0.00" oninput="calculateTotal()">
            </div>
            <button type="button" onclick="removeBudgetItem(${budgetItemCount})" 
                    class="p-2 text-red-600 hover:text-red-800 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHTML);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function removeBudgetItem(id) {
    const element = document.querySelector(`[data-budget-item="${id}"]`);
    if (element) {
        element.remove();
        calculateTotal();
    }
}

function calculateTotal() {
    const amounts = document.querySelectorAll('.budget-amount');
    let total = 0;
    amounts.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    document.getElementById('calculated-total').textContent = total.toFixed(2);
}
</script>
@endpush

@endsection

