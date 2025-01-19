@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-secondary-900">Customer Management</h1>
        <p class="mt-1 text-sm text-secondary-600">Manage and organize your customer database</p>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <button onclick="openCustomerModal()" 
                    class="btn-primary animate-scale group">
                <svg class="w-5 h-5 mr-2 transform group-hover:scale-110 transition-transform" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Customer
            </button>
            <button onclick="openImportModal()" 
                    class="btn-success btn animate-scale group">
                <svg class="w-5 h-5 mr-2 transform group-hover:scale-110 transition-transform" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Import Customers
            </button>
            <button onclick="exportCustomers()" 
                    class="btn-secondary animate-scale group">
                <svg class="w-5 h-5 mr-2 transform group-hover:scale-110 transition-transform" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4-4m0 0l-4 4m4-4v12"/>
                </svg>
                Export Customers
            </button>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">Total Customers: 
                <span class="font-semibold text-indigo-600">{{ number_format($customers->count()) }}</span>
            </span>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card mb-6 animate-fade-in">
        <div class="card-body">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="form-label">Search Customers</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               placeholder="Name, email, or phone number..." 
                               class="form-input pl-10"
                               value="{{ request('search') }}">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Group Filter -->
                <div>
                    <label class="form-label">Group</label>
                    <select name="group_id" class="form-input">
                        <option value="">All Groups</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" 
                                    {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">All Status</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full">
                        Filter Customers
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Customers Table Card -->
    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-secondary-50">
                            <th class="px-6 py-3 text-left">
                                <div class="flex items-center">
                                    <input type="checkbox" id="selectAll" 
                                           class="rounded border-gray-300 text-indigo-600 
                                                  focus:ring-indigo-500">
                                </div>
                            </th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Contact</th>
                            <th class="px-6 py-3 text-left">Group</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                       class="customer-checkbox rounded border-gray-300 
                                              text-indigo-600 focus:ring-indigo-500"
                                       value="{{ $customer->id }}">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-600 font-medium text-sm">
                                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                        <div class="text-sm text-gray-500">Added {{ $customer->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $customer->mobile }}</div>
                                <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    {{ $customer->group->name ?? 'No Group' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="badge {{ $customer->status ? 'badge-success' : 'badge-error' }}">
                                    {{ $customer->status ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium space-x-3">
                                <button onclick="editCustomer({{ $customer->id }})" 
                                        class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                    Edit
                                </button>
                                <button onclick="deleteCustomer({{ $customer->id }})" 
                                        class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No customers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>

<!-- Bulk Actions Bar -->
<div id="bulkActions" 
     class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 
            transform translate-y-full transition-transform duration-300 ease-in-out">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">
                    Selected <span id="selectedCount" class="font-semibold text-indigo-600">0</span> customers
                </span>
            </div>
            <div class="flex space-x-4">
                <button onclick="bulkUpdateGroup()" 
                        class="btn-secondary btn animate-scale">
                    Update Group
                </button>
                <button onclick="bulkUpdateStatus(true)" 
                        class="btn-success btn animate-scale">
                    Set Active
                </button>
                <button onclick="bulkUpdateStatus(false)" 
                        class="btn-warning btn animate-scale">
                    Set Inactive
                </button>
                <button onclick="confirmBulkDelete()" 
                        class="btn-danger btn animate-scale">
                    Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div id="customerModal" class="modal hidden">
    <div class="modal-content animate-slide-up">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add Customer</h3>
            <button onclick="closeCustomerModal()" 
                    class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="customerForm" onsubmit="saveCustomer(event)" class="space-y-4">
            <input type="hidden" id="customerId">
            <div class="form-group">
                <label class="form-label" for="customerName">Name</label>
                <input type="text" id="customerName" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="customerMobile">Mobile Number</label>
                <input type="text" id="customerMobile" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="customerEmail">Email</label>
                <input type="email" id="customerEmail" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label" for="customerGroup">Group</label>
                <select id="customerGroup" class="form-input">
                    <option value="">Select Group</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="customerStatus">Status</label>
                <select id="customerStatus" class="form-input">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeCustomerModal()" 
                        class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    Save Customer
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="modal hidden">
    <div class="modal-content animate-slide-up">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Import Customers</h3>
            <button onclick="closeImportModal()" 
                    class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="importForm" onsubmit="importCustomers(event)" class="space-y-4">
            <div class="form-group">
                <label class="form-label" for="importFile">CSV File</label>
                <input type="file" id="importFile" accept=".csv" 
                       class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="importGroup">Assign to Group (Optional)</label>
                <select id="importGroup" class="form-input">
                    <option value="">Select Group</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeImportModal()" 
                        class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    Import Customers
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Customer Modal Functions
function openCustomerModal() {
    document.getElementById('modalTitle').textContent = 'Add Customer';
    document.getElementById('customerModal').classList.remove('hidden');
}

function closeCustomerModal() {
    document.getElementById('customerForm').reset();
    document.getElementById('customerId').value = '';
    document.getElementById('customerModal').classList.add('hidden');
}

function saveCustomer(event) {
    event.preventDefault();
    const id = document.getElementById('customerId').value;
    const data = {
        name: document.getElementById('customerName').value,
        mobile: document.getElementById('customerMobile').value,
        email: document.getElementById('customerEmail').value,
        group_id: document.getElementById('customerGroup').value || null,
        status: document.getElementById('customerStatus').value
    };

    fetch(`/customers${id ? `/${id}` : ''}`, {
        method: id ? 'PUT' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
    if (!response.ok) {
        return response.json().then(err => Promise.reject(err));
    }
    return response.json();
})
.then(data => {
    if (data.success) {
        showSuccessMessage(data.message || 'Customer saved successfully');
        closeCustomerModal();
        window.location.reload();
    } else {
        showErrorMessage(data.message || 'Failed to save customer');
    }
})
.catch(error => {
    if (error.errors) {
        displayErrors(error.errors);
    } else if (error.message) {
        showErrorMessage(error.message);
    } else {
        showErrorMessage('An error occurred while saving the customer');
    }
});
}

function displayErrors(errors) {
    // Clear any existing error messages
    const errorContainer = document.querySelector('.error-messages');
    if (errorContainer) {
        errorContainer.innerHTML = '';
    }

    // Handle both string and object error formats
    if (typeof errors === 'string') {
        showErrorMessage(errors);
        return;
    }

    // Handle multiple errors
    Object.keys(errors).forEach(field => {
        const errorMessages = errors[field];
        if (Array.isArray(errorMessages)) {
            errorMessages.forEach(message => {
                // Try to find field-specific error container
                const fieldError = document.querySelector(`#${field}-error`);
                if (fieldError) {
                    fieldError.textContent = message;
                    fieldError.classList.remove('hidden');
                } else {
                    // Fallback to general error display
                    showErrorMessage(message);
                }
            });
        } else {
            showErrorMessage(errorMessages);
        }
    });
}

function showErrorMessage(message) {
    const modal = document.getElementById('customerModal').classList.contains('hidden') ? 
        document.getElementById('importModal') : 
        document.getElementById('customerModal');
    
    // Remove any existing error messages
    const existingError = modal.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }

    // Create and insert error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4';
    errorDiv.innerHTML = message;
    
    modal.querySelector('.modal-content').insertBefore(
        errorDiv,
        modal.querySelector('.modal-content').firstChild
    );

    // Auto remove after 5 seconds
    setTimeout(() => {
        errorDiv.remove();
    }, 5000);
}

// Import Modal Functions
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importForm').reset();
    document.getElementById('importModal').classList.add('hidden');
}

function importCustomers(event) {
    event.preventDefault();
    const formData = new FormData();
    formData.append('file', document.getElementById('importFile').files[0]);
    const groupId = document.getElementById('importGroup').value;
    if (groupId) {
        formData.append('group_id', groupId);
    }

    fetch('/customers/import', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        closeImportModal();
        alert(data.message);
        window.location.reload();
    })
    .catch(error => alert('An error occurred during import'));
}

// Bulk Actions
let selectedCustomers = new Set();

document.getElementById('selectAll').addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.customer-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = e.target.checked;
        if (e.target.checked) {
            selectedCustomers.add(checkbox.value);
        } else {
            selectedCustomers.delete(checkbox.value);
        }
    });
    updateBulkActionsBar();
});

document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
        if (e.target.checked) {
            selectedCustomers.add(e.target.value);
        } else {
            selectedCustomers.delete(e.target.value);
        }
        updateBulkActionsBar();
    });
});

function updateBulkActionsBar() {
    const bar = document.getElementById('bulkActions');
    const count = selectedCustomers.size;
    document.getElementById('selectedCount').textContent = count;
    
    if (count > 0) {
        bar.classList.remove('translate-y-full');
    } else {
        bar.classList.add('translate-y-full');
    }
}

// Utility Functions
function confirmBulkDelete() {
    if (confirm('Are you sure you want to delete the selected customers?')) {
        bulkDelete();
    }
}

async function bulkDelete() {
    try {
        const response = await fetch('/customers/bulk/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ids: Array.from(selectedCustomers)
            })
        });

        if (!response.ok) throw new Error('Failed to delete customers');

        window.location.reload();
    } catch (error) {
        alert('Failed to delete customers. Please try again.');
    }
}

async function bulkUpdateStatus(status) {
    try {
        const response = await fetch('/customers/bulk/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ids: Array.from(selectedCustomers),
                status: status ? 1 : 0
            })
        });

        if (!response.ok) throw new Error('Failed to update status');

        window.location.reload();
    } catch (error) {
        alert('Failed to update customer status. Please try again.');
    }
}

function bulkUpdateGroup() {
    const groupId = prompt('Please enter the group ID:');
    if (groupId) {
        fetch('/customers/bulk/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                ids: Array.from(selectedCustomers),
                group_id: groupId
            })
        })
        .then(response => {
            if (!response.ok) throw new Error('Failed to update group');
            window.location.reload();
        })
        .catch(error => alert('Failed to update customer group. Please try again.'));
    }
}

function exportCustomers() {
    window.location.href = '/customers/export';
}
</script>
@endpush

@endsection