<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Forms\TagForm;
use Botble\JobBoard\Http\Requests\TagRequest;
use Botble\JobBoard\Models\Tag;
use Botble\JobBoard\Tables\TagTable;
use Exception;
use Illuminate\Http\Request;

class TagController extends BaseController
{
    public function index(TagTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::tag.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::tag.create'));

        return TagForm::create()->renderForm();
    }

    public function store(TagRequest $request)
    {
        $tag = Tag::query()->create($request->input());

        event(new CreatedContentEvent(JOB_BOARD_TAG_MODULE_SCREEN_NAME, $request, $tag));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('job-board.tag.index'))
            ->setNextUrl(route('job-board.tag.edit', $tag->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Tag $tag, Request $request)
    {
        event(new BeforeEditContentEvent($request, $tag));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $tag->name]));

        return TagForm::createFromModel($tag)->renderForm();
    }

    public function update(Tag $tag, TagRequest $request)
    {
        $tag->fill($request->input());
        $tag->save();

        event(new UpdatedContentEvent(JOB_BOARD_TAG_MODULE_SCREEN_NAME, $request, $tag));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('job-board.tag.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Tag $tag, Request $request)
    {
        try {
            $tag->delete();

            event(new DeletedContentEvent(JOB_BOARD_TAG_MODULE_SCREEN_NAME, $request, $tag));

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

    public function getAllTags()
    {
        return Tag::query()->pluck('name')->all();
    }
}
