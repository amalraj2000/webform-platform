<x-app-layout>
    <x-slot name="header">
        Form Management
    </x-slot>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold">My Forms</h5>
                    <button class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#createFormModal">+</button>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($forms as $f)
                        <a href="{{ route('admin.forms.show', $f->id) }}" class="list-group-item list-group-item-action {{ (isset($form) && $form->id === $f->id) ? 'active' : '' }}">
                            <div class="fw-bold">{{ $f->title }}</div>
                            <small class="{{ (isset($form) && $form->id === $f->id) ? 'text-white-50' : 'text-muted' }} d-block text-truncate" style="max-width: 100%;">{{ $f->description ?? 'No description' }}</small>
                        </a>
                    @empty
                        <div class="p-3 text-muted text-center small">No forms created yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            @if(isset($form))
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-4">
                            <div class="mb-3 mb-md-0">
                                <h3 class="h4 fw-bold mb-1">{{ $form->title }}</h3>
                                <p class="text-muted small mb-2">{{ $form->description }}</p>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="small text-muted fw-bold text-uppercase">Share Link:</span>
                                    <input type="text" readonly value="{{ url('/forms/' . $form->uuid) }}" class="form-control form-control-sm bg-light" style="width: 250px;" id="share-link-{{ $form->id }}" onclick="this.select()">
                                    <button onclick="copyLink('{{ url('/forms/' . $form->uuid) }}', {{ $form->id }})" class="btn btn-sm btn-outline-secondary">Copy Link</button>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="/forms/{{ $form->uuid }}" target="_blank" class="btn btn-primary">↗ Open Public Form</a>
                                <a href="/admin/forms/{{ $form->id }}/export" class="btn btn-success">↓ Export CSV</a>
                            </div>
                        </div>

                        <!-- Form Builder Section -->
                        <div class="p-4 bg-light border rounded mb-5">
                            <h4 class="h6 fw-bold mb-4">Schema Builder</h4>
                            <div id="schema-builder-{{ $form->id }}" class="mb-4">
                                <!-- Fields injected via JS -->
                            </div>
                            <div class="d-flex gap-2">
                                <button onclick="addField()" class="btn btn-secondary fw-bold">+ Add Field</button>
                                <button onclick="publishForm({{ $form->id }})" class="btn btn-primary fw-bold">Publish Version</button>
                            </div>
                            <p class="small text-muted mt-2 mb-0">Publishing will freeze the current fields and make them live.</p>
                        </div>

                        <!-- Submissions Viewer -->
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="h6 fw-bold m-0">
                                    Recent Submissions (Version: {{ $form->publishedVersion?->version_number ?? 'Draft' }})
                                </h4>
                                <button onclick="fetchSubmissions({{ $form->id }})" class="btn btn-sm btn-outline-secondary">Refresh</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm" id="submissions-table-{{ $form->id }}">
                                    <thead class="table-light">
                                        <tr id="submissions-headers-{{ $form->id }}">
                                            <th>ID</th>
                                            <th>IP Address</th>
                                            <th>Submitted At</th>
                                            <!-- Dynamic headers -->
                                        </tr>
                                    </thead>
                                    <tbody id="submissions-body-{{ $form->id }}">
                                        <!-- Submissions injected via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scripts for Dashboard -->
                <script>
                    const schema = {!! json_encode($form->publishedVersion?->schema ?? []) !!};
                    if(schema.length === 0) {
                        schema.push({ name: 'first_name', label: 'First Name', type: 'text', required: true });
                    }

                    function copyLink(url, formId) {
                        const input = document.getElementById('share-link-' + formId);
                        input.select();
                        navigator.clipboard.writeText(url);
                        
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Link copied to clipboard!',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }

                    function renderBuilder() {
                        const container = document.getElementById(`schema-builder-{{ $form->id }}`);
                        if(!container) return;
                        
                        container.innerHTML = '';
                        schema.forEach((field, index) => {
                            container.innerHTML += `
                                <div class="row g-3 p-3 mb-3 bg-white border rounded position-relative">
                                    <div class="position-absolute top-0 end-0 p-2" style="width: auto; z-index: 10;">
                                        <button onclick="removeField(${index})" class="btn btn-sm btn-outline-danger border-0">✖ Remove</button>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Field Name (Internal)</label>
                                        <input type="text" value="${field.name}" onchange="updateField(${index}, 'name', this.value)" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold text-muted mb-1">Label (Public)</label>
                                        <input type="text" value="${field.label}" onchange="updateField(${index}, 'label', this.value)" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small fw-bold text-muted mb-1">Type</label>
                                        <select onchange="updateField(${index}, 'type', this.value)" class="form-select form-select-sm">
                                            <option value="text" ${field.type === 'text' ? 'selected' : ''}>Text</option>
                                            <option value="email" ${field.type === 'email' ? 'selected' : ''}>Email</option>
                                            <option value="number" ${field.type === 'number' ? 'selected' : ''}>Number</option>
                                            <option value="date" ${field.type === 'date' ? 'selected' : ''}>Date</option>
                                            <option value="select" ${field.type === 'select' ? 'selected' : ''}>Select</option>
                                            <option value="radio" ${field.type === 'radio' ? 'selected' : ''}>Radio</option>
                                            <option value="checkbox" ${field.type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end pb-1">
                                        <div class="form-check">
                                            <input type="checkbox" ${field.required ? 'checked' : ''} onchange="updateField(${index}, 'required', this.checked)" class="form-check-input" id="req-{{ $form->id }}-${index}">
                                            <label class="form-check-label small fw-bold" for="req-{{ $form->id }}-${index}">Required</label>
                                        </div>
                                    </div>

                                    ${['select', 'radio'].includes(field.type) ? `
                                    <div class="col-12 mt-2">
                                        <label class="form-label small fw-bold text-muted mb-1">Options (comma separated)</label>
                                        <input type="text" value="${(field.options || []).join(',')}" onchange="updateField(${index}, 'options', this.value.split(','))" class="form-control form-control-sm" placeholder="Option 1, Option 2, Option 3">
                                    </div>
                                    ` : ''}
                                </div>
                            `;
                        });
                    }

                    function updateField(index, key, value) {
                        schema[index][key] = value;
                        if(key === 'type') {
                            if(['select', 'radio'].includes(value)) {
                                schema[index]['options'] = ['Option 1'];
                            } else {
                                delete schema[index]['options'];
                            }
                            renderBuilder();
                        }
                    }

                    function addField() {
                        schema.push({ name: 'new_field_' + schema.length, label: 'New Field', type: 'text', required: false });
                        renderBuilder();
                    }

                    function removeField(index) {
                        schema.splice(index, 1);
                        renderBuilder();
                    }

                    async function publishForm(formId) {
                        const result = await Swal.fire({
                            title: 'Publish Form?',
                            text: "Publishing will create a new immutable version. Existing submissions will remain attached to the old version.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#0d6efd',
                            cancelButtonColor: '#dc3545',
                            confirmButtonText: 'Yes, publish it!'
                        });

                        if (!result.isConfirmed) return;
                        
                        try {
                            const response = await fetch(`/admin/forms/${formId}/publish`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ schema: schema })
                            });
                            
                            const res = await response.json();
                            
                            await Swal.fire({
                                title: 'Published!',
                                text: res.message,
                                icon: 'success',
                                confirmButtonColor: '#0d6efd'
                            });
                            
                            location.reload();
                        } catch (e) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to publish the form.',
                                icon: 'error',
                                confirmButtonColor: '#0d6efd'
                            });
                        }
                    }

                    async function fetchSubmissions(formId) {
                        try {
                            const response = await fetch(`/admin/forms/${formId}/submissions`);
                            const result = await response.json();
                            
                            const headersTr = document.getElementById(`submissions-headers-${formId}`);
                            if(!headersTr) return;
                            
                            headersTr.innerHTML = `
                                <th>ID</th>
                                <th>IP Address</th>
                                <th>Submitted At</th>
                            `;
                            schema.forEach(f => {
                                headersTr.innerHTML += `<th>${f.label}</th>`;
                            });

                            const tbody = document.getElementById(`submissions-body-${formId}`);
                            tbody.innerHTML = '';

                            if(result.data.length === 0) {
                                tbody.innerHTML = `<tr><td colspan="100%" class="text-center text-muted p-3">No submissions yet.</td></tr>`;
                                return;
                            }

                            result.data.forEach(sub => {
                                let row = `<tr>
                                    <td class="font-monospace small text-muted" title="${sub.id}">${sub.id.substring(0,8)}...</td>
                                    <td class="small text-muted">${sub.ip_address || 'N/A'}</td>
                                    <td class="small text-muted">${new Date(sub.created_at).toLocaleString()}</td>
                                `;
                                schema.forEach(f => {
                                    row += `<td class="small">${sub.data[f.name] || '-'}</td>`;
                                });
                                row += `</tr>`;
                                tbody.innerHTML += row;
                            });
                        } catch (e) {
                            console.error(e);
                        }
                    }

                    // Initialize
                    document.addEventListener('DOMContentLoaded', () => {
                        renderBuilder();
                        fetchSubmissions({{ $form->id }});
                    });
                </script>
            @else
                <div class="card shadow-sm border-0 h-100 d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="text-center text-muted">
                        <svg class="mx-auto mb-3" style="width: 64px; height: 64px; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h4 class="fw-bold">No Form Selected</h4>
                        <p>Select a form from the sidebar or create a new one to get started.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Form Modal -->
    <div class="modal fade" id="createFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.forms.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Create New Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Form Title</label>
                        <input type="text" name="title" class="form-control" required placeholder="e.g. Contact Us">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional brief description of the form's purpose"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Create Form</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
