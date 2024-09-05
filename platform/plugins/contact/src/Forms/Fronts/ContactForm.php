<?php

namespace Botble\Contact\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Captcha\Facades\Captcha;
use Botble\Captcha\Forms\Fields\MathCaptchaField;
use Botble\Contact\Http\Requests\ContactRequest;
use Closure;

class ContactForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->contentOnly()
            ->setUrl(route('public.send.contact'))
            ->setValidatorClass(ContactRequest::class)
            ->setFormOption('class', 'contact-form')
            ->add(
                'filters_before_form',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(apply_filters('pre_contact_form', null))
                    ->toArray()
            )
            ->addRowWrapper(
                'name_and_email',
                function (self $form) {
                    $form
                        ->addColumnWrapper('name', function (self $form) {
                            $form->add(
                                'name',
                                TextField::class,
                                TextFieldOption::make()
                                    ->required()
                                    ->label(__('Name'))
                                    ->placeholder(__('Name'))
                                    ->wrapperAttributes(['class' => 'contact-form-group'])
                                    ->cssClass('contact-form-input')
                                    ->maxLength(-1)
                                    ->toArray()
                            );
                        })
                        ->addColumnWrapper('email', function (self $form) {
                            $form->add(
                                'email',
                                TextField::class,
                                TextFieldOption::make()
                                    ->required()
                                    ->label(__('Email'))
                                    ->placeholder(__('Email'))
                                    ->wrapperAttributes(['class' => 'contact-form-group'])
                                    ->cssClass('contact-form-input')
                                    ->maxLength(-1)
                                    ->toArray()
                            );
                        });
                }
            )
            ->addRowWrapper(
                'address_and_phone',
                function (self $form) {
                    $form
                        ->addColumnWrapper('address', function (self $form) {
                            $form->add(
                                'address',
                                TextField::class,
                                TextFieldOption::make()
                                    ->label(__('Address'))
                                    ->placeholder(__('Address'))
                                    ->wrapperAttributes(['class' => 'contact-form-group'])
                                    ->cssClass('contact-form-input')
                                    ->maxLength(-1)
                                    ->toArray()
                            );
                        })
                        ->addColumnWrapper('phone', function (self $form) {
                            $form->add(
                                'phone',
                                TextField::class,
                                TextFieldOption::make()
                                    ->label(__('Phone'))
                                    ->placeholder(__('Phone'))
                                    ->wrapperAttributes(['class' => 'contact-form-group'])
                                    ->cssClass('contact-form-input')
                                    ->maxLength(-1)
                                    ->toArray()
                            );
                        });
                }
            )
            ->addRowWrapper(
                'subject',
                function (self $form) {
                    $form->addColumnWrapper(
                        'subject',
                        function (self $form) {
                            $form->add(
                                'subject',
                                TextField::class,
                                TextFieldOption::make()
                                    ->label(__('Subject'))
                                    ->placeholder(__('Subject'))
                                    ->wrapperAttributes(['class' => 'contact-form-group'])
                                    ->cssClass('contact-form-input')
                                    ->maxLength(-1)
                                    ->toArray()
                            );
                        },
                        12
                    );
                }
            )
            ->addRowWrapper(
                'content',
                function (self $form) {
                    $form->addColumnWrapper(
                        'content',
                        function (self $form) {
                            $form->add(
                                'content',
                                TextareaField::class,
                                TextareaFieldOption::make()
                                    ->required()
                                    ->label(__('Message'))
                                    ->placeholder(__('Message'))
                                    ->wrapperAttributes(['class' => 'contact-form-group'])
                                    ->cssClass('contact-form-input')
                                    ->rows(5)
                                    ->maxLength(-1)
                                    ->toArray()
                            );
                        },
                        12
                    );
                }
            )
            ->when(is_plugin_active('captcha'), function (self $form) {
                $form
                    ->when(Captcha::reCaptchaEnabled(), function (self $form) {
                        $form->addRowWrapper(
                            'captcha',
                            function (self $form) {
                                $form->addColumnWrapper(
                                    'captcha',
                                    function (self $form) {
                                        $form->addWrappedField(
                                            'captcha',
                                            HtmlField::class,
                                            HtmlFieldOption::make()
                                                ->content(Captcha::display())
                                                ->toArray()
                                        );
                                    },
                                    12
                                );
                            }
                        );
                    })
                    ->when(Captcha::mathCaptchaEnabled() && setting('enable_math_captcha_for_contact_form', 0), function (self $form) {
                        $form->add(
                            'math_captcha',
                            MathCaptchaField::class,
                        );
                    });
            })
            ->add(
                'filters_after_form',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(apply_filters('after_contact_form', null))
                    ->toArray()
            )
            ->addWrappedField(
                'button',
                'submit',
                ButtonFieldOption::make()
                    ->cssClass('contact-button')
                    ->label(__('Send'))
                    ->toArray()
            )
            ->addWrappedField(
                'messages',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(<<<'HTML'
                        <div class="contact-message contact-success-message" style="display: none"></div>
                        <div class="contact-message contact-error-message" style="display: none"></div>
                    HTML)
                    ->toArray()
            );
    }

    protected function addWrappedField(string $name, string $type, array $options): static
    {
        $this->add(
            "open_{$name}_field_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('<div class="contact-form-group">')->toArray()
        );

        $this->add($name, $type, $options);

        return $this->add(
            "close_{$name}_field_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')->toArray()
        );
    }

    protected function addRowWrapper(string $name, Closure $callback): static
    {
        $this->add(
            "open_{$name}_row_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('<div class="contact-form-row row">')->toArray()
        );

        $callback($this);

        return $this->add(
            "close_{$name}_row_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')->toArray()
        );
    }

    protected function addColumnWrapper(string $name, Closure $callback, int $column = 6): static
    {
        $this->add(
            "open_{$name}_column_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content(sprintf('<div class="contact-column-%s col-md-%s">', $column, $column))->toArray()
        );

        $callback($this);

        return $this->add(
            "close_{$name}_column_wrapper",
            HtmlField::class,
            HtmlFieldOption::make()->content('</div>')->toArray()
        );
    }
}
