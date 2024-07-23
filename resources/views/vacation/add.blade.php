@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vacations.list', $id) }}">الاجازات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافه </a></li>
            </ol>
        </nav>
    </div>
    @include('inc.flash')
    <div class="row">
        <div class="container welcome col-11">
            <p> الاجـــــــــازات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <form action="{{ route('vacation.store', $id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-2 mt-4">
                    <div class="form-group col-md-6 ">
                        <label for="vacation_type_id">نوع الاجازة</label>


                        <select id="vacation_type_id" name="vacation_type_id" class="form-control" required>
                            <option value="">اختر النوع</option>
                            @foreach ($vacation_types as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="employee_id">اسم الموظف</label>


                        <select id="employee_id" name="employee_id" class="form-control" required
                            @if ($id) disabled @endif>
                            <option value="">اختر الموظف</option>
                            @foreach ($employees as $item)
                                <option value="{{ $item->id }}" @if ($id && $id == $item->id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row mx-2 mt-4">

                    <div class="form-group col-md-6">
                        <label for="date_from">تاريخ البداية</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="date_to">تاريخ النهاية</label>
                        <input type="date" id="date_to" name="date_to" class="form-control">
                    </div>
                </div>

                <div class="form-row mx-2 mt-4">
                    <div class="form-group col-md-12">
                        <label for="reportImage">اضافة ملف</label>
                        <div id="reportImage">
                            <div class="file-input mb-3" dir="rtl">
                                <input type="file" name="reportImage" class="form-control-file">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container col-12 ">
                    <div class="form-row mt-4 mb-5">
                        <button type="submit" class="btn-blue">حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('scripts')
        <script>
            $(document).ready(function() {
                // Get today's date
                var today = new Date().toISOString().split('T')[0];
                $('#date_from').attr('min', today);
                $('#date_to').attr('min', today);

                $('#date_from').attr('value', today);
                $('#date_to').attr('value', today);


                $('#vacation_type_id').change(function() {
                    var value = $('#vacation_type_id option:selected').val();

                    if (value == '3') {
                        $('#reportImage').hide();
                        $('#date_to').prop('disabled', false);

                        $('#employee_id').prop('disabled', true);

                        $('#employee_id').removeAttr('required');

                    } else if (value == '4') {
                        $('#reportImage').hide();

                        $('#date_to').prop('disabled', true);
                        $('#employee_id').prop('disabled', false);
                        $('#employee_id').attr('required', true);

                    } else if (value == '2') {
                        $('#reportImage').show();
                        $('#date_to').prop('disabled', false);

                        $('#employee_id').prop('disabled', false);
                        $('#employee_id').attr('required', true);
                    } else {
                        $('#reportImage').hide();
                        $('#date_to').prop('disabled', false);

                        $('#employee_id').prop('disabled', false);
                        $('#employee_id').attr('required', true);
                    }


                });
            });
        </script>
    @endpush
@endsection
