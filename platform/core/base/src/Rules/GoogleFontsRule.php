<?php

namespace Botble\Base\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleFontsRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = __('The selected :attribute is invalid.', compact('attribute'));

        if ($value === null || $value === '') {
            $fail($message);
        }

        $googleFonts = config('core.base.general.google_fonts', []);

        $customGoogleFonts = config('core.base.general.custom_google_fonts');

        if ($customGoogleFonts) {
            $googleFonts = [...$googleFonts, ...explode(',', $customGoogleFonts)];
        }

        if (! in_array($value, $googleFonts)) {
            $fail($message);
        }
    }
}
