<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Forms\CompanyForm;
use Botble\JobBoard\Http\Requests\AjaxCompanyRequest;
use Botble\JobBoard\Http\Requests\CompanyRequest;
use Botble\JobBoard\Http\Resources\CompanyResource;
use Botble\JobBoard\Models\Company;
use Botble\JobBoard\Services\StoreCompanyAccountService;
use Botble\JobBoard\Tables\CompanyTable;
use Illuminate\Http\Request;

class CompanyController extends BaseController
{
    public function index(CompanyTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::company.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::company.create'));

        return CompanyForm::create()->renderForm();
    }

    public function store(CompanyRequest $request, StoreCompanyAccountService $storeCompanyAccountService)
    {
        /** @var \Botble\JobBoard\Models\Company $company */
        $company = Company::query()->create($request->input());

        event(new CreatedContentEvent(COMPANY_MODULE_SCREEN_NAME, $request, $company));

        $storeCompanyAccountService->execute($request, $company);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('companies.index'))
            ->setNextUrl(route('companies.edit', $company->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Company $company, Request $request)
    {
        event(new BeforeEditContentEvent($request, $company));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $company->name]));

        return CompanyForm::createFromModel($company)->renderForm();
    }

    public function update(Company $company, CompanyRequest $request, StoreCompanyAccountService $storeCompanyAccountService)
    {
        $company->fill($request->input());
        $company->save();

        $storeCompanyAccountService->execute($request, $company);

        event(new UpdatedContentEvent(COMPANY_MODULE_SCREEN_NAME, $request, $company));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('companies.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Company $company)
    {
        return DeleteResourceAction::make($company);
    }

    public function getList(Request $request)
    {
        $keyword = $request->input('q');

        if (! $keyword) {
            return $this
                ->httpResponse()
                ->setData([]);
        }

        $data = Company::query()
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->select(['id', 'name'])
            ->paginate(10);

        return $this
            ->httpResponse()
            ->setData(CompanyResource::collection($data));
    }

    public function ajaxGetCompany(int|string $id)
    {
        $company = Company::query()->findOrFail($id);

        return $this
            ->httpResponse()
            ->setData(new CompanyResource($company));
    }

    public function ajaxCreateCompany(AjaxCompanyRequest $request)
    {
        $company = Company::query()->create($request->input());

        event(new CreatedContentEvent(COMPANY_MODULE_SCREEN_NAME, $request, $company));

        return $this
            ->httpResponse()
            ->setData(new CompanyResource($company));
    }

    public function getAllCompanies()
    {
        return Company::query()->pluck('name')->all();
    }

    public function analytics(int $id)
    {
        $company = Company::query()
            ->where('id', $id)
            ->withCount(['jobs'])
            ->firstOrFail();

        Assets::addScripts(['counterup', 'equal-height'])
            ->addStylesDirectly('vendor/core/core/dashboard/css/dashboard.css');

        $this->pageTitle(__('Analytics for company ":name"', ['name' => $company->name]));

        return view('plugins/job-board::company.analytics', compact('company'));
    }
}
