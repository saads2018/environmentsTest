@extends('layouts.dashboard')

@section('content')
    <div class="login-wrapper">
        <div class="logo-img">
            <img src="{{ Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
        </div>

        <div class="login-title">
            <h6>Log in</h6>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="login-input">
                <label for="email">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="login-input">
                <label for="password">{{ __('Password') }}</label>
                <input id="password" type="password" class="@error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="login-checkbox">
                <label for="remember">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>{{ __('Remember Me') }}</span>
                </label>
            </div>

            <a href="{{ route('password.request') }}">Forgot password?</a>

            <button type="submit" class="login-btn">{{ __('Login') }}</button>
        </Form>
    </div>
</template>
<style>
    aside {
        display: none;
    }
</style>
@endsection