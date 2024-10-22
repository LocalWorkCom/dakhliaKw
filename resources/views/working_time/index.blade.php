@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>
@section('title')
    فترة العمل
@endsection
@section('content')


    <div class="row ">
        <div class="container welcome col-11">
            <div class="d-flex justify-content-between">
                <p> فترة العمل</p>
                <button class="btn-all px-3" style="color: #0D992C;" data-bs-toggle="modal" data-bs-target="#myModal1">

                    اضافة فترة <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="">
                </button>
            </div>

        </div>
    </div>
    <br>

    <div class="row">
        <div class="container  col-11 mt-3 p-0  pt-5 pb-4">
            <!-- <div class="row d-flex justify-content-between " dir="rtl">
                <div class="form-group mt-4 mx-3  d-flex">
                    <button class="btn-all px-3" style="color: #274373;" data-bs-toggle="modal" data-bs-target="#myModal1">
                        <img src="{{ asset('frontend/images/time.svg') }}" alt="">
                        اضافة فترة
                    </button>
                </div>
            </div> -->

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
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

            <div class="col-lg-12">
                <div class="bg-white ">
                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm  table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>رقم التسلسلي</th>
                                    <th>اسم الفترة</th>
                                    <th>بدايه وقت العمل </th>
                                    <th> نهاية وقت العمل</th>
                                    <th> لون الفترة</th>
                                    <th style="width:150px !important;">العمليات</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm';

            $('#users-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('api/working_time') }}',
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
                        data: 'start_time',
                        name: 'start_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'color',
                        name: 'color',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                if (data === null || data === '') {
                                    return 'لا يوجد لون';
                                } else {
                                    return `<div class="d-flex justify-content-center align-items-center">  <div style="width: 25px; height: 25px; border-radius:50%; background-color: ${data}; border: 1px solid #000;" ></div> </div>`;
                                }
                            }
                            return data;
                        }

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
                        return `
                        <a href="#" class="btn btn-sm " style="background-color: #274373;" onclick="openViewModal('${row.id}', '${row.name}')"> <i class="fa fa-eye"></i>عرض  </a>
                        <a href="#" class="btn btn-sm" style="background-color: #F7AF15;" onclick="openedit('${row.id}', '${row.name}','${row.start_time}','${row.end_time}','${row.color}')"> <i class="fa fa-edit"></i> تعديل </a>
                    `;
                    }
                }],
                oLanguage: {
                    sSearch: "",
                    sSearchPlaceholder: "بحث",
                    sInfo: 'اظهار صفحة _PAGE_ من _PAGES_',
                    sInfoEmpty: 'لا توجد بيانات متاحه',
                    sInfoFiltered: '(تم تصفية من _MAX_ اجمالى البيانات)',
                    sLengthMenu: 'اظهار _MENU_ عنصر لكل صفحة',
                    sZeroRecords: 'نأسف لا توجد نتيجة',
                    oPaginate: {
                        "sFirst": '<i class="fa fa-fast-backward" aria-hidden="true"></i>', // This is the link to the first page
                        "sPrevious": '<i class="fa fa-chevron-left" aria-hidden="true"></i>', // This is the link to the previous page
                        "sNext": '<i class="fa fa-chevron-right" aria-hidden="true"></i>', // This is the link to the next page
                        "sLast": '<i class="fa fa-step-forward" aria-hidden="true"></i>' // This is the link to the last page
                    }
                },

                pagingType: "full_numbers",
                "fnDrawCallback": function(oSettings) {
                    console.log('Page ' + this.api().page.info().pages)
                    var page = this.api().page.info().pages;
                    console.log($('#users-table tr').length);
                    if (page == 1) {
                        //   $('.dataTables_paginate').hide();//css('visiblity','hidden');
                        $('.dataTables_paginate').css('visibility', 'hidden'); // to hide

                    }
                }
            });
        });
    </script>
@endpush
