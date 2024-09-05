<?php

namespace Botble\Base\Forms\FieldTypes;

use Botble\Base\Traits\Forms\CanSpanColumns;

abstract class FormField extends \Kris\LaravelFormBuilder\Fields\FormField
{
    use CanSpanColumns;
}
