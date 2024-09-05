<?php

namespace Botble\Base\Supports;

use BadMethodCallException;
use Botble\Base\Traits\Componentable;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;

class HtmlBuilder
{
    use Macroable, Componentable {
        Macroable::__call as macroCall;
        Componentable::__call as componentCall;
    }

    protected UrlGenerator|null $url;

    protected Factory $view;

    public function __construct(UrlGenerator $url = null, Factory $view)
    {
        $this->url = $url;
        $this->view = $view;
    }

    public function entities($value): string
    {
        return htmlentities($value, ENT_QUOTES, 'UTF-8', false);
    }

    public function decode($value): string
    {
        return html_entity_decode($value, ENT_QUOTES, 'UTF-8');
    }

    public function script($url, $attributes = [], $secure = null): HtmlString
    {
        $attributes['src'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<script' . $this->attributes($attributes) . '></script>');
    }

    public function style($url, $attributes = [], $secure = null): HtmlString
    {
        $defaults = ['media' => 'all', 'type' => 'text/css', 'rel' => 'stylesheet'];

        $attributes = array_merge($defaults, $attributes);

        $attributes['href'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<link' . $this->attributes($attributes) . '>');
    }

    public function image($url, $alt = null, $attributes = [], $secure = null): HtmlString
    {
        $attributes['alt'] = $alt;

        return $this->toHtmlString(
            '<img src="' . $this->url->asset(
                $url,
                $secure
            ) . '"' . $this->attributes($attributes) . '>'
        );
    }

    public function favicon($url, $attributes = [], $secure = null): HtmlString
    {
        $defaults = ['rel' => 'shortcut icon', 'type' => 'image/x-icon'];

        $attributes = array_merge($defaults, $attributes);

        $attributes['href'] = $this->url->asset($url, $secure);

        return $this->toHtmlString('<link' . $this->attributes($attributes) . '>');
    }

    public function link($url, $title = null, $attributes = [], $secure = null, $escape = true): HtmlString
    {
        $url = $this->url->to($url, [], $secure);

        if (is_null($title) || $title === false) {
            $title = $url;
        }

        if ($escape) {
            $title = $this->entities($title);
        }

        return $this->toHtmlString(
            '<a href="' . $this->entities($url) . '"' . $this->attributes($attributes) . '>' . $title . '</a>'
        );
    }

    public function secureLink($url, $title = null, $attributes = [], $escape = true): HtmlString
    {
        return $this->link($url, $title, $attributes, true, $escape);
    }

    public function linkAsset($url, $title = null, $attributes = [], $secure = null, $escape = true): HtmlString
    {
        $url = $this->url->asset($url, $secure);

        return $this->link($url, $title ?: $url, $attributes, $secure, $escape);
    }

    public function linkSecureAsset($url, $title = null, $attributes = [], $escape = true): HtmlString
    {
        return $this->linkAsset($url, $title, $attributes, true, $escape);
    }

    public function linkRoute(
        $name,
        $title = null,
        $parameters = [],
        $attributes = [],
        $secure = null,
        $escape = true
    ): HtmlString {
        return $this->link($this->url->route($name, $parameters), $title, $attributes, $secure, $escape);
    }

    public function linkAction(
        $action,
        $title = null,
        $parameters = [],
        $attributes = [],
        $secure = null,
        $escape = true
    ): HtmlString {
        return $this->link($this->url->action($action, $parameters), $title, $attributes, $secure, $escape);
    }

    public function mailto($email, $title = null, $attributes = [], $escape = true): HtmlString
    {
        $email = $this->email($email);

        $title = $title ?: $email;

        if ($escape) {
            $title = $this->entities($title);
        }

        $email = $this->obfuscate('mailto:') . $email;

        return $this->toHtmlString('<a href="' . $email . '"' . $this->attributes($attributes) . '>' . $title . '</a>');
    }

    public function email($email): string
    {
        return str_replace('@', '&#64;', $this->obfuscate($email));
    }

    public function nbsp($num = 1): string
    {
        return str_repeat('&nbsp;', $num);
    }

    public function ol($list, $attributes = []): HtmlString|string
    {
        return $this->listing('ol', $list, $attributes);
    }

    public function ul($list, $attributes = []): HtmlString|string
    {
        return $this->listing('ul', $list, $attributes);
    }

    public function dl(array $list, array $attributes = []): HtmlString
    {
        $attributes = $this->attributes($attributes);

        $html = "<dl{$attributes}>";

        foreach ($list as $key => $value) {
            $value = (array)$value;

            $html .= "<dt>$key</dt>";

            foreach ($value as $v_value) {
                $html .= "<dd>$v_value</dd>";
            }
        }

        $html .= '</dl>';

        return $this->toHtmlString($html);
    }

    protected function listing($type, $list, array $attributes = []): HtmlString|string
    {
        $html = '';

        if (count($list) === 0) {
            return $html;
        }

        // Essentially we will just spin through the list and build the list of the HTML
        // elements from the array. We will also handled nested lists in case that is
        // present in the array. Then we will build out the final listing elements.
        foreach ($list as $key => $value) {
            $html .= $this->listingElement($key, $type, $value);
        }

        $attributes = $this->attributes($attributes);

        return $this->toHtmlString("<{$type}{$attributes}>{$html}</{$type}>");
    }

    protected function listingElement($key, $type, $value): HtmlString|string
    {
        if (is_array($value)) {
            return $this->nestedListing($key, $type, $value);
        } else {
            return '<li>' . e($value, false) . '</li>';
        }
    }

    protected function nestedListing($key, $type, $value): HtmlString|string
    {
        if (is_int($key)) {
            return $this->listing($type, $value);
        } else {
            return '<li>' . $key . $this->listing($type, $value) . '</li>';
        }
    }

    public function attributes($attributes): string
    {
        $html = [];

        foreach ((array)$attributes as $key => $value) {
            $element = $this->attributeElement($key, $value);

            if (! empty($element)) {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    protected function attributeElement($key, $value): string
    {
        // For numeric keys we will assume that the value is a boolean attribute
        // where the presence of the attribute represents a true value and the
        // absence represents a false value.
        // This will convert HTML attributes such as "required" to a correct
        // form instead of using incorrect numerics.
        if (is_numeric($key)) {
            return $value;
        }

        // Treat boolean attributes as HTML properties
        if (is_bool($value) && $key !== 'value') {
            return $value ? $key : '';
        }

        if (is_array($value) && $key === 'class') {
            return 'class="' . implode(' ', $value) . '"';
        }

        if (! is_null($value)) {
            return $key . '="' . e($value, false) . '"';
        }

        return '';
    }

    public function obfuscate($value): string
    {
        $safe = '';

        foreach (str_split($value) as $letter) {
            if (ord($letter) > 128) {
                return $letter;
            }

            // To properly obfuscate the value, we will randomly convert each letter to
            // its entity or hexadecimal representation, keeping a bot from sniffing
            // the randomly obfuscated letters out of the string on the responses.
            switch (rand(1, 3)) {
                case 1:
                    $safe .= '&#' . ord($letter) . ';';

                    break;

                case 2:
                    $safe .= '&#x' . dechex(ord($letter)) . ';';

                    break;

                case 3:
                    $safe .= $letter;
            }
        }

        return $safe;
    }

    public function meta($name, $content, array $attributes = []): HtmlString
    {
        $defaults = compact('name', 'content');

        $attributes = array_merge($defaults, $attributes);

        return $this->toHtmlString('<meta' . $this->attributes($attributes) . '>');
    }

    public function tag($tag, $content, array $attributes = []): HtmlString
    {
        $content = is_array($content) ? implode('', $content) : $content;

        return $this->toHtmlString(
            '<' . $tag . $this->attributes($attributes) . '>' . $this->toHtmlString($content) . '</' . $tag . '>'
        );
    }

    protected function toHtmlString($html): HtmlString
    {
        return new HtmlString($html);
    }

    public function __call($method, $parameters)
    {
        if (static::hasComponent($method)) {
            return $this->componentCall($method, $parameters);
        }

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exist.");
    }
}
