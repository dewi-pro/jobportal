<?php

namespace Botble\JobBoard\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\JobBoard\Enums\JobRecruitmentProgressStatusEnum;
use Botble\JobBoard\Models\Builders\FilterJobsBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;

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
        'job_id',
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

     public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id')->withDefault();
    }

protected static function boot()
{
    parent::boot();

    static::updating(function (RecruitmentProgress $recruitmentProgress) {

        if ($recruitmentProgress->isDirty('status')) {
            $newStatus = $recruitmentProgress->status;
            $reflection = new \ReflectionObject($newStatus);
            if ($reflection->hasProperty('value')) {
                $property = $reflection->getProperty('value');
                $property->setAccessible(true);
                $newStatusValue = $property->getValue($newStatus);
            } else {
                $newStatusValue = null; // Handle the case when value is not available
            }

            if ($newStatusValue === JobRecruitmentProgressStatusEnum::DONE) {
                $job = Job::find($recruitmentProgress->job_id); // Ensure 'job_id' exists in RecruitmentProgress

                if ($job) {
                    // Check if number_of_positions is greater than 0 before decrementing
                    if ($job->number_of_positions > 0) {
                        $job->number_of_positions -= 1; // Decrement by 1
                        $job->save(); // Save the changes
                    } else {
                        Log::warning('Cannot decrement number_of_positions as it is already 0 for job ID: ' . $job->id);
                    }
                } else {
                    Log::error('Job not found for job_id: ' . $recruitmentProgress->job_id);
                }
            }

        }
    });
}

}
