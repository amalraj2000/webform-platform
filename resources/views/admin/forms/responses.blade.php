<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin.forms.index') }}" class="text-decoration-none text-muted small">&larr; Back to Forms</a>
                <h4 class="mb-0 fw-bold mt-1">{{ $form->title }} - Responses</h4>
            </div>
            <div>
                <a href="{{ route('admin.forms.export', $form->id) }}" class="btn btn-success fw-bold">↓ Export CSV</a>
            </div>
        </div>
    </x-slot>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-header bg-white border-0 pt-4 pb-2 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0" style="color: #4b5563;">
                Recent Submissions (Version: {{ $form->publishedVersion?->version_number ?? 'Draft' }})
            </h5>
            <button onclick="fetchSubmissions({{ $form->id }})" class="btn btn-sm btn-outline-secondary">Refresh</button>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover align-middle mb-0 w-100" id="submissions-table-{{ $form->id }}">
                    <thead>
                        <tr id="submissions-headers-{{ $form->id }}" style="background-color: #E5E7EB;">
                    <tbody id="submissions-body-{{ $form->id }}">
                        <!-- Submissions injected via JS -->
                        <tr><td colspan="100%" class="text-center text-muted p-5">Loading submissions...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        const schema = {!! json_encode($form->publishedVersion?->schema ?? []) !!};

        function escapeHtml(unsafe) {
            return (unsafe || '').toString()
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        async function fetchSubmissions(formId) {
            try {
                const response = await fetch(`/admin/forms/${formId}/submissions`);
                const result = await response.json();
                
                const headersTr = document.getElementById(`submissions-headers-${formId}`);
                if(!headersTr) return;
                
                headersTr.innerHTML = `
                    <th class="text-muted small fw-bold text-uppercase border-0 py-3 ps-3">ID</th>
                    <th class="text-muted small fw-bold text-uppercase border-0 py-3">IP Address</th>
                    <th class="text-muted small fw-bold text-uppercase border-0 py-3">Submitted At</th>
                `;
                schema.forEach(f => {
                    headersTr.innerHTML += `<th class="text-muted small fw-bold text-uppercase border-0 py-3">${escapeHtml(f.label)}</th>`;
                });

                const tbody = document.getElementById(`submissions-body-${formId}`);
                tbody.innerHTML = '';

                if(result.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="100%" class="text-center text-muted py-5"><svg class="mx-auto mb-3" style="width: 48px; height: 48px; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg><br>No submissions yet.</td></tr>`;
                    return;
                }

                result.data.forEach(sub => {
                    let row = `<tr>
                        <td class="font-monospace small text-muted ps-3 py-3" title="${sub.id}">${sub.id.substring(0,8)}...</td>
                        <td class="small text-muted">${escapeHtml(sub.ip_address || 'N/A')}</td>
                        <td class="small text-muted">${new Date(sub.created_at).toLocaleString()}</td>
                    `;
                    schema.forEach(f => {
                        row += `<td class="small text-dark">${escapeHtml(sub.data[f.name] || '-')}</td>`;
                    });
                    row += `</tr>`;
                    tbody.innerHTML += row;
                });

                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable(`#submissions-table-${formId}`)) {
                    $(`#submissions-table-${formId}`).DataTable().destroy();
                }

                // Initialize DataTable
                $(`#submissions-table-${formId}`).DataTable({
                    "language": {
                        "search": "Search:",
                        "lengthMenu": "Show _MENU_ entries"
                    },
                    "pageLength": 10,
                    "ordering": true
                });
            } catch (e) {
                console.error(e);
                const tbody = document.getElementById(`submissions-body-${formId}`);
                tbody.innerHTML = `<tr><td colspan="100%" class="text-center text-danger p-3">Failed to load submissions.</td></tr>`;
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            fetchSubmissions({{ $form->id }});
        });
    </script>
</x-app-layout>
