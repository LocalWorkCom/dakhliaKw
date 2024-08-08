@extends('layout.main')
@push('style')
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
                <li class="breadcrumb-item active" aria-current="page"> <a> أضافه</a></li>
            </ol>
        </nav>
    </div>
    {{-- <div class="row ">
    <div class="container welcome col-11">
        <p> نقاط الوزاره </p>
    </div>
</div> --}}
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

                <div class="form-row mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3">
                        <label class="pb-3" for="sector_id"> اختر القطاع </label>
                        <select name="sector_id" id="sector_id" style="border: 0.2px solid rgb(199, 196, 196);" required>
                            <option value="">قطاع </option>

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
                        <select name="governorate" id="governorate" style="border: 0.2px solid rgb(199, 196, 196);" required>
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
                        <input type="time" id="fromTime" name="from" class="form-control" required />
                    </div>
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="toTime"> موعد النهايه </label>
                        <input type="time" id="toTime" name="to" class="form-control" required />
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
                        <input type="text" id="map_link" name="map_link" class="form-control"
                            placeholder=" ادخل الرابط" />
                    </div>
                </div>
                <div class="form-row   mx-2 mb-2 ">
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="long"> خطوط الطول </label>
                        <input type="text" id="long" name="long" class="form-control" placeholder="  خطوط الطول " />
                    </div>
                    <div class="input-group moftsh2 px-md-5 px-3 pt-3 col-6">
                        <label class="pb-3" for="lat"> خطوط العرض </label>
                        <input type="text" id="lat" name="Lat" class="form-control" placeholder="  خطوط العرض " />
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
                            <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img" height="20px" width="20px"> اضافة</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            $('#sector_id').change(function() {
                var sectorId = $(this).val();
                if (sectorId) {
                    $.ajax({
                        url: '/get-governorates/' + sectorId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#governorate').empty().append('<option value="">نقطة</option>');

                            // Check if data is an array
                            if (Array.isArray(data)) {
                                $.each(data, function(key, value) {
                                    $('#governorate').append('<option value="' + value
                                        .id + '">' + value.name + '</option>');
                                });
                                $('#governorate').prop('disabled', false);
                            } else {
                                // Handle case where data is not in expected format
                                console.error('Unexpected data format', data);
                                $('#governorate').prop('disabled', true);
                            }

                            $('#region').empty().append('<option value="">نقطة</option>').prop(
                                'disabled', true);
                        },
                        error: function(xhr) {
                            console.error('AJAX request failed', xhr);
                        }
                    });
                } else {
                    $('#governorate').empty().append('<option value="">نقطة</option>').prop('disabled',
                        true);
                    $('#region').empty().append('<option value="">نقطة</option>').prop('disabled', true);
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
                            $('#region').empty().append('<option value="">نقطة</option>');

                            // Check if data is an array
                            if (Array.isArray(data)) {
                                $.each(data, function(key, value) {
                                    $('#region').append('<option value="' + value.id +
                                        '">' + value.name + '</option>');
                                });
                                $('#region').prop('disabled', false);
                            } else {
                                // Handle case where data is not in expected format
                                console.error('Unexpected data format', data);
                                $('#region').prop('disabled', true);
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX request failed', xhr);
                        }
                    });
                } else {
                    $('#region').empty().append('<option value="">نقطة</option>').prop('disabled', true);
                }
            });
        });
    </script>
@endpush
