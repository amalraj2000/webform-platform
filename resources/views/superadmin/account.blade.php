<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center gap-3">
            <a href="/superadmin/dashboard" class="btn btn-sm btn-outline-secondary">← Back</a>
            <h2 class="h4 m-0 fw-bold">
                {{ $account->name }} - Forms
            </h2>
        </div>
    </x-slot>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h3 class="h5 m-0 fw-bold">Forms for {{ $account->name }}</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover m-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2">UUID</th>
                            <th class="px-4 py-2 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($account->forms as $form)
                            <tr>
                                <td class="px-4 py-2 align-middle">{{ $form->id }}</td>
                                <td class="px-4 py-2 align-middle fw-semibold">{{ $form->title }}</td>
                                <td class="px-4 py-2 align-middle text-muted font-monospace small">{{ $form->uuid }}</td>
                                <td class="px-4 py-2 align-middle text-end">
                                    <a href="/forms/{{ $form->uuid }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Public Link</a>
                                    <a href="/superadmin/forms/{{ $form->id }}" class="btn btn-sm btn-primary">View Submissions</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 text-center text-muted">No forms created yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
