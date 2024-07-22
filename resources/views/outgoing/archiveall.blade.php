@extends('layout.main')


@section('content')
    <div class="container">
      
<div class="row ">
    <div class="container welcome col-11">
        <p> ارشيف الصادر</p>
    </div>
</div>

    <div class="container  col-11 mt-3 p-0 ">
        <div class="row justify-content-end">
            <div class="col-auto">
                <button class="btn-all mt-3">
                    <a href="{{ route('Export.index') }}" style="color:#0D992C;">رجوع<img
                            src="{{ asset('frontend/images/add-btn')}}" alt=""></a>
                </button>
            </div>
        </div>
    </div>
       
    

        <div class="row ">
            <div class="mb-3">
                {{-- <input type="text" id="global_search" class="form-control" placeholder="بحث ..."> --}}
            </div>
            {!! $dataTable->table(['class' => 'table table-responsive table-bordered table-hover dataTable']) !!}
        </div>
   

@endsection

@push('scripts')
{{ $dataTable->scripts() }}
@endpush


