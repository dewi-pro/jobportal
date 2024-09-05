@if ($action->hasIcon())
    @if ($action->isRenderabeIcon())
        {!! BaseHelper::clean($action->getIcon()) !!}
    @else
        <x-core::icon :name="$action->getIcon()" />
    @endif
@endif
