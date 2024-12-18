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
                <p> الادارات </p>
                <div class="form-group">
                    <button type="button" class="wide-btn "
                        onclick="window.location.href='{{ route('departments.create') }}'" style="    color: #0D992C;">
                        اضافة جديد
                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                    </button>
                    @if (Auth::user()->hasPermission('create Postman'))
                        <!--   <button type="button" class="wide-btn mx-md-3 mx-1"
                        onclick="window.location.href='{{ route('postmans.create') }}'">
                        اضافة مندوب
                       <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                    </button> -->
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="container  col-11 mt-3 p-0  pt-5 pb-4">
            <div class="row" dir="rtl">

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('reject'))
                    <div class="alert alert-danger">
                        {{ session('reject') }}
                    </div>
                @endif

            </div>

            <div class="col-lg-12">
                <div class="bg-white ">
                    <div>
                        <table id="users-table"
                            class="display table table-responsive-sm table-bordered table-hover dataTable">
                            <thead>
                                <tr>
                                    <th>رقم التعريف</th>
                                    <th>الاسم</th>
                                    <th>المدير</th>
                                    <th>الاقسام</th>
                                    <th>الوارد</th>
                                    <th>الصادر</th>
                                    <th>إجراء</th>
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
                <form id="delete-form" action="{{ route('departments.destroy') }}" method="POST">
                    @csrf
                    <div class="modal-body  d-flex justify-content-center">
                        <h5 class="modal-title " id="deleteModalLabel"> هل تريد حذف هذه الادارة ؟</h5>


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
<script>
    $(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class
        @php
            $Dataurl = url('api/department');
            if (isset($mode)) {
                if ($mode == 'search') {
                    $Dataurl = url('searchDept/departments') . '/' . $q;
                }
            }
            // dd($Dataurl);
        @endphp
        console.log('Rasha', "{{ $Dataurl }}")
        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ $Dataurl }}/',

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
                    data: 'manager_name',
                    name: 'manager_name'
                }, // Ensure 'manager' column exists
                {
                    data: 'children_count',
                    name: 'children_count'
                },
                {
                    data: 'iotelegrams_count',
                    name: 'iotelegrams_count'
                },
                {
                    data: 'outgoings_count',
                    name: 'outgoings_count'
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
                    //var departmentEdit = '{{ route('departments.edit', ':id') }}';
                    var departmentEdit = '<a href="{{ route('departments.edit', ':id') }}"  class="btn btn-sm " style="background-color: #274373;"> <i class="fa fa-edit"></i> تعديل</a>';
                    departmentEdit = departmentEdit.replace(':id', row.id);
                    //var departmentShow = '{{ route('departments.show', ':id') }}';
                    var departmentShow = '<a href="{{ route('departments.show', ':id') }}" class="btn btn-sm"  style="background-color: #F7AF15;"> <i class="fa fa-eye"></i>  عرض</a>';
                    departmentShow = departmentShow.replace(':id', row.id);
                    var departmentDelete = '';
                    @if(auth()->user()->hasPermission('delete departements'))
                        var departmentDelete = '<a class="btn btn-sm" style="background-color: #C91D1D;"  onclick="opendelete(id)">  <i class="fa fa-trash"></i> حذف </a>';
                        departmentDelete = departmentDelete.replace('id', row.id);
                    @endif
                    return departmentEdit+' '+departmentShow+' '+departmentDelete;
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

    function deleteDepartment(id) {
        console.log(id);
        if (confirm('هل أنت متأكد من حذف هذا القسم؟')) {
            $.ajax({
                url: '/departments/delete/' + id,
                type: 'get',

                success: function(response) {
                    // Handle success, e.g., refresh DataTable, show success message
                    $('#users-table').DataTable().ajax.reload();
                    alert('تم حذف القسم بنجاح');
                },
                error: function(xhr) {
                    console.log(xhr);
                    // Handle error, e.g., show error message
                    // alert('حدث خطأ أثناء حذف القسم');
                }
            });
        }
    }

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

@endsection
