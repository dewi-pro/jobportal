@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>{{ trans('plugins/location::location.export_location') }}</x-core::card.title>
        </x-core::card.header>

        <x-core::card.body>
            <div class="row text-center py-5">
                <div class="col-sm-4">
                    <h3>{{ trans('plugins/location::location.total_country') }}</h3>
                    <h2 class="h1 text-primary font-bold">{{ number_format($countryCount) }}</h2>
                </div>
                <div class="col-sm-4">
                    <h3>{{ trans('plugins/location::location.total_state') }}</h3>
                    <h2 class="h1 text-info font-bold">{{ number_format($stateCount) }}</h2>
                </div>
                <div class="col-sm-4">
                    <h3>{{ trans('plugins/location::location.total_city') }}</h3>
                    <h2 class="h1 text-info font-bold">{{ number_format($cityCount) }}</h2>
                </div>
            </div>
            <div class="text-center">
                <x-core::button
                    class="btn-export-data"
                    color="info"
                    data-loading-text="{{ trans('plugins/location::location.exporting') }}"
                    data-filename="exported_location.csv"
                    href="{{ route('location.export.process') }}"
                >
                    {{ trans('plugins/location::location.start_export') }}
                </x-core::button>
            </div>
        </x-core::card.body>
    </x-core::card>
@stop
