<?php

use Botble\Base\Facades\MetaBox;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Blog\Models\Post;
use Botble\JobBoard\Forms\AccountForm;
use Botble\JobBoard\Forms\Fronts\AccountSettingForm;
use Botble\JobBoard\Models\Account;
use Botble\JobBoard\Models\Category;
use Botble\JobBoard\Models\Job;
use Botble\Media\Facades\RvMedia;
use Botble\Menu\Facades\Menu;
use Botble\Page\Models\Page;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

register_page_template([
    'default' => __('Default'),
    'page-detail' => __('Page detail full width'),
    'page-detail-boxed' => __('Page detail boxed'),
    'homepage' => __('Homepage'),
]);

register_sidebar([
    'id' => 'footer_sidebar',
    'name' => __('Footer sidebar'),
    'description' => __('Widgets in footer of page'),
]);

register_sidebar([
    'id' => 'pre_footer_sidebar',
    'name' => __('Pre footer sidebar'),
    'description' => __('Widgets at the bottom of the page.'),
]);

register_sidebar([
    'id' => 'blog_sidebar',
    'name' => __('Blog sidebar'),
    'description' => __('Widgets at the right of the page.'),
]);

register_sidebar([
    'id' => 'candidate_sidebar',
    'name' => __('Candidate sidebar'),
    'description' => __('Widgets at the right of the page candidate detail.'),
]);

register_sidebar([
    'id' => 'company_sidebar',
    'name' => __('Company sidebar'),
    'description' => __('Widgets at the right of the page company detail.'),
]);

Menu::addMenuLocation('footer-menu', 'Footer navigation');

app()->booted(function () {
    RvMedia::addSize('featured', 403, 257);

    if (is_plugin_active('job-board')) {
        AccountSettingForm::beforeRendering(function (AccountSettingForm $form) {
            return $form->remove(['slug']);
        });

        AccountSettingForm::beforeSaving(function (AccountSettingForm $form) {
            $request = $form->getRequest();
            $model = $form->getModel();

            if ($request->has('cover_image')) {
                $coverImageUrl = $request->input('cover_image');
                if ($request->hasFile('cover_image')) {
                    $result = RvMedia::handleUpload($request->file('cover_image'), 0, $model->upload_folder);

                    $coverImageUrl = $result['data']->url;
                }

                MetaBox::saveMetaBoxData($model, 'cover_image', $coverImageUrl);
            }
        });

        FormAbstract::extend(function (FormAbstract $form) {
            if ($form instanceof AccountSettingForm || $form instanceof AccountForm) {
                $form
                    ->addAfter(
                        'description',
                        'linkedin',
                        TextField::class,
                        TextFieldOption::make()
                            ->label(__('LinkedIn URL'))
                            ->metadata()
                            ->toArray()
                    );
            }

            if ($form instanceof AccountForm) {
                $form->add(
                    'cover_image',
                    MediaImageField::class,
                    MediaImageFieldOption::make()
                        ->label(__('Cover Image'))
                        ->metadata()
                        ->toArray()
                );
            }

            return $form;
        });
    }

    add_filter(BASE_FILTER_BEFORE_RENDER_FORM, function (FormAbstract $form, ?Model $data) {
        switch (get_class($data)) {
            case Category::class:
                $form
                    ->addAfter('status', 'job_category_image', 'mediaImage', [
                        'label' => __('Image'),
                        'value' => MetaBox::getMetaData($data, 'job_category_image', true),
                    ])
                    ->addAfter('job_category_image', 'icon_image', 'mediaImage', [
                        'label' => __('Icon Image'),
                        'value' => MetaBox::getMetaData($data, 'icon_image', true),
                    ]);

                break;
            case Post::class:
                $form
                    ->add('cover_image', 'mediaImage', [
                        'label' => __('Cover Image'),
                        'label_attr' => ['class' => 'control-label'],
                        'value' => MetaBox::getMetaData($data, 'cover_image', true),
                    ])
                    ->addAfter('status', 'time_to_read', 'number', [
                        'label' => __('Time to read'),
                        'value' => MetaBox::getMetaData($data, 'time_to_read', true),
                        'attr' => [
                            'placeholder' => __('Time to read (minute)'),
                            'class' => ['image-data'],
                        ],
                    ]);

                break;
            case Page::class:
                $form
                    ->add('background_breadcrumb', 'mediaImage', [
                        'label' => __('Background Breadcrumb'),
                        'label_attr' => ['class' => 'control-label'],
                        'value' => MetaBox::getMetaData($data, 'background_breadcrumb', true),
                    ]);

                break;
            case Job::class:
                if (auth()->check()) {
                    $form
                        ->addBefore('categories[]', 'featured_image', 'mediaImage', [
                            'label' => __('Featured Image'),
                            'label_attr' => ['class' => 'control-label'],
                            'value' => MetaBox::getMetaData($data, 'featured_image', true),
                        ]);
                } else {
                    $form
                        ->addAfter('status', 'featured_image', 'mediaImage', [
                            'label' => __('Featured Image'),
                            'label_attr' => ['class' => 'control-label'],
                            'value' => MetaBox::getMetaData($data, 'featured_image', true),
                        ]);
                }

                break;
        }

        return $form;
    }, 120, 3);

    add_action([BASE_ACTION_AFTER_CREATE_CONTENT, BASE_ACTION_AFTER_UPDATE_CONTENT], function (string $screen, Request $request, $data): void {
        if ($data instanceof Post && $request->has('time_to_read')) {
            MetaBox::saveMetaBoxData($data, 'time_to_read', $request->input('time_to_read'));
        }

        if ($data instanceof Category) {
            if ($request->has('job_category_image')) {
                MetaBox::saveMetaBoxData($data, 'job_category_image', $request->input('job_category_image'));
            }

            if ($request->has('icon_image')) {
                MetaBox::saveMetaBoxData($data, 'icon_image', $request->input('icon_image'));
            }
        }

        if ($data instanceof Post) {
            MetaBox::saveMetaBoxData($data, 'cover_image', $request->input('cover_image'));
        }

        if ($data instanceof Page) {
            MetaBox::saveMetaBoxData($data, 'background_breadcrumb', $request->input('background_breadcrumb'));
        }

        if ($data instanceof Job && $request->has('featured_image')) {
            MetaBox::saveMetaBoxData($data, 'featured_image', $request->input('featured_image'));
        }
    }, 120, 3);

    add_filter('account_settings_page', function (?string $html, Account $account) {
        return $html . Theme::partial('account-custom-fields', compact('account'));
    }, 127, 2);
});

if (! function_exists('get_currencies_json')) {
    function get_currencies_json(): array
    {
        $currency = get_application_currency();
        $numberAfterDot = $currency->decimals ?: 0;

        return [
            'display_big_money' => config('plugins.real-estate.real-estate.display_big_money_in_million_billion'),
            'billion' => __('billion'),
            'million' => __('million'),
            'is_prefix_symbol' => $currency->is_prefix_symbol,
            'symbol' => $currency->symbol,
            'title' => $currency->title,
            'decimal_separator' => setting('job_board_decimal_separator', '.'),
            'thousands_separator' => setting('job_board_thousands_separator', ','),
            'number_after_dot' => $numberAfterDot,
            'show_symbol_or_title' => true,
        ];
    }
}
