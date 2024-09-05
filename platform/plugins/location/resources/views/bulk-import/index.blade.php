@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        $supportedLocales = defined('LANGUAGE_MODULE_SCREEN_NAME') ? Language::getSupportedLocales() : [];
    @endphp

    <x-core::card class="mb-3">
        <x-core::card.header>
            <x-core::card.title>{{ trans('plugins/location::bulk-import.import_available_data') }}</x-core::card.title>
        </x-core::card.header>

        <x-core::card.body>
            <x-core::form :url="route('location.bulk-import.import-location-data')" method="post" class="form-import-available-data">
                <x-core::alert type="warning">
                    {!! BaseHelper::clean(
                        trans(
                            'plugins/location::bulk-import.import_available_data_help',
                             ['link' => Html::link(route('country.index'), trans('plugins/location::country.name'))]
                         )
                    ) !!}
                </x-core::alert>
                <x-core::form.select
                    name="country_code"
                    :options="$countries"
                    :searchable="true"
                    :multiple="true"
                    :data-placeholder="trans('plugins/location::bulk-import.choose_country')"
                    :helper-text="trans('plugins/location::bulk-import.available_data_help', ['link' => Html::link('https://github.com/botble/locations', attributes: ['target' => '_blank'])])"
                />

                <x-core::button type="submit" color="primary">
                    {{ trans('plugins/location::bulk-import.import') }}
                </x-core::button>
            </x-core::form>
        </x-core::card.body>
    </x-core::card>

    <div class="bulk-import">
        <x-core::card class="mb-3">
            <x-core::card.header>
                <x-core::card.title>{{ trans('plugins/location::bulk-import.menu') }}</x-core::card.title>
            </x-core::card.header>

            <x-core::card.body>
                <x-core::form
                    method="post"
                    class="form-bulk-import"
                    files="true"
                    data-upload-url="{{ route('location.upload.process') }}"
                    data-validate-url="{{ route('location.upload.validate') }}"
                    data-import-url="{{ route('location.import') }}"
                >
                    <x-core::form-group>
                        <x-core::form.label
                            for="input-group-file">{{ trans('plugins/location::bulk-import.choose_file') }}</x-core::form.label>
                        <div
                            class="location-dropzone dropzone"
                            data-mimetypes="{{ $mimetypes }}"
                        >
                            <div class="dz-message">
                                {{ trans('plugins/location::bulk-import.upload_file_placeholder') }}<br>
                            </div>
                        </div>

                        <x-core::form.helper-text class="mt-1">
                            {{ trans('plugins/location::bulk-import.choose_file_with_mime', ['types' => implode(', ', config('plugins.location.general.bulk-import.mimes', []))]) }}
                        </x-core::form.helper-text>
                    </x-core::form-group>

                    <div class="mb-3 text-center p-2 border bg-body text-body">
                        <a
                            class="download-template"
                            data-url="{{ route('location.bulk-import.download-template') }}"
                            data-extension="csv"
                            data-filename="template_locations_import.csv"
                            data-downloading="<i class='fas fa-spinner fa-spin'></i> {{ trans('plugins/location::bulk-import.downloading') }}"
                            href="#"
                        >
                            <i class="fas fa-file-csv"></i>
                            {{ trans('plugins/location::bulk-import.download-csv-file') }}
                        </a>
                        &nbsp; | &nbsp;
                        <a
                            class="download-template"
                            data-url="{{ route('location.bulk-import.download-template') }}"
                            data-extension="xlsx"
                            data-filename="template_locations_import.xlsx"
                            data-downloading="<i class='fas fa-spinner fa-spin'></i> {{ trans('plugins/location::bulk-import.downloading') }}"
                            href="#"
                        >
                            <i class="fas fa-file-excel"></i>
                            {{ trans('plugins/location::bulk-import.download-excel-file') }}
                        </a>
                    </div>

                    <div class="d-grid">
                        <x-core::button
                            type="submit"
                            color="primary"
                            data-uploading-text="{{ __('plugins/location::bulk-import.uploading') }}"
                            data-validating-text="{{ __('plugins/location::bulk-import.validating') }}"
                            data-importing-text="{{ __('plugins/location::bulk-import.importing') }}"
                        >
                            {{ trans('plugins/location::bulk-import.start_import') }}
                        </x-core::button>
                    </div>
                </x-core::form>
            </x-core::card.body>
        </x-core::card>

        <x-core::alert
            type="info"
            class="bulk-import-message"
            style="display: none"
        ></x-core::alert>

        <x-core::card
            class="show-errors mb-3 bg-danger-lt"
            style="display: none"
        >
            <x-core::card.header>
                <x-core::card.title>{{ trans('plugins/location::bulk-import.failures') }}</x-core::card.title>
            </x-core::card.header>

            <div
                class="table-responsive overflow-auto"
                style="max-height: 100vh"
            >
                <x-core::table>
                    <x-core::table.header>
                        <x-core::table.header.cell>
                            #_Row
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Errors
                        </x-core::table.header.cell>
                    </x-core::table.header>

                    <x-core::table.body id="imported-listing"></x-core::table.body>
                </x-core::table>
            </div>
        </x-core::card>

        <x-core::card class="mb-3">
            <x-core::card.header>
                <x-core::card.title>{{ trans('plugins/location::bulk-import.template') }}</x-core::card.title>
            </x-core::card.header>
            <div class="table-responsive">
                <x-core::table>
                    <x-core::table.header>
                        <x-core::table.header.cell>
                            Name
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Slug
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Abbreviation
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            State
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Country
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Import Type
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Status
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Order
                        </x-core::table.header.cell>
                        @foreach ($supportedLocales as $localeCode => $properties)
                            @continue($localeCode === Language::getCurrentLocale())
                            <x-core::table.header.cell>
                                Name {{ Str::upper($properties['lang_code']) }}
                            </x-core::table.header.cell>
                        @endforeach
                    </x-core::table.header>

                    <x-core::table.body>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Texas
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>

                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                TX
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>

                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                United States
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                state
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                published
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                0
                            </x-core::table.body.cell>
                            @foreach ($supportedLocales as $localeCode => $properties)
                                @continue($localeCode === Language::getCurrentLocale())
                                <x-core::table.body.cell>
                                    Texas {{ Str::upper($properties['lang_code']) }}
                                </x-core::table.body.cell>
                            @endforeach
                        </x-core::table.body.row>

                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Washington
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>

                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                WA
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>

                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                United States
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                state
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                published
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                0
                            </x-core::table.body.cell>

                            @foreach ($supportedLocales as $localeCode => $properties)
                                @continue($localeCode === Language::getCurrentLocale())
                                <x-core::table.body.cell></x-core::table.body.cell>
                            @endforeach
                        </x-core::table.body.row>

                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Houston
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                houston
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>

                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                Texas
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                United States
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                city
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                published
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                0
                            </x-core::table.body.cell>

                            @foreach ($supportedLocales as $localeCode => $properties)
                                @continue($localeCode === Language::getCurrentLocale())
                                <x-core::table.body.cell></x-core::table.body.cell>
                            @endforeach
                        </x-core::table.body.row>

                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                San Antonio
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                san-antonio
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>

                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                Texas
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                United States
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                city
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                published
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                0
                            </x-core::table.body.cell>

                            @foreach ($supportedLocales as $localeCode => $properties)
                                @continue($localeCode === Language::getCurrentLocale())
                                <x-core::table.body.cell></x-core::table.body.cell>
                            @endforeach
                        </x-core::table.body.row>
                    </x-core::table.body>
                </x-core::table>
            </div>
        </x-core::card>

        <x-core::card>
            <x-core::card.header>
                <x-core::card.title>{{ trans('plugins/location::bulk-import.rules') }}</x-core::card.title>
            </x-core::card.header>

            <div class="table-responsive">
                <x-core::table>
                    <x-core::table.header>
                        <x-core::table.header.cell>
                            Column
                        </x-core::table.header.cell>
                        <x-core::table.header.cell>
                            Rules
                        </x-core::table.header.cell>
                    </x-core::table.header>
                    <x-core::table.body>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Name
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (required)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Slug
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (nullable)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Abbreviation
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (nullable|max:2)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                State
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (nullable|required_if:type,city)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Country
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (nullable|required_if:type,state,city)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Import Type
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (nullable|enum:country,state,city|default:state)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Status
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (required|enum:{{ implode(',', Botble\Base\Enums\BaseStatusEnum::values()) }}|default:{{ Botble\Base\Enums\BaseStatusEnum::PUBLISHED }})
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Order
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (nullable|integer|min:0|max:127|default:0)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                Nationality
                            </x-core::table.body.cell>
                            <x-core::table.body.cell>
                                (required_if:import_type,country|max:120)
                            </x-core::table.body.cell>
                        </x-core::table.body.row>

                        @foreach ($supportedLocales as $localeCode => $properties)
                            @continue($localeCode === Language::getCurrentLocale())
                            <x-core::table.body.row>
                                <x-core::table.body.cell>
                                    Name {{ $properties['lang_code'] }}
                                    <i
                                        class="fas fa-info-circle"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ trans('plugins/location::bulk-import.available_enable_multi_language') }}"
                                    ></i>
                                </x-core::table.body.cell>
                                <x-core::table.body.cell>
                                    (nullable|default:{Name})
                                </x-core::table.body.cell>
                            </x-core::table.body.row>
                        @endforeach
                    </x-core::table.body>
                </x-core::table>
            </div>
        </x-core::card>
    </div>
@endsection

@push('footer')
    <x-core::custom-template id="failure-template">
        <tr>
            <td scope="row">__row__</td>
            <td>__errors__</td>
        </tr>
    </x-core::custom-template>
    <x-core::custom-template id="preview-template">
        <div class="position-relative d-flex gap-3">
            <div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="height: 2rem; width: 2rem">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                </svg>
            </div>
            <div>
                <h4><span data-dz-name></span></h4>
                <div class="small text-muted">
                    <span data-dz-size></span>
                    <a href="#" class="ms-1 text-danger cursor-pointer" data-dz-remove>
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                <div class="text-danger small" data-dz-errormessage></div>
            </div>
        </div>
    </x-core::custom-template>
@endpush
