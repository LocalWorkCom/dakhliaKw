@extends('layout.header')
@section('content')

<section>
    <ol class="breadcrumb" dir="rtl">
        <li class="breadcrumb-item"><a href="#">الرئيسيه</a></li>
        <li class="breadcrumb-item active"><a href="">الصلاحيات  </a></li>

        <li class="breadcrumb-item active"> اضافه صلاحية</li>

    </ol>
     
    <div class="container-fluid p-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="bg-white p-5">
                    {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
                </div>
            </div>
        </div>
        
    </div>

   
</section>

    
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush