@extends('layout.header')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.list') }}" class="btn btn-primary mt-3">رجوع</a>
        </div>

        <div class="card">
            <div class="card-header">الواردات</div>
            <div class="card-body">
                <form action="{{ route('iotelegram.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="date">التاريخ:</label>
                        <input type="date" id="date" name="date" class="form-control">
                    </div>
                    <div class="row" style="justify-content: space-evenly;">
                        <div class="mb-3">
                            <input type="radio" id="intern" name="type" checked value="intern">
                            <label for="radio">داخلي</label>
                        </div>
                        <div class="mb-3">
                            <input type="radio" id="extern" name="type" value="extern">
                            <label for="radio">خارجي</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="department_id">الجهة المرسلة:</label>
                        <div id="extern-department-dev" style="display: none">

                            <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                                data-bs-target="#extern-department">
                                <i class="fa fa-plus"></i> اضافة جديد
                            </button>
                        </div>
                        <select id="department_id" name="department_id" class="form-control">
                            <option value="">اختر الجهة</option>
                            @foreach ($departments as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#representative">
                            <i class="fa fa-plus"></i>
                        </button>
                        <label for="representive">اسم المندوب الجهة المرسلة :</label>
                        <select id="representive" name="representive" class="form-control">
                            <option value="">اختر المندوب</option>
                            @foreach ($representives as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="recieved_by">الموظف المستلم:</label>
                        <select id="recieved_by" name="recieved_by" class="form-control">
                            <option value="">اختر الموظف</option>
                            @foreach ($recieves as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="files_num"> عدد الكتب:</label>
                        <br>
                        <select id="files_num" name="files_num" class="form-control">
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">التالي</button>
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
                            <label for="modal-department_id">الادارة:</label>
                            <select id="modal-department_id" name="modal-department_id" class="form-control">
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
                            <input type="text" id="national_id" name="national_id" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="phone1">رقم الهاتف الاول:</label>
                            <input type="text" id="phone1" name="phone1" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="phone2">رقم الهاتف الثاني:</label>
                            <input type="text" id="phone2" name="phone2" class="form-control">
                        </div>
                        <!-- Save button -->
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="saveRepresentative">حفظ</button>
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
                    <form id="addRepresentativeForm" action="{{ route('department.ajax') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name">الاسم:</label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="desc">الوصف:</label>
                            <input type="text" id="desc" name="desc" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="phone">الهاتف</label>
                            <input type="text" id="phone" name="phone" class="form-control">
                        </div>

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" id="saveExternalDepartment">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#saveRepresentative').click(function(e) {
                    e.preventDefault();

                    // Serialize the form data
                    var formData = $('#addRepresentativeForm').serialize();

                    // Submit AJAX request
                    $.ajax({
                        url: $('#addRepresentativeForm').attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            // Handle success response
                            console.log(response);

                            // Optionally, you can close the modal after successful save
                            $('#extern-departmen').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error(xhr.responseText);
                        }
                    });
                });
                $('#saveExternalDepartment').click(function(e) {
                    e.preventDefault();

                    // Serialize the form data
                    var formData = $('#saveExternalDepartment').serialize();

                    // Submit AJAX request
                    $.ajax({
                        url: $('#saveExternalDepartment').attr('action'),
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            // Handle success response
                            console.log(response);

                            // Optionally, you can close the modal after successful save
                            $('#exampleModal').modal('hide');
                        },
                        error: function(xhr, status, error) {
                            // Handle error response
                            console.error(xhr.responseText);
                        }
                    });
                });

                $('input[name=type]').click(function() {
                    if ($(this).is(':checked')) {
                        var value = $(this).val();
                        console.log(value);
                        if (value == 'intern') {
                            $('#department_id').show();
                            $('#extern-department-dev').hide();


                        } else {

                            $('#department_id').hide();
                            $('#extern-department-dev').show();
                        }

                    }
                });
            });
        </script>
    @endpush
@endsection
