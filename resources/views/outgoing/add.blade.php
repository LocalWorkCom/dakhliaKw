@extends('layout.main')

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('title')
    أضافه
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('Export.index') }}">الصادرات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافه الصادر</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> الصــــــــــــادرات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row justify-content-center" dir="rtl">
                <div class="form-group mt-4  mx-5 col-10 d-flex ">
                    <button type="button" class="wide-btn  " data-bs-toggle="modal" id="extern-user-dev"
                        data-bs-target="#extern-user" style="color: #0D992C;">
                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        اضافة شخص خارجى
                    </button>

                    <button type="button" class="btn-all mx-3 " data-bs-toggle="modal" id="extern-department-dev"
                        data-bs-target="#extern-department" style="color: #0D992C;">
                        <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                        اضافة أداره خارجيه
                    </button>
                </div>
            </div>
            <div class="container col-10 mt-1 mb-5 pb-5 pt-4" style="border:0.5px solid #C7C7CC;">
                @include('inc.flash')
                <form action="{{ route('Export.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row mx-2 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-2">
                            <label for="nameex">العنوان</label>
                            <input type="text" class="form-control" name="nameex" id="nameex" placeholder="العنوان"
                                required>
                        </div>
                        <div class="form-group col-md-5 mx-2 ">
                            <label for="select-person-to">person_to </label>
                            <select id="mySelect" name="person_to" class="form-control js-example-basic-single" >
                                <option value="" disabled selected> اختر من القائمه</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }} (الرقم العسكرى : {{ $user->military_number }})
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row mx-2 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-2">
                            <label for="date">تاريخ الصادر </label>
                            <input type="date" id="date" name="date" class="form-control" required>
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control" name="num" id="exportnum" required>
                        </div>

                    </div>
                    <div class="form-row mx-2 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-2">
                            <label for="active">الحاله</label>
                            <select id="active" class="form-control" name="active">
                                <option value="0">مفعل</option>
                                <option value="1">غير مفعل</option>

                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="from_departement">الجهة المرسلة</label>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" style="display: none"
                                id="extern-department-dev" data-bs-target="#extern-department">
                                <i class="fa fa-plus"></i>
                            </button>
                            <select id="from_departement" name="from_departement" class="form-control">
                                <option value="">اختر الجهة</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row  d-flex justify-content-center">
                        <div class="form-group col-md-10">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3" required> </textarea>
                        </div>
                    </div>
                    {{-- <div class="form-row  d-flex justify-content-center">
                    <div class="form-group col-md-10">
                        <label for="files">الملفات</label>
                        <div class="mb-2 d-flex" id="fileInputs">
                            <input type="file" name="files[]" class="form-control" dir="rtl"
                                style="border: none"> <br>
                            <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>

                        </div>
                        <button type="button" class=" btn-all mx-3" id="addFile">إضافة ملف جديد</button>
                    </div>
                </div> --}}
                    <div class="form-row d-block ">
                        <div class="form-group col-md-12">
                            <label for="files">اضافة ملف</label>
                            <div id="fileInputs">
                                <div class="file-input mb-3" dir="rtl">
                                    <input type="file" name="files[]" class="form-control">
                                    <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row" dir="rtl">
                        <button type="button" class="btn-all btn-sm mt-2" id="addFile"
                            style="background-color: #FAFBFD; border: none;"><img
                                src="{{ asset('frontend/images/add-btn.svg') }}" alt="">إضافة ملف جديد
                        </button>

                    </div> <br>
            </div>
            <div class="container col-10 mt-5 mb-5 ">
                <div class="form-row col-10 " dir="ltr">
                    <button class="btn-blue " type="submit">
                        اضافة </button>
                </div>
            </div>
            <br>
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
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة جهة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                        </button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="saveExternalDepartment" action="{{ route('department.ajax') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="desc">الوصف</label>
                            <input type="text" id="desc" name="desc" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
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
    {{-- model for add new user --}}
    <div class="modal fade" id="extern-user" tabindex="-1" aria-labelledby="extern-departmentLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة شخص خارجى</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            &times;</button>
                    </div>
                </div>
                <div class="modal-body">
                    <form id="saveExternalUser" action="{{ route('userexport.ajax') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="nameus"> الاسم</label>
                            <input type="text" id="nameus" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="military_number">رقم العسكرى</label>
                            <input type="text" id="military_number" name="military_number" class="form-control"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="filenum">رقم الملف</label>
                            <input type="text" id="filenum" name="filenum" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="Civil_number">رقم الهويه</label>
                            <input type="text" id="Civil_number" name="Civil_number" class="form-control" required>
                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- </div>
</section>  --}}
    @endsection

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>


        <script>
            $(document).ready(function() {
                $('.js-example-basic-single').select2();
            });
        </script>

        </script>
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
                            '<input type="file" name="files[]" class="form-control-file" required>' +
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
