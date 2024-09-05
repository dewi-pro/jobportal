<?php

namespace Botble\Base\Supports;

use BadMethodCallException;
use Botble\Base\Traits\Componentable;
use DateTime;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Session\Session;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Traits\Macroable;

class FormBuilder
{
    use Macroable, Componentable {
        Macroable::__call as macroCall;
        Componentable::__call as componentCall;
    }

    protected HtmlBuilder $html;

    protected UrlGenerator|null $url;

    protected Factory $view;

    protected string $csrfToken;

    protected bool $considerRequest = false;

    protected Session $session;

    protected mixed $model;

    protected array $labels = [];

    protected Request $request;

    protected array $reserved = ['method', 'url', 'route', 'action', 'files'];

    protected array $spoofedMethods = ['DELETE', 'PATCH', 'PUT'];

    protected array $skipValueTypes = ['file', 'password', 'checkbox', 'radio'];

    protected string|null $type = null;

    protected array $payload;

    public function __construct(HtmlBuilder $html, UrlGenerator $url, Factory $view, $csrfToken, Request $request = null)
    {
        $this->url = $url;
        $this->html = $html;
        $this->view = $view;
        $this->csrfToken = $csrfToken;
        $this->request = $request;
    }

    public function open(array $options = []): HtmlString
    {
        $method = Arr::get($options, 'method', 'post');

        // We need to extract the proper method from the attributes. If the method is
        // something other than GET or POST we'll use POST since we will spoof the
        // actual method since forms don't support the reserved methods in HTML.
        $attributes['method'] = $this->getMethod($method);

        $attributes['action'] = $this->getAction($options);

        $attributes['accept-charset'] = 'UTF-8';

        // If the method is PUT, PATCH or DELETE we will need to add a spoofer hidden
        // field that will instruct the Symfony request to pretend the method is a
        // different method than it actually is, for convenience from the forms.
        $append = $this->getAppendage($method);

        if (isset($options['files']) && $options['files']) {
            $options['enctype'] = 'multipart/form-data';
        }

        // Finally we're ready to create the final form HTML field. We will attribute
        // format the array of attributes. We will also add on the appendage which
        // is used to spoof requests for this PUT, PATCH, etc. methods on forms.
        $attributes = array_merge(
            $attributes,
            Arr::except($options, $this->reserved)
        );

        // Finally, we will concatenate all the attributes into a single string, so
        // we can build out the final form open statement. We'll also append on an
        // extra value for the hidden _method field if it's needed for the form.
        $attributes = $this->html->attributes($attributes);

        return $this->toHtmlString('<form' . $attributes . '>' . $append);
    }

    public function model($model, array $options = []): HtmlString
    {
        $this->model = $model;

        return $this->open($options);
    }

    public function setModel($model): void
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function close(): HtmlString
    {
        $this->labels = [];

        $this->model = null;

        return $this->toHtmlString('</form>');
    }

    public function token(): HtmlString|string
    {
        $token = ! empty($this->csrfToken) ? $this->csrfToken : $this->session->token();

        return $this->hidden('_token', $token);
    }

    public function label($name, $value = null, $options = [], $escape_html = true): HtmlString
    {
        $this->labels[] = $name;

        $options = $this->html->attributes($options);

        $value = $this->formatLabel($name, $value);

        if ($escape_html) {
            $value = $this->html->entities($value);
        }

        return $this->toHtmlString('<label for="' . $name . '"' . $options . '>' . $value . '</label>');
    }

    protected function formatLabel($name, $value): string
    {
        return $value ?: ucwords(str_replace('_', ' ', $name));
    }

    public function input($type, $name, $value = null, $options = []): HtmlString
    {
        $this->type = $type;

        if (! isset($options['name'])) {
            $options['name'] = $name;
        }

        // We will get the appropriate value for the given field. We will look for the
        // value in the session for the value in the old input data then we'll look
        // in the model instance if one is set. Otherwise we will just use empty.
        $id = $this->getIdAttribute($name, $options);

        if (! in_array($type, $this->skipValueTypes)) {
            $value = $this->getValueAttribute($name, $value);
        }

        // Once we have the type, value, and ID we can merge them into the rest of the
        // attributes array so we can convert them into their HTML attribute format
        // when creating the HTML element. Then, we will return the entire input.
        $merge = compact('type', 'value', 'id');

        $options = array_merge($options, $merge);

        return $this->toHtmlString('<input' . $this->html->attributes($options) . '>');
    }

    public function text($name, $value = null, $options = []): HtmlString
    {
        return $this->input('text', $name, $value, $options);
    }

    public function password($name, $options = []): HtmlString
    {
        return $this->input('password', $name, '', $options);
    }

    public function range($name, $value = null, $options = []): HtmlString
    {
        return $this->input('range', $name, $value, $options);
    }

    public function hidden($name, $value = null, $options = []): HtmlString
    {
        return $this->input('hidden', $name, $value, $options);
    }

    public function search($name, $value = null, $options = []): HtmlString
    {
        return $this->input('search', $name, $value, $options);
    }

    public function email($name, $value = null, $options = []): HtmlString
    {
        return $this->input('email', $name, $value, $options);
    }

    public function tel($name, $value = null, $options = []): HtmlString
    {
        return $this->input('tel', $name, $value, $options);
    }

    public function number($name, $value = null, $options = []): HtmlString
    {
        return $this->input('number', $name, $value, $options);
    }

    public function date($name, $value = null, $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d');
        }

        return $this->input('date', $name, $value, $options);
    }

    public function datetime($name, $value = null, $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format(DateTime::RFC3339);
        }

        return $this->input('datetime', $name, $value, $options);
    }

    public function datetimeLocal($name, $value = null, $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m-d\TH:i');
        }

        return $this->input('datetime-local', $name, $value, $options);
    }

    public function time($name, $value = null, $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format('H:i');
        }

        return $this->input('time', $name, $value, $options);
    }

    public function url($name, $value = null, $options = []): HtmlString
    {
        return $this->input('url', $name, $value, $options);
    }

    public function week($name, $value = null, $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-\WW');
        }

        return $this->input('week', $name, $value, $options);
    }

    public function file($name, $options = []): HtmlString
    {
        return $this->input('file', $name, null, $options);
    }

    public function textarea($name, $value = null, $options = []): HtmlString
    {
        $this->type = 'textarea';

        if (! isset($options['name'])) {
            $options['name'] = $name;
        }

        // Next we will look for the rows and cols attributes, as each of these are put
        // on the textarea element definition. If they are not present, we will just
        // assume some sane default values for these attributes for the developer.
        $options = $this->setTextAreaSize($options);

        $options['id'] = $this->getIdAttribute($name, $options);

        $value = (string) $this->getValueAttribute($name, $value);

        unset($options['size']);

        // Next we will convert the attributes into a string form. Also we have removed
        // the size attribute, as it was merely a short-cut for the rows and cols on
        // the element. Then we'll create the final textarea elements HTML for us.
        $options = $this->html->attributes($options);

        return $this->toHtmlString('<textarea' . $options . '>' . e($value, false) . '</textarea>');
    }

    protected function setTextAreaSize($options): array
    {
        if (isset($options['size'])) {
            return $this->setQuickTextAreaSize($options);
        }

        // If the "size" attribute was not specified, we will just look for the regular
        // columns and rows attributes, using sane defaults if these do not exist on
        // the attributes array. We'll then return this entire options array back.
        $cols = Arr::get($options, 'cols', 50);

        $rows = Arr::get($options, 'rows', 10);

        return array_merge($options, compact('cols', 'rows'));
    }

    protected function setQuickTextAreaSize($options): array
    {
        $segments = explode('x', $options['size']);

        return array_merge($options, ['cols' => $segments[0], 'rows' => $segments[1]]);
    }

    public function select(
        $name,
        $list = [],
        $selected = null,
        array $selectAttributes = [],
        array $optionsAttributes = [],
        array $optgroupsAttributes = []
    ): HtmlString {
        $this->type = 'select';

        // When building a select box the "value" attribute is really the selected one
        // so we will use that when checking the model or session for a value which
        // should provide a convenient method of re-populating the forms on post.
        $selected = $this->getValueAttribute($name, $selected);

        $selectAttributes['id'] = $this->getIdAttribute($name, $selectAttributes);

        if (! isset($selectAttributes['name'])) {
            $selectAttributes['name'] = $name;
        }

        // We will simply loop through the options and build an HTML value for each of
        // them until we have an array of HTML declarations. Then we will join them
        // all together into one single HTML element that can be put on the form.
        $html = [];

        if (isset($selectAttributes['placeholder'])) {
            $html[] = $this->placeholderOption($selectAttributes['placeholder'], $selected);
            unset($selectAttributes['placeholder']);
        }

        foreach ($list as $value => $display) {
            $optionAttributes = $optionsAttributes[$value] ?? [];
            $optgroupAttributes = $optgroupsAttributes[$value] ?? [];
            $html[] = $this->getSelectOption($display, $value, $selected, $optionAttributes, $optgroupAttributes);
        }

        // Once we have all of this HTML, we can join this into a single element after
        // formatting the attributes into an HTML "attributes" string, then we will
        // build out a final select statement, which will contain all the values.
        $selectAttributes = $this->html->attributes($selectAttributes);

        $list = implode('', $html);

        return $this->toHtmlString("<select{$selectAttributes}>{$list}</select>");
    }

    public function selectRange($name, $begin, $end, $selected = null, $options = []): HtmlString
    {
        $range = array_combine($range = range($begin, $end), $range);

        return $this->select($name, $range, $selected, $options);
    }

    public function selectYear(): mixed
    {
        return call_user_func_array([$this, 'selectRange'], func_get_args());
    }

    public function selectMonth($name, $selected = null, $options = [], $format = '%B'): HtmlString
    {
        $months = [];

        foreach (range(1, 12) as $month) {
            $months[$month] = strftime($format, mktime(0, 0, 0, $month, 1));
        }

        return $this->select($name, $months, $selected, $options);
    }

    public function getSelectOption($display, $value, $selected, array $attributes = [], array $optgroupAttributes = []): HtmlString
    {
        if (is_iterable($display)) {
            return $this->optionGroup($display, $value, $selected, $optgroupAttributes, $attributes);
        }

        return $this->option($display, $value, $selected, $attributes);
    }

    protected function optionGroup($list, $label, $selected, array $attributes = [], array $optionsAttributes = [], $level = 0): HtmlString
    {
        $html = [];
        $space = str_repeat('&nbsp;', $level);
        foreach ($list as $value => $display) {
            $optionAttributes = $optionsAttributes[$value] ?? [];
            if (is_iterable($display)) {
                $html[] = $this->optionGroup($display, $value, $selected, $attributes, $optionAttributes, $level + 5);
            } else {
                $html[] = $this->option($space . $display, $value, $selected, $optionAttributes);
            }
        }

        return $this->toHtmlString('<optgroup label="' . e($space . $label, false) . '"' . $this->html->attributes($attributes) . '>' . implode('', $html) . '</optgroup>');
    }

    protected function option($display, $value, $selected, array $attributes = []): HtmlString
    {
        $selected = $this->getSelectedValue($value, $selected);

        $options = array_merge(['value' => $value, 'selected' => $selected], $attributes);

        $string = '<option' . $this->html->attributes($options) . '>';
        if ($display !== null) {
            $string .= e($display, false) . '</option>';
        }

        return $this->toHtmlString($string);
    }

    protected function placeholderOption($display, $selected): HtmlString
    {
        $selected = $this->getSelectedValue(null, $selected);

        $options = [
            'selected' => $selected,
            'value' => '',
        ];

        return $this->toHtmlString('<option' . $this->html->attributes($options) . '>' . e($display, false) . '</option>');
    }

    protected function getSelectedValue($value, $selected): bool|string|null
    {
        if (is_array($selected)) {
            return in_array($value, $selected, true) || in_array((string) $value, $selected, true) ? 'selected' : null;
        } elseif ($selected instanceof Collection) {
            return $selected->contains($value) ? 'selected' : null;
        }
        if (is_int($value) && is_bool($selected)) {
            return (bool)$value === $selected;
        }

        return ((string) $value === (string) $selected) ? 'selected' : null;
    }

    public function checkbox($name, $value = 1, $checked = null, $options = []): HtmlString
    {
        return $this->checkable('checkbox', $name, $value, $checked, $options);
    }

    public function radio($name, $value = null, $checked = null, $options = []): HtmlString
    {
        if (is_null($value)) {
            $value = $name;
        }

        return $this->checkable('radio', $name, $value, $checked, $options);
    }

    protected function checkable($type, $name, $value, $checked, $options): HtmlString
    {
        $this->type = $type;

        $checked = $this->getCheckedState($type, $name, $value, $checked);

        if ($checked) {
            $options['checked'] = 'checked';
        }

        return $this->input($type, $name, $value, $options);
    }

    protected function getCheckedState($type, $name, $value, $checked): bool
    {
        return match ($type) {
            'checkbox' => $this->getCheckboxCheckedState($name, $value, $checked),
            'radio' => $this->getRadioCheckedState($name, $value, $checked),
            default => $this->compareValues($name, $value),
        };
    }

    protected function getCheckboxCheckedState($name, $value, $checked): bool
    {
        $request = $this->request($name);

        if (isset($this->session) && ! $this->oldInputIsEmpty() && is_null($this->old($name)) && ! $request) {
            return false;
        }

        if ($this->missingOldAndModel($name) && is_null($request)) {
            return $checked;
        }

        $posted = $this->getValueAttribute($name, $checked);

        if (is_array($posted)) {
            return in_array($value, $posted);
        } elseif ($posted instanceof Collection) {
            return $posted->contains('id', $value);
        } else {
            return (bool) $posted;
        }
    }

    protected function getRadioCheckedState($name, $value, $checked): bool
    {
        $request = $this->request($name);

        if ($this->missingOldAndModel($name) && ! $request) {
            return $checked;
        }

        return $this->compareValues($name, $value);
    }

    protected function compareValues($name, $value): bool
    {
        return $this->getValueAttribute($name) == $value;
    }

    protected function missingOldAndModel($name): bool
    {
        return (is_null($this->old($name)) && is_null($this->getModelValueAttribute($name)));
    }

    public function reset($value, $attributes = []): HtmlString
    {
        return $this->input('reset', null, $value, $attributes);
    }

    public function image($url, $name = null, $attributes = []): HtmlString
    {
        $attributes['src'] = $this->url->asset($url);

        return $this->input('image', $name, null, $attributes);
    }

    public function month($name, $value = null, $options = []): HtmlString
    {
        if ($value instanceof DateTime) {
            $value = $value->format('Y-m');
        }

        return $this->input('month', $name, $value, $options);
    }

    public function color($name, $value = null, $options = []): HtmlString
    {
        return $this->input('color', $name, $value, $options);
    }

    public function submit($value = null, $options = []): HtmlString
    {
        return $this->input('submit', null, $value, $options);
    }

    public function button($value = null, $options = []): HtmlString
    {
        if (! array_key_exists('type', $options)) {
            $options['type'] = 'button';
        }

        return $this->toHtmlString('<button' . $this->html->attributes($options) . '>' . $value . '</button>');
    }

    public function datalist($id, $list = []): HtmlString
    {
        $this->type = 'datalist';

        $attributes['id'] = $id;

        $html = [];

        if ($this->isAssociativeArray($list)) {
            foreach ($list as $value => $display) {
                $html[] = $this->option($display, $value, null, []);
            }
        } else {
            foreach ($list as $value) {
                $html[] = $this->option($value, $value, null, []);
            }
        }

        $attributes = $this->html->attributes($attributes);

        $list = implode('', $html);

        return $this->toHtmlString("<datalist{$attributes}>{$list}</datalist>");
    }

    protected function isAssociativeArray($array): bool
    {
        return (array_values($array) !== $array);
    }

    protected function getMethod($method): string
    {
        $method = strtoupper($method);

        return $method !== 'GET' ? 'POST' : $method;
    }

    protected function getAction(array $options): string
    {
        // We will also check for a "route" or "action" parameter on the array so that
        // developers can easily specify a route or controller action when creating
        // a form providing a convenient interface for creating the form actions.
        if (isset($options['url'])) {
            return $this->getUrlAction($options['url']);
        }

        if (isset($options['route'])) {
            return $this->getRouteAction($options['route']);
        }

        // If an action is available, we are attempting to open a form to a controller
        // action route. So, we will use the URL generator to get the path to these
        // actions and return them from the method. Otherwise, we'll use current.
        elseif (isset($options['action'])) {
            return $this->getControllerAction($options['action']);
        }

        return $this->url->current();
    }

    protected function getUrlAction($options): string
    {
        if (is_array($options)) {
            return $this->url->to($options[0], array_slice($options, 1));
        }

        return $this->url->to($options);
    }

    protected function getRouteAction($options): string
    {
        if (is_array($options)) {
            $parameters = array_slice($options, 1);

            if (array_keys($options) === [0, 1]) {
                $parameters = head($parameters);
            }

            return $this->url->route($options[0], $parameters);
        }

        return $this->url->route($options);
    }

    protected function getControllerAction($options): string
    {
        if (is_array($options)) {
            return $this->url->action($options[0], array_slice($options, 1));
        }

        return $this->url->action($options);
    }

    protected function getAppendage($method): string
    {
        [$method, $appendage] = [strtoupper($method), ''];

        // If the HTTP method is in this list of spoofed methods, we will attach the
        // method spoofer hidden input to the form. This allows us to use regular
        // form to initiate PUT and DELETE requests in addition to the typical.
        if (in_array($method, $this->spoofedMethods)) {
            $appendage .= $this->hidden('_method', $method);
        }

        // If the method is something other than GET we will go ahead and attach the
        // CSRF token to the form, as this can't hurt and is convenient to simply
        // always have available on every form the developers creates for them.
        if ($method !== 'GET') {
            $appendage .= $this->token();
        }

        return $appendage;
    }

    public function getIdAttribute($name, $attributes): string
    {
        if (array_key_exists('id', $attributes)) {
            return $attributes['id'];
        }

        if (in_array($name, $this->labels)) {
            return $name;
        }

        return '';
    }

    public function getValueAttribute($name, $value = null): mixed
    {
        if (is_null($name)) {
            return $value;
        }

        $old = $this->old($name);

        if (! is_null($old) && $name !== '_method') {
            return $old;
        }

        if (function_exists('app')) {
            $hasNullMiddleware = app("Illuminate\Contracts\Http\Kernel")
                ->hasMiddleware(ConvertEmptyStringsToNull::class);

            if ($hasNullMiddleware
                && is_null($old)
                && is_null($value)
                && ! is_null($this->view->shared('errors'))
                && count(is_countable($this->view->shared('errors')) ? $this->view->shared('errors') : []) > 0
            ) {
                return null;
            }
        }

        $request = $this->request($name);
        if (! is_null($request) && $name != '_method') {
            return $request;
        }

        if (! is_null($value)) {
            return $value;
        }

        if (isset($this->model)) {
            return $this->getModelValueAttribute($name);
        }

        return '';
    }

    public function considerRequest($consider = true): void
    {
        $this->considerRequest = $consider;
    }

    protected function request($name): array|string|null
    {
        if (! $this->considerRequest) {
            return null;
        }

        if (! isset($this->request)) {
            return null;
        }

        return $this->request->input($this->transformKey($name));
    }

    protected function getModelValueAttribute($name): mixed
    {
        $key = $this->transformKey($name);

        if ((is_string($this->model) || is_object($this->model)) && method_exists($this->model, 'getFormValue')) {
            return $this->model->getFormValue($key);
        }

        return data_get($this->model, $key);
    }

    public function old($name): mixed
    {
        if (isset($this->session)) {
            $key = $this->transformKey($name);
            $payload = $this->session->getOldInput($key);

            if (! is_array($payload)) {
                return $payload;
            }

            if (! in_array($this->type, ['select', 'checkbox'])) {
                if (! isset($this->payload[$key])) {
                    $this->payload[$key] = collect($payload);
                }

                if (! empty($this->payload[$key])) {
                    return $this->payload[$key]->shift();
                }
            }

            return $payload;
        }

        return '';
    }

    public function oldInputIsEmpty(): bool
    {
        return (isset($this->session) && count((array) $this->session->getOldInput()) === 0);
    }

    protected function transformKey($key): array|string
    {
        return str_replace(['.', '[]', '[', ']'], ['_', '', '.', ''], $key);
    }

    protected function toHtmlString($html): HtmlString
    {
        return new HtmlString($html);
    }

    public function getSessionStore(): Session
    {
        return $this->session;
    }

    public function setSessionStore(Session $session): static
    {
        $this->session = $session;

        return $this;
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
