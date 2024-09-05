<footer>
    @php
        $hasLanguageSwitcher = false;
    @endphp
    @if (is_plugin_active('language'))
        @php
            $supportedLocales = Language::getSupportedLocales();
        @endphp

        @if ($supportedLocales && count($supportedLocales) > 1)
            @if (count(\Botble\Base\Supports\Language::getAvailableLocales()) > 1)
                @php
                    $hasLanguageSwitcher = true;
                @endphp
                <p class="d-inline-block mb-0">{{ __('Languages') }}:
                    @foreach ($supportedLocales as $localeCode => $properties)
                        <a
                            hreflang="{{ $localeCode }}"
                            href="{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}"
                            rel="alternate"
                            @if ($localeCode == Language::getCurrentLocale()) class="active" @endif
                        >
                            {!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}
                            <span class="d-inline-block ms-1">{{ $properties['lang_name'] }}</span>
                        </a> &nbsp;
                    @endforeach
                </p>
            @endif
        @endif
    @endif

    @php
        $currencies = get_all_currencies();
    @endphp

    @if ($currencies->count() > 1)
        @if ($hasLanguageSwitcher)
            | &nbsp;
        @endif
        <p class="d-inline-block mb-0">{{ __('Currencies') }}:
            @foreach ($currencies as $currency)
                <a
                    href="{{ route('public.change-currency', $currency->title) }}"
                    @if (get_application_currency_id() == $currency->id) class="active" @endif
                ><span>{{ $currency->title }}</span></a>
                @if (!$loop->last)
                    -
                @endif
            @endforeach
        </p>
    @endif
</footer>

<script src="{{ asset('vendor/core/plugins/job-board/js/app.js') }}"></script>
