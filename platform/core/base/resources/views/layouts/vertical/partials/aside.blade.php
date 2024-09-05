<aside
    class="navbar navbar-vertical navbar-expand-lg"
    data-bs-theme="dark"
>
    <div class="{{ AdminAppearance::getContainerWidth() }}">
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark">
            @include('core/base::partials.logo')
        </h1>
        <div class="navbar-nav flex-row d-lg-none">
            <x-core::dropdown
                wrapper-class="nav-item"
                :has-arrow="true"
                position="end"
            >
                <x-slot:trigger>
                    <a
                        href="#"
                        class="nav-link d-flex lh-1 text-reset p-0"
                        data-bs-toggle="dropdown"
                        aria-label="{{ __('Open user menu') }}"
                    >
                        <span
                            class="crop-image-original avatar avatar-sm"
                            style="background-image: url({{ Auth::guard()->user()->avatar_url }})"
                        ></span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ Auth::guard()->user()->name }}</div>
                            <div class="mt-1 small text-muted">{{ Auth::guard()->user()->email }}</div>
                        </div>
                    </a>
                </x-slot:trigger>

                <x-core::dropdown.item
                    :href="Auth::guard()->user()->url"
                    :label="trans('core/base::layouts.profile')"
                    icon="ti ti-user"
                />

                <x-core::dropdown.item
                    :href="route('access.logout')"
                    :label="trans('core/base::layouts.logout')"
                    icon="ti ti-logout"
                />
            </x-core::dropdown>
        </div>

        @include('core/base::layouts.vertical.partials.sidebar')
    </div>
</aside>
