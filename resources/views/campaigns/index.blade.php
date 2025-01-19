@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-secondary-900">Campaign Management</h1>
        <p class="mt-1 text-sm text-secondary-600">Create and manage your SMS campaigns</p>
    </div>

    <!-- Action Buttons & Stats -->
    <div class="flex justify-between items-center mb-6">
        <button onclick="openCampaignModal()" 
                class="btn-primary animate-scale group">
            <svg class="w-5 h-5 mr-2 transform group-hover:scale-110 transition-transform" 
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M12 4v16m8-8H4"/>
            </svg>
            Create Campaign
        </button>
        <div class="flex items-center space-x-6">
            <div class="text-sm">
                <span class="text-secondary-500">Total Campaigns:</span>
                <span class="font-semibold text-indigo-600 ml-1">{{ number_format($campaigns->count()) }}</span>
            </div>
        </div>
    </div>

    <!-- Campaigns Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        @foreach($campaigns as $campaign)
        <div class="card transform hover:scale-[1.02] transition-all duration-300">
            <div class="card-body">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $campaign->name }}</h3>
                        <span class="badge {{ 
                            $campaign->status === 'completed' ? 'badge-success' :
                            ($campaign->status === 'failed' ? 'badge-error' :
                            ($campaign->status === 'running' ? 'badge-info' :
                            ($campaign->status === 'scheduled' ? 'badge-warning' : 'badge-secondary')))
                        }}">
                            {{ ucfirst($campaign->status) }}
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        @if(in_array($campaign->status, ['draft', 'scheduled']))
                            <button onclick="editCampaign({{ $campaign->id }})" 
                                    class="text-primary-600 hover:text-primary-900 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                        @endif
                        <button onclick="viewCampaign({{ $campaign->id }})" 
                                class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                                title="Quick View">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                        <a href="{{ route('campaigns.show', $campaign->id) }}"
                           class="text-blue-600 hover:text-blue-900 transition-colors duration-200"
                           title="Full View">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>                        <button onclick="duplicateCampaign({{ $campaign->id }})"
                                class="text-gray-600 hover:text-gray-900 transition-colors duration-200"
                                title="Duplicate Campaign">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </button>
                        @if($campaign->status === 'completed')
                            <button onclick="resendCampaign({{ $campaign->id }})"
                                    class="text-green-600 hover:text-green-900 transition-colors duration-200"
                                    title="Resend Campaign">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <p class="text-sm text-secondary-600 mb-4">{{ $campaign->description ?? 'No description provided' }}</p>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-secondary-500">Template:</span>
                        <span class="font-medium text-gray-900">{{ $campaign->template?->name ?? 'None' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Target Groups:</span>
                        <span class="font-medium text-gray-900">{{ $campaign->groups->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Schedule:</span>
                        <span class="font-medium text-gray-900">
                            {{ $campaign->scheduled_at ? $campaign->scheduled_at->format('M d, Y H:i') : 'Not scheduled' }}
                        </span>
                    </div>
                </div>

                @if(in_array($campaign->status, ['draft', 'scheduled']))
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button onclick="executeCampaign({{ $campaign->id }})" 
                                class="w-full btn-primary">
                            Execute Now
                        </button>
                    </div>
                @endif

                @if($campaign->messages_count)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Messages:</span>
                            <div class="flex space-x-4">
                                <span class="text-green-600">{{ $campaign->sent_count }} sent</span>
                                <span class="text-yellow-600">{{ $campaign->pending_count }} pending</span>
                                <span class="text-red-600">{{ $campaign->failed_count }} failed</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Empty State -->
    @if($campaigns->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No campaigns</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new campaign.</p>
            <div class="mt-6">
                <button onclick="openCampaignModal()" class="btn-primary">
                    Create Campaign
                </button>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if($campaigns->hasPages())
        <div class="mt-6">
            {{ $campaigns->links() }}
        </div>
    @endif
</div>

<!-- Campaign Modal -->
<div id="campaignModal" class="modal hidden">
    <div class="modal-content w-[800px] max-w-full animate-slide-up">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Create Campaign</h3>
            <button onclick="closeCampaignModal()" 
                    class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <form id="campaignForm" onsubmit="saveCampaign(event)" class="space-y-6">
            <input type="hidden" id="campaignId">
            
            <div class="grid grid-cols-1 gap-6">
                <div class="form-group">
                    <label for="campaignName" class="form-label">Campaign Name</label>
                    <input type="text" id="campaignName" class="form-input" required
                           placeholder="Enter campaign name">
                </div>

                <div class="form-group">
                    <label for="campaignDescription" class="form-label">Description</label>
                    <textarea id="campaignDescription" rows="3" class="form-input"
                            placeholder="Enter campaign description"></textarea>
                </div>

                <div class="form-group">
                    <label for="templateId" class="form-label">Message Template</label>
                    <select id="templateId" class="form-input" required>
                        <option value="">Select a template</option>
                        @foreach($templates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                    <div id="templatePreview" class="mt-2 p-3 bg-gray-50 rounded-md text-sm text-gray-600 hidden">
                        <!-- Template preview will be shown here -->
                    </div>
                </div>

                <div class="form-group">
                    <label for="groupIds" class="form-label">Target Groups</label>
                    <select id="groupIds" class="form-input" multiple required>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">
                                {{ $group->name }} ({{ number_format($group->customers_count) }} customers)
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple groups</p>
                </div>

                <div class="form-group">
                    <label for="scheduledAt" class="form-label">Schedule (Optional)</label>
                    <input type="datetime-local" id="scheduledAt" class="form-input">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <button type="button" onclick="closeCampaignModal()" class="btn-secondary">
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    Save Campaign
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Campaign Modal -->
<div id="viewCampaignModal" class="modal hidden">
    <div class="modal-content w-[800px] max-w-full animate-slide-up">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Campaign Details</h3>
            <button onclick="closeViewCampaignModal()" 
                    class="text-gray-400 hover:text-gray-500 transition-colors duration-200">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

function closeViewCampaignModal() {
    document.getElementById('viewCampaignModal').classList.add('hidden');
}

// Template Preview
document.getElementById('templateId').addEventListener('change', function(e) {
    const templateId = e.target.value;
    if (templateId) {
        fetch(`/templates/${templateId}`)
            .then(response => response.json())
            .then(template => {
                const preview = document.getElementById('templatePreview');
                preview.textContent = template.content;
                preview.classList.remove('hidden');
            });
    } else {
        document.getElementById('templatePreview').classList.add('hidden');
    }
});

// Save Campaign
async function saveCampaign(event) {
    event.preventDefault();
    const id = document.getElementById('campaignId').value;
    const data = {
        name: document.getElementById('campaignName').value,
        description: document.getElementById('campaignDescription').value,
        template_id: document.getElementById('templateId').value,
        group_ids: Array.from(document.getElementById('groupIds').selectedOptions).map(option => option.value),
        scheduled_at: document.getElementById('scheduledAt').value || null
    };

    try {
        const response = await fetch(`/api/campaigns${id ? `/${id}` : ''}`, {
            method: id ? 'PUT' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error('Failed to save campaign');

        closeCampaignModal();
        window.location.reload();
    } catch (error) {
        alert('An error occurred while saving the campaign');
    }
}

// View Campaign Details
async function viewCampaign(id) {
    try {
        const response = await fetch(`/api/campaigns/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch campaign details');
        }

        const { data: campaign } = await response.json();
        
        const details = document.getElementById('campaignDetails');
        details.innerHTML = `
            <div class="border-b pb-4">
                <div class="flex items-center justify-between">
                    <h4 class="text-xl font-semibold text-gray-900">${campaign.name}</h4>
                    <span class="badge ${getCampaignStatusClass(campaign.status)}">
                        ${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}
                    </span>
                </div>
                <p class="mt-2 text-gray-600">${campaign.description || 'No description provided'}</p>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h5 class="font-medium mb-2">Campaign Details</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Created:</span>
                            <span class="font-medium">${formatDate(campaign.created_at)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Scheduled:</span>
                            <span class="font-medium">
                                ${campaign.scheduled_at ? formatDateTime(campaign.scheduled_at) : 'Not scheduled'}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h5 class="font-medium mb-2">Message Template</h5>
                    <div class="bg-gray-50 p-3 rounded-md text-sm">
                        ${campaign.template ? campaign.template.content : 'No template selected'}
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="font-medium mb-2">Target Groups</h5>
                <div class="grid grid-cols-2 gap-4">
                    ${campaign.groups.map(group => `
                        <div class="bg-gray-50 p-3 rounded-md">
                            <div class="flex justify-between">
                                <span class="font-medium">${group.name}</span>
                                <span class="text-sm text-gray-500">${group.customers_count} customers</span>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>

            ${campaign.messages_count ? `
                <div class="mt-4">
                    <h5 class="font-medium mb-2">Message Stats</h5>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-green-50 p-4 rounded-md">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">${campaign.sent_count}</div>
                                <div class="text-sm text-gray-500">Sent</div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-md">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600">${campaign.pending_count}</div>
                                <div class="text-sm text-gray-500">Pending</div>
                            </div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-md">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">${campaign.failed_count}</div>
                                <div class="text-sm text-gray-500">Failed</div>
                            </div>
                        </div>
                    </div>
                </div>
            ` : ''}
        `;

        // Show the modal
        document.getElementById('viewCampaignModal').classList.remove('hidden');

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Failed to load campaign details',
            confirmButtonColor: '#3085d6'
        });
    }
}

// Helper functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function formatDateTime(dateString) {
    return new Date(dateString).toLocaleString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getCampaignStatusClass(status) {
    const statusClasses = {
        'draft': 'bg-gray-100 text-gray-800',
        'scheduled': 'bg-blue-100 text-blue-800',
        'active': 'bg-green-100 text-green-800',
        'completed': 'bg-purple-100 text-purple-800',
        'failed': 'bg-red-100 text-red-800',
        'cancelled': 'bg-yellow-100 text-yellow-800'
    };
    return statusClasses[status] || 'bg-gray-100 text-gray-800';
}

// Optional: Add loading state
function showLoading() {
    const details = document.getElementById('campaignDetails');
    details.innerHTML = `
        <div class="flex justify-center items-center p-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900"></div>
        </div>
    `;
}

function getCampaignStatusClass(status) {
    switch (status.toLowerCase()) {
        case 'completed': return 'badge-success';
        case 'failed': return 'badge-error';
        case 'running': return 'badge-info';
        case 'scheduled': return 'badge-warning';
        default: return 'badge-secondary';
    }
}

// Campaign Operations
async function duplicateCampaign(id) {
    try {
        const response = await fetch(`/api/campaigns/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to duplicate campaign');

        const data = await response.json();
        window.location.reload();
    } catch (error) {
        alert('Failed to duplicate campaign. Please try again.');
    }
}

async function resendCampaign(id) {
    if (!confirm('Are you sure you want to resend this campaign?')) return;

    try {
        const response = await fetch(`/api/campaigns/${id}/resend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to resend campaign');

        window.location.reload();
    } catch (error) {
        alert('Failed to resend campaign. Please try again.');
    }
}

async function executeCampaign(id) {
    if (!confirm('Are you sure you want to execute this campaign now?')) return;

    try {
        const response = await fetch(`/api/campaigns/${id}/execute`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) throw new Error('Failed to execute campaign');

        const data = await response.json();
        alert(data.message);
        window.location.reload();
    } catch (error) {
        alert('Failed to execute campaign. Please try again.');
    }
}

// Edit Campaign
async function editCampaign(id) {
    try {
        const response = await fetch(`/api/campaigns/${id}`,
            {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            },
        );
        const res = await response.json();

        campaign = res.data;

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
    } catch (error) {
        alert('Failed to load campaign details. Please try again.');
    }
}
</script>
@endpush

@endsection