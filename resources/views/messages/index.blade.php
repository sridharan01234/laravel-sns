@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Message History</h1>
        <p class="mt-1 text-sm text-gray-600">Track and monitor your SMS campaign messages</p>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="card-body">
            <form id="filterForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="form-label">Search</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               placeholder="Phone number or content" 
                               class="form-input pl-10"
                               value="{{ request('search') }}">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <label class="form-label">Campaign</label>
                    <select name="campaign_id" class="form-input">
                        <option value="">All Campaigns</option>
                        @foreach($campaigns as $campaign)
                            <option value="{{ $campaign->id }}" {{ request('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                {{ $campaign->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">All Status</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn-primary w-full inline-flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        Filter Messages
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Message Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card transform hover:scale-105 transition-all duration-200">
            <div class="card-body flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-secondary-600">Sent Messages</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($stats->sent ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="card transform hover:scale-105 transition-all duration-200">
            <div class="card-body flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Messages</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($stats->pending ?? 0) }}</p>
                </div>
            </div>
        </div>

        <div class="card transform hover:scale-105 transition-all duration-200">
            <div class="card-body flex items-center">
                <div class="p-3 rounded-full bg-red-100 mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Failed Messages</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($stats->failed ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-secondary-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Recipient</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary-500 uppercase tracking-wider">Campaign</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Content</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sent At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($messages as $message)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $message->recipient_number }}</div>
                            <div class="text-sm text-gray-500">{{ $message->recipient_name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $message->campaign->name }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 max-w-xs truncate">{{ $message->content }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="badge 
                                {{ $message->status === 'sent' ? 'badge-success' : 
                                   ($message->status === 'failed' ? 'badge-error' : 'badge-warning') }}">
                                {{ ucfirst($message->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $message->sent_at ? $message->sent_at->format('M d, Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <button onclick="viewMessage({{ $message->id }})" 
                                    class="text-primary-600 hover:text-indigo-900 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                            @if($message->status === 'failed')
                            <button onclick="retryMessage({{ $message->id }})" 
                                    class="text-green-600 hover:text-green-900">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No messages found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $messages->links() }}
    </div>
</div>

<!-- View Message Modal -->
<div id="viewMessageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Message Details</h3>
            <button onclick="closeViewMessageModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div id="messageDetails" class="space-y-4">
            <!-- Message details will be loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewMessage(id) {
    fetch(`/messages/${id}`)
        .then(response => response.json())
        .then(message => {
            document.getElementById('messageDetails').innerHTML = `
                <div class="border-b pb-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <span class="badge ${getStatusBadgeClass(message.status)}">
                            ${message.status.charAt(0).toUpperCase() + message.status.slice(1)}
                        </span>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="text-sm font-medium text-gray-500">Recipient</div>
                        <div class="text-sm text-gray-900">${message.recipient_number}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-sm font-medium text-gray-500">Sent At</div>
                        <div class="text-sm text-gray-900">${message.sent_at || '-'}</div>
                    </div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 mb-2">Message Content</div>
                    <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-900">
                        ${message.content}
                    </div>
                </div>
                ${message.error ? `
                    <div>
                        <div class="text-sm font-medium text-red-500 mb-2">Error Details</div>
                        <div class="bg-red-50 rounded-lg p-4 text-sm text-red-900">
                            ${message.error}
                        </div>
                    </div>
                ` : ''}
            `;
            document.getElementById('viewMessageModal').classList.remove('hidden');
        });
}

function closeViewMessageModal() {
    document.getElementById('viewMessageModal').classList.add('hidden');
}

function getStatusBadgeClass(status) {
    switch (status) {
        case 'sent': return 'badge-success';
        case 'failed': return 'badge-error';
        default: return 'badge-warning';
    }
}

async function retryMessage(id) {
    try {
        const response = await fetch(`/messages/${id}/retry`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        if (!response.ok) throw new Error('Failed to retry message');

        window.location.reload();
    } catch (error) {
        console.error('Error retrying message:', error);
        alert('Failed to retry message. Please try again.');
    }
}
</script>
@endpush

@endsection