<?php

namespace Botble\JobBoard\Contracts;

use Botble\Base\Models\BaseModel;

trait Typeable
{
    public function stringToArray(string|null $string): array
    {
        if ($string === null) {
            return [];
        }

        return explode(',', $string);
    }

    public function yesNoToBoolean(string|null $string): bool
    {
        return strtolower($string) === 'yes';
    }

    public function stringToModelIds(string|null $value, BaseModel $model, string $column = 'name'): array|null
    {
        if (! $value) {
            return null;
        }

        $items = $this->stringToArray($value);

        $ids = [];

        foreach ($items as $index => $item) {
            if (is_numeric($item)) {
                $column = 'id';
            }

            $ids[$index] = $model->where($column, $item)->value('id');
        }

        return array_filter($ids);
    }

    /**
     * @deprecated
     * Replace by stringToModelIds()
     */
    public function getIdsFromString(string|null $value, BaseModel $model, string $column = 'name'): array|null
    {
        return $this->stringToModelIds($value, $model, $column);
    }
}
