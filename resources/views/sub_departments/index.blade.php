

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
                <p>الادارات</p>
            </div>
        </div>

        <div class="row">
            <div class="container col-11 mt-3 p-0">
                <div class="row" dir="rtl">
                    <div class="form-group mt-4 mx-2 col-12 d-flex">
                        <button type="button" class="wide-btn" data-bs-toggle="modal"
                            data-bs-target="#Department" data-dismiss="modal" id="representative-dev"
                            >
                            <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img"> اضافة ادارة
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
                                        <th>الاسم</th>
                                        <th>الاقسام</th>
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

    <!-- Modal ADD-->
    <div class="modal fade" id="Department" tabindex="-1" aria-labelledby="extern-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة ادارة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="Department" action="{{ route('sub_departments.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return submit()">
                        @csrf
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <span class="text-danger span-error" id="name-error"></span>

                        </div>
                       
                        <div class="form-group">
                            <label for="parent_idd">الادارة</label>
                            <select name="parent_id" id="parent_idd" class="form-control" required>
                                <option value="" {{ is_null($parentDepartment) ? 'selected' : '' }} >اختار الادارة</option>
                                @foreach ($subdepartments as $department)
                                    <option value="{{ $department->id }}">
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger span-error" id="parent_id-error"></span>

                        </div>
                        <div class="form-group">
                            <label for="manger">المدير</label>
                            <select name="manger" class="form-control " id="mangered" required>
                            <option value="">اختار المدير</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                            <span class="text-danger span-error" id="manger-error"></span>

                        </div>
                        <div class="form-row pt-4 mx-md-2 d-flex justify-content-center">
                        <div class="form-group col-md-10 ">
                            <label class="pb-2" for="description">الموظفين (يمكنك اختيار اكثر من واحد)</label>
                            <select name="employess[]" id="employees" class="form-group col-md-12 " multiple
                                style="   height: 150px; font-size: 18px; border:0.2px solid lightgray;" dir="rtl">
                                
                            </select>
                            
                        </div>
                    </div>
                        <!-- <div class="form-group">
                        <label for="employees">الموظفين </label>
                        <select name="employess[]" id="employees" class="form-control" multiple style="    height: 100px; font-size: 18px; border:0.2px solid lightgray;" dir="rtl">
                            
                        </select>
                       
                    </div> -->
                </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit-->
    <div class="modal fade" id="Departmentedit" tabindex="-1" aria-labelledby="extern-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">تعديل ادارة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="Departmentedit" action="{{ route('sub_departments.update', $department->id) }}" method="POST" enctype="multipart/form-data" onsubmit="return submitedit()">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $department->name) }}">
                            <span class="text-danger span-error" id="name-error"></span>

                        </div>
                        
                        <div class="form-group">
                        <label for="parent_id">الادارة</label>

                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">اختار الادارة</option>
                            @foreach ($subdepartments as $dept)
                                <option value="{{ $dept->id }}" {{ $dept->id == old('parent_id', $department->parent_id) ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                            <span class="text-danger span-error" id="parent_id-error"></span>

                        </div>
                        <div class="form-group">
                            <label for="mangers">المدير</label>

                            <select name="manger" id="mangers" class="form-control">
                                <option value="">اختر المدير </option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $user->id == old('manger', $department->manger) ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                                @endforeach
                            </select>
                            <span class="text-danger span-error" id="manger-error"></span>

                        </div>

                        <div class="form-row pt-4 mx-md-2 d-flex justify-content-center">
                        <div class="form-group col-md-10 ">
                            <label class="pb-2" for="description">الموظفين (يمكنك اختيار اكثر من واحد)</label>
                            <select name="employess[]" id="employeess" class="form-group col-md-12 " multiple
                                style="   height: 150px; font-size: 18px; border:0.2px solid lightgray;" dir="rtl">
                                
                            </select>
                           
                        </div>
                    </div>
                        <!-- Save button -->
                        <div class="text-end mx-2 mb-3">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        $.fn.dataTable.ext.classes.sPageButton = 'btn-pagination btn-sm'; // Change Pagination Button Class

        $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ url('api/sub_department') }}',
            columns: [
                { data: 'id',sWidth: '50px', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'children_count', name: 'children_count' },
                { data: 'action', name: 'action',  sWidth: '100px', orderable: false, searchable: false }
            ],
            columnDefs: [{
                targets: -1,
                render: function(data, type, row) {
                    var sub_departmentsEdit = '{{ route('sub_departments.edit', ':id') }}';
                    sub_departmentsEdit = sub_departmentsEdit.replace(':id', row.id);
                    // var departmentShow = '{{ route('departments.show', ':id') }}';
                    // departmentShow = departmentShow.replace(':id', row.id);
                    // var departmentDelete = '{{ route('departments.destroy', ':id') }}';
                    // departmentDelete = departmentDelete.replace(':id', row.id);

                    return `
                        <a href="" class="btn btn-sm"  style="background-color: #F7AF15;" data-bs-toggle="modal"
                            data-bs-target="#Departmentedit" data-dismiss="modal" id="edit-dev"> <i class="fa fa-edit"></i> </a>
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


    // modal Add
function submit(){
    var name = document.getElementById('name').value;
    var manger = document.getElementById('manger').value;
    var parent_id = document.getElementById('parent_id').value;
            var form = document.getElementById('Department');

            form.submit();
            resetModal();
            $('#Department').modal('hide');
}


// modal edit
function submitedit(){
    var name = document.getElementById('name').value;
    var manger = document.getElementById('manger').value;
    var parent_id = document.getElementById('parent_id').value;
            var form = document.getElementById('Departmentedit');

            form.submit();
            resetModal();
            $('#Departmentedit').modal('hide');
}

    $(document).ready(function() {
    function resetModal() {
        $('#Department')[0].reset();
        $('.text-danger').html('');
    }
    });
    // $("#Department").on("submit", function(e) {
    //     e.preventDefault();

    //     // Serialize the form data
    //     var formData = $(this).serialize();
    //     formData.submit();
    //     resetModal();
    //   $('#Department').modal('hide'); // Ensure this is the correct ID
        // var csrfToken = $('meta[name="csrf-token"]').attr('content');
        // // Submit AJAX request
        // $.ajax({
        //     url: $(this).attr('action'),
        //     type: 'POST',
        //     data: formData,
        //     headers: {
        //         'X-CSRF-TOKEN': csrfToken
        //     },
        //     success: function(response) {
        //         console.log('AJAX request successful:', response); // Debug statement

        //         if (response.success) {
        //             resetModal();
        //             $('#Department').modal('hide'); // Ensure this is the correct ID
        //         } else {
        //             $.each(response.message, function(key, value) {
        //                 $('#' + key + '-error').html(value[0]);
        //             });
        //         }
        //     },
        //     error: function(xhr, status, error) {
        //         console.error('AJAX request error:', xhr.responseText); // Debug statement

        //         if (xhr.status == 422) {
        //             var errors = xhr.responseJSON.errors;
        //             $.each(errors, function(key, value) {
        //                 $('#' + key + '-error').html(value[0]);
        //             });
        //         }
        //     }
        // });
//     });
// });
$(document).ready(function() {
    $('#parent_idd').on('change', function() {
        var departmentId = $(this).val();
        console.log(departmentId);

        if (departmentId) {
            $.ajax({
                url: '/employees/by-department/' + departmentId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#mangered').empty();
                    $('#employees').empty();
                    $.each(data, function(key, employee) {
                        // if (employee.id != selectedManager) {
                        //     $('#employees').append('<option value="' + employee.id + '">' + employee.name + '</option>');
                        // }
                        $('#mangered').append('<option value="' + employee.id + '">' + employee.name + '</option>');
                    });

                    $('#mangered').on('change', function() {
                        var selectedManager = $(this).val();
                        console.log(selectedManager);
                        $('#employees').empty();

                        $.each(data, function(key, employee) {
                        if (employee.id != selectedManager) {
                            $('#employees').append('<option value="' + employee.id + '">' + employee.name + '</option>');
                        }
                        // $('#mangered').append('<option value="' + employee.id + '">' + employee.name + '</option>');
                    });
                       
                    });
                    // var selectedManager = $('#mangered').val();
                    
                   
                    

                    
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            $('#mangered').empty();
            $('#employees').empty();
        }
    });
});





    // edit modal
    $(document).ready(function() {
        $('#parent_id').on('change', function() {
            var departmentId = $(this).val();
            console.log(departmentId);
            if (departmentId) {
                $.ajax({
                    url: '/employees/by-department/' + departmentId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                    $('#employeess').empty();
                    $('#mangers').empty();
                    $.each(data, function(key, employee) {
                        $('#employeess').append('<option value="' + employee.id + '" class="pb-2">' + employee.name + '</option>');
                        $('#mangers').append('<option value="' + employee.id + '" class="pb-2">' + employee.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('XHR:', xhr.responseText);
                }
            });
        } else {
            $('#employeess').empty();
        }
        });
    });
    </script>

@endsection
