@extends('layouts.app')

@section('content')
    <div class="auth-container">
        <div class="auth-card">
            <div class="card-header auth-card-header">{{ __('Inscription') }}</div>

            <div class="card-body auth-card-body">
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="auth-form-label">{{ __('Name') }}</label>
                        <input id="name" type="text"
                            class="form-control auth-form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" required autocomplete="name" autofocus>

                        @error('name')
                            <span class="invalid-feedback auth-invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="auth-form-label">{{ __('Email Address') }}</label>
                        <input id="email" type="email"
                            class="form-control auth-form-control @error('email') is-invalid @enderror" name="email"
                            value="{{ old('email') }}" required autocomplete="email">

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
                            required autocomplete="new-password">

                        @error('password')
                            <span class="invalid-feedback auth-invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password-confirm" class="auth-form-label">{{ __('Confirm Password') }}</label>
                        <input id="password-confirm" type="password" class="form-control auth-form-control"
                            name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <div class="mb-0">
                        <button type="submit" class="btn auth-btn-primary">
                            {{ __('Register') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection