<x-app-layout>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <span>Form Management</span>
            <button class="btn btn-primary fw-bold" style="background-color: #1eb589; border-color: #1eb589;" data-bs-toggle="modal" data-bs-target="#createFormModal">+ Create New Form</button>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 rounded-3">
                <div class="card-body py-4">
                    <h6 class="text-muted fw-bold mb-3" style="font-size: 0.85rem;">Total Forms</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $formsCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 rounded-3">
                <div class="card-body py-4">
                    <h6 class="text-muted fw-bold mb-3" style="font-size: 0.85rem;">Published Forms</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $publishedCount }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100 rounded-3">
                <div class="card-body py-4">
                    <h6 class="text-muted fw-bold mb-3" style="font-size: 0.85rem;">Draft Forms</h6>
                    <h3 class="fw-bold mb-0 text-dark">{{ $draftCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Forms List Card -->
    <div class="card shadow-sm border-0 mb-4 rounded-3">
        <div class="card-header bg-white border-0 pt-4 pb-2">
            <h5 class="fw-bold text-dark mb-0" style="color: #4b5563;">Forms List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table id="formsTable" class="table table-hover align-middle mb-0 w-100">
                    <thead>
                        <tr style="background-color: #E5E7EB;">
                            <th class="text-muted small fw-bold text-uppercase border-0 py-3 ps-3">Title</th>
                            <th class="text-muted small fw-bold text-uppercase border-0 py-3">Description</th>
                            <th class="text-muted small fw-bold text-uppercase border-0 py-3 text-center">Submissions</th>
                            <th class="text-muted small fw-bold text-uppercase border-0 py-3 text-center">Status</th>
                            <th class="text-muted small fw-bold text-uppercase border-0 py-3 text-center">Created At</th>
                            <th class="text-muted small fw-bold text-uppercase border-0 py-3 text-center pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($forms as $form)
                            <tr>
                                <td class="ps-3 py-3 fw-medium text-dark">{{ $form->title }}</td>
                                <td class="text-muted small text-truncate" style="max-width: 250px;">{{ $form->description ?? '--' }}</td>
                                <td class="text-center">{{ $form->submissions_count }}</td>
                                <td class="text-center">
                                    @if($form->published_version_id)
                                        <span class="badge" style="background-color: #e0f2ec; color: #1eb589;">Active</span>
                                    @else
                                        <span class="badge" style="background-color: #fff3cd; color: #856404;">Draft</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $form->created_at->format('M d, Y') }}
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-flex justify-content-center gap-1">
                                        <!-- Edit button (pencil icon) -->
                                        <button class="btn btn-sm text-secondary p-1" title="Edit Form Details" onclick="editForm({{ $form->id }}, '{{ addslashes($form->title) }}', '{{ addslashes($form->description) }}')">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        <!-- Build Schema Button -->
                                        <a href="{{ route('admin.forms.builder', $form->id) }}" class="btn btn-sm text-primary p-1" title="Build Schema">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        </a>

                                        <!-- Responses Button -->
                                        <a href="{{ route('admin.forms.responses', $form->id) }}" class="btn btn-sm text-info p-1" title="View Responses">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <!-- Delete Button -->
                                        @if($form->submissions_count === 0)
                                            <form action="{{ route('admin.forms.destroy', $form->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this form?');" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm text-secondary p-1" title="Delete Form">
                                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Disabled Delete Button -->
                                            <button type="button" class="btn btn-sm text-muted p-1" title="Cannot delete forms with submissions" disabled style="opacity: 0.3;">
                                                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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
                        <label class="form-label fw-bold">Form Title <span class="text-danger">*</span></label>
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

    <!-- Edit Form Modal -->
    <div class="modal fade" id="editFormModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editFormElement" method="POST" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Form Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Form Title <span class="text-danger">*</span></label>
                        <input type="text" id="editFormTitle" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea id="editFormDescription" name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery and DataTables JS -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#formsTable').DataTable({
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries"
                },
                "pageLength": 10,
                "ordering": false // Disabling to keep it simple and match the image layout
            });
        });

        function editForm(id, title, description) {
            document.getElementById('editFormElement').action = `/admin/forms/${id}`;
            document.getElementById('editFormTitle').value = title;
            document.getElementById('editFormDescription').value = description;
            
            var editModal = new bootstrap.Modal(document.getElementById('editFormModal'));
            editModal.show();
        }
    </script>
</x-app-layout>
