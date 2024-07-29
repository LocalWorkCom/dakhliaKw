<?php

namespace App\DataTables;

use App\Models\outgoings;
use App\Models\outgoing_files;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;

class outgoingsDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
           
            ->addColumn('person_to_username', function ($row) {
                return $row->personTo->name ?? 'لايوجد شخص صادر له'; // Assuming 'name' is the column in external_users
            })
            ->addColumn('department_External_name', function ($row) {
                return $row->department_External->name ?? 'لا يوجد قسم خارجى صادر له'; // Assuming 'name' is the column in external_users
            })
            ->addColumn('action', function ($row) {
                // $is_file = outgoing_files::where('outgoing_id', $row->id)->exists();
                $fileCount = outgoing_files::where('outgoing_id', $row->id)->count();
                 $is_file = $fileCount == 0;
                $uploadButton = $is_file 
                    ? '<a href="' . route('Export.edit', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>'
                    : '<a href="' . route('export.archive', $row->id) . '" class="edit btn btn-info btn-sm" ><i class="fa fa-archive"></i></a>';
    
                return '
                    <a href="' . route('Export.show', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                   
                    ' . $uploadButton ;
                
            })
            ->setRowId('id');
    }
    

    /**
     * Get the query source of dataTable.
     */
    public function query(outgoings $model): QueryBuilder
    {
        $status = $this->request()->get('status', 'active'); // Default to 'active' if not provided

        if ($status === 'active') {
            return $model->newQuery()
                ->with(['personTo', 'department_External'])
                ->where('outgoings.active', 0)
                ->select('outgoings.*');
        } else {
            return $model->newQuery()
                ->with(['personTo', 'department_External'])
                ->where('outgoings.active', 1)
                ->select('outgoings.*');
        }
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
                    ->setTableId('outgoings-table')
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
              ->title('الخيارات')
              ->addClass('text-center'),
        Column::make('num')->title('رقم الصادر')->addClass('text-center'),
        Column::make('note')->title('الملاحظات')->addClass('text-center'),
        Column::make('date')->title('تايخ الصادر')->addClass('text-center'),
        Column::make('person_to_username')->title(' العسكرى')->addClass('text-center'),  
        Column::make('department_External_name')->title('الاداره الصادر منها')->addClass('text-center'),  
    ];
}


    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'outgoings_' . date('YmdHis');
    }
}
