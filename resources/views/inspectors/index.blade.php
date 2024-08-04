
@extends('layout.main')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.css" defer>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js" defer></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.js" defer>
</script>

@section('content')
@section('title')
    عرض
@endsection

<div class="row ">
        <div class="container welcome col-11">
<div class="d-flex justify-content-between">
    <p> المفتــــــشون ({{\App\Models\Inspector::count()}})</p>

    <!-- <h2 style="color: #274373;"> مجموعة (أ)</h2> -->
</div>
        </div>
    </div>
    <br>

<div class="row" >
    <div class="container  col-11 mt-3 p-0 ">
        <div class="row d-flex justify-content-between " dir="rtl">
            <div class="form-group mt-4 mx-1  d-flex">
                <button class="btn-all px-3 mx-2" style="color: #274373;">
                    الكل (7)
                </button>
                <button class="btn-all px-3 mx-2" style="color: #274373;">
                    مفتشون تم توزعهم
                </button>
                <button class="btn-all px-3 mx-2" style="color: #274373;">
                    مفتشون لم يتم توزعهم
                </button>
            </div>
            <div class="form-group mt-4 mx-4  d-flex justify-content-end ">
                <button class="btn-all px-3 " style="color: #FFFFFF; background-color: #274373;" onclick="window.print()">
                     <img src="../images/print.svg" alt=""> طباعة
                </button>
        </div>
    </div>
    
        <div class="col-lg-12" dir="rtl">
            <div class="bg-white ">
                <div>
                    <table id="users-table" class="display table table-responsive-sm  table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>رقم التعريف</th>
                                <th>الرتبة</th>
                                <th>الاسم</th>
                                <th>رقم الهوية </th>
                                <th>المجموعة </th>
                                <th>رقم الهاتف </th>
                                <th style="width:150px !important;">العمليات</th>
                            </tr>
                           
                            
                        </thead>
                    </table>
</div>
</div>

<script>

$(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('api/Inspectors') }}',
            columns: [
                { data: 'id',  sWidth: '50px', name: 'id' },
                { data: 'position', name: 'position' },
                { data: 'name', name: 'name' },
                { data: 'Id_number', name: 'Id_number' },  // Ensure 'manager' column exists
                { data: 'group_id', name: 'group_id' },
                { data: 'phone', name: 'phone' },
                
                { data: 'action', name: 'action',  sWidth: '100px', orderable: false, searchable: false }
            ],
            columnDefs: [{
                targets: -1,
                render: function(data, type, row) {
                    var departmentEdit = '{{ route('inspectors.edit', ':id') }}';
                    departmentEdit = departmentEdit.replace(':id', row.id);
                    var departmentShow = '{{ route('inspectors.show', ':id') }}';
                    departmentShow = departmentShow.replace(':id', row.id);
                   

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


    document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.btn-all');
    
    buttons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove 'btn-active' class from all buttons
            buttons.forEach(btn => btn.classList.remove('btn-active'));
            
            // Add 'btn-active' class to the clicked button
            button.classList.add('btn-active');
        });
    });
});

</script>
@endsection
