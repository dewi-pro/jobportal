<?php

namespace Botble\Theme\Listeners;

use Botble\Base\Events\FormRendering;
use Botble\Base\Facades\AdminHelper;
use Botble\JsValidation\Facades\JsValidator;
use Botble\Theme\Facades\Theme;

class AddFormJsValidation
{
    public function handle(FormRendering $event): void
    {
        if (AdminHelper::isInAdmin()) {
            return;
        }

        $form = $event->form;

        if (! $form->getValidatorClass()) {
            return;
        }

        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add('js-validation', 'vendor/core/core/js-validation/js/js-validation.js', ['jquery']);

        Theme::asset()
            ->container('footer')
            ->writeContent(
                'js-validation-form-rules',
                JsValidator::formRequest($form->getValidatorClass(), '#' . $form->getFormOption('id'))->render(),
                ['jquery']
            );
    }
}
