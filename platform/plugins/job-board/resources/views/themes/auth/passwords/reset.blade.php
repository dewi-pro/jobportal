<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card login-form">
                <div class="card-body">
                    <h4 class="text-center">{{ trans('plugins/job-board::dashboard.reset-password-title') }}</h4>
                    <br>
                    <form
                        method="POST"
                        action="{{ route('public.account.password.update') }}"
                    >
                        @csrf
                        <input
                            name="token"
                            type="hidden"
                            value="{{ $token }}"
                        >
                        <div class="form-group">
                            <input
                                class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                id="email"
                                name="email"
                                type="email"
                                value="{{ $email or old('email') }}"
                                required
                                autofocus
                                placeholder="{{ trans('plugins/job-board::dashboard.email') }}"
                            >
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input
                                class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                id="password"
                                name="password"
                                type="password"
                                required
                                placeholder="{{ trans('plugins/job-board::dashboard.password') }}"
                            >
                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input
                                class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}"
                                id="password-confirm"
                                name="password_confirmation"
                                type="password"
                                required
                                placeholder="{{ trans('plugins/job-board::dashboard.password-confirmation') }}"
                            >
                            @if ($errors->has('password_confirmation'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group mb-0">
                            <button
                                class="btn btn-blue btn-full fw6"
                                type="submit"
                            >
                                {{ trans('plugins/job-board::dashboard.reset-password-cta') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
