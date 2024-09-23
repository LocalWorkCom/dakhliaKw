@extends('layout.main')
@section('title')
    عرض
@endsection
@section('content')
    <div class="row " dir="rtl">
        <div class="container  col-11" style="background-color:transparent;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('viollation') }}">سجل المخالفات </a></li>
                    <li class="breadcrumb-item active" aria-current="page"> <a href=""> تفاصيل </a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> {{ $title }}</p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3 p-0 ">
            <div class="row " dir="rtl">

            </div>

            <div class="form-row mx-2 pt-5 pb-4">
                @if ($type == 0)
                    <table class="table table-bordered" dir="rtl">

                        <tbody>
                            <tr>
                                <th>النقطة :</th>
                                <td>{{ $data->point->name }}</td>
                            </tr>
                            <tr>
                                <th scope="row">العددا لكلى</th>
                                <td>{{ $data->total_number }}</td>
                            </tr>

                            <tr>
                                <th scope="row">العدد الفعلى</th>
                                <td>{{ $data->actual_number }}</td>
                            </tr>
                            <tr>
                                <th scope="row"> العجز</th>
                                <td>{{ $data->total_number - $data->actual_number }}</td>
                            </tr>
                        </tbody>
                    </table>
                    @foreach ($details as $detail)
                        <table class="table table-bordered" dir="rtl">
                            <thead>
                                <tr>
                                    <th colspan="2">سجل العجز</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>الأسم :</th>
                                    <td>{{ $detail->grade_id ? $detail->grade->name . '/' : ' ' }}{{ $detail->name }}</td>
                                </tr>
                                @if ($detail->military_number)
                                    <tr>
                                        <th scope="row"> الرقم العسكرى</th>
                                        <td>{{ $detail->military_number }}</td>
                                    </tr>
                                @endif
                                @if ($detail->civil_number)
                                    <tr>
                                        <th scope="row"> الرقم المدني</th>
                                        <td>{{ $detail->civil_number }}</td>
                                    </tr>
                                @endif

                                <tr>
                                    <th scope="row"> نوع الغياب</th>
                                    <td>{{ $detail->absenceType->name }}</td>
                                </tr>
                            </tbody>
                        </table>
                    @endforeach
                    @elseif ($type == 2)
                    <table class="table table-bordered" dir="rtl">
                        <tbody>
                            <tr>
                                <th>رقم القيد :</th>
                                <td>{{ $data->civil_number ? $data->civil_number : ' ' }}</td>
                            </tr>
                            <tr>
                                <th>رقم الأحوال :</th>
                                <td>{{ $data->registration_number ? $data->registration_number : ' ' }}</td>
                            </tr>

                            @if ($data->images)
                                <tr>
                                    <th scope="row" style="background: #f5f6fa;"> الصور المرفقه </th>
                                    <td>
                                        @php
                                            $images = explode(',', $data->image);
                                        @endphp
                                        @foreach ($images as $file)
                                            <div class="pb-4 mx-2">

                                                <a href="#" class="image-popup" data-toggle="modal"
                                                    data-target="#imageModal" data-image="{{ $file }}"
                                                    data-title="{{ $file }}">
                                                    <img src="{{ $file }}" class="img-thumbnail mx-2"
                                                        alt="{{ $file }}">
                                                    <br> <br>
                                                    {{-- @if (Auth::user()->hasPermission('download Io_file')) --}}
                                                    {{-- <a id="downloadButton"
                                                            href="{{ route('iotelegram.downlaodfile', ['id' => $file->id]) }}"
                                                            class="btn-download"><i class="fa fa-download" style="color:green;"></i>
                                                                </a> --}}
                                                    {{-- @endif --}}
                                                </a>
                                            </div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @else
                    <table class="table table-bordered" dir="rtl">
                        <tbody>
                            <tr>
                                <th>الأسم :</th>
                                <td>{{ $data->grade_id ? $detail->grade->name . '/' : ' ' }}{{ $data->name }}</td>
                            </tr>
                            @if ($data->military_number)
                                <tr>
                                    <th scope="row"> الرقم العسكرى</th>
                                    <td>{{ $data->military_number }}</td>
                                </tr>
                            @endif
                            @if ($data->civil_number)
                                <tr>
                                    <th scope="row"> الرقم المدني</th>
                                    <td>{{ $data->civil_number }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th scope="row"> المخالفات</th>
                                <td>{{ $data->ViolationType }}</td>
                            </tr>
                            @if ($data->image)
                                <tr>
                                    <th scope="row" style="background: #f5f6fa;"> الصور المرفقه </th>
                                    <td>
                                        @php
                                            $images = explode(',', $data->image);
                                        @endphp
                                        @foreach ($images as $file)
                                            <div class="pb-4 mx-2">

                                                <a href="#" class="image-popup" data-toggle="modal"
                                                    data-target="#imageModal" data-image="{{ $file }}"
                                                    data-title="{{ $file }}">
                                                    <img src="{{ $file }}" class="img-thumbnail mx-2"
                                                        alt="{{ $file }}">
                                                    <br> <br>
                                                    {{-- @if (Auth::user()->hasPermission('download Io_file')) --}}
                                                    {{-- <a id="downloadButton"
                                                            href="{{ route('iotelegram.downlaodfile', ['id' => $file->id]) }}"
                                                            class="btn-download"><i class="fa fa-download" style="color:green;"></i>
                                                                </a> --}}
                                                    {{-- @endif --}}
                                                </a>
                                            </div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">عرض الصورة</h5>

                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="#" class="img-fluid" alt="صورة">
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
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
