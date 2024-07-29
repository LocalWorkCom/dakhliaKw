@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                @if ($id)
                    <li class="breadcrumb-item "><a href="{{ route('user.employees', 1) }}">الموظفين</a></li>
                @endif
                <li class="breadcrumb-item"><a href="{{ route('vacations.list', $id) }}">الاجازات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة </a></li>
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
                <div class="container col-10 mt-5 mb-5 pb-5" style="border:0.5px solid #C7C7CC;">
                    @csrf
                    <div class="form-row mx-md-3 mt-4 d-flex justify-content-center">

                        <div class="form-group col-md-10" id="name_dev" hidden>

                            <label for="name">اسم الاجازة</label>
                            <input type="text" id="name" name="name" class="form-control">
                        </div>
                    </div>
                    <div class="form-row mx-md-3 mt-4 d-flex justify-content-center" dir="rtl">
                        <div class="form-group col-md-5 mx-md-2 ">
                            <label for="vacation_type_id" style=" display: flex; justify-content: flex-start;">نوع
                                الاجازة</label>
                            <select id="vacation_type_id" name="vacation_type_id" class="form-control" required>
                                <option value="">اختر النوع</option>
                                @foreach ($vacation_types as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="employee_id" style=" display: flex; justify-content: flex-start;">اسم الموظف</label>
                            <select id="employee_id" name="employee_id" class="form-control" required @if ($id) disabled
                                @endif>
                                <option value="">اختر الموظف</option>
                                @foreach ($employees as $item)
                                <option value="{{ $item->id }}" @if ($id && $id==$item->id) selected @endif>
                                    {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                      
                    </div>
                    {{-- <div class="form-row mx-md-3 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-2">
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
                        <div class="form-group col-md-5 mx-md-2 ">
                            <label for="vacation_type_id">نوع الاجازة</label>
                            <select id="vacation_type_id" name="vacation_type_id" class="form-control" required>
                                <option value="">اختر النوع</option>
                                @foreach ($vacation_types as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div> --}}

                    <div class="form-row mx-md-3 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-md-2">
                            <label for="date_to">تاريخ النهاية</label>
                            <input type="date" id="date_to" name="date_to" class="form-control">
                        </div>
                        <div class="form-group col-md-5 mx-2">
                            <label for="date_from">تاريخ البداية</label>
                            <input type="date" id="date_from" name="date_from" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row mx-md-2 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-10" id="reportImage-div" hidden>
                            <label for="reportImage">اضافة ملف</label>
                            <div id="reportImage">
                                <div class="file-input mb-3" dir="rtl">
                                    <input type="file" name="reportImage" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container col-10 mt-5 mb-5 ">
                    <div class="form-row col-10 " dir="ltr">
                        <button type="submit" class="btn-blue">حفظ</button>
                    </div>
                </div>

            </form>
        </div>
    </div>



    @push('scripts')
        <script>
            $(document).ready(function() {
                var id = "{{ $id }}";
                // Get today's date
                var today = new Date().toISOString().split('T')[0];
                $('#date_from').attr('min', today);
                $('#date_to').attr('min', today);

                $('#date_from').attr('value', today);
                $('#date_to').attr('value', today);


                $('#vacation_type_id').change(function() {
                    var value = $('#vacation_type_id option:selected').val();

                    if (value == '3') {
                        $('#name_dev').attr('hidden', false);
                        $('#reportImage-div').attr('hidden', true);

                        $('#date_to').prop('disabled', false);

                        $('#employee_id').prop('disabled', true);
                        $('#employee_id').removeAttr('required');
                        $('#name').attr('required', true);

                        $('#mySelect employee_id').prop('selected', false);


                    } else if (value == '4') {
                        $('#name_dev').attr('hidden', true);
                        $('#reportImage-div').attr('hidden', true);
                        $('#date_to').prop('disabled', true);
                        $('#date_to').attr('value', today);
                        $('#name').attr('required', false);

                        if (id == 0 || id == '') {

                            $('#employee_id').prop('disabled', false);
                            $('#employee_id').attr('required', true);
                        }

                    } else if (value == '2') {
                        $('#name_dev').attr('hidden', true);
                        $('#reportImage-div').attr('hidden', false);
                        $('#date_to').prop('disabled', false);
                        $('#name').attr('required', false);

                        if (id == 0 || id == '') {
                            $('#employee_id').prop('disabled', false);
                            $('#employee_id').attr('required', true);
                        }
                    } else {
                        $('#reportImage-div').attr('hidden', true);
                        $('#name_dev').attr('hidden', true);
                        $('#date_to').prop('disabled', false);
                        $('#name').attr('required', false);

                        if (id == 0 || id == '') {
                            $('#employee_id').prop('disabled', false);
                            $('#employee_id').attr('required', true);
                        }
                    }


                });
            });
        </script>
    @endpush
@endsection
