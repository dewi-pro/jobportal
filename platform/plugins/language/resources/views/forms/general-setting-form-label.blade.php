<x-core::form.helper-text class="mt-2">
    {!! BaseHelper::clean(trans(
        'plugins/language::language.setup_site_language',
        [
            'link' => Html::link(route('languages.index'), trans('plugins/language::language.name')),
            'appearance_link' => Html::link(route('settings.admin-appearance'), trans('core/setting::setting.appearance.title')),
        ]
    )) !!}
</x-core::form.helper-text>
