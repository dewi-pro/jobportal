<?php

namespace Botble\Base\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BaseQueryBuilder extends Builder
{
    public function addSearch(string $column, string|null $term, bool $isPartial = true, bool $or = true): static
    {
        $term = trim($term);
        $term = str_replace('&', '&amp;', $term);

        if (! $isPartial) {
            $this->{$or ? 'orWhere' : 'where'}($column, 'LIKE', '%' . $term . '%');

            return $this;
        }

        $searchTerms = explode(' ', $term);

        $sql = 'LOWER(' . $this->getGrammar()->wrap($column) . ') LIKE ? ESCAPE ?';

        foreach ($searchTerms as $searchTerm) {
            $searchTerm = mb_strtolower($searchTerm, 'UTF8');
            $searchTerm = str_replace('\\', $this->getBackslashByPdo(), $searchTerm);
            $searchTerm = addcslashes($searchTerm, '%_');

            $this->orWhereRaw($sql, ['%' . $searchTerm . '%', '\\']);
        }

        return $this;
    }

    protected function getBackslashByPdo(): string
    {
        if (DB::getDefaultConnection() === 'sqlite') {
            return '\\\\';
        }

        return '\\\\\\';
    }

    public function wherePublished($column = 'status'): static
    {
        $this->where($column, BaseStatusEnum::PUBLISHED);

        return $this;
    }

    public function get($columns = ['*'])
    {
        $data = parent::get($columns);

        return apply_filters('model_after_execute_get', $data, $this->getModel(), $columns);
    }
}
