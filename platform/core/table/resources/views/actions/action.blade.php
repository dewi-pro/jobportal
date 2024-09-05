@php
    /** @var Botble\Table\Actions\Action $action */
@endphp

<a
    @if (!$action->getAttribute('class')) @class([
        'btn',
        'btn-sm',
        'btn-icon' => $action->hasIcon(),
        $action->getColor(),
    ]) @endif
    @include('core/table::actions.includes.action-attributes')
>
    @include('core/table::actions.includes.action-icon')

    <span @class(['sr-only' => $action->hasIcon()])>{{ $action->getLabel() }}</span>
</a>
