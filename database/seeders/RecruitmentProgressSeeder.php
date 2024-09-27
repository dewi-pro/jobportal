<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\JobBoard\Enums\AccountTypeEnum;
use Botble\JobBoard\Enums\JobRecruitmentProgressStatusEnum;
use Botble\JobBoard\Models\JobApplication;
use Botble\JobBoard\Models\Job;
use Botble\JobBoard\Models\RecruitmentProgress;

class RecruitmentProgressSeeder extends BaseSeeder
{
    public function run(): void
    {
        RecruitmentProgress::query()->truncate();

        $faker = $this->fake();

        $jobApplication = JobApplication::query()
            ->select(['id', 'first_name', 'last_name', 'phone', 'email', 'resume'])
            ->inRandomOrder()
            ->limit(20)
            ->get();

        $jobs = Job::query()
            ->select('id')
            ->inRandomOrder()
            ->limit(20)
            ->get();

    }
}
