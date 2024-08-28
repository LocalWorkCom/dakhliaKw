@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="row " dir="rtl">
        <div class="container  col-11" style="background-color:transparent;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="/">الرئيسية</a></li>
                    @if ($id)
                        <li class="breadcrumb-item "><a href="{{ route('user.employees', 1) }}">الموظفين</a></li>
                    @endif
                    <li class="breadcrumb-item"><a href="{{ route('vacations.list', $id) }}">الاجازات </a></li>
                    <li class="breadcrumb-item active" aria-current="page"> <a href=""> اضافة </a></li>
                </ol>
            </nav>
        </div>
    </div>
    @include('inc.flash')
    <div class="row">
        <div class="container welcome col-11">
            <p> الاجـــــــــازات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container col-11 mt-3 p-0">
            <form action="{{ route('vacation.store', $id) }}" method="POST" enctype="multipart/form-data">
                <div class="container col-10 mt-5 mb-5 pb-5" style="border:0.5px solid #C7C7CC;">
                    @csrf

                    <div class="form-row mx-md-3 mt-4 d-flex justify-content-center" dir="rtl">
                        <div class="form-group col-md-5 mx-md-2 ">
                            <label for="vacation_type_id" style=" display: flex; justify-content: flex-start;">نوع
                                الاجازة</label>
                            <select id="vacation_type_id" name="vacation_type_id" class="form-control  select2">
                                <option value="">اختر النوع</option>
                                @foreach ($vacation_types as $vacation_type)
                                    <option value="{{ $vacation_type->id }}">{{ $vacation_type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="employee_id" style=" display: flex; justify-content: flex-start;">اسم الموظف</label>
                            <select id="employee_id" name="employee_id" class="form-control  select2"
                                @if ($id) disabled @endif>
                                <option value="">اختر الموظف</option>
                                @foreach ($employees as $item)
                                    <option value="{{ $item->id }}" @if ($id && $id == $item->id) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>


                    <div class="form-row mx-md-3 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-md-2">
                            <label for="days_num">عدد الايام</label>
                            <input type="number" id="days_num" name="days_num" class="form-control">
                        </div>

                        <div class="form-group col-md-5 mx-2">
                            <label for="start_date">تاريخ البداية</label>
                            <input type="date" id="start_date" name="start_date" class="form-control"
                                oninput="this.type='text'; this.type='date';">
                        </div>



                    </div>
                    <div class="form row mx-md-3 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-10 mx-md-md-2  d-flex" dir="rtl">
                            <input type="checkbox" class="mx-2" name="check_country" id="toggleCheckbox">
                            <label for="toggleCheckbox"> دولة خارجية </label>
                        </div>
                    </div>

                    <div class="form row mx-md-3 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-10 mx-2" style="display: none" id="toggleDiv">
                            <label for="country_id" style="display: flex;">الدولة</label>
                            <select id="country_id" name="country_id" class="form-control">
                                <option value="">اختر الدولة</option>
                                @foreach ($countries as $item)
                                    <option value="{{ $item->id }}">{{ $item->country_name_ar }}</option>
                                @endforeach
                            </select>
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
            document.getElementById('start_date').addEventListener('keydown', function(event) {
                event.preventDefault();
            });
        </script>
        <script>
            $('.select2').select2({
                // dir: "rtl"
            });
        </script>
        <script>
            document.getElementById('toggleCheckbox').addEventListener('change', function() {
                var toggleDiv = document.getElementById('toggleDiv');
                if (this.checked) {
                    toggleDiv.style.display = 'block';
                } else {
                    toggleDiv.style.display = 'none';
                }
            });
        </script>
        {{-- <script>
            document.getElementById('toggleCheckbox').addEventListener('change', function () {
                var toggleDiv = document.getElementById('toggleDiv');
                var countrySelect = document.getElementById('country_id');
                if (this.checked) {
                    toggleDiv.style.display = 'block';
                    countrySelect.setAttribute('required', 'required');
                } else {
                    toggleDiv.style.display = 'none';
                    countrySelect.removeAttribute('required');
                }
            });
        </script> --}}
        <script>
            $(document).ready(function() {
                // Set minimum date for the start_date input to today's date

                var id = "{{ $id }}";
                // Get today's date
                var today = new Date().toISOString().split('T')[0];
                $('#start_date').attr('min', today);

                $('#start_date').attr('value', today);


                // $('#vacation_type_id').change(function() {
                //     var value = $('#vacation_type_id option:selected').val();

                //     if (value == '3') {
                //         $('#name_dev').attr('hidden', false);
                //         $('#reportImage-div').attr('hidden', true);

                //         $('#date_to').prop('disabled', false);

                //         $('#employee_id').prop('disabled', true);
                //         $('#employee_id').removeAttr('required');
                //         $('#name').attr('required', true);

                //         $('#mySelect employee_id').prop('selected', false);


                //     } else if (value == '4') {
                //         $('#name_dev').attr('hidden', true);
                //         $('#reportImage-div').attr('hidden', true);
                //         $('#date_to').prop('disabled', true);
                //         $('#date_to').attr('value', today);
                //         $('#name').attr('required', false);

                //         if (id == 0 || id == '') {

                //             $('#employee_id').prop('disabled', false);
                //             $('#employee_id').attr('required', true);
                //         }

                //     } else if (value == '2') {
                //         $('#name_dev').attr('hidden', true);
                //         $('#reportImage-div').attr('hidden', false);
                //         $('#date_to').prop('disabled', false);
                //         $('#name').attr('required', false);

                //         if (id == 0 || id == '') {
                //             $('#employee_id').prop('disabled', false);
                //             $('#employee_id').attr('required', true);
                //         }
                //     } else {
                //         $('#reportImage-div').attr('hidden', true);
                //         $('#name_dev').attr('hidden', true);
                //         $('#date_to').prop('disabled', false);
                //         $('#name').attr('required', false);

                //         if (id == 0 || id == '') {
                //             $('#employee_id').prop('disabled', false);
                //             $('#employee_id').attr('required', true);
                //         }
                //     }


                // });
                $('#check_country').click(function(e) {
                    e.preventDefault();
                    $("#country-dev").show();

                });
            });
        </script>
    @endpush
@endsection
