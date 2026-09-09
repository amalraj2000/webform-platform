<x-app-layout>
    <x-slot name="header">
        Dashboard Overview
    </x-slot>

    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card text-white bg-primary h-100 shadow-sm border-0 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                    <h5 class="card-title text-uppercase opacity-75 fw-bold tracking-wider">Total Forms</h5>
                    <p class="display-2 fw-bold m-0">{{ $formsCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card text-white bg-info h-100 shadow-sm border-0 rounded-4">
                <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                    <h5 class="card-title text-uppercase opacity-75 fw-bold tracking-wider">Total Submissions</h5>
                    <p class="display-2 fw-bold m-0">{{ $submissionsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 h-100 d-flex flex-column align-items-center justify-content-center py-5 mt-4 rounded-4">
        <div class="text-center text-muted">
            <svg class="mx-auto mb-3" style="width: 64px; height: 64px; opacity: 0.5;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <h4 class="fw-bold">Ready to build?</h4>
            <p>Navigate to the <a href="/admin/forms" class="text-decoration-none fw-bold">Forms</a> tab to create and manage your forms.</p>
        </div>
    </div>
</x-app-layout>
