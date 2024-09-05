<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Enums\ModerationStatusEnum;
use Botble\JobBoard\Events\JobPublishedEvent;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Forms\RecruitmentProgressForm;
use Botble\JobBoard\Http\Requests\ExpireJobsRequest;
use Botble\JobBoard\Http\Requests\RecruitmentRequest;
use Botble\JobBoard\Models\Account;
use Botble\JobBoard\Models\CustomFieldValue;
use Botble\JobBoard\Models\RecruitmentProgress;
use Botble\JobBoard\Repositories\Interfaces\AnalyticsInterface;
use Botble\JobBoard\Services\StoreTagService;
use Botble\JobBoard\Tables\RecruitmentProgressTable;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class RecruitmentProgressController extends BaseController
{

    public function index(RecruitmentProgressTable $table)
    {
        $this->pageTitle('Recruitment Progress');

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle('New Recruitment');

        return RecruitmentProgressForm::create()->renderForm();
    }

    public function store(RecruitmentRequest $request)
    {
        $recruitment = RecruitmentProgress::query()->create($request->input());

        event(new CreatedContentEvent(JOB_RECRUITMENT_MODULE_SCREEN_NAME, $request, $recruitment));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('recruitment.index'))
            ->setNextUrl(route('recruitment.edit', $recruitment->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(RecruitmentProgress $recruitment, Request $request)
    {

        event(new BeforeEditContentEvent($request, $recruitment));
        
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $recruitment->nama_kandidat]));
    
        return RecruitmentProgressForm::createFromModel($recruitment)->renderForm();
    }

    public function update(RecruitmentProgress $recruitment, RecruitmentRequest $request)
    {

        $recruitment->fill($request->input());
        $recruitment->save();

        event(new UpdatedContentEvent(JOB_RECRUITMENT_MODULE_SCREEN_NAME, $request, $recruitment));


        return $this
        ->httpResponse()
        ->setPreviousUrl(route('recruitment.index'))
        ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(RecruitmentProgress $recruitment, Request $request)
    {
        try {
            $recruitment->delete();
            event(new DeletedContentEvent(JOB_RECRUITMENT_MODULE_SCREEN_NAME, $request, $recruitment));

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
