<?php

namespace Botble\JobBoard\Tables;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\JobBoard\Models\Company;
use Botble\JobBoard\Models\Review;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\StatusColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;

class ReviewTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Review::class)
            ->addActions([
                DeleteAction::make()->route('reviews.destroy'),
            ]);
    }

    public function ajax(): JsonResponse
    {
        $data = $this->table
            ->eloquent($this->query())
            ->editColumn('reviewable_id', function (Review $item) {
                if (! $item->reviewable_id || ! $item->reviewable?->id) {
                    return '&mdash;';
                }

                return Html::link(
                    route(
                        $item->reviewable_type === Company::class ? 'companies.edit' : 'accounts.edit',
                        $item->reviewable_id
                    ),
                    BaseHelper::clean($item->reviewable->name)
                )->toHtml();
            })
            ->editColumn('created_by_id', function (Review $item) {
                if (! $item->created_by_id || ! $item->createdBy?->id) {
                    return '&mdash;';
                }

                return Html::link(
                    route(
                        $item->created_by_type === Company::class ? 'companies.edit' : 'accounts.edit',
                        $item->created_by_id
                    ),
                    BaseHelper::clean($item->createdBy->name)
                )->toHtml();
            })
            ->editColumn('star', function (Review $item) {
                return view('plugins/job-board::reviews.partials.rating', ['star' => $item->star])->render();
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
                'star',
                'review',
                'reviewable_id',
                'reviewable_type',
                'created_by_id',
                'created_by_type',
                'status',
                'created_at',
            ])
            ->with(['account', 'reviewable']);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('reviewable_id')
                ->title(trans('plugins/job-board::review.account_or_company'))
                ->alignLeft(),
            Column::make('created_by_id')
                ->title(trans('plugins/job-board::review.reviewed_by'))
                ->alignLeft(),
            Column::make('star')
                ->title(trans('plugins/job-board::review.star')),
            Column::make('review')
                ->title(trans('plugins/job-board::review.review'))
                ->alignLeft(),
            StatusColumn::make(),
            CreatedAtColumn::make(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('reviews.destroy'),
        ];
    }
}
