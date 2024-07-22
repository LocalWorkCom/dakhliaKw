@extends('layout.header')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.list') }}" class="btn btn-primary mt-3">رجوع</a>
        </div>
        @include('inc.flash')

        <div class="card">
            <div class="card-header">الواردات</div>
            <div class="card-body">
                <form action="{{ route('iotelegram.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- <div class="mb-3">
                        <label for="date">التاريخ:</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div> -->
                    <!-- <div class="row" style="justify-content: space-evenly;">
                        <div class="mb-3">
                            <input type="radio" id="intern" name="type" checked value="in" required>
                            <label for="radio">داخلي</label>
                        </div>
                        <div class="mb-3">
                            <input type="radio" id="extern" name="type" value="out" required>
                            <label for="radio">خارجي</label>
                        </div>
                    </div> -->
                    <!-- <div class="mb-3">
                        <label for="from_departement">الجهة المرسلة:</label>

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
                    </div> -->

                    <!-- <div class="mb-3">
                        <label for="representive_id">اسم المندوب الجهة المرسلة :</label>
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#representative" id="representative-dev">
                            <i class="fa fa-plus"></i>
                        </button>
                        <select id="representive_id" name="representive_id" class="form-control" required>
                            <option value="">اختر المندوب</option>
                            @foreach ($representives as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div> -->

                    <!-- <div class="mb-3">
                        <label for="recieved_by">الموظف المستلم:</label>
                        <select id="recieved_by" name="recieved_by" class="form-control" required>
                            <option value="">اختر الموظف</option>
                            @foreach ($recieves as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div> -->

                    <!-- <div class="mb-3">
                        <label for="files_num"> عدد الكتب:</label>
                        <br>
                        <select id="files_num" name="files_num" class="form-control" required>
                            <option value="">اختر العدد</option>

                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div> -->
                    <div class="mb-3">
                        <!-- <label for="files">الملفات:</label>
                        <div id="fileInputs">
                            <div class="file-input mb-3">
                                <input type="file" name="files[]" class="form-control-file">
                                <button type="button" class="btn btn-danger btn-sm remove-file">حذف</button>
                            </div> -->
                        </div>
                        <!-- <button type="button" class="btn btn-primary btn-sm mt-2" id="addFile">إضافة ملف جديد</button> -->
                    </div>

                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="representative" tabindex="-1" aria-labelledby="representativeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="representativeLabel">إضافة مندوب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRepresentativeForm" action="{{ route('postman.ajax') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="modal_from_departement">الادارة:</label>
                            <select id="modal_from_departement" name="modal_from_departement" class="form-control"
                                required>
                                <option value="">اختر الادارة</option>
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="name">الاسم:</label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="national_id">رقم الهوية:</label>
                            <input type="text" id="national_id" name="national_id" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone1">رقم الهاتف الاول:</label>
                            <input type="text" id="phone1" name="phone1" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone2">رقم الهاتف الثاني:</label>
                            <input type="text" id="phone2" name="phone2" class="form-control" required>
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                checkFileCount();

                $("#addRepresentativeForm").on("submit", function(e) {
                    e.preventDefault();

                    // Serialize the form data
                    var formData = $(this).serialize(); // Changed to $(this)

                    // Submit AJAX request
                    $.ajax({
                        url: $(this).attr('action'), // Changed to $(this)
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            // Handle success response
                            console.log(response);
                            $.ajax({

                                url: "{{ route('postman.get') }}",
                                type: 'get',
                                success: function(response) {
                                    // Handle success response
                                    var selectOptions =
                                        '<option value="">اختر المندوب</option>';
                                    response.forEach(function(postman) {
                                        selectOptions += '<option value="' +
                                            postman.id +
                                            '">' + postman.name +
                                            '</option>';
                                    });
                                    $('#representive_id').html(
                                        selectOptions
                                    ); // Assuming you have a select element with id 'from_departement'


                                },
                                error: function(xhr, status, error) {
                                    // Handle error response
                                    console.error(xhr.responseText);
                                }
                            });
                            // Optionally, you can close the modal after successful save
                            $('#representative').modal('hide'); // Changed modal ID
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error(xhr.responseText);
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
