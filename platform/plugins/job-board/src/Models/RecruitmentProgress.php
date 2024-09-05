<?php

namespace Botble\JobBoard\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\JobBoard\Enums\JobRecruitmentProgressStatusEnum;
use Botble\JobBoard\Models\Builders\FilterJobsBuilder;
// use Botble\Media\Facades\RvMedia;
// use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\Casts\Attribute;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;
// use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use Illuminate\Database\Eloquent\Relations\HasMany;
// use Illuminate\Database\Eloquent\Relations\MorphMany;
// use Illuminate\Database\Eloquent\Relations\MorphTo;
// use Illuminate\Support\Facades\DB;

class RecruitmentProgress extends BaseModel
{
    protected $table = 'jb_recruitmentProgres';

    protected $fillable = [
        'tanggal_FPK',
        'cabang',
        'user',
        'posisi',
        'nama_kandidat',
        'psikotes',
        'offering_letter',
        'recruitment',
        'user_date',
        'tanggal_masuk',
        'status',
        'proses',
        'sumber',
        'catatan',
        'job_application_id',
    ];

    protected $casts = [
        'status' => JobRecruitmentProgressStatusEnum::class,
        'tanggal_FPK' => 'date',
        'psikotes' => 'date',
        'offering_letter' => 'date',
        'recruitment' => 'date',
        'user_date' => 'date',
        'tanggal_masuk' => 'date',
        'user' => SafeContent::class,
        'catatan' => SafeContent::class,
     ];

}
