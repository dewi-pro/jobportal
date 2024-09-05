<meta
    name="csrf-token"
    content="{{ csrf_token() }}"
>

@foreach (RvMedia::getConfig('libraries.stylesheets', []) as $css)
    <link
        type="text/css"
        href="{{ asset($css) }}"
        rel="stylesheet"
    />
@endforeach

@include('core/media::config')
