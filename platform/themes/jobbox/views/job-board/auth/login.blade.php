<section class="pt-100 login-register">
    <div class="container">
        <div class="row login-register-cover">
            <div class="col-lg-4 col-md-6 col-sm-12 mx-auto">
                <div class="text-center">
                    <p class="font-sm text-brand-2">{{ __('Welcome Back!') }}</p>
                    <h2 class="mt-10 mb-5 text-brand-1">{{ __('Member Login') }}</h2>
                    <p class="font-sm text-muted mb-30">{{ __('Sign in to continue.') }}</p>
                </div>
                <form class="login-register text-start mt-20" action="{{ route('public.account.login') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('Email address') }} *</label>
                        <input @class(['form-control', 'is-invalid' => $errors->has('email')]) id="email" type="text" name="email" required placeholder="{{ __('Email') }}" value="{{ BaseHelper::stringify(old('email')) }}">
                        @error('email')
                        <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">{{ __('Password') }} *</label>
                        <input @class(['form-control', 'is-invalid' => $errors->has('password')]) id="password" type="password" name="password" required placeholder="{{ __('Password') }}">
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="login_footer mb-3 d-flex justify-content-between">
                        <label class="cb-container">
                            <input type="checkbox" name="remember" value="1" @checked(old('remember', 1))>
                            <span class="text-small">{{ __('Remember me') }}</span>
                            <span class="checkmark"></span>
                        </label>
                        <a class="text-muted" href="{{ route('public.account.password.request') }}">{{ __('Forgot Password') }}</a>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-brand-1 hover-up w-100" type="submit">
                            {{ __('Login') }}
                        </button>
                    </div>
                    <div class="text-muted text-center">
                        {{ __("Don't have an account?") }}
                        <a href="{{ route('public.account.register') }}">{{ __('Sign Up') }}</a>
                    </div>
                </form>
                <div class="text-center text-muted">
                    {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\JobBoard\Models\Account::class) !!}
                </div>
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
