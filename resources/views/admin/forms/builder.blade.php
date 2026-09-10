<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin.forms.index') }}" class="text-decoration-none text-muted small">&larr; Back to Forms</a>
                <h4 class="mb-0 fw-bold mt-1">{{ $form->title }} - Schema Builder</h4>
            </div>
            <div>
                <a href="{{ route('admin.forms.responses', $form->id) }}" class="btn btn-outline-secondary fw-bold me-2">View Responses</a>
            </div>
        </div>
    </x-slot>

    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center border-bottom pb-3 mb-4">
                <div class="mb-3 mb-md-0">
                    <p class="text-muted small mb-2">{{ $form->description }}</p>
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted fw-bold text-uppercase">Share Link:</span>
                        <input type="text" readonly value="{{ url('/forms/' . $form->uuid) }}" class="form-control form-control-sm bg-light" style="width: 250px;" id="share-link-{{ $form->id }}" onclick="this.select()">
                        <button onclick="copyLink('{{ url('/forms/' . $form->uuid) }}', {{ $form->id }})" class="btn btn-sm btn-outline-secondary">Copy Link</button>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="/forms/{{ $form->uuid }}" target="_blank" class="btn btn-primary">↗ Open Public Form</a>
                </div>
            </div>

            <!-- Form Builder Section -->
            <div class="p-4 bg-light border rounded mb-5">
                <h4 class="h6 fw-bold mb-4">Current Version: {{ $form->publishedVersion?->version_number ?? 'Draft' }}</h4>
                <div id="schema-builder-{{ $form->id }}" class="mb-4">
                    <!-- Fields injected via JS -->
                </div>
                <div class="d-flex gap-2">
                    <button onclick="addField()" class="btn btn-secondary fw-bold">+ Add Field</button>
                    <button onclick="publishForm({{ $form->id }})" class="btn btn-primary fw-bold">Publish Version</button>
                </div>
                <p class="small text-muted mt-2 mb-0">Publishing will freeze the current fields and make them live.</p>
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
            
            // Build options for condition field dropdown
            let conditionFieldOptions = '<option value="">-- None --</option>';
            schema.forEach(f => {
                conditionFieldOptions += `<option value="${f.name}">${escapeHtml(f.label)} (${f.name})</option>`;
            });

            container.innerHTML = '';
            schema.forEach((field, index) => {
                // Clean up dangling condition_fields
                if (field.condition_field && !schema.find(f => f.name === field.condition_field)) {
                    field.condition_field = '';
                    field.condition_value = '';
                }
                
                let fieldOptions = conditionFieldOptions.replace(`value="${field.condition_field}"`, `value="${field.condition_field}" selected`);
                
                container.innerHTML += `
                    <div class="row g-3 p-3 mb-3 bg-white border rounded position-relative">
                        <div class="position-absolute top-0 end-0 p-2" style="width: auto; z-index: 10;">
                            <button onclick="removeField(${index})" class="btn btn-sm btn-outline-danger border-0">✖ Remove</button>
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Field Name (Internal)</label>
                            <input type="text" value="${escapeHtml(field.name)}" onchange="updateField(${index}, 'name', this.value)" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted mb-1">Label (Public)</label>
                            <input type="text" value="${escapeHtml(field.label)}" onchange="updateField(${index}, 'label', this.value)" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted mb-1">Help Text</label>
                            <input type="text" value="${escapeHtml(field.help_text || '')}" onchange="updateField(${index}, 'help_text', this.value)" class="form-control form-control-sm" placeholder="Optional hint">
                        </div>
                        <div class="col-md-2 d-flex align-items-end pb-1">
                            <div class="form-check">
                                <input type="checkbox" ${field.required ? 'checked' : ''} onchange="updateField(${index}, 'required', this.checked)" class="form-check-input" id="req-{{ $form->id }}-${index}">
                                <label class="form-check-label small fw-bold" for="req-{{ $form->id }}-${index}">Required</label>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mt-2">
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

                        ${['select', 'radio'].includes(field.type) ? `
                        <div class="col-md-9 mt-2">
                            <label class="form-label small fw-bold text-muted mb-1">Options (comma separated)</label>
                            <input type="text" value="${escapeHtml((field.options || []).join(','))}" onchange="updateField(${index}, 'options', this.value.split(','))" class="form-control form-control-sm" placeholder="Option 1, Option 2, Option 3">
                        </div>
                        ` : ''}

                        <div class="col-12 mt-3 pt-3 border-top">
                            <label class="form-label small fw-bold text-primary mb-2">Conditional Visibility (Show if...)</label>
                            <div class="d-flex gap-2 align-items-center">
                                <select onchange="updateField(${index}, 'condition_field', this.value); renderBuilder();" class="form-select form-select-sm" style="max-width: 200px;">
                                    ${fieldOptions}
                                </select>
                                <span class="small text-muted">equals</span>
                                <input type="text" value="${escapeHtml(field.condition_value || '')}" onchange="updateField(${index}, 'condition_value', this.value)" class="form-control form-control-sm" placeholder="Value..." style="max-width: 200px;" ${!field.condition_field ? 'disabled' : ''}>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        function escapeHtml(unsafe) {
            return (unsafe || '').toString()
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        function updateField(index, key, value) {
            if (key === 'name') {
                // Prevent duplicate names
                const isDuplicate = schema.some((f, i) => i !== index && f.name === value);
                if (isDuplicate) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Field name must be unique!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                    renderBuilder(); // Reset UI to old value
                    return;
                }

                const oldName = schema[index].name;
                schema.forEach(f => {
                    if (f.condition_field === oldName) {
                        f.condition_field = value;
                    }
                });
            }
            
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
                
                if (!response.ok) {
                    let errorText = res.message || 'Validation failed.';
                    if (res.errors) {
                        errorText += '\n' + Object.values(res.errors).flat().join('\n');
                    }
                    throw new Error(errorText);
                }
                
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
                    text: e.message || 'Failed to publish the form.',
                    icon: 'error',
                    confirmButtonColor: '#0d6efd'
                });
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            renderBuilder();
        });
    </script>
</x-app-layout>
