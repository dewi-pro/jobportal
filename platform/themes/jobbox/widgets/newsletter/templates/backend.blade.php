<div class="mb-3">
    <label class="form-label" for="widget-title">{{ __('Title') }}</label>
    <input type="text" id="widget-title" class="form-control" name="title" value="{{ $config['title'] }}">
</div>

<div class="mb-3">
    <label class="form-label" for="widget-background-image">{{ __('Background Image') }}</label>
    {!! Form::mediaImage('background_image', $config['background_image']) !!}
</div>

<div class="mb-3">
    <label class="form-label" for="widget-image-left">{{ __('Image left') }}</label>
    {!! Form::mediaImage('image_left', $config['image_left']) !!}
</div>


<div class="mb-3">
    <label class="form-label" for="widget-image-right">{{ __('Image right') }}</label>
    {!! Form::mediaImage('image_right', $config['image_right']) !!}
</div>


@php($random = Str::random(20))

<div class="mb-3">
    <label class="control-label" for="style_{{ $random }}">{{ __('Style') }}</label>
    {!!
        Form::customSelect('style', [
            'style-1' => __('Style 1'),
            'style-2' => __('Style 2'),
            'style-3' => __('Style 3'),
        ], Arr::get($config, 'style'), ['id' => 'style_' . $random])
    !!}
</div>

