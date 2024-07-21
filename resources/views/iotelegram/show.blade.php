@extends('layout.header')

@section('title')
    عرض
@endsection
@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.list') }}" class="btn btn-primary mt-3">رجوع</a>
        </div>

        <div class="card">
            <div class="card-header">الواردات</div>
            <div class="card-body">

                <div class="mb-3">
                    <label for="date">التاريخ:</label>
                    <input disabled type="date" id="date" name="date" class="form-control"
                        value="{{ $iotelegram->date }}">
                </div>
                <div class="row" style="justify-content: space-evenly;">
                    <div class="mb-3">
                        <input disabled type="checkbox" id="extern" name="type" value="in"
                            @if ($iotelegram->type == 'in') checked @endif>
                        <label for="checkbox">داخلي</label>
                    </div>
                    <div class="mb-3">
                        <input disabled type="checkbox" id="intern" name="type" value="out"
                            @if ($iotelegram->type == 'out') checked @endif>
                        <label for="checkbox">خارجي</label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="department_id">الجهة المرسلة:</label>
                    <select disabled id="from_departement" name="from_departement" class="form-control">
                        <option value="">اختر الجهة</option>
                        @if ($iotelegram->type == 'in')
                            @foreach ($departments as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $iotelegram->from_departement) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        @else
                            @foreach ($external_departments as $item)
                                <option value="{{ $item->id }}" @if ($item->id == $iotelegram->from_departement) selected @endif>
                                    {{ $item->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="mb-3">

                    <label for="representive">اسم المندوب الجهة المرسلة :</label>
                    <select disabled id="representive_id" name="representive_id" class="form-control">
                        <option value="">اختر المندوب</option>
                        @foreach ($representives as $item)
                            <option value="{{ $item->id }}" @if ($item->id == $iotelegram->representive_id) selected @endif>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="recieved_by">الموظف المستلم:</label>
                    <select disabled id="recieved_by" name="recieved_by" class="form-control">
                        <option value="">اختر الموظف</option>
                        @foreach ($recieves as $item)
                            <option value="{{ $item->id }}" @if ($item->id == $iotelegram->recieved_by) selected @endif>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="files_num"> عدد الكتب:</label>
                    <br>
                    <select disabled id="files_num" name="files_num" class="form-control">
                        @for ($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" @if ($i == $iotelegram->files_num) selected @endif>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                {{-- Display Uploaded Images with Modal Popup --}}
                <div class="mb-3">
                    <label for="uploaded_images">الصور المرفقة:</label>
                    <div class="row">
                        @foreach ($iotelegram->ioFiles as $file)
                            @if ($file->file_type == 'image')
                                <div class="col-md-3 mb-3">
                                    <a href="#" class="image-popup" data-toggle="modal" data-target="#imageModal"
                                        data-image="{{ asset($file->file_name) }}" data-title="{{ $file->file_name }}">
                                        <img src="{{ asset($file->file_name) }}" class="img-thumbnail"
                                            alt="{{ $file->file_name }}">
                                        <a id="downloadButton"
                                            href="{{ route('iotelegram.downlaodfile', ['id' => $file->id]) }}"
                                            class="btn btn-primary"> <i class="fa fa-download"></i></a>

                                    </a>

                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                {{-- Display Uploaded Other Files --}}
                <div class="mb-3">
                    <label for="uploaded_files">الملفات المرفقة الأخرى:</label>
                    <ul class="list-group">
                        @foreach ($iotelegram->ioFiles as $file)
                            @if ($file->file_type == 'pdf')
                                <li class="list-group-item">
                                    <a id="downloadButton"
                                        href="{{ route('iotelegram.downlaodfile', ['id' => $file->id]) }}" target="_blank">
                                        <i class="fa fa-download"></i> {{ basename($file->real_name) }}</a>

                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
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
