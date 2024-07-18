@extends('layout.header')

@section('title')
    تعديل
@endsection
@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.list') }}" class="btn btn-primary mt-3">رجوع</a>
        </div>

        <div class="card">
            <div class="card-header">الواردات</div>
            <div class="card-body">
                <form action="{{ route('iotelegram.update', $iotelegram->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="date">التاريخ:</label>
                        <input type="date" id="date" name="date" class="form-control"
                            value="{{ $iotelegram->date }}">
                    </div>
                    <div class="row" style="justify-content: space-evenly;">
                        <div class="mb-3">
                            <input type="radio" id="intern" name="type" value="in"
                                @if ('in' == $iotelegram->type) checked @endif>
                            <label for="radio">داخلي</label>
                        </div>
                        <div class="mb-3">
                            <input type="radio" id="extern" name="type" value="out"
                                @if ('out' == $iotelegram->type) checked @endif>
                            <label for="radio">خارجي</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="from_departement">الجهة المرسلة:</label>


                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#extern-department" id="extern-department-dev" style="display: none">
                            <i class="fa fa-plus"></i>
                        </button>
                        <select id="from_departement" name="from_departement" class="form-control">
                            <option value="">اختر الجهة</option>
                            @if ($iotelegram->type == 'in')
                                @foreach ($departments as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $iotelegram->from_departement) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            @else
                                @foreach ($external_departments as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $iotelegram->from_departement) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">


                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#representative">
                            <i class="fa fa-plus"></i>
                        </button>
                        <label for="representive_id">اسم المندوب الجهة المرسلة :</label>
                        <select id="representive_id" name="representive_id" class="form-control">
                            <option value="">اختر المندوب</option>
                            @foreach ($representives as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $iotelegram->representive_id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="recieved_by">الموظف المستلم:</label>
                        <select id="recieved_by" name="recieved_by" class="form-control">
                            <option value="">اختر الموظف</option>
                            @foreach ($recieves as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $iotelegram->recieved_by) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="files_num"> عدد الكتب:</label>
                        <br>
                        <select id="files_num" name="files_num" class="form-control">
                            @for ($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" @if ($i == $iotelegram->files_num) selected @endif>
                                    {{ $i }}</option>
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
                var value = $('input[name=type]').val();
                console.log(value);
                if (value == 'in') {
                    $('#from_departement').show();
                    $('#extern-department-dev').hide();


                } else {

                    $('#extern-department-dev').show();
                }
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
                $("#saveExternalDepartment").on("submit", function(e) {

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
                            $('#from_departement').empty();
                            $.ajax({

                                url: "{{ route('external.departments') }}",
                                type: 'get',
                                success: function(response) {
                                    // Handle success response
                                    var selectOptions =
                                        '<option value="">اختر الادارة</option>';
                                    response.forEach(function(department) {
                                        selectOptions += '<option value="' +
                                            department.id +
                                            '">' + department.name +
                                            '</option>';
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
                            // Optionally, you can close the modal after successful save
                            $('#extern-department').modal('hide'); // Changed modal ID
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
                            $('#from_departement').show();
                            $('#extern-department-dev').hide();


                        } else {

                            $('#extern-department-dev').show();
                            $('#from_departement').empty();
                            $.ajax({

                                url: "{{ route('external.departments') }}",
                                type: 'get',
                                success: function(response) {
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
            });
        </script>
    @endpush
@endsection
