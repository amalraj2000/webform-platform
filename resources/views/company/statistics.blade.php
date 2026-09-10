<x-app-layout>
    <x-slot name="header">
        Admin Dashboard - Statistics
    </x-slot>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold text-muted text-uppercase mb-3">Total Forms</h5>
                    <p class="display-4 fw-bold text-primary">{{ $formsCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center">
                    <h5 class="card-title fw-bold text-muted text-uppercase mb-3">Total Submissions</h5>
                    <p class="display-4 fw-bold text-success">{{ $submissionsCount }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
