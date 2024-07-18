@extends('layout.header')

@section('title', 'الواردات')

@section('content')
    <div class="container">
        <div class="mb-3">
            <a href="{{ route('iotelegrams.add') }}" class="btn btn-primary mt-3">إضافة جديد</a>
        </div>
        <div class="card">
            <div class="card-header">الواردات</div>

            <div class="card-body">
         
                {!! $dataTable->table(['class' => 'table table-bordered table-hover dataTable']) !!}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
@endpush
