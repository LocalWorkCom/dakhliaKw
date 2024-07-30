@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('iotelegrams.list') }}">الواردات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة </a></li>
            </ol>
        </nav>
    </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <form action="{{ route('iotelegram.store') }}" method="POST" enctype="multipart/form-data"
                onsubmit="return validation()">
                @csrf

                <div class="container col-10 mt-5" dir="rtl">
                    <div class="form-row justify-content-center">
                        <div class="header-radio d-flex align-items-center justify-content-around">
                            <div class="radio1 mr-3">
                                <input type="radio" id="extern" name="type" value="out" required>
                                <label for="extern">خارجي</label>
                            </div>
                            <div class="radio2">
                                <input type="radio" id="intern" name="type" checked value="in" required>
                                <label for="intern">داخلي</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container col-10 mt-4 pb-3" style="border:0.5px solid #C7C7CC;">
                    <div class="form-row pt-2 mx-md-3 d-flex justify-content-center  mt-5">
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="outgoing_num">رقم الصادر</label>
                            <input type="text" id="outgoing_num" name="outgoing_num" class="form-control" required
                                value="">
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="outgoing_date">تاريخ الصادر</label>
                            <input type="date" id="outgoing_date" name="outgoing_date" class="form-control" required>
                        </div>

                    </div>

                    <div class="form-row mx-md-3 d-flex justify-content-center">

                        <div class="form-group col-md-5 mx-md-2">
                            <label for="date">التاريخ</label>
                            <input type="date" id="date" name="date" class="form-control" required>
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="iotelegram_num"> رقم الوارد</label>
                            <input type="hidden" name="iotelegram_num" id="iotelegram_num" value="{{ $iotelegram_num }}">
                            <input type="text" id="iotelegram_num" name="iotelegram_num" class="form-control" disabled
                                value="{{ $iotelegram_num }}">
                        </div>
                    </div>

                    <div class="form-row pt-2 mx-md-3 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-2 " dir="rtl">
                            <div class="d-flex justify-content-between">
                                <label for="representive_id">اختر المندوب </label>
                                @if (Auth::user()->hasPermission('create Postman'))
                                    <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="" class="mx-2 mb-2"
                                        data-bs-toggle="modal" data-bs-target="#representative" data-dismiss="modal"
                                        id="representative-dev">
                                @endif
                            </div>
                            <select id="representive_id" name="representive_id" class="form-control" required>
                                <option value="">اختر المندوب</option>
                                @foreach ($representives as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-md-2 " dir="rtl">
                            <div class="d-flex justify-content-between">
                                <label for="from_departement">القطاع </label>
                                @if (Auth::user()->hasPermission('create ExternalDepartment'))
                                    <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="" class="mx-2 "
                                        style="display: none" data-bs-toggle="modal" id="extern-department-dev"
                                        data-bs-target="#extern-department" data-dismiss="modal">
                                @endif
                            </div>
                            <select id="from_departement" name="from_departement" class="form-control" required>
                                <option value="">اختر القطاع</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>

                    <div class="form-row pt-2 mx-md-3 d-flex justify-content-center">
                        @if (Auth::user()->hasPermission('create Io_file'))
                            <div class="form-group col-md-5 mx-md-2">
                                <label for="files_num"> عدد الكتب</label>

                                <select id="files_num" name="files_num" class="form-control"
                                    onchange="updateFileInput()">
                                    <option value="">اختر العدد</option>

                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        @endif
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="recieved_by">الموظف المستلم</label>
                            <select id="recieved_by" name="recieved_by" class="form-control" required>
                                <option value="">اختر الموظف</option>
                                @foreach ($recieves as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>


                    </div>


                    @if (Auth::user()->hasPermission('create Io_file'))
                        <div class="form-row mx-md-2 d-flex justify-content-center">
                            <div class="form-group col-md-10">
                                <label for="files">اضف ملفات بحد اقصي 10</label>
                            </div>
                            <div class="form-group col-md-10" dir="rtl">
                                <div class="fileupload d-inline">
                                    <div class="d-flex">
                                        <input id="fileInput" type="file" name="files[]" multiple
                                            class="mb-2 form-control" accept=".pdf,.jpg,.png,.jpeg"onchange="uploadFils()"
                                            disabled>

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

                    <div class="form-row  mx-md-2 d-flex justify-content-center">
                        <div class="form-group d-flex col-md-10 mx-md-2" dir="rtl">
                            <input type="checkbox" id="linked_employee">
                            <label for="linked_employee">هل الوارد خاص بموظف ؟</label>
                        </div>
                    </div>

                    <div class="form-row  mx-md-2 d-flex justify-content-center">
                        <div class="form-group col-md-10 mx-md-2 " id="identityGroup" hidden>
                            <label for="user_id">رقم الهوية أو العسكري</label>
                            <select id="user_id" class="form-control" name="user_id">
                                <option value="" selected>اختر المستخدم</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"> {{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="container col-10 ">
                    <div class="form-row mt-4 mb-5">
                        <button type="submit" class="btn-blue">حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <!-- Modal -->
    <div class="modal fade" id="representative" tabindex="-1" aria-labelledby="representativeLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="representativeLabel">إضافة مندوب</h5>
                        <img src="{{ asset('frontend/images/add-mandob.svg') }}" alt="">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addRepresentativeForm" action="{{ route('postman.ajax') }}" method="POST">
                        @csrf


                        <div class="form-group">
                            <label for="modal-department_id ">الادارة</label>
                            <select id="modal-department_id" name="modal_department_id" class="form-control" required>
                                <option value="">اختر الادارة</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <span class="text-danger span-error" id="name-error"></span>

                        </div>
                        <div class="form-group">
                            <label for="national_id">رقم الهوية</label>
                            <input type="text" id="national_id" name="national_id" class="form-control"required>
                            <span class="text-danger span-error" id="national_id-error"></span>

                        </div>
                        <div class="form-group">
                            <label for="phone1">رقم الهاتف الاول</label>
                            <input type="text" id="phone1" name="phone1" class="form-control" required>
                            <span class="text-danger span-error" id="phone1-error"></span>

                        </div>
                        <div class="form-group">
                            <label for="phone2">رقم الهاتف الثاني</label>
                            <input type="text" id="phone2" name="phone2" class="form-control">
                            <span class="text-danger span-error" id="phone2-error"></span>

                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn-blue">حفظ</button>
                        </div>
                    </form>

                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                </button>
            </div>

        </div>
    </div>
    <div class="modal fade" id="extern-department" tabindex="-1" aria-labelledby="extern-departmentLabel"
        role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <div class="title d-flex flex-row align-items-center">
                        <h5 class="modal-title" id="extern-departmentLabel">إضافة جهة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            &times; </button>
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
                            <input type="text" id="desc" name="desc" class="form-control">

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
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function sortSelectOptions(selectId) {
            var options = $(selectId + ' option');
            options.sort(function(a, b) {
                return a.text.localeCompare(b.text);
            });
            $(selectId).empty().append(options);
        }

        function validation() {

            var fileNum = document.getElementById('files_num');
            var files = document.getElementById('fileInput');
            if (fileNum.value != "" && files.value === "") {
                alert('من فضلك أختر الملفات المطلوبه');
                return false; // Prevent form submission
            }

        }

        function resetModal() {
            $('#saveExternalDepartment')[0].reset();
            $('#addRepresentativeForm')[0].reset();
            $('.text-danger').html('');
        }
        $(document).ready(function() {
            var today = new Date().toISOString().split('T')[0];


            $('#outgoing_date').attr('value', today);
            $('#date').attr('value', today);

            $('#date').attr('value', today);

            checkFileCount();

            $("#addRepresentativeForm").on("submit", function(e) {
                e.preventDefault();

                var formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        console.log(response); // Log the response for debugging

                        if (response.success) {
                            $('#representative').modal('hide'); // Close the modal on success

                            // Construct new option
                            var newOption = '<option value="' + response.postman.id + '">' +
                                response.postman.name + '</option>';

                            // Append new option to select element
                            $('#representive_id').append(newOption);

                            // Optionally, you can sort options alphabetically
                            sortSelectOptions('#representive_id');
                            resetModal();


                        } else {
                            // Handle success:false scenario if needed
                            $.each(response.message, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText); // Log the error response for debugging
                    }
                });
            });
            // Additional event handler for radio button click
            $('input[name=type]').click(function() {
                if ($(this).is(':checked')) {
                    var value = $(this).val();
                    console.log(value);
                    if (value == 'in') {
                        $('#extern-department-dev').hide();
                        $('#from_departement').show();
                        $('#from_departement').empty();
                        $.ajax({

                            url: "{{ route('internal.departments') }}",
                            type: 'get',
                            success: function(response) {
                                console.log(response);
                                // Handle success response
                                var selectOptions =
                                    '<option value="">اختر الادارة</option>';
                                response.forEach(function(department) {
                                    selectOptions += '<option value="' + department.id +
                                        '">' + department.name + '</option>';
                                });
                                $('#from_departement').html(
                                    selectOptions
                                ); // Assuming you have a select element with id 'from_departement'

                            },
                            error: function(xhr, status, error) {
                                // Handle error response
                                console.error(xhr.responseText);
                            }
                        });

                    } else {
                        $('#extern-department-dev').show();
                        $('#from_departement').empty();
                        $.ajax({

                            url: "{{ route('external.departments') }}",
                            type: 'get',
                            success: function(response) {
                                console.log(response);
                                // Handle success response
                                var selectOptions =
                                    '<option value="">اختر الادارة</option>';
                                response.forEach(function(department) {
                                    selectOptions += '<option value="' + department.id +
                                        '">' + department.name + '</option>';
                                });
                                $('#from_departement').html(
                                    selectOptions
                                ); // Assuming you have a select element with id 'from_departement'

                                // Optionally, you can close the modal after successful save
                                $('#exampleModal').modal('hide');
                            },
                            error: function(xhr, status, error) {
                                // Handle error response
                                console.error(xhr.responseText);
                            }
                        });
                    }

                }
            });

            $('#addFile').click(function() {
                var files_num = $('#files_num option:selected').val();
                if (files_num == '') {
                    alert("please choose file number");
                    return;
                }
                var fileCount = $('#fileInputs').find('.file-input').length;
                if (fileCount < files_num) {
                    var newInput = '<div class="file-input mb-3">' +
                        '<input type="file" name="files[]" class="form-control" required>' +
                        '<button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>' +
                        '</div>';
                    $('#fileInputs').append(newInput);
                    checkFileCount(); // Update button states
                } else {
                    alert('لا يمكنك إضافة المزيد من الملفات.');
                }
            });
            $('#linked_employee').click(function() {
                if ($(this).is(':checked')) {
                    $('#identityGroup').attr('hidden', false);
                    $('#user_id').attr('required', true);
                } else {
                    $('#identityGroup').attr('hidden', true);
                    $('#user_id').attr('required', false);


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
