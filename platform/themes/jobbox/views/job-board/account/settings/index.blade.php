@php
    Theme::asset()->container('footer')->add('location-js', asset('vendor/core/plugins/location/js/location.js'), ['jquery']);
@endphp

@extends(Theme::getThemeNamespace('views.job-board.account.partials.layout-settings'))

@section('content')
    <div>
        <h3 class="mt-0 mb-15 color-brand-1">{{ __('My Account') }}</h3>

        {!! Form::open(['route' => 'public.account.post.settings', 'method' => 'POST', 'files' => true]) !!}
                <div class="mt-35 mb-40 box-info-profile avatar-view d-inline-block">
                    <div class="image-profile">
                        <img src="{{ $account->avatar_url }}" id="profile-img" alt="{{ $account->name }}">
                    </div>
                    <a class="btn btn-apply">{{ __('Upload Avatar') }}</a>
                </div>

            {!! $form->renderForm(showStart: false, showEnd: false) !!}

            <div class="box-button mt-15">
                <button type="submit" class="btn btn-apply-big font-md font-bold">{{ __('Save All Changes') }}</button>
            </div>
        {!! Form::close() !!}
    </div>
@endsection
