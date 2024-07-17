<?php

return [
    /*
     * DataTables search options.
     */
<<<<<<< HEAD
    'search'         => [
=======
    'search' => [
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5
        /*
         * Smart search will enclose search keyword with wildcard string "%keyword%".
         * SQL: column LIKE "%keyword%"
         */
<<<<<<< HEAD
        'smart'            => true,
=======
        'smart' => true,
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

        /*
         * Multi-term search will explode search keyword using spaces resulting into multiple term search.
         */
<<<<<<< HEAD
        'multi_term'       => true,
=======
        'multi_term' => true,
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

        /*
         * Case insensitive will search the keyword in lower case format.
         * SQL: LOWER(column) LIKE LOWER(keyword)
         */
        'case_insensitive' => true,

        /*
         * Wild card will add "%" in between every characters of the keyword.
         * SQL: column LIKE "%k%e%y%w%o%r%d%"
         */
<<<<<<< HEAD
        'use_wildcards'    => false,
=======
        'use_wildcards' => false,
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

        /*
         * Perform a search which starts with the given keyword.
         * SQL: column LIKE "keyword%"
         */
<<<<<<< HEAD
        'starts_with'      => false,
=======
        'starts_with' => false,
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5
    ],

    /*
     * DataTables internal index id response column name.
     */
<<<<<<< HEAD
    'index_column'   => 'DT_RowIndex',
=======
    'index_column' => 'DT_RowIndex',
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

    /*
     * List of available builders for DataTables.
     * This is where you can register your custom dataTables builder.
     */
<<<<<<< HEAD
    'engines'        => [
        'eloquent'   => Yajra\DataTables\EloquentDataTable::class,
        'query'      => Yajra\DataTables\QueryDataTable::class,
=======
    'engines' => [
        'eloquent' => Yajra\DataTables\EloquentDataTable::class,
        'query' => Yajra\DataTables\QueryDataTable::class,
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5
        'collection' => Yajra\DataTables\CollectionDataTable::class,
        'resource' => Yajra\DataTables\ApiResourceDataTable::class,
    ],

    /*
     * DataTables accepted builder to engine mapping.
     * This is where you can override which engine a builder should use
     * Note, only change this if you know what you are doing!
     */
<<<<<<< HEAD
    'builders'       => [
=======
    'builders' => [
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5
        //Illuminate\Database\Eloquent\Relations\Relation::class => 'eloquent',
        //Illuminate\Database\Eloquent\Builder::class            => 'eloquent',
        //Illuminate\Database\Query\Builder::class               => 'query',
        //Illuminate\Support\Collection::class                   => 'collection',
    ],

    /*
     * Nulls last sql pattern for PostgreSQL & Oracle.
     * For MySQL, use 'CASE WHEN :column IS NULL THEN 1 ELSE 0 END, :column :direction'
     */
    'nulls_last_sql' => ':column :direction NULLS LAST',

    /*
     * User friendly message to be displayed on user if error occurs.
     * Possible values:
     * null             - The exception message will be used on error response.
     * 'throw'          - Throws a \Yajra\DataTables\Exceptions\Exception. Use your custom error handler if needed.
     * 'custom message' - Any friendly message to be displayed to the user. You can also use translation key.
     */
<<<<<<< HEAD
    'error'          => env('DATATABLES_ERROR', null),
=======
    'error' => env('DATATABLES_ERROR', null),
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

    /*
     * Default columns definition of dataTable utility functions.
     */
<<<<<<< HEAD
    'columns'        => [
        /*
         * List of columns hidden/removed on json response.
         */
        'excess'    => ['rn', 'row_num'],
=======
    'columns' => [
        /*
         * List of columns hidden/removed on json response.
         */
        'excess' => ['rn', 'row_num'],
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

        /*
         * List of columns to be escaped. If set to *, all columns are escape.
         * Note: You can set the value to empty array to disable XSS protection.
         */
<<<<<<< HEAD
        'escape'    => '*',
=======
        'escape' => '*',
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

        /*
         * List of columns that are allowed to display html content.
         * Note: Adding columns to list will make us available to XSS attacks.
         */
<<<<<<< HEAD
        'raw'       => ['action'],
=======
        'raw' => ['action'],
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5

        /*
         * List of columns are forbidden from being searched/sorted.
         */
        'blacklist' => ['password', 'remember_token'],

        /*
         * List of columns that are only allowed fo search/sort.
         * If set to *, all columns are allowed.
         */
        'whitelist' => '*',
    ],

    /*
     * JsonResponse header and options config.
     */
<<<<<<< HEAD
    'json'           => [
        'header'  => [],
=======
    'json' => [
        'header' => [],
>>>>>>> 6a55ba30dee83febf798d4c63275552142a395e5
        'options' => 0,
    ],

    /*
     * Default condition to determine if a parameter is a callback or not.
     * Callbacks needs to start by those terms, or they will be cast to string.
     */
    'callback' => ['$', '$.', 'function'],
];
