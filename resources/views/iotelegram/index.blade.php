@extends('layout.header')

@section('title', 'الواردات')

@section('content')
<div class="row ">
    <div class="container welcome col-11">
        <p> الــــــواردات</p>
    </div>
</div>

<div class="container  col-11 mt-3 p-0 ">
        <div class="row justify-content-end">
            <div class="col-auto">
            <button class="btn-all mt-3">
                    <a  href="{{ route('iotelegrams.add') }}" style="color:#0D992C;">إضافة جديد <img
                            src="{{ asset('frontend/images/addnew.svg')}}" alt=""></a>
                </button>
                
            </div>
        </div>
          
         
                {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
           
        </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
