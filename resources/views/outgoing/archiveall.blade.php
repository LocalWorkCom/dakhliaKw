@extends('layout.main')


@section('content')
 
<div class="row col-11" dir="rtl">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item "><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item"><a href="{{ route('Export.index') }}">الصادرات </a></li>
        <li class="breadcrumb-item active" aria-current="page"> <a href="">   ارشيف الصادر</a></li>
      </ol>
    </nav>
  </div>
<div class="row ">
    <div class="container welcome col-11">
        <p> ارشيف الصادر</p>
    </div>
</div>

    <div class="container  col-11 mt-4 p-0 ">
      
        <div class="row ">
            <div class="mb-3">
                {{-- <input type="text" id="global_search" class="form-control" placeholder="بحث ..."> --}}
            </div>
            {!! $dataTable->table(['class' => 'table table-responsive table-bordered table-hover dataTable']) !!}
        </div>
    </div>
       
    

        
   

@endsection

@push('scripts')
{{ $dataTable->scripts() }}
@endpush


