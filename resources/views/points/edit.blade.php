@extends('layout.main')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/timepicker@1.13.18/jquery.timepicker.min.css">
@endpush
@section('title')
    القطاعات
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('points.index') }}"> نقاط الوزاره </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a> تعديل</a></li>
            </ol>
        </nav>
    </div>
    {{-- <div class="row ">
    <div class="container welcome col-11">
        <p> نقاط الوزاره </p>
    </div>
</div> --}}
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
                <h3 class="pt-3  px-md-5 px-3 "> اضافة نقطة </h3>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="name"> اسم النقطة </label>
                        <input type="text" id="name" class="form-control" name="name" value="{{ $data->name }}" dir="rtl"
                            placeholder=" اسم النقطه" required />
                        <span class="text-danger span-error" id="name-error"></span>

                    </div>
                </div>

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="sector_id"> اختر القطاع </label>
                        <select name="sector_id" id="sector_id" style="border: 0.2px solid rgb(199, 196, 196);" required>
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
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="governorate"> اختر المحافظة </label>
                        <select name="governorate" id="governorate" style="border: 0.2px solid rgb(199, 196, 196);"
                            required>
                            <option value="">محافظه </option>
                        </select>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="region"> اختر المنطقة </label>
                        <select name="region" id="region" style="border: 0.2px solid rgb(199, 196, 196);" required>
                            <option value="">منطقه </option>
                        </select>
                    </div>
                </div>
               <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="fromTime"> موعد البدايه </label>
                        <input type="time" id="fromTime"   value="{{ $data->to ? date('H:i', strtotime($data->to)) : '' }}"  name="from" class="form-control" required />
                    </div>
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="toTime"> موعد النهايه </label>
                        <input type="time" id="toTime" name="to"  value="{{ $data->to ? date('H:i', strtotime($data->to)) : '' }}"   class="form-control" required />
                    </div>
                </div>

                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <span class="text-danger span-error" id="error-message" style="font-weight: bold;"></span>
                    </div>
                </div>
                
                {{-- <div id="error-message" class="error"></div> --}}

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="map_link"> رابط جوجل ماب </label>
                        <input type="text" id="map_link" name="map_link" class="form-control" placeholder=" ادخل الرابط"  dir="rtl"
                            value="{{ $data->google_map }}" />
                    </div>
                </div>
                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="long"> خطوط الطول </label>
                        <input type="text" id="long" name="long" value="{{ $data->long }}"  dir="rtl"
                            class="form-control" placeholder="  خطوط الطول " />
                    </div>
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="lat"> خطوط العرض </label>
                        <input type="text" id="lat" name="Lat"  class="form-control" placeholder="  خطوط العرض "  value="{{ $data->lat }}" dir="rtl"/>
                    </div>
                </div>
                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="note"> اضف ملاحظتك </label>
                        <textarea type="text" id="note" name="note" class="form-control note" placeholder="ملاحظتك" dir="rtl">{{ $data->note }}</textarea>
                    </div>
                </div>
                <div class="container col-11 ">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">

                        <button type="submit" class="btn-blue"><img src="{{ asset('frontend/images/white-add.svg') }}" alt="img" height="20px" width="20px"> اضافة</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/timepicker@1.13.18/jquery.timepicker.min.js"></script> --}}
    {{-- <script>
        $(document).ready(function() {
            $('#toTime').timepicker({
                timeFormat: 'h:i A',
                ampm: true,
                ampmText: {
                    am: 'ص',
                    pm: 'م'
                },
                lang: {
                    am: 'ص',
                    pm: 'م'
                },
                minTime: '00:00', // Set the minimum time if needed
                maxTime: '23:59' // Set the maximum time if needed
            });

            // Initialize with the existing value if available
            if (isset($data - > to)) {
                $('#toTime').timepicker('setTime',
                    '{{ Carbon\Carbon::createFromFormat('H:i:s', $data->to)->format('h:i A') }}');
            }
        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fromTimeInput = document.getElementById('fromTime');
            const toTimeInput = document.getElementById('toTime');
            const errorMessage = document.getElementById('error-message');

            function validateTimes() {
                const fromTime = fromTimeInput.value;
                const toTime = toTimeInput.value;

                // Clear previous error message
                errorMessage.textContent = '';

                if (fromTime && toTime) {
                    if (toTime < fromTime) {
                        errorMessage.textContent = 'يجب ان يكون موعد نهايه بعد مده البدايه';
                        toTimeInput.setCustomValidity('يجب ان يكون موعد نهايه بعد مده البدايه');
                    } else {
                        toTimeInput.setCustomValidity('');
                    }
                }
            }

            fromTimeInput.addEventListener('input', validateTimes);
            toTimeInput.addEventListener('input', validateTimes);

            // Additional form validation before submission
            document.getElementById('Points-form').addEventListener('submit', function(event) {
                validateTimes();
                if (toTimeInput.validationMessage) {
                    event.preventDefault();
                }
            });
        });
    </script>
    <script>
     $(document).ready(function() {
    var currentGovernorateId = {{ $data->government_id }};
    var currentRegionId = {{ $data->region_id  ?? 'null' }};

    $('#sector_id').change(function() {
        var sectorId = $(this).val();
        if (sectorId) {
            $.ajax({
                url: '/get-governorates/' + sectorId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#governorate').empty().append('<option value="">اختر المحافظة</option>');
                    $.each(data, function(key, value) {
                        var selected = value.id == currentGovernorateId ? 'selected' : '';
                        $('#governorate').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                    });
                    $('#governorate').prop('disabled', false);

                    // Trigger change to load regions if currentGovernorateId is set
                    if (currentGovernorateId) {
                        $('#governorate').trigger('change');
                    }
                },
                error: function(xhr) {
                    console.error('AJAX request failed', xhr);
                }
            });
        } else {
            $('#governorate').empty().append('<option value="">اختر المحافظة</option>').prop('disabled', true);
            $('#region').empty().append('<option value="">اختر المنطقة</option>').prop('disabled', true);
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
                    $('#region').empty().append('<option value="">اختر المنطقة</option>');
                    $.each(data, function(key, value) {
                        var selected = value.id == currentRegionId ? 'selected' : '';
                        $('#region').append('<option value="' + value.id + '" ' + selected + '>' + value.name + '</option>');
                    });
                    $('#region').prop('disabled', false);
                },
                error: function(xhr) {
                    console.error('AJAX request failed', xhr);
                }
            });
        } else {
            $('#region').empty().append('<option value="">اختر المنطقة</option>').prop('disabled', true);
        }
    });

    // Trigger change event to prepopulate data
    $('#sector_id').trigger('change');
});

    </script>
@endpush
