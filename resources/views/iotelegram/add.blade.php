@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    {{-- <div class="mb-3">
            <a href="{{ route('iotelegrams.list') }}" class="btn btn-primary mt-3">رجوع</a>
        </div> --}}
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('iotelegrams.list') }}">الواردات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافه </a></li>
            </ol>
        </nav>
    </div>
    @include('inc.flash')
    <div class="row ">
        <div class="container welcome col-11">
            <p> الــــــــــــواردات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <form action="{{ route('iotelegram.store') }}" method="POST" enctype="multipart/form-data">
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

                <div class="container col-10 mt-4" style="border:0.5px solid #C7C7CC;">

                    <div class="form-row pt-4">
                        <div class="form-group col-md-6 ">
                            <label for="representive_id">اختر المندوب </label>
                            <select id="representive_id" name="representive_id" class="form-control" required>
                                <option value="">اختر المندوب</option>
                                @foreach ($representives as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="recieved_by">الموظف المستلم</label>
                            <select id="recieved_by" name="recieved_by" class="form-control" required>
                                <option value="">اختر الموظف</option>
                                @foreach ($recieves as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="date">التاريخ:</label>
                            <input type="date" id="date" name="date" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="from_departement">الجهة المرسلة</label>

                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" style="display: none"
                                id="extern-department-dev" data-bs-target="#extern-department">
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
                        <div class="form-group col-md-12">
                            <label for="files_num"> عدد الكتب</label>

                            <select id="files_num" name="files_num" class="form-control" required>
                                <option value="">اختر العدد</option>

                                @for ($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-row" dir="rtl">
                        <button type="button" class="btn-all mt-3" data-bs-toggle="modal" data-bs-target="#representative"
                            data-dismiss="modal" id="representative-dev" style="background-color: #FAFBFD; border: none;">
                            <img src="../images/add-btn.svg" alt=""> اضافة مندوب
                        </button>
                    </div> <br>
                    <div class="form-row d-block ">
                        <div class="form-group col-md-12">
                            <label for="files">اضافة ملف</label>
                            <div id="fileInputs">
                                <div class="file-input mb-3" dir="rtl">
                                    <input type="file" name="files[]" class="form-control-file">
                                    <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-row" dir="rtl">
                        <button type="button" class="btn-all btn-sm mt-2" id="addFile"
                            style="background-color: #FAFBFD; border: none;"><img src="../images/add-btn.svg"
                                alt="">إضافة ملف جديد
                        </button>

                    </div> <br>
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
                        <img src="../images/add-mandob.svg" alt="">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> &times;
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addRepresentativeForm" action="{{ route('postman.ajax') }}" method="POST">
                        @csrf


                        <div class="form-group">
                            <label for="modal-department_id ">الادارة</label>
                            <select id="modal-department_id" name="modal_department_id" class="form-control">
                                <option value="">اختر الادارة</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">الاسم</label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="national_id">رقم الهوية</label>
                            <input type="text" id="national_id" name="national_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="phone1">رقم الهاتف الاول</label>
                            <input type="text" id="phone1" name="phone1" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="phone2">رقم الهاتف الثاني</label>
                            <input type="text" id="phone2" name="phone2" class="form-control">
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
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="desc">الوصف</label>
                            <input type="text" id="desc" name="desc" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control">
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

    @push('scripts')
        <script>
            function sortSelectOptions(selectId) {
                var options = $(selectId + ' option');
                options.sort(function(a, b) {
                    return a.text.localeCompare(b.text);
                });
                $(selectId).empty().append(options);
            }
            $(document).ready(function() {
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

                            } else {
                                // Handle success:false scenario if needed
                                console.log(response.errors); // Log validation errors if any
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
@endsection
