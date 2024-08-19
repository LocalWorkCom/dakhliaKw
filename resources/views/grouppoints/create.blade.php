@extends('layout.main')
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@section('title')
    نقاط الوزاره
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('points.index') }}">نقاط الوزاره</a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> أنشاء مجموعه للنقاط</a></li>
            </ol>
        </nav>
    </div>
 <div class="row ">
        <div class="container welcome col-11">
            <p> نقـــاط الوزاره </p>
        </div>
    </div> 
    {{-- {{ dd($governments) }} --}}
    <br>
    <form class="edit-grade-form" id="Qta3-form" action=" {{ route('grouppoints.store') }}" method="POST">
        @csrf
        <div class="row" dir="rtl">
            <div id="first-container" class="container moftsh col-11 mt-3 p-0 pb-3">
                <div class="form-row mx-2 mb-2">
                    <h3 class="pt-3 px-md-5 px-3">اضف مجموعه</h3>
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                    <div class="input-group moftsh px-md-5 px-3 pt-3">
                        <label class="pb-3" for="name">ادخل اسم المجموعه</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="قطاع واحد"
                            required />
                        <span class="text-danger span-error" id="name-error"></span>

                    </div>
                </div>
                <div class="container col-11">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3">
                        <button type="button" id="next-button" class="btn-blue">التالى</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" dir="rtl">
            <div id="second-container" class="container moftsh col-11 mt-3 p-0 pb-3 hidden">
                <h3 class="pt-3 px-md-5 px-3">اضف محافظات داخل قطاع</h3>
                <div class="form-row mx-2">
                    <!-- <div class="form-group moftsh px-md-5 px-3 pt-3">
                        <h4 style="color: #274373; font-size: 24px;">حدد المحافظات المراد اضافتها</h4>
                    </div> -->

                    <div class="input-group moftsh  px-md-5 px-3 pt-3">
                        <label class="pb-3" for="governorate">أختر المحافظه الخاصه لمجوعه النقاط</label>
                        <select name="governorate" id="governorate"
                            class=" form-control custom-select custom-select-lg mb-3 select2 "
                            style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;">
                            <option value="" selected disabled>اختر</option>
                            @foreach (getgovernments() as $government)
                                <option value="{{ $government->id }}">{{ $government->name }}</option>
                            @endforeach

                        </select>
                    </div>
                </div>
                {{-- {{ dd($governments== ''? 't' :'f') }} --}}
                <div class="input-group moftsh px-md-5 px-3 pt-4">
                    <label class=" d-flex pb-3" for="pointsIDs" >أختر النقاط</label>
                    <select name="pointsIDs[]" id="pointsIDs" multiple class=" form-control custom-select custom-select-lg mb-3 select2 col-12"
                        style="border: 0.2px solid rgb(199, 196, 196); width:100% !important;">
                        <option value="" selected disabled>اختر</option>


                    </select>
                </div>
                <span class="text-danger span-error" id="governmentIDS-error"></span>
                <div class="container col-12 pt-4">
                    <div class="form-row d-flex justify-content-end mt-4 mb-3 mx-5">
                        <button type="submit" class="btn-blue mx-3">
                            <img src="{{ asset('frontend/images/white-add.svg') }}" alt="img" height="20px"
                                width="20px"> اضافة
                        </button>
                        <button type="button" id="back-button" class="btn-back ">
                            <img src="{{ asset('frontend/images/previous.svg') }}" alt="img" height="20px"
                                width="20px"> السابق</button>

                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $('.select2').select2({
            dir: "rtl"
        });

        // document.getElementById('Qta3-form').addEventListener('submit', function(event) {
        //     var checkboxes = document.querySelectorAll('input[name="pointsIDs[]"]');
        //     var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);

        //     if (!checkedOne) {
        //         event.preventDefault(); // Prevent form submission
        //         document.getElementById('governmentIDS-error').textContent =
        //             'من فضلك اختر محافظه واحده على الأقل .';
        //     } else {
        //         document.getElementById('governmentIDS-error').textContent = ''; // Clear any error messages
        //     }
        // });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var nameInput = document.getElementById('name');
            var nextButton = document.getElementById('next-button');
            var nameError = document.getElementById('name-error');

            nameInput.addEventListener('input', function() {
                if (nameInput.value.trim() !== '') {
                    nextButton.disabled = false;
                } else {
                    nextButton.disabled = true;
                }
            });

            nextButton.addEventListener('click', function() {
                if (nameInput.value.trim() === '') {
                    nameError.textContent = 'يرجى إدخال اسم القطاع';
                } else {
                    nameError.textContent = ''; // Clear any previous error message
                    document.getElementById('first-container').classList.add('hidden');
                    document.getElementById('second-container').classList.remove('hidden');
                }
            });

            document.getElementById('back-button').addEventListener('click', function() {
                document.getElementById('second-container').classList.add('hidden');
                document.getElementById('first-container').classList.remove('hidden');
            });
        });
        $(document).ready(function() {
            // Initialize Select2 for the pointsIDs select element
            // $('#pointsIDs').select2({
            //     placeholder: 'اختر',
            //     allowClear: true
            // });
        });
            $('#governorate').change(function() {
                var governorateId = $(this).val();
                if (governorateId) {
                    $.ajax({
                        url: '/get-points/' + governorateId,
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            
                            var pointsSelect = $('#pointsIDs');
                            pointsSelect.empty().append(
                                '<option value="" selected disabled>اختر</option>');

                            // Check if data is an array
                            if (Array.isArray(data)) {
                                if (data.length > 0) {
                                    $.each(data, function(key, value) {
                                        var optionHtml =
                                            '<option value="' + value.id + '">' + value
                                            .name + '</option>';
                                        pointsSelect.append(optionHtml);
                                    });
                                    pointsSelect.prop('disabled', false);
                                } else {
                                    // Show a message if no data is available
                                    pointsSelect.append(
                                        '<option disabled>عفوا لا يوجد نقاط لهذه المافظه بعد</option>'
                                        );
                                    pointsSelect.prop('disabled', false);
                                }
                            } else {
                                // Handle unexpected data format
                                console.error('Unexpected data format', data);
                                pointsSelect.append(
                                    '<option disabled>تعذر تحميل النقاط</option>');
                                pointsSelect.prop('disabled', false);
                            }

                            // Update Select2 component
                            pointsSelect.trigger('change');
                        },
                        error: function(xhr) {
                            // Handle AJAX errors
                            console.error('AJAX request failed', xhr);
                            pointsSelect.empty().append(
                                '<option disabled>حدث خطأ في تحميل النقاط</option>');
                            pointsSelect.prop('disabled', false);

                            // Update Select2 component
                            pointsSelect.trigger('change');
                        }
                    });
                } else {
                    $('#pointsIDs').empty().append('<option value="" selected disabled>اختر</option>').prop(
                        'disabled', false);

                    // Update Select2 component
                    $('#pointsIDs').trigger('change');
                }
            });
        
    </script>
@endpush
