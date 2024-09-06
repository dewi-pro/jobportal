<?php

namespace Botble\JobBoard\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\JobBoard\Enums\DatabaseInfoStatusEnum;
use Botble\JobBoard\Http\Requests\EditDatabaseInfoRequest;
use Botble\JobBoard\Models\DatabaseInfo;

class DatabaseInfoForm extends FormAbstract
{
    public function setup(): void
    {
        Assets::addStylesDirectly('vendor/core/plugins/job-board/css/application.css');

        $this
            ->setupModel(new DatabaseInfo())
            ->setValidatorClass(EditDatabaseInfoRequest::class)
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(DatabaseInfoStatusEnum::labels())->toArray())
            ->setBreakFieldPoint('status')
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins/job-board::database-info.information'),
                    'content' => view('plugins/job-board::dbInfo', ['databaseInfo' => $this->getModel()])->render(),
                    'attributes' => [
                        'style' => 'margin-top: 0',
                    ],
                ],
            ]);
    }
}
