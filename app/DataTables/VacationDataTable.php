<?php

namespace App\DataTables;

use App\Models\io_files;
use App\Models\EmployeeVacation;
use App\Models\UserVaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VacationDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('employee_id', function ($row) {
                return $row->employee->name ?? '';  // Use null coalescing operator to handle null values
            })
            ->addColumn('vacation_type_id', function ($row) {
                return $row->vacation_type->name ?? '';  // Use null coalescing operator to handle null values

            })

            ->addColumn('action', 'employee_vactions.action')
            ->addColumn('action', function ($row) {
                CheckStartVacationDate($row->id)

                    ?   $deleteButton = '<a href="' . route('vacation.delete', $row->id) . '" class="delete btn btn-success btn-sm"><i class="fa fa-trash"></i></a>'
                    :   $deleteButton   = '';


                return
                    '<a href="' . route('vacation.edit', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>' . '
                    <a href="' . route('vacation.show', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                    ' . $deleteButton . '';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(EmployeeVacation $model): QueryBuilder
    {
        return $model->newQuery()->with(['created_by', 'updated_by', 'employee', 'vacation_type']);
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('UserVaction-table')
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
            Column::make('date_from')->title('تاريخ البداية'),
            Column::make('date_to')->title('تاريخ النهاية'),
            Column::make('employee_id')->title('الموظف'),
            Column::make('vacation_type_id')->title('نوع الاجازة'),
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
        return 'UserVaction_' . date('YmdHis');
    }
}
