@extends('layouts.guest')

@section('content')
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">
                        <!-- Logo -->
                        <div class="card-header pt-4 pb-4 text-center  bg-sunlit">
                            <a href="index.html">
                                <span><img src="images/logo.png" alt="" ></span>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            
                            <div class="text-center m-auto">
                                <img src="assets/images/mail_sent.svg" alt="mail sent image" height="64" />
                                <h4 class="text-dark-50 text-center mt-4 fw-bold">Please check your email</h4>
                                <p class="text-muted mb-4">
                                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                                </p>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                    
                                    <div>
                                        <x-button>
                                            {{ __('Resend Verification Email') }}
                                        </x-button>
                                    </div>
                                </form>
                    
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                    
                                    <x-button>
                                        {{ __('Log Out') }}
                                    </x-button>
                                </form>
                            </div>

                        </div> <!-- end card-body-->
                    </div>
                    <!-- end card-->
                    
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection