<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 m-0 fw-bold">
            {{ __('Super Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card text-white bg-primary h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title text-uppercase opacity-75">Total Accounts</h5>
                    <p class="display-4 fw-bold m-0">{{ $accounts->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card text-white bg-success h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h5 class="card-title text-uppercase opacity-75">Total Submissions</h5>
                    <p class="display-4 fw-bold m-0">{{ $totalSubmissions }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h3 class="h5 m-0 fw-bold">Registered Companies</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover m-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-2">ID</th>
                            <th class="px-4 py-2">Company Name</th>
                            <th class="px-4 py-2">Slug</th>
                            <th class="px-4 py-2 text-center">Total Forms</th>
                            <th class="px-4 py-2">Joined</th>
                            <th class="px-4 py-2 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($accounts as $account)
                            <tr>
                                <td class="px-4 py-2 align-middle">{{ $account->id }}</td>
                                <td class="px-4 py-2 align-middle fw-semibold">{{ $account->name }}</td>
                                <td class="px-4 py-2 align-middle text-muted">{{ $account->slug }}</td>
                                <td class="px-4 py-2 align-middle text-center">
                                    <span class="badge bg-secondary rounded-pill">{{ $account->forms_count }}</span>
                                </td>
                                <td class="px-4 py-2 align-middle text-muted">{{ $account->created_at->format('M d, Y') }}</td>
                                <td class="px-4 py-2 align-middle text-end">
                                    <a href="/superadmin/accounts/{{ $account->id }}" class="btn btn-sm btn-outline-primary">View Company</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
