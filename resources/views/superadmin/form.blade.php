<x-app-layout>
    <x-slot name="header">
        Form Submissions
    </x-slot>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold">Companies</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($accounts as $acc)
                        <a href="/superadmin/accounts/{{ $acc->id }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ (isset($account) && $account->id === $acc->id) ? 'active' : '' }}">
                            <div class="fw-bold">{{ $acc->name }}</div>
                            <span class="badge {{ (isset($account) && $account->id === $acc->id) ? 'bg-light text-dark' : 'bg-primary' }} rounded-pill">{{ $acc->forms_count }}</span>
                        </a>
                    @empty
                        <div class="p-3 text-muted text-center small">No companies registered yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <div class="d-flex align-items-center mb-3">
                <a href="/superadmin/accounts/{{ $form->account_id }}" class="btn btn-sm btn-outline-secondary me-3">← Back to Forms</a>
                <h3 class="h4 m-0 fw-bold">{{ $form->title }}</h3>
            </div>

            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="h5 m-0 fw-bold">
                        Submissions <span class="badge bg-secondary ms-2">Version: {{ $form->publishedVersion?->version_number ?? 'Draft' }}</span>
                    </h4>
                    <a href="/admin/forms/{{ $form->id }}/export" class="btn btn-sm btn-success fw-bold">↓ Export CSV</a>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm m-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3 py-2">ID</th>
                                    <th class="px-3 py-2">IP Address</th>
                                    <th class="px-3 py-2">Submitted At</th>
                                    @php $schema = $form->publishedVersion?->schema ?? []; @endphp
                                    @foreach($schema as $field)
                                        <th class="px-3 py-2">{{ $field['label'] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $submissions = $form->publishedVersion?->submissions ?? collect([]); @endphp
                                @forelse($submissions as $sub)
                                    <tr>
                                        <td class="px-3 py-2 font-monospace small text-muted" title="{{ $sub->id }}">{{ substr($sub->id, 0, 8) }}...</td>
                                        <td class="px-3 py-2 small text-muted">{{ $sub->ip_address ?? 'N/A' }}</td>
                                        <td class="px-3 py-2 small text-muted">{{ $sub->created_at->format('M d, Y H:i') }}</td>
                                        @foreach($schema as $field)
                                            <td class="px-3 py-2 small">{{ $sub->data[$field['name']] ?? '-' }}</td>
                                        @endforeach
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="100%" class="px-3 py-4 text-center text-muted">No submissions yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
