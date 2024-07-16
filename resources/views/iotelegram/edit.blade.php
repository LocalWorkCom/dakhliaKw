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
                            <input type="checkbox" id="intern" name="type"
                                @if ('intern' == $iotelegram->type) checked @endif>
                            <label for="checkbox">داخلي</label>
                        </div>
                        <div class="mb-3">
                            <input type="checkbox" id="extern" name="type"
                                @if ('extern' == $iotelegram->type) checked @endif>
                            <label for="checkbox">خارجي</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="department_id">الجهة المرسلة:</label>
                        <select id="department_id" name="department_id" class="form-control">
                            <option value="">اختر الجهة</option>
                            @foreach ($departments as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $iotelegram->department_id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal"
                            data-bs-target="#exampleModal">
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
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">إضافة مندوب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRepresentativeForm" action="{{ route('user.ajax') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="department_id">الادارة:</label>
                            <select id="department_id" name="department_id" class="form-control">
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                var value = $('input[name=type]').val();
                    if (value == 'intern') {

                        $('#department_id').show();
                        $('#extern-department-dev').hide();

                    } else {
                        $('#department_id').hide();
                        $('#extern-department-dev').show();

                    }
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
                            $('#exampleModal').modal('hide');
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
                    var value = $(this).val();
                    if (value == 'intern') {

                        $('#department_id').show();
                        $('#extern-department-dev').hide();

                    } else {
                        $('#department_id').hide();
                        $('#extern-department-dev').show();

                    }
                });
            });
        </script>
    @endpush
@endsection
