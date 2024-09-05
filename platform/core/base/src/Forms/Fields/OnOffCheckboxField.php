<?php

namespace Botble\Base\Forms\Fields;

use Botble\Base\Forms\FieldTypes\FormField;

class OnOffCheckboxField extends FormField
{
    protected function getTemplate(): string
    {
        return 'core/base::forms.fields.on-off-checkbox';
    }

    public function getDefaults(): array
    {
        return [
            ...parent::getDefaults(),
            'attr' => ['class' => null, 'id' => $this->getName()],
        ];
    }
}
