@php
    Arr::set($selectAttributes, 'class', Arr::get($selectAttributes, 'class') . ' form-select');
    $choices = $list ?? $choices;
    $selectAttributes = ['data-placeholder' => trans('core/base::forms.select_placeholder'), ...$selectAttributes];

    if ($optionsAttributes && ! is_array($optionsAttributes)) {
        $optionsAttributes = [];
    }
@endphp

{!! Form::select(
    $name,
    $choices,
    $selected,
    $selectAttributes,
    $optionsAttributes,
    $optgroupsAttributes,
) !!}
