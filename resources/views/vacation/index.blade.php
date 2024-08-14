@extends('layout.main')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title', 'الاجازات')

@section('content')

    <div class="row ">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p> الاجــــــازات </p>

                @if (Auth::user()->hasPermission('create EmployeeVacation'))
                    <button type="button" class="btn-all-2 mt-1 px-3 mx-3" style="color: #274373;" onclick="window.location.href='{{ route('vacation.add', $id) }}'">

                        اضافة جديد <img src="{{ asset('frontend/images/time.svg') }}" alt="img">
                    </button>
                @endif
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">


                </div>
            </div>
            @include('inc.flash')

            <div class="col-lg-12">
                <div class="bg-white ">
                </div>


                <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>حالة الاجازة</th>
                            <th>اسم الموظف</th>
                            <th>نوع الاجازة</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th>تاريخ المباشرة</th>
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
                                    data: 'VacationStatus',
                                    sWidth: '50px',
                                    name: 'VacationStatus'
                                },
                                {
                                    data: 'employee.name',
                                    name: 'employee.name'
                                },
                                {
                                    data: 'vacation_type.name',
                                    name: 'vacation_type.name'
                                },
                                {
                                    data: 'start_date',
                                    name: 'start_date'
                                },
                                {
                                    data: 'EndDate',
                                    name: 'EndDate'
                                },
                                {
                                    data: 'StartWorkDate',
                                    name: 'StartWorkDate'
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
                                    var showVacation = "<?php echo Auth::user()->hasPermission('view EmployeeVacation'); ?>";
                                    var showUrl = '{{ route('vacation.show', ':id') }}';
                                    var acceptUrl = '{{ route('vacation.accept', ':id') }}';
                                    var rejectUrl = '{{ route('vacation.reject', ':id') }}';

                                    showUrl = showUrl.replace(':id', row.id);
                                    acceptUrl = acceptUrl.replace(':id', row.id);
                                    rejectUrl = rejectUrl.replace(':id', row.id);

                                    var showButton = '';
                                    var acceptButton = '';
                                    var rejectButton = '';

                                    if (showVacation) {
                                        showButton =
                                            `<a href="${showUrl}" class="edit btn btn-sm" style="background-color: #375a97;"><i class="fa fa-eye"></i> عرض</a>`;
                                    }

                                    if (row.VacationStatus == 'مقدمة') {
                                        acceptButton = `
                                            <form id="acceptForm" action="${acceptUrl}" method="POST" style="display:inline;">
                                                @csrf
                                                <a href="#" class="edit btn btn-sm" style="background-color: #28a745;" onclick="document.getElementById('acceptForm').submit();">
                                                    <i class="fa fa-check"></i> موافقة
                                                </a>
                                            </form>`;

                                        rejectButton = `
                                            <form id="rejectForm" action="${rejectUrl}" method="POST" style="display:inline;">
                                                @csrf
                                                <a href="#" class="edit btn btn-sm" style="background-color: #dc3545;" onclick="document.getElementById('rejectForm').submit();">
                                                    <i class="fa fa-times"></i> رفض
                                                </a>
                                            </form>`;
                                    }

                                    return `${showButton}${acceptButton}${rejectButton}`;
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
                            "pagingType": "full_numbers"
                        });
                    });
                </script>


            </div>
        </div>
    </div>
@endsection
