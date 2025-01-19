@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Message Templates</h1>
        <button onclick="openTemplateModal()" 
                class="bg-primary-500 text-white px-4 py-2 rounded">
            Create Template
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($templates as $template)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-semibold">{{ $template->name }}</h3>
                    <span class="px-2 py-1 rounded-full text-xs {{ $template->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $template->status ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="flex space-x-2">
                    <button onclick="editTemplate({{ $template->id }})" 
                            class="text-primary-500 hover:text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="previewTemplate({{ $template->id }})" 
                            class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <button onclick="toggleTemplateStatus({{ $template->id }})" 
                            class="text-{{ $template->status ? 'red' : 'green' }}-500 hover:text-{{ $template->status ? 'red' : 'green' }}-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Template ID:</label>
                    <p class="text-sm font-mono">{{ $template->msg91_template_id }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Content:</label>
                    <p class="text-sm">{{ $template->content }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Variables:</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @if($template->variables)
                        @foreach($template->variables as $variable)
                        <span class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $variable }}</span>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Template Modal -->
<div id="templateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[800px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium" id="modalTitle">Create Template</h3>
            <button onclick="closeTemplateModal()" class="text-secondary-400 hover:text-secondary-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form id="templateForm" onsubmit="saveTemplate(event)">
            <input type="hidden" id="templateId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Template Name</label>
                    <input type="text" id="templateName" 
                           class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">MSG91 Template ID</label>
                    <input type="text" id="msg91TemplateId" 
                           class="mt-1 block w-full rounded-md border-gray-300" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Content</label>
                    <textarea id="templateContent" 
                            class="mt-1 block w-full rounded-md border-gray-300" 
                            rows="4" required></textarea>
                    <p class="mt-1 text-sm text-gray-500">
                        Use {variable_name} for variables. Example: Hello {name}!
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Variables</label>
                    <div id="variablesContainer" class="mt-2 space-y-2">
                        <div class="flex gap-2">
                            <input type="text" 
                                   class="variable-input block w-full rounded-md border-gray-300" 
                                   placeholder="Variable name">
                            <button type="button" onclick="addVariableField()" 
                                    class="px-3 py-2 bg-gray-100 rounded-md hover:bg-gray-200">
                                +
                            </button>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="templateStatus" class="mt-1 block w-full rounded-md border-gray-300">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="mt-5 flex justify-end space-x-2">
                <button type="button" onclick="closeTemplateModal()" 
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

<!-- Preview Modal -->
<div id="previewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-[600px] shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium">Template Preview</h3>
            <button onclick="closePreviewModal()" class="text-gray-400 hover:text-gray-500">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Test Variables</label>
                <div id="previewVariables" class="space-y-2">
                    <!-- Variable inputs will be added here -->
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                <div id="previewContent" class="p-4 bg-gray-50 rounded-md">
                    <!-- Preview content will be shown here -->
                </div>
            </div>
        </div>
        <div class="mt-5 flex justify-end">
            <button onclick="closePreviewModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                Close
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTemplate = null;

function openTemplateModal() {
    document.getElementById('modalTitle').textContent = 'Create Template';
    document.getElementById('templateModal').classList.remove('hidden');
}

function closeTemplateModal() {
    document.getElementById('templateForm').reset();
    document.getElementById('templateId').value = '';
    document.getElementById('variablesContainer').innerHTML = `
        <div class="flex gap-2">
            <input type="text" class="variable-input block w-full rounded-md border-gray-300" 
                   placeholder="Variable name">
            <button type="button" onclick="addVariableField()" 
                    class="px-3 py-2 bg-gray-100 rounded-md hover:bg-gray-200">
                +
            </button>
        </div>
    `;
    document.getElementById('templateModal').classList.add('hidden');
}

function addVariableField() {
    const container = document.getElementById('variablesContainer');
    const div = document.createElement('div');
    div.className = 'flex gap-2';
    div.innerHTML = `
        <input type="text" class="variable-input block w-full rounded-md border-gray-300" 
               placeholder="Variable name">
        <button type="button" onclick="this.parentElement.remove()" 
                class="px-3 py-2 bg-red-100 rounded-md hover:bg-red-200">
            -
        </button>
    `;
    container.appendChild(div);
}

function getVariables() {
    return Array.from(document.querySelectorAll('.variable-input'))
        .map(input => input.value)
        .filter(value => value.trim() !== '');
}

function saveTemplate(event) {
    event.preventDefault();
    const id = document.getElementById('templateId').value;
    const data = {
        name: document.getElementById('templateName').value,
        msg91_template_id: document.getElementById('msg91TemplateId').value,
        content: document.getElementById('templateContent').value,
        variables: getVariables(),
        status: document.getElementById('templateStatus').value === '1'
    };

    fetch(`/templates${id ? `/${id}` : ''}`, {
        method: id ? 'PUT' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        closeTemplateModal();
        window.location.reload();
    })
    .catch(error => alert('An error occurred'));
}

function editTemplate(id) {
    fetch(`/templates/${id}`)
        .then(response => response.json())
        .then(template => {
            document.getElementById('modalTitle').textContent = 'Edit Template';
            document.getElementById('templateId').value = template.id;
            document.getElementById('templateName').value = template.name;
            document.getElementById('msg91TemplateId').value = template.msg91_template_id;
            document.getElementById('templateContent').value = template.content;
            document.getElementById('templateStatus').value = template.status ? '1' : '0';

            // Set variables
            const container = document.getElementById('variablesContainer');
            container.innerHTML = template.variables.map((variable, index) => `
                <div class="flex gap-2">
                    <input type="text" class="variable-input block w-full rounded-md border-gray-300" 
                           value="${variable}" placeholder="Variable name">
                    <button type="button" 
                            onclick="${index === 0 ? 'addVariableField()' : 'this.parentElement.remove()'}" 
                            class="px-3 py-2 bg-${index === 0 ? 'gray' : 'red'}-100 rounded-md 
                                   hover:bg-${index === 0 ? 'gray' : 'red'}-200">
                        ${index === 0 ? '+' : '-'}
                    </button>
                </div>
            `).join('');

            openTemplateModal();
        });
}

function previewTemplate(id) {
    fetch(`/templates/${id}`)
        .then(response => response.json())
        .then(template => {
            currentTemplate = template;
            const variablesContainer = document.getElementById('previewVariables');
            variablesContainer.innerHTML = template.variables.map(variable => `
                <div>
                    <label class="block text-sm text-secondary-600">${variable}</label>
                    <input type="text" 
                           class="preview-variable mt-1 block w-full rounded-md border-gray-300" 
                           data-variable="${variable}" 
                           oninput="updatePreview()">
                </div>
            `).join('');
            
            document.getElementById('previewContent').textContent = template.content;
            document.getElementById('previewModal').classList.remove('hidden');
            updatePreview();
        });
}

function updatePreview() {
    if (!currentTemplate) return;

    let content = currentTemplate.content;
    const variables = document.querySelectorAll('.preview-variable');
    variables.forEach(input => {
        const value = input.value || `{${input.dataset.variable}}`;
        content = content.replace(
            new RegExp(`{${input.dataset.variable}}`, 'g'), 
            value
        );
    });

    document.getElementById('previewContent').textContent = content;
}

function closePreviewModal() {
    currentTemplate = null;
    document.getElementById('previewModal').classList.add('hidden');
}

function toggleTemplateStatus(id) {
    fetch(`/templates/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => window.location.reload())
    .catch(error => alert('An error occurred'));
}
</script>
@endpush
@endsection
