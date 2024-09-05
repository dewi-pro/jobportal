@extends('core/base::forms.form')

@section('form_end')
    @if ($form->getModel()->id)
        <x-core::modal
            id="add-credit-modal"
            :title="trans('plugins/job-board::account.add_credit_to_account')"
            :button-label="trans('plugins/job-board::account.action_table.add')"
            button-id="confirm-add-credit-button"
        >
            @include('plugins/job-board::accounts.credit-form', ['account' => $form->getModel()])
        </x-core::modal>

        <x-core::modal
            id="edit-education-modal"
            :title="trans('plugins/job-board::account.edit_education')"
        >
            <x-core::loading />
            <x-slot:footer>
                <x-core::button
                    data-bs-dismiss="modal"
                >
                    {{ trans('core/base::base.close') }}
                </x-core::button>

                <x-core::button
                    class="ms-auto"
                    color="primary"
                    id="confirm-edit-education-button"
                >
                    {{ trans('plugins/job-board::account.action_table.edit') }}
                </x-core::button>
            </x-slot:footer>
        </x-core::modal>

        <x-core::modal
            id="edit-experience-modal"
            :title="trans('plugins/job-board::account.edit_experience')"
        >
            <x-core::loading />
            <x-slot:footer>
                <x-core::button
                    data-bs-dismiss="modal"
                >
                    {{ trans('core/base::base.close') }}
                </x-core::button>

                <x-core::button
                    class="ms-auto"
                    color="primary"
                    id="confirm-edit-experience-button"
                >
                    {{ trans('plugins/job-board::account.action_table.edit') }}
                </x-core::button>
            </x-slot:footer>
        </x-core::modal>
    @endif

    {!! Form::modalAction(
        'add-education-modal',
        trans('plugins/job-board::account.add_education'),
        'info',
        FormBuilder::create(\Botble\JobBoard\Forms\AccountEducationForm::class, [
            'data' => [
                'account' => $form->getModel(),
            ],
        ])->renderForm(),
        'confirm-add-education-button',
        trans('plugins/job-board::account.action_table.add'),
        'modal-md',
    ) !!}

    {!! Form::modalAction(
        'add-experience-modal',
        trans('plugins/job-board::account.add_experience'),
        'info',
        FormBuilder::create(\Botble\JobBoard\Forms\AccountExperienceForm::class, [
            'data' => [
                'account' => $form->getModel(),
            ],
        ])->renderForm(),
        'confirm-add-experience-button',
        trans('plugins/job-board::account.action_table.add'),
        'modal-md',
    ) !!}

    <x-core::modal.action
        type="danger"
        id="modal-confirm-delete"
        :title="trans('core/base::tables.confirm_delete')"
        :description="trans('core/base::tables.confirm_delete_msg')"
        :submit-button-label="trans('core/base::tables.delete')"
        :submit-button-attrs="['data-bb-toggle' => 'confirm-delete']"
    />
@stop
