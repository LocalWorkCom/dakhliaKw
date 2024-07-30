@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
@section('title')
    الصلاحيات
@endsection
    <section>
        <div class="row">
            <div class="container welcome col-11">
                <p>الصـــلاحيات</p>
            </div>
        </div>

        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row" dir="rtl">
                    <div class="form-group mt-4 mx-md-2 col-12 d-flex">
                        <button type="button" class="wide-btn"
                            onclick="window.location.href='{{ route('permission.create') }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img"> اضافة جديد
                        </button>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="bg-white ">
                        <div>
                            <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                                <thead>
                                    <tr>
                                        <th>رقم التعريف</th>
                                        <th>الصلاحية</th>
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
                ajax: '{{ url('api/permission') }}',
                columns: [
                    { data: 'id', sWidth: '50px', name: 'id' },

                    { data: 'name', name: 'name' },

                    // {
                    //     data: 'guard_name',
                    //     name: 'guard_name',
                    //     render: function(data, type, row) {
                    //         return models[data] || data; // Use translation or fallback to original
                    //     }
                    // }

                    { data: 'guard_name',sWidth: '60px', name: 'guard_name' },

                    { data: 'action', name: 'action',  sWidth: '100px', orderable: false, searchable: false }
                ],
                columnDefs: [{
                    targets: -1,
                    render: function(data, type, row) {

                        // Using route generation correctly in JavaScript
                        // var permissionedit = '{{ route('permissions_edit', ':id') }}';
                        // permissionedit = permissionedit.replace(':id', row.id);
                        var permissionshow = '{{ route('permissions_show', ':id') }}';
                        permissionshow = permissionshow.replace(':id', row.id);
                        var permissiondelete = '{{ route('permissions_destroy', ':id') }}';
                        permissiondelete = permissiondelete.replace(':id', row.id);
                        return `
                       <a href="` + permissionshow + `" class="btn  btn-sm " style="background-color: #375A97;"> <i class="fa fa-eye"></i> </a>
                       <a href="` + permissiondelete + `" class="btn  btn-sm " style="background-color: #C91D1D;"> <i class="fa-solid fa-trash"></i> </a>`;
                    }

                }],
                "oLanguage": {
                    "sSearch": "",
                    "sSearchPlaceholder":"بحث",
                                                                "sInfo": 'اظهار صفحة _PAGE_ من _PAGES_',
                                            "sInfoEmpty": 'لا توجد بيانات متاحه',
                                            "sInfoFiltered": '(تم تصفية  من _MAX_ اجمالى البيانات)',
                                            "sLengthMenu": 'اظهار _MENU_ عنصر لكل صفحة',
                                            "sZeroRecords": 'نأسف لا توجد نتيجة',
                                            "oPaginate": {
                                                    "sFirst": "<< &nbsp;", // This is the link to the first page
                                                    "sPrevious": "<&nbsp;", // This is the link to the previous page
                                                    "sNext": ">&nbsp;", // This is the link to the next page
                                                    "sLast": "&nbsp; >>" // This is the link to the last page
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
@endsection
{{-- <a href="` + permissionedit + `" class="btn btn-primary btn-sm">تعديل</a> --}}
