<x-guest-layout>
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h4 class="card-title text-center mb-4 fw-bold">Sign In</h4>
            
            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success mb-4" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" />
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-3 form-check">
                    <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                    <label class="form-check-label" for="remember_me">Remember me</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg fw-bold">Log in</button>
                </div>
                
                <div class="text-center mt-3">
                    @if (Route::has('password.request'))
                        <a class="text-decoration-none small text-muted" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
