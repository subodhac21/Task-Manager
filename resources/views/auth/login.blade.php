@extends('components.auth.layouts')


@section('body')


    <body>
        <div class="auth-container" id="authContainer">
            <!-- Login Form -->
            <div class="form-container login-container">
                <h2 class="form-title">Welcome Back</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                    </div>
                @endif
                <form id="loginForm" method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Email Address"
                            value="{{ old('email') }}">
                        <div class="error-message" id="loginEmailError"></div>
                    </div>

               

                    <div class="form-group">
                        <input type="password" name="password" class="form-input" placeholder="Password">
                        <div class="error-message" id="loginPasswordError"></div>
                    </div>



                   
                   

                    <div class="remember-forgot">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                        <a href="#" class="forgot-password">Forgot Password?</a>
                    </div>

                    

                    <button type="submit" class="form-button" id="loginButton">
                        <div class="loading"></div>
                        Sign In
                    </button>
                </form>

                <div class="switch-form">
                    <span>Don't have an account? </span>
                    <a href="/register" id="showSignup">Sign Up</a>
                </div>
            </div>


            
        </div>


    </body>

    @include('auth.scripts')

@endsection
