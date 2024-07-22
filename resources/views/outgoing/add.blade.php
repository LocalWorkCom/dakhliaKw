@extends('layout.header')

@push('style')
@endpush

@section('content')
<section style="direction: rtl;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="{{ route('Export.index') }}">الصادرات </a></li>

        <li class="breadcrumb-item active">اضافه الصادر</li>

    </ol>
    
    <div class="container-fluid" style="text-align: center">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-block">
                    @include('inc.flash')
                    <form action="{{ route('Export.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                          <div class="form-group col-md-6">
                            <label for="nameex">العنوان</label>
                            <input type="text" class="form-control" name="nameex"  id="nameex" placeholder="العنوان" required>
                          </div>
                          <div class="form-group col-md-6">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control"  name="num" id="exportnum" required>
                          </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3" required> </textarea>
                        </div>
                        <div class="form-group">
                            <label for="select-person-to">person_to </label>
                            <select id="select-person-to" name="person_to" class="form-control">
                                <option disabled> اختر من القائمه</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->username }}  (الرقم العسكرى : {{ $user->military_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                       
                        <div class="form-group">
                                <div class="form-row">
                                    <label for="active">الحاله</label>
                                    <select id="active" class="form-control" name="active" >
                                        <option value="0" >مفعل</option>
                                        <option value="1">غير مفعل</option>
                                  
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label for="from_departement">الجهة المرسلة:</label>
                                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" style="display: none" id="extern-department-dev"
                                        data-bs-target="#extern-department">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <select id="from_departement" name="from_departement" class="form-control" required>
                                        <option value="">اختر الجهة</option>
                                        @foreach ($departments as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select> 
                               </div>
                        </div>
                        <div class="form-row">
                            
                           <div class="form-group col-md-4">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                        id="extern-department-dev" data-bs-target="#extern-department">
                                    أضافه أداره خارجيه
                                </button>
                           </div>
                           <div class="form-group col-md-5">
                            <!-- Button trigger modal -->
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                    id="extern-user-dev" data-bs-target="#extern-user">
                                أضافه شخص صادر خارجى 
                            </button>
                       </div>
                           <div class="form-group col-md-2">
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                    id="addFile-dev" data-bs-target="#addFile">
                                    اضافه ملفات
                            </button>
                            {{-- <button type="button" class="btn btn-primary btn-sm mt-2" id="addFile">إضافة ملف جديد</button> --}}

                           </div>

                        </div>
                         {{-- model for add files --}}
                            <div class="modal fade" id="addFile" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="extern-departmentLabel">إضافة ملفات جديدة</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-row">
                                                <div class="mb-3">
                                                    <label for="files">حمل الملفات</label>
                                                    <div id="fileInputs">
                                                        <div class="file-input mb-3">
                                                            <input type="file" name="files[]" class="form-control-file" required>
                                                            <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-sm mt-2" id="addFile">إضافة ملف جديد</button>

                                                <!-- Save button -->
                                                {{-- <div class="text-end">
                                                    <button type="submit" class="btn btn-primary">حفظ</button>
                                                </div> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">اضافه </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
  {{-- model for add new department --}}
    <div class="modal fade" id="extern-department" tabindex="-1" aria-labelledby="extern-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extern-departmentLabel">إضافة جهة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="saveExternalDepartment" action="{{ route('department.ajax') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name">الاسم:</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="desc">الوصف:</label>
                            <input type="text" id="desc" name="desc" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

   {{-- model for add new user --}}
   <div class="modal fade" id="extern-user" tabindex="-1" aria-labelledby="extern-departmentLabel"
   aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered">
       <div class="modal-content">
           <div class="modal-header">
               <h5 class="modal-title" id="extern-departmentLabel">إضافة شخص صادر جديدة</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="saveExternalUser" action="{{ route('userexport.ajax') }}" method="POST">
                   @csrf

                   <div class="mb-3">
                        <label for="nameus"> الاسم</label>
                        <input type="text" id="nameus" name="name" class="form-control" required>
                        </div>
                    <div class="mb-3">
                        <label for="phone">الهاتف</label>
                        <input type="text" id="phone" name="phone" class="form-control" required>
                    </div>
                   <div class="mb-3">
                       <label for="military_number">رقم العسكرى:</label>
                       <input type="text" id="military_number" name="military_number" class="form-control" required>
                   </div>
                   <div class="mb-3">
                       <label for="filenum">رقم الملف:</label>
                       <input type="text" id="filenum" name="filenum" class="form-control" required>
                   </div>
                   <div class="mb-3">
                       <label for="Civil_number">رقم الهويه</label>
                       <input type="text" id="Civil_number" name="Civil_number" class="form-control" required>
                   </div>

                   <!-- Save button -->
                   <div class="text-end">
                       <button type="submit" class="btn btn-primary">حفظ</button>
                   </div>
               </form>
           </div>
       </div>
   </div>
</div>
</section>
@endsection

@push('scripts')
    <script> 
$(document).ready(function() {

$("#saveExternalUser").on("submit", function(e) {
    e.preventDefault();
    // Serialize the form data
    var formData = $(this).serialize(); // Changed to $(this)
    // Submit AJAX request
    $.ajax({
        url: $(this).attr('action'), // Changed to $(this)
        type: 'POST',
        data: formData,
        success: function(response) {
            $('#select-person-to').empty();
            $.ajax({

                url: "{{ route('external.users') }}",
                type: 'get',
                success: function(response) {
                    // Handle success response
                    var selectOptions =
                        '<option value="">اختر الشخص الصادر</option>';
                    response.forEach(function(department) {
                        selectOptions += '<option value="' +
                            department.id +
                            '">' + department.name +
                            '</option>';
                    });
                    $('#select-person-to').html(
                        selectOptions
                    );

                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error(xhr.responseText);
                }
            });
            // Optionally, you can close the modal after successful save
            $('#extern-user').modal('hide'); // Changed modal ID
        },
        error: function(xhr, status, error) {
            // Handle error response
            console.error(xhr.responseText);
        }
    });
});
});
$(document).ready(function() {
    let fileInputCount = 1;
            const maxFileInputs = 9;
            $('#addFile').click(function() {
                    var fileCount = $('#fileInputs').find('.file-input').length;
                    if (fileCount < 10) {
                        var newInput = '<div class="file-input mb-3">' +
                            '<input type="file" name="files[]" class="form-control-file" >' +
                            '<button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>' +
                            '</div>';
                        $('#fileInputs').append(newInput);
                        checkFileCount(); // Update button states
                    } else {
                        alert('لا يمكنك إضافة المزيد من الملفات.');
                    }
                });

                // Remove file input
                $(document).on('click', '.remove-file', function() {
                    $(this).parent('.file-input').remove();
                    checkFileCount(); // Update button states

                });

                function checkFileCount() {
                    var fileCount = $('#fileInputs').find('.file-input').length;
                    if (fileCount > 1) {
                        $('.remove-file').prop('disabled', false);
                    } else {
                        $('.remove-file').prop('disabled', true);
                    }
                }
        });

    </script>
@endpush
