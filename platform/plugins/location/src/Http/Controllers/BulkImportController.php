<?php

namespace Botble\Location\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Location\Exports\TemplateLocationExport;
use Botble\Location\Facades\Location;
use Botble\Location\Http\Requests\ImportLocationRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class BulkImportController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/location::location.name'))
            ->add(trans('plugins/location::bulk-import.name'), route('location.bulk-import.index'));
    }

    public function index()
    {
        $this->pageTitle(trans('plugins/location::bulk-import.name'));

        $mimetypes = collect(config('plugins.location.general.bulk-import.mime_types', []))->implode(',');

        Assets::addScriptsDirectly('vendor/core/plugins/location/js/bulk-import.js')
            ->addScripts('dropzone')
            ->addStyles('dropzone');

        $countries = Location::getAvailableCountries();

        return view('plugins/location::bulk-import.index', compact('mimetypes', 'countries'));
    }

    public function downloadTemplate(Request $request)
    {
        $extension = $request->input('extension');
        $extension = $extension === 'csv' ? $extension : Excel::XLSX;
        $writeType = $extension === 'csv' ? Excel::CSV : Excel::XLSX;
        $contentType = $extension === 'csv' ? ['Content-Type' => 'text/csv'] : ['Content-Type' => 'text/xlsx'];
        $fileName = 'template_locations_import.' . $extension;

        return (new TemplateLocationExport($extension))->download($fileName, $writeType, $contentType);
    }

    public function importLocationData(ImportLocationRequest $request)
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        $result = Location::downloadRemoteLocation(
            strtolower($request->input('country_code')),
            $request->boolean('continue')
        );

        return $this
            ->httpResponse()
            ->setError($result['error'])
            ->setMessage($result['message']);
    }
}
