  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-6">
              <div class="card login-form">
                  <div class="card-body">
                      <h4 class="text-center">{{ trans('plugins/job-board::dashboard.login-title') }}</h4>
                      <br>
                      <form
                          method="POST"
                          action="{{ route('public.account.login') }}"
                      >
                          @csrf
                          <div class="form-group">
                              <input
                                  class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                  id="email"
                                  name="email"
                                  type="email"
                                  value="{{ old('email') }}"
                                  placeholder="{{ trans('plugins/job-board::dashboard.email') }}"
                                  autofocus
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
                                  placeholder="{{ trans('plugins/job-board::dashboard.password') }}"
                              >
                              @if ($errors->has('password'))
                                  <span class="invalid-feedback">
                                      <strong>{{ $errors->first('password') }}</strong>
                                  </span>
                              @endif
                          </div>
                          <div class="form-group">
                              <div class="checkbox">
                                  <label>
                                      <input
                                          name="remember"
                                          type="checkbox"
                                          {{ old('remember') ? 'checked' : '' }}
                                      > {{ trans('plugins/job-board::dashboard.remember-me') }}
                                  </label>
                              </div>
                          </div>
                          <div class="form-group mb-0">
                              <button
                                  class="btn btn-blue btn-full fw6"
                                  type="submit"
                              >
                                  {{ trans('plugins/job-board::dashboard.login-cta') }}
                              </button>
                              <div class="text-center">
                                  <a
                                      class="btn btn-link"
                                      href="{{ route('public.account.password.request') }}"
                                  >
                                      {{ trans('plugins/job-board::dashboard.forgot-password-cta') }}
                                  </a>
                              </div>
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
