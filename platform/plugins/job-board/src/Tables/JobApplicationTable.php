<?php

namespace Botble\JobBoard\Tables;

use Botble\Base\Facades\Html;
use Botble\JobBoard\Enums\JobApplicationStatusEnum;
use Botble\JobBoard\Models\JobApplication;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class JobApplicationTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(JobApplication::class)
            ->addActions([
                EditAction::make()->route('job-applications.edit'),
                DeleteAction::make()->route('job-applications.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('job_id', function (JobApplication $item) {
                if (! $item->job->name) {
                    return '&mdash;';
                }

                return Html::link(
                    $item->job->url,
                    $item->job->name . ' ' . Html::tag('i', '', ['class' => 'fas fa-external-link-alt']),
                    ['target' => '_blank'],
                    null,
                    false
                );
            })
            ->editColumn('full_name', function (JobApplication $item) {
                return trim($item->first_name . ' ' . $item->last_name) ?: '&mdash;';
            })            
            ->editColumn('phone', function (JobApplication $item) {
                return $item->phone ?: '&mdash;';
            })
            ->editColumn('is_external_apply', function (JobApplication $item) {
                return $item->is_external_apply ? __('External') : __('Internal');
            });
        return $this->toJson($data);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this
            ->getModel()
            ->query()
            ->select([
                'id',
                'job_id',
                'email',
                'phone',
                'created_at',
                'is_external_apply',
                'status',
                'first_name',
                'last_name',
            ])
            ->with(['job', 'job.slugable']);


        return $this->applyScopes($query);
    }


    public function columns(): array
    {
        return [
            IdColumn::make(),
            StatusColumn::make(),
            Column::make('job_id')
                ->title(__('Job Name'))
                ->alignLeft(),
            Column::make('full_name')
                ->title(__('Kandidat'))
                ->alignLeft(),
            Column::make('email')
                ->title(trans('plugins/job-board::job-application.tables.email'))
                ->alignLeft(),
            Column::make('phone')
                ->title(trans('plugins/job-board::job-application.tables.phone'))
                ->alignLeft(),
            // Column::make('is_external_apply')
            //     ->title(__('Type')),
            CreatedAtColumn::make()
            ->title(__('Submit Form')),
        ];
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('job-applications.destroy'),
        ];
    }

    public function getBulkChanges(): array
    {
        return [
            'first_name' => [
                'title' => __('First name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'last_name' => [
                'title' => __('Last name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'job_id' => [
                'title' => __('Job Name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'email' => [
                'title' => trans('core/base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'customSelect',
                'choices' => JobApplicationStatusEnum::labels(),
                'validate' => 'required|in:' . implode(',', JobApplicationStatusEnum::values()),
            ],
            'created_at' => [
                'title' => __('Submit Form'),
                'type' => 'datePicker',
            ],
        ];
    }

    public function getFilters(): array
    {
        return [
            'first_name' => [
                'title' => __('First name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'last_name' => [
                'title' => __('Last name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'job_id' => [
                'title' => __('Job Name'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'email' => [
                'title' => trans('core/base::tables.email'),
                'type' => 'text',
                'validate' => 'required|max:120',
            ],
            'created_at' => [
                'title' => __('Submit Form'),
                'type' => 'datePicker',
            ],
            'status' => [
                'title' => trans('core/base::tables.status'),
                'type' => 'select',
                'choices' => JobApplicationStatusEnum::labels(),
                'validate' => 'required|' . Rule::in(JobApplicationStatusEnum::values()),
            ],
            // 'is_external_apply' => [
            //     'title' => __('Type'),
            //     'type' => 'select',
            //     'choices' => [0 => __('Internal'), 1 => __('External')],
            // ],
        ];
    }

    public function getDefaultButtons(): array
    {
        return [
            'export',
            'reload',
        ];
    }
}
