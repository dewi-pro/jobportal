    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card login-form">
                    <div class="card-body">
                        <h4 class="text-center">{{ trans('plugins/job-board::dashboard.register-title') }}</h4>
                        <br>
                        <form
                            method="POST"
                            action="{{ route('public.account.register') }}"
                        >
                            @csrf
                            <div class="form-group">
                                <select
                                    class="form-control"
                                    name="type"
                                >
                                    <option value="">{{ __('Select account type') }}</option>
                                    <option value="employer">{{ __('Employer') }}</option>
                                    <option value="job-seeker">{{ __('Job Seeker') }}</option>
                                </select>
                                @if ($errors->has('type'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('type') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input
                                    class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                    id="first_name"
                                    name="first_name"
                                    type="text"
                                    value="{{ old('first_name') }}"
                                    required
                                    autofocus
                                    placeholder="{{ trans('plugins/job-board::dashboard.first_name') }}"
                                >
                                @if ($errors->has('first_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <input
                                    class="form-control{{ $errors->has('last_name') ? ' is-invalid' : '' }}"
                                    id="last_name"
                                    name="last_name"
                                    type="text"
                                    value="{{ old('last_name') }}"
                                    required
                                    placeholder="{{ trans('plugins/job-board::dashboard.last_name') }}"
                                >
                                @if ($errors->has('last_name'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </span>
                                @endif
                            </div>
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
                                    class="form-control"
                                    id="password-confirm"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                    placeholder="{{ trans('plugins/job-board::dashboard.password-confirmation') }}"
                                >
                            </div>
                            <div class="form-group mb-0">
                                <button
                                    class="btn btn-blue btn-full fw6"
                                    type="submit"
                                >
                                    {{ trans('plugins/job-board::dashboard.register-cta') }}
                                </button>
                            </div>

                            <div class="text-center">
                                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\JobBoard\Models\Account::class) !!}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
