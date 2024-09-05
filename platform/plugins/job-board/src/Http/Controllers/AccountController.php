<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Enums\AccountTypeEnum;
use Botble\JobBoard\Forms\AccountForm;
use Botble\JobBoard\Http\Requests\AccountCreateRequest;
use Botble\JobBoard\Http\Requests\AccountEditRequest;
use Botble\JobBoard\Http\Resources\AccountResource;
use Botble\JobBoard\Models\Account;
use Botble\JobBoard\Tables\AccountTable;
use Botble\Media\Models\MediaFile;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends BaseController
{
    public function index(AccountTable $dataTable)
    {
        $this->pageTitle(trans('plugins/job-board::account.name'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::account.create'));

        return AccountForm::create()
            ->remove('is_change_password')
            ->renderForm();
    }

    public function store(AccountCreateRequest $request)
    {
        $form = AccountForm::create()->setRequest($request);

        $form->saving(function (AccountForm $form) {
            $account = $form->getModel();
            $request = $form->getRequest();

            $request->merge(['password' => Hash::make($request->input('password'))]);
            $account->fill($request->input());
            $account->is_featured = $request->input('is_featured', false);
            $account->confirmed_at = Carbon::now();

            if ($request->input('avatar_image')) {
                $image = MediaFile::query()
                    ->where('url', $request->input('avatar_image'))
                    ->first();

                if ($image) {
                    $account->avatar_id = $image->id;
                }
            }

            $account->save();
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('accounts.index'))
            ->setNextUrl(route('accounts.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Account $account, Request $request)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $account->name]));

        $account->password = null;

        event(new BeforeEditContentEvent($request, $account));

        return AccountForm::createFromModel($account)
            ->renderForm();
    }

    public function update(Account $account, AccountEditRequest $request)
    {
        AccountForm::createFromModel($account)
            ->setRequest($request)
            ->saving(function (AccountForm $form) {
                $request = $form->getRequest();
                $account = $form->getModel();

                if ($request->input('is_change_password') == 1) {
                    $request->merge(['password' => Hash::make($request->input('password'))]);
                    $data = $request->input();
                } else {
                    $data = $request->except('password');
                }

                $account->fill($data);
                $account->is_featured = $request->input('is_featured', false);

                if ($request->input('avatar_image')) {
                    $imageId = MediaFile::query()
                        ->where('url', $request->input('avatar_image'))
                        ->value('id');

                    if ($imageId) {
                        $account->avatar_id = $imageId;
                    }
                }

                $account->save();
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('accounts.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Account $account, Request $request)
    {
        try {
            $account->delete();
            event(new DeletedContentEvent(ACCOUNT_MODULE_SCREEN_NAME, $request, $account));

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

    public function getList(Request $request)
    {
        $keyword = BaseHelper::stringify($request->input('q'));

        if (! $keyword) {
            return $this
                ->httpResponse()
                ->setData([]);
        }

        $data = Account::query()
            ->where('jb_accounts.type', AccountTypeEnum::EMPLOYER)
            ->when($keyword, function ($query) use ($keyword) {
                $query->where(function ($query) use ($keyword) {
                    $query->where('jb_accounts.first_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('jb_accounts.last_name', 'LIKE', "%{$keyword}%")
                        ->orWhere('jb_accounts.email', 'LIKE', "%{$keyword}%");
                });
            })
            ->select(['jb_accounts.id', 'jb_accounts.first_name', 'jb_accounts.last_name', 'jb_accounts.email'])
            ->take(10)
            ->get();

        return $this
            ->httpResponse()
            ->setData(AccountResource::collection($data));
    }

    public function getAllEmployers()
    {
        return Account::query()
            ->select(DB::raw('CONCAT(jb_accounts.first_name, " ", jb_accounts.last_name) as name'))
            ->where('type', AccountTypeEnum::EMPLOYER)
            ->pluck('name')
            ->all();
    }
}
