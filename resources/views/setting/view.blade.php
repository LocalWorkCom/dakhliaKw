@extends('layout.header')

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
                                <button class="btn-all mt-3">
                                    <a href="" style="color:#0D992C;">إضافة جديد <img
                                            src="{{ asset('frontend/images/add-btn.svg') }}" alt=""></a>
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
@endsection
@push('scripts')
    {{-- {!! $vacation->scripts() !!} --}}
    {!! $dataTable->scripts() !!}
    {{-- {!! $job->scripts() !!} --}}
@endpush
