    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card login-form">
                    <div class="card-body">
                        <h4 class="text-center">{{ trans('plugins/job-board::account.forgot_password') }}</h4>
                        <br>
                        <form
                            method="POST"
                            action="{{ route('public.account.password.email') }}"
                        >
                            @csrf
                            <div class="form-group">
                                <input
                                    class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                    id="email"
                                    name="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="{{ trans('plugins/job-board::dashboard.email') }}"
                                >
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
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
                                <div class="text-center">
                                    <a
                                        class="btn btn-link"
                                        href="{{ route('public.account.login') }}"
                                    >{{ trans('plugins/job-board::dashboard.back-to-login') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
