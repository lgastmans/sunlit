@extends('layouts.guest')

@section("content")
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header pt-4 pb-4 text-center bg-sunlit">
                            <a href="index.html">
                                <span><img src="/images/logo.png" alt="" ></span>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />
                            
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Sign In</h4>
                                <p class="text-muted mb-4">Enter your email address and password to access admin panel.</p>
                            </div>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input tabindex="1" id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus >
                                </div>

                                <div class="mb-3">
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-muted float-end"><small>{{ __('Forgot your password?') }}</small></a>
                                    @endif
                                    <label for="password" class="form-label">Password</label>
                                    <input tabindex="2" id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
    
                                </div>

                                <div class="mb-3 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember" checked>
                                        <label class="form-check-label" for="remember_me">{{ __('Remember me') }}</label>
                                    </div>
                                </div>

                                <div class="mb-3 mb-0 text-center">
                                    <x-button class="ml-4">
                                        {{ __('Log in') }}
                                    </x-button>
                                </div>

                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection