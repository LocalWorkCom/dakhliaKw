@extends('layout.main')
@section('title')
    عرض
@endsection
@section('content')
    <div class="row" dir="rtl">
        <div class="container col-11" style="background-color:transparent;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instant_mission.index') }}">أمر الخدمه</a></li>
                    <li class="breadcrumb-item active" aria-current="page"> <a> المخالفات</a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="container welcome col-11">
            <p>تفاصيل المخالفات</p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container col-11 mt-3 p-0">
            <div class="form-row mx-2 pt-5 pb-4">
                @foreach ($data as $violation)
                    <table class="table table-bordered" dir="rtl">
                        <tbody>
                            <tr>
                                <th>الأسم:</th>
                                <td>{{ $violation->name }}</td>
                            </tr>
                            @if( $violation->grade_name)
                            <tr>
                                <th>الرتبه:</th>
                                <td>{{ $violation->grade_name }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>الرقم العسكري:</th>
                                <td>{{ $violation->military_number ?? 'لا يوجد رقم عسكرى' }}</td>
                            </tr>
                            <tr>
                                <th>الرقم المدني:</th>
                                <td>{{ $violation->Civil_number ?? 'لا يوجد رقم مدنى' }}</td>
                            </tr>
                            <tr>
                                <th>المخالفات:</th>
                                <td>{{ $violation->ViolationType }}</td>
                            </tr>
                            <tr>
                                <th>نوع المخالفة:</th>
                                <td>{{ $violation->Type }}</td>
                            </tr>
                            <tr>
                                <th>نوع الحالة المدنية:</th>
                                <td>{{ $violation->civil_type_name }}</td>
                            </tr>
                            <tr>
                                <th>رقم الملف:</th>
                                <td>{{ $violation->file_num ?? 'لا يوجد رقم ملف' }}</td>
                            </tr>
                            @if ($violation->image)
                                <tr>
                                    <th>الصور المرفقة:</th>
                                    <td>
                                        @php
                                            $images = explode(',', $violation->image);
                                        @endphp
                                        @foreach ($images as $file)
                                            <div class="pb-4 mx-2">
                                                <a href="#" class="image-popup" data-toggle="modal"
                                                    data-target="#imageModal" data-image="{{ $file }}"
                                                    data-title="{{ $file }}">
                                                    <img src="{{ $file }}" class="img-thumbnail mx-2"
                                                        alt="صورة مرفقة">
                                                </a>
                                            </div>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                @endforeach

            </div>
        </div>
    </div>

    <!-- Modal for image preview -->
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
