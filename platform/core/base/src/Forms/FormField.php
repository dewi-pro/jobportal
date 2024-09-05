<?php

namespace Botble\Base\Forms;

use Botble\Base\Traits\Forms\CanSpanColumns;
use Kris\LaravelFormBuilder\Fields\FormField as BaseFormField;

abstract class FormField extends BaseFormField
{
    use CanSpanColumns;
}
