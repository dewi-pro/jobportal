<?php

namespace Botble\Base\Forms\FieldOptions;

use Carbon\Carbon;

class DatePickerFieldOption extends InputFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->defaultValue(Carbon::now()->toDateString());
    }
}
