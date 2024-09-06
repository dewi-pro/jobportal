<?php

namespace Botble\JobBoard\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static JobApplicationStatusEnum SUBMIT()
 * @method static JobApplicationStatusEnum PROCESS()
 * @method static JobApplicationStatusEnum SAVEDB()
 * @method static JobApplicationStatusEnum NOTSUITABLE()
 */

class DatabaseInfoStatusEnum extends Enum
{
    public const SUBMIT = 'submit';
    public const PROCESS = 'process';
    public const SAVEDB = 'saveToDatabase';
    public const NOTSUITABLE = 'notSuitable';

    public static $langPath = 'plugins/job-board::database-info.statuses';

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::PROCESS => 'warning',
            self::SAVEDB => 'success',
            self::NOTSUITABLE => 'danger',
            default => 'primary',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
