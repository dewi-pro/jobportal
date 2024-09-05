<?php

namespace Botble\JobBoard\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\JobBoard\Enums\JobApplicationStatusEnum;
use Botble\JobBoard\Enums\JobRecruitmentProgressStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log; 
use Botble\Base\Events\CreatedContentEvent;
use Illuminate\Support\Facades\Mail; // Import the Mail facade
use App\Mail\JobApplicationStatusChanged; // Import the Mailable class

class JobApplication extends BaseModel
{
    protected $table = 'jb_applications';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'resume',
        'cover_letter',
        'message',
        'job_id',
        'account_id',
        'status',
    ];

    protected $casts = [
        'status' => JobApplicationStatusEnum::class,
        'first_name' => SafeContent::class,
        'last_name' => SafeContent::class,
        'message' => SafeContent::class,
    ];
    
    protected $attributes = [
        'status' => JobApplicationStatusEnum::SUBMIT,
    ];
    
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id')->withDefault();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id')->withDefault();
    }

    public function getFullNameAttribute(): string
    {
        if ($this->account->id && $this->account->is_public_profile) {
            return $this->account->name;
        }

        return $this->first_name . ' ' . $this->last_name;
    }

    public function getJobUrlAttribute(): string
    {
        $url = '';
        if (! $this->job->is_expired) {
            $url = $this->job->url;
        }

        return $url;
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function (JobApplication $jobApplication) {

            if ($jobApplication->isDirty('status')) {
                $newStatus = $jobApplication->status;
                $reflection = new \ReflectionObject($newStatus);
                if ($reflection->hasProperty('value')) {
                    $property = $reflection->getProperty('value');
                    $property->setAccessible(true);
                    $newStatusValue = $property->getValue($newStatus);
                } else {
                    $newStatusValue = null; // Handle the case when value is not available
                }

                if ($newStatusValue === JobApplicationStatusEnum::NOTSUITABLE) {
                    // Delete the job application when the status changes to 'Not Suitable'
                    $jobApplication->delete();
                } elseif ($newStatusValue === JobApplicationStatusEnum::PROCESS) {

                    $existingRecruitmentProgress = RecruitmentProgress::where('job_application_id', $jobApplication->id)->first();
                    
                    if (!$existingRecruitmentProgress) {
                        // Create a new RecruitmentProgress record
                        $recruitmentProgress = RecruitmentProgress::create([
                            'status' => JobRecruitmentProgressStatusEnum::PROCESS, // Adjust the status or other fields as necessary
                            'nama_kandidat' => $jobApplication->first_name . ' ' . $jobApplication->last_name,
                            'tanggal_FPK' => now(),
                            'job_application_id' => $jobApplication->id,
                        ]);
                        $url = 'https://survey.antavaya.com/index.php/783242';
                        Mail::to($jobApplication->email)->send(new JobApplicationStatusChanged($recruitmentProgress, $url));

            
                    }
            
                }
            
            }
        });
    }



}
