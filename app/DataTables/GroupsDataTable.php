<?php

namespace App\DataTables;

use App\Models\Groups;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Html\Editor\Editor;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class GroupsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', function ($row) {
                return '
                <button type="button" class="wide-btn" data-bs-toggle="modal" data-bs-target="#edit' . $row->id . '" style="color: #0D992C;">
                    <i class="fa fa-edit"></i>
                </button>
                <div class="modal fade" id="edit' . $row->id . '" tabindex="-1" aria-labelledby="editLabel' . $row->id . '" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editLabel' . $row->id . '">إضافة مجموعة  جديدة</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="saveExternalUser" action="' . route('group.edit', $row->id) . '" method="POST">
                                    ' . csrf_field() . '
                                    <div class="mb-3">
                                        <label for="name">اسم المجموعة</label>
                                        <input type="text" id="name' . $row->id . '" value="' . $row->name . '" name="name" class="form-control" required>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">حفظ</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Groups $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('groups-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
            Column::make('name'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'groups_' . date('YmdHis');
    }
}
