@extends('layout.main')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker@1.13.18/jquery.timepicker.min.css">
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
    نقاط الوزاره / تعديل
@endsection
@section('content')
<div class="row " dir="rtl">
<div class="container  col-11" style="background-color:transparent;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('points.index') }}"> نقاط الوزاره </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a> تعديل</a></li>
            </ol>
        </nav>
    </div>
    </div>
    <br>
    <form class="edit-grade-form" id="Points-form" action=" {{ route('points.update') }}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $data->id }}">
        <div class="row" dir="rtl">
            <div class="bg-white">
                @if (session()->has('message'))
                    <div class="alert alert-info">
                        {{ session('message') }}
                    </div>
                @endif
            </div>
            <div class="container moftsh col-11 mt-3 p-0 pb-3 ">
                <h3 class="pt-3  px-md-4 px-3 "> اضافة نقطة </h3>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3">
                        <label class="pb-3" for="name"> اسم النقطة </label>
                        <input type="text" id="name" class="form-control" name="name" value="{{ $data->name }}"
                            dir="rtl" placeholder=" اسم النقطه" required />
                        <span class="text-danger span-error" id="name-error"></span>

                    </div>
                </div>

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3">
                        <label class="pb-3" for="sector_id"> اختر القطاع </label>
                        <select name="sector_id" id="sector_id"
                            class=" form-control custom-select custom-select-lg mb-3 select2 "
                            style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;" required>
                            <option value="">قطاع </option>

                            @foreach (getsectores() as $sector)
                                <option value="{{ $sector->id }}" @if ($data->sector_id == $sector->id) selected @endif>
                                    {{ $sector->name }} </option>
                            @endforeach
                        </select>
                        <span class="text-danger span-error" id="sector_id-error"></span>

                    </div>
                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3">
                        <label class="pb-3" for="governorate"> اختر المحافظة </label>
                        <select name="governorate" id="governorate"  class=" form-control custom-select custom-select-lg mb-3 select2 "
                        style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;" 
                            required>
                            <option value="">محافظه </option>
                        </select>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3">
                        <label class="pb-3" for="region"> اختر المنطقة </label>
                        <select name="region" id="region"  class=" form-control custom-select custom-select-lg mb-3 select2 "
                        style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;"  required>
                            <option value="">منطقه </option>
                        </select>
                    </div>
                </div>
                {{-- <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                        <label class="pb-3" for="fromTime"> موعد البدايه </label>
                        <input type="text" id="fromTime"   value="{{ $data->to ? date('H:i', strtotime($data->to)) : '' }}"  name="from" class="form-control" required />
                    </div>
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                        <label class="pb-3" for="toTime"> موعد النهايه </label>
                        <input type="text" id="toTime" name="to"  value="{{ $data->to ? date('H:i', strtotime($data->to)) : '' }}"   class="form-control" required />
                    </div>
                </div>

                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                        <span class="text-danger span-error" id="error-message" style="font-weight: bold;"></span>
                    </div>
                </div>
                 --}}
                {{-- <div id="error-message" class="error"></div> --}}

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3">
                        <label class="pb-3" for="map_link"> رابط جوجل ماب </label>
                        <input type="text" id="map_link" name="map_link" class="form-control" placeholder=" ادخل الرابط"
                            dir="rtl" value="{{ $data->google_map }}" required />
                    </div>
                </div>
                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                        <label class="pb-3" for="long"> خطوط الطول </label>
                        <input type="text" id="long" name="long" value="{{ $data->long }}" dir="rtl"
                            class="form-control" placeholder="  خطوط الطول " />
                    </div>
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                        <label class="pb-3" for="lat"> خطوط العرض </label>
                        <input type="text" id="lat" name="Lat" class="form-control" placeholder="  خطوط العرض "
                            value="{{ $data->lat }}" dir="rtl" />
                    </div>
                </div>

                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                        <label class="pb-3" for="time_type"> اختر نظام العمل </label>
                        <select name="time_type" id="time_type" class=" form-control custom-select custom-select-lg mb-3 select2 "
                        style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;"  required>
                            <option value="0" @if ($data->work_type == 0) selected @endif>نظام 24 ساعه</option>
                            <option value="1" @if ($data->work_type == 1) selected @endif>نظام دوام جزئى
                            </option>

                        </select>
                        <span class="text-danger span-error" id="time_type-error"></span>

                    </div>

                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                        <label class="pb-3" for="days_num">عدد أيام العمل</label>
                        <input type="number" id="days_num" name="days_num"
                            value="{{ trim($data->work_type == 0 ? ($data->days_work ? count($data->days_work) : 0) : count($days)) }}"
                            class="form-control" max="7" min="1" required aria-label="Number of days"
                            aria-required="true" />

                    </div>
                </div>
                <!-- Container for dynamically added inputs -->

                <div id="dynamic-input-container">

                    @if ($data->work_type == 0 && $data->days_work)
                        @if (count($data->days_work) != 0)
                            @foreach ($data->days_work as $day)
                                <div class="form-row mx-2 mb-2 ">
                                    <div class="input-group moftsh2 px-md-4 px-3 pt-3" id="day_name-container">
                                        <label class="pb-3" for="day_name"> اختر اليوم </label>
                                        <select name="day_name[]" id="day_name"
                                            style="border: 0.2px solid rgb(199, 196, 196);" required>
                                            <option value="0" @if ($day == 0) selected @endif> السبت
                                            </option>
                                            <option value="1" @if ($day == 1) selected @endif> الأحد
                                            </option>
                                            <option value="2" @if ($day == 2) selected @endif>
                                                الأثنين </option>
                                            <option value="3" @if ($day == 3) selected @endif>
                                                الثلاثاء </option>
                                            <option value="4" @if ($day == 4) selected @endif>
                                                الأربعاء </option>
                                            <option value="5" @if ($day == 5) selected @endif>
                                                الخميس </option>
                                            <option value="6" @if ($day == 6) selected @endif>
                                                الجمعه </option>
                                        </select>
                                        <span class="text-danger span-error" id="day_name-error"></span>

                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endif
                    @if ($data->work_type == 1)
                        @if ($days)
                            @foreach ($days as $day)
                                <div class="form-row mx-2 mb-2 ">
                                    <div class="input-group moftsh2 px-md-4 px-3 pt-3" id="day_name-container">
                                        <label class="pb-3" for="day_name"> اختر اليوم </label>
                                        <select name="day_name[]" id="day_name"
                                            style="border: 0.2px solid rgb(199, 196, 196);" required>
                                            <option value="0" @if ($day->name == 0) selected @endif>
                                                السبت
                                            </option>
                                            <option value="1" @if ($day->name == 1) selected @endif>
                                                الأحد
                                            </option>
                                            <option value="2" @if ($day->name == 2) selected @endif>
                                                الأثنين </option>
                                            <option value="3" @if ($day->name == 3) selected @endif>
                                                الثلاثاء </option>
                                            <option value="4" @if ($day->name == 4) selected @endif>
                                                الأربعاء </option>
                                            <option value="5" @if ($day->name == 5) selected @endif>
                                                الخميس </option>
                                            <option value="6" @if ($day->name == 6) selected @endif>
                                                الجمعه </option>
                                        </select>
                                        <span class="text-danger span-error" id="day_name-error"></span>

                                    </div>
                                </div>
                                <div class="form-row   mx-2 mb-2 ">
                                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                                        <label class="pb-3" for="fromTime"> موعد البدايه </label>
                                        <input type="time" id="fromTime"
                                            value="{{ $day->from ? date('H:i', strtotime($day->from)) : '' }}"
                                            name="from[]" class="form-control" required />
                                    </div>
                                    <div class="input-group moftsh2 px-md-4 px-3 pt-3 col-6">
                                        <label class="pb-3" for="toTime"> موعد النهايه </label>
                                        <input type="time" id="toTime"
                                            value="{{ $day->to ? date('H:i', strtotime($day->to)) : '' }}" name="to[]"
                                            class="form-control" required />
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endif

                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-4 px-3 pt-3">
                        <label class="pb-3" for="note"> اضف ملاحظتك </label>
                        <textarea type="text" id="note" name="note" class="form-control note" placeholder="ملاحظتك"
                            dir="rtl">{{ $data->note }}</textarea>
                    </div>
                </div>
                <div class="container col-11 ">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">

                        <button type="submit" class="btn-blue"><img src="{{ asset('frontend/images/white-add.svg') }}"
                                alt="img" height="20px" width="20px"> اضافة</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            let oldValue = document.getElementById('time_type').value;
            let savedHTML = ''; // Store previous state

            const timeTypeSelect = document.getElementById('time_type');
            const daysNumInput = document.getElementById('days_num');
            const dynamicInputContainer = document.getElementById('dynamic-input-container');

            timeTypeSelect.addEventListener('change', handleSelectChange);
            daysNumInput.addEventListener('input', handleDaysInput);

            function handleSelectChange() {
                const timeType = timeTypeSelect.value;

                if (timeType === oldValue && savedHTML) {
                    dynamicInputContainer.innerHTML = savedHTML;
                } else {
                    savedHTML = dynamicInputContainer.innerHTML;
                    updateForm();
                }
                oldValue = timeType;
            }

            function handleDaysInput() {
                updateForm();
            }

            function updateForm() {
                const timeType = timeTypeSelect.value;
                const daysNum = parseInt(daysNumInput.value, 10);

                dynamicInputContainer.innerHTML = ''; // Clear the container

                if (isNaN(daysNum) || daysNum <= 0) {
                    dynamicInputContainer.style.display = 'none';
                    return;
                }

                dynamicInputContainer.style.display = 'block';

                if (timeType === '0') {
                    createSelectDays(daysNum);
                } else if (timeType === '1') {
                    createSelectDaysWithTime(daysNum);
                }

                savedHTML = dynamicInputContainer.innerHTML; // Save the current state
            }

            function createSelectDays(daysNum) {
                const timeType = timeTypeSelect.value;
                for (let i = 0; i < daysNum; i++) {
                    const mainDiv = document.createElement('div');
                    mainDiv.className = 'form-row col-md-12 px-md-4 mb-2';

                    const inputGroup = document.createElement('div');
                    inputGroup.className = 'input-group moftsh2 px-md-3 px-3 pt-3';
                    inputGroup.id = `day_name-container_${i}`;

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
                    option.text = "اختر يوم ";
                    option.disabled = true;
                    option.selected = true;
                    select.appendChild(option);

                    ['السبت', 'الأحد', 'الأثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعه'].forEach((day,
                        index) => {
                        const option = document.createElement('option');
                        option.value = index;
                        option.text = day;
                        select.appendChild(option);
                    });

                    const errorSpan = document.createElement('span');
                    errorSpan.className = 'text-danger span-error';
                    errorSpan.id = `day_name_${i}-error`;

                    inputGroup.appendChild(label);
                    inputGroup.appendChild(select);
                    inputGroup.appendChild(errorSpan);

                    mainDiv.appendChild(inputGroup);

                    dynamicInputContainer.appendChild(mainDiv);
                }

                // Add event listeners to the new selects
                addDayChangeListeners();
            }

            function createSelectDaysWithTime(daysNum) {
                const timeType = timeTypeSelect.value;
                for (let i = 0; i < daysNum; i++) {
                    const mainDiv = document.createElement('div');
                    mainDiv.className = 'form-row col-md-12 px-md-4 mb-2';

                    const inputGroup = document.createElement('div');
                    inputGroup.className = 'input-group moftsh2 px-md-3 px-3 pt-3';
                    inputGroup.id = `day_name-container_${i}`;

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
                    option.text = "اختر يوم ";
                    option.disabled = true;
                    option.selected = true;
                    select.appendChild(option);

                    ['السبت', 'الأحد', 'الأثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعه'].forEach((day,
                        index) => {
                        const option = document.createElement('option');
                        option.value = index;
                        option.text = day;
                        select.appendChild(option);
                    });

                    const errorSpan = document.createElement('span');
                    errorSpan.className = 'text-danger span-error';
                    errorSpan.id = `day_name_${i}-error`;

                    inputGroup.appendChild(label);
                    inputGroup.appendChild(select);
                    inputGroup.appendChild(errorSpan);

                    mainDiv.appendChild(inputGroup);

                    const timeInputRow = document.createElement('div');
                    timeInputRow.className = 'form-row col-md-12 mx-2 mb-2';

                    const fromTimeGroup = document.createElement('div');
                    fromTimeGroup.className = 'input-group moftsh2 px-md-3 px-3 pt-3 col-6';

                    const fromTimeLabel = document.createElement('label');
                    fromTimeLabel.className = 'pb-3';
                    fromTimeLabel.setAttribute('for', `fromTime_${i}`);
                    fromTimeLabel.textContent = 'موعد البدايه';

                    const fromTimeInput = document.createElement('input');
                    fromTimeInput.type = 'time';
                    fromTimeInput.id = `fromTime_${i}`;
                    fromTimeInput.name = 'from[]';
                    fromTimeInput.className = 'form-control';
                    fromTimeInput.required = true;

                    fromTimeGroup.appendChild(fromTimeLabel);
                    fromTimeGroup.appendChild(fromTimeInput);

                    const toTimeGroup = document.createElement('div');
                    toTimeGroup.className = 'input-group moftsh2 px-md-3 px-3 pt-3 col-6';

                    const toTimeLabel = document.createElement('label');
                    toTimeLabel.className = 'pb-3';
                    toTimeLabel.setAttribute('for', `toTime_${i}`);
                    toTimeLabel.textContent = 'موعد النهايه';

                    const toTimeInput = document.createElement('input');
                    toTimeInput.type = 'time';
                    toTimeInput.id = `toTime_${i}`;
                    toTimeInput.name = 'to[]';
                    toTimeInput.className = 'form-control';
                    toTimeInput.required = true;

                    toTimeGroup.appendChild(toTimeLabel);
                    toTimeGroup.appendChild(toTimeInput);

                    timeInputRow.appendChild(fromTimeGroup);
                    timeInputRow.appendChild(toTimeGroup);

                    mainDiv.appendChild(timeInputRow);

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

                // Add event listeners to the new selects
                addDayChangeListeners();
            }

            function addDayChangeListeners() {
                const allSelects = document.querySelectorAll('select[name="day_name[]"]');
                allSelects.forEach(select => {
                    select.addEventListener('change', handleDayChange);
                });

                handleDayChange(); // Initial call to update options
            }

            function handleDayChange() {
                const allSelects = document.querySelectorAll('select[name="day_name[]"]');
                const selectedValues = Array.from(allSelects).map(select => select.value);

                allSelects.forEach(select => {
                    Array.from(select.options).forEach(option => {
                        if (selectedValues.includes(option.value) && option.value !== select
                            .value) {
                            option.disabled = true;
                            option.classList.add('disabled-option');
                        } else {
                            option.disabled = false;
                            option.classList.remove('disabled-option');
                        }
                    });
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            var currentGovernorateId = {{ $data->government_id }};
            var currentRegionId = {{ $data->region_id ?? 'null' }};

            $('#sector_id').change(function() {
                var sectorId = $(this).val();
                if (sectorId) {
                    $.ajax({
                        url: '/get-governorates/' + sectorId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#governorate').empty().append(
                                '<option value="" disabled >اختر المحافظه </option>'
                            );
                            if (Array.isArray(data)) {
                                if (data.length > 0) {
                                    $.each(data, function(key, value) {
                                        var selected = value.id ==
                                            currentGovernorateId ?
                                            'selected' : '';
                                        $('#governorate').append('<option value="' +
                                            value.id +
                                            '" ' + selected + '>' + value.name +
                                            '</option>'
                                        );
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
                                        '<option value=""disabled selected>اختر المنطقه </option>'
                                        )
                                    .prop(
                                        'disabled', true);
                                $('#region').prop('disabled', false);
                            }

                            // Update Select2 component
                            $('#governorate').trigger('change');
                        },
                        error: function(xhr) {
                            console.error('AJAX request failed', xhr);
                        }
                    });
                } else {
                    $('#governorate').empty().append('<option value="">اختر المحافظة</option>').prop(
                        'disabled', true);
                    $('#region').empty().append('<option value="">اختر المنطقة</option>').prop('disabled',
                        true);
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
                            $('#region').empty().append(
                                '<option value="">اختر المنطقة</option>');
                            $.each(data, function(key, value) {
                                var selected = value.id == currentRegionId ?
                                    'selected' : '';
                                $('#region').append('<option value="' + value.id +
                                    '" ' + selected + '>' + value.name + '</option>'
                                );
                            });
                            $('#region').prop('disabled', false);
                        },
                        error: function(xhr) {
                            console.error('AJAX request failed', xhr);
                        }
                    });
                } else {
                    $('#region').empty().append('<option value="">اختر المنطقة</option>').prop('disabled',
                        true);
                }
            });

            // Trigger change event to prepopulate data
            $('#sector_id').trigger('change');
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
                            '<option disabled>حدث خطأ في تحميل البيانات</option>');
                            $('#governorate').prop('disabled', false);

                        // Update Select2 component
                        $('#governorate').trigger('change');
                    }
                });
            } else {
                $('#governorate').empty().append('<option value="" selected disabled>اختر المحافظه </option>').prop(
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
                                    '<option disabled>عفوا لا يوجد منطقه  لهذه المحافظه   بعد</option>'
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
                            '<option disabled>حدث خطأ في تحميل البيانات</option>');
                            $('#region').prop('disabled', false);

                        // Update Select2 component
                        $('#region').trigger('change');
                    }
                });
            } else {
                $('#region').empty().append('<option value="" selected disabled>اختر المنطقه </option>').prop(
                    'disabled', false);

                // Update Select2 component
                $('#region').trigger('change');
            }
        });
    </script>
@endpush
