@extends('layout.main')


@section('content')


<div class="row ">
    <div class="container welcome col-11">
        <p> الصـــــــــادرات</p>
    </div>
</div>
<br>
<div class="row">
    <div class="container  col-11 mt-3 p-0 ">
        <div class="row " dir="rtl">
            <div class="form-group mt-4  mx-2 col-12 d-flex ">
                <button class="btn-all mt-2 mx-3">
                    <a href="{{ route('Export.create') }}" style="color:#0D992C;">إضافة جديد <img
                            src="{{ asset('frontend/images/add-btn.svg') }}" alt=""></a>
                </button>
                <button class="btn-all mt-2">
                    <a href="{{ route('Export.archive.show', ['status' => 'inactive']) }}"
                        style="color:#0D992C;">الارشيف
                        <img src="{{ asset('frontend/images/archieve.svg') }}" alt=""></a>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="mb-3">
                {{-- <input type="text" id="global_search" class="form-control" placeholder="بحث ..."> --}}
            </div>
            {!! $dataTable->table(['class' => 'table table-responsive table-bordered table-hover dataTable']) !!}
        </div>
    </div>
</div>






@endsection

@push('scripts')
{{ $dataTable->scripts() }}
@endpush