@extends('layout.main')
@push('style')
    <style>
        /* Add a CSS class for visually indicating disabled options */
        .disabled-option {
            background-color: #f0f0f0;
            /* Light grey background for disabled options */
            color: #999;
            /* Grey text color */
        }

        .invalid-time {
            border: 1px solid red;
            /* Red border for invalid input */
        }
    </style>
@endpush
@section('title')
    النقاط
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('points.index') }}"> نقاط الوزاره </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a> أضافه</a></li>
            </ol>
        </nav>
    </div>
    <br>
    <div class="bg-white">
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

    </div>
    <form class="edit-grade-form" id="Points-form" action=" {{ route('points.store') }}" method="POST">
        @csrf
        <div class="row" dir="rtl">

            <div class="container moftsh col-11 mt-3 p-0 pb-3 ">
                <h3 class="pt-3  px-md-5 px-3 "> اضافة نقطة </h3>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="name"> اسم النقطة </label>
                        <input type="text" id="name" class="form-control" name="name" placeholder=" اسم النقطه"
                            required />
                        <span class="text-danger span-error" id="name-error"></span>

                    </div>
                </div>
                {{-- <div class="input-group moftsh  px-md-5 px-3 pt-3">
                    <label class="pb-3" for="governorate">أختر المحافظه الخاصه لمجوعه النقاط</label>
                    <select name="sector_id" id="sector_id"
                        class=" form-control custom-select custom-select-lg mb-3 select2 "
                        style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;">
                        <option value="" selected disabled>اختر</option>
                        @foreach (getsectores() as $sector)
                            <option value="{{ $sector->id }}">{{ $sector->name }} </option>
                        @endforeach

                    </select>
                </div> --}}

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="sector_id"> اختر القطاع </label>
                        <select name="sector_id" id="sector_id"
                            class=" form-control custom-select custom-select-lg mb-3 select2 "
                            style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;">
                            <option value="" selected disabled>اختر</option>
                            @foreach (getsectores() as $sector)
                                <option value="{{ $sector->id }}">{{ $sector->name }} </option>
                            @endforeach

                        </select>
                        <span class="text-danger span-error" id="sector_id-error"></span>

                    </div>
                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="governorate"> اختر المحافظة </label>
                        <select name="governorate" id="governorate"  class=" form-control custom-select custom-select-lg mb-3 select2 "
                            style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;"
                            required>
                            <option value="" disabled selected>اختر المحافظه </option>
                        </select>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="region"> اختر المنطقة </label>
                        <select name="region" id="region"  class=" form-control custom-select custom-select-lg mb-3 select2 "
                        style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;" required>
                            <option value="" disabled selected>اختر المنطقه </option>
                        </select>
                    </div>
                </div>
                {{-- <div id="error-message" class="error"></div> --}}

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="map_link"> رابط جوجل ماب </label>
                        <input type="text" id="map_link" name="map_link" class="form-control" placeholder=" ادخل الرابط"
                            required />
                    </div>
                </div>
                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="long"> خطوط الطول </label>
                        <input type="text" id="long" name="long" class="form-control"
                            placeholder="  خطوط الطول " />
                    </div>
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="lat"> خطوط العرض </label>
                        <input type="text" id="lat" name="Lat" class="form-control"
                            placeholder="  خطوط العرض " />
                    </div>
                </div>
                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="time_type"> اختر نظام العمل </label>
                        <select name="time_type" id="time_type" style="border: 0.2px solid rgb(199, 196, 196);" required>
                            <option value="0">نظام 24 ساعه</option>
                            <option value="1">نظام دوام جزئى </option>

                        </select>
                        <span class="text-danger span-error" id="time_type-error"></span>

                    </div>
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="days_num">عدد أيام العمل</label>
                        <input type="number" id="days_num" name="days_num" class="form-control" max="7"
                            min="1" required />
                    </div>
                </div>
                <!-- Container for dynamically added inputs -->
                <div id="dynamic-input-container">
                </div>
                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <span class="text-danger span-error" id="error-message" style="font-weight: bold;"></span>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="note"> اضف ملاحظتك </label>
                        <textarea type="text" id="note" name="note" class="form-control note" placeholder="ملاحظتك"></textarea>
                    </div>
                </div>
                <div class="container col-11 ">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">

                        <button type="submit" class="btn-blue">
                            <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img" height="20px"
                                width="20px">
                            اضافة</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $('.select2').select2({
            dir: "rtl"
        });
        document.getElementById('time_type').addEventListener('change', updateForm);
        document.getElementById('days_num').addEventListener('input', updateForm);

        function updateForm() {
            const timeType = document.getElementById('time_type').value;
            const daysNum = parseInt(document.getElementById('days_num').value, 10);
            const dynamicInputContainer = document.getElementById('dynamic-input-container');

            // Clear previous dynamic inputs
            dynamicInputContainer.innerHTML = '';

            if (isNaN(daysNum) || daysNum <= 0) {
                dynamicInputContainer.style.display = 'none';
                return;
            }

            dynamicInputContainer.style.display = 'block';

            // Create divs dynamically based on the selected value and the number entered
            for (let i = 0; i < daysNum; i++) {
                const mainDiv = document.createElement('div');
                mainDiv.className = 'form-row col-12 mb-2';

                const dayNameContainer = document.createElement('div');
                dayNameContainer.className = 'form-row col-12 mb-2';

                const inputGroup = document.createElement('div');
                inputGroup.className = 'input-group moftsh2 px-md-5 px-3 pt-3';
                inputGroup.id = `day_name-container_${i}`;

                // Create day_name select
                const label = document.createElement('label');
                label.className = 'pb-3';
                label.setAttribute('for', `day_name_${i}`);
                label.textContent = 'اختر اليوم';

                const select = document.createElement('select');
                select.name = 'day_name[]';
                select.id = `day_name_${i}`;
                select.style.border = '0.2px solid rgb(199, 196, 196)';
                select.required = true;

                const option = document.createElement('option');
                option.value = '';
                option.text = "اختر يوم";
                option.disabled = true;
                option.selected = true;
                select.appendChild(option);

                // Add options to select
                ['السبت', 'الأحد', 'الأثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعه'].forEach((day, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.text = day;
                    select.appendChild(option);
                });

                select.addEventListener('change', handleDayChange);

                const errorSpan = document.createElement('span');
                errorSpan.className = 'text-danger span-error';
                errorSpan.id = `day_name_${i}-error`;

                inputGroup.appendChild(label);
                inputGroup.appendChild(select);
                inputGroup.appendChild(errorSpan);

                dayNameContainer.appendChild(inputGroup);
                mainDiv.appendChild(dayNameContainer);

                // If timeType == 1, add fromTime and toTime inputs
                if (timeType == '1') {
                    const timeInputRow = document.createElement('div');
                    timeInputRow.className = 'form-row col-12 mx-2 mb-2';

                    const fromTimeGroup = document.createElement('div');
                    fromTimeGroup.className = 'input-group moftsh2 px-md-5 px-3 pt-3 col-6';

                    const fromTimeLabel = document.createElement('label');
                    fromTimeLabel.className = 'pb-3';
                    fromTimeLabel.setAttribute('for', `fromTime_${i}`);
                    fromTimeLabel.textContent = 'موعد البدايه';

                    const fromTimeInput = document.createElement('input');
                    fromTimeInput.type = 'text';
                    fromTimeInput.id = `fromTime_${i}`;
                    fromTimeInput.name = 'from[]';
                    fromTimeInput.className = 'form-control';
                    fromTimeInput.required = true;

                    fromTimeGroup.appendChild(fromTimeLabel);
                    fromTimeGroup.appendChild(fromTimeInput);

                    const toTimeGroup = document.createElement('div');
                    toTimeGroup.className = 'input-group moftsh2 px-md-5 px-3 pt-3 col-6';

                    const toTimeLabel = document.createElement('label');
                    toTimeLabel.className = 'pb-3';
                    toTimeLabel.setAttribute('for', `toTime_${i}`);
                    toTimeLabel.textContent = 'موعد النهايه';

                    const toTimeInput = document.createElement('input');
                    toTimeInput.type = 'text';
                    toTimeInput.id = `toTime_${i}`;
                    toTimeInput.name = 'to[]';
                    toTimeInput.className = 'form-control';
                    toTimeInput.required = true;

                    toTimeGroup.appendChild(toTimeLabel);
                    toTimeGroup.appendChild(toTimeInput);

                    timeInputRow.appendChild(fromTimeGroup);
                    timeInputRow.appendChild(toTimeGroup);

                    mainDiv.appendChild(timeInputRow);
                }

                dynamicInputContainer.appendChild(mainDiv);

                // Initialize Flatpickr after appending inputs
                if (timeType == '1') {
                    flatpickr(`#fromTime_${i}`, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "h:i K",
                        time_24hr: false,
                        minuteIncrement: 1
                    });

                    flatpickr(`#toTime_${i}`, {
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "h:i K",
                        time_24hr: false,
                        minuteIncrement: 1
                    });
                }
            }
        }

        function handleDayChange() {
            const allSelects = document.querySelectorAll('select[name="day_name[]"]');
            const selectedValues = Array.from(allSelects).map(select => select.value);

            allSelects.forEach(select => {
                Array.from(select.options).forEach(option => {
                    if (selectedValues.includes(option.value) && option.value !== select.value) {
                        option.disabled = true;
                        option.classList.add('disabled-option');
                    } else {
                        option.disabled = false;
                        option.classList.remove('disabled-option');
                    }
                });
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            const daysInput = document.getElementById('days_num');

            daysInput.addEventListener('input', function() {
                if (parseInt(this.value) > 7) {
                    this.value = 7; // Restrict the value to 7 if the user enters a larger number
                    alert("عفوا عدد الايام لا يمكن ان تكون اكثر من 7 ايام");
                }
                if (parseInt(this.value) < 1) {
                    this.value = 1; // Restrict the value to 7 if the user enters a larger number
                    alert("عفوا عدد الايام لا يمكن ان تكون اقل من يوم");
                }
            });
            // $('#sector_id').change(function() {
            //     var sectorId = $(this).val();
            //     if (sectorId) {
            //         $.ajax({
            //             url: '/get-governorates/' + sectorId,
            //             type: 'GET',
            //             dataType: 'json',
            //             success: function(data) {
            //                 $('#governorate').empty().append(
            //                     '<option value="" disabled selected>اختر المحافظه </option>'
            //                 );

            //                 // Check if data is an array
            //                 if (Array.isArray(data)) {
            //                     $.each(data, function(key, value) {
            //                         $('#governorate').append('<option value="' + value
            //                             .id + '">' + value.name + '</option>');
            //                     });
            //                     $('#governorate').prop('disabled', false);
            //                 } else {
            //                     // Handle case where data is not in expected format
            //                     console.error('Unexpected data format', data);
            //                     $('#governorate').prop('disabled', true);
            //                 }

            //                 $('#region').empty().append(
            //                         '<option value=""disabled selected>اختر المنطقه </option>')
            //                     .prop(
            //                         'disabled', true);
            //             },
            //             error: function(xhr) {
            //                 console.error('AJAX request failed', xhr);
            //             }
            //         });
            //     } else {
            //         $('#governorate').empty().append(
            //             '<option value=""  disabled selected>اختر المحافظه </option>').prop('disabled',
            //             true);
            //         $('#region').empty().append('<option value="" disabled selected>اختر المنطقه </option>')
            //             .prop('disabled', true);
            //     }
            // });

            // $('#sector_id').change(function() {
            //     var governorateId = $(this).val();
            //     if (governorateId) {
            //         $.ajax({
            //             url: '/get-regions/' + governorateId,
            //             type: 'GET',
            //             dataType: 'json',
            //             success: function(data) {
            //                 $('#region').empty().append(
            //                     '<option value=""  disabled selected>اختر المنطقه </option>'
            //                 );

            //                 // Check if data is an array
            //                 if (Array.isArray(data)) {
            //                     $.each(data, function(key, value) {
            //                         $('#region').append('<option value="' + value.id +
            //                             '">' + value.name + '</option>');
            //                     });
            //                     $('#region').prop('disabled', false);
            //                 } else {
            //                     // Handle case where data is not in expected format
            //                     console.error('Unexpected data format', data);
            //                     $('#region').prop('disabled', true);
            //                 }
            //             },
            //             error: function(xhr) {
            //                 console.error('AJAX request failed', xhr);
            //             }
            //         });
            //     } else {
            //         $('#region').empty().append('<option value="" disabled selected>اختر المنطقه </option>')
            //             .prop('disabled', true);
            //     }
            // });
        });



        $('#sector_id').change(function() {
            var sectorId = $(this).val();
            if (sectorId) {
                $.ajax({
                    url: '/get-governorates/' + sectorId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        $('#governorate').empty().append(
                                '<option value="" disabled selected>اختر المحافظه </option>'
                            );

                        // Check if data is an array
                        if (Array.isArray(data)) {
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    $('#governorate').append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                                $('#governorate').prop('disabled', false);
                            } else {
                                // Show a message if no data is available
                                $('#governorate').append(
                                    '<option disabled>عفوا لا يوجد محافظه لهذا القطاع   بعد</option>'
                                );
                                $('#governorate').prop('disabled', true);
                            }
                        } else {
                            $('#region').empty().append(
                                    '<option value=""disabled selected>اختر المنطقه </option>')
                                .prop(
                                    'disabled', true);
                                    $('#region').prop('disabled', false);
                        }

                        // Update Select2 component
                        $('#governorate').trigger('change');
                    },
                    error: function(xhr) {
                        // Handle AJAX errors
                        console.error('AJAX request failed', xhr);
                        $('#governorate').empty().append(
                            '<option disabled>حدث خطأ في تحميل النقاط</option>');
                            $('#governorate').prop('disabled', false);

                        // Update Select2 component
                        $('#governorate').trigger('change');
                    }
                });
            } else {
                $('#governorate').empty().append('<option value="" selected disabled>اختر</option>').prop(
                    'disabled', false);

                // Update Select2 component
                $('#governorate').trigger('change');
            }
        });
        $('#governorate').change(function() {
            var governorateId = $(this).val();
            if (governorateId) {
                $.ajax({
                    url: '/get-regions/' + governorateId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                        $('#region').empty().append(
                                '<option value="" disabled selected>اختر المنطقه </option>'
                            );

                        // Check if data is an array
                        if (Array.isArray(data)) {
                            if (data.length > 0) {
                                $.each(data, function(key, value) {
                                    $('#region').append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                                $('#region').prop('disabled', false);
                            } else {
                                // Show a message if no data is available
                                $('#region').append(
                                    '<option disabled>عفوا لا يوجد منطقه  لهذا المحافظه   بعد</option>'
                                );
                                $('#region').prop('disabled', true);
                            }
                        } else {
                            $('#region').empty().append(
                                    '<option value=""disabled selected>اختر المنطقه </option>')
                                .prop(
                                    'disabled', true);
                                    $('#region').prop('disabled', false);
                        }

                        // Update Select2 component
                        $('#region').trigger('change');
                    },
                    error: function(xhr) {
                        // Handle AJAX errors
                        console.error('AJAX request failed', xhr);
                        $('#region').empty().append(
                            '<option disabled>حدث خطأ في تحميل النقاط</option>');
                            $('#region').prop('disabled', false);

                        // Update Select2 component
                        $('#region').trigger('change');
                    }
                });
            } else {
                $('#region').empty().append('<option value="" selected disabled>اختر</option>').prop(
                    'disabled', false);

                // Update Select2 component
                $('#region').trigger('change');
            }
        });
    </script>
@endpush
