<?php

namespace Botble\JobBoard\Enums;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static JobRecruitmentProgressStatusEnum PROCESS()
 * @method static JobRecruitmentProgressStatusEnum DONE()
 * @method static JobRecruitmentProgressStatusEnum HOLD()
 */

class JobRecruitmentProgressStatusEnum extends Enum
{
    public const PROCESS = 'On Process';
    public const HOLD = 'Hold';
    public const DONE = 'Done';

    public static $langPath = 'plugins/job-board::job-recruitmentProgress.statuses';

    public function toHtml(): HtmlString|string
    {
        $color = match ($this->value) {
            self::PROCESS => 'warning',
            self::DONE => 'success',
            self::HOLD => 'danger',
        };

        return BaseHelper::renderBadge($this->label(), $color);
    }
}
