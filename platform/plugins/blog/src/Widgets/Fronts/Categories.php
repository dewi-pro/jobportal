<?php

namespace Botble\Blog\Widgets\Fronts;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Blog\Models\Category;
use Botble\Widget\AbstractWidget;
use Botble\Widget\Forms\WidgetForm;
use Illuminate\Support\Collection;

class Categories extends AbstractWidget
{
    public function __construct()
    {
        parent::__construct([
            'name' => __('Blog Categories'),
            'description' => __('Widget display blog categories'),
            'number_display' => 10,
        ]);
    }

    protected function data(): array|Collection
    {
        if (! is_plugin_active('blog')) {
            return [];
        }

        $categories = Category::query()
            ->wherePublished()
            ->with('slugable')
            ->take((int)$this->getConfig('number_display') ?: 10)
            ->get();

        return [
            'categories' => $categories,
        ];
    }

    protected function settingForm(): WidgetForm|string|null
    {
        if (! is_plugin_active('blog')) {
            return null;
        }

        return WidgetForm::createFromArray($this->getConfig())
            ->add('name', TextField::class, NameFieldOption::make()->toArray())
            ->add(
                'number_display',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(__('Number categories to display'))
                    ->toArray()
            );
    }
}
