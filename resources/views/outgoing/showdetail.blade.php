@extends('layout.main')

@push('style')
@endpush
@section('title')
    التفاصيل
@endsection
@section('content')
<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
            <li class="breadcrumb-item"><a href="{{ route('Export.index') }}">الصادرات </a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href="#"> التفاصيل </a></li>
        </ol>
    </nav>
</div>
<div class="row ">
    <div class="container welcome col-11">
        <p> الصــــــــــــادرات </p>
    </div>
</div>
<br>

    <section style="direction: rtl;">
        <div class="row">
            <div class="container c col-12 mt-3 p-0 col-md-11 col-lg-11 col-s-11 pt-5 pb-4 px-3">
                <table class="table table-bordered ">
                    <tbody>
                        <tr style="background-color:#f5f6fa;">
                            <th scope="row"> الموظف المستلم</th>
                            <td>{{ $data->person_to ? $data->personTo->name : 'لا يوجد موظف مستلم' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">العنوان</th>
                            <td>{{ $data->name ? $data->name : 'لا يوجد عنوان للصادر' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">اسم القطاع</th>
                            <td>{{ $data->department_id ? $data->department_External->name : 'لا يوجد قطاع' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">رقم الصادر</th>
                            <td>{{ $data->num ? $data->num : 'لا يوجد رقم للصادر' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">تاريخ الصادر</th>
                            <td>{{ $data->date ? $data->date : 'لا يوجد تاريخ للصادر' }}</td>
                        </tr>
                        <tr>
                            <th scope="row"> الحالة</th>
                            <td>{{ $data->active == 0 ? 'جديد' : ' أرشيف' }}</td>
                        </tr>
                        <tr>
                            <th scope="row">الملاحظات </th>
                            <td>{{ $data->note ? $data->note : 'لايوجد ملاحظات للصادر' }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;"> الصور المرفقه </th>
                            <td>
                                <div class="row">
                                    <div class="col-md-11 mb-3 px-5 mt-2 d-flex flex-wrap">
                                        @if (!empty($is_file))
                                            @foreach ($is_file as $file)
                                                @if ($file->file_type == 'image')
                                                    <div class="pb-4 mx-2">

                                                        <a href="#" class="image-popup" data-toggle="modal"
                                                            data-target="#imageModal"
                                                            data-image="{{ asset($file->file_name) }}"
                                                            data-title="{{ $file->file_name }}">
                                                            <img src="{{ asset($file->file_name) }}"
                                                                class="img-thumbnail mx-2" alt="{{ $file->file_name }}">
                                                            <br> <br>
                                                            @if (Auth::user()->hasPermission('download outgoing_files'))
                                                            <a id="downloadButton"
                                                                href="{{ route('downlaodfile', $file->id) }}"
                                                                class="btn-download "><i class="fa fa-download"
                                                                    style="color:green;"></i>
                                                                تحميل الملف
                                                            </a> 
                                                            @endif
                                                        </a>
                                                    </div>
                                                @endif
                                            @endforeach

                                    </div>
                                </div>
                            @else
                                لا يوجد صور لهذا الصادر
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;"> الملفات المرفقة الاخري </th>
                            <td>
                                <ul class="list-group">
                                    <div class="col-md-11 mb-3 px-5 mt-2 d-flex">
                                        @if (!empty($is_file))
                                            @foreach ($is_file as $file)
                                                @if ($file->file_type == 'pdf')
                                                    <div>
                                                        <a id="downloadButton"
                                                            href="{{ route('downlaodfile', $file->id) }}" target="_blank"
                                                            class="btn-download">
                                                            <i class="fa fa-download" style="color:green; "> </i>
                                                            {{ basename($file->real_name) }}</a>

                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            لا يوجد ملفات لهذا الصادر
                                        @endif
                                        </div>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        
                    </tfoot>

                </table>

            </div>

            {{-- Modal for Image Popup --}}
            <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="imageModalLabel">عرض الصورة</h5>
                            {{-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button> --}}
                        </div>
                        <div class="modal-body text-center">
                            <img id="modalImage" src="#" class="img-fluid" alt="صورة">
                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection

@push('scripts')
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
