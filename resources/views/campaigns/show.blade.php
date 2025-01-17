@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <a href="{{ route('campaigns.index') }}" 
                   class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-2xl font-bold">Campaign Details</h1>
            </div>
            @if(in_array($campaign->status, ['draft', 'scheduled']))
            <div class="flex space-x-2">
                <a href="{{ route('campaigns.edit', $campaign) }}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded">
                    Edit Campaign
                </a>
                <button onclick="executeCampaign({{ $campaign->id }})" 
                        class="bg-green-500 text-white px-4 py-2 rounded">
                    Execute Now
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Campaign Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $campaign->name }}</h2>
                        <p class="text-gray-600">{{ $campaign->description }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm 
                        @if($campaign->status === 'completed') bg-green-100 text-green-800
                        @elseif($campaign->status === 'failed') bg-red-100 text-red-800
                        @elseif($campaign->status === 'running') bg-blue-100 text-blue-800
                        @elseif($campaign->status === 'scheduled') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($campaign->status) }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Created:</span>
                        <span class="font-medium">{{ $campaign->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Scheduled:</span>
                        <span class="font-medium">
                            {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y H:i') : 'Not scheduled' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">Completed:</span>
                        <span class="font-medium">
                            {{ $campaign->completed_at ? $campaign->completed_at->format('M d, Y H:i') : 'Not completed' }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-500">Created By:</span>
                        <span class="font-medium">{{ $campaign->user->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Message Template -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Message Template</h3>
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-500">Template Name:</span>
                        <span class="font-medium">{{ $campaign->template->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Content:</span>
                        <p class="mt-1 p-3 bg-gray-50 rounded">{{ $campaign->template->content }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">Variables:</span>
                        <div class="mt-1 flex flex-wrap gap-2">
                            @if($campaign->template->variables)
                            @foreach($campaign->template->variables as $variable)
                            <span class="px-2 py-1 bg-gray-100 rounded text-sm">{{ $variable }}</span>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Message Stats -->
            @if($campaign->messages->isNotEmpty())
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Message Statistics</h3>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-green-50 p-4 rounded">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $campaign->messages->where('status', 'sent')->count() }}
                            </div>
                            <div class="text-sm text-gray-500">Delivered</div>
                        </div>
                    </div>
                    <div class="bg-red-50 p-4 rounded">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">
                                {{ $campaign->messages->where('status', 'failed')->count() }}
                            </div>
                            <div class="text-sm text-gray-500">Failed</div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                {{ $campaign->messages->where('status', 'pending')->count() }}
                            </div>
                            <div class="text-sm text-gray-500">Pending</div>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sent At
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($campaign->messages->take(5) as $message)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $message->customer->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $message->customer->mobile }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($message->status === 'sent') bg-green-100 text-green-800
                                        @elseif($message->status === 'failed') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($message->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $message->sent_at ? $message->sent_at->format('M d, Y H:i') : '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Target Groups -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Target Groups</h3>
                <div class="space-y-4">
                    @foreach($campaign->groups as $group)
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <div>
                            <div class="font-medium">{{ $group->name }}</div>
                            <div class="text-sm text-gray-500">{{ $group->customers_count }} customers</div>
                        </div>
                        <button onclick="viewGroupCustomers({{ $group->id }})" 
                                class="text-blue-500 hover:text-blue-700">
                            View
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Campaign Progress -->
            @if(in_array($campaign->status, ['running', 'completed']))
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Campaign Progress</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>Progress</span>
                            <span>{{ $campaign->progress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-blue-600 h-2.5 rounded-full" 
                                 style="width: {{ $campaign->progress }}%"></div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        {{ $campaign->messages->count() }} messages processed
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Group Customers Modal -->
<div id="groupCustomersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium" id="groupModalTitle">Group Customers</h3>
            <button onclick="closeGroupCustomersModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="overflow-x-auto">
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
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody id="groupCustomersList" class="bg-white divide-y divide-gray-200">
                    <!-- Customers will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
function executeCampaign(id) {
    if (!confirm('Are you sure you want to execute this campaign now?')) return;

    fetch(`/campaigns/${id}/execute`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        window.location.reload();
    })
    .catch(error => alert('An error occurred'));
}

function viewGroupCustomers(groupId) {
    fetch(`/groups/${groupId}/customers`)
        .then(response => response.json())
        .then(customers => {
            const tbody = document.getElementById('groupCustomersList');
            tbody.innerHTML = customers.map(customer => `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">${customer.name}</div>
                        <div class="text-sm text-gray-500">${customer.email || '-'}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        ${customer.mobile}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                            ${customer.status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                            ${customer.status ? 'Active' : 'Inactive'}
                        </span>
                    </td>
                </tr>
            `).join('');
            document.getElementById('groupCustomersModal').classList.remove('hidden');
        });
}

function closeGroupCustomersModal() {
    document.getElementById('groupCustomersModal').classList.add('hidden');
}
</script>
@endpush
@endsection
