<?php

namespace Botble\JobBoard\Tables;

use Botble\Base\Facades\Html;
use Botble\JobBoard\Enums\DatabaseInfoStatusEnum;
use Botble\JobBoard\Models\DatabaseInfo;
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

class DatabaseInfoTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(DatabaseInfo::class)
            ->addActions([
                EditAction::make()->route('db.edit'),
                DeleteAction::make()->route('db.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $defaultStatus = DatabaseInfoStatusEnum::SAVEDB;
        $query = $this->query()->where('status', $defaultStatus);

        $data = $this->table
            ->eloquent($query)
            ->editColumn('job_id', function (DatabaseInfo $item) {
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
            ->editColumn('full_name', function (DatabaseInfo $item) {
                return trim($item->first_name . ' ' . $item->last_name) ?: '&mdash;';
            })
            ->editColumn('phone', function (DatabaseInfo $item) {
                return $item->phone ?: '&mdash;';
            })
            ->editColumn('is_external_apply', function (DatabaseInfo $item) {
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
                ->title(trans('plugins/job-board::database-info.tables.email'))
                ->alignLeft(),
            Column::make('phone')
                ->title(trans('plugins/job-board::database-info.tables.phone'))
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
            DeleteBulkAction::make()->permission('database-infos.destroy'),
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
