

@extends('layout.header')

@section('title', 'الادارات')

@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('departments.create') }}" class="btn btn-primary mt-3">إضافة ادارة</a>
        </div>
        <div class="mb-3">
            <a href="{{ route('postmans.create') }}" class="btn btn-primary mt-3">إضافة مندوب</a>
        </div>
       {{--  <div class="card"> --}}
          {{--   <div class="card-header">الادارات</div>

            <div class="card-body"> --}}
                <h3>الادارات</h3>
         
                {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
         {{--    </div>
        </div>
 --}}    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
