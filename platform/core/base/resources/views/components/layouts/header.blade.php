<style>
    [v-cloak],
    [x-cloak] {
        display: none;
    }
</style>

<link
    href="{{ BaseHelper::getGoogleFontsURL() }}"
    rel="preconnect"
>
<link
    href="{{ BaseHelper::getGoogleFontsURL(sprintf('css2?family=%s:wght@100;200;300;400;500;600;700;800;900&display=swap', setting('admin_primary_font', 'Inter'))) }}"
    rel="stylesheet"
>

<style>
    :root {
        --primary-font: "{{ setting('admin_primary_font', 'Inter') }}";
        --primary-color: {{ $primaryColor = setting('admin_primary_color', '#206bc4') }};
        --primary-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($primaryColor)) }};
        --secondary-color: {{ $secondaryColor = setting('admin_secondary_color', '#6c7a91') }};
        --secondary-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($secondaryColor)) }};
        --heading-color: {{ setting('admin_heading_color', 'inherit') }};
        --text-color: {{ $textColor = setting('admin_text_color', '#182433') }};
        --text-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($textColor)) }};
        --link-color: {{ $linkColor = setting('admin_link_color', '#206bc4') }};
        --link-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($linkColor)) }};
        --link-hover-color: {{ $linkHoverColor = setting('admin_link_hover_color', '#206bc4') }};
        --link-hover-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($linkHoverColor)) }};
    }
</style>

{!! Assets::renderHeader(['core']) !!}

