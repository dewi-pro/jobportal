<section class="section-box mt-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mb-40">
                <span class="font-md color-brand-2 mt-20 d-inline-block">{!! BaseHelper::clean($shortcode->title) !!}</span>
                <h2 class="mt-5 mb-10">{!! BaseHelper::clean($shortcode->subtitle) !!}</h2>
                <p class="font-md color-text-paragraph-2">{!! BaseHelper::clean($shortcode->description) !!}</p>
                @if(session('success_msg'))
                    <div class="alert alert-success mt-3">
                        {{ session('success_msg') }}
                    </div>
                @endif
                <form class="contact-form-style contact-form mt-30" action="{{ route('public.send.contact') }}" method="post">
                    @csrf
                    <div class="row wow animate__animated animate__fadeInUp" data-wow-delay=".1s">
                        <div class="col-lg-6 col-md-6">
                            <div class="input-style mb-20">
                                <input @class(['font-sm color-text-paragraph-2', 'is-invalid' => $errors->has('name')]) name="name" placeholder="{{ __('Enter your name') }}" type="text" value="{{ old('name') }}">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="input-style mb-20">
                                <input @class(['font-sm color-text-paragraph-2', 'is-invalid' => $errors->has('email')]) name="email" placeholder="{{ __('Your email') }}" type="email" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12">
                            <div class="textarea-style mb-30">
                                <textarea @class(['font-sm color-text-paragraph-2', 'is-invalid' => $errors->has('content')]) name="content" placeholder="{{ __('Tell us about yourself') }}">{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            @if (is_plugin_active('captcha'))
                                @if (setting('enable_captcha'))
                                    {!! Captcha::display() !!}
                                @endif

                                @if (setting('enable_math_captcha_for_contact_form', 0))
                                    <div class="input-style mb-20">
                                        {!! app('math-captcha')->input(['class' => 'font-sm color-text-paragraph-2', 'id' => 'math-group', 'placeholder' => app('math-captcha')->label()]) !!}
                                    </div>
                                @endif
                            @endif

                            <button class="submit btn btn-send-message" type="submit">{{ __('Send message') }}</button>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="contact-mb-3 mt-4">
                            <div class="contact-message contact-success-message" style="display: none"></div>
                            <div class="contact-message contact-error-message" style="display: none"></div>
                        </div>
                    </div>
                </form>
            </div>
            @if($shortcode->image)
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <img src="{{ RvMedia::getImageUrl($shortcode->image) }}" alt="{{ setting('site_title') }}">
                </div>
            @endif
        </div>
    </div>
</section>
