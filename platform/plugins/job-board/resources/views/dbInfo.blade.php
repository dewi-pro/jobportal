@if ($databaseInfo)
    <x-core::datagrid>
        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/job-board::database-info.tables.time') }}
            </x-slot:title>

            {{ $databaseInfo->created_at }}
        </x-core::datagrid.item>

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/job-board::database-info.tables.position') }}
            </x-slot:title>

            <a href="{{ $databaseInfo->job->url }}" target="_blank">
                {{ $databaseInfo->job->name }}
                <x-core::icon name="ti ti-external-link" />
            </a>
        </x-core::datagrid.item>

        @if (!$databaseInfo->is_external_apply)
            <x-core::datagrid.item>
                <x-slot:title>
                    {{ trans('plugins/job-board::database-info.tables.name') }}
                </x-slot:title>

                @if ($databaseInfo->account->id && $databaseInfo->account->is_public_profile)
                    <a href="{{ $databaseInfo->account->url }}" target="_blank">
                        {{ $databaseInfo->account->name }}
                        <x-core::icon name="ti ti-external-link" />
                    </a>
                @else
                    {{ $databaseInfo->full_name }}
                @endif
            </x-core::datagrid.item>
        @endif

        @if ($databaseInfo->phone)
            <x-core::datagrid.item>
                <x-slot:title>
                    {{ trans('plugins/job-board::database-info.tables.phone') }}
                </x-slot:title>

                <a href="tel:{{ $databaseInfo->phone }}">{{ $databaseInfo->phone }}</a>
            </x-core::datagrid.item>
        @endif

        <x-core::datagrid.item>
            <x-slot:title>
                {{ trans('plugins/job-board::database-info.tables.email') }}
            </x-slot:title>

            <a href="mailto:{{ $databaseInfo->email }}">{{ $databaseInfo->email }}</a>
        </x-core::datagrid.item>

        @if (!$databaseInfo->is_external_apply)
            @if ($databaseInfo->resume)
                <x-core::datagrid.item>
                    <x-slot:title>
                        {{ trans('plugins/job-board::database-info.tables.resume') }}
                    </x-slot:title>

                    <a
                        href="{{ route(auth()->check() && is_in_admin(true) ? 'db.download-cv' : 'public.account.applicants.download-cv', $databaseInfo->id) }}"
                        target="_blank"
                        class="d-flex align-items-center gap-1"
                    >
                        {{ trans('plugins/job-board::database-info.tables.download_resume') }}
                        <x-core::icon name="ti ti-external-link" />
                    </a>
                </x-core::datagrid.item>
            @endif

            @if ($databaseInfo->cover_letter)
                    <x-core::datagrid.item>
                        <x-slot:title>
                            {{ trans('plugins/job-board::database-info.tables.cover_letter') }}
                        </x-slot:title>

                        <a href="{{ RvMedia::url($databaseInfo->cover_letter) }}" target="_blank" class="d-flex align-items-center gap-1">
                            {{ RvMedia::url($databaseInfo->cover_letter) }}
                            <x-core::icon name="ti ti-external-link" />
                        </a>
                    </x-core::datagrid.item>
            @endif
        @endif
    </x-core::datagrid>

    @if ($databaseInfo->message)
        <x-core::datagrid.item class="mt-4">
            <x-slot:title>
                {{ trans('plugins/job-board::database-info.tables.message') }}
            </x-slot:title>

            {{ $databaseInfo->message }}
        </x-core::datagrid.item>
    @endif
@endif
