<section class="pt-100 login-register">
    <div class="container">
        <div class="row login-register-cover">
            <div class="col-lg-4 col-md-6 col-sm-12 mx-auto">
                <div class="text-center">
                    <p class="font-sm text-brand-2">{{ __('Register') }}</p>
                    <h2 class="mt-10 mb-5 text-brand-1">{{ __("Let's Get Started") }}</h2>
                    <p class="font-sm text-muted mb-30">{{ __('Sign Up and get access to all the features.') }}</p>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form class="login-register text-start mt-20" action="{{ route('public.account.register') }}" method="post">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="first_name">{{ __('First Name') }} *</label>
                        <input class="form-control" id="first_name" type="text" name="first_name" required placeholder="{{ __('First Name') }}" value="{{ old('first_name') }}">
                        @error('first_name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="last_name">{{ __('Last Name') }} *</label>
                        <input class="form-control" id="last_name" type="text" name="last_name" required placeholder="{{ __('Last Name') }}" value="{{ old('last_name') }}">
                        @error('last_name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('Email') }} *</label>
                        <input class="form-control" id="email" type="email" name="email" required placeholder="{{ __('Email address') }}" value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="phone">{{ __('Phone') }}</label>
                        <input class="form-control" id="phone" type="tel" name="phone" placeholder="{{ __('Phone number') }}" value="{{ old('phone') }}">
                        @error('phone')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">{{ __('Password') }} *</label>
                        <input class="form-control" id="password" type="password" name="password" required placeholder="{{ __('Password') }}">
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">{{ __('Password Confirmation') }}</label>
                        <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" required placeholder="{{ __('Password Confirmation') }}">
                        @error('password_confirmation')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    @if (setting('job_board_enabled_register_as_employer', 1))
                        <div class="login_footer mb-3 d-flex justify-content-between">
                            <label class="cb-container">
                                <input type="checkbox" name="is_employer" @checked(old('is_employer', false))>
                                <span class="text-small">{{ __('Is Employer?') }}</span>
                                <span class="checkmark"></span>
                            </label>
                        </div>
                    @endif

                    @if (is_plugin_active('captcha') && setting('enable_captcha') && setting('job_board_enable_recaptcha_in_register_page', 0))
                        <div class="mb-3">
                            {!! Captcha::display() !!}
                        </div>
                    @endif

                    <div class="login_footer mb-3 d-flex justify-content-between">
                        <label class="cb-container">
                            <input type="hidden" name="agree_terms_and_policy" value="0">
                            <input type="checkbox" name="agree_terms_and_policy" value="1" @checked(old('agree_terms_and_policy', 1))>
                            <span class="text-small">{{ __('Agree our terms and policy') }}</span>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-brand-1 hover-up w-100" type="submit">
                            {{ __('Register') }}
                        </button>
                    </div>
                    <div class="text-muted text-center">
                        {{ __('Already have an account?') }}
                        <a href="{{ route('public.account.login') }}">{{ __('Sign In') }}</a>
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
