<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input type="mediaImage" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Subtitle') }}</label>
    <input type="text" name="subtitle" value="{{ Arr::get($attributes, 'subtitle') }}" class="form-control" placeholder="{{ __('Subtitle') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Description') }}</label>
    <input type="text" name="description" value="{{ Arr::get($attributes, 'description') }}" class="form-control" placeholder="{{ __('Description') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Image') }}</label>
    {!! Form::mediaImage('image', Arr::get($attributes, 'image')) !!}
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Phone') }}</label>
    <input type="text" name="phone" value="{{ Arr::get($attributes, 'phone') }}" class="form-control" placeholder="{{ theme_option('hotline') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Email') }}</label>
    <input type="email" name="email" value="{{ Arr::get($attributes, 'subtitle') }}" class="form-control" placeholder="{{ theme_option('email') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Address') }}</label>
    <input type="text" name="address" value="{{ Arr::get($attributes, 'address') }}" class="form-control" placeholder="{{ theme_option('address') }}">
</div>
@if (is_plugin_active('newsletter'))
    <div class="mb-3">
        <label class="form-label">{{ __('Show newsletter form') }}</label>
        {!! Form::customSelect('show_newsletter', ['yes' => __('Yes'), 'no' => __('No')], Arr::get($attributes, 'show_newsletter', 'yes')) !!}
    </div>
@endif
