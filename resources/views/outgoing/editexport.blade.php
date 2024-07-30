@extends('layout.main')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
                    enctype="multipart/form-data" onsubmit="return validation()">
                    @csrf

                    <div class="form-row mx-md-2 d-flex justify-content-center">

                        <div class="form-group col-md-10 ">
                            <div class="d-flex justify-content-between" dir="rtl">
                                <label for="select-person-to"> الموظف المستلم</label>
                                @if (Auth::user()->hasPermission('edit exportuser'))
                                    <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="" class="mx-2 mb-2"
                                        data-bs-toggle="modal" id="extern-user-dev" data-bs-target="#extern-user">
                                @endif
                            </div>
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
                    <div class="form-row mx-md-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="date">تاريخ الصادر </label>
                            <input type="date" id="date" name="date" value="{{ $data->date }}"
                                class="form-control" required>
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="exportnum">رقم الصادر</label>
                            <input type="text" class="form-control" name="num" value="{{ $data->num }}"
                                id="exportnum" hidden>
                            <input type="text" class="form-control" name="num" value="{{ $data->num }}"
                                id="exportnum" disabled>
                        </div>

                    </div>
                    <div class="form-row mx-md-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="active">الحاله</label>
                            <select id="active" class="form-control" name="active" disabled>
                                <option value="0" @if ($data->active == 0) selected @endif>جديد</option>
                                <option value="1" @if ($data->active == 1) selected @endif> أرشيف</option>

                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <div class="d-flex justify-content-between" dir="rtl">
                                <label for="from_departement"> القطاع </label>
                                @if (Auth::user()->hasPermission('create ExternalDepartment'))
                                    <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="" class="mx-2 mb-2"
                                        data-bs-toggle="modal" id="extern-department-dev"
                                        data-bs-target="#extern-department">
                                @endif
                            </div>
                            <!-- <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" style="display: none"
                                                                id="extern-department-dev" data-bs-target="#extern-department">
                                                                <i class="fa fa-plus"></i>
                                                            </button> -->
                            <select id="from_departement" name="from_departement" class="form-control">
                                <option value="">اختر القطاع</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}" @if ($data->department_id == $item->id) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (Auth::user()->hasPermission('edit outgoing_files'))
                        <div class="form-row mx-md-2 d-flex justify-content-center">
                            <div class="form-group col-md-10">
                                <label for="files_num"> عدد الكتب</label>

                                <select id="files_num" name="files_num" class="form-control" onchange="updateFileInput()">
                                    <option value="">اختر العدد</option>

                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="form-row mx-md-2 d-flex justify-content-center">
                        <div class="form-group col-md-10">
                            <label for="nameex">العنوان</label>
                            <textarea type="text" class="form-control" name="nameex" id="nameex" placeholder="العنوان" required>{{ $data->name }}</textarea>
                        </div>
                    </div>
                    <div class="form-row mx-md-2 d-flex justify-content-center">
                        <div class="form-group col-md-10">
                            <label for="exampleFormControlTextarea1">ملاحظات </label>
                            <textarea class="form-control" name="note" id="exampleFormControlTextarea1" rows="3"> {{ $data->note }}</textarea>
                        </div>
                    </div>
                    @if (Auth::user()->hasPermission('edit outgoing_files'))
                        <div class="form-row mx-md-2 d-flex justify-content-center">
                            <div class="form-group col-md-10">
                                <label for="files">اضف ملفات بحد اقصي 10</label>
                            </div>
                            <div class="form-group col-md-10" dir="rtl">
                                <div class="fileupload d-inline">
                                    <div class="d-flex">
                                        <input id="fileInput" type="file" name="files[]" multiple
                                            class="mb-2 form-control" accept=".pdf,.jpg,.png,.jpeg"
                                            onchange="uploadFils()" disabled>
                                    </div>
                                    <div class="space-uploading">
                                        <ul id="fileList" class="d-flex flex-wrap">
                                            <!-- Uploaded files will be listed here -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- <div class="form-row d-flex  justify-content-center" dir="rtl">
                                                        <div class="form-group d-flex justify-content-start col-md-10 "> -->
                    <!-- <button type="button" class="btn-all  mx-md-3" data-bs-toggle="modal" id="extern-user-dev"
                                                                data-bs-target="#extern-user" style="background-color: #FAFBFD; border: none;">
                                                                <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="">اضافة موظف
                                                            </button> -->
                    <!-- <button type="button" class="btn-all" data-bs-toggle="modal" id="extern-department-dev"
                                                                data-bs-target="#extern-department" style="background-color: #FAFBFD; border: none; ">
                                                                <img src="{{ asset('frontend/images/add-btn.svg') }}" alt=""> اضافة الجهه
        
                                                            </button> -->
                    <!-- </div> -->

                    <!-- </div><br> -->
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

            function validation() {
                var personToSelect = document.getElementById('select-person-to');
                var fromDepartmentSelect = document.getElementById('from_departement');
                var fileNum = document.getElementById('files_num');
                var files = document.getElementById('fileInput');
                if (fileNum.value != "" && files.value === "") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'من فضلك أختر الملفات المطلوبه',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__slow'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    });
                    return false;
                }
                // Check if at least one select has a selected value
                if (personToSelect.value === "" && fromDepartmentSelect.value === "") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'من فضلك اختر القطاع او الموظف المستلم التابعين الى هذا الصادر',
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown animate__slow'
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp'
                        }
                    });
                    return false; // Prevent form submission
                }
            }

            // function updateFileInput() {
            //     var fileInput = document.getElementById('fileInput');
            //     var filesNum = document.getElementById('files_num').value;

            //     if (filesNum) {
            //         fileInput.disabled = false;
            //     } else {
            //         fileInput.disabled = true;
            //         document.getElementById('fileList').innerHTML = '';
            //     }
            // }

            // function uploadFils() {
            //     const files = document.getElementById('fileInput').files;
            //     const fileList = document.getElementById('fileList');
            //     const filesNum = parseInt(document.getElementById('files_num').value);

            //     if (!filesNum) {
            //         alert("Please choose the number of books first.");
            //         document.getElementById('fileInput').value = '';
            //         return;
            //     }

            //     if (files.length === 0) {
            //         alert("Please choose files.");
            //         return;
            //     }

            //     if (files.length > filesNum) {
            //         alert('لا يمكنك أضافه اكثر من' + filesNum + ' ملف.');
            //         document.getElementById('fileInput').value = '';
            //         return;
            //     }
            //     if (files.length < filesNum) {
            //         alert('لا يمكن اضافه ملفات أقل من ' + filesNum + ' ملف.');
            //         document.getElementById('fileInput').value = '';
            //         return;
            //     }

            //     fileList.innerHTML = ''; // Clear previous list

            //     for (let i = 0; i < files.length; i++) {
            //         const file = files[i];

            //         const listItem = document.createElement('li');
            //         listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
            //         listItem.dataset.filename = file.name;

            //         const fileName = document.createElement('span');
            //         fileName.textContent = file.name;

            //         const deleteButton = document.createElement('button');
            //         deleteButton.className = 'btn btn-danger btn-sm';
            //         deleteButton.textContent = 'Delete';
            //         deleteButton.onclick = function() {
            //             fileList.removeChild(listItem);
            //             document.getElementById('fileInput').value = '';
            //         };

            //         listItem.appendChild(fileName);
            //         listItem.appendChild(deleteButton);
            //         fileList.appendChild(listItem);
            //     }
            // }
        </script>
    @endpush
