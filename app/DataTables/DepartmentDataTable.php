<?php

namespace App\DataTables;

use App\Models\departements;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class DepartmentDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addColumn('action', 'department.action')
            ->addColumn('iotelegrams_count', function ($row) {
                return $row->iotelegrams_count;
            })
            ->addColumn('outgoings_count', function ($row) {
                return $row->outgoings_count;
            })
            ->addColumn('children_count', function ($row) { // New column for departments count
                return $row->children_count;
            })
            ->addColumn('action', function ($row) {
                return '
                    <a href="' . route('departments.show', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                    <a href="' . route('departments.edit', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                    <form action="' . route('departments.destroy', $row->id) . '" method="POST" style="display:inline;">
                        ' . csrf_field() . '
                        ' . method_field('DELETE') . '
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure?\')"><i class="fas fa-trash-alt"></i></button>
                    </form>
                ';
            })
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(departements $model): QueryBuilder
    {
        return $model->newQuery()
        ->withCount('iotelegrams')
        ->withCount('outgoings')
        ->withCount('children')
        ->with(['createdBy', 'managerAssistant', 'manager', 'updatedBy']);
        // ->where('parent_id', Auth::user()->department_id);

    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('departments-table')
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
            Column::make('id')->title('ت'),
            Column::make('name')->title('الاسم'),
            Column::make('manger')->title('المدير'),
            Column::make('manger_assistance')->title('مساعد المدير'),
            Column::make('children_count')->title('الاقسام'),
            Column::make('outgoings_count')->title(' الصادر'),
            Column::make('iotelegrams_count')->title('الوارد'),
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->width(60)
                  ->title('الخيارات')
                  ->addClass('text-center'),
            
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Department_' . date('YmdHis');
    }
}
