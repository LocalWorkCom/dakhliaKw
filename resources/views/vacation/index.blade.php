@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title', 'الاجازات')

@section('content')

    <div class="row ">
        <div class="container welcome col-11">
            <p> الاجــــــازات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">
                    @if (Auth::user()->hasPermission('add EmployeeVacation'))
                        <button type="button" class="wide-btn"
                            onclick="window.location.href='{{ route('vacation.add', $id) }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            اضافة جديد
                        </button>
                    @endif

                </div>
            </div>
            @include('inc.flash')

            <div class="col-lg-12">
                <div class="bg-white ">
                </div>


                <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>نوع الاجازة</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th style="width:150px !important;">العمليات</th>
                        </tr>
                    </thead>
                </table>




                <script>
                    $(document).ready(function() {
                        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

                        var id = {{ $id }};
                        $('#users-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: '{{ route('employee.vacations', $id) }}', // Correct URL concatenation
                            columns: [{
                                    data: 'id',
                                    sWidth: '50px',
                                    name: 'id'
                                },
                                {
                                    data: 'vacation_type.name',
                                    name: 'vacation_type.name'
                                },
                                {
                                    data: 'date_from',
                                    name: 'date_from'
                                },
                                {
                                    data: 'date_to',
                                    name: 'date_to'
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
                                    var editVacation = "<?php echo Auth::user()->hasPermission('edit EmployeeVacation'); ?>";
                                    var showVacation = "<?php echo Auth::user()->hasPermission('show EmployeeVacation'); ?>";
                                    var deleteVacation = "<?php echo Auth::user()->hasPermission('delete EmployeeVacation'); ?>";
                                    // Using route generation correctly in JavaScript
                                    var editUrl = '{{ route('vacation.edit', ':id') }}';
                                    var showUrl = '{{ route('vacation.show', ':id') }}';
                                    var deleteUrl = '{{ route('vacation.delete', ':id') }}';

                                    editUrl = editUrl.replace(':id', row.id);
                                    showUrl = showUrl.replace(':id', row.id);
                                    deleteUrl = deleteUrl.replace(':id', row.id);
                                    var deleteButton = '';
                                    var editButton = '';
                                    var showButton = '';
                                    if (row.StartVacation) {
                                        if (deleteVacation) {
                                            
                                            deleteButton =
                                                `<a href="${deleteUrl}" class="delete btn  btn-sm" style="background-color: #c91d1d;"><i class="fa fa-trash"></i> حذف</a>`;
                                        }
                                    }
                                    if (editVacation) {
                                        editButton =
                                            `<a href="${editUrl}" class="edit btn  btn-sm" style="background-color: #259240;"><i class="fa fa-edit"></i> تعديل</a>`;
                                    }
                                    if (showVacation) {
                                        showButton =
                                            `<a href="${showUrl}" class="edit btn  btn-sm" style="background-color: #375a97;"><i class="fa fa-eye"></i> عرض</a>`;
                                    }
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
                                    "sFirst": "<<", // This is the link to the first page
                                    "sPrevious": "<", // This is the link to the previous page
                                    "sNext": ">", // This is the link to the next page
                                    "sLast": " >>" // This is the link to the last page
                                }


                            },
                            layout: {
                                bottomEnd: {
                                    paging: {
                                        firstLast: false
                                    }
                                }
                            },
                            "pagingType": "full_numbers"
                        });
                    });
                </script>


            </div>
        </div>
    </div>
@endsection
