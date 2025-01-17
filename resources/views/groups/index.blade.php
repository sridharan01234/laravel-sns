@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Groups</h1>
        <button onclick="openGroupModal()" 
                class="bg-blue-500 text-white px-4 py-2 rounded">
            Create Group
        </button>
    </div>

    <!-- Groups Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($groups as $group)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold">{{ $group->name }}</h3>
                    <p class="text-gray-600 text-sm">{{ $group->customers_count }} customers</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="editGroup({{ $group->id }})" 
                            class="text-blue-500 hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="deleteGroup({{ $group->id }})" 
                            class="text-red-500 hover:text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
            <p class="text-gray-600 mb-4">{{ $group->description }}</p>
            <div class="border-t pt-4">
                <button onclick="openCustomerModal({{ $group->id }})" 
                        class="text-blue-500 hover:text-blue-700 text-sm">
                    Add Customers
                </button>
                <button onclick="viewCustomers({{ $group->id }})" 
                        class="text-gray-500 hover:text-gray-700 text-sm ml-4">
                    View Customers
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Group Modal -->
<div id="groupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium" id="modalTitle">Create Group</h3>
            <button onclick="closeGroupModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="groupForm" onsubmit="saveGroup(event)">
            <input type="hidden" id="groupId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="groupName" 
                           class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="groupDescription" 
                            class="mt-1 block w-full rounded-md border-gray-300" rows="3"></textarea>
                </div>
            </div>
            <div class="mt-5 flex justify-end space-x-2">
                <button type="button" onclick="closeGroupModal()" 
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

<!-- Add Customers Modal -->
<div id="customerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Add Customers to Group</h3>
            <button onclick="closeCustomerModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <input type="text" id="customerSearch" 
                   placeholder="Search customers..." 
                   class="w-full rounded-md border-gray-300">
        </div>
        <div class="max-h-[400px] overflow-y-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllCustomers" class="rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mobile
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Current Group
                        </th>
                    </tr>
                </thead>
                <tbody id="customersList" class="bg-white divide-y divide-gray-200">
                    <!-- Customers will be loaded here -->
                </tbody>
            </table>
        </div>
        <div class="mt-5 flex justify-end space-x-2">
            <button onclick="closeCustomerModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                Cancel
            </button>
            <button onclick="addCustomersToGroup()" 
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600">
                Add Selected
            </button>
        </div>
    </div>
</div>

<!-- View Customers Modal -->
<div id="viewCustomersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Group Customers</h3>
            <button onclick="closeViewCustomersModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="max-h-[400px] overflow-y-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
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
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="groupCustomersList" class="bg-white divide-y divide-gray-200">
                    <!-- Group customers will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentGroupId = null;
let selectedCustomers = new Set();

// Group Modal Functions
function openGroupModal() {
    document.getElementById('modalTitle').textContent = 'Create Group';
    document.getElementById('groupModal').classList.remove('hidden');
}

function closeGroupModal() {
    document.getElementById('groupForm').reset();
    document.getElementById('groupId').value = '';
    document.getElementById('groupModal').classList.add('hidden');
}

function saveGroup(event) {
    event.preventDefault();
    const id = document.getElementById('groupId').value;
    const data = {
        name: document.getElementById('groupName').value,
        description: document.getElementById('groupDescription').value
    };

    fetch(`/groups${id ? `/${id}` : ''}`, {
        method: id ? 'PUT' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        closeGroupModal();
        window.location.reload();
    })
    .catch(error => alert('An error occurred'));
}

function editGroup(id) {
    fetch(`/groups/${id}`)
        .then(response => response.json())
        .then(group => {
            document.getElementById('modalTitle').textContent = 'Edit Group';
            document.getElementById('groupId').value = group.id;
            document.getElementById('groupName').value = group.name;
            document.getElementById('groupDescription').value = group.description;
            openGroupModal();
        });
}

function deleteGroup(id) {
    if (confirm('Are you sure you want to delete this group?')) {
        fetch(`/groups/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(() => window.location.reload());
    }
}

// Customer Modal Functions
function openCustomerModal(groupId) {
    currentGroupId = groupId;
    document.getElementById('customerModal').classList.remove('hidden');
    loadAvailableCustomers();
}

function closeCustomerModal() {
    currentGroupId = null;
    selectedCustomers.clear();
    document.getElementById('customerModal').classList.add('hidden');
}

function loadAvailableCustomers(search = '') {
    fetch(`/api/customers?search=${search}`)
        .then(response => response.json())
        .then(data => {
            const customersList = document.getElementById('customersList');
            customersList.innerHTML = data.data.map(customer => `
                <tr>
                    <td class="px-6 py-4">
                        <input type="checkbox" value="${customer.id}" 
                               class="customer-checkbox rounded"
                               ${customer.group_id === currentGroupId ? 'checked disabled' : ''}>
                    </td>
                    <td class="px-6 py-4">${customer.name}</td>
                    <td class="px-6 py-4">${customer.mobile}</td>
                    <td class="px-6 py-4">${customer.group?.name || 'No Group'}</td>
                </tr>
            `).join('');

            // Reinitialize checkboxes
            document.querySelectorAll('.customer-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function(e) {
                    if (e.target.checked) {
                        selectedCustomers.add(e.target.value);
                    } else {
                        selectedCustomers.delete(e.target.value);
                    }
                });
            });
        });
}

function addCustomersToGroup() {
    if (selectedCustomers.size === 0) {
        alert('Please select customers to add');
        return;
    }

    fetch(`/groups/${currentGroupId}/add-customers`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            customer_ids: Array.from(selectedCustomers)
        })
    })
    .then(response => response.json())
    .then(data => {
        closeCustomerModal();
        window.location.reload();
    })
    .catch(error => alert('An error occurred'));
}

// View Customers Modal Functions
function viewCustomers(groupId) {
    currentGroupId = groupId;
    document.getElementById('viewCustomersModal').classList.remove('hidden');
    loadGroupCustomers();
}

function closeViewCustomersModal() {
    currentGroupId = null;
    document.getElementById('viewCustomersModal').classList.add('hidden');
}

function loadGroupCustomers() {
    fetch(`/groups/${currentGroupId}/customers`)
        .then(response => response.json())
        .then(data => {
            const customersList = document.getElementById('groupCustomersList');
            customersList.innerHTML = data.map(customer => `
                <tr>
                    <td class="px-6 py-4">${customer.name}</td>
                    <td class="px-6 py-4">${customer.mobile}</td>
                    <td class="px-6 py-4">${customer.email || '-'}</td>
                    <td class="px-6 py-4">
                        <button onclick="removeCustomerFromGroup(${customer.id})" 
                                class="text-red-500 hover:text-red-700">
                            Remove
                        </button>
                    </td>
                </tr>
            `).join('');
        });
}

function removeCustomerFromGroup(customerId) {
    if (confirm('Are you sure you want to remove this customer from the group?')) {
        fetch(`/groups/${currentGroupId}/remove-customer/${customerId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(() => loadGroupCustomers());
    }
}

// Search functionality
document.getElementById('customerSearch').addEventListener('input', function(e) {
    loadAvailableCustomers(e.target.value);
});

// Select all customers
document.getElementById('selectAllCustomers').addEventListener('change', function(e) {
    document.querySelectorAll('.customer-checkbox:not(:disabled)').forEach(checkbox => {
        checkbox.checked = e.target.checked;
        if (e.target.checked) {
            selectedCustomers.add(checkbox.value);
        } else {
            selectedCustomers.delete(checkbox.value);
        }
    });
});
</script>
@endpush
@endsection
