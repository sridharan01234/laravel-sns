@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Customers</h1>
        <div class="space-x-2">
            <button onclick="openImportModal()" 
                    class="bg-green-500 text-white px-4 py-2 rounded">
                Import Customers
            </button>
            <a href="{{ route('customers.export') }}" 
               class="bg-blue-500 text-white px-4 py-2 rounded">
                Export Customers
            </a>
            <button onclick="openCustomerModal()" 
                    class="bg-indigo-500 text-white px-4 py-2 rounded">
                Add Customer
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form id="filterForm" class="flex space-x-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       placeholder="Search by name, email or mobile" 
                       class="w-full rounded border-gray-300"
                       value="{{ request('search') }}">
            </div>
            <div class="w-48">
                <select name="group_id" 
                        class="w-full rounded border-gray-300">
                    <option value="">All Groups</option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" 
                                {{ request('group_id') == $group->id ? 'selected' : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="w-48">
                <select name="status" 
                        class="w-full rounded border-gray-300">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" 
                    class="bg-gray-500 text-white px-4 py-2 rounded">
                Filter
            </button>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" id="selectAll" class="rounded">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Name
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Mobile
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Group
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <input type="checkbox" 
                               class="customer-checkbox rounded" 
                               value="{{ $customer->id }}">
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $customer->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $customer->mobile }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $customer->email }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $customer->group->name ?? 'No Group' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $customer->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $customer->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="editCustomer({{ $customer->id }})" 
                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                            Edit
                        </button>
                        <button onclick="deleteCustomer({{ $customer->id }})" 
                                class="text-red-600 hover:text-red-900">
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        No customers found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $customers->links() }}
    </div>

    <!-- Bulk Actions -->
    <div id="bulkActions" class="fixed bottom-0 left-0 right-0 bg-white shadow-lg p-4 hidden">
        <div class="container mx-auto flex justify-between items-center">
            <span class="text-sm text-gray-600">
                <span id="selectedCount">0</span> customers selected
            </span>
            <div class="space-x-2">
                <button onclick="bulkUpdateGroup()" 
                        class="bg-blue-500 text-white px-4 py-2 rounded">
                    Update Group
                </button>
                <button onclick="bulkUpdateStatus()" 
                        class="bg-green-500 text-white px-4 py-2 rounded">
                    Update Status
                </button>
                <button onclick="bulkDelete()" 
                        class="bg-red-500 text-white px-4 py-2 rounded">
                    Delete Selected
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div id="customerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium" id="modalTitle">Add Customer</h3>
            <button onclick="closeCustomerModal()" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="customerForm" onsubmit="saveCustomer(event)">
            <input type="hidden" id="customerId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="customerName" class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Mobile</label>
                    <input type="text" id="customerMobile" class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="customerEmail" class="mt-1 block w-full rounded-md border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Group</label>
                    <select id="customerGroup" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">Select Group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="customerStatus" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-5 flex justify-end space-x-2">
                <button type="button" onclick="closeCustomerModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Import Customers</h3>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="importForm" onsubmit="importCustomers(event)">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">CSV File</label>
                    <input type="file" id="importFile" accept=".csv" 
                           class="mt-1 block w-full" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Group</label>
                    <select id="importGroup" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="">Select Group</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-5 flex justify-end space-x-2">
                <button type="button" onclick="closeImportModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let selectedCustomers = new Set();

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
        group_id: document.getElementById('customerGroup').value,
        status: document.getElementById('customerStatus').value
    };

    fetch(`/customers${id ? `/${id}` : ''}`, {
        method: id ? 'PUT' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        closeCustomerModal();
        window.location.reload();
    })
    .catch(error => alert('An error occurred'));
}

function editCustomer(id) {
    fetch(`/customers/${id}`)
        .then(response => response.json())
        .then(customer => {
            document.getElementById('modalTitle').textContent = 'Edit Customer';
            document.getElementById('customerId').value = customer.id;
            document.getElementById('customerName').value = customer.name;
            document.getElementById('customerMobile').value = customer.mobile;
            document.getElementById('customerEmail').value = customer.email;
            document.getElementById('customerGroup').value = customer.group_id || '';
            document.getElementById('customerStatus').value = customer.status;
            openCustomerModal();
        });
}

function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer?')) {
        fetch(`/customers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(() => window.location.reload());
    }
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
    formData.append('group_id', document.getElementById('importGroup').value);

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
        window.location.reload();
    })
    .catch(error => alert('An error occurred during import'));
}

// Bulk Actions
document.getElementById('selectAll').addEventListener('change', function(e) {
    document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
        checkbox.checked = e.target.checked;
        if (e.target.checked) {
            selectedCustomers.add(checkbox.value);
        } else {
            selectedCustomers.delete(checkbox.value);
        }
    });
    updateBulkActions();
});

document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function(e) {
        if (e.target.checked) {
            selectedCustomers.add(e.target.value);
        } else {
            selectedCustomers.delete(e.target.value);
        }
        updateBulkActions();
    });
});

function updateBulkActions() {
    const count = selectedCustomers.size;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkActions').classList.toggle('hidden', count === 0);
}

function bulkUpdateGroup() {
    const groupId = prompt('Enter group ID:');
    if (groupId) {
        fetch('/customers/bulk-update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ids: Array.from(selectedCustomers),
                group_id: groupId
            })
        })
        .then(() => window.location.reload());
    }
}

function bulkUpdateStatus() {
    const status = confirm('Set selected customers as active?') ? 1 : 0;
    fetch('/customers/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ids: Array.from(selectedCustomers),
            status: status
        })
    })
    .then(() => window.location.reload());
}

function bulkDelete() {
    if (confirm('Are you sure you want to delete selected customers?')) {
        fetch('/customers/bulk-delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                ids: Array.from(selectedCustomers)
            })
        })
        .then(() => window.location.reload());
    }
}
</script>
@endpush
@endsection
