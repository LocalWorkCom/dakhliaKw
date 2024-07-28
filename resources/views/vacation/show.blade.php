@extends('layout.main')

@section('title')
    اضافة
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('vacations.list') }}">الاجازات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href="">عرض</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> الاجـــــــــازات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">

                </div>
            </div>



            <div class="form-row mx-2 ">
                <table class="table table-bordered" dir="rtl">
                    <tbody>
                        @if ($vacation->vacation_type_id == 3)
                            <tr>
                                <th scope="row"style="background: #f5f6fa;">اسم الاجازة:</th>
                                <td>{{ $vacation->name }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th scope="row"style="background: #f5f6fa;">نوع الاجازة:</th>
                            <td>{{ $vacation->vacation_type->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">اسم الموظف:</th>
                            <td>
                                {{ $vacation->employee ? $vacation->employee->name : 'عامة' }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">تاريخ البداية:</th>
                            <td>{{ $vacation->date_from }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;"> تاريخ النهاية:</th>
                            <td>{{ $vacation->date_to ? $vacation->date_to : $vacation->date_from }}</td>
                        </tr>
                        @if ($vacation->report_image && $vacation->vacation_type_id == 2)
                            <tr>
                                <th scope="row" style="background: #f5f6fa;"> الصور المرفقه </th>
                                <td>
                                    <div class="row">
                                        <div class="col-md-11 mb-3 px-5 mt-2">
                                            <a href="#" class="image-popup" data-toggle="modal"
                                                data-target="#imageModal" data-image="{{ asset($vacation->report_image) }}"
                                                data-title="{{ $vacation->report_image }}">
                                                <img src="{{ asset($vacation->report_image) }}" class="img-thumbnail mx-2"
                                                    alt="{{ $vacation->report_image }}"> <br> <br>
                                                <a id="downloadButton"
                                                    href="{{ route('vacation.downlaodfile', ['id' => $vacation->id]) }}"
                                                    class="btn-download"><i class="fa fa-download" style="color:green;"></i>
                                                    تحميل الملف
                                                </a>

                                            </a>

                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @endif



                    </tbody>
                </table>



            </div>
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
