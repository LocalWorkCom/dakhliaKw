@extends('layout.main')

@section('title')
    تعديل
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                @if ($id)
                    <li class="breadcrumb-item "><a href="{{ route('user.employees', 1) }}">الموظفين</a></li>
                @endif
                <li class="breadcrumb-item"><a
                        href="{{ route('vacations.list', $vacation->employee_id ? $vacation->employee_id : '') }}">الاجازات
                    </a></li>
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
                <div class="container col-10 mt-5 mb-5 pb-5" style="border:0.5px solid #C7C7CC;">
                    @csrf

                    <div class="form-row mx-md-3 mt-4 d-flex justify-content-center">

                        <div class="form-group col-md-3 mx-md-2" id="name_dev" hidden>


                            <label for="name">اسم الاجازة:</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="{{ $vacation->name }}">
                        </div>
                    </div>
                    <div class="form-row mx-3 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-2 "> <label for="vacation_type_id">نوع الاجازة</label>


                            <select id="vacation_type_id" name="vacation_type_id" class="form-control" required>
                                <option value="">اختر النوع</option>
                                @foreach ($vacation_types as $item)
                                    <option value="{{ $item->id }}" @if ($item->id == $vacation->vacation_type_id) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="employee_id">اسم الموظف</label>
                            <select id="employee_id" name="employee_id" class="form-control" disabled
                                @if ($vacation->vacation_type_id == 3) required @endif>
                                <option value="">اختر الموظف</option>
                                @foreach ($employees as $item)
                                    <option value="{{ $item->id }}" @if ($vacation->employee_id == $item->id) selected @endif>
                                        {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>



                    <div class="form-row mx-md-3 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-5 mx-md-md-2">
                            <label for="date_to">تاريخ النهاية</label>
                            <input type="date" id="date_to" name="date_to" class="form-control"
                                value="{{ $vacation->date_to ? $vacation->date_to : date('Y-m-d') }}">
                        </div>
                        <div class="form-group col-md-5 mx-md-2">
                            <label for="date_from">تاريخ البداية</label>
                            <input type="date" id="date_from" name="date_from" class="form-control" required
                                value="{{ $vacation->date_from ? $vacation->date_from : date('Y-m-d') }}">
                        </div>

                    </div>
                    <div class="form-row mx-md-2 mt-4 d-flex justify-content-center">
                        <div class="form-group col-md-10" id="reportImage-div" hidden>
                            <label for="reportImage">تعديل ملف</label>
                            <div id="reportImage">
                                <div class="file-input mb-3" dir="rtl">
                                    <input type="file" name="reportImage" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($vacation->report_image && $vacation->vacation_type_id == 2)
                        <div class="form-row mx-2 mt-4 d-flex justify-content-center">
                            <div class="form-group col-md-10 d-flex justify-content-end">
                                <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                    id="reportImage-div-1" data-image="{{ asset($vacation->report_image) }}"
                                    data-title="{{ $vacation->report_image }}">
                                    <img src="{{ asset($vacation->report_image) }}" class="img-thumbnail mx-2"
                                        alt="{{ $vacation->report_image }}"> <br> <br>
                                    {{-- <a id="downloadButton" href="{{ route('vacation.downlaodfile', ['id' => $vacation->id]) }}"
                                class="btn-download"><i class="fa fa-download" style="color:green;"></i>
                                تحميل الملف
                            </a> --}}

                                </a>

                            </div>

                        </div>
                    @endif
                </div>
                <div class="container  col-10 mt-5 mb-5 ">
                    <div class="form-row col-10 " dir="ltr">
                        <button type="submit" class="btn-blue">حفظ</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">عرض الصورة</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="#" class="img-fluid" alt="صورة">
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
            $(document).ready(function() {
                var today = new Date().toISOString().split('T')[0];

                $('#date_from').attr('min', today);
                $('#date_to').attr('min', today);

                var value = $('#vacation_type_id option:selected').val();
                if (value == '3') {
                    $('#name_dev').attr('hidden', false);
                    $('#reportImage-div').attr('hidden', true);
                    $('#reportImage-div-1').attr('hidden', true);


                    $('#date_to').prop('disabled', false);

                    $('#employee_id').prop('disabled', true);

                    $('#employee_id').removeAttr('required');

                } else if (value == '4') {
                    $('#name_dev').attr('hidden', true);
                    $('#reportImage-div').attr('hidden', true);
                    $('#reportImage-div-1').attr('hidden', true);


                    $('#date_to').prop('disabled', true);
                    $('#employee_id').prop('disabled', false);
                    $('#employee_id').attr('required', true);

                } else if (value == '2') {

                    $('#name_dev').attr('hidden', true);
                    $('#reportImage-div').attr('hidden', false);
                    $('#reportImage-div-1').attr('hidden', false);


                    $('#date_to').prop('disabled', false);

                    $('#employee_id').prop('disabled', false);
                    $('#employee_id').attr('required', true);
                } else {
                    $('#reportImage-div').attr('hidden', true);
                    $('#reportImage-div-1').attr('hidden', true);

                    $('#name_dev').attr('hidden', true);
                    $('#date_to').prop('disabled', false);

                    $('#employee_id').prop('disabled', false);
                    $('#employee_id').attr('required', true);
                }

                $('#vacation_type_id').change(function() {
                    var value = $('#vacation_type_id option:selected').val();

                    if (value == '3') {
                        $('#reportImage-div').attr('hidden', true);
                        $('#reportImage-div-1').attr('hidden', true);

                        $('#date_to').prop('disabled', false);
                        $('#name_dev').hide();

                        $('#employee_id').prop('disabled', true);

                        $('#employee_id').removeAttr('required');

                    } else if (value == '4') {
                        $('#reportImage-div').attr('hidden', true);
                        $('#reportImage-div-1').attr('hidden', true);


                        $('#name_dev').hide();

                        $('#date_to').prop('disabled', true);
                        $('#employee_id').prop('disabled', false);
                        $('#employee_id').attr('required', true);

                    } else if (value == '2') {
                        $('#reportImage-div').attr('hidden', false);
                        $('#reportImage-div-1').attr('hidden', false);


                        $('#date_to').prop('disabled', false);
                        $('#name_dev').show();

                        $('#employee_id').prop('disabled', false);
                        $('#employee_id').attr('required', true);
                    } else {
                        $('#reportImage-div').attr('hidden', true);
                        $('#reportImage-div-1').attr('hidden', true);


                        $('#date_to').prop('disabled', false);
                        $('#name_dev').hide();

                        $('#employee_id').prop('disabled', false);
                        $('#employee_id').attr('required', true);
                    }


                });
                $('.image-popup').click(function(event) {
                    event.preventDefault();
                    var imageUrl = $(this).data('image');
                    var imageTitle = $(this).data('title');

                    // Set modal image and title
                    $('#modalImage').attr('src', imageUrl);
                    $('#imageModalLabel').text(imageTitle);

                    // Show the modal
                    $('#imageModal').modal('show');
                });

            });
        </script>
    @endpush
@endsection
