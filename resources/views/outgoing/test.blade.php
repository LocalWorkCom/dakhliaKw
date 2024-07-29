@extends('layout.main')


@section('content')
    <div class="container">

<div class="row ">
    <div class="container welcome col-11">
        <p> الصـــــــــادرات</p>
    </div>
</div>

    <div class="container  col-11 mt-3 p-0 ">
        <div class="row justify-content-end">
            <div class="col-auto">
                <button class="btn-all mt-3">
                    <a href="{{ route('Export.create') }}" style="color:#0D992C;">إضافة جديد <img
                            src="{{ asset('frontend/images/addnew.svg')}}" alt=""></a>
                </button>
                <button class="btn-all mt-3">
                    <a href="{{ route('Export.archive.show',['status' => 'inactive']) }}" style="color:#0D992C;">الارشيف <img
                            src="{{ asset('frontend/images/addnew.svg')}}" alt=""></a>
                </button>
            </div>
        </div>
    </div>



        <div class="row ">
            <div class="mb-3">
                {{-- <input type="text" id="global_search" class="form-control" placeholder="بحث ..."> --}}
            </div>
            {!! $data->table(['class' => 'table table-responsive table-bordered table-hover dataTable']) !!}
        </div>


@endsection

@push('scripts')
{{ $data->scripts() }}
@endpush


