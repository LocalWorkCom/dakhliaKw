@extends('layout.header')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('vacations.list') }}" class="btn btn-primary mt-3">رجوع</a>
        </div>
        @include('inc.flash')

        <div class="card">
            <div class="card-header">الاجازات</div>
            <div class="card-body">
                <form action="{{ route('vacation.update', $vacation->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="vacation_type_id">نوع الاجازة:</label>


                        <select id="vacation_type_id" name="vacation_type_id" class="form-control" required>
                            <option value="">اختر النوع</option>
                            @foreach ($vacation_types as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $vacation->vacation_type_id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
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


                    <div class="mb-3">
                        <label for="date_from">تاريخ البداية:</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" required
                            value="{{ $vacation->date_from }}">
                    </div>
                    <div class="mb-3">
                        <label for="date_to">تاريخ النهاية:</label>
                        <input type="date" id="date_to" name="date_to" class="form-control"
                            value="{{ $vacation->date_to }}">
                    </div>


                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
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
