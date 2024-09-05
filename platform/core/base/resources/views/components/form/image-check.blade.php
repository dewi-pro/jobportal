@props([
    'name' => null,
    'options' => [],
    'value' => null,
])

@php
    $id = $attributes->get('id', $name);
@endphp

<div class="row g-2">
    @foreach($options as $key => $option)
        @php
            $label = Arr::get($option, 'label');
            $image = Arr::get($option, 'image', asset('vendor/core/core/base/images/ui-selector-placeholder.jpg'));
        @endphp

        <div class="col-6 col-sm-4 ui-selector mb-3">
            <label for="{{ $id }}-{{ $key }}" class="form-imagecheck form-imagecheck-tick mb-2">
                <input {{ $attributes->merge(['id' => "$id-$key", 'name' => $name, 'type' => 'radio', 'value' => $key, 'class' => 'form-imagecheck-input', 'checked' => $key == old($name, $value)]) }}>
                <span class="form-imagecheck-figure">
                    <img src="{{ $image }}" alt="{{ $label }}" class="form-imagecheck-image">
                </span>
            </label>

            @if($label)
                <label for="{{ $id }}-{{ $key }}" class="cursor-pointer text-center form-check-label">
                    {{ $label }}
                </label>
            @endif
        </div>
    @endforeach
</div>
