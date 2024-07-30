



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
                <p> القطاعات </p>
            </div>
        </div>

        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row" dir="rtl">
                    <div class="form-group mt-4 mx-md-2 col-12 d-flex">
                        <button type="button" class="wide-btn mx-3"
                            onclick="window.location.href='{{ route('departments.create') }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img"> اضافة جديد
                        </button>

                        <button type="button" class="wide-btn mx-md-3"
                            onclick="window.location.href='{{ route('postmans.create') }}'">
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img"> اضافة مندوب
                        </button>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="bg-white ">
                        <div>
                            <table id="users-table" class="display table table-responsive-sm table-bordered table-hover dataTable">
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
    </section>
    <script>
    $(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('api/department') }}',
            columns: [
                { data: 'id',  sWidth: '50px', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'manager_name', name: 'manager_name' },  // Ensure 'manager' column exists
                { data: 'children_count', name: 'children_count' },
                { data: 'iotelegrams_count', name: 'iotelegrams_count' },
                { data: 'outgoings_count', name: 'outgoings_count' },
                { data: 'action', name: 'action',  sWidth: '100px', orderable: false, searchable: false }
            ],
            columnDefs: [{
                targets: -1,
                render: function(data, type, row) {
                    var departmentEdit = '{{ route('departments.edit', ':id') }}';
                    departmentEdit = departmentEdit.replace(':id', row.id);
                    var departmentShow = '{{ route('departments.show', ':id') }}';
                    departmentShow = departmentShow.replace(':id', row.id);
                    var departmentDelete = '{{ route('departments.destroy', ':id') }}';
                    departmentDelete = departmentDelete.replace(':id', row.id);

                    return `
                        <a href="${departmentEdit}" class="btn btn-sm"  style="background-color: #F7AF15;"> <i class="fa fa-edit"></i> </a>
                        <a href="${departmentShow}"  class="btn btn-sm " style="background-color: #274373;"> <i class="fa fa-eye"></i> </a>
                        
                        `;
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
    function deleteDepartment(id) {
        console.log(id);
    if (confirm('هل أنت متأكد من حذف هذا القسم؟')) {
        $.ajax({
            url: '/departments/delete/' + id ,
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
    </script>

@endsection
