<?php

namespace App\DataTables;

use App\Models\iotelegrams;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class IoTelegramDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'iotelegrams.action')
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(iotelegrams $model): QueryBuilder
    {
        return $model->newQuery()->with(['created_by', 'recieved_by', 'representive', 'updated_by', 'department']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('iotelegrams-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([
                Button::make('excel'),
                Button::make('csv'),
                Button::make('pdf'),
                Button::make('print'),
                Button::make('reset'),
                Button::make('reload')
            ])->parameters([
                'initComplete' => 'function () {
                            this.api().columns().every(function () {
                                var column = this;
                                var input = document.createElement("input");
                                $(input).appendTo($(column.header()).empty())
                                    .on("keyup change clear", function () {
                                        if (column.search() !== this.value) {
                                            column.search(this.value).draw();
                                        }
                                    });
                            });
                        }',
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('الرقم'),
            Column::make('الجهة المرسلة'),
            Column::make('التاريخ'),
            Column::make('المستلم'),
            Column::make('الموضوع'),
            Column::make('الملاحظات'),
            Column::computed('الخيارات')
                ->exportable(false)
                ->printable(false)
                ->searchable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Iotelegrams_' . date('YmdHis');
    }
}
