<?php

namespace Botble\Table\Actions;

use Botble\Base\Supports\Builders\HasAttributes;
use Botble\Base\Supports\Builders\HasColor;
use Botble\Base\Supports\Builders\HasIcon;
use Botble\Base\Supports\Builders\HasUrl;
use Botble\Table\Abstracts\TableActionAbstract;
use Botble\Table\Actions\Concerns\HasAction;

class Action extends TableActionAbstract
{
    use HasAction;
    use HasAttributes;
    use HasColor;
    use HasIcon;
    use HasUrl;
}
