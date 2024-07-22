@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vacations.list') }}">الاجازات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> تعديل </a></li>
            </ol>
        </nav>
    </div>
    @include('inc.flash')
    <div class="row ">
        <div class="container welcome col-11">
            <p> الاجـــــــــازات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <form action="{{ route('vacation.update', $vacation->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row mx-2 mt-4">
                    <div class="form-group col-md-6 "> <label for="vacation_type_id">نوع الاجازة:</label>


                        <select id="vacation_type_id" name="vacation_type_id" class="form-control" required>
                            <option value="">اختر النوع</option>
                            @foreach ($vacation_types as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $vacation->vacation_type_id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="employee_id">اسم الموظف:</label>
                        <select id="employee_id" name="employee_id" class="form-control" required
                            @if ($vacation->vacation_type_id == '3') disabled @endif>
                            <option value="">اختر الموظف</option>
                            @foreach ($employees as $item)
                                <option value="{{ $item->id }}" @if ($vacation->id == $item->id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>



                <div class="form-row mx-2 mt-4">

                    <div class="form-group col-md-6">
                        <label for="date_from">تاريخ البداية:</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" required
                            value="{{ $vacation->date_from }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="date_to">تاريخ النهاية:</label>
                        <input type="date" id="date_to" name="date_to" class="form-control"
                            value="{{ $vacation->date_to }}">
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

                $('#vacation_type_id').change(function() {
                    var value = $('#vacation_type_id option:selected').val();

                    if (value == '3') {
                        console.log("kjhjgf");
                        $('#employee_id').prop('disabled', true);

                        $('#employee_id').removeAttr('required');

                    } else {
                        $('#employee_id').prop('disabled', false);
                        $('#employee_id').attr('required', true);
                    }


                });
            });
        </script>
    @endpush
@endsection
