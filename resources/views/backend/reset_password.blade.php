<!doctype html>
<html lang="en">

<head>
    @include('components.backend.head')
</head>


<body>
    <div class="page-wrapper">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <div class="login-card login-dark">
                        <div>
                            <div>
                                <a class="logo" href="{{ route('admin.login') }}">
                                    <img class="img-fluid for-dark" src="{{ asset('admin/assets/images/logo/tata-trust-logo.webp') }}" alt="logo" style="max-width: 17% !important;">
                                    <img class="img-fluid for-light" src="{{ asset('admin/assets/images/logo/tata-trust-logo.webp') }}" alt="logo" style="max-width: 35% !important;">
                                </a>
                            </div>

                            <div class="login-main">
                                <form class="theme-form" action="{{ route('admin.password.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $token }}">

                                    <h4>Create Your New Password</h4>

                                    <div class="form-group">
                                        <label class="col-form-label">Email Address</label>
                                        <input class="form-control @error('email') is-invalid @enderror"
                                            type="email" name="email" required
                                            placeholder="Enter Email" value="{{ old('email', $email) }}">

                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label">New Password</label>
                                        <div class="form-input position-relative">
                                            <input class="form-control @error('password') is-invalid @enderror"
                                                type="password" name="password" required
                                                placeholder="Enter New Password">
                                            <div class="show-hide"><span class="show"></span></div>
                                        </div>

                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="col-form-label">Retype Password</label>
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                            type="password" name="password_confirmation" required
                                            placeholder="Confirm Password">

                                        @error('password_confirmation')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-0">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Reset Password</button>
                                    </div>

                                    <p class="mt-4 mb-0 text-center">
                                        Already have a password?
                                        <a class="ms-2" href="{{ route('admin.login') }}">Sign in</a>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('components.backend.auth-js')

</body>

</html>
