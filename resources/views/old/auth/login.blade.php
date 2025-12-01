@extends('layouts.app')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <div class="card-header auth-card-header">{{ __('Connexion') }}</div>

            <div class="card-body auth-card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="email" class="auth-form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email"
                            class="form-control auth-form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" required autocomplete="email" autofocus>

                        @error('email')
                            <span class="invalid-feedback auth-invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="auth-form-label">{{ __('Password') }}</label>
                        <input id="password" type="password"
                            class="form-control auth-form-control @error('password') is-invalid @enderror" name="password"
                            required autocomplete="current-password">

                        @error('password')
                            <span class="invalid-feedback auth-invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input class="form-check-input auth-form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label auth-form-check-label" for="remember">
                            {{ __('Remember Me') }}
                        </label>
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn auth-btn-primary">
                            {{ __('Login') }}
                        </button>

                        @if (Route::has('password.request'))
                            <a class="auth-btn-link" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection