<section class="pt-100 login-register">
    <div class="container">
        <div class="row login-register-cover">
            <div class="col-lg-4 col-md-6 col-sm-12 mx-auto">
                <div class="text-center">
                    <p class="font-sm text-brand-2">{{ __('Request Password') }}</p>
                    <h2 class="mt-10 mb-5 text-brand-1">{{ __('Reset your password') }}</h2>
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
                <form class="login-register text-start mt-20" action="{{ route('public.account.password.reset.update') }}" method="post">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
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
                        <label class="form-label" for="password">{{ __('Password') }} *</label>
                        <input @class(['form-control', 'is-invalid' => $errors->has('email')]) id="password" type="password" name="password" required placeholder="{{ __('Password') }}">
                        @error('password')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password_confirmation">{{ __('Password confirmation') }} *</label>
                        <input @class(['form-control', 'is-invalid' => $errors->has('email')]) id="password_confirmation" type="password" name="password_confirmation" required placeholder="{{ __('Password confirmation') }}">
                        @error('password_confirmation')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <button class="btn btn-brand-1 hover-up w-100" type="submit">
                            {{ __('Change Password') }}
                        </button>
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
