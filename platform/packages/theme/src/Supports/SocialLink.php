<?php

namespace Botble\Theme\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Media\Facades\RvMedia;
use Illuminate\Support\HtmlString;

class SocialLink
{
    public function __construct(
        protected string|null $name,
        protected string|null $url,
        protected string|null $icon,
        protected string|null $image,
        protected string|null $color,
        protected string|null $backgroundColor,
    ) {
    }

    public function getName(): string|null
    {
        return $this->name;
    }

    public function getIcon(): string|null
    {
        return $this->icon;
    }

    public function getUrl(): string|null
    {
        return $this->url;
    }

    public function getImage(): string|null
    {
        return $this->image;
    }

    public function getColor(): string|null
    {
        if ($this->color === 'transparent') {
            return null;
        }

        return $this->color;
    }

    public function getBackgroundColor(): string|null
    {
        if ($this->backgroundColor === 'transparent') {
            return null;
        }

        return $this->backgroundColor;
    }

    public function getAttributes(array $attributes = []): HtmlString
    {
        $backgroundColor = $this->getBackgroundColor();
        $color = $this->getColor();

        $attributes = [
            'href' => $this->getUrl(),
            'title' => $this->getName(),
            'target' => '_blank',
            'style' =>
                ($backgroundColor ? sprintf('background-color: %s !important;', $backgroundColor) : null) .
                ($color ? sprintf('color: %s !important;', $color) : null)
            ,
            ...$attributes,
        ];

        if (! $attributes['style']) {
            unset($attributes['style']);
        }

        return new HtmlString(Html::attributes($attributes));
    }

    public function getIconHtml(array $attributes = []): HtmlString|null
    {
        if ($this->image) {
            return RvMedia::image($this->image, $this->name, attributes: $attributes);
        }

        if (! $this->icon) {
            return null;
        }

        if (BaseHelper::hasIcon($this->icon)) {
            $icon = BaseHelper::renderIcon($this->icon, attributes: $attributes);
        } else {
            $icon = sprintf('<i class="%s"></i>', $this->icon);
        }

        return new HtmlString($icon);
    }
}
