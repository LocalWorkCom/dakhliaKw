@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('content')
@section('title')
    عرض
@endsection

<section>
    <div class="row">
        @if (url()->current() == url('/users/0'))
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p>المستخـــــــــــدمين</p>

                    <div class="form-group">
                        @if (Auth::user()->hasPermission('create User'))
                            <button type="button" class="wide-btn"
                                onclick="window.location.href='{{ route('user.create', $id) }}'" style="color: #0D992C;">

                                اضافة جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @elseif (url()->current() == url('/employees/1'))
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p>المـــــــــــوظفين</p>
                    <div class="form-group">
                        @if (Auth::user()->hasPermission('add_employee User'))
                            <button type="button" class="wide-btn"
                                onclick="window.location.href='{{ route('user.create', $id) }}'"
                                style="color: #0D992C;">

                                اضافة جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @elseif ($id == 0)
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p>المستخـــــــــــدمين</p>

                    <div class="form-group">
                        @if (Auth::user()->hasPermission('create User'))
                            <button type="button" class="wide-btn"
                                onclick="window.location.href='{{ route('user.create', $id) }}'">

                                اضافة جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @elseif($id == 1)
            <div class="container welcome col-11">
                <div class="d-flex justify-content-between">
                    <p>المـــــــــــوظفين</p>
                    <div class="form-group">
                        @if (Auth::user()->hasPermission('add_employee User'))
                            <button type="button" class="wide-btn"
                                onclick="window.location.href='{{ route('user.create', $id) }}'">

                                اضافة جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif


    </div>


    <br>


    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            @include('inc.flash')
            <div class="col-lg-12 pt-5 pb-5">
                <div class="bg-white ">

                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm  table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>رقم التعريف</th>
                                    <th>الاسم</th>
                                    <th>القسم</th>
                                    <th>الهاتف</th>
                                    <th>الرقم العسكري</th>
                                    <th style="width:150px !important;">العمليات</th>
                                </tr>
                            </thead>
                            <!--  <tfoot>
                                    <tr>
                                        <th>رقم التعريف</th>
                                        <th>الاسم</th>
                                        <th>القسم</th>
                                        <th>الهاتف</th>
                                        <th>الرقم العسكري</th>
                                        <th style="width:150px !important;"></th>
                                    </tr>
                         </tfoot> -->
                        </table>




                        <script>
                            $(document).ready(function() {
                                $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

                                var id = {{ $id }};

                                @php
                                    $Dataurl = url('api/users');
                                    if (isset($mode)) {
                                        if ($mode == 'search') {
                                            $Dataurl = url('searchUsers/users');
                                        }
                                    }
                                    // dd($Dataurl);
                                @endphp
                                /*
                                  $('#users-table tfoot th').each(function (i) {
                                      var title = $('#users-table thead th')
                                          .eq($(this).index())
                                          .text();
                                      $(this).html(
                                          '<input type="text" placeholder="' + title + '" data-index="' + i + '" />'
                                      );
                                  }); */
                                var table = $('#users-table').DataTable({
                                    processing: true,
                                    serverSide: true,
                                    ajax: '{{ $Dataurl }}/' + id + "/" +
                                        '{{ isset($q) ? $q : '' }}', // Correct URL concatenation
                                    bAutoWidth: false,
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
                                            data: 'department',
                                            name: 'department'
                                        },
                                        {
                                            data: 'phone',
                                            name: 'phone'
                                        },
                                        {
                                            data: 'military_number',
                                            name: 'military_number'
                                        },
                                        {
                                            data: 'action',
                                            name: 'action',

                                            sWidth: '200px',
                                            orderable: false,
                                            searchable: false
                                        }
                                    ],
                                    columnDefs: [{
                                        targets: -1,
                                        render: function(data, type, row) {


                                            //  console.log("dalia", data);
                                            // Using route generation correctly in JavaScript
                                            var useredit = '{{ route('user.edit', ':id') }}';
                                            useredit = useredit.replace(':id', row.id);
                                            var usershow = '{{ route('user.show', ':id') }}';
                                            usershow = usershow.replace(':id', row.id);
                                            var vacation = '{{ route('vacations.list', ':id') }}';
                                            vacation = vacation.replace(':id', row.id);

                                            var unsigned = '{{ route('user.unsigned', ':id') }}';
                                            unsigned = unsigned.replace(':id', row.id);
                                            var visibility = row.department_id != null ? 'd-block-inline' :
                                                'd-none';
                                            var showUnassignedButton = row.id !==
                                                {{ auth()->user()->id }}; // Replace with your specific condition

                                            // Conditionally include the "Unassigned" button based on `showUnassignedButton`
                                            var unassignedButton = showUnassignedButton ? `
                                                <a href="${unsigned}" class="btn btn-sm ${visibility}" style="background-color: #28a39c;">
                                                    <i class="fa-solid fa-user-minus"></i> الغاء التعيين
                                                </a>` : '';

                                            return `

                                                <a href="` + usershow + `"  class="btn btn-sm " style="background-color: #274373;"> <i class="fa fa-eye"></i>عرض  </a>
                                                <a href="` + useredit + `" class="btn btn-sm"  style="background-color: #F7AF15;"> <i class="fa fa-edit"></i> تعديل </a>
                                                {{-- <a href="${vacation}"  "   class="btn btn-sm" style=" background-color:#864824; "> <i class="fa-solid fa-mug-hot" ></i> </a> --}}
                                                        ${unassignedButton}




                                        `;
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

                                $(table.table().container()).on('keyup', 'tfoot input', function() {
                                    table
                                        .column($(this).data('index'))
                                        .search(this.value)
                                        .draw();
                                });
                            });
                        </script>


                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
