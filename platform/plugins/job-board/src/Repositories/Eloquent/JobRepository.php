<?php

namespace Botble\JobBoard\Repositories\Eloquent;

use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Models\Job;
use Botble\JobBoard\Repositories\Interfaces\JobInterface;
use Botble\Location\Facades\Location;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class JobRepository extends RepositoriesAbstract implements JobInterface
{
    public function getJobs(array $filters = [], array $params = [])
    {
        $filters = JobBoardHelper::getJobFilters($filters);

        $filters = array_merge([
            'keyword' => null,
            'city_id' => null,
            'state_id' => null,
            'city' => null,
            'location' => null,
            'job_categories' => [],
            'job_tags' => [],
            'job_types' => [],
            'job_experiences' => [],
            'job_skills' => [],
            'offered_salary_from' => null,
            'offered_salary_to' => null,
            'date_posted' => null,
        ], $filters);

        $with = [
            'slugable',
            'tags',
            'jobTypes',
            'jobExperience',
            'company',
            'company.slugable',
        ];

        if (is_plugin_active('location')) {
            $with = array_merge($with, array_keys(Location::getSupported(Job::class)));
        }

        $params = array_merge([
            'condition' => [],
            'order_by' => [
                'created_at' => 'DESC',
                'is_featured' => 'DESC',
            ],
            'take' => null,
            'paginate' => [
                'per_page' => 20,
                'current_paged' => 1,
            ],
            'select' => ['jb_jobs.*'],
            'with' => $with,
            'withCount' => ['applicants'],
        ], $params);

        $this->model = $this->originalModel;

        // @phpstan-ignore-next-line
        $this->model = $this->model->select($params['select'])->active();

        $this->model = $this->model->filterJobs($filters);

        $params['select'] = []; // Reset select to avoid duplicate columns

        return $this->advancedGet($params);
    }

    public function getFeaturedJobs(int $limit = 10, array $with = [])
    {
        $params = [
            'condition' => ['is_featured' => true],
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
        ];

        if ($with) {
            $params['with'] = $with;
        }

        return $this->getJobs([], $params);
    }

    public function getRecentJobs(int $limit = 10, array $with = [])
    {
        $params = [
            'order_by' => [
                'created_at' => 'DESC',
            ],
            'take' => $limit,
        ];
        if ($with) {
            $params['with'] = $with;
        }

        return $this->getJobs([], $params);
    }

    public function getPopularJobs(int $limit = 10, array $with = [])
    {
        $params = [
            'order_by' => [
                'views' => 'DESC',
                'created_at' => 'DESC',
            ],
            'take' => $limit,
        ];
        if ($with) {
            $params['with'] = $with;
        }

        return $this->getJobs([], $params);
    }

    public function countActiveJobs()
    {
        // @phpstan-ignore-next-line
        $data = $this->model->active();

        return $this->applyBeforeExecuteQuery($data)->count('id');
    }
}
