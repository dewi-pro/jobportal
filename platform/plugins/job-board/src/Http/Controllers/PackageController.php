<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Forms\PackageForm;
use Botble\JobBoard\Http\Requests\PackageRequest;
use Botble\JobBoard\Models\Package;
use Botble\JobBoard\Tables\PackageTable;
use Exception;
use Illuminate\Http\Request;

class PackageController extends BaseController
{
    public function index(PackageTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::package.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::package.create'));

        return PackageForm::create()->renderForm();
    }

    public function store(PackageRequest $request)
    {
        if (! $request->input('price')) {
            $request->merge(['price' => 0]);
        }

        $package = Package::query()->create($request->input());

        event(new CreatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('packages.index'))
            ->setNextUrl(route('packages.edit', $package->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Package $package, Request $request)
    {
        event(new BeforeEditContentEvent($request, $package));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $package->name]));

        return PackageForm::createFromModel($package)->renderForm();
    }

    public function update(Package $package, PackageRequest $request)
    {
        if (! $request->input('price')) {
            $request->merge(['price' => 0]);
        }

        $package->fill($request->input());
        $package->save();

        event(new UpdatedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('packages.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Package $package, Request $request)
    {
        try {
            $package->delete();

            event(new DeletedContentEvent(PACKAGE_MODULE_SCREEN_NAME, $request, $package));

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
