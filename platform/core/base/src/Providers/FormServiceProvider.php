<?php

namespace Botble\Base\Providers;

use Botble\Base\Facades\Form;
use Botble\Base\Forms\Form as PlainFormClass;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Forms\FormHelper;
use Botble\Base\Supports\FormBuilder as FormCollectiveBuilder;
use Botble\Base\Supports\HtmlBuilder;
use Botble\Base\Supports\ServiceProvider;
use Illuminate\Config\Repository;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\BladeCompiler;

class FormServiceProvider extends ServiceProvider
{
    protected bool $defer = true;

    public function register(): void
    {
        /**
         * @var Repository $config
         */
        $config = $this->app['config'];

        $config->set([
            'laravel-form-builder' => [
                ...$config->get('laravel-form-builder', []),
                'defaults.wrapper_class' => 'mb-3 position-relative',
                'defaults.label_class' => 'form-label',
                'defaults.field_error_class' => 'is-invalid',
                'defaults.help_block_class' => 'form-hint',
                'defaults.error_class' => 'invalid-feedback',
                'defaults.help_block_tag' => 'small',
                'defaults.select' => [
                    'field_class' => 'form-select',
                ],
                'plain_form_class' => PlainFormClass::class,
                'form_builder_class' => FormBuilder::class,
                'form_helper_class' => FormHelper::class,
            ],
        ]);

        $this->app->alias('html', HtmlBuilder::class);
        $this->app->alias('form', FormCollectiveBuilder::class);

        $this->registerHtmlBuilder();
        $this->registerFormBuilder();
        $this->registerBladeDirectives();
    }

    public function boot(): void
    {
        Form::component('mediaImage', 'core/base::forms.partials.image', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('mediaImages', 'core/base::forms.partials.images', [
            'name',
            'values' => [],
            'attributes' => [],
        ]);

        Form::component('mediaFile', 'core/base::forms.partials.file', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('modalAction', 'core/base::forms.partials.modal', [
            'name',
            'title',
            'type' => null,
            'content' => null,
            'action_id' => null,
            'action_name' => null,
            'modal_size' => null,
        ]);

        Form::component('helper', 'core/base::forms.partials.helper', ['content', 'icon']);

        Form::component('onOff', 'core/base::forms.partials.on-off', [
            'name',
            'value' => false,
            'attributes' => [],
        ]);

        Form::component('onOffCheckbox', 'core/base::forms.partials.on-off-checkbox', [
            'name',
            'value' => false,
            'attributes' => [],
        ]);

        /**
         * Custom checkbox
         * Every checkbox will not have the same name
         */
        Form::component('customCheckbox', 'core/base::forms.partials.custom-checkbox', [
            /**
             * @var array $values
             * @template: [
             *      [string $name, string $value, string $label, bool $selected, bool $disabled],
             *      [string $name, string $value, string $label, bool $selected, bool $disabled],
             *      [string $name, string $value, string $label, bool $selected, bool $disabled],
             * ]
             */
            'values',
        ]);

        /**
         * Custom radio
         * Every radio in list must have the same name
         */
        Form::component('customRadio', 'core/base::forms.partials.custom-radio', [
            /**
             * @var string $name
             */
            'name',
            /**
             * @var array $values
             * @template: [
             *      [string $value, string $label, bool $disabled],
             *      [string $value, string $label, bool $disabled],
             *      [string $value, string $label, bool $disabled],
             * ]
             */
            'values',
            /**
             * @var string|null $selected
             */
            'selected' => null,
            'attributes' => [],
        ]);

        Form::component('error', 'core/base::forms.partials.error', [
            'name',
            'errors' => null,
        ]);

        Form::component('editor', 'core/base::forms.partials.editor', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('ckeditor', 'core/base::forms.partials.ckeditor', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('tinymce', 'core/base::forms.partials.tinymce', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('customSelect', 'core/base::forms.partials.custom-select', [
            'name',
            'choices' => [],
            'selected' => null,
            'selectAttributes' => [],
            'optionsAttributes' => [],
            'optgroupsAttributes' => [],
        ]);

        Form::component('autocomplete', 'core/base::forms.partials.autocomplete', [
            'name',
            'choices' => [],
            'selected' => null,
            'selectAttributes' => [],
            'optionsAttributes' => [],
            'optgroupsAttributes' => [],
        ]);

        Form::component('googleFonts', 'core/base::forms.partials.google-fonts', [
            'name',
            'selected' => null,
            'selectAttributes' => [],
            'optionsAttributes' => [],
        ]);

        Form::component('customColor', 'core/base::forms.partials.color', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('repeater', 'core/base::forms.partials.repeater', [
            'name',
            'value' => null,
            'fields' => [],
            'attributes' => [],
        ]);

        Form::component('phoneNumber', 'core/base::forms.partials.phone-number', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('datePicker', 'core/base::forms.partials.date-picker', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('timePicker', 'core/base::forms.partials.time-picker', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('codeEditor', 'core/base::forms.partials.code-editor', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);

        Form::component('nestedSelectDropdown', 'core/base::forms.partials.nested-select-dropdown', [
            'name',
            'choices' => [],
            'selected' => null,
            'attributes' => [],
            'selectAttributes' => [],
            'optionsAttributes' => [],
            'optgroupsAttributes' => [],
        ]);

        Form::component('uiSelector', 'core/base::forms.partials.ui-selector', [
            'name',
            'value' => null,
            'choices' => [],
            'attributes' => [],
        ]);

        Form::component('multiChecklist', 'core/base::forms.partials.multi-checklist', [
            'name',
            'value' => null,
            'choices' => [],
            'attributes' => [],
            'emptyValue' => null,
            'inline' => false,
            'asDropdown' => false,
            'ajaxUrl' => null,
        ]);

        Form::component('coreIcon', 'core/base::forms.partials.core-icon', [
            'name',
            'value' => null,
            'attributes' => [],
        ]);
    }

    public function provides(): array
    {
        return ['html', 'form', HtmlBuilder::class, FormBuilder::class];
    }

    protected function registerHtmlBuilder(): void
    {
        $this->app->singleton('html', function ($app) {
            return new HtmlBuilder($app['url'], $app['view']);
        });
    }

    protected function registerFormBuilder(): void
    {
        $this->app->singleton('form', function ($app) {
            $form = new FormCollectiveBuilder(
                $app['html'],
                $app['url'],
                $app['view'],
                $app['session.store']->token(),
                $app['request']
            );

            return $form->setSessionStore($app['session.store']);
        });
    }

    protected function registerBladeDirectives(): void
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {
            $namespaces = [
                'Html' => get_class_methods(HtmlBuilder::class),
                'Form' => get_class_methods(FormCollectiveBuilder::class),
            ];

            $directives = [
                'entities',
                'decode',
                'script',
                'style',
                'image',
                'favicon',
                'link',
                'secureLink',
                'linkAsset',
                'linkSecureAsset',
                'linkRoute',
                'linkAction',
                'mailto',
                'email',
                'ol',
                'ul',
                'dl',
                'meta',
                'tag',
                'open',
                'model',
                'close',
                'token',
                'label',
                'input',
                'text',
                'password',
                'hidden',
                'email',
                'tel',
                'number',
                'date',
                'datetime',
                'datetimeLocal',
                'time',
                'url',
                'file',
                'textarea',
                'select',
                'selectRange',
                'selectYear',
                'selectMonth',
                'getSelectOption',
                'checkbox',
                'radio',
                'reset',
                'image',
                'color',
                'submit',
                'button',
                'old',
            ];

            foreach ($namespaces as $namespace => $methods) {
                foreach ($methods as $method) {
                    if (! in_array($method, $directives)) {
                        continue;
                    }

                    $snakeMethod = Str::snake($method);
                    $directive = strtolower($namespace) . '_' . $snakeMethod;

                    $bladeCompiler->directive($directive, function ($expression) use ($namespace, $method) {
                        return "<?php echo $namespace::$method($expression); ?>";
                    });
                }
            }
        });
    }
}
