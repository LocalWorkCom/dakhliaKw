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
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p>المـــــــهام</p>
                <button type="button" class="wide-btn" onclick="window.location.href='{{ route('rule.create') }}'"
                    style="color: #0D992C;">
                    اضافة جديد <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                </button>
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
            <div class="col-lg-12">
                <div class="bg-white">
                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm  table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>رقم التعريف</th>
                                    <th>الاسم</th>
                                    <th>الصلاحيات</th>
                                    <th>القسم</th>
                                    <th style="width:150px;">العمليات</th>
                                </tr>
                            </thead>
                        </table>




                    </div>
                </div>
            </div>

        </div>

    </div>
</section>
<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('api/rule') }}',
            columns: [{
                    data: 'id',
                    sWidth: '50px',
                    name: 'id'
                },
                {
                    data: 'name',
                    sWidth: '60px',
                    name: 'name'
                },
                {
                    data: 'permissions',
                    name: 'permissions'
                },
                {
                    data: 'department',
                    sWidth: '60px',
                    name: 'department'
                },
                {
                    data: 'action',
                    name: 'action',
                    sWidth: '100px',
                    orderable: false,
                    searchable: false
                }
            ],
            columnDefs: [{
                targets: -1,
                render: function(data, type, row) {

                    // Using route generation correctly in JavaScript
                    var ruleedit = '{{ route('rule_edit', ':id') }}';
                    ruleedit = ruleedit.replace(':id', row.id);
                    var ruleshow = '{{ route('rule_show', ':id') }}';
                    ruleshow = ruleshow.replace(':id', row.id);
                    return `
                            <a href="` + ruleshow + `" class="btn  btn-sm" style="background-color: #375A97;"> <i class="fa fa-eye"></i> عرض</a>
                            <a href="` + ruleedit +
                    `" class="btn  btn-sm" style="background-color: #F7AF15;"> <i class="fa fa-edit"></i> تعديل </a>`;
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
@endsection
