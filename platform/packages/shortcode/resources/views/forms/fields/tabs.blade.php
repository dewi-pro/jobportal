@php
    $fields = Arr::get($options, 'fields', []);
    $attributes = Arr::get($options, 'shortcode_attributes', []);
    $max = Arr::get($options, 'max', 20);
@endphp

{!! shortcode()->fields()->tabs($fields, $attributes, $max) !!}
