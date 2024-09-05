@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::alert type="warning">
        <p>
            {{ trans('plugins/translation::translation.theme_translations_instruction') }}
        </p>

        <p class="mb-0">
            {!! trans(
                'plugins/translation::translation.re_import_alert',
                ['here' => Html::link('#', trans('plugins/translation::translation.here'), ['data-bs-toggle' => 'modal', 'data-bs-target' => '#confirm-publish-modal'])]
            ) !!}
        </p>
    </x-core::alert>

    <div class="row">
        <div class="col-md-6">
            <p>{{ trans('plugins/translation::translation.translate_from') }}
                <strong class="text-info">{{ $defaultLanguage ? $defaultLanguage['name'] : 'en' }}</strong>
                {{ trans('plugins/translation::translation.to') }}
                <strong class="text-info">{{ $locale['name'] }}</strong>
            </p>
        </div>
        <div class="col-md-6">
            <div class="text-end">
                @include(
                    'plugins/translation::partials.list-theme-languages-to-translate',
                    ['groups' => $locales, 'group' => $locale, 'route' => 'translations.index']
                )
            </div>
        </div>
    </div>

    @if(! $exists)
        <x-core::card>
            <x-core::card.body>
                <div class="text-center">
                    <p>{!! BaseHelper::clean(trans('plugins/translation::translation.no_translations', ['locale' => "<strong>{$locale['name']}</strong>"])) !!}</p>

                    <x-core::button color="primary" class="button-import-groups" :data-url="route('translations.import')">
                        {{ trans('plugins/translation::translation.import_group') }}
                    </x-core::button>
                </div>
            </x-core::card.body>
        </x-core::card>
    @else
        <div class="translations-table">
            {{ $translationTable->renderTable() }}
        </div>
    @endif
@endsection

@push('footer')
    <x-core::modal.action
        id="confirm-publish-modal"
        :title="trans('plugins/translation::translation.publish_translations')"
        :description="trans('plugins/translation::translation.confirm_publish_translations', ['locale' => $locale['name']])"
        type="warning"
        :submit-button-attrs="['class' => 'button-import-groups', 'data-url' => route('translations.import')]"
        :submit-button-label="trans('core/base::base.yes')"
    />
@endpush
