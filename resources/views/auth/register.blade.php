
@extends('components.auth.layouts')


@section('body')

<body>
    
    <div class="auth-container" id="authContainer">
    
    <div class="form-container">
                <h2 class="form-title">Create Account</h2>
                  @if ($errors->any())
                    <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                    </div>
                @endif
                <form id="signupForm" method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" class="form-input" placeholder="Full Name" value="{{ old('name') }}">
                        <div class="error-message" id="signupNameError"></div>
                    </div>

                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Email Address" value="{{ old('email') }}">
                        <div class="error-message" id="signupEmailError"></div>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password" class="form-input" placeholder="Password">
                        <div class="error-message" id="signupPasswordError"></div>
                    </div>

                    <div class="form-group">
                        <input type="password" name="password_confirmation" class="form-input"
                            placeholder="Confirm Password">
                        <div class="error-message" id="signupPasswordConfirmError"></div>
                    </div>

                    <button type="submit" class="form-button" id="signupButton">
                        <div class="loading"></div>
                        Create Account
                    </button>
                </form>

                <div class="switch-form">
                    <span>Already have an account? </span>
                    <a href="/" id="showLogin">Sign In</a>
                </div>
            </div>
    </div>
</body>

    @include('auth.scripts')


@endsection