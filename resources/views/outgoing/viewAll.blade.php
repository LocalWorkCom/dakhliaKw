@extends('layout.header')


@section('content')
<<<<<<< HEAD
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
            </div>
        </div>

        <div class="row ">
            <div class="mb-3">
                {{-- <input type="text" id="global_search" class="form-control" placeholder="بحث ..."> --}}
            </div>
            {!! $dataTable->table(['class' => 'table table-responsive table-bordered table-hover dataTable']) !!}
        </div>
   

=======
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('Export.create') }}" class="btn btn-primary mt-3">إضافة جديد</a>
        </div>
        <h2>الصادرات</h2>

           
                <div class="mb-3">
                    @include('inc.flash')
                    {{-- <input type="text" id="global_search" class="form-control" placeholder="بحث ..."> --}}
                </div>
                {!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}
            </div>
        </div>
    </div>
       
>>>>>>> 5be7510574168ba3b84256676f4babf4ce099cc6
@endsection

@push('scripts')
{{ $dataTable->scripts() }}
@endpush


