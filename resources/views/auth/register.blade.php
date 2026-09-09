<x-guest-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="card-title text-center mb-4 fw-bold">Create Account</h4>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Company Name -->
                <div class="mb-3">
                    <label for="company_name" class="form-label fw-semibold">Company Name</label>
                    <input id="company_name" class="form-control @error('company_name') is-invalid @enderror" type="text" name="company_name" value="{{ old('company_name') }}" required autofocus autocomplete="organization" />
                    @error('company_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Name -->
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Full Name</label>
                    <input id="name" class="form-control @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" />
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" />
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                    <input id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" />
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">Register</button>
                </div>
                
                <div class="text-center mt-3">
                    <a class="text-decoration-none small text-muted" href="{{ route('login') }}">
                        Already registered? Log in here.
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
