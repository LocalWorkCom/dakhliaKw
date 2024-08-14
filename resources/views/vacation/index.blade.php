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
                    <button type="button" class="btn-all-2 mt-1 px-3 mx-3" style="color: #274373;"
                        onclick="window.location.href='{{ route('vacation.add', $id) }}'">
                        اضافة جديد <img src="{{ asset('frontend/images/time.svg') }}" alt="img">
                    </button>
                @endif
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container col-11 mt-3 p-0">
            <div class="row d-flex justify-content-between" dir="rtl">
                <div class="form-group moftsh mt-4 mx-4 d-flex">
                    <p class="filter" style="font-size:35px;">عدد الاجازات : {{ $vacationCount }}</p>
                </div>
            </div>

            <div class="row d-flex justify-content-between" dir="rtl">
                <div class="form-group moftsh mx-4 d-flex">
                    <p class="filter">تصفية حسب:</p>
                    <button class="btn-all px-3 mx-3" data-filter="all" style="color: #274373;">
                        الكل ({{ \App\Models\EmployeeVacation::count() }})
                    </button>
                    <button class="btn-all px-3 mx-3" data-filter="exceeded" style="color: #274373;">
                        متجاوز ({{ $data_filter['exceeded'] }})
                    </button>
                    <button class="btn-all px-3 mx-3" data-filter="finished" style="color: #274373;">
                        الاجازات المنتهيه ({{ $data_filter['finished'] }})
                    </button>
                    <button class="btn-all px-3 mx-3" data-filter="current" style="color:#274373;">
                        الاجازات الحاليه ({{ $data_filter['current'] }})
                    </button>
                    <button class="btn-all px-3 mx-3" data-filter="not_begin" style="color: #274373;">
                        اجازات لم تبدا ({{ $data_filter['not_begin'] }})
                    </button>
                    <button class="btn-all px-3 mx-3" data-filter="pending" style="color:#274373;">
                        الاجازات المقدمة ({{ $data_filter['pending'] }})
                    </button>
                    <button class="btn-all px-3 mx-3" data-filter="rejected" style="color: #274373;">
                        الاجازات المرفوضة({{ $data_filter['rejected'] }})
                    </button>
                 
                </div>
            </div>

            @include('inc.flash')

            <div class="col-lg-12">
                <table id="users-table" class="display table table-responsive-sm table-bordered table-hover dataTable">
                    <thead>
                        <tr>
                            <th>رقم تسلسلي</th>
                            <th>حالة الاجازة</th>
                            <th>الاسم</th>
                            <th>نوع الاجازة</th>
                            <th>تاريخ البداية</th>
                            <th>عدد الايام</th>
                            <th>تاريخ النهاية</th>
                            <th>الايام المتبقية</th>
                            <th>تاريخ المباشرة</th>
                            <th style="width:150px !important;">العمليات</th>
                        </tr>
                    </thead>
                </table>
                <script>
                    $(document).ready(function() {
                        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

                        var id = {{ $id }};
                        var filter = 'all'; // Default filter

                        const table = $('#users-table').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: '{{ route('employee.vacations', $id) }}',
                                dataSrc: function(json) {
                                    // Filter data based on the selected filter
                                    if (filter === 'exceeded') {
                                        return json.data.filter(item => item.VacationStatus === 'متجاوزة');
                                    } else if (filter === 'finished') {
                                        return json.data.filter(item => item.VacationStatus === 'منتهية');
                                    } else if (filter === 'current') {
                                        return json.data.filter(item => item.VacationStatus === 'حالية');
                                    } else if (filter === 'not_begin') {
                                        return json.data.filter(item => item.VacationStatus === 'لم تبدأ بعد');
                                    } else if (filter === 'pending') {
                                        return json.data.filter(item => item.VacationStatus === 'مقدمة');
                            
                                    } else if (filter === 'rejected') {
                                        return json.data.filter(item => item.VacationStatus === 'مرفوضة');
                                    }
                                    return json.data; // 'all' or default case
                                }
                            },
                            columns: [{
                                    data: 'id',
                                    name: 'id'
                                },
                                {
                                    data: 'VacationStatus',
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
                                    data: 'days_number',
                                    name: 'days_number'
                                },
                                {
                                    data: 'EndDate',
                                    name: 'EndDate'
                                },
                                {
                                    data: 'DaysLeft',
                                    name: 'DaysLeft'
                                },
                                {
                                    data: 'StartWorkDate',
                                    name: 'StartWorkDate'
                                },
                                {
                                    data: 'action',
                                    name: 'action',
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

                                    if (row.VacationStatus === 'مقدمة') {
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

                                    return `${showButton} ${acceptButton} ${rejectButton}`;
                                }
                            }],
                            oLanguage: {
                                sSearch: "",
                                sSearchPlaceholder: "بحث",
                                sInfo: 'اظهار صفحة _PAGE_ من _PAGES_',
                                sInfoEmpty: 'لا توجد بيانات متاحه',
                                sInfoFiltered: '(تم تصفية  من _MAX_ اجمالى البيانات)',
                                sLengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                                sZeroRecords: 'نأسف لا توجد نتيجة',
                                oPaginate: {
                                    sFirst: '<i class="fa fa-fast-backward" aria-hidden="true"></i>',
                                    sPrevious: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                                    sNext: '<i class="fa fa-chevron-right" aria-hidden="true"></i>',
                                    sLast: '<i class="fa fa-step-forward" aria-hidden="true"></i>'
                                }
                            },
                            layout: {
                                bottomEnd: {
                                    paging: {
                                        firstLast: false
                                    }
                                }
                            },
                            pagingType: "full_numbers"
                        });

                        // Update filter based on button click
                        $('.btn-all').click(function() {
                            filter = $(this).data('filter'); // Update the filter based on the clicked button

                            // Remove 'btn-active' class from all buttons and add to the clicked one
                            $('.btn-all').removeClass('btn-active');
                            $(this).addClass('btn-active');

                            table.ajax.reload(); // Reload data with the new filter
                        });
                    });
                </script>

            </div>
        </div>
    </div>
@endsection
