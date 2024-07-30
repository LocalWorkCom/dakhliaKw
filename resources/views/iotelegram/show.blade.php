@extends('layout.main')

@section('title')
    التفاصيل
@endsection
@section('content')
    <div class="row col-11" dir="rtl">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item "><a href="/">الرئيسيه</a></li>
                <li class="breadcrumb-item"><a href="{{ route('iotelegrams.list') }}">الواردات </a></li>
                <li class="breadcrumb-item active" aria-current="page"> <a href=""> عرض الوارد</a></li>
            </ol>
        </nav>
    </div>
    <div class="row ">
        <div class="container welcome col-11">
            <p> الــــــــــــواردات </p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container  col-11 mt-3  p-0 ">
            <div class="row " dir="rtl">
                <div class="form-group mt-4  mx-2 col-12 d-flex ">

                </div>
            </div>



            <div class="form-row mx-3 mb-3">
                <table class="table table-bordered" dir="rtl">
                    <tbody>
                        <tr>
                            <th scope="row"style="background: #f5f6fa;">رقم الوارد</th>
                            <td>{{ $iotelegram->iotelegram_num }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"style="background: #f5f6fa;">تاريخ الوارد</th>
                            <td>{{ $iotelegram->date }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"style="background: #f5f6fa;">رقم الصادر</th>
                            <td>{{ $iotelegram->outgoing_num }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"style="background: #f5f6fa;">تاريخ الصادر</th>
                            <td>{{ $iotelegram->outgoing_date }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;">نوع الوارد</th>
                            <td>
                                {{ $iotelegram->type == 'in' ? 'داخلي' : 'خارجي' }}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"style="background: #f5f6fa;">الجهه المرسلة</th>
                            <td>{{ $iotelegram->type == 'out' ? $iotelegram->external_department->name : $iotelegram->internal_department->name }}
                            </td>
                        </tr>

                        <tr>
                            <th scope="row" style="background: #f5f6fa;">اسم مندوب الجهه المرسلة </th>
                            <td>{{ $iotelegram->representive->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;"> الموظف المستلم </th>
                            <td>{{ $iotelegram->recieved->name }}</td>
                        </tr>
                        <tr>
                            <th scope="row" style="background: #f5f6fa;"> عدد الكتب </th>
                            <td>{{ $iotelegram->files_num }}</td>
                        </tr>
                        @if (Auth::user()->hasPermission('view Io_file'))
                            <tr>
                                <th scope="row" style="background: #f5f6fa;"> الصور المرفقه </th>
                                <td>
                                    <?php $count = 0; ?>
                                    <div class="row">
                                        <div class="col-md-11 mb-3 px-5 mt-2 d-flex">
                                            @foreach ($iotelegram->ioFiles as $file)
                                                @if ($file->file_type == 'image')
                                                    <div class="pb-4 mx-2">

                                                        <a href="#" class="image-popup" data-toggle="modal"
                                                            data-target="#imageModal"
                                                            data-image="{{ asset($file->file_name) }}"
                                                            data-title="{{ $file->file_name }}">
                                                            <img src="{{ asset($file->file_name) }}"
                                                                class="img-thumbnail mx-2" alt="{{ $file->file_name }}">
                                                            <br> <br>
                                                            @if (Auth::user()->hasPermission('download Io_file'))
                                                                <a id="downloadButton"
                                                                    href="{{ route('iotelegram.downlaodfile', ['id' => $file->id]) }}"
                                                                    class="btn-download"><i class="fa fa-download"
                                                                        style="color:green;"></i>
                                                                    تحميل الملف
                                                                </a>
                                                            @endif
                                                        </a>
                                                    </div>
                                                    <?php $count++; ?>
                                                @endif
                                            @endforeach
                                            @if ($count == 0)
                                                _______________
                                            @endif

                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" style="background: #f5f6fa;"> الملفات المرفقة الاخري </th>
                                <td>
                                    <ul class="list-group">
                                        @foreach ($iotelegram->ioFiles as $file)
                                            @if ($file->file_type == 'pdf')
                                                <div class="col-md-11 mb-3 px-5 mt-3">
                                                    @if (Auth::user()->hasPermission('download Io_file'))
                                                        <a id="downloadButton"
                                                            href="{{ route('iotelegram.downlaodfile', ['id' => $file->id]) }}"
                                                            target="_blank" class="btn-download">
                                                            <i class="fa fa-download" style="color:green; "> </i>
                                                            {{ basename($file->real_name) }}</a>
                                                    @endif

                                                </div>
                                            @endif
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                        @endif



                    </tbody>
                </table>

            </div>
        </div>
    </div>



    {{-- Modal for Image Popup --}}
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
