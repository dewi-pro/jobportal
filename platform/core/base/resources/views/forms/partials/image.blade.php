@php
    $allowThumb = Arr::get($attributes, 'allow_thumb', true);
@endphp

<x-core::form.image
    :allow-thumb="$allowThumb"
    :name="$name"
    :value="$value"
    action="select-image"
/>
