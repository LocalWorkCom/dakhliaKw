@extends('layout.main')

@section('title')
    عرض
@endsection

@section('content')
    <div class="row" dir="rtl">
        <div class="container col-11" style="background-color:transparent;">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">الرئيسيه</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('instant_mission.index') }}">أمر الخدمه</a></li>
                    <li class="breadcrumb-item active" aria-current="page"> <a> الحضور </a></li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="container welcome col-11">
            <p>تفاصيل الحضور</p>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="container col-11 mt-3 p-0">
            <div class="form-row mx-2 pt-5 pb-4">
                @foreach ($data as $attendance)
                    <table class="table table-bordered" dir="rtl">
                        <tbody>
                            <tr>
                                <th>التاريخ:</th>
                                <td>{{ $attendance->date }}</td>
                            </tr>
                            <!-- Loop through employees for each attendance -->
                            @foreach ($attendance->employees as $employee)
                                <tr>
                                    <th>أسم الموظف :</th>
                                    <td>{{ $employee->name }}</td>
                                </tr>
                                <tr>
                                    <th>رقم العسكرى:</th>
                                    <td>{{ $employee->military_number ?? 'لا يوجد رقم عسكرى' }}</td>
                                </tr>
                                <tr>
                                    <th>الرتبه:</th>
                                    <td>{{ $employee->grade->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>الفئه:</th>
                                    <td>{{ $employee->type->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>أسم الاداره:</th>
                                    <td>{{ $employee->force->name ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
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
