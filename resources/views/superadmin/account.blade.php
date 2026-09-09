<x-app-layout>
    <x-slot name="header">
        Company Management
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
            @if(isset($account))
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3">
                        <h3 class="h4 m-0 fw-bold">Forms for {{ $account->name }}</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4 py-3">ID</th>
                                        <th class="px-4 py-3">Title</th>
                                        <th class="px-4 py-3">Description</th>
                                        <th class="px-4 py-3 text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($account->forms as $formItem)
                                        <tr>
                                            <td class="px-4 py-3 align-middle">{{ $formItem->id }}</td>
                                            <td class="px-4 py-3 align-middle fw-semibold">{{ $formItem->title }}</td>
                                            <td class="px-4 py-3 align-middle text-muted small">{{ $formItem->description ?? '-' }}</td>
                                            <td class="px-4 py-3 align-middle text-end">
                                                <a href="/forms/{{ $formItem->uuid }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Public Link</a>
                                                <a href="/superadmin/forms/{{ $formItem->id }}" class="btn btn-sm btn-primary">View Submissions</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-5 text-center text-muted">No forms created yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <!-- <div class="card shadow-sm border-0 h-100 d-flex flex-column align-items-center justify-content-center py-5 rounded-4">
                    <div class="text-center text-muted">
                        <svg class="mx-auto mb-3" style="width: 64px; height: 64px; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <h4 class="fw-bold">No Company Selected</h4>
                        <p>Select a company from the sidebar to view their forms.</p>
                    </div>
                </div> -->
            @endif
        </div>
    </div>
</x-app-layout>
