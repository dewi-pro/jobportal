<?php

namespace Botble\Captcha;

use Botble\Captcha\Contracts\Captcha as CaptchaContract;
use Botble\Captcha\Events\CaptchaRendered;
use Botble\Captcha\Events\CaptchaRendering;
use Illuminate\Support\Facades\Http;

class Captcha extends CaptchaContract
{
    protected bool $rendered = false;

    public function display(array $attributes = [], array $options = []): string|null
    {
        if (! $this->siteKey || ! $this->reCaptchaEnabled()) {
            return null;
        }

        $name = 'captcha_' . md5(uniqid((string)rand(), true));

        $headContent = $this->headRender();
        $footerContent = $this->footerRender($name);

        CaptchaRendering::dispatch($attributes, $options, $headContent, $footerContent);

        if (defined('THEME_FRONT_HEADER')) {
            add_filter(THEME_FRONT_HEADER, function ($html) use ($headContent) {
                return $html . $headContent;
            }, 299);
        }

        if (defined('THEME_FRONT_FOOTER')) {
            add_filter(THEME_FRONT_FOOTER, function (string|null $html) use ($footerContent): string {
                return $html . $footerContent;
            }, 99);
        }

        $this->rendered = true;

        return
            tap(
                view('plugins/captcha::v2.html', ['name' => $name, 'siteKey' => $this->siteKey])->render(),
                fn (string $rendered) => CaptchaRendered::dispatch($rendered)
            );
    }

    public function verify(string $response, string $clientIp = null, array $options = []): bool
    {
        if (! $this->reCaptchaEnabled()) {
            return true;
        }

        if (empty($response)) {
            return false;
        }

        $response = Http::asForm()
            ->withoutVerifying()
            ->post(self::RECAPTCHA_VERIFY_API_URL, [
                'secret' => $this->secretKey,
                'response' => $response,
                'remoteip' => $clientIp,
            ]);

        return $response->json('success');
    }

    protected function headRender(): string
    {
        return view('plugins/captcha::header-meta')->render();
    }

    protected function footerRender(string $name): string
    {
        $isRendered = $this->rendered;

        $url = self::RECAPTCHA_CLIENT_API_URL . '?' . http_build_query([
                'onload' => 'onloadCallback',
                'render' => 'explicit',
                'hl' => app()->getLocale(),
            ]);

        return view('plugins/captcha::v2.script', compact('url', 'isRendered', 'name'))->render();
    }
}
