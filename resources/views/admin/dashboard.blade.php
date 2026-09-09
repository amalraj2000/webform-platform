<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 m-0 fw-bold">
            {{ __('Company Admin Dashboard') }}
        </h2>
    </x-slot>

    @foreach($forms as $form)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-4">
                    <div class="mb-3 mb-md-0">
                        <h3 class="h5 fw-bold mb-2">{{ $form->title }}</h3>
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
                        <button onclick="addField({{ $form->id }})" class="btn btn-secondary fw-bold">+ Add Field</button>
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
    @endforeach

    <!-- Scripts for Dashboard -->
    <script>
        const schemas = {};

        @foreach($forms as $form)
            schemas[{{ $form->id }}] = {!! json_encode($form->publishedVersion?->schema ?? []) !!};
            if(schemas[{{ $form->id }}].length === 0) {
                schemas[{{ $form->id }}] = [
                    { name: 'first_name', label: 'First Name', type: 'text', required: true }
                ];
            }
        @endforeach

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

        function renderBuilder(formId) {
            const container = document.getElementById(`schema-builder-${formId}`);
            if(!container) return;
            
            container.innerHTML = '';
            schemas[formId].forEach((field, index) => {
                container.innerHTML += `
                    <div class="row g-3 p-3 mb-3 bg-white border rounded position-relative">
                        <div class="position-absolute top-0 end-0 p-2" style="width: auto; z-index: 10;">
                            <button onclick="removeField(${formId}, ${index})" class="btn btn-sm btn-outline-danger border-0">✖ Remove</button>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Field Name (Internal)</label>
                            <input type="text" value="${field.name}" onchange="updateField(${formId}, ${index}, 'name', this.value)" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Label (Public)</label>
                            <input type="text" value="${field.label}" onchange="updateField(${formId}, ${index}, 'label', this.value)" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted mb-1">Type</label>
                            <select onchange="updateField(${formId}, ${index}, 'type', this.value)" class="form-select form-select-sm">
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
                                <input type="checkbox" ${field.required ? 'checked' : ''} onchange="updateField(${formId}, ${index}, 'required', this.checked)" class="form-check-input" id="req-${formId}-${index}">
                                <label class="form-check-label small fw-bold" for="req-${formId}-${index}">Required</label>
                            </div>
                        </div>

                        ${['select', 'radio'].includes(field.type) ? `
                        <div class="col-12 mt-2">
                            <label class="form-label small fw-bold text-muted mb-1">Options (comma separated)</label>
                            <input type="text" value="${(field.options || []).join(',')}" onchange="updateField(${formId}, ${index}, 'options', this.value.split(','))" class="form-control form-control-sm" placeholder="Option 1, Option 2, Option 3">
                        </div>
                        ` : ''}
                    </div>
                `;
            });
        }

        function updateField(formId, index, key, value) {
            schemas[formId][index][key] = value;
            if(key === 'type') {
                if(['select', 'radio'].includes(value)) {
                    schemas[formId][index]['options'] = ['Option 1'];
                } else {
                    delete schemas[formId][index]['options'];
                }
                renderBuilder(formId);
            }
        }

        function addField(formId) {
            schemas[formId].push({ name: 'new_field_' + schemas[formId].length, label: 'New Field', type: 'text', required: false });
            renderBuilder(formId);
        }

        function removeField(formId, index) {
            schemas[formId].splice(index, 1);
            renderBuilder(formId);
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
                    body: JSON.stringify({ schema: schemas[formId] })
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
                schemas[formId].forEach(f => {
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
                    schemas[formId].forEach(f => {
                        row += `<td class="small">${sub.data[f.name] || '-'}</td>`;
                    });
                    row += `</tr>`;
                    tbody.innerHTML += row;
                });
            } catch (e) {
                console.error(e);
            }
        }

        // Initialize all forms
        document.addEventListener('DOMContentLoaded', () => {
            Object.keys(schemas).forEach(formId => {
                renderBuilder(formId);
                fetchSubmissions(formId);
            });
        });
    </script>
</x-app-layout>
