@extends('layouts.guest')

@section("content")
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header pt-4 pb-4 text-center bg-primary">
                            <a href="index.html">
                                <span><img src="/images/logo.png" alt="" height="18"></span>
                            </a>
                        </div>

                        <div class="card-body p-4">
                            <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('registration.password') }}">
            @csrf

            <!-- Invite token and email address -->
            <input type="hidden" name="invite_token" value="{{ $invite_token }}">
            <input type="hidden" name="email" value="{{ $email }}">

         
            <!-- Password -->
            <div class="mb-3">

                <label for="password" class="form-label">Password</label>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />

            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <x-label for="password_confirmation" :value="__('Confirm Password')" />
                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required />

            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    {{ __('Reset Password') }}
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
