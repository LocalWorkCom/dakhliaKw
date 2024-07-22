@extends('layout.main')

@push('style')
@endpush

@section('content')
    <div class="container">

        <div class="row ">
            <div class="container welcome col-11">
                <p> الاعدادات</p>
            </div>
        </div>

        <div class="row " dir="rtl">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                        type="button" role="tab" aria-controls="nav-home" aria-selected="true">رتب العسكريه</button>
                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile" aria-selected="false">الوظائف</button>
                    <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact"
                        type="button" role="tab" aria-controls="nav-contact" aria-selected="false">الاجازات</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <div class="container  col-11 mt-3 p-0 ">
                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <button type="button" class="wide-btn  " data-bs-toggle="modal" id="extern-user-dev"
                                    data-bs-target="#extern-user" style="color: #0D992C;">
                                    <img src="{{ asset('frontend/images/add-btn.svg') }}" alt="img">
                                    اضافةرتبه عسكريه
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row ">
                        {!! $dataTable->table(['class' => 'table table-bordered table-striped'], true) !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    {{-- {!! $job->table(['class' => 'table table-bordered table-striped'], true) !!} --}}

                </div>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                    {{-- {!! $vacation->table(['class' => 'table table-bordered table-striped'], true) !!} --}}

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="extern-user" tabindex="-1" aria-labelledby="extern-departmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="extern-departmentLabel">إضافة رتبه عسكريه  جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="saveExternalUser" action="{{ route('grade.add') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="name"> اسم الرتبه</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                       

                        <!-- Save button -->
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    {{-- {!! $vacation->scripts() !!} --}}
    {!! $dataTable->scripts() !!}
    {{-- {!! $job->scripts() !!} --}}
@endpush
