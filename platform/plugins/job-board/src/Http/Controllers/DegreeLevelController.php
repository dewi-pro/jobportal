<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Forms\DegreeLevelForm;
use Botble\JobBoard\Http\Requests\DegreeLevelRequest;
use Botble\JobBoard\Models\DegreeLevel;
use Botble\JobBoard\Tables\DegreeLevelTable;
use Exception;
use Illuminate\Http\Request;

class DegreeLevelController extends BaseController
{
    public function index(DegreeLevelTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::degree-level.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::degree-level.create'));

        return DegreeLevelForm::create()->renderForm();
    }

    public function store(DegreeLevelRequest $request)
    {
        if ($request->input('is_default')) {
            DegreeLevel::query()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $degreeLevel = DegreeLevel::query()->create($request->input());

        event(new CreatedContentEvent(DEGREE_LEVEL_MODULE_SCREEN_NAME, $request, $degreeLevel));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('degree-levels.index'))
            ->setNextUrl(route('degree-levels.edit', $degreeLevel->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(DegreeLevel $degreeLevel, Request $request)
    {
        event(new BeforeEditContentEvent($request, $degreeLevel));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $degreeLevel->name]));

        return DegreeLevelForm::createFromModel($degreeLevel)->renderForm();
    }

    public function update(DegreeLevel $degreeLevel, DegreeLevelRequest $request)
    {
        if ($request->input('is_default')) {
            DegreeLevel::query()->where('id', '!=', $degreeLevel->getKey())->update(['is_default' => 0]);
        }

        $degreeLevel->fill($request->input());
        $degreeLevel->save();

        event(new UpdatedContentEvent(DEGREE_LEVEL_MODULE_SCREEN_NAME, $request, $degreeLevel));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('degree-levels.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(DegreeLevel $degreeLevel, Request $request)
    {
        try {
            $degreeLevel->delete();

            event(new DeletedContentEvent(DEGREE_LEVEL_MODULE_SCREEN_NAME, $request, $degreeLevel));

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
