@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title', 'نظام العمل')

@section('content')

    <div class="row ">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p> نظام العمــــــــــل </p>
                <button type="button" class="wide-btn" style="color: #0D992C;"
                    onclick="window.location.href='{{ route('working_tree.add') }}'">
                    اضافة نظام عمل جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                </button>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0  pt-5 pb-4">
            <!-- <div class="row " dir="rtl">
                    <div class="form-group mt-4  mx-2 col-12 d-flex ">

                    </div>
                </div> -->



            @if (session('reject'))
                <div class="alert alert-danger">
                    {{ session('reject') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('inc.flash')

            <div class="col-lg-12">
                <div class="bg-white ">
                </div>


                <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>عدد ايام العمل</th>
                            <th>عدد ايام الاجازات</th>
                            <th style="width:150px !important;">العمليات</th>
                        </tr>
                    </thead>
                </table>




                {{-- model for delete form --}}
    <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="deleteModalLabel"> !تنبــــــيه</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body  mt-3 mb-5">
                    <div class="container pt-5 pb-3" style="border: 0.2px solid rgb(166, 165, 165);">
                        <form id="delete-form" action="{{ route('working_tree.delete') }}" method="POST">
                            @csrf
                            <div class="form-group d-flex justify-content-center ">
                                <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف نظام العمل هذا ؟</h5>


                                <input type="text" id="id" value="" hidden name="id"
                                    class="form-control">
                            </div>

                            <!-- Save button -->
                            <div class="text-end">
                                <div class="modal-footer mx-2 d-flex justify-content-center">
                                    <div class="text-end">
                                        <button type="button" class="btn-blue" id="closeButton">لا</button>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn-blue" onclick="confirmDelete()">نعم</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

                <script>
                    $(document).ready(function() {
                        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

                        $('#users-table').DataTable({
                            processing: true,
                            serverSide: true,
                            dom: 'lfrti',
                            ajax: '{{ route('working_trees') }}', // Correct URL concatenation
                            columns: [{
                                    data: 'id',
                                    sWidth: '50px',
                                    name: 'id'
                                },
                                {
                                    data: 'name',
                                    name: 'name'
                                },
                                {
                                    data: 'working_days_num',
                                    name: 'working_days_num'
                                },
                                {
                                    data: 'holiday_days_num',
                                    name: 'holiday_days_num'
                                },
                                {
                                    data: 'action',
                                    name: 'action',
                                    sWidth: '100px',
                                    orderable: false,
                                    searchable: false
                                }
                            ],
                            order: [
                                [1, 'desc']
                            ],
                            columnDefs: [{
                                targets: -1,
                                render: function(data, type, row) {

                                    // Using route generation correctly in JavaScript
                                    var editUrl = '{{ route('working_tree.edit', ':id') }}';
                                    var showUrl = '{{ route('working_tree.show', ':id') }}';
                                    var deleteUrl = '{{ route('working_tree.delete', ':id') }}';

                                    editUrl = editUrl.replace(':id', row.id);
                                    showUrl = showUrl.replace(':id', row.id);
                                    deleteUrl = deleteUrl.replace(':id', row.id);
                                    var editButton = '';
                                    var showButton = '';
                                    var deleteButton = '';

                                    editButton =
                                        `<a href="${editUrl}" class="edit btn  btn-sm" style="background-color: #259240;"><i class="fa fa-edit"></i> تعديل</a>`;

                                    showButton =
                                        `<a href="${showUrl}" class="edit btn  btn-sm" style="background-color: #375a97;"><i class="fa fa-eye"></i> عرض</a>`;

                                    deleteButton =
                                        `<a onclick="opendelete('${row.id}')" class="edit btn  btn-sm" style="background-color: #C91D1D;"><i class="fa fa-eye"></i> حذف</a>`;
                                    // Checking if the vacation start date condition is met

                                    return `${editButton}${showButton}${deleteButton}`;

                                }

                            }],
                            "oLanguage": {
                                "sSearch": "",
                                "sSearchPlaceholder": "بحث",
                                "sInfo": 'اظهار صفحة _PAGE_ من _PAGES_',
                                "sInfoEmpty": 'لا توجد بيانات متاحه',
                                "sInfoFiltered": '(تم تصفية  من _MAX_ اجمالى البيانات)',
                                "sLengthMenu": 'اظهار _MENU_ عنصر لكل صفحة',
                                "sZeroRecords": 'نأسف لا توجد نتيجة',
                                "oPaginate": {
                                    "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>', // This is the link to the first page
                                    "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>', // This is the link to the previous page
                                    "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>', // This is the link to the next page
                                    "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>' // This is the link to the last page
                                }


                            },
                            layout: {
                                bottomEnd: {
                                    paging: {
                                        firstLast: false
                                    }
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
                </script>

<script>
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
            console.log(id);
            var form = document.getElementById('delete-form');

            form.submit();

        }
</script>


            </div>
        </div>
    </div>
@endsection
