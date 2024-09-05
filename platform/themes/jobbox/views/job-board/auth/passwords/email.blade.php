<section class="pt-100 login-register">
    <div class="container">
        <div class="row login-register-cover">
            <div class="col-lg-4 col-md-6 col-sm-12 mx-auto">
                <div class="text-center">
                    <p class="font-sm text-brand-2">{{ __('Forgot Password?') }}</p>
                    <h2 class="mt-10 mb-5 text-brand-1">{{ __('Reset your password.') }}</h2>
                    <p class="font-sm text-muted mb-30">{{ __('Enter your email and instructions will be sent to you!') }}</p>
                </div>
                @if(session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <form class="login-register text-start mt-20" action="{{ route('public.account.password.email') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('Email address') }} *</label>
                        <input @class(['form-control', 'is-invalid' => $errors->has('email')]) id="email" type="text" name="email" required placeholder="{{ __('Email') }}" value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-brand-1 hover-up w-100" type="submit">
                            {{ __('Reset Password') }}
                        </button>
                    </div>
                    <div class="text-muted text-center">
                        {{ __('Remembered It?') }}
                        <a href="{{ route('public.account.login') }}">{{ __('Go to login') }}</a>
                    </div>
                </form>
            </div>
            <div class="img-1 d-none d-lg-block">
                <img class="shape-1" src="{{ RvMedia::getImageUrl(theme_option('auth_background_image_1')) }}" alt="{{ theme_option('site_name') }}">
            </div>
            <div class="img-2">
                <img src="{{ RvMedia::getImageUrl(theme_option('auth_background_image_2')) }}" alt="{{ theme_option('site_name') }}">
            </div>
        </div>
    </div>
</section>
