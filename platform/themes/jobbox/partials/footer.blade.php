        {!! dynamic_sidebar('pre_footer_sidebar') !!}
    </main>
    <footer class="footer mt-50">
        <div class="container">
            <div class="row">
                {!! dynamic_sidebar('footer_sidebar') !!}
            </div>
            <div class="footer-bottom mt-50">
                <div class="row">
                    <div class="col-md-6">
                        <span class="font-xs color-text-paragraph">
                            {!! BaseHelper::clean(theme_option('copyright')) !!}
                        </span>
                    </div>
                    <div class="col-md-6 text-md-end text-start">
                        <div class="footer-social">
                            {!!
                                Menu::renderMenuLocation('footer-menu', [
                                    'options' => ['class' => 'footer_menu'],
                                    'view'    => 'support-menu',
                                ])
                            !!}
                        </div>
                        <div class="nav float-right language-switcher-footer">
                            @if (is_plugin_active('language'))
                                @include(JobBoardHelper::viewPath('dashboard.partials.language-switcher'))
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script>
        @if (is_plugin_active('job-board'))
            window.currencies = {!! json_encode(get_currencies_json()) !!};
        @endif
    </script>

    {!! Theme::footer() !!}

    @if (session()->has('status') || session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg))
        <script type="text/javascript">
            'use strict';
            window.onload = function () {
                @if (session()->has('success_msg'))
                window.showAlert('alert-success', "{!! addslashes(session('success_msg')) !!}");
                @endif
                @if (session()->has('status'))
                window.showAlert('alert-success', "{!! addslashes(session('status')) !!}");
                @endif
                @if (session()->has('error_msg'))
                window.showAlert('alert-danger', "{!! addslashes(session('error_msg')) !!}");
                @endif
                @if (isset($error_msg))
                window.showAlert('alert-danger', "{!! addslashes($error_msg) !!}");
                @endif
                @if (isset($errors))
                @foreach ($errors->all() as $error)
                window.showAlert('alert-danger', "{!! addslashes($error) !!}");
                @endforeach
                @endif
            };
        </script>
    @endif
</body>
</html>
