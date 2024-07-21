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
                <form action="{{ route('iotelegram.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="vacation_type_id">نوع الاجازة:</label>


                        <select id="vacation_type_id" name="vacation_type_id" class="form-control" required>
                            <option value="">اختر النوع</option>
                            @foreach ($vacation_types as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="employee_id">اسم الموظف:</label>


                        <select id="employee_id" name="employee_id" class="form-control" required>
                            <option value="">اختر الموظف</option>
                            @foreach ($employees as $item)
                                <option value="{{ $item->id }}" @if ($id && $id == $item->id) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="mb-3">
                        <label for="date_from">تاريخ البداية:</label>
                        <input type="date" id="date_from" name="date_from" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_to">تاريخ النهاية:</label>
                        <input type="date" id="date_to" name="date_to" class="form-control">
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
                var today = new Date().toISOString().split('T')[0];
                $('#date_from').attr('min', today);
                $('#date_to').attr('min', today);

                $('#date_from').attr('value', today);
                $('#date_to').attr('value', today);
          

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
