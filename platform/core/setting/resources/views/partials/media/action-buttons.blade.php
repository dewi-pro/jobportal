@extends('core/setting::forms.partials.action')

@section('content')
    <x-core::button
        type="button"
        color="warning"
        class="generate-thumbnails-trigger-button"
        :data-saving="trans('core/setting::setting.generating_media_thumbnails')"
    >
        {{ trans('core/setting::setting.generate_thumbnails') }}
    </x-core::button>
@stop
