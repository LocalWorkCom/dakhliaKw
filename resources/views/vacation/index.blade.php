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
                {{-- ( {{ $vacationCount }} ) --}}
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
        <div class="container col-11 mt-3 p-0 pb-4">
            <div class="row d-flex justify-content-between" dir="rtl">
                <div class="form-group moftsh mt-4 mx-4 d-flex">
                    <!-- <p class="filter" style="font-size:35px;">عدد الاجازات :</p> -->
                </div>
            </div>

            <div class="row d-flex justify-content-between " dir="rtl">
                <div class="form-group moftsh mx-4 d-flex flex-wrap">
                    <p class="filter">تصفية حسب:</p>
                    {{-- <select name="vacation-select" id="vacation-select" class="form-group mx-md-2 btn-all "
                        style="text-align: center; color:#ff8f00;height: 40px;font-size: 19px; padding-inline:10px;">
                        <option value="">اختر نوع الاجازة</option>
                        <option value="all">الكل</option>
                        <option value="exceeded">متجاوز</option>
                        <option value="finished">الاجازات المنتهيه </option>
                        <option value="current"> الاجازات الحاليه </option>
                        <option value="not_begin">اجازات لم تبدا </option>
                        <option value="pending">الاجازات المقدمة </option>
                        <option value="rejected"> الاجازات المرفوضة</option>

                    </select> --}}
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

                <!-- Modal for adding representative -->
                <div class="modal fade" id="representative" tabindex="-1" aria-labelledby="representativeLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header d-flex justify-content-center">
                                <div class="title d-flex flex-row align-items-center">
                                    <h5 class="modal-title" id="representativeLabel">تعديل التاريخ</h5>
                                    <!-- <img src="{{ asset('frontend/images/add-mandob.svg') }}" alt=""> -->
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close">&times;</button>
                            </div>
                            <div class="modal-body pt-4 pb-5">
                                <input type="hidden" name="type" id="type">
                                <input type="hidden" name="id" id="id">
                                <div class="form-group">
                                    <label for="end_date">تاريخ النهاية</label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                                    <span class="text-danger span-error" id="end-date-error"></span>
                                </div>
                                <div class="text-end mt-3">
                                    <button type="submit" class="btn-blue" onclick="UpdateDate()">حفظ</button>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close">&times;</button>
                        </div>
                    </div>
                </div>

                <!-- Script for DataTables and modal behavior -->
                <script>
                    function UpdateDate() {
                        var end_date = $('#end_date').val();
                        var type = $('#type').val();
                        var id = $('#id').val();

                        // Correctly replace ':id' in the URL
                        var url = '{{ route('vacation.update', ':id') }}'.replace(':id', id);

                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                'end_date': end_date,
                                'type': type,
                                '_token': '{{ csrf_token() }}'
                            },
                            success: function(data) {
                                console.log('Success:', data);
                                // Optionally, you can close the modal and refresh the DataTable
                                $('#representative').modal('hide');
                                // $('#users-table').DataTable().ajax.reload();
                                window.location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.log('Error:', error);
                                console.log('XHR:', xhr.responseText);
                            }
                        });
                    }

                    function update_type(type, id) {
                        $('#type').val(type);
                        $('#id').val(id);
                    }

                    $(document).ready(function() {
                        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class
                        vacation_id = $('#vacation-select').val();

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
                                // data: function(d) {
                                //     d.vacation = $('#vacation-select').val(); // Add government_id to request
                                // }
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
                                    var showVacation =
                                        "{{ Auth::user()->hasPermission('view EmployeeVacation') }}";
                                    var urls = {
                                        show: '{{ route('vacation.show', ':id') }}',
                                        accept: '{{ route('vacation.accept', ':id') }}',
                                        reject: '{{ route('vacation.reject', ':id') }}',
                                        printReturn: '{{ route('vacation.print_return', ':id') }}',
                                        permit: '{{ route('vacation.permit', ':id') }}',
                                        print: '{{ route('vacation.print', ':id') }}'
                                    };

                                    for (var key in urls) {
                                        urls[key] = urls[key].replace(':id', row.id);
                                    }

                                    var buttons = '';
                                    if (showVacation) {
                                        buttons +=
                                            `<a href="${urls.show}" class="edit btn btn-sm" style="background-color: #375a97;"><i class="fa fa-eye"></i> عرض</a>`;
                                    }

                                    if (row.VacationStatus == 'منتهية') {
                                        buttons +=
                                            `<a href="" class="edit btn btn-sm" style="background-color: #2099c5;"><i class="fa-solid fa-print"></i> طباعة العودة</a>`;
                                        if (!row.end_date) {
                                            buttons +=
                                                `<a data-bs-toggle="modal" data-bs-target="#representative" class="edit btn btn-sm" style="background-color: #c96f3d;" onclick="update_type('direct_work', '${row.id}')"><i class="fa-brands fa-stack-overflow"></i> مباشرة العمل</a>`;
                                        }
                                    } else if (row.VacationStatus == 'مقدمة') {
                                        buttons +=
                                            `<form id="acceptForm" action="${urls.accept}" method="POST" style="display:inline;">@csrf<a href="#" class="edit btn btn-sm" style="background-color: #28a745;" onclick="document.getElementById('acceptForm').submit();"><i class="fa fa-check"></i> موافقة</a></form>`;
                                        buttons +=
                                            `<form id="rejectForm" action="${urls.reject}" method="POST" style="display:inline;">@csrf<a href="#" class="edit btn btn-sm" style="background-color: #dc3545;" onclick="document.getElementById('rejectForm').submit();"><i class="fa fa-times"></i> رفض</a></form>`;
                                        buttons +=
                                            `<form id="permitForm" action="${urls.permit}" method="POST" style="display:inline;">@csrf<a href="#" class="edit btn btn-sm" style="background-color: #dc3545;" onclick="document.getElementById('permitForm').submit();"><i class="fa fa-times"></i> تصريح</a></form>`;
                                        buttons +=
                                            `<form id="printForm" action="${urls.print}" method="POST" style="display:inline;">@csrf<a href="#" class="edit btn btn-sm" style="background-color: #dc3545;" onclick="document.getElementById('printForm').submit();"><i class="fa fa-times"></i> طباعة</a></form>`;
                                    } else if (row.VacationStatus == 'متجاوزة') {
                                        buttons +=
                                            `<a data-bs-toggle="modal" data-bs-target="#representative" class="edit btn btn-sm" style="background-color: #9dad1f;" onclick="update_type('direct_exceed', '${row.id}')"><i class="fa fa-eye"></i> باشر بعد التجاوز</a>`;
                                    } else if (row.VacationStatus == 'حالية') {
                                        if (!row.is_cut) {
                                            buttons +=
                                                `<a data-bs-toggle="modal" data-bs-target="#representative" class="edit btn btn-sm" style="background-color: #c55a49;" onclick="update_type('cut', '${row.id}')"><i class="fa fa-eye"></i> قطع الاجازة</a>`;
                                        }
                                    }

                                    return buttons;
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

                            if (filter === 'all') {
                                // Apply ordering by ID in descending order
                                table.order([0, 'desc']).page.len(10); // Assuming 10 records per page for 'all' filter
                            } else {
                                // Reset any ordering and show all data on a single page for other filters
                                table.order([]).page.len(-1); // Show all records on a single page
                            }

                            table.ajax.reload(); // Reload data with the new filter and ordering
                        });

                        // $('.btn-all').click(function() {
                        //     filter = $(this).data('filter'); // Update the filter based on the clicked button

                        //     // Remove 'btn-active' class from all buttons and add to the clicked one
                        //     $('.btn-all').removeClass('btn-active');
                        //     $(this).addClass('btn-active');

                        //     if (filter === 'all') {
                        //         // Apply ordering by ID in descending order and show 10 records per page
                        //         table.order([0, 'desc']).page.len(10); // Assuming 10 records per page for 'all' filter
                        //     } else {
                        //         // Reset any ordering and show 10 records per page for other filters
                        //         table.order([]).page.len(1); // Show 10 records per page with no specific order
                        //     }
                        //     // table.ajax.reload(); // Reload data with the new filter and ordering
                        //     table.ajax.reload(); // Reload data with the new filter

                        //     // Go to the first page to ensure data is shown
                        //     // table.page('first');
                        // });

                    });


                    $(document).ready(function() {
                        // $('#vacation-select').change(function() {
                        //     table.ajax.reload(); // Reload DataTable data on dropdown change
                        // });
                        // Set minimum date for the end_date input to today's date

                        var id = "{{ $id }}";
                        // Get today's date
                        var today = new Date().toISOString().split('T')[0];
                        $('#end_date').attr('min', today);

                        $('#end_date').attr('value', today);
                    });
                </script>

            </div>
        </div>
    </div>
@endsection
