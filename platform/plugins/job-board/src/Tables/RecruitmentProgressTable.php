<?php

namespace Botble\JobBoard\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\JobBoard\Enums\JobRecruitmentProgressStatusEnum;
use Botble\JobBoard\Enums\ModerationStatusEnum;
use Botble\JobBoard\Models\RecruitmentProgress;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\Action;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\EnumColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class RecruitmentProgressTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(RecruitmentProgress::class)
            ->addActions([
                EditAction::make()->route('recruitment.edit'),
                DeleteAction::make()->route('recruitment.destroy'),
            ]);
    }
    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            
            ->editColumn('tanggal_FPK', function (RecruitmentProgress $item) {
                return $item->tanggal_FPK->format('d/m/Y');
            })
            ->editColumn('psikotes', function (RecruitmentProgress $item) {
                return $item->psikotes ? $item->psikotes->format('d/m/Y'): '&mdash;';
            })
            ->editColumn('offering_letter', function (RecruitmentProgress $item) {
                return $item->offering_letter?$item->offering_letter->format('d/m/Y'): '&mdash;';
            })
            ->editColumn('recruitment', function (RecruitmentProgress $item) {
                return $item->recruitment?$item->recruitment->format('d/m/Y'): '&mdash;';
            })
            ->editColumn('user_date', function (RecruitmentProgress $item) {
                return $item->user_date?$item->user_date->format('d/m/Y'): '&mdash;';
            })
            ->editColumn('tanggal_masuk', function (RecruitmentProgress $item) {
                return $item->tanggal_masuk ? $item->tanggal_masuk->format('d/m/Y') : '&mdash;';
            })
            ->editColumn('cabang', function ($item) {
                return $item->cabang ?: '&mdash;';
            })
            ->editColumn('user', function ($item) {
                return $item->user ?: '&mdash;';
            })
            ->editColumn('posisi', function ($item) {
                return $item->posisi ?: '&mdash;';
            })
            ->editColumn('proses', function ($item) {
                return $item->proses ?: '&mdash;';
            })
            ->editColumn('sumber', function ($item) {
                return $item->sumber ?: '&mdash;';
            })
            ->editColumn('catatan', function ($item) {
                return $item->catatan ?: '&mdash;';
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
                'tanggal_FPK',
                'cabang',
                'user',
                'posisi',
                'nama_kandidat',
                'psikotes',
                'offering_letter',
                'recruitment',
                'user_date',
                'tanggal_masuk',
                'status',
                'proses',
                'sumber',
                'catatan',
                'job_application_id',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            StatusColumn::make(),
            Column::make('tanggal_FPK')
                ->title(__('Tanggal FPK'))
                ->alignLeft(),
            Column::make('cabang')
                ->title(__('Cabang/Dept'))
                ->alignLeft(),
            Column::make('user')
                ->title(__('User'))
                ->alignLeft(),
            Column::make('posisi')
                ->title(__('Posisi'))
                ->alignLeft(),
            Column::make('nama_kandidat')
                ->title(__('Nama Kandidat'))
                ->alignLeft()
                ->route('recruitment.edit'),
            Column::make('recruitment')
                ->title(__('Recruitment'))
                ->alignLeft(),
            Column::make('user_date')
                ->title(__('User'))
                ->alignLeft(),
            Column::make('psikotes')
                ->title(__('Psikotes'))
                ->alignLeft(),
            Column::make('offering_letter')
                ->title(__('Offering letter'))
                ->alignLeft(),
            Column::make('tanggal_masuk')
                ->title(__('Tanggal Masuk'))
                ->alignLeft(),
            Column::make('proses')
                ->title(__('Proses'))
                ->alignLeft(),
            Column::make('sumber')
                ->title(__('Sumber'))
                ->alignLeft(),
            Column::make('catatan')
                ->title(__('Catatan')),
        ];
    }

    public function buttons(): array
    {
        $buttons = $this->addCreateButton(route('recruitment.create'), 'recruitment.create');

        // if ($this->hasPermission('import-jobs.index')) {
        //     $buttons['import'] = [
        //         'link' => route('import-jobs.index'),
        //         'text' =>
        //             BaseHelper::renderIcon('ti ti-upload')
        //             . trans('plugins/job-board::import.name'),
        //     ];
        // }

        // if ($this->hasPermission('export-jobs.index')) {
        //     $buttons['export'] = [
        //         'link' => route('export-jobs.index'),
        //         'text' =>
        //             BaseHelper::renderIcon('ti ti-download')
        //             . trans('plugins/job-board::export.jobs.name'),
        //     ];
        // }

        return $buttons;
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('recruitment.destroy'),
        ];
    }

    // public function getBulkChanges(): array
    // {
    //     return [
    //         'nama_kandidat' => [
    //             'title' => trans('core/base::tables.name'),
    //             'type' => 'text',
    //             'validate' => 'required|max:120',
    //         ],
    //         'status' => [
    //             'title' => trans('core/base::tables.status'),
    //             'type' => 'select',
    //             'choices' => JobRecruitmentProgressStatusEnum::labels(),
    //             'validate' => 'required|in:' . implode(',', JobRecruitmentProgressStatusEnum::values()),
    //         ],
    //         'tanggal_FPK' => [
    //             'title' => 'Tanggal FPK',
    //             'type' => 'datePicker',
    //         ],
    //         'psikotes' => [
    //             'title' => 'Psikotes ',
    //             'type' => 'datePicker',
    //         ],
    //         'offering_letter' => [
    //             'title' => 'Offering Letter',
    //             'type' => 'datePicker',
    //         ],
    //         'recruitment' => [
    //             'title' => 'Recruitment',
    //             'type' => 'datePicker',
    //         ],
    //         'user_date' => [
    //             'title' => 'User',
    //             'type' => 'datePicker',
    //         ],
    //         'tanggal_masuk' => [
    //             'title' => 'Tanggal Masuk',
    //             'type' => 'datePicker',
    //         ],
    //     ];
    // }

}
