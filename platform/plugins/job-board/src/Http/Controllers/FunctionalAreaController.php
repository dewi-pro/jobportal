<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Forms\FunctionalAreaForm;
use Botble\JobBoard\Http\Requests\FunctionalAreaRequest;
use Botble\JobBoard\Models\FunctionalArea;
use Botble\JobBoard\Tables\FunctionalAreaTable;
use Exception;
use Illuminate\Http\Request;

class FunctionalAreaController extends BaseController
{
    public function index(FunctionalAreaTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::functional-area.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::functional-area.create'));

        return FunctionalAreaForm::create()->renderForm();
    }

    public function store(FunctionalAreaRequest $request)
    {
        if ($request->input('is_default')) {
            FunctionalArea::query()->where('id', '>', 0)->update(['is_default' => 0]);
        }

        $functionalArea = FunctionalArea::query()->create($request->input());

        event(new CreatedContentEvent(FUNCTIONAL_AREA_MODULE_SCREEN_NAME, $request, $functionalArea));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('functional-areas.index'))
            ->setNextUrl(route('functional-areas.edit', $functionalArea->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(FunctionalArea $functionalArea, Request $request)
    {
        event(new BeforeEditContentEvent($request, $functionalArea));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $functionalArea->name]));

        return FunctionalAreaForm::createFromModel($functionalArea)->renderForm();
    }

    public function update(FunctionalArea $functionalArea, FunctionalAreaRequest $request)
    {
        if ($request->input('is_default')) {
            FunctionalArea::query()->where('id', '!=', $functionalArea->getKey())->update(['is_default' => 0]);
        }

        $functionalArea->fill($request->input());
        $functionalArea->save();

        event(new UpdatedContentEvent(FUNCTIONAL_AREA_MODULE_SCREEN_NAME, $request, $functionalArea));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('functional-areas.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(FunctionalArea $functionalArea, Request $request)
    {
        try {
            $functionalArea->delete();

            event(new DeletedContentEvent(FUNCTIONAL_AREA_MODULE_SCREEN_NAME, $request, $functionalArea));

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
