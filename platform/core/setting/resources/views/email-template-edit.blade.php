@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::form
        :url="$updateUrl"
        method="put"
    >
        <input
            type="hidden"
            name="module"
            value="{{ $pluginData['name'] }}"
        >
        <input
            type="hidden"
            name="template_file"
            value="{{ $pluginData['template_file'] }}"
        >

        <x-core-setting::section
            :title="trans('core/setting::setting.email.title')"
            :description="trans('core/setting::setting.email.description')"
        >
            @if ($emailSubject)
                <input
                    type="hidden"
                    name="email_subject_key"
                    value="{{ get_setting_email_subject_key($pluginData['type'], $pluginData['name'], $pluginData['template_file']) }}"
                >

                <x-core::form.text-input
                    name="email_subject"
                    :label="trans('core/setting::setting.email.subject')"
                    :value="$emailSubject"
                    data-counter="300"
                />
            @endif

            <x-core::form-group>
                <x-core::form.label for="mail-template-editor">
                    {{ trans('core/setting::setting.email.content') }}
                </x-core::form.label>

                <x-core::twig-editor
                    :variables="EmailHandler::getVariables($pluginData['type'], $pluginData['name'], $pluginData['template_file'])"
                    :functions="EmailHandler::getFunctions()"
                    :value="$emailContent"
                    name="email_content"
                    mode="html"
                >
                </x-core::twig-editor>
            </x-core::form-group>

            @if (
                $metabox = apply_filters(
                    'setting_email_template_meta_boxes',
                    null,
                    request()->route()->parameters()))
                <x-slot:footer>
                    <div class="mt-3">
                        {!! $metabox !!}
                    </div>
                </x-slot:footer>
            @endif
        </x-core-setting::section>

        <x-core-setting::section.action>
            <div class="btn-list">
                <x-core::button
                    type="submit"
                    color="primary"
                    icon="ti ti-device-floppy"
                >
                    {{ trans('core/setting::setting.save_settings') }}
                </x-core::button>
                <x-core::button
                    tag="a"
                    href="{{ $restoreUrl . BaseHelper::stringify(request()->input('ref_lang')) }}"
                    icon="ti ti-arrow-back-up"
                    data-bb-toggle="reset-default"
                >
                    {{ trans('core/setting::setting.email.reset_to_default') }}
                </x-core::button>
                <x-core::button
                    tag="a"
                    href="{{ route('settings.email.template.preview', ['type' => $pluginData['type'], 'module' => $pluginData['name'], 'template' => $pluginData['template_file'], 'ref_lang' => request()->input('ref_lang')]) }}"
                    target="_blank"
                    icon="ti ti-eye"
                >
                    {{ trans('core/setting::setting.preview') }}
                </x-core::button>
            </div>
        </x-core-setting::section.action>
    </x-core::form>

    <x-core::modal.action
        id="reset-template-to-default-modal"
        type="warning"
        :title="trans('core/setting::setting.email.confirm_reset')"
        :description="trans('core/setting::setting.email.confirm_message')"
        :submit-button-attrs="['id' => 'reset-template-to-default-button']"
        :submit-button-label="trans('core/setting::setting.email.continue')"
    />
@stop
