
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
            <div class="form-group moftsh mt-4  mx-4  d-flex">
                <p class="filter "> تصفية حسب:</p>
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
                     <img src="{{ asset('frontend/images/print.svg')}}" alt=""> طباعة
                </button>
        </div>
    </div>
    
        <div class="col-lg-12" >
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
                                <th class="action" style="width:150px !important;">العمليات</th>
                            </tr>
                           
                            
                        </thead>
                    </table>
</div>
<!-- Add Form Modal -->
<div class="modal fade" id="myModal1" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <div class="title d-flex flex-row align-items-center">
                    <h5 class="modal-title"> اضافة مجموعة</h5>
                    <img src="{{ asset('frontend/images/group-add-modal.svg') }}" alt="">
                   
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
            </div>
            <div class="modal-body mt-3 mb-3">
                <div class="container pt-5 pb-2" style="border: 0.2px solid rgb(166, 165, 165);">
                <form id="add-form" action="{{ route('inspectors.addToGroup') }}" method="POST">
                    @csrf
                  
                    <div class="mb-3">
                        <label for="group_id" style="    justify-content: flex-end;">اختر المجموعه </label>
                        <select class="form-control" name="group_id" id="group_id" required>
                            <option selected disabled>اختار من القائمة</option>
                            @foreach (getgroups() as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger span-error" id="group_id-error"></span>
                        <input type="hidden" name="id" id="id" value="">
                    </div>
                    <div class="text-end pt-3">
                        <button type="button" class="btn-all p-2 "
                        style="background-color: transparent; border: 0.5px solid rgb(188, 187, 187); color: rgb(218, 5, 5);"
                        data-bs-dismiss="modal" aria-label="Close" data-bs-dismiss="modal">
                        <img src="{{ asset('frontend/images/red-close.svg') }}" alt="img"> الغاء
                    </button>
                        <button type="submit" class="btn-all mx-2 p-2"
                            style="background-color: #274373; color: #ffffff;">
                            <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img"> اضافة
                        </button>
                       
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: transparent; border: none;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;</button>
                </button>
            </div>
            <div class="modal-body mb-3 mt-3 d-flex justify-content-center">
                <div class="body-img-modal d-block ">
                    <img src="{{ asset('frontend/images/ordered.svg')}}" alt="">
                    <p>تمت الاضافه بنجاح</p>
                </div>
            </div>

        </div>
    </div>
</div>
</div>




<script>

$(document).ready(function() {
    $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class
  
    const table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ url('api/Inspectors') }}',
        columns: [
            { data: 'id', sWidth: '50px', name: 'id' },
            { data: 'position', name: 'position' },
            { data: 'name', name: 'name' },
            { data: 'Id_number', name: 'Id_number' },
            { data: 'group_id', name: 'group_id' },
            { data: 'phone', name: 'phone' },
            { data: 'action', name: 'action', sWidth: '200px', orderable: false, searchable: false }
        ],
        columnDefs: [{
            targets: -1,
            render: function(data, type, row) {
                var departmentEdit = '{{ route('inspectors.edit', ':id') }}'.replace(':id', row.id);
                var departmentShow = '{{ route('inspectors.show', ':id') }}'.replace(':id', row.id);
                var btn_add = '';

                if (row.group_id == null) {
                    var addToGroup = '{{ route('inspectors.addToGroup', ':id') }}'.replace(':id', row.id);
                    btn_add = `
                        <a class="btn btn-sm" id="updateValueButton" style="background-color: green;" 
                           onclick="openAddModal(${row.id},0)" data-bs-toggle="modal" 
                           data-bs-target="#myModal1">
                           <i class="fa fa-plus"></i> أضافه
                        </a>`;
                }else{
                    var addToGroup = '{{ route('inspectors.addToGroup', ':id') }}'.replace(':id', row.id);
                    btn_add = `
                        <a class="btn btn-sm" id="updateValueButton" style="background-color: #7e7d7c;" 
                           onclick="openAddModal(${row.id} , ${row.group_id})" data-bs-toggle="modal" 
                           data-bs-target="#myModal1">
                           <i class="fa fa-edit"></i> تعديل مجموعه
                        </a>`;
                }

                return `
                    <a href="${departmentEdit}" class="btn btn-sm"  style="background-color: #F7AF15;">
                        <i class="fa fa-edit"></i> تعديل 
                    </a>
                    <a href="${departmentShow}" class="btn btn-sm " style="background-color: #274373;">
                       <i class="fa fa-eye"></i>عرض</a>
                    ${btn_add}
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


// Add button click event listeners
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
@push('scripts')

<script>
    $(document).ready(function() {
        $('#add-form').on('submit', function(event) {
            const groupId = $('#group_id').val();
            const errorSpan = $('#group_id-error'); // Get the error span element
            errorSpan.text(''); // Clear any existing error message

            if (!groupId) {
                event.preventDefault(); 
                errorSpan.text('يرجى اختيار مجموعة قبل الإضافة.'); // Set the error message

                return false; 
            }
        });
    });

function openAddModal(id,idGroup) {
    $('#myModal1').modal('show');
    document.getElementById('id').value = id;
    document.getElementById('group_id').value = idGroup;

}

// $(document).ready(function() {
//     // Function to set the value of the select element
//     function setSelectValue(id) {
//         $('#group_id').val(id);
//     }

//     // Example: Set the select element to the group with ID 2
//     setSelectValue(2); // Replace 2 with the desired ID
// });
</script>
</script>
@endpush