@extends('layouts.admin')

@section('title', 'Budget Management - Admin')

@section('page-content')
<div class="space-y-6">
    <!-- Header with Fiscal Year Selector -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-lgu-headline">Budget Management</h2>
                <p class="text-gray-600 mt-1">Manage budget allocations and expenditures for FY {{ $fiscalYear }}</p>
            </div>
            
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('admin.budget.index') }}" class="flex items-center gap-3">
                    <select name="fiscal_year" onchange="this.form.submit()" class="px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                        @foreach($fiscalYears as $year)
                            <option value="{{ $year }}" {{ $year == $fiscalYear ? 'selected' : '' }}>
                                FY {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </form>
                
                <button onclick="openCreateModal()" class="px-6 py-2 bg-lgu-headline text-white rounded-lg hover:bg-opacity-90 transition-all flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Budget Allocation
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
        <div class="flex items-center">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mr-3"></i>
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
        <div class="flex items-center">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mr-3"></i>
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Budget Allocations Table -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-lgu-headline mb-6">Budget Allocations for FY {{ $fiscalYear }}</h3>

        @if($budgetAllocations->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Category</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Allocated</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Spent</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Remaining</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Utilization</th>
                        <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($budgetAllocations as $allocation)
                    @php
                        $categoryLabels = [
                            'maintenance' => 'Facility Maintenance',
                            'equipment' => 'Equipment Purchase',
                            'operations' => 'Operational Costs',
                            'staff' => 'Staff Salaries',
                            'utilities' => 'Utility Bills',
                            'other' => 'Other Expenses'
                        ];
                    @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                        <td class="py-4 px-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $categoryLabels[$allocation->category] ?? ucfirst($allocation->category) }}</p>
                                @if($allocation->category_name)
                                <p class="text-xs text-gray-500">{{ $allocation->category_name }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right font-medium text-gray-900">
                            ₱{{ number_format($allocation->allocated_amount, 2) }}
                        </td>
                        <td class="py-4 px-4 text-right text-orange-600 font-medium">
                            ₱{{ number_format($allocation->spent_amount, 2) }}
                        </td>
                        <td class="py-4 px-4 text-right text-green-600 font-medium">
                            ₱{{ number_format($allocation->remaining_amount, 2) }}
                        </td>
                        <td class="py-4 px-4 text-center">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $allocation->status_color === 'red' ? 'bg-red-100 text-red-800' : ($allocation->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                {{ number_format($allocation->utilization_percentage, 1) }}%
                            </span>
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ $allocation->id }}, '{{ $allocation->category }}', '{{ $allocation->category_name }}', {{ $allocation->allocated_amount }}, '{{ $allocation->notes }}')" 
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded transition" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <button onclick="openExpenditureModal({{ $allocation->id }}, '{{ $categoryLabels[$allocation->category] ?? ucfirst($allocation->category) }}', {{ $allocation->remaining_amount }})" 
                                        class="p-2 text-green-600 hover:bg-green-50 rounded transition" title="Add Expenditure">
                                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                </button>
                                @if($allocation->expenditures()->count() == 0)
                                <button onclick="deleteAllocation({{ $allocation->id }})" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded transition" title="Delete">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i data-lucide="alert-circle" class="w-16 h-16 mx-auto mb-4 text-gray-400"></i>
            <p class="text-gray-600 text-lg">No budget allocations for FY {{ $fiscalYear }}</p>
            <p class="text-sm text-gray-500 mt-1">Click "Add Budget Allocation" to get started</p>
        </div>
        @endif
    </div>
</div>

<!-- Create Budget Allocation Modal -->
<div id="createModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-lgu-headline mb-4">Add Budget Allocation</h3>
        
        <form method="POST" action="{{ route('admin.budget.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fiscal Year</label>
                    <input type="number" name="fiscal_year" value="{{ $fiscalYear }}" min="2020" max="2050" required
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" required class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                        <option value="">Select Category</option>
                        <option value="maintenance">Facility Maintenance</option>
                        <option value="equipment">Equipment Purchase</option>
                        <option value="operations">Operational Costs</option>
                        <option value="staff">Staff Salaries</option>
                        <option value="utilities">Utility Bills</option>
                        <option value="other">Other Expenses</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Name (Optional)</label>
                    <input type="text" name="category_name" placeholder="e.g., Annual Equipment Upgrade"
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allocated Amount (₱)</label>
                    <input type="number" name="allocated_amount" step="0.01" min="0" required
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="3" placeholder="Budget allocation notes..."
                              class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeCreateModal()" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-lgu-headline text-white rounded-lg hover:bg-opacity-90 transition">
                    Create Allocation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Budget Allocation Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-lgu-headline mb-4">Edit Budget Allocation</h3>
        
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <input type="text" id="editCategory" readonly
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Custom Name (Optional)</label>
                    <input type="text" name="category_name" id="editCategoryName"
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Allocated Amount (₱)</label>
                    <input type="number" name="allocated_amount" id="editAllocatedAmount" step="0.01" min="0" required
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" id="editNotes" rows="3"
                              class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-lgu-headline text-white rounded-lg hover:bg-opacity-90 transition">
                    Update Allocation
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Add Expenditure Modal -->
<div id="expenditureModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-semibold text-lgu-headline mb-4">Record Expenditure</h3>
        <p class="text-sm text-gray-600 mb-4">Category: <span id="expCategory" class="font-semibold"></span></p>
        <p class="text-sm text-gray-600 mb-4">Available: <span id="expAvailable" class="font-semibold text-green-600"></span></p>
        
        <form method="POST" action="{{ route('admin.budget.expenditure.store') }}">
            @csrf
            <input type="hidden" name="budget_allocation_id" id="expBudgetId">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expenditure Type</label>
                    <select name="expenditure_type" required class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                        <option value="">Select Type</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="equipment_purchase">Equipment Purchase</option>
                        <option value="operational_cost">Operational Cost</option>
                        <option value="staff_salary">Staff Salary</option>
                        <option value="utility_bill">Utility Bill</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" required rows="2" placeholder="Brief description of expenditure..."
                              class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₱)</label>
                    <input type="number" name="amount" step="0.01" min="0" required
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expenditure Date</label>
                    <input type="date" name="expenditure_date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Number (Optional)</label>
                    <input type="text" name="invoice_number"
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vendor Name (Optional)</label>
                    <input type="text" name="vendor_name"
                           class="w-full px-4 py-2 border border-lgu-stroke rounded-lg focus:ring-2 focus:ring-lgu-highlight focus:border-lgu-highlight">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" onclick="closeExpenditureModal()" 
                        class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    Record Expenditure
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Initialize Lucide icons
if (typeof lucide !== 'undefined') {
    lucide.createIcons();
}

// Create Modal
function openCreateModal() {
    document.getElementById('createModal').classList.remove('hidden');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.add('hidden');
}

// Edit Modal
function openEditModal(id, category, categoryName, allocatedAmount, notes) {
    document.getElementById('editForm').action = `/admin/budget/${id}`;
    document.getElementById('editCategory').value = category.charAt(0).toUpperCase() + category.slice(1);
    document.getElementById('editCategoryName').value = categoryName || '';
    document.getElementById('editAllocatedAmount').value = allocatedAmount;
    document.getElementById('editNotes').value = notes || '';
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

// Expenditure Modal
function openExpenditureModal(budgetId, categoryName, remainingAmount) {
    document.getElementById('expBudgetId').value = budgetId;
    document.getElementById('expCategory').textContent = categoryName;
    document.getElementById('expAvailable').textContent = '₱' + remainingAmount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('expenditureModal').classList.remove('hidden');
}

function closeExpenditureModal() {
    document.getElementById('expenditureModal').classList.add('hidden');
}

// Delete Allocation
function deleteAllocation(id) {
    Swal.fire({
        title: 'Delete Budget Allocation?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/budget/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCreateModal();
        closeEditModal();
        closeExpenditureModal();
    }
});
</script>
@endpush
@endsection

