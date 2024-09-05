<?php

namespace Botble\Captcha\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool verify(string $response, string $clientIp, array $options = [])
 * @method static string|null display(array $attributes = [], array $options = [])
 * @method static array rules()
 * @method static bool isEnabled()
 * @method static bool reCaptchaEnabled()
 * @method static bool mathCaptchaEnabled()
 * @method static array mathCaptchaRules()
 * @method static string captchaType()
 * @method static string reCaptchaType()
 * @method static array attributes()
 * @method static array scores()
 *
 * @see \Botble\Captcha\Contracts\Captcha
 */
class Captcha extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'captcha';
    }
}
