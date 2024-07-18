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
                return $row->person_to_username ?? '';  // Use null coalescing operator to handle null values
            })
            ->addColumn('created_by_username', function ($row) {
                return $row->created_by_username ?? '';  // Use null coalescing operator to handle null values
            })
            ->addColumn('updated_by_username', function ($row) {
                return $row->updated_by_username ?? '';  // Use null coalescing operator to handle null values
            })
           
            ->addColumn('action', function ($row) {
                // $is_file = outgoing_files::where('outgoing_id', $row->id)->exists();
                $fileCount = outgoing_files::where('outgoing_id', $row->id)->count();
                 $is_file = $fileCount > 0;
                $uploadButton = $is_file 
                    ? '<a href="' . route('Export.upload.files', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-upload"></i>('.$fileCount.')</a>'
                    : '<a href="' . route('Export.view.files', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-file"></i></a>';
    
                return '
                    <a href="' . route('Export.show', $row->id) . '" class="edit btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                    <a href="' . route('Export.edit', $row->id) . '" class="edit btn btn-success btn-sm"><i class="fa fa-edit"></i></a>
                    ' . $uploadButton ;
                
            })
            ->setRowId('id');
    }
    

    /**
     * Get the query source of dataTable.
     */
    public function query(outgoings $model): QueryBuilder
    {
        return $model->newQuery()
        ->leftJoin('users as person_to_user', 'outgoings.person_to', '=', 'person_to_user.id')
        ->leftJoin('users as created_by_user', 'outgoings.created_by', '=', 'created_by_user.id')
        ->leftJoin('users as updated_by_user', 'outgoings.updated_by', '=', 'updated_by_user.id')
        ->where('outgoings.active', 0)
        ->select('outgoings.*', 
                 'person_to_user.username as person_to_username',  
                 'created_by_user.username as created_by_username',  
                 'updated_by_user.username as updated_by_username');
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
            Column::make('id')->title('رقم '),
            Column::make('name')->title('العنوان'),
            Column::make('num')->title('رقم الصادر'),
            Column::make('note')->title(' ملاحظات'),
            Column::make('active')->title('الحاله')->render('function() { return this.active == 1 ? "مفعل" : " مفعل"; }'),
            Column::make('person_to_username')->title(' العسكرى'),  // Display the username for person_to
            Column::make('created_by_username')->title('أنشاء بواسطه'),  // Display the username for created_by
            Column::make('updated_by_username')->title(' تعديل بواسطه'),  // Display the username for updated_by
          
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
