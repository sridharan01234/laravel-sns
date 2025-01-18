@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Campaigns</h1>
        <button onclick="openCampaignModal()" 
                class="bg-blue-500 text-white px-4 py-2 rounded">
            Create Campaign
        </button>
    </div>

    <!-- Campaign List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($campaigns as $campaign)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold">{{ $campaign->name }}</h3>
                    <span class="px-2 py-1 rounded-full text-xs 
                        @if($campaign->status === 'completed') bg-green-100 text-green-800
                        @elseif($campaign->status === 'failed') bg-red-100 text-red-800
                        @elseif($campaign->status === 'running') bg-blue-100 text-blue-800
                        @elseif($campaign->status === 'scheduled') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($campaign->status) }}
                    </span>
                </div>
                <div class="flex space-x-2">
    @if(in_array($campaign->status, ['draft', 'scheduled']))
        <button onclick="editCampaign({{ $campaign->id }})" 
                class="text-blue-500 hover:text-blue-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </button>
    @endif
    <a href="{{ route('campaigns.show', ['campaign' => $campaign->id]) }}" 
            class="text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
    </a>
    <!-- Add Duplicate Button -->
    <button onclick="duplicateCampaign({{ $campaign->id }})"
            class="text-gray-500 hover:text-gray-700"
            title="Duplicate Campaign">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
    </button>
    <!-- Add Resend Button for completed campaigns -->
    @if($campaign->status === 'completed')
    <button onclick="resendCampaign({{ $campaign->id }})"
            class="text-gray-500 hover:text-gray-700"
            title="Resend Campaign">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
    </button>
    @endif
</div>

            </div>
            <p class="text-gray-600 text-sm mb-4">{{ $campaign->description }}</p>
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Template:</span>
                    <span class="font-medium">{{ $campaign->template?->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Groups:</span>
                    <span class="font-medium">{{ $campaign->groups->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Scheduled:</span>
                    <span class="font-medium">
                        {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y H:i') : 'Not scheduled' }}
                    </span>
                </div>
            </div>
            @if(in_array($campaign->status, ['draft', 'scheduled']))
            <div class="mt-4 pt-4 border-t">
                <button onclick="executeCampaign({{ $campaign->id }})" 
                        class="w-full bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Execute Now
                </button>
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

<!-- Campaign Modal -->
<div id="campaignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium" id="modalTitle">Create Campaign</h3>
            <button onclick="closeCampaignModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="campaignForm" onsubmit="saveCampaign(event)">
            <input type="hidden" id="campaignId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Campaign Name</label>
                    <input type="text" id="campaignName" 
                           class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="campaignDescription" 
                            class="mt-1 block w-full rounded-md border-gray-300" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Template</label>
                    <select id="templateId" class="mt-1 block w-full rounded-md border-gray-300" required>
                        <option value="">Select Template</option>
                        @foreach($templates as $template)
                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Groups</label>
                    <select id="groupIds" class="mt-1 block w-full rounded-md border-gray-300" multiple required>
                        @foreach($groups as $group)
                        <option value="{{ $group->id }}">{{ $group->name }} ({{ $group->customers_count }} customers)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Schedule (Optional)</label>
                    <input type="datetime-local" id="scheduledAt" 
                           class="mt-1 block w-full rounded-md border-gray-300">
                </div>
            </div>
            <div class="mt-5 flex justify-end space-x-2">
                <button type="button" onclick="closeCampaignModal()" 
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

<!-- View Campaign Modal -->
<div id="viewCampaignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Campaign Details</h3>
            <button onclick="closeViewCampaignModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div id="campaignDetails" class="space-y-6">
            <!-- Campaign details will be loaded here -->
        </div>
    </div>
</div>

@push('scripts')
<script>
// Campaign Modal Functions
function openCampaignModal() {
    document.getElementById('modalTitle').textContent = 'Create Campaign';
    document.getElementById('campaignModal').classList.remove('hidden');
}

function closeCampaignModal() {
    document.getElementById('campaignForm').reset();
    document.getElementById('campaignId').value = '';
    document.getElementById('campaignModal').classList.add('hidden');
}

function saveCampaign(event) {
    event.preventDefault();
    const id = document.getElementById('campaignId').value;
    const data = {
        name: document.getElementById('campaignName').value,
        description: document.getElementById('campaignDescription').value,
        template_id: document.getElementById('templateId').value,
        group_ids: Array.from(document.getElementById('groupIds').selectedOptions).map(option => option.value),
        scheduled_at: document.getElementById('scheduledAt').value || null
    };

    fetch(`/campaigns${id ? `/${id}` : ''}`, {
        method: id ? 'PUT' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        closeCampaignModal();
        window.location.reload();
    })
    .catch(error => alert('An error occurred'));
}

function editCampaign(id) {
    fetch(`/campaigns/${id}`)
        .then(response => response.json())
        .then(campaign => {
            document.getElementById('modalTitle').textContent = 'Edit Campaign';
            document.getElementById('campaignId').value = campaign.id;
            document.getElementById('campaignName').value = campaign.name;
            document.getElementById('campaignDescription').value = campaign.description;
            document.getElementById('templateId').value = campaign.template_id;
            
            const groupSelect = document.getElementById('groupIds');
            campaign.groups.forEach(group => {
                Array.from(groupSelect.options).forEach(option => {
                    if (option.value == group.id) option.selected = true;
                });
            });

            if (campaign.scheduled_at) {
                document.getElementById('scheduledAt').value = campaign.scheduled_at.slice(0, 16);
            }
            
            openCampaignModal();
        });
}

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

function viewCampaign(id) {
    fetch(`/campaigns/${id}`)
        .then(response => response.json())
        .then(campaign => {
            const details = document.getElementById('campaignDetails');
            details.innerHTML = `
                <div class="border-b pb-4">
                    <h4 class="text-lg font-medium mb-2">${campaign.name}</h4>
                    <p class="text-gray-600">${campaign.description || 'No description'}</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h5 class="font-medium mb-2">Campaign Details</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Status:</span>
                                <span class="font-medium">${campaign.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Scheduled:</span>
                                <span class="font-medium">${campaign.scheduled_at || 'Not scheduled'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Created:</span>
                                <span class="font-medium">${new Date(campaign.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h5 class="font-medium mb-2">Template</h5>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Name:</span>
                                <span class="font-medium">${campaign.template.name}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Content:</span>
                                <p class="mt-1">${campaign.template.content}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <h5 class="font-medium mb-2">Groups</h5>
                    <div class="grid grid-cols-2 gap-4">
                        ${campaign.groups.map(group => `
                            <div class="bg-gray-50 p-3 rounded">
                                <div class="flex justify-between">
                                    <span class="font-medium">${group.name}</span>
                                    <span class="text-sm text-gray-500">${group.customers_count} customers</span>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                ${campaign.messages ? `
                    <div class="mt-4">
                        <h5 class="font-medium mb-2">Message Stats</h5>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="bg-green-50 p-3 rounded">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">
                                        ${campaign.messages.filter(m => m.status === 'sent').length}
                                    </div>
                                    <div class="text-sm text-gray-500">Sent</div>
                                </div>
                            </div>
                            <div class="bg-red-50 p-3 rounded">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">
                                        ${campaign.messages.filter(m => m.status === 'failed').length}
                                    </div>
                                    <div class="text-sm text-gray-500">Failed</div>
                                </div>
                            </div>
                            <div class="bg-yellow-50 p-3 rounded">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-600">
                                        ${campaign.messages.filter(m => m.status === 'pending').length}
                                    </div>
                                    <div class="text-sm text-gray-500">Pending</div>
                                </div>
                            </div>
                        </div>
                    </div>
                ` : ''}
            `;
            document.getElementById('viewCampaignModal').classList.remove('hidden');
        });
}

function closeViewCampaignModal() {
    document.getElementById('viewCampaignModal').classList.add('hidden');
}

// Add these functions after the existing modal functions

async function duplicateCampaign(campaignId) {
    try {
        const response = await fetch(`/api/campaigns/${campaignId}/duplicate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Failed to duplicate campaign');
        }

        const data = await response.json();
        
        // Open the campaign modal with the duplicated data
        document.getElementById('modalTitle').textContent = 'Edit Duplicated Campaign';
        document.getElementById('campaignId').value = data.id;
        document.getElementById('campaignName').value = data.name + ' (Copy)';
        document.getElementById('campaignDescription').value = data.description;
        document.getElementById('templateId').value = data.template_id;
        
        // Set the selected groups
        const groupSelect = document.getElementById('groupIds');
        data.groups.forEach(group => {
            Array.from(groupSelect.options).forEach(option => {
                if (option.value == group.id) {
                    option.selected = true;
                }
            });
        });

        openCampaignModal();
    } catch (error) {
        console.error('Error duplicating campaign:', error);
        alert('Failed to duplicate campaign. Please try again.');
    }
}

async function resendCampaign(campaignId) {
    if (!confirm('Are you sure you want to resend this campaign?')) {
        return;
    }

    try {
        const response = await fetch(`/api/campaigns/${campaignId}/resend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (!response.ok) {
            throw new Error('Failed to resend campaign');
        }

        // Refresh the page to show the new campaign status
        window.location.reload();
    } catch (error) {
        console.error('Error resending campaign:', error);
        alert('Failed to resend campaign. Please try again.');
    }
}

</script>
@endpush
@endsection
