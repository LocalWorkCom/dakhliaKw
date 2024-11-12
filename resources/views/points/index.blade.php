@extends('layout.main')
@push('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
    </script>
@endpush
@section('title')
    النقاط
@endsection
@section('content')
    <section>
        <div class="row">

            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p> نقاط الوزاره </p>
                    <div class="d-flex justify-content-between">
                        {{-- @if (Auth::user()->hasPermission('create Point')) --}}
                        <button type="button" class="btn-all mr-2" onclick="window.location.href='{{ route('points.create') }}'"
                            style="    color: #0D992C;">
                            اضافة نقطة جديدة <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        </button>
                        {{-- @endif --}}
                        {{-- @if (Auth::user()->hasPermission('create Point')) --}}
                        <button type="button" class="btn-all  "
                            onclick="window.location.href='{{ route('grouppoints.create') }}'" style="    color: #0D992C;">
                            اضافة نقاط لمجموعه  <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        </button>
                        {{-- @endif --}}
                    </div>

                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="container  col-11 mt-3 p-0  pt-5 pb-4">
                <div class="col-lg-12">
                    <div class="bg-white ">
                        @if (session()->has('message'))
                            <div class="alert alert-info">
                                {{ session('message') }}
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-info">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('reject'))
                            <div class="alert alert-danger">
                                {{ session('reject') }}
                            </div>
                        @endif
                        
                        <div>
                            <table id="users-table"
                                class="display table table-responsive-sm  table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>اسم النقطة</th>
                                        <th> أسم المجموعه  بالنقطه</th>
                                        <th>القطاع </th>
                                        <th>المحافظه</th>
                                        <th>المنطقه</th>

                                        <th>دوام النقطه</th>
                                        <th style="width:150px;">العمليات</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    {{-- model for delete form --}}
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="deleteModalLabel"> !تنبــــــيه</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <form id="delete-form" action="{{ route('points.delete') }}" method="POST">
                    @csrf
                    <div class="modal-body  d-flex justify-content-center">
                        <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه النقطة ؟</h5>


                        <input type="text" id="id" hidden name="id" class="form-control">
                    </div>
                    <div class="modal-footer mx-2 d-flex justify-content-center">
                        <div class="text-end">
                            <button type="button" class="btn-blue">لا</button>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn-blue" onclick="confirmDelete()">نعم</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div> 

    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm';

            var table = $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('getAllpoints') }}',
                },
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'group_name', name: 'group_name' },
                    { data: 'sector_name', name: 'sector_name' },
                    { data: 'government_name', name: 'government_name' },
                    { data: 'region_name', name: 'region_name' },
                    { data: 'work_type', name: 'work_type' },
                    {
                        data: 'action',
                        name: 'action',
                        sWidth: '100px',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [[1, 'desc']],
                "oLanguage": {
                    "sSearch": "",
                    "sSearchPlaceholder": "بحث",
                    "sInfo": 'اظهار صفحة _PAGE_ من _PAGES_',
                    "sInfoEmpty": 'لا توجد بيانات متاحه',
                    "sInfoFiltered": '(تم تصفية من _MAX_ اجمالى البيانات)',
                    "sLengthMenu": 'اظهار _MENU_ عنصر لكل صفحة',
                    "sZeroRecords": 'نأسف لا توجد نتيجة',
                    "oPaginate": {
                        "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>',
                        "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                        "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
                        "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>'
                    }
                },
                "pagingType": "full_numbers",
                "fnDrawCallback": function(oSettings) {
                    var api = this.api();
                    var pageInfo = api.page.info();

                    // Check if the total number of records is less than or equal to the number of entries per page
                    if (pageInfo.recordsTotal <= 10) { // Adjust this number based on your page length
                        $('.dataTables_paginate').css('visibility', 'hidden'); // Hide pagination
                    } else {
                        $('.dataTables_paginate').css('visibility', 'visible'); // Show pagination
                    }
                }
            });
        });

        $(document).ready(function() {
            function closeModal() {
                $('#delete').modal('hide');
            }

            $('#closeButton').on('click', function() {
                closeModal();
            });
        });

        function opendelete(id) {
            document.getElementById('id').value = id;
            $('#delete').modal('show');
        }

        function confirmDelete() {
            var id = document.getElementById('id').value;
            var form = document.getElementById('delete-form');
            form.submit();
        }

    </script>
@endpush
