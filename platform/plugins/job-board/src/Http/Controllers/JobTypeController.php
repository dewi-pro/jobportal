<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Forms\JobTypeForm;
use Botble\JobBoard\Http\Requests\JobTypeRequest;
use Botble\JobBoard\Models\JobType;
use Botble\JobBoard\Tables\JobTypeTable;
use Exception;
use Illuminate\Http\Request;

class JobTypeController extends BaseController
{
    public function index(JobTypeTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::job-type.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::job-type.create'));

        return JobTypeForm::create()->renderForm();
    }

    public function store(JobTypeRequest $request)
    {
        if ($request->input('is_default')) {
            JobType::query()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $jobType = JobType::query()->create($request->input());

        event(new CreatedContentEvent(JOB_TYPE_MODULE_SCREEN_NAME, $request, $jobType));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('job-types.index'))
            ->setNextUrl(route('job-types.edit', $jobType->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(JobType $jobType, Request $request)
    {
        event(new BeforeEditContentEvent($request, $jobType));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $jobType->name]));

        return JobTypeForm::createFromModel($jobType)->renderForm();
    }

    public function update(JobType $jobType, JobTypeRequest $request)
    {
        if ($request->input('is_default')) {
            JobType::query()->where('id', '!=', $jobType->getKey())->update(['is_default' => 0]);
        }

        $jobType->fill($request->input());
        $jobType->save();

        event(new UpdatedContentEvent(JOB_TYPE_MODULE_SCREEN_NAME, $request, $jobType));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('job-types.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(JobType $jobType, Request $request)
    {
        try {
            $jobType->delete();

            event(new DeletedContentEvent(JOB_TYPE_MODULE_SCREEN_NAME, $request, $jobType));

            return $this
                ->httpResponse()
                ->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
