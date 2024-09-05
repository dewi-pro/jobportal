@props([
    'label' => null,
    'color' => 'primary',
    'lite' => false,
    'outline' => false,
])

@php
    $classes = Arr::toCssClasses(['badge', "bg-$color text-$color-fg" => !$lite && !$outline, "bg-$color-lt" => $lite, "badge-outline text-$color" => $outline]);
@endphp

<span {{ $attributes->class($classes) }}>
    {{ $label ?? $slot }}
</span>
