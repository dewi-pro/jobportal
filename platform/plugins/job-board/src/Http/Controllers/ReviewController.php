<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Models\Review;
use Botble\JobBoard\Tables\ReviewTable;
use Exception;
use Illuminate\Http\Request;

class ReviewController extends BaseController
{
    public function index(ReviewTable $dataTable)
    {
        $this->pageTitle(trans('plugins/job-board::review.name'));

        Assets::addStylesDirectly('vendor/core/plugins/job-board/css/review.css');

        return $dataTable->renderTable();
    }

    public function destroy(Review $review, Request $request)
    {
        try {
            $review->delete();

            event(new DeletedContentEvent(REVIEW_MODULE_SCREEN_NAME, $request, $review));

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
