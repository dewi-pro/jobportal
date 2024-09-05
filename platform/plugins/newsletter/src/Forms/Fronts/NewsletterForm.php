<?php

namespace Botble\Newsletter\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\FormAbstract;
use Botble\Captcha\Facades\Captcha;
use Botble\Captcha\Forms\Fields\ReCaptchaField;

class NewsletterForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->contentOnly()
            ->setUrl(route('public.newsletter.subscribe'))
            ->setFormOption('class', 'subscribe-form')
            ->add('wrapper_before', HtmlField::class, HtmlFieldOption::make()->content('<div class="input-group mb-3">')->toArray())
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(false)
                    ->cssClass('')
                    ->wrapperAttributes(false)
                    ->maxLength(-1)
                    ->placeholder(__('Enter Your Email'))
                    ->toArray()
            )
            ->add(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->label(__('Subscribe'))
                    ->cssClass('btn btn-primary')
                    ->toArray(),
            )
            ->add('wrapper_after', HtmlField::class, HtmlFieldOption::make()->content('</div>')->toArray())
            ->when(is_plugin_active('captcha') && Captcha::reCaptchaEnabled(), function (FormAbstract $form) {
                $form->add(
                    'captcha',
                    ReCaptchaField::class,
                );
            });
    }
}
