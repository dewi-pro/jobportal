<?php

namespace Botble\Theme\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Form;
use Botble\Base\Facades\Html;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FormHelper;
use Botble\Shortcode\Compilers\Shortcode;
use Botble\Shortcode\Forms\ShortcodeForm;
use Botble\Theme\Events\RenderingThemeOptionSettings;
use Botble\Theme\Facades\ThemeOption;
use Botble\Theme\Forms\Fields\ThemeIconField;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ThemeSupport
{
    public static function registerYoutubeShortcode(string $viewPath = null): void
    {
        $viewPath = $viewPath ?: 'packages/theme::shortcodes';

        shortcode()
            ->register(
                'youtube-video',
                __('YouTube video'),
                __('Add YouTube video'),
                function ($shortcode) use ($viewPath) {
                    $url = Youtube::getYoutubeVideoEmbedURL($shortcode->content);
                    $width = $shortcode->width;
                    $height = $shortcode->height;

                    return view($viewPath . '.youtube', compact('url', 'width', 'height'))
                        ->render();
                }
            )
            ->setPreviewImage('youtube-video', asset('vendor/core/packages/theme/images/ui-blocks/youtube-video.jpg'))
            ->setAdminConfig('youtube-video', function ($attributes, $content) {
                return ShortcodeForm::createFromArray($attributes)
                    ->add('url', TextField::class, [
                        'label' => __('YouTube URL'),
                        'attr' => [
                            'placeholder' => 'https://www.youtube.com/watch?v=SlPhMPnQ58k ',
                            'data-shortcode-attribute' => 'content',
                        ],
                        'value' => $content,
                    ])
                    ->add('width', NumberField::class, [
                        'label' => __('Width'),
                    ])
                    ->add('height', NumberField::class, [
                        'label' => __('Height'),
                    ]);
            });
    }

    public static function registerGoogleMapsShortcode(string $viewPath = null): void
    {
        $viewPath = $viewPath ?: 'packages/theme::shortcodes';

        shortcode()
            ->register(
                'google-map',
                __('Google Maps'),
                __('Add Google Maps iframe'),
                function (Shortcode $shortcode) use ($viewPath) {
                    $address = $shortcode->content;

                    if (! $address) {
                        return '';
                    }

                    $width = $shortcode->width ?: '100%';
                    $height = $shortcode->height ?: '500';

                    return view($viewPath . '.google-map', compact('address', 'width', 'height'))
                        ->render();
                }
            )
            ->setPreviewImage('google-map', asset('vendor/core/packages/theme/images/ui-blocks/google-map.jpg'))
            ->setAdminConfig('google-map', function (array $attributes, string|null $content) {
                return ShortcodeForm::createFromArray($attributes)
                    ->add('address', 'textarea', [
                        'label' => __('Address'),
                        'attr' => [
                            'data-shortcode-attribute' => 'content',
                            'placeholder' => '24 Roberts Street, SA73, Chester',
                            'rows' => 3,
                        ],
                        'value' => $content,
                    ])
                    ->add('width', NumberField::class, [
                        'label' => __('Width'),
                    ])
                    ->add('height', NumberField::class, [
                        'label' => __('Height'),
                        'default_value' => 500,
                    ]);
            });
    }

    public static function getCustomJS(string $location): string
    {
        $js = setting('custom_' . $location . '_js');

        if (empty($js)) {
            return '';
        }

        if ((! Str::contains($js, '<script') || ! Str::contains($js, '</script>')) && ! Str::contains(
            $js,
            '<noscript'
        ) && ! Str::contains($js, '</noscript>')) {
            $js = Html::tag('script', $js);
        }

        return $js;
    }

    public static function getCustomHtml(string $location): string
    {
        $html = setting('custom_' . $location . '_html');

        if (empty($html)) {
            return '';
        }

        return $html;
    }

    public static function insertBlockAfterTopHtmlTags(string|null $block, string|null $html): string|null
    {
        if (! $block || ! $html) {
            return $html;
        }

        preg_match_all('/^<([a-z]+)([^>]+)*(?:>(.*)<\/\1>|\s+\/>)$/sm', $html, $matches);

        if (empty($matches[0])) {
            return $html;
        }

        $parsedHtml = '';

        foreach ($matches[0] as $blockItem) {
            $parsedHtml .= Str::replaceLast('</', $block . '</', $blockItem);
        }

        return $parsedHtml;
    }

    public static function registerPreloader(): void
    {
        add_filter(THEME_FRONT_HEADER, function (string|null $html): string {
            if (theme_option('preloader_enabled', 'no') != 'yes') {
                return $html;
            }

            $preloader = null;

            if (theme_option('preloader_version', 'v1') === 'v1') {
                $preloader = view('packages/theme::fronts.preloader')->render();
            }

            return $html . apply_filters('theme_preloader', $preloader);
        }, 16);

        app('events')->listen(RenderingThemeOptionSettings::class, function () {
            theme_option()
                ->setField([
                    'id' => 'preloader_enabled',
                    'section_id' => 'opt-text-subsection-general',
                    'type' => 'customSelect',
                    'label' => __('Enable Preloader?'),
                    'attributes' => [
                        'name' => 'preloader_enabled',
                        'list' => [
                            'yes' => __('Yes'),
                            'no' => __('No'),
                        ],
                        'value' => 'no',
                        'options' => [
                            'class' => 'form-control',
                        ],
                    ],
                ])
                ->when(count(static::getPreloaderVersions()) > 1, function () {
                    return theme_option()
                        ->setField([
                            'id' => 'preloader_version',
                            'section_id' => 'opt-text-subsection-general',
                            'type' => 'customSelect',
                            'label' => __('Preloader Version'),
                            'attributes' => [
                                'name' => 'preloader_version',
                                'list' => static::getPreloaderVersions(),
                                'value' => 'v1',
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ]);
                });
        });
    }

    public static function getPreloaderVersions(): array
    {
        return apply_filters('theme_preloader_versions', [
            'v1' => __('Default'),
        ]);
    }

    public static function registerToastNotification(): void
    {
        add_filter(THEME_FRONT_FOOTER, function (string|null $html): string {
            $toastNotification = view('packages/theme::fronts.toast-notification')->render();

            return $html . apply_filters('theme_toast_notification', $toastNotification);
        }, 16);
    }

    public static function registerThemeIconFields(array $icons, array $css = [], array $js = []): void
    {
        Form::component('themeIcon', 'packages/theme::forms.fields.icons-field', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        add_filter('form_custom_fields', function (FormAbstract $form, FormHelper $formHelper) {
            if ($formHelper->hasCustomField('themeIcon')) {
                return $form;
            }

            return $form->addCustomField('themeIcon', ThemeIconField::class);
        }, 29, 2);

        add_filter('theme_icon_js_code', function (string|null $html) use ($css, $js) {
            $cssHtml = '';
            $jsHtml = '';

            foreach ($css as $cssItem) {
                $cssHtml .= Html::style($cssItem)->toHtml();
            }

            foreach ($js as $jsItem) {
                $jsHtml .= Html::style($jsItem)->toHtml();
            }

            return $html . $cssHtml . $jsHtml;
        });

        add_filter('theme_icon_list_icons', function (array $defaultIcons) use ($icons) {
            return array_merge($defaultIcons, $icons);
        });
    }

    public static function registerFacebookIntegration(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function () {
            theme_option()
                ->setSection([
                    'title' => __('Facebook Integration'),
                    'id' => 'opt-text-subsection-facebook-integration',
                    'subsection' => true,
                    'icon' => 'ti ti-brand-facebook',
                    'fields' => [
                        [
                            'id' => 'facebook_chat_enabled',
                            'type' => 'customSelect',
                            'label' => __('Enable Facebook chat?'),
                            'attributes' => [
                                'name' => 'facebook_chat_enabled',
                                'list' => [
                                    'no' => __('No'),
                                    'yes' => __('Yes'),
                                ],
                                'value' => 'no',
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                            'helper' => __(
                                'To show chat box on that website, please go to :link and add :domain to whitelist domains!',
                                [
                                    'domain' => Html::link(url('')),
                                    'link' => Html::link(
                                        sprintf(
                                            'https://www.facebook.com/%s/settings/?tab=messenger_platform',
                                            theme_option('facebook_page_id', '[PAGE_ID]')
                                        )
                                    ),
                                ]
                            ),
                        ],
                        [
                            'id' => 'facebook_page_id',
                            'type' => 'text',
                            'label' => __('Facebook page ID'),
                            'helper' => __(
                                'You can get fan page ID using this site :link',
                                ['link' => Html::link('https://findidfb.com')]
                            ),
                            'attributes' => [
                                'name' => 'facebook_page_id',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                        [
                            'id' => 'facebook_comment_enabled_in_post',
                            'type' => 'customSelect',
                            'label' => __('Enable Facebook comment in post detail page?'),
                            'attributes' => [
                                'name' => 'facebook_comment_enabled_in_post',
                                'list' => [
                                    'yes' => __('Yes'),
                                    'no' => __('No'),
                                ],
                                'value' => 'no',
                                'options' => [
                                    'class' => 'form-control',
                                ],
                            ],
                        ],
                        [
                            'id' => 'facebook_app_id',
                            'type' => 'text',
                            'label' => __('Facebook App ID'),
                            'attributes' => [
                                'name' => 'facebook_app_id',
                                'value' => null,
                                'options' => [
                                    'class' => 'form-control',
                                ],
                                'placeholder' => 'Ex: 2061237023872679',
                            ],
                            'helper' => __(
                                'You can create your app in :link',
                                ['link' => Html::link('https://developers.facebook.com/apps')]
                            ),
                        ],
                        [
                            'id' => 'facebook_admins',
                            'type' => 'repeater',
                            'label' => __('Facebook Admins'),
                            'attributes' => [
                                'name' => 'facebook_admins',
                                'value' => null,
                                'fields' => [
                                    [
                                        'type' => 'text',
                                        'label' => __('Facebook Admin ID'),
                                        'attributes' => [
                                            'name' => 'text',
                                            'value' => null,
                                            'options' => [
                                                'class' => 'form-control',
                                                'data-counter' => 40,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'helper' => __(
                                'Facebook admins to manage comments :link',
                                ['link' => Html::link('https://developers.facebook.com/docs/plugins/comments')]
                            ),
                        ],
                    ],
                ]);
        });

        add_filter(THEME_FRONT_HEADER, function (string|null $html): string|null {
            if (theme_option('facebook_app_id')) {
                $html .= Html::meta('', theme_option('facebook_app_id'), ['property' => 'fb:app_id'])->toHtml();
            }

            if (theme_option('facebook_admins')) {
                foreach (json_decode(theme_option('facebook_admins'), true) as $facebookAdminId) {
                    if (Arr::get($facebookAdminId, '0.value')) {
                        $html .= Html::meta('', Arr::get($facebookAdminId, '0.value'), ['property' => 'fb:admins'])
                            ->toHtml();
                    }
                }
            }

            if (theme_option('facebook_chat_enabled', 'no') == 'yes' && theme_option('facebook_page_id')) {
                $html .= '<link href="//connect.facebook.net" rel="dns-prefetch" />';
            }

            return $html;
        }, 1180);

        add_filter(THEME_FRONT_FOOTER, function (string|null $html): string {
            return $html . view('packages/theme::partials.facebook-integration')->render();
        }, 1180);

        add_filter(BASE_FILTER_PUBLIC_COMMENT_AREA, function ($html) {
            if (
                theme_option('facebook_comment_enabled_in_post', 'yes') == 'yes' ||
                theme_option('facebook_comment_enabled_in_gallery', 'yes') == 'yes' ||
                theme_option('facebook_comment_enabled_in_product', 'yes') == 'yes'
            ) {
                return $html . view('packages/theme::partials.facebook-comments')->render();
            }

            return $html;
        }, 1180);
    }

    public static function registerSocialLinks(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function () {
            ThemeOption::setSection([
                'title' => __('Social Links'),
                'id' => 'opt-text-subsection-social-links',
                'subsection' => true,
                'icon' => 'ti ti-share',
                'fields' => [
                    [
                        'id' => 'social_links',
                        'type' => 'repeater',
                        'label' => __('Social Links'),
                        'attributes' => [
                            'name' => 'social_links',
                            'value' => null,
                            'fields' => static::getSocialLinksRepeaterFields(),
                        ],
                    ],
                ],
            ]);
        });
    }

    public static function getSocialLinksRepeaterFields(): array
    {
        return [
            [
                'type' => 'text',
                'label' => __('Name'),
                'attributes' => [
                    'name' => 'name',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'coreIcon',
                'label' => __('Icon'),
                'attributes' => [
                    'name' => 'icon',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'text',
                'label' => __('URL'),
                'attributes' => [
                    'name' => 'url',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                    ],
                ],
            ],
            [
                'type' => 'mediaImage',
                'label' => __('Icon Image (It will override icon above if set)'),
                'attributes' => [
                    'name' => 'image',
                    'value' => null,
                ],
            ],
            [
                'type' => 'customColor',
                'label' => __('Color'),
                'attributes' => [
                    'name' => 'color',
                    'value' => 'transparent',
                    'options' => [
                        'default_value' => 'transparent',
                    ],
                ],
            ],
            [
                'type' => 'customColor',
                'label' => __('Background color'),
                'attributes' => [
                    'name' => 'background-color',
                    'value' => null,
                    'options' => [
                        'default_value' => 'transparent',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<SocialLink>
     */
    public static function getSocialLinks(): array
    {
        $data = theme_option('social_links');

        if (empty($data)) {
            return [];
        }

        $data = json_decode($data, true);

        if (empty($data)) {
            return [];
        }

        return static::convertSocialLinksToArray($data);
    }

    public static function convertSocialLinksToArray(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $socialLinks = [];

        foreach ($data as $item) {
            $item = Arr::pluck($item, 'value', 'key');

            $socialLinks[] = new SocialLink(
                name: Arr::get($item, 'name') ?: Arr::get($item, 'social-name'),
                url: Arr::get($item, 'url') ?: Arr::get($item, 'social-url'),
                icon: Arr::get($item, 'icon') ?: Arr::get($item, 'social-icon'),
                image: Arr::get($item, 'image') ?: Arr::get($item, 'social-image'),
                color: Arr::get($item, 'color'),
                backgroundColor: Arr::get($item, 'background-color')
            );
        }

        return $socialLinks;
    }

    public static function getThemeIcons(): array
    {
        return apply_filters('theme_icon_list_icons', []);
    }

    public static function registerSiteCopyright(): void
    {
        app('events')->listen(RenderingThemeOptionSettings::class, function () {
            ThemeOption::setField([
                'id' => 'copyright',
                'section_id' => 'opt-text-subsection-general',
                'type' => 'textarea',
                'label' => __('Copyright'),
                'attributes' => [
                    'name' => 'copyright',
                    'value' => null,
                    'options' => [
                        'class' => 'form-control',
                        'placeholder' => __('Change copyright'),
                        'data-counter' => 255,
                        'rows' => 3,
                    ],
                ],
                'helper' => __('Copyright on footer of site. Using %Y to display current year.'),
            ]);
        });
    }

    public static function getSiteCopyright(): string|null
    {
        $copyright = theme_option('copyright');

        if ($copyright) {
            $copyright = str_replace('%Y', Carbon::now()->format('Y'), $copyright);
        }

        $copyright = apply_filters('theme_site_copyright', $copyright);

        if (! $copyright) {
            return null;
        }

        return BaseHelper::clean(nl2br($copyright));
    }
}
