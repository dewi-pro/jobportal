<?php

namespace Botble\Theme\Providers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Supports\ServiceProvider;
use Botble\Dashboard\Events\RenderingDashboardWidgets;
use Botble\Dashboard\Supports\DashboardWidgetInstance;
use Botble\Media\Facades\RvMedia;
use Botble\Page\Models\Page;
use Botble\Page\Tables\PageTable;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Shortcode\Compilers\ShortcodeCompiler;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\AdminBar;
use Botble\Theme\Facades\Theme;
use Botble\Theme\Supports\ThemeSupport;
use Botble\Theme\Supports\Vimeo;
use Botble\Theme\Supports\Youtube;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        PageTable::beforeRendering(function () {
            add_filter(PAGE_FILTER_PAGE_NAME_IN_ADMIN_LIST, function (string $name, Page $page) {
                if (BaseHelper::isHomepage($page->getKey())) {
                    $name .= Html::tag('span', ' — ' . trans('packages/page::pages.front_page'), [
                        'class' => 'additional-page-name',
                    ])->toHtml();
                }

                return $name;
            }, 10, 2);
        });

        $this->app['events']->listen(RenderingDashboardWidgets::class, function () {
            if (! config('packages.theme.general.display_theme_manager_in_admin_panel', true)) {
                return;
            }

            add_filter(DASHBOARD_FILTER_ADMIN_LIST, [$this,  'addStatsWidgets'], 4, 2);
        });

        add_filter('get_http_exception_view', function (string $defaultView, HttpExceptionInterface $exception) {
            if (view()->exists($view = Theme::getThemeNamespace('views.' . $exception->getStatusCode()))) {
                return $view;
            }

            return $defaultView;
        }, 10, 2);

        add_filter('core_email_template_site_logo', function (string $defaultLogo): string {
            if ($logo = theme_option('logo')) {
                $defaultLogo = RvMedia::getImageUrl($logo);
            }

            return $defaultLogo;
        });

        $this->app['events']->listen(RenderingThemeOptionSettings::class, function () {
            theme_option()
                ->setSection([
                    'title' => trans('packages/theme::theme.theme_option_general'),
                    'priority' => 0,
                    'id' => 'opt-text-subsection-general',
                    'subsection' => true,
                    'icon' => 'ti ti-home',
                    'fields' => [
                        [
                            'id' => 'site_title',
                            'type' => 'text',
                            'label' => trans('core/setting::setting.general.site_title'),
                            'attributes' => [
                                'name' => 'site_title',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                    'placeholder' => trans('core/setting::setting.general.site_title'),
                                    'data-counter' => 255,
                                ],
                            ],
                        ],
                        [
                            'id' => 'show_site_name',
                            'section_id' => 'opt-text-subsection-general',
                            'type' => 'customSelect',
                            'label' => trans('core/setting::setting.general.show_site_name'),
                            'attributes' => [
                                'name' => 'show_site_name',
                                'list' => [
                                    '0' => __('No'),
                                    '1' => __('Yes'),
                                ],
                                'value' => '0',
                            ],
                        ],
                        [
                            'id' => 'seo_title',
                            'type' => 'text',
                            'label' => trans('core/setting::setting.general.seo_title'),
                            'attributes' => [
                                'name' => 'seo_title',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                    'placeholder' => trans('core/setting::setting.general.seo_title'),
                                    'data-counter' => 120,
                                ],
                            ],
                        ],
                        [
                            'id' => 'seo_description',
                            'type' => 'textarea',
                            'label' => trans('core/setting::setting.general.seo_description'),
                            'attributes' => [
                                'name' => 'seo_description',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                    'rows' => 4,
                                ],
                            ],
                        ],
                        [
                            'id' => 'seo_og_image',
                            'type' => 'mediaImage',
                            'label' => trans('packages/theme::theme.theme_option_seo_open_graph_image'),
                            'attributes' => [
                                'name' => 'seo_og_image',
                                'value' => null,
                            ],
                        ],
                        [
                            'id' => 'theme_breadcrumb_enabled',
                            'section_id' => 'opt-text-subsection-general',
                            'type' => 'customSelect',
                            'label' => trans('packages/theme::theme.breadcrumb_enabled'),
                            'attributes' => [
                                'name' => 'theme_breadcrumb_enabled',
                                'list' => [
                                    '1' => __('Yes'),
                                    '0' => __('No'),
                                ],
                                'value' => '1',
                            ],
                        ],
                    ],
                ])
                ->setSection([
                    'title' => trans('packages/theme::theme.theme_option_logo'),
                    'priority' => 0,
                    'id' => 'opt-text-subsection-logo',
                    'subsection' => true,
                    'icon' => 'ti ti-photo',
                    'fields' => [
                        [
                            'id' => 'favicon',
                            'type' => 'mediaImage',
                            'label' => trans('packages/theme::theme.theme_option_favicon'),
                            'attributes' => [
                                'name' => 'favicon',
                                'value' => null,
                                'attributes' => ['allow_thumb' => false],
                            ],
                        ],
                        [
                            'id' => 'logo',
                            'type' => 'mediaImage',
                            'label' => trans('packages/theme::theme.theme_option_logo'),
                            'attributes' => [
                                'name' => 'logo',
                                'value' => null,
                                'attributes' => ['allow_thumb' => false],
                            ],
                        ],
                    ],
                ]);
        });

        add_shortcode('media', 'Media', 'Media', function (Shortcode $shortcode) {
            $url = $shortcode->url;

            if (! $url) {
                return null;
            }

            $url = rtrim($url, '/');

            if (! $url) {
                return null;
            }

            $data = [];

            if ($shortcode->width) {
                $data['width'] = $shortcode->width;
            }

            if ($shortcode->height) {
                $data['height'] = $shortcode->height;
            }

            $type = null;

            if (Youtube::isYoutubeURL($url)) {
                $data['url'] = Youtube::getYoutubeVideoEmbedURL($url);

                $type = 'youtube';
            } elseif (Vimeo::isVimeoURL($url)) {
                $videoId = Vimeo::getVimeoID($url);
                if ($videoId) {
                    $data['url'] = 'https://player.vimeo.com/video/' . $videoId;

                    $type = 'vimeo';
                }
            } elseif (preg_match('/^.*https:\/\/(?:m|www|vm)?\.?tiktok\.com\/((?:.*\b(?:(?:usr|v|embed|user|video)\/|\?shareId=|\&item_id=)(\d+))|\w+)/', $url)) {
                $type = 'tiktok';

                $data['url'] = $url;
                $data['video_id'] = Str::afterLast($url, 'video/');

                Theme::asset()->container('footer')->add(
                    'tiktok-embed',
                    'https://www.tiktok.com/embed.js',
                    attributes: ['async' => true]
                );
            } elseif (preg_match('/^.*https:\/\/twitter\.com\/(?:#!\/)?(\w+)\/status(es)?\/(\d+)/', $url)) {
                $data['url'] = $url;

                $type = 'twitter';

                Theme::asset()->container('footer')->add(
                    'twitter-embed',
                    'https://platform.twitter.com/widgets.js',
                    attributes: ['async' => true, 'charset' => 'utf-8']
                );
            } elseif (in_array(Str::lower(File::extension($url)), ['mp4', 'webm', 'ogg'])) {
                $data['width'] = $shortcode->width ?: '100%';
                $data['height'] = $shortcode->height ?: 400;
                $data['extension'] = File::extension($url) ?: 'mp4';
                $data['url'] = $url;

                $type = 'video';
            }

            return view('packages/theme::shortcodes.media', ['type' => $type, 'data' => $data])->render();
        });

        shortcode()->setPreviewImage('media', asset('vendor/core/packages/theme/images/ui-blocks/media.jpg'));

        shortcode()->setAdminConfig('media', function (array $attributes) {
            return ShortcodeForm::createFromArray($attributes)
                ->add('url', TextField::class, [
                    'label' => __('Media URL'),
                    'attr' => [
                        'placeholder' => 'YouTube, Vimeo, TikTok, ...',
                    ],
                ])
                ->add('width', NumberField::class, [
                    'label' => __('Width'),
                    'default_value' => 420,
                ])
                ->add('height', NumberField::class, [
                    'label' => __('Height'),
                    'default_value' => 315,
                ]);
        });

        add_filter(THEME_FRONT_HEADER, function (string|null $html): string|null {
            $file = Theme::getStyleIntegrationPath();
            if ($this->app['files']->exists($file)) {
                $html .= PHP_EOL . Html::style(Theme::asset()->url('css/style.integration.css?v=' . filectime($file)));
            }

            return $html;
        }, 15);

        if (! BaseHelper::hasDemoModeEnabled()) {
            if (config('packages.theme.general.enable_custom_html_shortcode')) {
                add_shortcode('custom-html', __('Custom HTML'), __('Add custom HTML content'), function (Shortcode $shortcode) {
                    return html_entity_decode($shortcode->getContent());
                });

                shortcode()->setPreviewImage('custom-html', asset('vendor/core/packages/shortcode/images/placeholder-code.jpg'));

                shortcode()->setAdminConfig('custom-html', function (array $attributes, string|null $content) {
                    return ShortcodeForm::createFromArray($attributes)
                        ->add('content', 'textarea', [
                            'label' => __('Content'),
                            'attr' => [
                                'placeholder' => __('HTML code'),
                                'rows' => 3,
                                'data-shortcode-attribute' => 'content',
                            ],
                            'value' => $content,
                        ]);
                });
            }

            if (config('packages.theme.general.enable_custom_js')) {
                if (setting('custom_header_js')) {
                    add_filter(THEME_FRONT_HEADER, function (string|null $html): string {
                        return $html . ThemeSupport::getCustomJS('header');
                    }, 15);
                }

                if (setting('custom_body_js')) {
                    add_filter(THEME_FRONT_BODY, function (string|null $html): string {
                        return $html . ThemeSupport::getCustomJS('body');
                    }, 15);
                }

                if (setting('custom_footer_js')) {
                    add_filter(THEME_FRONT_FOOTER, function (string|null $html): string {
                        return $html . ThemeSupport::getCustomJS('footer');
                    }, 15);
                }
            }

            if (config('packages.theme.general.enable_custom_html')) {
                if (setting('custom_header_html')) {
                    add_filter(THEME_FRONT_HEADER, function (string|null $html): string {
                        return $html . ThemeSupport::getCustomHtml('header');
                    }, 16);
                }

                if (setting('custom_body_html')) {
                    add_filter(THEME_FRONT_BODY, function (string|null $html): string {
                        return $html . ThemeSupport::getCustomHtml('body');
                    }, 16);
                }

                if (setting('custom_footer_html')) {
                    add_filter(THEME_FRONT_FOOTER, function (string|null $html): string {
                        return $html . ThemeSupport::getCustomHtml('footer');
                    }, 16);
                }
            }
        }

        add_filter(THEME_FRONT_FOOTER, function (string|null $html): string|null {
            try {
                if (! Auth::guard()->check() || ! AdminBar::isDisplay() || ! (int)setting('show_admin_bar', 1)) {
                    return $html;
                }

                return $html . Html::style('vendor/core/packages/theme/css/admin-bar.css') . AdminBar::render();
            } catch (Throwable) {
                return $html;
            }
        }, 14);

        add_filter(
            'shortcode_content_compiled',
            function (string|null $html, string $name, $callback, ShortcodeCompiler $compiler) {
                $editLink = $compiler->getEditLink();

                if (! $editLink || ! setting('show_theme_guideline_link', false)) {
                    return $html;
                }

                Theme::asset()
                    ->usePath(false)
                    ->add('theme-guideline-css', asset('vendor/core/packages/theme/css/guideline.css'));

                $link = view('packages/theme::guideline-link', [
                    'html' => $html,
                    'editLink' => $editLink . '?shortcode=' . $compiler->getName(),
                    'editLabel' => __('Edit this shortcode'),
                ])->render();

                return ThemeSupport::insertBlockAfterTopHtmlTags($link, $html);
            },
            9999,
            4
        );

        add_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, function () {
            if (BaseHelper::getRichEditor() === 'ckeditor') {
                Theme::asset()
                    ->add('ckeditor-content-styles', 'vendor/core/core/base/libraries/ckeditor/content-styles.css');
            }
        }, 15);
    }

    public function addStatsWidgets(array $widgets, Collection $widgetSettings): array
    {
        $themes = count(BaseHelper::scanFolder(theme_path()));

        return (new DashboardWidgetInstance())
            ->setType('stats')
            ->setPermission('theme.index')
            ->setTitle($themes === 1 ? trans('packages/theme::theme.theme') : trans('packages/theme::theme.themes'))
            ->setKey('widget_total_themes')
            ->setIcon('ti ti-palette')
            ->setColor('pink')
            ->setStatsTotal($themes)
            ->setRoute(route('theme.index'))
            ->setColumn('col-12 col-md-6 col-lg-3')
            ->init($widgets, $widgetSettings);
    }
}
