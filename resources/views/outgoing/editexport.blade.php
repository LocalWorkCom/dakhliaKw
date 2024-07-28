@extends('layout.main')

@push('style')
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
                <li class="breadcrumb-item active" aria-current="page"> <a href="#"> تعديل الصادر</a></li>
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
            <div class="container col-10 mt-1 mb-5 pb-5 pt-4 mt-5" style="border:0.5px solid #C7C7CC;">
                @include('inc.flash')
                <form action="{{ route('Export.update', ['id' => $data->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-row mx-3 d-flex justify-content-center">

                        <div class="form-group col-md-10 mx-2">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control" value="{{ $data->num }}" name="num"
                                id="exportnum" required>
                        </div>

                    </div>
                    <div class="form-row mx-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-2">
                            <label for="date">تاريخ الصادر </label>
                            <input type="date" id="date" value="{{ $data->date }}" name="date"
                                class="form-control" required>
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="active">الحاله</label>
                            <select id="active" class="form-control" name="active" disabled>
                                <option value="0" @if ($data->active == 0) selected @endif>جديد</option>
                                <option value="1" @if ($data->active == 1) selected @endif> أرشيف</option>

                            </select>
                        </div>


                    </div>
                    <div class="form-row mx-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-2">
                            <label for="from_departement">الجهة المرسلة</label>
                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" style="display: none"
                                id="extern-department-dev" data-bs-target="#extern-department">
                                <i class="fa fa-plus"></i>
                            </button>
                            <select id="from_departement" name="from_departement" class="form-control">
                                <option value="">اختر الجهة</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}" @if ($data->department_id == $item->id) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-2 ">
                            <label for="select-person-to">الموظف المستلم </label>
                            <select id="select-person-to" name="person_to" class="form-control js-example-basic-single">
                                <option value="" disabled selected> اختر من القائمه</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if ($data->person_to == $user->id) selected @endif>
                                        {{ $user->name }} (الرقم العسكرى : {{ $user->military_number }})
                                    </option>
                                @endforeach
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="form-row mx-2 d-flex justify-content-center">
                        <div class="form-group col-md-10">
                            <label for="nameex">العنوان</label>
                            <textarea class="form-control" name="nameex" id="nameex" rows="3"> {{ $data->name }} </textarea>
                        </div>
                    </div>
                    <div class="form-row mx-2 d-flex justify-content-center">
                        <div class="form-group col-md-10">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"> {{ $data->note }} </textarea>
                        </div>
                    </div>

                    <div class="form-row mx-2 d-flex justify-content-center">
                        <div class="form-group  col-md-10 ">
                            <label for="files"> اضف ملفات بحد اقصي 10 </label>
                        </div>
                        <div class="form-group col-md-10 " dir="rtl">
                            <div class=" fileupload d-inline">
                                <div class="d-flex">
                                    <input id="fileInput" type="file" name="files[]" multiple class="mb-2 form-control"
                                        accept=".pdf,.jpg,.png,.jpeg">
                                    <button class="btn-all mx-1" type="button" onclick="uploadFiles()"
                                        style="color:green;"> اضف </button>
                                </div>
                                <div class="space-uploading">
                                    <ul id="fileList" class="d-flex flex-wrap">
                                        <!-- Uploaded files will be listed here -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group col-md-12">
                                            <label for="files">اضافة ملف</label>
                                            <div id="fileInputs">
                                                <div class="file-input mb-3" dir="rtl">
                                                    <input type="file" name="files[]" class="form-control">
                                                    <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                                                </div>
                                            </div> -->
                    <!-- </div> -->

                    <div class="form-row d-flex  justify-content-center" dir="rtl">
                        <div class="form-group d-flex justify-content-start col-md-10 ">
                            <button type="button" class="btn-all  mx-3" data-bs-toggle="modal" id="extern-user-dev"
                                data-bs-target="#extern-user" style="background-color: #FAFBFD; border: none;">
                                <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="">اضافة موظف
                            </button>
                            <button type="button" class="btn-all" data-bs-toggle="modal" id="extern-department-dev"
                                data-bs-target="#extern-department" style="background-color: #FAFBFD; border: none; ">
                                <img src="{{ asset('frontend/images/add-btn.svg') }}" alt=""> اضافة الجهه

                            </button>
                        </div>

                    </div><br>
                    <!-- <div class="form-row d-block ">
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

                                    </div> <br> -->
            </div>
            <div class="container col-10 mt-5 mb-3 ">
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
                            <span class="text-danger span-error" id="name-error"></span>

                        </div>
                        <div class="form-group">
                            <label for="desc">الوصف</label>
                            <input type="text" id="desc" name="desc" class="form-control" required>
                            <span class="text-danger span-error" id="desc-error"></span>

                        </div>
                        <div class="form-group">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control" required>
                            <span class="text-danger span-error" id="phone-error"></span>

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
                            <span class="text-danger span-error" id="name-error" dir="rtl"></span>

                        </div>
                        <div class="form-group">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phoneuser" class="form-control" required>
                            <span class="text-danger span-error" id="phoneuser-error" dir="rtl"></span>

                        </div>
                        <div class="form-group">
                            <label for="military_number">رقم العسكرى</label>
                            <input type="text" id="military_number" name="military_number" class="form-control"
                                required>
                            <span class="text-danger span-error" id="military_number-error" dir="rtl"></span>

                        </div>
                        <div class="form-group">
                            <label for="filenum">رقم الملف</label>
                            <input type="text" id="filenum" name="filenum" class="form-control" required>
                            <span class="text-danger span-error" id="filenum-error" dir="rtl"></span>

                        </div>
                        <div class="form-group">
                            <label for="Civil_number">رقم المدنى</label>
                            <input type="text" id="Civil_number" name="Civil_number" class="form-control" required>
                            <span class="text-danger span-error" id="Civil_number-error" dir="rtl"></span>

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
        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                let dateInput = document.getElementById('date');
                let dateInputValue = dateInput.value;
                console.log(dateInputValue);
                if (dateInputValue === "") {
                    let today = new Date();
                    let day = ("0" + today.getDate()).slice(-2);
                    let month = ("0" + (today.getMonth() + 1)).slice(-2);
                    let todayDate = today.getFullYear() + "-" + (month) + "-" + (day);
                    dateInput.value = todayDate;
                }
            });
        </script>
        <script>
            $(document).ready(function() {
                $(document).ready(function() {
                    $('#fileInput').on('change', function() {
                        if ($(this).val()) {
                            $('#active').prop('disabled', false);
                        } else {
                            $('#active').prop('disabled', true);
                        }
                    });
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                function resetModal() {
                    $('#saveExternalUser')[0].reset();
                    $('.text-danger').html('');
                }
                $("#saveExternalUser").on("submit", function(e) {
                    e.preventDefault();
                    var formData = $(this).serialize();
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.success) {
                                $('#select-person-to').empty();
                                $.ajax({
                                    url: "{{ route('external.users') }}",
                                    type: 'GET',
                                    success: function(response) {
                                        var selectOptions =
                                            '<option value="">اختر الشخص الصادر</option>';
                                        response.forEach(function(user) {
                                            selectOptions += '<option value="' +
                                                user.id + '">' + user.name +
                                                '</option>';
                                        });
                                        $('#select-person-to').html(selectOptions);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error(xhr.responseText);
                                    }
                                });
                                resetModal();
                                $('#extern-user').modal('hide');
                            } else {
                                $.each(response.message, function(key, value) {
                                    $('#' + key + '-error').html(value[0]);
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            if (xhr.status == 422) {
                                var errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $('#' + key + '-error').html(value[0]);
                                });
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
