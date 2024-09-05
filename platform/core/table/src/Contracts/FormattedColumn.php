<?php

namespace Botble\Table\Contracts;

use Botble\Base\Contracts\BaseModel;
use Botble\Table\Abstracts\TableAbstract;

interface FormattedColumn
{
    public function formattedValue($value): string|null;

    public function renderCell(BaseModel|array $item, TableAbstract $table): string;
}
