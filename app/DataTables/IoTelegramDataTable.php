<?php

namespace App\DataTables;

use App\Models\io_files;
use App\Models\Iotelegram;
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
            ->addColumn('representive_id', function ($row) {
                return $row->representive->name ?? '';  // Use null coalescing operator to handle null values
            })
            ->addColumn('from_departement', function ($row) {
                if ($row->type == 'in') {
                    return $row->internal_department->name ?? '';  // Use null coalescing operator to handle null values

                } else {
                    return $row->external_department->name ?? '';  // Use null coalescing operator to handle null values

                }
            })
            ->addColumn('recieved_by', function ($row) {
                return $row->recieved_by->name ?? '';  // Use null coalescing operator to handle null values
            })
            ->addColumn('type', function ($row) {
                if ($row->type == 'in') {

                    return 'داخلي';  // Use null coalescing operator to handle null values
                } else {
                    return 'خارجي';  // Use null coalescing operator to handle null values

                }
            })
            ->addColumn('action', 'iotelegrams.action')
            ->addColumn('action', function ($row) {
                $archiveUrl = route('iotelegram.archive.add', $row->id);

                CheckUploadIoFiles($row->id)

                    ?   $uploadButton = '<a href="' . $archiveUrl . '" class="archive btn btn-success btn-sm" onclick="confirmArchive(event, this)"> <i class="fa fa-archive"></i> </a>'
                    :   $uploadButton   = '<a href="' . route('iotelegram.edit', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>';

                return '
                    <a href="' . route('iotelegram.show', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                    ' . $uploadButton . '';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Iotelegram $model): QueryBuilder
    {
        return $model->newQuery()->where('active', 1)->with(['created_by', 'recieved_by', 'representive', 'updated_by', 'created_department', 'internal_department', 'external_department']);
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
            Column::make('date')->title('التاريخ'),
            Column::make('from_departement')->title('الجهة المرسلة'),
            Column::make('representive_id')->title('المندوب'),
            Column::make('recieved_by')->title('الموظف المستلم'),
            Column::make('files_num')->title('عدد الفايلات'),
            Column::make('type')->title('النوع'),
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
