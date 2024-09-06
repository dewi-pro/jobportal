<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Forms\DatabaseInfoForm;
use Botble\JobBoard\Http\Requests\EditDatabaseInfoRequest;
use Botble\JobBoard\Enums\DatabaseInfoStatusEnum;
use Botble\JobBoard\Models\DatabaseInfo;
use Botble\JobBoard\Tables\DatabaseInfoTable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DatabaseInfoController extends BaseController
{
    public function index(DatabaseInfoTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::database-info.name'));

        return $table->renderTable();
    }

    public function edit(DatabaseInfo $databaseInfo)
    {
        $this->pageTitle(trans('plugins/job-board::database-info.edit'));

        return DatabaseInfoForm::createFromModel($databaseInfo)->renderForm();
    }

    public function update(DatabaseInfo $databaseInfo, EditDatabaseInfoRequest $request)
    {
        // Get the new status from the request
        $newStatus = $request->input('status');
    
        // Check if the status is being updated to 'Not Suitable'
        if ($newStatus === DatabaseInfoStatusEnum::NOTSUITABLE) {
            // Delete the job application
            $databaseInfo->delete();
    
            // Trigger the event
            event(new UpdatedContentEvent(DATABASE_INFO_MODULE_SCREEN_NAME, $request, $databaseInfo));
    
            // Return response indicating the record was deleted
            return $this
                ->httpResponse()
                ->setPreviousUrl(route('db.index'))
                ->setMessage(trans('core/base::notices.delete_success_message'));
        }
    
        // Update the job application status or other fields
        $databaseInfo->fill($request->input());
        $databaseInfo->save();
    
        // Trigger the event
        event(new UpdatedContentEvent(DATABASE_INFO_MODULE_SCREEN_NAME, $request, $databaseInfo));
    
        // Return response indicating the update was successful
        return $this
            ->httpResponse()
            ->setPreviousUrl(route('db.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }
    

    public function destroy(DatabaseInfo $databaseInfo, Request $request)
    {
        try {
            $databaseInfo->delete();
            event(new DeletedContentEvent(DATABASE_INFO_MODULE_SCREEN_NAME, $request, $databaseInfo));

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

    public function downloadCv(int|string $id)
    {
        $databaseInfo = DatabaseInfo::query()->findOrFail($id);

        if ($databaseInfo->resume) {
            return Storage::download($databaseInfo->resume);
        }

        $account = $databaseInfo->account;

        if ($account->id && $account->resume) {
            return Storage::download($account->resume);
        }

        return Storage::download($databaseInfo->resume);
    }

}
