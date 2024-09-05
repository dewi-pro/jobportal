<?php

namespace Botble\Base\Forms;

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Closure;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Tappable;
use LogicException;

class FormCollapse
{
    use Conditionable;
    use Tappable;

    protected string $targetFieldName;

    protected string $targetFieldType = OnOffCheckboxField::class;

    protected array $targetFieldOption = [];

    protected bool $targetFieldModify = false;

    protected string $targetFieldValue = '1';

    protected Closure $fieldsetCallback;

    protected Closure|null $beforeRegisterFieldset = null;

    protected Closure|null $afterRegisterFieldset = null;

    protected bool $isOpened = false;

    public function __construct(protected string $id)
    {
    }

    public static function make(string $id): static
    {
        return app(static::class, compact('id'));
    }

    public function getId(): string
    {
        return sprintf('collapsible-form-%s', $this->id);
    }

    public function targetField(
        string $fieldName,
        string $fieldType = OnOffCheckboxField::class,
        array|FormFieldOptions $fieldOptions = [],
        bool $fieldModify = false
    ): static {
        $this->targetFieldName = $fieldName;
        $this->targetFieldType = $fieldType;
        $this->targetFieldOption = is_array($fieldOptions)
            ? $fieldOptions
            : $fieldOptions->toArray();
        $this->targetFieldOption['attr']['data-bb-toggle'] = 'collapse';
        $this->targetFieldOption['attr']['data-bb-target'] = '.' . $this->getId();

        $this->targetFieldModify = $fieldModify;

        return $this;
    }

    public function fieldset(Closure $callback): static
    {
        $this->fieldsetCallback = $callback;

        return $this;
    }

    public function targetValue(string|bool $targetValue): static
    {
        $this->targetFieldValue = $targetValue;

        return $this;
    }

    public function isOpened(bool $isOpened = true): static
    {
        $this->isOpened = $isOpened;

        return $this;
    }

    public function beforeRegisterField(Closure $callback): static
    {
        $this->beforeRegisterFieldset = $callback;

        return $this;
    }

    public function afterRegisterField(Closure $callback): static
    {
        $this->afterRegisterFieldset = $callback;

        return $this;
    }

    public function build(FormAbstract $form): void
    {
        if (! isset($this->targetFieldName)) {
            throw new LogicException('Collapsible form requires fieldset and target field name.');
        }

        $form->add($this->targetFieldName, $this->targetFieldType, $this->targetFieldOption, $this->targetFieldModify);

        if ($this->beforeRegisterFieldset) {
            call_user_func($this->beforeRegisterFieldset, $form);
        }

        $form->add(
            sprintf('open_fieldset_%s', $this->id),
            HtmlField::class,
            HtmlFieldOption::make()
                ->content(sprintf(
                    '<fieldset class="%s form-fieldset" data-bb-value="%s" style="display: %s"/>',
                    $this->getId(),
                    $this->targetFieldValue,
                    $this->isOpened ? 'block' : 'none',
                ))
                ->toArray()
        );

        if (isset($this->fieldsetCallback)) {
            call_user_func($this->fieldsetCallback, $form);
        }

        $form->add(
            sprintf('close_fieldset_%s', $this->id),
            HtmlField::class,
            HtmlFieldOption::make()->content('</fieldset>')->toArray()
        );

        if ($this->afterRegisterFieldset) {
            call_user_func($this->afterRegisterFieldset, $form);
        }
    }
}
