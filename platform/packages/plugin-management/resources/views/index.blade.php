@extends(BaseHelper::getAdminMasterLayoutTemplate())

@push('header-action')
    @if (
        $isEnabledMarketplaceFeature = config('packages.plugin-management.general.enable_marketplace_feature')
        && auth()->user()->hasPermission('plugins.marketplace')
    )
        <x-core::button
            tag="a"
            :href="route('plugins.new')"
            color="primary"
            icon="ti ti-plus"
            class="ms-auto"
        >
            {{ trans('packages/plugin-management::plugin.plugins_add_new') }}
        </x-core::button>
    @endif
@endpush

@section('content')
    <x-core::form.text-input
        type="search"
        name="search"
        :placeholder="__('Search...')"
        :group-flat="true"
    >
        <x-slot:prepend>
            <span class="input-group-text">
                <x-core::icon name="ti ti-search" />
            </span>
        </x-slot:prepend>
    </x-core::form.text-input>

    <div class="row row-cards plugin-list">
        @foreach ($plugins as $plugin)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 plugin-item" data-plugin="{{ Js::encode($plugin) }}">
                <x-core::card class="h-100">
                    <div
                        class="position-relative img-responsive img-responsive-3x1 card-img-top border-bottom"
                        @style(['background-color: #efefef', "background-image: url('$plugin->image')" => $plugin->image])
                    >
                        @if (! $plugin->image)
                            <x-core::icon class="position-absolute" style="top: calc(50% - 28px); left: calc(50% - 28px)" name="ti ti-puzzle" size="lg" />
                        @endif
                    </div>

                    <x-core::card.body class="d-flex flex-column justify-content-between">
                        <div>
                            <x-core::card.title class="text-truncate mb-2" title="{{ $plugin->name }}">
                                {{ $plugin->name }}
                            </x-core::card.title>
                            @if ($plugin->description)
                                <p class="text-secondary text-truncate" title="{{ $plugin->description }}">
                                    {{ $plugin->description }}
                                </p>
                            @endif
                        </div>

                        <div class="row g-1 g-lg-0">
                            @if (!config('packages.plugin-management.general.hide_plugin_author', false) && $plugin->author)
                                <div class="col-12 col-lg">
                                    {{ trans('packages/plugin-management::plugin.author') }}:
                                    @if ($plugin->url)
                                        <a href="{{ $plugin->url }}" target="_blank" class="fw-bold">{{ $plugin->author }}</a>
                                    @else
                                        <strong>{{ $plugin->author }}</strong>
                                    @endif
                                </div>
                            @endif
                            @if ($plugin->version)
                                <div class="col-12 col-lg-auto">
                                    {{ trans('packages/plugin-management::plugin.version') }}:
                                    <strong>{{ $plugin->version }}</strong>
                                </div>
                            @endif
                        </div>
                    </x-core::card.body>

                    <x-core::card.footer>
                        <div class="btn-list">
                            @if (auth()->user()->hasPermission('plugins.edit'))
                                <x-core::button
                                    type="button"
                                    :color="$plugin->status ? 'warning' : 'primary'"
                                    class="btn-trigger-change-status"
                                    data-plugin="{{ $plugin->path }}"
                                    data-status="{{ $plugin->status }}"
                                    :data-check-requirement-url="route('plugins.check-requirement', ['name' => $plugin->path])"
                                    :data-change-status-url="route('plugins.change.status', ['name' => $plugin->path])"
                                >
                                    @if ($plugin->status)
                                        {{ trans('packages/plugin-management::plugin.deactivate') }}
                                    @else
                                        {{ trans('packages/plugin-management::plugin.activate') }}
                                    @endif
                                </x-core::button>
                            @endif

                            @if ($isEnabledMarketplaceFeature)
                                <x-core::button
                                    class="btn-trigger-update-plugin"
                                    color="success"
                                    style="display: none;"
                                    data-name="{{ $plugin->path }}"
                                    data-check-update="{{ $plugin->id ?? 'plugin-' . $plugin->path }}"
                                    :data-check-update-url="route('plugins.marketplace.ajax.check-update')"
                                    :data-update-url="route('plugins.marketplace.ajax.update', ['id' => '__id__', 'name' => $plugin->path])"
                                    data-version="{{ $plugin->version }}"
                                >
                                    {{ trans('packages/plugin-management::plugin.update') }}
                                </x-core::button>
                            @endif

                            @if (auth()->user()->hasPermission('plugins.remove'))
                                <x-core::button
                                    type="button"
                                    class="btn-trigger-remove-plugin"
                                    data-plugin="{{ $plugin->path }}"
                                    :data-url="route('plugins.remove', ['plugin' => $plugin->path])"
                                >
                                    {{ trans('packages/plugin-management::plugin.remove') }}
                                </x-core::button>
                            @endif
                        </div>
                    </x-core::card.footer>
                </x-core::card>
            </div>
        @endforeach
    </div>

    <x-core::empty-state
        :title="trans('No plugins found')"
        :subtitle="trans('It looks as there are no plugins here.')"
        style="display: none;"
    />

    <x-core::modal.action
        id="remove-plugin-modal"
        type="danger"
        :title="trans('packages/plugin-management::plugin.remove_plugin')"
        :description="trans('packages/plugin-management::plugin.remove_plugin_confirm_message')"
        :submit-button-attrs="['id' => 'confirm-remove-plugin-button']"
        :submit-button-label="trans('packages/plugin-management::plugin.remove_plugin_confirm_yes')"
    />

    @if ($isEnabledMarketplaceFeature)
        <x-core::modal
            id="confirm-install-plugin-modal"
            :title="trans('packages/plugin-management::plugin.install_plugin')"
            button-id="confirm-install-plugin-button"
            :button-label="trans('packages/plugin-management::plugin.install')"
        >
            <input
                type="hidden"
                name="plugin_name"
                value=""
            >
            <input
                type="hidden"
                name="ids"
                value=""
            >
            <p id="requirement-message"></p>
        </x-core::modal>
    @endif
@endsection
