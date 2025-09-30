@extends('layouts.dashboard')

@section('content')
<div class="form-wrapper">

<div class="logo-img">
            <img src="{{ Vite::asset('resources/images/dashboard/naviwell-logo.png') }}" alt="Naviwell logo">
        </div>

        <div class="form-title">
            <h6>{{ __('Reset Password') }}</h6>
</div>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf 
                        
                        <div class="form-input">
                            <label for="token">{{ __('Reset Code') }}</label>

                            <input id="token" name="token" class="@error('token') is-invalid @enderror" >
                            @error('code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                        </div>

                        <div class="form-input">
                            <label for="email" >{{ __('Email Address') }}</label>

                                <input id="email" type="email" class="@error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-input">
                            <label for="password">{{ __('Password') }}</label>

                                <input id="password" type="password" class=" @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="form-input">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="form-btn">{{ __('Reset Password') }}</button>

                    </form>
</div>


<style>
    aside {
        display: none;
    }

    img {
        max-width: 100%;
        display: block;
        object-fit: cover;
    }
    
    .form-wrapper {
        max-width: 24%;
        width: 100%;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .form-wrapper .logo-img {
        max-width: 240px;
        margin: 0 auto 56px;
    }

    .form-title h6 {
        margin-bottom: 16px;
        font-size: 32px;
    }

    .form-wrapper form {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-input {
        width: 100%;
        margin-bottom: 8px;
    }

    .form-input:first-child {
        margin-bottom: 16px;
    }

    .form-input label {
        margin-bottom: 4px;
        font-size: 18px;
    }

    .form-input input {
        width: 100%;
        padding: 16px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #777777;
        transition: .3s ease;
    }

    .form-input input:focus {
        outline: none;
        box-shadow: 2px 4px 8px rgba(0, 0, 0, .08);
    }

    .form-input input:valid {
        background-color: #FFFFFF;
    }

    input:-webkit-autofill, input:-webkit-autofill:focus {
        box-shadow: 0 0 0 1000px #F8F7FA inset;
        -webkit-text-fill-color: #1E1E1E;
    }

    .form-checkbox {
        margin-top: 16px;
        align-self: flex-start;
    }

    .form-checkbox span {
        margin-left: 4px;
    }

    .form-checkbox label {
        display: flex;
        align-items: center;
    }

    .form-checkbox input {
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .form-btn {
        background-color: #084073;
        width: 100%;
        margin-top: 32px;
        padding: 16px 56px;
        color: #FFFFFF;
        border-radius: 8px;
        cursor: pointer;
    }

    @media screen and (max-width: 1280px) {
        .form-wrapper {
            max-width: 50%;
        }
    }

    @media screen and (max-width: 768px) {
        .form-wrapper {
            max-width: 75%;
        }

        .form-wrapper .logo-img {
            max-width: 180px;
            margin: 0 auto 40px;
        }

        .form-title h6 {
            font-size: 24px;
        }

        .form-input label {
            font-size: 16px;
        }
    }

    @media screen and (max-width: 576px) {
        .form-wrapper {
            max-width: 100%;
            margin: 0 32px;
        }
    }
</style>

@endsection
