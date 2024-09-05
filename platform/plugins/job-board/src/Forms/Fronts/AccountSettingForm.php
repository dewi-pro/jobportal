<?php

namespace Botble\JobBoard\Forms\Fronts;

use Botble\Base\Facades\Html;
use Botble\Base\Forms\FieldOptions\EditorFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TagFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TagField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\FormFieldOptions;
use Botble\JobBoard\Enums\AccountGenderEnum;
use Botble\JobBoard\Enums\AccountTypeEnum;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Http\Requests\SettingRequest;
use Botble\JobBoard\Models\Account;
use Botble\JobBoard\Models\JobSkill;
use Botble\JobBoard\Models\Tag;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;

class AccountSettingForm extends FormAbstract
{
    public function setup(): void
    {
        $jobSkills = [];
        $jobTags = [];
        $selectedJobSkills = [];
        $selectedJobTags = [];
        $account = $this->getModel();

        if ($account->isJobSeeker()) {
            $selectedJobSkills = $account->favoriteSkills()->pluck('jb_job_skills.id')->all();

            $jobSkills = JobSkill::query()
                ->wherePublished()
                ->pluck('name', 'id')
                ->toArray();

            $selectedJobTags = $account->favoriteTags()->pluck('jb_tags.id')->all();

            $jobTags = Tag::query()
                ->wherePublished()
                ->pluck('name', 'id')
                ->toArray();
        }

        $this
            ->setupModel(new Account())
            ->setValidatorClass(SettingRequest::class)
            ->contentOnly()
            ->add(
                'first_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('First Name'))
                    ->placeholder(__('Enter first name'))
                    ->required()
                    ->toArray()
            )
            ->add(
                'last_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Last name'))
                    ->placeholder(__('Enter last name'))
                    ->required()
                    ->toArray()
            )
            ->add(
                'phone',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Phone'))
                    ->placeholder(__('Enter phone number'))
                    ->toArray()
            )
            ->add(
                'dob',
                'date',
                TextFieldOption::make()
                    ->label(__('Date of birth'))
                    ->value($this->getModel()->dob ? $this->getModel()->dob->toDateString() : null)
                    ->attributes([
                        'max' => Carbon::now()->toDateString(),
                    ])
                    ->toArray()
            )
            ->add(
                'gender',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Gender'))
                    ->choices(AccountGenderEnum::labels())
                    ->toArray()
            )
            ->when(is_plugin_active('location'), function (FormAbstract $form) {
                $form->add(
                    'location_data',
                    SelectLocationField::class,
                    SelectLocationFieldOption::make()
                        ->toArray()
                );
            })
            ->add(
                'address',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Address'))
                    ->placeholder(__('Enter address'))
                    ->toArray()
            )
            ->when(! $this->getModel()->type->getKey() && setting('job_board_enabled_register_as_employer'), function (AccountSettingForm $form) {
                $form
                    ->add(
                        'type',
                        SelectField::class,
                        SelectFieldOption::make()
                            ->label(__('Type'))
                            ->choices(AccountTypeEnum::labels())
                            ->toArray()
                    );
            })
            ->when($this->getModel()->isJobSeeker(), function (AccountSettingForm $form) use ($selectedJobTags, $jobTags, $selectedJobSkills, $jobSkills) {
                $form->when(! empty($jobSkills) || ! empty($selectedJobSkills), function (AccountSettingForm $form) use ($selectedJobSkills, $jobSkills) {
                    $form->add(
                        'favorite_skills',
                        TagField::class,
                        TagFieldOption::make()
                            ->label(__('Favorite job skills'))
                            ->choices($jobSkills)
                            ->selected(implode(',', $selectedJobSkills))
                            ->toArray()
                    );
                })
                ->when(! empty($jobTags) || ! empty($selectedJobTags), function (AccountSettingForm $form) use ($selectedJobTags, $jobTags) {
                    $form->add(
                        'favorite_tags',
                        TagField::class,
                        TagFieldOption::make()
                            ->label(__('Favorite job tags'))
                            ->choices($jobTags)
                            ->selected(implode(',', $selectedJobTags))
                            ->toArray()
                    );
                })
                ->add(
                    'title_profile',
                    HtmlField::class,
                    HtmlFieldOption::make()
                        ->content(sprintf('<h5 class="fs-17 fw-semibold mb-3">%s</h5>', __('Profile')))
                        ->toArray()
                )
                ->when(! JobBoardHelper::isDisabledPublicProfile(), function (FormAbstract $form) {
                    $form
                        ->add(
                            'is_public_profile',
                            OnOffField::class,
                            OnOffFieldOption::make()
                                ->label(__('Public your profile?'))
                                ->toArray()
                        )
                        ->add(
                            'hide_cv',
                            OnOffField::class,
                            OnOffFieldOption::make()
                                ->label(__('Hide your CV?'))
                                ->toArray()
                        );
                })
                ->add(
                    'available_for_hiring',
                    OnOffField::class,
                    OnOffFieldOption::make()
                        ->label(__('Available for hiring?'))
                        ->toArray()
                );
            })
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(__('Introduce Yourself'))
                    ->toArray()
            )
            ->add(
                'bio',
                EditorField::class,
                EditorFieldOption::make()
                    ->addAttribute('without-buttons', true)
                    ->allowedShortcodes(false)
                    ->label(__('Bio'))
                    ->toArray()
            )
            ->when($this->getModel()->isJobSeeker(), function (AccountSettingForm $form) use ($account) {
                $form->add(
                    'resume',
                    'file',
                    FormFieldOptions::make()
                        ->label(__('Attachments CV'))
                        ->when($account->resume, function (FormFieldOptions $fieldOptions) use ($account) {
                            return $fieldOptions->helperText(
                                __('Your current resume :resume. Just upload a new resume if you want to change it.', [
                                    'resume' => Html::link(RvMedia::url($account->resume), $account->resume, ['target' => '_blank'])->toHtml(),
                                ])
                            );
                        })
                        ->toArray()
                )
                ->add(
                    'cover_letter',
                    'file',
                    FormFieldOptions::make()
                        ->label(__('Cover letter'))
                        ->when($account->cover_letter, function (FormFieldOptions $fieldOptions) use ($account) {
                            return $fieldOptions->helperText(
                                __('Your current cover letter :cover_letter. Just upload a new resume if you want to change it.', [
                                    'cover_letter' => Html::link(RvMedia::url($account->cover_letter), $account->cover_letter, ['target' => '_blank'])->toHtml(),
                                ])
                            );
                        })
                        ->toArray()
                )
                ->add(
                    'cover_image',
                    'file',
                    FormFieldOptions::make()
                        ->label(__('Cover image'))
                        ->toArray()
                );
            });
    }
}
