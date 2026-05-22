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
                                <form class="theme-form" action="{{ route('admin.password.email') }}" method="POST">
                                    @csrf
                                    <h4>Forgot Your Password?</h4>
                                    <p>Enter your email and we'll send you a link to reset your password.</p>

                                    <div class="form-group">
                                        <label class="col-form-label">Email Address</label>
                                        <input class="form-control @error('email') is-invalid @enderror"
                                            type="email" name="email" required
                                            placeholder="Enter Email" value="{{ old('email') }}">

                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-0">
                                        <button class="btn btn-primary btn-block w-100" type="submit">Send Reset Link</button>
                                    </div>

                                    <p class="mt-4 mb-0 text-center">
                                        Remember your password?
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
