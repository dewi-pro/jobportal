@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    {!! $form->renderForm() !!}
@stop

@push('footer')
    <x-core::modal.action
        id="generate-thumbnails-modal"
        :title="trans('core/setting::setting.generate_thumbnails')"
        :description="trans('core/setting::setting.generate_thumbnails_description')"
        type="warning"
        :submit-button-label="trans('core/setting::setting.generate')"
        :submit-button-attrs="['id' => 'generate-thumbnails-button']"
        :has-form="true"
        :form-action="route('settings.media.generate-thumbnails')"
    />
@endpush
