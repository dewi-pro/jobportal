<?php

namespace Botble\JobBoard\Forms;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\FormAbstract;
use Botble\JobBoard\Enums\JobRecruitmentProgressStatusEnum;
use Botble\JobBoard\Enums\ModerationStatusEnum;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Http\Requests\RecruitmentRequest;
use Botble\JobBoard\Models\RecruitmentProgress;

class RecruitmentProgressForm extends FormAbstract
{
   public function setup(): void
{
    $model = $this->getModel(); 
    if (!$model) {
    }
    
    $this
        ->setupModel(new RecruitmentProgress())
        ->setValidatorClass(RecruitmentRequest::class)
        ->columns(12)
        ->add('tanggal_FPK', 'datePicker', [
            'label' => __('Tanggal FPK'),
            'required' => true,
            'name' => 'tanggal_FPK',
            'id' => 'tanggal_FPK',
            'value' => $model->id ? BaseHelper::formatDate($model->tanggal_FPK) : '',
            'colspan' => 6,
        ])
        ->add('cabang', 'text', [
            'label' => __('Cabang / Dept.'),
            'required' => true,
            'name' => 'cabang',
            'id' => 'cabang',
            'value' => $model->cabang,
            'attr' => [
                'placeholder' => trans('core/base::forms.name_placeholder'),
                'data-counter' => 50,
            ],
        ])
        ->add('user', 'text', [
            'label' => __('User'),
            'required' => true,
            'name' => 'user',
            'id' => 'user',
            'value' => $model->user,
            'attr' => [
                'placeholder' => trans('core/base::forms.name_placeholder'),
                'data-counter' => 120,
            ],
        ])
        ->add('posisi', 'text', [
            'label' => __('Posisi'),
            'required' => true,
            'name' => 'posisi',
            'id' => 'posisi',
            'value' => $model->posisi,
            'attr' => [
                'placeholder' => trans('core/base::forms.name_placeholder'),
                'data-counter' => 50,
            ],
        ])
        ->add('nama_kandidat', 'text', [
            'label' => __('Nama Kandidat'),
            'required' => true,
            'name' => 'nama_kandidat',
            'id' => 'nama_kandidat',
            'value' => $model->nama_kandidat,
            'attr' => [
                'placeholder' => trans('core/base::forms.name_placeholder'),
                'data-counter' => 120,
            ],
        ])
        ->add('psikotes', 'datePicker', [
            'label' => __('Psikotes'),
            'name' => 'psikotes',
            'id' => 'psikotes',
            'value' => $this->getModel()->id ? BaseHelper::formatDate($this->getModel()->psikotes) : '',
            'colspan' => 6,
        ])
        ->add('offering_letter', 'datePicker', [
            'label' => __('Offering Letter'),
            'name' => 'offering_letter',
            'id' => 'offering_letter',
            'value' => $this->getModel()->id ? BaseHelper::formatDate($this->getModel()->offering_letter) : '',
            'colspan' => 6,
        ])
        ->add('recruitment', 'datePicker', [
            'label' => __('Recruitment'),
            'name' => 'recruitment',
            'id' => 'recruitment',
            'value' => $this->getModel()->id ? BaseHelper::formatDate($this->getModel()->recruitment) : '',
            'colspan' => 6,
        ])
        ->add('user_date', 'datePicker', [
            'label' => __('User Date'),
            'name' => 'user_date',
            'id' => 'user_date',
            'value' => $this->getModel()->id ? BaseHelper::formatDate($this->getModel()->user_date) : '',
            'colspan' => 6,
        ])
        ->add('tanggal_masuk', 'datePicker', [
            'label' => __('Tanggal Masuk'),
            'name' => 'tanggal_masuk',
            'id' => 'tanggal_masuk',
            'value' => $this->getModel()->id ? BaseHelper::formatDate($this->getModel()->tanggal_masuk) : '',
            'colspan' => 6,
        ])
        ->add('status', SelectField::class, StatusFieldOption::make()->choices(JobRecruitmentProgressStatusEnum::labels())->toArray())
        ->add('proses', 'number', [
            'label' => __('Proses'),
            'name' => 'proses',
            'id' => 'proses',
            'value' => $model->proses,
            'attr' => [
                'placeholder' => __('Proses'),
            ],
            'default_value' => 1,
            'colspan' => 6,
        ])
        ->add('sumber', 'text', [
            'label' => __('Sumber'),
            'required' => true,
            'name' => 'sumber',
            'id' => 'sumber',
            'value' => $model->sumber,
            'attr' => [
                'placeholder' => trans('core/base::forms.name_placeholder'),
                'data-counter' => 15,
            ],
        ])
        ->add('catatan', TextareaField::class, DescriptionFieldOption::make()->toArray());
}

    
}
