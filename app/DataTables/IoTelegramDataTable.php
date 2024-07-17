<?php

namespace App\DataTables;

use App\Models\io_files;
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
            ->addColumn('action', function ($row) {
                $is_file = io_files::where('iotelegram_id', $row->id)->exists();
                $uploadButton = $is_file
                    ? '<a href="' . route('iotelegram.files', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-upload"></i></a>'
                    : '<a href="' . route('iotelegram.files.view', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-file"></i></a>';

                return '
                    <a href="' . route('iotelegram.show', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                    <a href="' . route('iotelegram.edit', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                    ' . $uploadButton . '';
            })
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

            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make('id')->title('الرقم'),
            Column::make('from_departement')->title('الجهة المرسلة'),
            Column::make('date')->title('التاريخ'),
            Column::make('representive_id')->title('المستلم'),
            // Column::make('')->title('الموضوع'),
            // Column::make('')->title('الملاحظات'),
            Column::computed('action')
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
